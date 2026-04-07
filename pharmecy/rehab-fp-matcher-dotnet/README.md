# .NET Fingerprint Matcher Service (DigitalPersona-ready)

This service exposes:
- `POST /identify` (1:N)
- `POST /verify` (1:1)
- `POST /enroll` (demo helper)
- `GET /health`
- `POST /debug-template` (DPFP parse diagnostics)
- `POST /compat-check` (strict compatibility report)

It is designed to work with your PHP app URLs like:
- `http://127.0.0.1:9100/identify`
- `http://127.0.0.1:9100/verify`

## Run

```bash
cd c:/xampp/htdocs/pharmecy/rehab-fp-matcher-dotnet
dotnet restore
dotnet run
```

## Configure PHP

In `pharmecy/includes/company_info.php`:

```php
define('REHAB_FP_IDENTIFY_URL', 'http://127.0.0.1:9100/identify');
define('REHAB_FP_VERIFY_URL', 'http://127.0.0.1:9100/verify');
define('REHAB_FP_IDENTIFY_TIMEOUT_SEC', 30);
define('REHAB_FP_MATCH_THRESHOLD', 50);
```

## Request formats

### `/identify`
```json
{
  "probe": "captured_template",
  "threshold": 50,
  "templates": [
    {"patient_id": 1, "thumb_left": "tpl_left", "thumb_right": "tpl_right"}
  ]
}
```

### `/verify`
```json
{
  "stored_left": "tpl_left",
  "stored_right": "tpl_right",
  "probe": "captured_template",
  "threshold": 50
}
```

### `/compat-check` (Option B helper)
Use same payload as `/identify`; response shows whether probe/stored templates are DPFP-compatible.

## DigitalPersona SDK integration point

Open:
- `Services/DigitalPersonaMatcher.cs`

Replace method:
- `ScoreWithDigitalPersonaOrFallback(...)`

with your actual U.are.U SDK compare logic.

The service expects a score in range `0..100` and applies threshold matching.

## Current behavior

- Runs immediately with demo fallback (`USE_DEMO_FALLBACK=true`).
- This fallback is not biometric-grade; it is only for service wiring/testing.
- For production, set `USE_DEMO_FALLBACK=false` after SDK compare is implemented.
