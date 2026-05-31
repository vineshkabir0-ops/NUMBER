<?php
error_reporting(0);
header('Content-Type: application/json');

define('API_KEY', 'toxicadminn');

$apikey = $_GET['apikey'] ?? '';

if ($apikey !== API_KEY) {
    die(json_encode([
        "success" => false,
        "message" => "Invalid API Key"
    ]));
}

$number = $_GET['number'] ?? '';

if (empty($number)) {
    die(json_encode([
        "success" => false,
        "message" => "Please provide number",
        "example" => "?apikey=toxicadminn&number=8651369226"
    ]));
}

$number = preg_replace('/\D/', '', $number);

$url = "https://number-to-api-team-only.vercel.app/api/index.js?api_key=team6months&number=" . urlencode($number);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT => 'Mozilla/5.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if (!$response || $httpCode != 200) {
    die(json_encode([
        "success" => false,
        "message" => "Failed to fetch data"
    ]));
}

$data = json_decode($response, true);

echo json_encode([
    "success" => true,
    "result" => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
