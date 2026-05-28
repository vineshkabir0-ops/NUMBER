<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ======================
// API SETTINGS
// ======================

define('API_KEY', 'toxicadminn');
define('API_URL', 'https://numosintapi.vercel.app/api/number-info');

// ======================
// API EXPIRY
// ======================

$expiryDate = strtotime('2026-12-31');
$currentDate = time();

if ($currentDate > $expiryDate) {
    echo json_encode([
        "success" => false,
        "message" => "API Expired! Contact Developer",
        "developer" => "https://t.me/botadminshere",
        "credit" => "https://t.me/Toxicadminn",
        "private" => "https://t.me/+14rDlunTEzwwZGY1"
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
        "message" => "Invalid API Key",
        "developer" => "https://t.me/botadminshere",
        "credit" => "https://t.me/Toxicadminn",
        "private" => "https://t.me/+14rDlunTEzwwZGY1"
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
        "message" => "Please provide a number",
        "developer" => "https://t.me/botadminshere",
        "credit" => "https://t.me/Toxicadminn",
        "private" => "https://t.me/+14rDlunTEzwwZGY1"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// sanitize number
$number = preg_replace('/[^0-9]/', '', $number);

// ======================
// FETCH API
// ======================

$url = API_URL . "?number=" . urlencode($number);

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 30,
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
        "message" => "Failed to fetch data",
        "developer" => "https://t.me/botadminshere",
        "credit" => "https://t.me/Toxicadminn",
        "private" => "https://t.me/+14rDlunTEzwwZGY1"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

$data = json_decode($response, true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "No data found",
        "developer" => "https://t.me/botadminshere",
        "credit" => "https://t.me/Toxicadminn",
        "private" => "https://t.me/+14rDlunTEzwwZGY1"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// ======================
// HIDE CHANNEL
// ======================

unset($data['channel']);

// ======================
// FINAL OUTPUT
// ======================

$output = [
    "success" => true,
    "developer" => "https://t.me/botadminshere",
    "credit" => "https://t.me/Toxicadminn",
    "private" => "https://t.me/+14rDlunTEzwwZGY1",
    "result" => $data
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

?>
