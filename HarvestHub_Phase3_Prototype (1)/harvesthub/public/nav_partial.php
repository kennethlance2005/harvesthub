<?php
/** Expects $navTitle and $user (from requireRole) to be set before include. */
?>
<header class="site-header app-header">
  <div class="wrap app-header-row">
    <a class="wordmark" href="#top">HarvestHub</a>
    <div class="app-header-right">
      <span class="app-header-title"><?= htmlspecialchars($navTitle) ?></span>
      <span class="app-header-greeting">Hi, <?= htmlspecialchars($user['name'] ?? '') ?></span>
      <?php if (isset($user['role']) && $user['role'] === 'staff'): ?>
        <a href="api.php?action=export_staff_report" class="btn btn-light btn-sm" target="_blank" style="margin-right: 4px; border-radius: var(--radius);">Download Report</a>
      <?php endif; ?>
      <a href="logout.php" class="btn btn-on-dark btn-sm">Log Out</a>
    </div>
  </div>
</header>