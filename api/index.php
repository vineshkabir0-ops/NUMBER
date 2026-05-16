<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// API expiry
$expiryDate = strtotime('2027-04-06');
$currentDate = time();

if ($currentDate > $expiryDate) {
    echo json_encode([
        "success" => false,
        "message" => "API Expired! Contact admin for renewal",
        "credit" => "@botadminshere",
        "channel" => "https://t.me/Toxicadminn"
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

$remainingDays = floor(($expiryDate - $currentDate) / 86400);

// New API
define('MOBILE_API_URL', 'https://users-xinfo-admin-six.vercel.app/api');

// Put your API key here
$apiKey = "YOUR_API_KEY_HERE";

// Get mobile number
$term = $_GET['term'] ?? null;

if (!$term) {
    echo json_encode([
        "success" => false,
        "message" => "Please provide mobile number using ?term=NUMBER",
        "example" => "?term=9876543210",
        "credit" => "@botadminshere",
        "channel" => "https://t.me/Toxicadminn",
        "api_valid_until" => "April 6, 2027",
        "days_remaining" => $remainingDays
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Sanitize number
$term = preg_replace('/[^0-9]/', '', $term);

if (strlen($term) < 10) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid mobile number",
        "credit" => "@botadminshere",
        "channel" => "https://t.me/Toxicadminn",
        "api_valid_until" => "April 6, 2027",
        "days_remaining" => $remainingDays
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Build API URL
$apiUrl = MOBILE_API_URL . "?" . http_build_query([
    "key" => $apiKey,
    "type" => "mobile",
    "term" => $term
]);

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_USERAGENT => 'Mozilla/5.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to fetch data",
        "error" => $error ?: "HTTP Code: " . $httpCode,
        "credit" => "@botadminshere",
        "channel" => "https://t.me/Toxicadminn",
        "api_valid_until" => "April 6, 2027",
        "days_remaining" => $remainingDays
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

$data = json_decode($response, true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid API response",
        "raw_response" => $response,
        "credit" => "@botadminshere",
        "channel" => "https://t.me/Toxicadminn",
        "api_valid_until" => "April 6, 2027",
        "days_remaining" => $remainingDays
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Final output
$output = [
    "success" => true,
    "credit" => "@botadminshere",
    "channel" => "https://t.me/Toxicadminn",
    "api_valid_until" => "April 6, 2027",
    "days_remaining" => $remainingDays,
    "query" => [
        "type" => "mobile",
        "term" => $term
    ],
    "result" => $data
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
