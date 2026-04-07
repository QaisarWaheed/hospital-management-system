<?php
include 'includes/connect_ajax.php';
include_once 'includes/rehab_fingerprint.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_rehabilitation_branch($branch_id)) {
    echo json_encode(array('ok' => false, 'error' => 'Not a rehabilitation branch'));
    exit;
}

if (!defined('REHAB_FP_IDENTIFY_URL') || !REHAB_FP_IDENTIFY_URL || !is_string(REHAB_FP_IDENTIFY_URL)) {
    echo json_encode(array(
        'ok' => false,
        'error' => 'Fingerprint identify service is not configured. Set REHAB_FP_IDENTIFY_URL in includes/company_info.php.',
    ));
    exit;
}

$probe = isset($_POST['fp_probe']) ? $_POST['fp_probe'] : '';
$patient_id = rehab_find_patient_by_fingerprint($con, $probe);

if (!$patient_id) {
    echo json_encode(array('ok' => true, 'match' => false));
    exit;
}

$pq = mysqli_query($con, "SELECT id, name, age, phone, gender FROM patients WHERE id = '" . (int) $patient_id . "' LIMIT 1");
if (!$pq || mysqli_num_rows($pq) !== 1) {
    echo json_encode(array('ok' => true, 'match' => false));
    exit;
}
$pat = mysqli_fetch_assoc($pq);
$snap = rehab_get_last_visit_snapshot($con, $patient_id, $branch_id);

echo json_encode(array(
    'ok' => true,
    'match' => true,
    'patient' => array(
        'id' => (int) $pat['id'],
        'name' => $pat['name'],
        'age' => $pat['age'],
        'phone' => $pat['phone'],
        'gender' => $pat['gender'],
    ),
    'last_visit' => $snap ? array(
        'doctor_id' => $snap['doctor_id'],
        'tokan_type_id' => $snap['tokan_type_id'],
    ) : null,
));
