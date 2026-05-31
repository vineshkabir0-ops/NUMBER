<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

define('API_KEY', 'toxicadminn');

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

$number = $_GET['number'] ?? '';

if (empty($number)) {
    echo json_encode([
        "success" => false,
        "message" => "Please provide number",
        "example" => "?apikey=toxicadminn&number=8651369226",
        "developer" => "https://t.me/botadminshere",
        "credit" => "https://t.me/Toxicadminn",
        "private" => "https://t.me/+14rDlunTEzwwZGY1"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

$number = preg_replace('/\D/', '', $number);

$url = "https://number-to-api-team-only.vercel.app/api/index.js?api_key=team6months&number=" . urlencode($number);

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
        "message" => "Invalid response from server",
        "developer" => "https://t.me/botadminshere",
        "credit" => "https://t.me/Toxicadminn",
        "private" => "https://t.me/+14rDlunTEzwwZGY1"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

echo json_encode([
    "success" => true,
    "developer" => "https://t.me/botadminshere",
    "credit" => "https://t.me/Toxicadminn",
    "private" => "https://t.me/+14rDlunTEzwwZGY1",
    "result" => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
