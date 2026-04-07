<?php
$company_name = 'Youth Community Development Organization';
$company_trademark = 'YCDO';
$company_ambition = 'SERVE HUMANITY';
$company_phone = '0304-1110222';

// Rehabilitation fingerprint matchers. 1:N identify must be configured for returning-patient detection.
define('REHAB_FP_IDENTIFY_URL', 'http://127.0.0.1:9100/identify');
define('REHAB_FP_VERIFY_URL', 'http://127.0.0.1:9100/verify');
define('REHAB_FP_IDENTIFY_TIMEOUT_SEC', 30);
define('REHAB_FP_MATCH_THRESHOLD', 50);
?>