<?php
require 'config.php';
require 'vendor/autoload.php'; // If using Composer for Stripe

use Stripe\Stripe;
use Stripe\Token;
use Stripe\Charge;

// Load Stripe secret key
$config = require 'config.php';
Stripe::setApiKey($config['stripe']['secret_key']);

// 👉 Load proxy from config.json
$configFile = json_decode(file_get_contents('config.json'), true);
$proxy = $configFile['proxy'] ?? '';

// ✅ If proxy is set, apply it to Stripe's HTTP client
if (!empty($proxy)) {
    \Stripe\Stripe::setHttpClient(
        new \Stripe\HttpClient\CurlClient([
            CURLOPT_PROXY => $proxy
        ])
    );
}

// 📦 Log checker result into DB
function log_to_db($data) {
    global $config;
    $mysqli = new mysqli(
        $config['db']['host'],
        $config['db']['user'],
        $config['db']['pass'],
        $config['db']['name']
    );
    $stmt = $mysqli->prepare("INSERT INTO logs (card, status, bank, country, ip) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $data['card'], $data['status'], $data['bank'], $data['country'], $data['ip']);
    $stmt->execute();
}

// 🔍 BIN Lookup
function bin_lookup($bin) {
    $json = file_get_contents("https://lookup.binlist.net/$bin");
    return json_decode($json, true);
}

// Get card from POST
$card = $_POST['card'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'];

if (!$card || strlen($card) < 12) {
    exit("Invalid card.");
}

list($cc, $mm, $yy, $cvv) = explode('|', $card);

// Step 1: Tokenize
try {
    $token = Token::create([
        'card' => [
            'number' => $cc,
            'exp_month' => $mm,
            'exp_year' => $yy,
            'cvc' => $cvv
        ]
    ]);
} catch (Exception $e) {
    log_to_db([
        'card' => $card,
        'status' => 'DECLINED',
        'bank' => 'Invalid',
        'country' => 'Unknown',
        'ip' => $ip
    ]);
    exit("Declined: Token error");
}

// Step 2: Charge
try {
    $charge = Charge::create([
        'amount' => 100, // $1.00
        'currency' => 'usd',
        'source' => $token->id,
        'description' => 'Checker Test'
    ]);
    $status = $charge->status === 'succeeded' ? 'APPROVED' : 'DECLINED';
} catch (Exception $e) {
    $status = 'DECLINED';
}

// Step 3: BIN Lookup
$binData = bin_lookup(substr($cc, 0, 6));
$bank = $binData['bank']['name'] ?? 'N/A';
$country = $binData['country']['name'] ?? 'Unknown';

// Log final result
log_to_db([
    'card' => $card,
    'status' => $status,
    'bank' => $bank,
    'country' => $country,
    'ip' => $ip
]);

echo $status;
?>
