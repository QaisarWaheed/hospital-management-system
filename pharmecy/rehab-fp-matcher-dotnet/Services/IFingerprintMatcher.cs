namespace RehabFpMatcher.Services;

public interface IFingerprintMatcher
{
    MatchResult Identify(string probeTemplate, IEnumerable<MatchCandidate> candidates, double threshold);
    MatchResult Verify(string probeTemplate, IEnumerable<string> storedTemplates, double threshold);
}
