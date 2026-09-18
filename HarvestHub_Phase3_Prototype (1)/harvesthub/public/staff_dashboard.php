<?php
require_once __DIR__ . '/auth.php';
$user = requireRole('staff');
$navTitle = 'Garden Coordinator Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HarvestHub — Coordinator Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include __DIR__ . '/nav_partial.php'; ?>

<main class="wrap page-wrap" id="top">
  <div class="staff-dashboard-grid">

    <div class="pending-request-panels">
      <div class="panel">
        <p class="panel-title">Pending Plot Applications</p>
        <div class="pending-request-list" id="applications-list"></div>
        <p class="text-muted" id="applications-empty" hidden>No pending applications.</p>
      </div>

      <div class="panel">
        <p class="panel-title">Pending Resource Requests</p>
        <div class="pending-request-list" id="resource-txns-list"></div>
        <p class="text-muted" id="resource-txns-empty" hidden>No pending resource requests.</p>
      </div>
    </div>

    <div class="panel">
      <p class="panel-title">All Plots</p>
      <div class="table-wrap">
        <table class="data-table">
          <thead><tr><th>Plot</th><th>Status</th><th>Gardener</th></tr></thead>
          <tbody id="plots-table"></tbody>
        </table>
      </div>
    </div>

    <div class="panel">
      <p class="panel-title">All Resources</p>
      <div class="table-wrap">
        <table class="data-table">
          <thead><tr><th>Resource</th><th>Total</th><th>Available</th><th>Borrowed By</th></tr></thead>
          <tbody id="resources-table"></tbody>
        </table>
      </div>
    </div>

  </div>
</main>

<div class="toast-container" id="toast-container" aria-live="polite"></div>
<script src="assets/staff.js"></script>
</body>
</html>
