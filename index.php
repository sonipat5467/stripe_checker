// index.php - placeholder content for demonstration.
<?php
require 'config.php';

$config = require 'config.php';
$mysqli = new mysqli(
    $config['db']['host'],
    $config['db']['user'],
    $config['db']['pass'],
    $config['db']['name']
);

// Fetch logs
$result = $mysqli->query("SELECT * FROM logs ORDER BY id DESC LIMIT 100");

// Stats
$total = $mysqli->query("SELECT COUNT(*) as total FROM logs")->fetch_assoc()['total'];
$approved = $mysqli->query("SELECT COUNT(*) as approved FROM logs WHERE status = 'APPROVED'")->fetch_assoc()['approved'];
$declined = $mysqli->query("SELECT COUNT(*) as declined FROM logs WHERE status = 'DECLINED'")->fetch_assoc()['declined'];
?>
<!DOCTYPE html>
<html>
<head>
  <title>CC Checker Panel</title>
  <style>
    body { font-family: Arial; background: #111; color: #eee; margin: 40px; }
    h1 { color: #0f0; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { padding: 8px 12px; border: 1px solid #444; }
    th { background: #222; }
    .APPROVED { color: lime; }
    .DECLINED { color: red; }
    .stats { margin-bottom: 20px; }
    .stat-box { display: inline-block; margin-right: 30px; font-size: 18px; }
  </style>
</head>
<body>
  <h1>🛡️ CC Checker Dashboard</h1>

  <div class="stats">
    <div class="stat-box">📦 Total: <strong><?= $total ?></strong></div>
    <div class="stat-box" style="color:lime">✅ Approved: <strong><?= $approved ?></strong></div>
    <div class="stat-box" style="color:red">❌ Declined: <strong><?= $declined ?></strong></div>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Card</th>
        <th>Status</th>
        <th>Bank</th>
        <th>Country</th>
        <th>IP</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['card'] ?></td>
        <td class="<?= $row['status'] ?>"><?= $row['status'] ?></td>
        <td><?= $row['bank'] ?></td>
        <td><?= $row['country'] ?></td>
        <td><?= $row['ip'] ?></td>
        <td><?= $row['created_at'] ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
// index.php - placeholder content for demonstration.
