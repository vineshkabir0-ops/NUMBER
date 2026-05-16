<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// API Expiry
$expiryDate = strtotime('2027-04-06');
$currentDate = time();

if ($currentDate > $expiryDate) {
    echo json_encode([
        "success" => false, 
        "message" => "API Expired! Contact @botadminshere for renewal"
    ]);
    exit;
}

$remainingDays = floor(($expiryDate - $currentDate) / 86400);

// ==================== API CONFIG ====================
$apiBaseUrl = "https://users-xinfo-admin-six.vercel.app/api";
$apiKey = "qwertyuioplk847isuhnsiandj";
// ===================================================

$term = $_GET['term'] ?? $_GET['mobile'] ?? $_GET['number'] ?? null;

if (!$term) {
    echo json_encode([
        "success" => false,
        "message" => "Provide ?mobile=6203522947",
        "credit" => "@botadminshere",
        "channel" => "https://t.me/Toxicadminn",
        "days_remaining" => $remainingDays
    ]);
    exit;
}

$number = preg_replace('/[^0-9]/', '', $term);

// Clean URL
$apiUrl = $apiBaseUrl . "?" . http_build_query([
    "key"  => $apiKey,
    "type" => "mobile",
    "term" => $number
]);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 90,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; SupportToxicAPI/4.2)'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo json_encode([
        "success" => false,
        "message" => "Backend Down (502 Bad Gateway)",
        "note" => "XINFO server down hai. @botadminshere ko bol do fix kare.",
        "http_code" => $httpCode,
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
    "api" => "support-toxicadminn.vercel.app",
    "source" => "pawan",
    "days_remaining" => $remainingDays,
    "result" => $data['data'] ?? $data ?? []
];

echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
