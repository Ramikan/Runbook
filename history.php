<?php
require 'vendor/autoload.php'; require 'classes/Auth.php';
$pdo = new PDO(...); $auth = new Auth($pdo); $auth->requireLogin();
$rows = $pdo->prepare("SELECT * FROM saved_runbooks WHERE user_id=? ORDER BY created_at DESC");
$rows->execute([$auth->userId()]);
?>
<!DOCTYPE html><html><head><title>History</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body class="container py-4">
  <h1>Your Saved Runbooks</h1>
  <a href="index.php" class="btn btn-secondary mb-3">New Runbook</a>
  <ul class="list-group">
  <?php while ($r = $rows->fetch()): ?>
    <li class="list-group-item">
      <?= htmlspecialchars($r['apt']) ?> —
      <?= date('Y‑m‑d H:i', strtotime($r['created_at'])) ?>
      <a class="btn btn-sm btn-outline-primary float-end" href="#" onclick="showHtml(`<?= addslashes($r['runbook_html']) ?>`)">View</a>
    </li>
  <?php endwhile; ?>
  </ul>
  <div id="viewer" class="mt-4"></div>
  <script>
    function showHtml(h){document.getElementById('viewer').innerHTML = h;}
  </script>
</body></html>
