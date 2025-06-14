// log.php - placeholder content for demonstration.
<?php
require 'config.php';

$config = require 'config.php';
$mysqli = new mysqli(
    $config['db']['host'],
    $config['db']['user'],
    $config['db']['pass'],
    $config['db']['name']
);

// Fetch latest 100 logs
$result = $mysqli->query("SELECT * FROM logs ORDER BY id DESC LIMIT 100");

echo "<h2>Last 100 Logs</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>ID</th><th>Card</th><th>Status</th><th>Bank</th><th>Country</th><th>IP</th><th>Time</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['card']}</td>";
    echo "<td>{$row['status']}</td>";
    echo "<td>{$row['bank']}</td>";
    echo "<td>{$row['country']}</td>";
    echo "<td>{$row['ip']}</td>";
    echo "<td>{$row['created_at']}</td>";
    echo "</tr>";
}

echo "</table>";
?>
// log.php - placeholder content for demonstration.
