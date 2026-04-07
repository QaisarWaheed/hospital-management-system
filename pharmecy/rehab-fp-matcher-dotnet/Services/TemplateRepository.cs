using Microsoft.Data.Sqlite;

namespace RehabFpMatcher.Services;

public sealed class TemplateRepository
{
    private readonly string _connectionString;

    public TemplateRepository(string dbPath)
    {
        _connectionString = $"Data Source={dbPath}";
        EnsureSchema();
    }

    private void EnsureSchema()
    {
        using var con = new SqliteConnection(_connectionString);
        con.Open();
        using var cmd = con.CreateCommand();
        cmd.CommandText = """
            CREATE TABLE IF NOT EXISTS templates (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                patient_id INTEGER NOT NULL,
                finger_code TEXT NOT NULL,
                template TEXT NOT NULL,
                created_at TEXT NOT NULL
            );
            CREATE INDEX IF NOT EXISTS idx_templates_patient ON templates(patient_id);
            """;
        cmd.ExecuteNonQuery();
    }

    public void AddTemplate(int patientId, string fingerCode, string template)
    {
        using var con = new SqliteConnection(_connectionString);
        con.Open();
        using var cmd = con.CreateCommand();
        cmd.CommandText = """
            INSERT INTO templates (patient_id, finger_code, template, created_at)
            VALUES ($pid, $fcode, $tpl, datetime('now'))
            """;
        cmd.Parameters.AddWithValue("$pid", patientId);
        cmd.Parameters.AddWithValue("$fcode", fingerCode);
        cmd.Parameters.AddWithValue("$tpl", template);
        cmd.ExecuteNonQuery();
    }

    public List<MatchCandidate> GetAllTemplates()
    {
        using var con = new SqliteConnection(_connectionString);
        con.Open();
        using var cmd = con.CreateCommand();
        cmd.CommandText = "SELECT patient_id, finger_code, template FROM templates";
        using var rdr = cmd.ExecuteReader();
        var list = new List<MatchCandidate>();
        while (rdr.Read())
        {
            list.Add(new MatchCandidate
            {
                PatientId = rdr.GetInt32(0),
                FingerCode = rdr.GetString(1),
                Template = rdr.GetString(2)
            });
        }
        return list;
    }

    public List<MatchCandidate> GetTemplatesByPatient(int patientId)
    {
        using var con = new SqliteConnection(_connectionString);
        con.Open();
        using var cmd = con.CreateCommand();
        cmd.CommandText = "SELECT patient_id, finger_code, template FROM templates WHERE patient_id = $pid";
        cmd.Parameters.AddWithValue("$pid", patientId);
        using var rdr = cmd.ExecuteReader();
        var list = new List<MatchCandidate>();
        while (rdr.Read())
        {
            list.Add(new MatchCandidate
            {
                PatientId = rdr.GetInt32(0),
                FingerCode = rdr.GetString(1),
                Template = rdr.GetString(2)
            });
        }
        return list;
    }
}
