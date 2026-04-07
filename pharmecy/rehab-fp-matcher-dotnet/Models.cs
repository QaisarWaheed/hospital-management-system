namespace RehabFpMatcher;

public sealed class IdentifyRequest
{
    public string? Probe { get; set; }
    public double? Threshold { get; set; }
    public List<IdentifyTemplate>? Templates { get; set; }
}

public sealed class IdentifyTemplate
{
    public int Patient_Id { get; set; }
    public string? Template { get; set; }
    public string? Thumb_Left { get; set; }
    public string? Thumb_Right { get; set; }
    public string? Finger_Code { get; set; }
}

public sealed class VerifyRequest
{
    public int? Patient_Id { get; set; }
    public string? Stored_Left { get; set; }
    public string? Stored_Right { get; set; }
    public string? Probe { get; set; }
    public double? Threshold { get; set; }
}

public sealed class EnrollRequest
{
    public int Patient_Id { get; set; }
    public string? Template { get; set; }
    public string? Finger_Code { get; set; }
}

public sealed class MatchCandidate
{
    public int PatientId { get; set; }
    public string Template { get; set; } = string.Empty;
    public string FingerCode { get; set; } = "unknown";
}

public sealed class MatchResult
{
    public bool Match { get; set; }
    public int? PatientId { get; set; }
    public double Score { get; set; }
}
