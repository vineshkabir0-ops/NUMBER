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
    echo json_encode([
        "success" => false,
        "message" => "Invalid API Key"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// ======================
// QUERY CHECK
// ======================

$query = $_GET['query'] ?? '';

if (empty($query)) {
    echo json_encode([
        "success" => false,
        "message" => "Please provide query",
        "example" => "?apikey=toxicadminn&query=9876543210"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// sanitize query
$query = preg_replace('/[^0-9]/', '', $query);

// ======================
// TARGET API URL
// ======================

$url = API_URL . urlencode($query);

// ======================
// CURL REQUEST
// ======================

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT => 'Mozilla/5.0'
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

// ======================
// DECODE RESPONSE
// ======================

$data = json_decode($response, true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid response from server"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// ======================
// HIDE UNWANTED FIELDS
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
    "result" => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
