/**
 * Minimal matcher service for PHP rehab fingerprint flow.
 *
 * IMPORTANT:
 * - This demo uses a text-similarity matcher for templates.
 * - For production with DigitalPersona U.are.U, replace scoreTemplates()
 *   with vendor SDK comparison logic and return 0..100 score.
 */
const fs = require("fs");
const path = require("path");
const express = require("express");
const cors = require("cors");
require("dotenv").config();

const PORT = Number(process.env.PORT || 9100);
const HOST = process.env.HOST || "0.0.0.0";
const DEFAULT_THRESHOLD = Number(process.env.MATCH_THRESHOLD || 50);
const DB_PATH = process.env.DB_PATH || path.join(__dirname, "matcher-db.json");

const app = express();
app.use(cors());
app.use(express.json({ limit: "20mb" }));

function loadStore() {
  try {
    if (!fs.existsSync(DB_PATH)) return { templates: [], lastId: 0 };
    const raw = fs.readFileSync(DB_PATH, "utf8");
    const parsed = JSON.parse(raw);
    if (!parsed || !Array.isArray(parsed.templates)) return { templates: [], lastId: 0 };
    return { templates: parsed.templates, lastId: Number(parsed.lastId || 0) };
  } catch (_err) {
    return { templates: [], lastId: 0 };
  }
}

function saveStore(store) {
  fs.writeFileSync(DB_PATH, JSON.stringify(store, null, 2), "utf8");
}

const store = loadStore();

function normalizeTemplate(input) {
  return String(input || "").trim();
}

/**
 * DEMO scoring (0..100): character overlap ratio.
 * Replace this with DigitalPersona SDK matching score for production.
 */
function scoreTemplates(stored, probe) {
  const a = normalizeTemplate(stored);
  const b = normalizeTemplate(probe);
  if (!a || !b) return 0;
  if (a === b) return 100;

  // Lightweight similarity for demo only.
  const minLen = Math.min(a.length, b.length);
  const maxLen = Math.max(a.length, b.length);
  if (maxLen === 0) return 0;
  let same = 0;
  for (let i = 0; i < minLen; i += 1) {
    if (a[i] === b[i]) same += 1;
  }
  return Math.round((same / maxLen) * 100);
}

function bestMatchFromCandidates(candidates, probe, threshold) {
  let best = null;
  for (const c of candidates) {
    const score = scoreTemplates(c.template, probe);
    if (!best || score > best.score) {
      best = { patient_id: Number(c.patient_id), score };
    }
  }
  if (!best || best.score < threshold) {
    return { match: false, patient_id: null, score: best ? best.score : 0 };
  }
  return { match: true, patient_id: best.patient_id, score: best.score };
}

app.get("/health", (_req, res) => {
  res.json({
    ok: true,
    service: "rehab-fp-matcher",
    threshold: DEFAULT_THRESHOLD,
    db: DB_PATH,
    templates_count: store.templates.length
  });
});

/**
 * Helper endpoint (demo): enroll/store template in local matcher DB.
 * Body: { patient_id, template, finger_code? }
 */
app.post("/enroll", (req, res) => {
  const patientId = Number(req.body?.patient_id || 0);
  const template = normalizeTemplate(req.body?.template);
  const fingerCode = normalizeTemplate(req.body?.finger_code || "unknown");
  if (!patientId || !template) {
    return res.status(400).json({ ok: false, error: "patient_id and template are required" });
  }
  store.lastId += 1;
  store.templates.push({
    id: store.lastId,
    patient_id: patientId,
    finger_code: fingerCode,
    template,
    created_at: new Date().toISOString()
  });
  saveStore(store);
  return res.json({ ok: true });
});

/**
 * 1:N identify
 * Supports two modes:
 * A) PHP sends templates[] in body
 *    { probe, threshold?, templates:[{patient_id, template|thumb_left|thumb_right}] }
 * B) Service DB mode (no templates[] provided)
 *    { probe, threshold? } -> compare with local SQLite templates table
 */
app.post("/identify", (req, res) => {
  const probe = normalizeTemplate(req.body?.probe);
  const threshold = Number(req.body?.threshold ?? DEFAULT_THRESHOLD);
  if (!probe) {
    return res.status(400).json({ match: false, error: "probe is required" });
  }

  let candidates = [];
  if (Array.isArray(req.body?.templates) && req.body.templates.length > 0) {
    for (const t of req.body.templates) {
      const patientId = Number(t?.patient_id || 0);
      if (!patientId) continue;
      // Compatible with your current PHP payload.
      const left = normalizeTemplate(t?.template || t?.thumb_left);
      const right = normalizeTemplate(t?.thumb_right);
      if (left) candidates.push({ patient_id: patientId, template: left });
      if (right) candidates.push({ patient_id: patientId, template: right });
    }
  } else {
    candidates = store.templates.map((t) => ({
      patient_id: Number(t.patient_id),
      template: String(t.template || ""),
      finger_code: String(t.finger_code || "unknown")
    }));
  }

  if (candidates.length === 0) {
    return res.json({ match: false, patient_id: null, score: 0 });
  }

  const result = bestMatchFromCandidates(candidates, probe, threshold);
  return res.json(result);
});

/**
 * 1:1 verify
 * Supports:
 * A) Your PHP-compatible mode:
 *    { stored_left, stored_right, probe, threshold? }
 * B) patient_id mode:
 *    { patient_id, probe, threshold? } - checks all templates for that patient from local DB
 */
app.post("/verify", (req, res) => {
  const probe = normalizeTemplate(req.body?.probe);
  const threshold = Number(req.body?.threshold ?? DEFAULT_THRESHOLD);
  if (!probe) {
    return res.status(400).json({ match: false, error: "probe is required" });
  }

  let candidates = [];
  if (req.body?.patient_id) {
    const patientId = Number(req.body.patient_id);
    candidates = store.templates
      .filter((t) => Number(t.patient_id) === patientId)
      .map((t) => ({ template: String(t.template || "") }));
  } else {
    const left = normalizeTemplate(req.body?.stored_left);
    const right = normalizeTemplate(req.body?.stored_right);
    if (left) candidates.push({ template: left });
    if (right) candidates.push({ template: right });
  }

  if (candidates.length === 0) {
    return res.json({ match: false, score: 0 });
  }
  const result = bestMatchFromCandidates(candidates, probe, threshold);
  return res.json({ match: !!result.match, score: result.score });
});

app.listen(PORT, HOST, () => {
  console.log(`[rehab-fp-matcher] listening on http://${HOST}:${PORT}`);
  console.log(`[rehab-fp-matcher] db: ${DB_PATH}`);
});

