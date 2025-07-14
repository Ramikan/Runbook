<!DOCTYPE html><html><head><meta charset="utf-8"><title>Runbook Generator</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="css/styles.css" rel="stylesheet"></head><body class="container py-3">
<h1>MITRE Runbook Generator</h1>
<a href="logout.php" class="btn btn-link float-end">Logout</a>

<form method="get" class="row gy-2 gx-3 align-items-end">
  <div class="col-auto">
    <select name="apt" class="form-select">
      <option value="">Select APT…</option>
      <?php foreach ($aptsData as $name => $data): ?>
      <option value="<?= htmlspecialchars($name) ?>" <?= $selectedName === $name ? 'selected' : '' ?>>
        <?= htmlspecialchars($name) ?>
      </option>
      <?php endforeach; ?>
    </select>
  </div>

  <?php if ($techniques): ?>
  <div class="col-auto">
    <?php foreach (array_unique(array_merge(...array_map(fn($tech)=> $tech->killChainPhases, $techniques))) as $tac): ?>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="tactics[]" value="<?=htmlspecialchars($tac)?>"
        <?= in_array($tac, $tacts) ? 'checked' : '' ?>>
      <label class="form-check-label"><?=htmlspecialchars($tac)?></label>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="col-auto">
    <button class="btn btn-primary">Generate Runbook</button>
  </div>
</form>

<?php if ($apt): ?>
<hr>
<form method="post">
  <button name="export_pdf" class="btn btn-success">Download PDF</button>
  <button name="save_runbook" class="btn btn-secondary">Save Runbook</button>
</form>
<div class="mt-3">
  <?= RunbookGenerator::renderHtml($apt, $techniques, $tacts) ?>
</div>
<?php endif; ?>

</body></html>
