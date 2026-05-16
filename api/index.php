<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// API Expiry
$expiryDate = strtotime('2027-04-06');
$currentDate = time();

if ($currentDate > $expiryDate) {
    echo json_encode(["success" => false, "message" => "API Expired! Contact @AbdulDevStoreBot"]);
    exit;
}

$remainingDays = floor(($expiryDate - $currentDate) / 86400);

define('XINFO_API_URL', 'https://users-xinfo-admin-six.vercel.app/api');
define('XINFO_API_KEY', 'qwertyuioplk847isuhnsiandj');

$term = $_GET['term'] ?? $_GET['mobile'] ?? null;

if (!$term) {
    echo json_encode([
        "success" => false,
        "message" => "Provide ?mobile= or ?term=",
        "credit" => "@botadminshere",
        "channel" => "https://t.me/Toxicadminn"
    ]);
    exit;
}

$number = preg_replace('/[^0-9]/', '', $term);

$url = XINFO_API_URL . "?key=" . XINFO_API_KEY . "&type=mobile&term=" . $number;

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 60,                    // increased
    CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; NumberSageAPI/3.1)'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo json_encode([
        "success" => false,
        "message" => "Backend API Down (502 Bad Gateway)",
        "http_code" => $httpCode,
        "curl_error" => $curlError,
        "note" => "Backend temporarily down. Try again in few minutes or contact owner.",
        "credit" => "@botadminshere",
        "channel" => "https://t.me/Toxicadminn",
        "days_remaining" => $remainingDays
    ]);
    exit;
}

$data = json_decode($response, true);

$output = [
    "success" => true,
    "credit" => "@botadminshere",
    "channel" => "https://t.me/Toxicadminn",
    "api" => "number-sage-eta.vercel.app",
    "source" => "pawan",
    "days_remaining" => $remainingDays,
    "result" => $data['data'] ?? $data ?? []
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
