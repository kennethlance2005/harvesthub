<?php
require_once __DIR__ . '/auth.php';
$user = requireRole('customer');
$navTitle = 'Produce Exchange Board';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HarvestHub — Produce Exchange Board</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header class="site-header app-header">
  <div class="wrap app-header-row">
    <a class="wordmark" href="customer_dashboard.php">HarvestHub</a>
    <div class="app-header-right">
      <span class="app-header-title">Produce Exchange Board</span>
      <span class="app-header-greeting">Hi, <?= htmlspecialchars($user['name']) ?></span>
      <a href="logout.php" class="btn btn-on-dark btn-sm">Log Out</a>
    </div>
  </div>
</header>

<main class="wrap page-wrap" id="top">

  <a href="customer_dashboard.php" class="back-link">← Back to My Dashboard</a>

  <section class="app-section" id="board" style="padding: 0;">
    <div class="app-row">

      <div class="post-panel" id="post">
        <h2>Post surplus produce</h2>
        <p class="panel-hint">Fields marked with an asterisk are required.</p>

        <form id="listing-form" novalidate>
          <div class="field">
            <label for="crop">Crop *</label>
            <input type="text" id="crop" name="crop" maxlength="60" autocomplete="off" required>
            <p class="field-error" id="crop-error" hidden>Enter a crop name using letters, spaces, or hyphens (max 60 characters).</p>
          </div>

          <div class="field">
            <label for="qty">Quantity *</label>
            <input type="number" id="qty" name="qty" min="1" max="1000" required>
            <p class="field-error" id="qty-error" hidden>Enter a whole number between 1 and 1000.</p>
          </div>

          <div class="field">
            <label for="notes">Notes <span class="field-optional">(optional)</span></label>
            <textarea id="notes" name="notes" maxlength="200" rows="3"></textarea>
            <p class="field-hint"><span id="notes-count">200</span> characters left</p>
          </div>

          <button type="submit" class="btn btn-accent btn-block">Post listing</button>

          <p class="form-alert" id="form-alert" role="alert" hidden></p>
          <p class="form-success" id="form-success" role="status" hidden></p>
        </form>
      </div>

      <div class="board-panel">
        <div class="board-head">
          <h2>Available listings</h2>
          <p class="board-count"><span id="results-count">0</span> results</p>
        </div>

        <div class="board-controls">
          <input type="text" id="search" placeholder="Search by crop, e.g. tomato" aria-label="Search by crop">
          <input type="number" id="min-qty" min="0" placeholder="Min qty" aria-label="Minimum quantity">
          <select id="sort" aria-label="Sort listings">
            <option value="newest">Newest first</option>
            <option value="oldest">Oldest first</option>
            <option value="qty_high">Quantity: high to low</option>
            <option value="qty_low">Quantity: low to high</option>
          </select>
        </div>

        <ul class="listings" id="listings" aria-live="polite"></ul>
        <p class="empty-state" id="empty-state" hidden>No listings match your search yet — try widening it, or be the first to post.</p>
      </div>

    </div>
  </section>

</main>

<!-- Claim confirmation modal -->
<div class="modal-overlay" id="claim-modal" hidden>
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="claim-modal-title">
    <h3 id="claim-modal-title">Claim this listing?</h3>
    <p id="claim-modal-body"></p>
    <div class="modal-actions">
      <button type="button" class="btn btn-ghost" id="claim-cancel">Cancel</button>
      <button type="button" class="btn btn-accent" id="claim-confirm">Claim it</button>
    </div>
  </div>
</div>

<div class="toast-container" id="toast-container" aria-live="polite"></div>

<script src="assets/app.js"></script>
</body>
</html>
