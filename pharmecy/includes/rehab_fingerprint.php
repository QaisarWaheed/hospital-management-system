<?php
/**
 * Rehabilitation-center fingerprint enrollment and identify/verify.
 *
 * Matcher HTTP contract: includes/REHAB_FP_MATCHER_API.txt
 * - REHAB_FP_VERIFY_URL — 1:1 POST { stored_left, stored_right, probe } → { match }
 * - REHAB_FP_IDENTIFY_URL — 1:N POST { probe, templates[] } → { match, patient_id? }
 *
 * DigitalPersona U.are.U: use WebSdk in browser; matching needs vendor SDK or the URLs above.
 */

if (!defined('REHAB_FP_MIN_TEMPLATE_LEN')) {
    define('REHAB_FP_MIN_TEMPLATE_LEN', 64);
}

if (!defined('REHAB_FP_IDENTIFY_TIMEOUT_SEC')) {
    define('REHAB_FP_IDENTIFY_TIMEOUT_SEC', 30);
}

if (!defined('REHAB_FP_MATCH_THRESHOLD')) {
    define('REHAB_FP_MATCH_THRESHOLD', 50);
}

function rehabilitation_branch_ids()
{
    return array(15, 24);
}

function is_rehabilitation_branch($branch_id)
{
    return in_array((int) $branch_id, rehabilitation_branch_ids(), true);
}

/** True when a captured/pasted template string is long enough to store or match. */
function rehab_fingerprint_template_valid($template)
{
    return strlen(trim((string) ($template ?? ''))) >= REHAB_FP_MIN_TEMPLATE_LEN;
}

/** Both thumbs captured — required only when persisting enrollment, not for token save. */
function rehab_fingerprint_both_thumbs_provided($thumb_left, $thumb_right)
{
    return rehab_fingerprint_template_valid($thumb_left) && rehab_fingerprint_template_valid($thumb_right);
}

/**
 * Save fingerprints when both thumbs are present; otherwise skip (token creation still allowed).
 *
 * @return bool mysqli success, or true when nothing to save
 */
function rehab_fingerprint_save_if_provided($con, $patient_id, $thumb_left, $thumb_right)
{
    if (!rehab_fingerprint_both_thumbs_provided($thumb_left, $thumb_right)) {
        return true;
    }
    return (bool) save_patient_fingerprints($con, $patient_id, $thumb_left, $thumb_right);
}

/**
 * Returning patient with fingerprints on file: verify only when staff supplied a probe.
 *
 * @return bool true if verification passed or was skipped
 */
function rehab_fingerprint_verify_if_probe_provided($con, $patient_id, $probe)
{
    if (!rehab_patient_has_fingerprints($con, $patient_id)) {
        return true;
    }
    $probe = trim((string) ($probe ?? ''));
    if ($probe === '' || !rehab_fingerprint_template_valid($probe)) {
        return true;
    }
    return verify_rehab_patient_fingerprint($con, $patient_id, $probe);
}

function rehab_patient_has_fingerprints($con, $patient_id)
{
    $pid = (int) $patient_id;
    $q = mysqli_query($con, "SELECT 1 FROM patient_fingerprints WHERE patient_id = '$pid' LIMIT 1");
    if ($q && mysqli_num_rows($q) > 0) {
        return true;
    }
    if (!rehab_fp_has_templates_table($con)) {
        return false;
    }
    $q2 = mysqli_query($con, "SELECT 1 FROM patient_fingerprint_templates WHERE patient_id = '$pid' LIMIT 1");
    return $q2 && mysqli_num_rows($q2) > 0;
}

function save_patient_fingerprints($con, $patient_id, $thumb_left, $thumb_right)
{
    $pid = (int) $patient_id;
    $tl = mysqli_real_escape_string($con, $thumb_left);
    $tr = mysqli_real_escape_string($con, $thumb_right);
    $cd = mysqli_real_escape_string($con, $GLOBALS['current_date']);
    $sql = "INSERT INTO patient_fingerprints (patient_id, thumb_left, thumb_right, created)
            VALUES ('$pid', '$tl', '$tr', '$cd')
            ON DUPLICATE KEY UPDATE thumb_left = '$tl', thumb_right = '$tr', updated = '$cd'";
    $ok = mysqli_query($con, $sql);
    if ($ok) {
        rehab_fp_store_additional_template($con, $pid, 'left', $thumb_left);
        rehab_fp_store_additional_template($con, $pid, 'right', $thumb_right);
    }
    return $ok;
}

function rehab_fp_external_verify($stored_left, $stored_right, $probe)
{
    if (!defined('REHAB_FP_VERIFY_URL') || !REHAB_FP_VERIFY_URL || !is_string(REHAB_FP_VERIFY_URL)) {
        return null;
    }
    $url = REHAB_FP_VERIFY_URL;
    $payload = json_encode(array(
        'stored_left' => $stored_left,
        'stored_right' => $stored_right,
        'probe' => $probe,
        'threshold' => (int) REHAB_FP_MATCH_THRESHOLD,
    ));
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        $body = curl_exec($ch);
        curl_close($ch);
    } else {
        $ctx = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 8,
            ),
        ));
        $body = @file_get_contents($url, false, $ctx);
    }
    if ($body === false || $body === '') {
        return false;
    }
    $j = json_decode($body, true);
    if (!is_array($j)) {
        return false;
    }
    if (array_key_exists('score', $j)) {
        return ((float) $j['score']) >= (float) REHAB_FP_MATCH_THRESHOLD;
    }
    if (array_key_exists('match', $j)) {
        return (bool) $j['match'];
    }
    return false;
}

/**
 * POST JSON string body, return response body or null on failure / HTTP error.
 *
 * @param string $json_string
 * @return string|null
 */
function rehab_fp_http_post_json_body($url, $json_string, $timeout_sec)
{
    $timeout_sec = max(5, min(120, (int) $timeout_sec));
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout_sec);
        $body = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code >= 400 || !is_string($body)) {
            return null;
        }
        return $body;
    }
    $ctx = stream_context_create(array(
        'http' => array(
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => $json_string,
            'timeout' => $timeout_sec,
        ),
    ));
    $body = @file_get_contents($url, false, $ctx);
    if ($body === false) {
        return null;
    }
    return $body;
}

/**
 * Single HTTP call 1:N identify. Caller checks ['handled'] === true to trust result.
 *
 * @return array{handled:bool, patient_id?:int|null}
 */
function rehab_fp_external_identify_one_shot($con, $probe)
{
    if (!defined('REHAB_FP_IDENTIFY_URL') || !REHAB_FP_IDENTIFY_URL || !is_string(REHAB_FP_IDENTIFY_URL)) {
        return array('handled' => false);
    }
    $templates = rehab_fp_all_templates($con);
    $payload = json_encode(array(
        'probe' => $probe,
        'templates' => $templates,
        'threshold' => (int) REHAB_FP_MATCH_THRESHOLD,
    ));
    if ($payload === false) {
        return array('handled' => false);
    }
    $body = rehab_fp_http_post_json_body(REHAB_FP_IDENTIFY_URL, $payload, REHAB_FP_IDENTIFY_TIMEOUT_SEC);
    if ($body === null || $body === '') {
        return array('handled' => false);
    }
    $j = json_decode($body, true);
    if (!is_array($j)) {
        return array('handled' => false);
    }
    if (!empty($j['match']) && isset($j['patient_id']) && (int) $j['patient_id'] > 0) {
        return array('handled' => true, 'patient_id' => (int) $j['patient_id']);
    }
    if (array_key_exists('score', $j) && isset($j['patient_id']) && (int) $j['patient_id'] > 0) {
        if (((float) $j['score']) >= (float) REHAB_FP_MATCH_THRESHOLD) {
            return array('handled' => true, 'patient_id' => (int) $j['patient_id']);
        }
        return array('handled' => true, 'patient_id' => null);
    }
    if (isset($j['candidates']) && is_array($j['candidates'])) {
        $best_id = null;
        $best_score = -INF;
        foreach ($j['candidates'] as $cand) {
            if (!is_array($cand) || !isset($cand['patient_id']) || !isset($cand['score'])) {
                continue;
            }
            $cid = (int) $cand['patient_id'];
            $cs = (float) $cand['score'];
            if ($cid > 0 && $cs > $best_score) {
                $best_score = $cs;
                $best_id = $cid;
            }
        }
        if ($best_id !== null && $best_score >= (float) REHAB_FP_MATCH_THRESHOLD) {
            return array('handled' => true, 'patient_id' => (int) $best_id);
        }
        return array('handled' => true, 'patient_id' => null);
    }
    if (!empty($j['match']) && isset($j['patient_id']) && (int) $j['patient_id'] > 0) {
        return array('handled' => true, 'patient_id' => (int) $j['patient_id']);
    }
    if (array_key_exists('match', $j) && $j['match'] === false) {
        return array('handled' => true, 'patient_id' => null);
    }
    return array('handled' => false);
}

function rehab_probe_matches_stored_pair($stored_left, $stored_right, $probe_raw)
{
    $probe = trim((string) $probe_raw);
    if (strlen($probe ?? '') < REHAB_FP_MIN_TEMPLATE_LEN) {
        return false;
    }
    $ext = rehab_fp_external_verify($stored_left, $stored_right, $probe);
    if ($ext !== null) {
        return $ext;
    }
    return hash_equals($stored_left, $probe) || hash_equals($stored_right, $probe);
}

function verify_rehab_patient_fingerprint($con, $patient_id, $probe_raw)
{
    $probe = trim((string) $probe_raw);
    if (strlen($probe ?? '') < REHAB_FP_MIN_TEMPLATE_LEN) {
        return false;
    }
    $pid = (int) $patient_id;
    if (rehab_fp_has_templates_table($con)) {
        $qt = mysqli_query($con, "SELECT template_data FROM patient_fingerprint_templates WHERE patient_id = '$pid' ORDER BY id DESC");
        if ($qt) {
            while ($rt = mysqli_fetch_assoc($qt)) {
                $tpl = (string) $rt['template_data'];
                if (rehab_probe_matches_stored_pair($tpl, $tpl, $probe)) {
                    return true;
                }
            }
        }
    }
    $q = mysqli_query($con, "SELECT thumb_left, thumb_right FROM patient_fingerprints WHERE patient_id = '$pid' LIMIT 1");
    if (!$q || mysqli_num_rows($q) != 1) {
        return false;
    }
    $row = mysqli_fetch_assoc($q);
    return rehab_probe_matches_stored_pair($row['thumb_left'], $row['thumb_right'], $probe);
}

/**
 * 1:N search on stored templates (same matching rules as single-patient verify).
 *
 * @return int|null patient_id
 */
function rehab_find_patient_by_fingerprint($con, $probe_raw)
{
    $probe = trim((string) $probe_raw);
    if (strlen($probe ?? '') < REHAB_FP_MIN_TEMPLATE_LEN) {
        return null;
    }
    $shot = rehab_fp_external_identify_one_shot($con, $probe);
    if (!empty($shot['handled'])) {
        return isset($shot['patient_id']) && $shot['patient_id'] !== null
            ? (int) $shot['patient_id']
            : null;
    }
    $templates = rehab_fp_all_templates($con);
    foreach ($templates as $tpl) {
        if (!is_array($tpl) || !isset($tpl['patient_id']) || !isset($tpl['template'])) {
            continue;
        }
        if (rehab_probe_matches_stored_pair($tpl['template'], $tpl['template'], $probe)) {
            return (int) $tpl['patient_id'];
        }
    }
    return null;
}

function rehab_fp_has_templates_table($con)
{
    static $exists = null;
    if ($exists !== null) {
        return $exists;
    }
    $q = mysqli_query($con, "SHOW TABLES LIKE 'patient_fingerprint_templates'");
    $exists = $q && mysqli_num_rows($q) > 0;
    return $exists;
}

function rehab_fp_store_additional_template($con, $patient_id, $finger_code, $template_data)
{
    if (!rehab_fp_has_templates_table($con)) {
        return false;
    }
    $pid = (int) $patient_id;
    $fc = mysqli_real_escape_string($con, substr((string) $finger_code, 0, 20));
    $tpl = mysqli_real_escape_string($con, (string) $template_data);
    $cd = mysqli_real_escape_string($con, $GLOBALS['current_date']);
    return mysqli_query(
        $con,
        "INSERT INTO patient_fingerprint_templates (patient_id, finger_code, template_data, created)
         VALUES ('$pid', '$fc', '$tpl', '$cd')"
    );
}

function rehab_fp_all_templates($con)
{
    $out = array();
    $q = mysqli_query($con, 'SELECT patient_id, thumb_left, thumb_right FROM patient_fingerprints');
    if ($q) {
        while ($row = mysqli_fetch_assoc($q)) {
            $pid = (int) $row['patient_id'];
            $left = (string) $row['thumb_left'];
            $right = (string) $row['thumb_right'];
            if ($left !== '') {
                $out[] = array(
                    'patient_id' => $pid,
                    'finger_code' => 'left',
                    'template' => $left,
                    'thumb_left' => $left,
                    'thumb_right' => $right,
                );
            }
            if ($right !== '') {
                $out[] = array(
                    'patient_id' => $pid,
                    'finger_code' => 'right',
                    'template' => $right,
                    'thumb_left' => $left,
                    'thumb_right' => $right,
                );
            }
        }
    }
    if (rehab_fp_has_templates_table($con)) {
        $qt = mysqli_query($con, 'SELECT patient_id, finger_code, template_data FROM patient_fingerprint_templates');
        if ($qt) {
            while ($row = mysqli_fetch_assoc($qt)) {
                $tpl = (string) $row['template_data'];
                if ($tpl === '') {
                    continue;
                }
                $out[] = array(
                    'patient_id' => (int) $row['patient_id'],
                    'finger_code' => (string) $row['finger_code'],
                    'template' => $tpl,
                    'thumb_left' => $tpl,
                    'thumb_right' => $tpl,
                );
            }
        }
    }
    return $out;
}

/**
 * Last visit at this branch, otherwise last visit at any branch.
 *
 * @return array<string, string>|null
 */
function rehab_get_last_visit_snapshot($con, $patient_id, $branch_id)
{
    $pid = (int) $patient_id;
    $bid = (int) $branch_id;
    $q = mysqli_query($con, "SELECT doctor_id, tokan_type_id FROM tokans WHERE patient_id = '$pid' AND branch_id = '$bid' ORDER BY id DESC LIMIT 1");
    if ($q && mysqli_num_rows($q) === 1) {
        return mysqli_fetch_assoc($q);
    }
    $q2 = mysqli_query($con, "SELECT doctor_id, tokan_type_id FROM tokans WHERE patient_id = '$pid' ORDER BY id DESC LIMIT 1");
    if ($q2 && mysqli_num_rows($q2) === 1) {
        return mysqli_fetch_assoc($q2);
    }
    return null;
}

/**
 * Loads HID DigitalPersona browser bundles (U.are.U via local agent). Emits once per request.
 * Air-gapped sites: define('REHAB_DIGITALPERSONA_BROWSER_SDK', false) in company_info.php
 * and host copies of the scripts under /pharmecy/js/digitalpersona/ instead.
 */
function rehab_fingerprint_print_digitalpersona_scripts()
{
    static $printed = false;
    if ($printed) {
        return;
    }
    $printed = true;
    if (defined('REHAB_DIGITALPERSONA_BROWSER_SDK') && REHAB_DIGITALPERSONA_BROWSER_SDK === false) {
        return;
    }
    $websdk = 'https://unpkg.com/@digitalpersona/websdk@1.1.0/dist/websdk.client.ui.min.js';
    $core = 'https://unpkg.com/@digitalpersona/core@0.2.6/dist/es5.bundles/index.umd.js';
    $devices = 'https://unpkg.com/@digitalpersona/devices@0.2.6/dist/es5.bundles/index.umd.js';
    ?>
    <script src="<?php echo htmlspecialchars($websdk, ENT_QUOTES, 'UTF-8'); ?>"></script>
    <script src="<?php echo htmlspecialchars($core, ENT_QUOTES, 'UTF-8'); ?>"></script>
    <script src="<?php echo htmlspecialchars($devices, ENT_QUOTES, 'UTF-8'); ?>"></script>
    <script src="js/rehab_digitalpersona_capture.js"></script>
    <?php
}

function rehab_fingerprint_print_capture_try_script()
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    ?>
    <script>
    function rehabFpTryCapture(side) {
        var fieldId = side === 'left' ? 'fp_thumb_left' : 'fp_thumb_right';
        var base = <?php echo json_encode(defined('REHAB_FP_LOCAL_CAPTURE_URL') && REHAB_FP_LOCAL_CAPTURE_URL ? REHAB_FP_LOCAL_CAPTURE_URL : ''); ?>;
        if (base) {
            var url = base + (base.indexOf('?') >= 0 ? '&' : '?') + 'finger=' + encodeURIComponent(side);
            fetch(url).then(function (r) { return r.text(); }).then(function (t) {
                var el = document.getElementById(fieldId);
                if (el) { el.value = t.trim(); }
            }).catch(function () {
                alert('Could not reach local capture service. Paste the template manually.');
            });
            return;
        }
        if (typeof rehabDpCaptureToField === 'function') {
            rehabDpCaptureToField(fieldId).catch(function () {});
            return;
        }
        alert('DigitalPersona browser SDK did not load. Check internet (unpkg.com), refresh the page, install the Lite Client, or paste the template manually.');
    }
    </script>
    <?php
}

/**
 * Identify returning rehab patient by thumb before entering registration data (patient_registeration.php).
 *
 * @param int $default_next_patient_id next id used when registering a brand-new patient
 */
function rehab_fingerprint_identify_block($default_next_patient_id)
{
    rehab_fingerprint_print_digitalpersona_scripts();
    rehab_fingerprint_print_capture_try_script();
    $ajax_url = 'ajax_rehab_identify_patient.php';
    ?>
    <div class="col-md-12" style="margin-top: 0;">
        <div id="rehab_status_banner" class="alert alert-info" role="status" style="display:none;"></div>
        <fieldset class="border p-2 mb-2">
            <legend class="w-auto" style="font-size: 14px;"><strong>Identify patient (optional)</strong></legend>
            <p class="small text-muted mb-2">
                Fingerprint is <strong>optional</strong> — you can fill patient details and save a token without scanning.
                To identify a returning patient: scan <strong>one thumb</strong> first. If it matches, the form fills automatically.
                If not, scan the <strong>other thumb</strong> once. If still no match, register as a new patient (you may save without fingerprints).
            </p>
            <input type="hidden" name="rehab_existing_patient_id" id="rehab_existing_patient_id" value="">
            <textarea id="fp_identify_probe_1" class="form-control" rows="2" style="position:absolute;left:-9999px;opacity:0;height:10px;" tabindex="-1" aria-hidden="true" autocomplete="off"></textarea>
            <textarea id="fp_identify_probe_2" class="form-control" rows="2" style="position:absolute;left:-9999px;opacity:0;height:10px;" tabindex="-1" aria-hidden="true" autocomplete="off"></textarea>
            <button type="button" class="btn btn-primary btn-sm" id="rehab_btn_identify_1" onclick="rehabRegistrationIdentifyThumb(1); return false;">Scan first thumb</button>
            <button type="button" class="btn btn-secondary btn-sm" id="rehab_btn_identify_2" onclick="rehabRegistrationIdentifyThumb(2); return false;" style="display:none;">Scan other thumb (second scan only)</button>
            <button type="button" class="btn btn-outline-secondary btn-sm mt-1" id="rehab_show_thumb_fields" style="display:none" onclick="var w=document.getElementById('rehab_fp_enrollment_wrap');if(w){w.style.display='block';} this.style.display='none';">Edit thumb templates</button>
            <button type="button" class="btn btn-outline-info btn-sm mt-1" onclick="rehabFpToggleDebugTemplates(); return false;">Show/Hide templates for copy</button>
            <div id="rehab_fp_debug_wrap" style="display:none;margin-top:8px;">
                <div class="row">
                    <div class="col-md-6">
                        <label>Captured first thumb (probe #1)</label>
                        <textarea id="rehab_dbg_probe1" class="form-control" rows="2" readonly></textarea>
                        <button type="button" class="btn btn-sm btn-outline-dark mt-1" onclick="rehabFpCopyField('rehab_dbg_probe1'); return false;">Copy</button>
                    </div>
                    <div class="col-md-6">
                        <label>Captured second thumb (probe #2)</label>
                        <textarea id="rehab_dbg_probe2" class="form-control" rows="2" readonly></textarea>
                        <button type="button" class="btn btn-sm btn-outline-dark mt-1" onclick="rehabFpCopyField('rehab_dbg_probe2'); return false;">Copy</button>
                    </div>
                </div>
                <div class="row" style="margin-top:6px;">
                    <div class="col-md-6">
                        <label>New patient left thumb (saved value)</label>
                        <textarea id="rehab_dbg_left" class="form-control" rows="2" readonly></textarea>
                        <button type="button" class="btn btn-sm btn-outline-dark mt-1" onclick="rehabFpCopyField('rehab_dbg_left'); return false;">Copy</button>
                    </div>
                    <div class="col-md-6">
                        <label>New patient right thumb (saved value)</label>
                        <textarea id="rehab_dbg_right" class="form-control" rows="2" readonly></textarea>
                        <button type="button" class="btn btn-sm btn-outline-dark mt-1" onclick="rehabFpCopyField('rehab_dbg_right'); return false;">Copy</button>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
    <script>
    (function () {
        var ajaxUrl = <?php echo json_encode($ajax_url, JSON_UNESCAPED_SLASHES); ?>;
        window.rehabRegistrationDefaultRegId = <?php echo (int) $default_next_patient_id; ?>;
        window.rehabFpToggleDebugTemplates = function () {
            var box = document.getElementById('rehab_fp_debug_wrap');
            if (!box) { return; }
            box.style.display = box.style.display === 'none' ? 'block' : 'none';
        };
        window.rehabFpCopyField = function (id) {
            var el = document.getElementById(id);
            if (!el) { return; }
            el.focus();
            el.select();
            try {
                document.execCommand('copy');
            } catch (e) {}
            if (navigator.clipboard && el.value) {
                navigator.clipboard.writeText(el.value).catch(function () {});
            }
        };
        window.rehabFpSyncDebugTemplateFields = function () {
            var p1 = document.getElementById('fp_identify_probe_1');
            var p2 = document.getElementById('fp_identify_probe_2');
            var l = document.getElementById('fp_thumb_left');
            var r = document.getElementById('fp_thumb_right');
            var d1 = document.getElementById('rehab_dbg_probe1');
            var d2 = document.getElementById('rehab_dbg_probe2');
            var dl = document.getElementById('rehab_dbg_left');
            var dr = document.getElementById('rehab_dbg_right');
            if (d1 && p1) { d1.value = p1.value || ''; }
            if (d2 && p2) { d2.value = p2.value || ''; }
            if (dl && l) { dl.value = l.value || ''; }
            if (dr && r) { dr.value = r.value || ''; }
        };

        window.rehabRegistrationIdentifyThumb = function (step) {
            var tid = step === 1 ? 'fp_identify_probe_1' : 'fp_identify_probe_2';
            var base = <?php echo json_encode(defined('REHAB_FP_LOCAL_CAPTURE_URL') && REHAB_FP_LOCAL_CAPTURE_URL ? REHAB_FP_LOCAL_CAPTURE_URL : ''); ?>;
            function afterProbeReady() {
                var probe = document.getElementById(tid).value.trim();
                rehabFpSyncDebugTemplateFields();
                if (probe.length < <?php echo (int) REHAB_FP_MIN_TEMPLATE_LEN; ?>) {
                    alert('Capture or paste a full fingerprint template first.');
                    return;
                }
                var body = 'fp_probe=' + encodeURIComponent(probe);
                fetch(ajaxUrl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
                    body: body,
                    credentials: 'same-origin'
                }).then(function (r) {
                    return r.text().then(function (txt) {
                        var data;
                        try {
                            data = JSON.parse(txt);
                        } catch (e) {
                            var preview = (txt || '').replace(/</g, '&lt;').slice(0, 300);
                            throw new Error(preview ? ('Server did not return JSON. Check ajax_rehab_identify_patient.php. First bytes: ' + preview) : 'Empty response from identify.');
                        }
                        if (!r.ok) {
                            throw new Error((data && data.error) ? data.error : ('HTTP ' + r.status));
                        }
                        return data;
                    });
                }).then(function (data) {
                    if (!data.ok) {
                        alert(data.error || 'Identify request failed.');
                        return;
                    }
                    if (data.match) {
                        rehabRegistrationApplyMatch(data);
                    } else if (step === 1) {
                        var p1 = document.getElementById('fp_identify_probe_1').value.trim();
                        var fl = document.getElementById('fp_thumb_left');
                        if (fl) { fl.value = p1; }
                        rehabFpSyncDebugTemplateFields();
                        document.getElementById('rehab_btn_identify_1').style.display = 'none';
                        document.getElementById('rehab_btn_identify_2').style.display = 'inline-block';
                        var b = document.getElementById('rehab_status_banner');
                        b.style.display = 'block';
                        b.className = 'alert alert-warning';
                        b.textContent = 'First thumb saved for enrollment. Scan the other thumb once. If no match, you will register as a new patient — no third scan needed.';
                    } else {
                        rehabRegistrationMarkNewPatient();
                    }
                }).catch(function (err) {
                    alert(err && err.message ? err.message : 'Could not reach identify service. Check you are logged in and the pharmacy app URL is correct.');
                });
            }
            if (base) {
                var url = base + (base.indexOf('?') >= 0 ? '&' : '?') + 'finger=' + encodeURIComponent(step === 1 ? 'any' : 'any2');
                fetch(url).then(function (r) { return r.text(); }).then(function (t) {
                    document.getElementById(tid).value = t.trim();
                    afterProbeReady();
                }).catch(function () {
                    alert('Local capture service failed.');
                });
                return;
            }
            if (typeof rehabDpCaptureToField === 'function') {
                rehabDpCaptureToField(tid).then(afterProbeReady).catch(function () {});
                return;
            }
            alert('DigitalPersona SDK not loaded. Paste a template into a visible field or install Lite Client.');
        };

        window.rehabRegistrationApplyMatch = function (data) {
            var p = data.patient || {};
            var reg = document.getElementById('reg_display_id');
            if (reg) { reg.value = String(p.id || ''); }
            var n = document.getElementById('reg_name');
            if (n) { n.value = p.name || ''; }
            var a = document.getElementById('reg_age');
            if (a) { a.value = p.age != null ? String(p.age) : ''; }
            var ph = document.getElementById('reg_phone');
            if (ph) { ph.value = p.phone || ''; }
            var g = document.getElementById('reg_gender');
            if (g) { g.value = p.gender != null ? String(p.gender) : ''; }
            var d = document.getElementById('reg_doctor_id');
            if (d && data.last_visit && data.last_visit.doctor_id) {
                d.value = String(data.last_visit.doctor_id);
            }
            if (data.last_visit && data.last_visit.tokan_type_id) {
                var tt = String(data.last_visit.tokan_type_id);
                var radio = document.querySelector('input[name="tokan_type"][value="' + tt + '"]');
                if (radio) {
                    radio.checked = true;
                    if (typeof radio.onclick === 'function') { radio.onclick(); }
                    else { radio.click(); }
                }
            }
            document.getElementById('rehab_existing_patient_id').value = String(p.id || '');
            var wrap = document.getElementById('rehab_fp_enrollment_wrap');
            if (wrap) { wrap.style.display = 'none'; }
            document.getElementById('rehab_btn_identify_1').style.display = 'none';
            document.getElementById('rehab_btn_identify_2').style.display = 'none';
            var editTh = document.getElementById('rehab_show_thumb_fields');
            if (editTh) { editTh.style.display = 'none'; }
            var b = document.getElementById('rehab_status_banner');
            b.style.display = 'block';
            b.className = 'alert alert-success';
            b.textContent = 'Returning patient — details loaded from the last visit. Adjust if needed and save token.';
        };

        window.rehabRegistrationMarkNewPatient = function () {
            var p2 = document.getElementById('fp_identify_probe_2').value.trim();
            var fr = document.getElementById('fp_thumb_right');
            if (fr) { fr.value = p2; }
            rehabFpSyncDebugTemplateFields();
            document.getElementById('rehab_existing_patient_id').value = '';
            var reg = document.getElementById('reg_display_id');
            if (reg) { reg.value = String(window.rehabRegistrationDefaultRegId); }
            var wrap = document.getElementById('rehab_fp_enrollment_wrap');
            if (wrap) { wrap.style.display = 'none'; }
            document.getElementById('rehab_btn_identify_1').style.display = 'none';
            document.getElementById('rehab_btn_identify_2').style.display = 'none';
            var editTh = document.getElementById('rehab_show_thumb_fields');
            if (editTh) { editTh.style.display = 'inline-block'; }
            var b = document.getElementById('rehab_status_banner');
            b.style.display = 'block';
            b.className = 'alert alert-secondary';
            b.textContent = 'New patient — thumb scans saved if captured. Complete patient details and save (fingerprints optional). Use “Edit thumb templates” only to paste/capture again.';
        };

        window.rehabRegistrationOnReset = function () {
            document.getElementById('rehab_existing_patient_id').value = '';
            document.getElementById('fp_identify_probe_1').value = '';
            document.getElementById('fp_identify_probe_2').value = '';
            var fl = document.getElementById('fp_thumb_left');
            var frt = document.getElementById('fp_thumb_right');
            if (fl) { fl.value = ''; }
            if (frt) { frt.value = ''; }
            rehabFpSyncDebugTemplateFields();
            document.getElementById('rehab_btn_identify_1').style.display = 'inline-block';
            document.getElementById('rehab_btn_identify_2').style.display = 'none';
            var wrap = document.getElementById('rehab_fp_enrollment_wrap');
            if (wrap) { wrap.style.display = 'none'; }
            var reg = document.getElementById('reg_display_id');
            var fm = document.querySelector('form[data-default-reg-id]');
            var def = fm ? fm.getAttribute('data-default-reg-id') : String(window.rehabRegistrationDefaultRegId);
            if (reg) { reg.value = def || String(window.rehabRegistrationDefaultRegId); }
            var sb = document.getElementById('rehab_status_banner');
            sb.style.display = 'none';
            sb.textContent = '';
            var editTh = document.getElementById('rehab_show_thumb_fields');
            if (editTh) { editTh.style.display = 'none'; }
        };
        rehabFpSyncDebugTemplateFields();
    })();
    </script>
    <?php
}

function rehab_fingerprint_enrollment_block()
{
    rehab_fingerprint_print_digitalpersona_scripts();
    ?>
    <div class="col-md-12" style="margin-top: 12px;">
        <fieldset class="border p-2">
            <legend class="w-auto" style="font-size: 14px;"><strong>Fingerprints (optional)</strong></legend>
            <p class="small text-muted mb-2">
                Optional — leave blank to save the token without fingerprints. Use <strong>Capture with U.are.U reader</strong> for each thumb, or paste a template.
                Optional HTTP bridge: define <code>REHAB_FP_LOCAL_CAPTURE_URL</code> in <code>company_info.php</code>.
            </p>
            <div class="row">
                <div class="col-md-6">
                    <label>Left thumb template (base64 / ISO)</label>
                    <textarea name="fp_thumb_left" id="fp_thumb_left" class="form-control" rows="2" placeholder="Paste or capture from reader (optional)"></textarea>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="rehabFpTryCapture('left')">Capture with U.are.U reader (left)</button>
                </div>
                <div class="col-md-6">
                    <label>Right thumb template (base64 / ISO)</label>
                    <textarea name="fp_thumb_right" id="fp_thumb_right" class="form-control" rows="2" placeholder="Paste or capture from reader (optional)"></textarea>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="rehabFpTryCapture('right')">Capture with U.are.U reader (right)</button>
                </div>
            </div>
        </fieldset>
    </div>
    <?php
    rehab_fingerprint_print_capture_try_script();
}

/**
 * Fieldset only (wrapped by patient_registeration.php in #rehab_fp_enrollment_wrap). No duplicate script tags.
 */
function rehab_fingerprint_new_patient_enrollment_only()
{
    ?>
        <fieldset class="border p-2">
            <legend class="w-auto" style="font-size: 14px;"><strong>New patient — optional thumb correction</strong></legend>
            <p class="small text-muted mb-2">
                Normally both thumbs are filled automatically after “first thumb + other thumb” identification. Open this section only to paste or re-capture.
            </p>
            <div class="row">
                <div class="col-md-6">
                    <label>Left thumb (first identification scan)</label>
                    <textarea name="fp_thumb_left" id="fp_thumb_left" class="form-control" rows="2" placeholder="Auto-filled from first scan"></textarea>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="rehabFpTryCapture('left')">Capture with U.are.U reader (left)</button>
                </div>
                <div class="col-md-6">
                    <label>Right thumb (second identification scan)</label>
                    <textarea name="fp_thumb_right" id="fp_thumb_right" class="form-control" rows="2" placeholder="Auto-filled from second scan"></textarea>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="rehabFpTryCapture('right')">Capture with U.are.U reader (right)</button>
                </div>
            </div>
        </fieldset>
    <?php
}

function rehab_fingerprint_verify_block()
{
    rehab_fingerprint_print_digitalpersona_scripts();
    ?>
    <div class="col-md-12" style="margin-top: 10px;">
        <fieldset class="border p-2">
            <legend class="w-auto" style="font-size: 14px;"><strong>Thumb verification (optional)</strong></legend>
            <p class="small text-muted mb-2">Patient has fingerprints on file. Scan one thumb to verify, or leave blank and save the token.</p>
            <label>Thumb template for verification</label>
            <textarea name="fp_thumb_verify" id="fp_thumb_verify" class="form-control" rows="2" placeholder="Paste or capture from reader (optional)"></textarea>
            <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="rehabFpTryCaptureVerify()">Capture with U.are.U reader</button>
        </fieldset>
    </div>
    <script>
    function rehabFpTryCaptureVerify() {
        var base = <?php echo json_encode(defined('REHAB_FP_LOCAL_CAPTURE_URL') && REHAB_FP_LOCAL_CAPTURE_URL ? REHAB_FP_LOCAL_CAPTURE_URL : ''); ?>;
        if (base) {
            var url = base + (base.indexOf('?') >= 0 ? '&' : '?') + 'finger=any';
            fetch(url).then(function (r) { return r.text(); }).then(function (t) {
                var el = document.getElementById('fp_thumb_verify');
                if (el) { el.value = t.trim(); }
            }).catch(function () {
                alert('Could not reach local capture service. Paste the template manually.');
            });
            return;
        }
        if (typeof rehabDpCaptureToField === 'function') {
            rehabDpCaptureToField('fp_thumb_verify').catch(function () {});
            return;
        }
        alert('DigitalPersona browser SDK did not load. Check internet (unpkg.com), refresh the page, install the Lite Client, or paste the template manually.');
    }
    </script>
    <?php
}
