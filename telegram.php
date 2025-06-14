<?php
// Load .env values
$env = parse_ini_file(__DIR__ . '/.env');
$bot_token = $env['BOT_TOKEN'];

// Function to send Telegram message
function sendTelegramMessage($message, $chat_id) {
    global $bot_token;
    file_get_contents("https://api.telegram.org/bot$bot_token/sendMessage?" . http_build_query([
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ]));
}

// BIN lookup function
function bin_lookup($bin) {
    $json = file_get_contents("https://lookup.binlist.net/$bin");
    return json_decode($json, true);
}

// Handle Telegram update
$content = file_get_contents("php://input");
$update = json_decode($content, true);
$message = $update['message'] ?? null;

if (!$message) exit;

$text = trim($message['text']);
$chat_id = $message['chat']['id'];

if (strpos($text, "/setproxy ") === 0) {
    $proxy = trim(str_replace("/setproxy", "", $text));
    $cfg = json_decode(file_get_contents("config.json"), true);
    $cfg['proxy'] = $proxy;
    file_put_contents("config.json", json_encode($cfg, JSON_PRETTY_PRINT));
    sendTelegramMessage("✅ Proxy updated to:\n`$proxy`", $chat_id);
}

elseif ($text === "/getproxy") {
    $cfg = json_decode(file_get_contents("config.json"), true);
    $proxy = $cfg['proxy'] ?? 'None set';
    sendTelegramMessage("🔎 Current proxy:\n`$proxy`", $chat_id);
}

elseif ($text === "/clearproxy") {
    $cfg = json_decode(file_get_contents("config.json"), true);
    unset($cfg['proxy']);
    file_put_contents("config.json", json_encode($cfg, JSON_PRETTY_PRINT));
    sendTelegramMessage("🧹 Proxy cleared.", $chat_id);
}

elseif (strpos($text, "/checkbin ") === 0) {
    $bin = trim(str_replace("/checkbin", "", $text));
    if (!preg_match('/^\d{6}$/', $bin)) {
        sendTelegramMessage("❌ Invalid BIN. Must be 6 digits.", $chat_id);
    } else {
        $binData = bin_lookup($bin);
        $bank = $binData['bank']['name'] ?? 'Unknown';
        $country = $binData['country']['name'] ?? 'Unknown';
        $type = $binData['type'] ?? 'Unknown';
        $level = $binData['brand'] ?? 'Unknown';
        sendTelegramMessage("🏦 BIN Info for `$bin`:\nBank: $bank\nCountry: $country\nType: $type\nLevel: $level", $chat_id);
    }
}

elseif (strpos($text, "/checkcard ") === 0) {
    $card = trim(str_replace("/checkcard", "", $text));
    if (!preg_match('/^\d+\|\d{2}\|\d{2,4}\|\d+$/', $card)) {
        sendTelegramMessage("❌ Invalid format. Use: `xxxx|mm|yy|cvv`", $chat_id);
    } else {
        $cfg = json_decode(file_get_contents("config.json"), true);
        $endpoint = $cfg['checker_endpoint'] ?? null;
        if (!$endpoint) {
            sendTelegramMessage("⚠️ Checker endpoint not configured.", $chat_id);
            exit;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => ['card' => $card]
        ]);
        $res = curl_exec($ch);
        curl_close($ch);
        sendTelegramMessage("💳 Check Result:\n`$card`\nStatus: *$res*", $chat_id);
    }
}

elseif ($text === "/start") {
    sendTelegramMessage("🤖 CC Checker Bot ready!\nCommands:\n/setproxy IP:PORT\n/getproxy\n/clearproxy\n/checkbin BIN\n/checkcard xxxx|mm|yy|cvv", $chat_id);
}
?>
