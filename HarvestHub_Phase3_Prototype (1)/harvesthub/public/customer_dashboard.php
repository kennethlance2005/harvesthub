<?php
require_once __DIR__ . '/auth.php';
$user = requireRole('customer');
$navTitle = 'Community Gardener Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HarvestHub — My Garden</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include __DIR__ . '/nav_partial.php'; ?>

<main class="wrap page-wrap" id="top">

  <div class="page-head">
    <h1>Welcome back, <?= htmlspecialchars($user['name']) ?></h1>
    <a href="index.php" class="btn btn-accent btn-sm">Go to Produce Exchange Board →</a>
  </div>

  <div class="grid grid-3">

    <div class="panel">
      <p class="panel-title">My Plot</p>
      <div id="plot-status"></div>
    </div>

    <div class="panel">
      <p class="panel-title">Crop Lifecycle Log</p>
      <form id="croplog-form" novalidate style="margin-bottom: 16px;">
        <div class="field">
          <input type="text" id="crop-name" placeholder="Crop name" maxlength="60" required>
        </div>
        <div class="field">
          <input type="text" id="crop-notes" placeholder="Maintenance notes" maxlength="300">
        </div>
        <div class="field">
          <input type="text" id="crop-yield" placeholder="Harvest yield (e.g. 5 kg)" maxlength="60">
        </div>
        <button type="submit" class="btn btn-ghost btn-block">Add Log Entry</button>
        <p class="form-alert" id="croplog-alert" hidden></p>
      </form>
      <div id="croplog-list" class="scroll-y"></div>
    </div>

    <div class="panel">
      <p class="panel-title">Resource Sharing Hub</p>
      <form id="resource-form" class="inline-form" style="margin-bottom: 14px;">
        <select id="resource-select" class="field-select" style="flex: 1;"></select>
        <input type="number" id="resource-qty" min="1" value="1" class="field-qty" style="width: 64px;">
        <button type="submit" class="btn btn-ghost btn-sm">Request</button>
      </form>
      <p class="form-alert" id="resource-alert" hidden></p>
      <p class="text-muted" style="font-size: 0.85rem; margin: 14px 0 6px;">My Requests</p>
      <div id="my-requests-list" class="scroll-y" style="max-height: 160px;"></div>
    </div>

  </div>
</main>

<div class="toast-container" id="toast-container" aria-live="polite"></div>
<script src="assets/customer.js"></script>
</body>
</html>
