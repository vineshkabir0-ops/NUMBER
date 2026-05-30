<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ======================
// API SETTINGS
// ======================

define('API_KEY', 'toxicadminn'); // Your API key
define('API_URL', 'https://paidxapi-number-info.devilxapis.workers.dev/');
define('REMOTE_API_KEY', 'dependonrequest');

// ======================
// API EXPIRY
// ======================

$expiryDate = strtotime('2026-12-31');

if (time() > $expiryDate) {
    echo json_encode([
        "success" => false,
        "message" => "API Expired! Contact Developer"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// ======================
// API KEY CHECK
// ======================

$apikey = $_GET['apikey'] ?? '';

if ($apikey !== API_KEY) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid API Key"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// ======================
// NUMBER CHECK
// ======================

$number = $_GET['number'] ?? '';

if (empty($number)) {
    echo json_encode([
        "success" => false,
        "message" => "Please provide a number"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Sanitize Number
$number = preg_replace('/\D/', '', $number);

// ======================
// FETCH API
// ======================

$url = API_URL .
       '?key=' . urlencode(REMOTE_API_KEY) .
       '&phone=' . urlencode($number);

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL            => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_USERAGENT      => 'Mozilla/5.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

// ======================
// ERROR CHECK
// ======================

if ($response === false || $httpCode !== 200) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to fetch data"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

$data = json_decode($response, true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid response from upstream API"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Hide channel field if present
unset($data['channel']);

// ======================
// FINAL OUTPUT
// ======================

echo json_encode([
    "success" => true,
    "result"  => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
