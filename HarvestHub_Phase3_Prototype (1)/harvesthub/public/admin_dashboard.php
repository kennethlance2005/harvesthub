<?php
require_once __DIR__ . '/auth.php';
$user = requireRole('admin');
$navTitle = 'System Administrator Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HarvestHub — Admin Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include __DIR__ . '/nav_partial.php'; ?>

<main class="wrap page-wrap" id="top">

  <!-- Wrap stats in a unified panel -->
  <div class="panel" style="margin-bottom: 24px;">
    <p class="panel-title">System Overview</p>
    <div class="stat-grid" id="stats-row" style="margin-bottom: 0;"></div>
  </div>

  <div class="panel" style="margin-bottom: 24px;">
    <p class="panel-title">Pending Account Requests</p>
    <div class="pending-request-list" id="signups-list"></div>
    <p class="text-muted" id="signups-empty" hidden>No pending account requests.</p>
  </div>

  <div class="grid" style="grid-template-columns: 2fr 1fr;">
    <div>
      <div class="panel" style="margin-bottom: 24px;">
        <p class="panel-title">Community Gardeners</p>
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr><th>Name</th><th>Email</th><th></th></tr></thead>
            <tbody id="gardeners-table"></tbody>
          </table>
        </div>
      </div>

      <div class="panel">
        <p class="panel-title">Garden Coordinators</p>
        <div class="table-wrap">
          <table class="data-table">
            <thead><tr><th>Name</th><th>Email</th><th>Shift</th><th></th></tr></thead>
            <tbody id="coordinators-table"></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="panel">
      <p class="panel-title">Add Garden Coordinator</p>
      <form id="add-coord-form" novalidate>
        <div class="field">
          <label for="coord-name">Name</label>
          <input type="text" id="coord-name" required>
        </div>
        <div class="field">
          <label for="coord-email">Email</label>
          <input type="email" id="coord-email" required>
        </div>
        <div class="field">
          <label for="coord-password">Temporary Password</label>
          <input type="password" id="coord-password" minlength="6" required>
        </div>
        <div class="field">
          <label for="coord-shift">Shift</label>
          <select id="coord-shift" style="width: 100%;">
            <option value="Morning">Morning</option>
            <option value="Afternoon">Afternoon</option>
            <option value="Evening">Evening</option>
          </select>
        </div>
        <button type="submit" class="btn btn-accent btn-block">Create Account</button>
        <p class="form-alert" id="coord-alert" hidden></p>
        <p class="form-success" id="coord-success" hidden></p>
      </form>
    </div>
  </div>
</main>

<!-- Delete confirmation modal -->
<div class="modal-overlay" id="delete-modal" hidden>
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
    <h3 id="delete-modal-title">Remove this account?</h3>
    <p id="delete-modal-body">This cannot be undone.</p>
    <div class="modal-actions">
      <button type="button" class="btn btn-ghost" id="delete-cancel">Cancel</button>
      <button type="button" class="btn btn-accent" id="delete-confirm">Remove</button>
    </div>
  </div>
</div>

<div class="toast-container" id="toast-container" aria-live="polite"></div>
<script src="assets/admin.js"></script>
</body>
</html>