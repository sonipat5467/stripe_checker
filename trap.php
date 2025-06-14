// trap.php - placeholder content for demonstration.
// trap.php - placeholder content for demonstration.
<?php
require 'config.php';

$config = require 'config.php';

// Get visitor details
$ip = $_SERVER['REMOTE_ADDR'];
$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
$time = date("Y-m-d H:i:s");

// Optional: Log to database
$mysqli = new mysqli(
    $config['db']['host'],
    $config['db']['user'],
    $config['db']['pass'],
    $config['db']['name']
);

$stmt = $mysqli->prepare("INSERT INTO traps (ip, user_agent, created_at) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $ip, $ua, $time);
$stmt->execute();

// Optional: Send Telegram alert
$token = $config['telegram']['bot_token'];
$chat_id = $config['telegram']['chat_id'];
$message = "🚨 Honeypot triggered!
IP: $ip
Agent: $ua
Time: $time";

file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=" . urlencode($message));

// Fake 404 response
http_response_code(404);
echo "<h1>404 Not Found</h1>";
?>
