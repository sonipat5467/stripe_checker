<?php
require 'vendor/autoload.php'; // Make sure this exists
\Stripe\Stripe::setApiKey('sk_test_XXXXXXXXXXXXXXXXXXXX');

try {
    $balance = \Stripe\Balance::retrieve();
    echo "✅ Stripe key is valid. Available: " . $balance->available[0]->amount;
} catch (Exception $e) {
    echo "❌ Invalid Stripe Key: " . $e->getMessage();
}
