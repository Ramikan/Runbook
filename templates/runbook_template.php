<!DOCTYPE html><html><head><meta charset="utf-8">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="css/styles.css" rel="stylesheet">
</head><body class="container py-3">
<h2>Runbook: <?= htmlspecialchars($apt->name) ?></h2>
<p><?= nl2br(htmlspecialchars($apt->description)) ?></p>

<ul class="list-group">
<?php foreach ($techniques as $tech): ?>
  <?php if (!$tacticsFilter || array_intersect($tacticsFilter, $tech->killChainPhases)): ?>
  <li class="list-group-item">
    <h5><?= htmlspecialchars($tech->id . ' – ' . $tech->name) ?></h5>
    <p><?= htmlspecialchars($tech->description) ?></p>
    <p><strong>Phases:</strong>
      <?php foreach ($tech->killChainPhases as $phase): ?>
      <span class="badge bg-secondary tactic-<?= strtolower($phase) ?>"><?= htmlspecialchars($phase) ?></span>
      <?php endforeach; ?>
    </p>
  </li>
  <?php endif; ?>
<?php endforeach; ?>
</ul>
</body></html>
