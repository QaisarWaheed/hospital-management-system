using System.Reflection;
namespace RehabFpMatcher.Services;

/// <summary>
/// Plug your DigitalPersona U.are.U SDK compare/identify calls here.
/// Current implementation contains a demo fallback scoring so service runs immediately.
/// </summary>
public sealed class DigitalPersonaMatcher : IFingerprintMatcher
{
    private sealed class TemplateCompatibility
    {
        public int raw_len { get; set; }
        public int bytes_len { get; set; }
        public bool feature_set_ok { get; set; }
        public string? feature_set_err { get; set; }
        public bool template_ok { get; set; }
        public string? template_err { get; set; }
    }
    public bool DemoFallbackEnabled => _demoFallback;
    private readonly bool _demoFallback;
    private readonly string _dpfpBinPath;
    private readonly object? _verification;
    private readonly Type? _featureSetType;
    private readonly Type? _templateType;
    private readonly MethodInfo? _verifyMethod;
    private readonly MethodInfo? _featureDeserialize;
    private readonly MethodInfo? _templateDeserialize;
    private readonly PropertyInfo? _verifiedProp;
    private readonly PropertyInfo? _farProp;
    private readonly bool _dpfpReady;

    public DigitalPersonaMatcher(bool demoFallback, string dpfpBinPath)
    {
        _demoFallback = demoFallback;
        _dpfpBinPath = dpfpBinPath ?? string.Empty;
        try
        {
            (_verification, _featureSetType, _templateType, _verifyMethod, _featureDeserialize, _templateDeserialize, _verifiedProp, _farProp, _dpfpReady) = InitDpfp();
        }
        catch
        {
            _dpfpReady = false;
        }
    }

    public MatchResult Identify(string probeTemplate, IEnumerable<MatchCandidate> candidates, double threshold)
    {
        if (_demoFallback) threshold = Math.Max(threshold, 35);
        var best = new MatchResult { Match = false, PatientId = null, Score = 0 };

        foreach (var c in candidates)
        {
            var score = ScoreWithDigitalPersonaOrFallback(c.Template, probeTemplate);
            if (score > best.Score)
            {
                best.Score = score;
                best.PatientId = c.PatientId;
            }
        }

        if (best.PatientId is not null && best.Score >= threshold)
        {
            best.Match = true;
            return best;
        }

        return new MatchResult { Match = false, PatientId = null, Score = best.Score };
    }

    public MatchResult Verify(string probeTemplate, IEnumerable<string> storedTemplates, double threshold)
    {
        double best = 0;
        foreach (var tpl in storedTemplates)
        {
            var score = ScoreWithDigitalPersonaOrFallback(tpl, probeTemplate);
            if (score > best) best = score;
        }

        return new MatchResult
        {
            Match = best >= threshold,
            PatientId = null,
            Score = best
        };
    }

    public object DebugTemplate(string probeTemplate, IEnumerable<string>? storedTemplates = null)
    {
        var probeRaw = (probeTemplate ?? string.Empty).Trim();
        var probeBytes = DecodeTemplateText(probeRaw);
        var probeFeatureSetOk = false;
        var probeTemplateOk = false;
        string? probeFeatureErr = null;
        string? probeTemplateErr = null;

        if (_dpfpReady && probeBytes is not null && _featureSetType is not null && _templateType is not null &&
            _featureDeserialize is not null && _templateDeserialize is not null)
        {
            try
            {
                var fs = Activator.CreateInstance(_featureSetType);
                _featureDeserialize.Invoke(fs, new object[] { probeBytes });
                probeFeatureSetOk = true;
            }
            catch (Exception ex)
            {
                probeFeatureErr = ex.GetBaseException().Message;
            }
            try
            {
                var tp = Activator.CreateInstance(_templateType);
                _templateDeserialize.Invoke(tp, new object[] { probeBytes });
                probeTemplateOk = true;
            }
            catch (Exception ex)
            {
                probeTemplateErr = ex.GetBaseException().Message;
            }
        }

        var stored = new List<object>();
        foreach (var s in (storedTemplates ?? Array.Empty<string>()).Take(3))
        {
            var txt = (s ?? string.Empty).Trim();
            var bytes = DecodeTemplateText(txt);
            var stTplOk = false;
            var stTplErr = (string?)null;
            if (_dpfpReady && bytes is not null && _templateType is not null && _templateDeserialize is not null)
            {
                try
                {
                    var tp = Activator.CreateInstance(_templateType);
                    _templateDeserialize.Invoke(tp, new object[] { bytes });
                    stTplOk = true;
                }
                catch (Exception ex)
                {
                    stTplErr = ex.GetBaseException().Message;
                }
            }
            stored.Add(new
            {
                raw_len = txt.Length,
                bytes_len = bytes?.Length ?? 0,
                template_ok = stTplOk,
                template_err = stTplErr
            });
        }

        return new
        {
            dpfp_ready = _dpfpReady,
            dpfp_bin_path = _dpfpBinPath,
            demo_fallback = _demoFallback,
            probe = new
            {
                raw_len = probeRaw.Length,
                bytes_len = probeBytes?.Length ?? 0,
                feature_set_ok = probeFeatureSetOk,
                feature_set_err = probeFeatureErr,
                template_ok = probeTemplateOk,
                template_err = probeTemplateErr
            },
            sample_stored_templates = stored
        };
    }

    public object CompatibilityReport(string probeTemplate, IEnumerable<string>? storedTemplates = null)
    {
        var probe = CheckTemplateCompatibility((probeTemplate ?? string.Empty).Trim());
        var stored = (storedTemplates ?? Array.Empty<string>())
            .Take(10)
            .Select(t => CheckTemplateCompatibility((t ?? string.Empty).Trim()))
            .ToList();
        return new
        {
            dpfp_ready = _dpfpReady,
            dpfp_bin_path = _dpfpBinPath,
            probe,
            stored_count = stored.Count,
            stored
        };
    }

    public bool IsTemplateCompatible(string templateText)
    {
        var c = CheckTemplateCompatibility((templateText ?? string.Empty).Trim());
        return c.feature_set_ok || c.template_ok;
    }

    private TemplateCompatibility CheckTemplateCompatibility(string raw)
    {
        var bytes = DecodeTemplateText(raw);
        var featureOk = false;
        var templateOk = false;
        string? featureErr = null;
        string? templateErr = null;

        if (_dpfpReady && bytes is not null && _featureSetType is not null && _templateType is not null &&
            _featureDeserialize is not null && _templateDeserialize is not null)
        {
            try
            {
                var fs = Activator.CreateInstance(_featureSetType);
                _featureDeserialize.Invoke(fs, new object[] { bytes });
                featureOk = true;
            }
            catch (Exception ex)
            {
                featureErr = ex.GetBaseException().Message;
            }
            try
            {
                var tp = Activator.CreateInstance(_templateType);
                _templateDeserialize.Invoke(tp, new object[] { bytes });
                templateOk = true;
            }
            catch (Exception ex)
            {
                templateErr = ex.GetBaseException().Message;
            }
        }

        return new TemplateCompatibility
        {
            raw_len = raw.Length,
            bytes_len = bytes?.Length ?? 0,
            feature_set_ok = featureOk,
            feature_set_err = featureErr,
            template_ok = templateOk,
            template_err = templateErr
        };
    }

    private double ScoreWithDigitalPersonaOrFallback(string storedTemplate, string probeTemplate)
    {
        var dpfpScore = TryDpfpScore(storedTemplate, probeTemplate);
        if (dpfpScore.HasValue)
        {
            return dpfpScore.Value;
        }
        if (_demoFallback)
        {
            return DemoScore(storedTemplate, probeTemplate);
        }
        // Strict mode but non-fatal: if DPFP cannot parse/compare these templates,
        // return 0 score so API responds with match=false instead of HTTP 500.
        return 0;
    }

    private double? TryDpfpScore(string storedTemplate, string probeTemplate)
    {
        if (!_dpfpReady || _verification is null || _featureSetType is null || _templateType is null ||
            _verifyMethod is null || _featureDeserialize is null || _templateDeserialize is null ||
            _verifiedProp is null || _farProp is null)
        {
            return null;
        }
        try
        {
            var probeBytes = DecodeTemplateText(probeTemplate);
            var storedBytes = DecodeTemplateText(storedTemplate);
            if (probeBytes is null || storedBytes is null) return null;

            var featureObj = Activator.CreateInstance(_featureSetType);
            if (featureObj is null) return null;
            _featureDeserialize.Invoke(featureObj, new object[] { probeBytes });

            var templateObj = Activator.CreateInstance(_templateType);
            if (templateObj is null) return null;
            _templateDeserialize.Invoke(templateObj, new object[] { storedBytes });

            var resultObj = _verifyMethod.Invoke(_verification, new[] { featureObj, templateObj });
            if (resultObj is null) return null;

            var verified = (bool)(_verifiedProp.GetValue(resultObj) ?? false);
            var farAchieved = Convert.ToDouble(_farProp.GetValue(resultObj) ?? 0);
            var score = FarToScore(farAchieved);
            if (verified && score < 60) score = 60;
            return score;
        }
        catch
        {
            return null;
        }
    }

    private static byte[]? DecodeTemplateText(string input)
    {
        var s = (input ?? string.Empty).Trim();
        if (s.Length == 0) return null;
        // Handle URL-safe Base64 and missing padding.
        s = s.Replace('-', '+').Replace('_', '/');
        var mod4 = s.Length % 4;
        if (mod4 > 0) s = s.PadRight(s.Length + (4 - mod4), '=');
        try
        {
            return Convert.FromBase64String(s);
        }
        catch
        {
            return null;
        }
    }

    private static double FarToScore(double farAchieved)
    {
        if (farAchieved <= 0) return 100;
        // FAR lower is better. Compress to 0..100 score.
        var v = 100.0 - (Math.Log10(farAchieved + 1.0) * 10.0);
        if (v < 0) return 0;
        if (v > 100) return 100;
        return Math.Round(v, 2);
    }

    private (object? verification, Type? featureSetType, Type? templateType, MethodInfo? verifyMethod,
        MethodInfo? featureDeserialize, MethodInfo? templateDeserialize, PropertyInfo? verifiedProp,
        PropertyInfo? farProp, bool ok) InitDpfp()
    {
        if (string.IsNullOrWhiteSpace(_dpfpBinPath) || !Directory.Exists(_dpfpBinPath))
        {
            return (null, null, null, null, null, null, null, null, false);
        }

        var shr = Assembly.LoadFrom(Path.Combine(_dpfpBinPath, "DPFPShrNET.dll"));
        var ver = Assembly.LoadFrom(Path.Combine(_dpfpBinPath, "DPFPVerNET.dll"));

        var featureSetType = shr.GetType("DPFP.FeatureSet");
        var templateType = shr.GetType("DPFP.Template");
        var verificationType = ver.GetType("DPFP.Verification.Verification");
        var resultType = ver.GetType("DPFP.Verification.Verification+Result");
        if (featureSetType is null || templateType is null || verificationType is null || resultType is null)
        {
            return (null, null, null, null, null, null, null, null, false);
        }

        var verification = Activator.CreateInstance(verificationType);
        var verifyMethod = verificationType.GetMethod("Verify", new[] { featureSetType, templateType });
        var featureDeserialize = featureSetType.GetMethod("DeSerialize", new[] { typeof(byte[]) });
        var templateDeserialize = templateType.GetMethod("DeSerialize", new[] { typeof(byte[]) });
        var verifiedProp = resultType.GetProperty("Verified");
        var farProp = resultType.GetProperty("FARAchieved");

        var ok = verification is not null && verifyMethod is not null &&
                 featureDeserialize is not null && templateDeserialize is not null &&
                 verifiedProp is not null && farProp is not null;
        return (verification, featureSetType, templateType, verifyMethod, featureDeserialize, templateDeserialize, verifiedProp, farProp, ok);
    }

    private static double DemoScore(string a, string b)
    {
        a = (a ?? string.Empty).Trim();
        b = (b ?? string.Empty).Trim();
        if (a.Length == 0 || b.Length == 0) return 0;
        if (a.Equals(b, StringComparison.Ordinal)) return 100;

        var min = Math.Min(a.Length, b.Length);
        var max = Math.Max(a.Length, b.Length);
        if (max == 0) return 0;

        var same = 0;
        for (var i = 0; i < min; i++)
        {
            if (a[i] == b[i]) same++;
        }
        return Math.Round((double)same / max * 100, 2);
    }
}
