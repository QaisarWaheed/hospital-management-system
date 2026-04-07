using RehabFpMatcher;
using RehabFpMatcher.Services;

var builder = WebApplication.CreateBuilder(args);

var port = builder.Configuration.GetValue("PORT", 9100);
var host = builder.Configuration.GetValue("HOST", "0.0.0.0");
var thresholdDefault = builder.Configuration.GetValue("MATCH_THRESHOLD", 50.0);
var dbPath = builder.Configuration.GetValue("DB_PATH", Path.Combine(AppContext.BaseDirectory, "matcher-dotnet.db"))!;
var useDemoFallback = builder.Configuration.GetValue("USE_DEMO_FALLBACK", true);
var dpfpBinPath = builder.Configuration.GetValue("DPFP_BIN_PATH", @"C:\Program Files (x86)\DigitalPersona\Bin")!;

builder.Services.AddSingleton(new TemplateRepository(dbPath));
builder.Services.AddSingleton<IFingerprintMatcher>(new DigitalPersonaMatcher(useDemoFallback, dpfpBinPath));
builder.Services.AddCors(o => o.AddDefaultPolicy(p => p.AllowAnyHeader().AllowAnyMethod().AllowAnyOrigin()));

var app = builder.Build();
app.UseCors();

app.MapGet("/health", (TemplateRepository repo) =>
{
    var count = repo.GetAllTemplates().Count;
    return Results.Ok(new
    {
        ok = true,
        service = "rehab-fp-matcher-dotnet",
        templates_count = count,
        default_threshold = thresholdDefault,
        demo_fallback = useDemoFallback,
        dpfp_bin_path = dpfpBinPath
    });
});

app.MapPost("/enroll", (EnrollRequest req, TemplateRepository repo) =>
{
    var tpl = (req.Template ?? string.Empty).Trim();
    if (req.Patient_Id <= 0 || tpl.Length == 0)
    {
        return Results.BadRequest(new { ok = false, error = "patient_id and template are required" });
    }
    repo.AddTemplate(req.Patient_Id, (req.Finger_Code ?? "unknown").Trim(), tpl);
    return Results.Ok(new { ok = true });
});

app.MapPost("/identify", (IdentifyRequest req, TemplateRepository repo, IFingerprintMatcher matcher) =>
{
    var probe = (req.Probe ?? string.Empty).Trim();
    if (probe.Length == 0) return Results.BadRequest(new { match = false, error = "probe is required" });
    var threshold = req.Threshold ?? thresholdDefault;

    var candidates = new List<MatchCandidate>();
    if (req.Templates is { Count: > 0 })
    {
        foreach (var t in req.Templates)
        {
            if (t.Patient_Id <= 0) continue;
            var left = (t.Template ?? t.Thumb_Left ?? string.Empty).Trim();
            var right = (t.Thumb_Right ?? string.Empty).Trim();
            if (left.Length > 0)
            {
                candidates.Add(new MatchCandidate
                {
                    PatientId = t.Patient_Id,
                    Template = left,
                    FingerCode = (t.Finger_Code ?? "left").Trim()
                });
            }
            if (right.Length > 0)
            {
                candidates.Add(new MatchCandidate
                {
                    PatientId = t.Patient_Id,
                    Template = right,
                    FingerCode = "right"
                });
            }
        }
    }
    else
    {
        candidates = repo.GetAllTemplates();
    }

    if (matcher is DigitalPersonaMatcher strictDp && !strictDp.DemoFallbackEnabled)
    {
        candidates = candidates.Where(c => strictDp.IsTemplateCompatible(c.Template)).ToList();
        if (!strictDp.IsTemplateCompatible(probe))
        {
            return Results.Ok(new { match = false, patient_id = (int?)null, score = 0.0, reason = "probe_not_dpfp_compatible" });
        }
    }
    if (candidates.Count == 0) return Results.Ok(new { match = false, patient_id = (int?)null, score = 0.0, reason = "no_compatible_templates" });

    var effectiveThreshold = threshold;
    if (matcher is DigitalPersonaMatcher dp && dp.DemoFallbackEnabled)
    {
        effectiveThreshold = Math.Max(effectiveThreshold, 35);
    }
    var result = matcher.Identify(probe, candidates, effectiveThreshold);
    Console.WriteLine($"[identify] candidates={candidates.Count} threshold={effectiveThreshold} score={result.Score} patientId={result.PatientId} match={result.Match}");
    return Results.Ok(new
    {
        match = result.Match,
        patient_id = result.PatientId,
        score = result.Score
    });
});

app.MapPost("/verify", (VerifyRequest req, TemplateRepository repo, IFingerprintMatcher matcher) =>
{
    var probe = (req.Probe ?? string.Empty).Trim();
    if (probe.Length == 0) return Results.BadRequest(new { match = false, error = "probe is required" });
    var threshold = req.Threshold ?? thresholdDefault;

    var templates = new List<string>();
    if (req.Patient_Id is > 0)
    {
        templates.AddRange(repo.GetTemplatesByPatient(req.Patient_Id.Value).Select(x => x.Template));
    }
    else
    {
        var left = (req.Stored_Left ?? string.Empty).Trim();
        var right = (req.Stored_Right ?? string.Empty).Trim();
        if (left.Length > 0) templates.Add(left);
        if (right.Length > 0) templates.Add(right);
    }

    if (templates.Count == 0) return Results.Ok(new { match = false, score = 0.0 });

    var result = matcher.Verify(probe, templates, threshold);
    return Results.Ok(new { match = result.Match, score = result.Score });
});

app.MapPost("/debug-template", (IdentifyRequest req, IFingerprintMatcher matcher) =>
{
    var probe = (req.Probe ?? string.Empty).Trim();
    var stored = new List<string>();
    if (req.Templates is { Count: > 0 })
    {
        foreach (var t in req.Templates)
        {
            var left = (t.Template ?? t.Thumb_Left ?? string.Empty).Trim();
            var right = (t.Thumb_Right ?? string.Empty).Trim();
            if (left.Length > 0) stored.Add(left);
            if (right.Length > 0) stored.Add(right);
        }
    }
    if (matcher is DigitalPersonaMatcher dp)
    {
        return Results.Ok(dp.DebugTemplate(probe, stored));
    }
    return Results.Ok(new { ok = false, error = "Matcher does not support debug endpoint." });
});

app.MapPost("/compat-check", (IdentifyRequest req, IFingerprintMatcher matcher) =>
{
    var probe = (req.Probe ?? string.Empty).Trim();
    var stored = new List<string>();
    if (req.Templates is { Count: > 0 })
    {
        foreach (var t in req.Templates)
        {
            var left = (t.Template ?? t.Thumb_Left ?? string.Empty).Trim();
            var right = (t.Thumb_Right ?? string.Empty).Trim();
            if (left.Length > 0) stored.Add(left);
            if (right.Length > 0) stored.Add(right);
        }
    }
    if (matcher is DigitalPersonaMatcher dp)
    {
        return Results.Ok(dp.CompatibilityReport(probe, stored));
    }
    return Results.Ok(new { ok = false, error = "Matcher does not support compatibility endpoint." });
});

app.Run($"http://{host}:{port}");
