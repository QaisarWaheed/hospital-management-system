# Rehab Fingerprint Matcher (Minimal Service)

Local HTTP service for your PHP/JS hospital system.
Uses a JSON file store by default (no C++ build tools required).

## Endpoints

- `POST /identify` -> 1:N identify
- `POST /verify` -> 1:1 verify
- `POST /enroll` -> demo helper to store local templates
- `GET /health` -> service status

## 1) Install and run

```bash
cd c:/xampp/htdocs/pharmecy/rehab-fp-matcher
npm install
copy .env.example .env
npm start
```

Service starts at:
- `http://127.0.0.1:9100`

## 2) Connect from PHP

In `pharmecy/includes/company_info.php`, add:

```php
define('REHAB_FP_IDENTIFY_URL', 'http://127.0.0.1:9100/identify');
define('REHAB_FP_VERIFY_URL', 'http://127.0.0.1:9100/verify');
define('REHAB_FP_IDENTIFY_TIMEOUT_SEC', 30);
define('REHAB_FP_MATCH_THRESHOLD', 50);
```

## 3) API payloads

### POST `/identify` (PHP-compatible)

```json
{
  "probe": "captured_template",
  "threshold": 50,
  "templates": [
    { "patient_id": 1, "thumb_left": "tpl1", "thumb_right": "tpl2" },
    { "patient_id": 2, "thumb_left": "tpl3", "thumb_right": "tpl4" }
  ]
}
```

Response:

```json
{ "match": true, "patient_id": 1, "score": 76 }
```

or

```json
{ "match": false, "patient_id": null, "score": 40 }
```

### POST `/verify` (PHP-compatible)

```json
{
  "stored_left": "tpl_left",
  "stored_right": "tpl_right",
  "probe": "captured_template",
  "threshold": 50
}
```

Response:

```json
{ "match": true, "score": 79 }
```

### POST `/verify` (patient ID mode)

```json
{
  "patient_id": 1,
  "probe": "captured_template",
  "threshold": 50
}
```

## 4) Quick health test

Open in browser:
- [http://127.0.0.1:9100/health](http://127.0.0.1:9100/health)

## 5) Important production note

This minimal service currently uses a simple text similarity score for demo behavior.
For real DigitalPersona U.are.U matching, replace `scoreTemplates()` in `server.js`
with DigitalPersona SDK matching logic and return score `0..100`.

Your PHP side is already prepared to consume this score + threshold.
