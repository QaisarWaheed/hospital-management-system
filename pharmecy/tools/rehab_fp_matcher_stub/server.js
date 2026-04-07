'use strict';

/**
 * Local HTTP matcher for REHAB fingerprint flows.
 * Contract: ../includes/REHAB_FP_MATCHER_API.txt
 *
 * Replace sdkVerify() with HID DigitalPersona (or other) template comparison.
 */

const http = require('http');

const HOST = process.env.REHAB_FP_MATCHER_HOST || '127.0.0.1';
const PORT = Number(process.env.REHAB_FP_MATCHER_PORT || 9100);

/** @returns {boolean} */
function sdkVerify(stored, probe) {
  if (stored == null || probe == null) return false;
  return String(stored) === String(probe);
}

/**
 * @param {string} probe
 * @param {{ patient_id: number, thumb_left?: string, thumb_right?: string }[]} templates
 */
function identify(probe, templates) {
  if (!Array.isArray(templates)) {
    return { match: false };
  }
  for (const t of templates) {
    if (sdkVerify(t.thumb_left, probe) || sdkVerify(t.thumb_right, probe)) {
      return { match: true, patient_id: t.patient_id };
    }
  }
  return { match: false };
}

function json(res, status, obj) {
  const body = JSON.stringify(obj);
  res.writeHead(status, {
    'Content-Type': 'application/json; charset=utf-8',
    'Content-Length': Buffer.byteLength(body),
  });
  res.end(body);
}

function readBody(req) {
  return new Promise((resolve, reject) => {
    const chunks = [];
    req.on('data', (c) => chunks.push(c));
    req.on('end', () => resolve(Buffer.concat(chunks).toString('utf8')));
    req.on('error', reject);
  });
}

const server = http.createServer(async (req, res) => {
  const u = new URL(req.url || '/', `http://${req.headers.host || 'localhost'}`);
  const path = u.pathname.replace(/\/$/, '') || '/';

  if (req.method !== 'POST' || (path !== '/verify' && path !== '/identify')) {
    json(res, 404, { error: 'not_found' });
    return;
  }

  let payload;
  try {
    const raw = await readBody(req);
    payload = raw ? JSON.parse(raw) : {};
  } catch (e) {
    json(res, 400, { error: 'invalid_json' });
    return;
  }

  try {
    if (path === '/verify') {
      const left = payload.stored_left;
      const right = payload.stored_right;
      const probe = payload.probe;
      const match = sdkVerify(left, probe) || sdkVerify(right, probe);
      json(res, 200, { match });
      return;
    }

    const probe = payload.probe;
    const templates = payload.templates;
    const result = identify(probe, templates);
    json(res, 200, result);
  } catch (e) {
    json(res, 500, { error: 'internal' });
  }
});

server.listen(PORT, HOST, () => {
  console.log(`REHAB FP matcher stub listening on http://${HOST}:${PORT}`);
  console.log('POST /verify and POST /identify — see README.txt');
});
