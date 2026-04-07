REHAB fingerprint matcher — local stub (DigitalPersona / HID)
=============================================================

What you need
-------------
1) PHP must know where the matcher listens. In includes/company_info.php uncomment:

   define('REHAB_FP_VERIFY_URL', 'http://127.0.0.1:9100/verify');
   define('REHAB_FP_IDENTIFY_URL', 'http://127.0.0.1:9100/identify');
   define('REHAB_FP_IDENTIFY_TIMEOUT_SEC', 30);

2) Start this HTTP service on the SAME PC as XAMPP (or any host PHP can reach; use that
   host in the URLs above instead of 127.0.0.1).

3) The JSON contract is defined in includes/REHAB_FP_MATCHER_API.txt.

Run the stub (Node.js)
----------------------
From this folder:

   node server.js

Default bind: http://127.0.0.1:9100

Stub behavior
-------------
server.js implements /verify and /identify with BYTE-EXACT string comparison of templates.
That does NOT work for real U.are.U scans (each capture differs). It is only useful to
confirm wiring from PHP → matcher.

Production: replace sdkVerify() in server.js with HID DigitalPersona (or your vendor)
verification:
  - Decode each stored template and the probe to binary FMD (or whatever your SDK expects).
  - Call the vendor “verify / match” API that returns match + score / FAR threshold.
  - Return { "match": true } or { "match": true, "patient_id": N } as in the API doc.

Typical production stack on Windows
-----------------------------------
- Small C# console or ASP.NET Core minimal API using HID’s Windows SDK / One Touch for
  Java/.NET samples (verification against enrolled FMDs).
- Or keep Node/Python as the HTTP shell and invoke a native DLL / child process that
  runs the vendor matcher — the HTTP shape stays the same.

Firewall
--------
If matcher and Apache are on one machine, 127.0.0.1 needs no extra rules.
