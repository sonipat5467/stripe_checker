<?php
require 'config.php';

$config = require 'config.php';
$mysqli = new mysqli(
    $config['db']['host'],
    $config['db']['user'],
    $config['db']['pass'],
    $config['db']['name']
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cards = [];

    // If a file is uploaded
    if (!empty($_FILES['card_file']['tmp_name'])) {
        $file = fopen($_FILES['card_file']['tmp_name'], 'r');
        while (($line = fgets($file)) !== false) {
            $line = trim($line);
            if (preg_match('/^\d{15,16}/', $line)) {
                $cards[] = $line;
            }
        }
        fclose($file);
    }

    // If textarea input provided
    if (!empty($_POST['card_text'])) {
        $lines = explode("\n", $_POST['card_text']);
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^\d{15,16}/', $line)) {
                $cards[] = $line;
            }
        }
    }

    $inserted = 0;
    foreach ($cards as $card) {
        $stmt = $mysqli->prepare("INSERT INTO card_queue (card, status) VALUES (?, 'pending')");
        $stmt->bind_param("s", $card);
        $stmt->execute();
        $inserted++;
    }

    echo "<p><strong>✅ Uploaded $inserted card(s) successfully.</strong></p>";
}
?>

<h2>📤 Upload Cards to Checker Queue</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Upload CSV/Text File:</label><br>
    <input type="file" name="card_file"><br><br>

    <label>Or Paste Cards (one per line):</label><br>
    <textarea name="card_text" rows="10" cols="50" placeholder="1234123412341234|12|24|123"></textarea><br><br>

    <button type="submit">🚀 Submit to Queue</button>
</form>
