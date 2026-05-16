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
        "message" => "API Expired! Contact @AbdulDevStoreBot for renewal",
        "credit" => "@botadminshere",
        "channel" => "https://t.me/Toxicadminn"
    ]);
    exit;
}

$remainingDays = floor(($expiryDate - $currentDate) / 86400);

define('OWNER_API_URL', 'https://tg2num-owner-api.vercel.app');
define('XINFO_API_URL', 'https://users-xinfo-admin-six.vercel.app/api');
define('XINFO_API_KEY', 'Toxicadminn');   // ← Your requested key

$userid = $_GET['userid'] ?? null;
$mobile = $_GET['mobile'] ?? null;   // New parameter for mobile lookup

// If mobile number is provided, use XINFO API
if ($mobile) {
    $mobile = preg_replace('/[^0-9]/', '', $mobile);
    
    $xinfoUrl = XINFO_API_URL . "?key=" . XINFO_API_KEY . "&type=mobile&term=" . $mobile;
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $xinfoUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_USERAGENT => 'Mozilla/5.0'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        echo json_encode([
            "success" => false,
            "message" => "XINFO API request failed",
            "credit" => "@botadminshere",
            "channel" => "https://t.me/Toxicadminn",
            "api_valid_until" => "April 6, 2027",
            "days_remaining" => $remainingDays
        ]);
        exit;
    }

    $data = json_decode($response, true);

    $output = [
        "success" => true,
        "credit" => "@botadminshere",
        "channel" => "https://t.me/Toxicadminn",
        "api_valid_until" => "April 6, 2027",
        "days_remaining" => $remainingDays,
        "source" => "xinfo_api",
        "result" => $data['data'] ?? $data ?? []
    ];

    echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

// Original logic for Telegram User ID (if no mobile provided)
if (!$userid) {
    echo json_encode([
        "success" => false,
        "message" => "Please provide userid or mobile parameter",
        "credit" => "@botadminshere",
        "channel" => "https://t.me/Toxicadminn",
        "api_valid_until" => "April 6, 2027",
        "days_remaining" => $remainingDays
    ]);
    exit;
}

$userid = preg_replace('/[^0-9]/', '', $userid);

$ownerApi = OWNER_API_URL . "?userid=" . $userid;

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $ownerApi,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_USERAGENT => 'Mozilla/5.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to fetch data from owner API",
        "credit" => "@botadminshere",
        "channel" => "https://t.me/Toxicadminn",
        "api_valid_until" => "April 6, 2027",
        "days_remaining" => $remainingDays
    ]);
    exit;
}

$data = json_decode($response, true);

if (!$data || !isset($data['data'])) {
    echo json_encode([
        "success" => false,
        "message" => "No data found",
        "credit" => "@botadminshere",
        "channel" => "https://t.me/Toxicadminn",
        "api_valid_until" => "April 6, 2027",
        "days_remaining" => $remainingDays
    ]);
    exit;
}

$output = [
    "success" => true,
    "credit" => "@botadminshere",
    "channel" => "https://t.me/Toxicadminn",
    "api_valid_until" => "April 6, 2027",
    "days_remaining" => $remainingDays,
    "source" => "owner_api",
    "result" => [
        "country" => $data['data']['country'] ?? null,
        "country_code" => $data['data']['country_code'] ?? null,
        "number" => $data['data']['number'] ?? null
    ]
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>