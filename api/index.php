<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ======================
// SETTINGS
// ======================

define('API_KEY', 'toxicadminn');
define('API_URL', 'https://number-to-api-team-only.vercel.app/api/index.js?api_key=free6m&number=');

// ======================
// API KEY CHECK
// ======================

$apikey = $_GET['apikey'] ?? '';

if ($apikey !== API_KEY) {
    die(json_encode([
        "success" => false,
        "message" => "Invalid API Key"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

// ======================
// QUERY CHECK
// ======================

$query = $_GET['query'] ?? '';

if (empty($query)) {
    die(json_encode([
        "success" => false,
        "message" => "Please provide query",
        "example" => "?apikey=toxicadminn&query=9876543210"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

// Only numbers
$query = preg_replace('/\D/', '', $query);

// ======================
// TARGET API URL
// ======================

$url = API_URL . urlencode($query);

// ======================
// CURL REQUEST
// ======================

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL            => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_USERAGENT      => 'Mozilla/5.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);

// ======================
// ERROR CHECK
// ======================

if ($response === false || !empty($curlError)) {
    die(json_encode([
        "success" => false,
        "message" => "cURL Error",
        "error"   => $curlError
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

if ($httpCode != 200) {
    die(json_encode([
        "success" => false,
        "message" => "HTTP Error",
        "status"  => $httpCode
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

// ======================
// DECODE RESPONSE
// ======================

$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die(json_encode([
        "success" => false,
        "message" => "Invalid JSON Response",
        "raw"     => $response
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

// ======================
// HIDE FIELDS
// ======================

unset($data['channel']);
unset($data['key']);
unset($data['api_key']);
unset($data['owner']);
unset($data['telegram']);
unset($data['API_Developer']);

// ======================
// FINAL RESPONSE
// ======================

echo json_encode([
    "success" => true,
    "result"  => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
