<?php
require_once __DIR__ . '/auth.php';
if ($user = currentUser()) {
    header('Location: ' . loginRedirectFor($user['role']));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HarvestHub — Create an Account</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="login-shell">
  <div class="login-card register-card">

    <div class="login-brand">
      <span class="sprout">🌱</span>
      <h1>HarvestHub</h1>
    </div>

    <h2 class="login-title">Create an Account</h2>
    <p class="signup-hint" style="margin-top: -12px; margin-bottom: 22px;">
      This creates a request — a system administrator has to approve it before you can log in.
    </p>

    <form id="register-form" novalidate>

      <div class="field-row">
        <div class="field field-underline">
          <label for="first-name">First Name</label>
          <input type="text" id="first-name" name="first_name" autocomplete="given-name" required>
        </div>
        <div class="field field-underline">
          <label for="last-name">Last Name</label>
          <input type="text" id="last-name" name="last_name" autocomplete="family-name" required>
        </div>
      </div>

      <div class="field-row">
        <div class="field field-underline">
          <label for="age">Age</label>
          <input type="number" id="age" name="age" min="13" max="120" autocomplete="off" required>
        </div>
        <div class="field field-underline">
          <label for="location">Location</label>
          <input type="text" id="location" name="location" autocomplete="address-level2" required>
        </div>
      </div>

      <div class="field field-underline">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" autocomplete="email" required>
      </div>

      <div class="field field-underline">
        <label for="role">I am a</label>
        <select id="role" name="role" required>
          <option value="" disabled selected>Select a role&hellip;</option>
          <option value="customer">Community Gardener</option>
          <option value="staff">Garden Coordinator</option>
        </select>
      </div>

      <div class="field-row">
        <div class="field field-underline">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" autocomplete="new-password" minlength="6" required>
        </div>
        <div class="field field-underline">
          <label for="confirm-password">Confirm Password</label>
          <input type="password" id="confirm-password" name="confirm_password" autocomplete="new-password" minlength="6" required>
        </div>
      </div>

      <button type="submit" class="btn btn-light btn-block" style="margin-top: 4px;">Request Account</button>
    </form>
    <p class="form-alert" id="register-alert" role="alert" hidden></p>
    <p class="form-success" id="register-success" role="status" hidden></p>

    <p class="signup-hint">
      Already have an account? <a href="login.php" class="signup-link">Back to Log In</a>
    </p>
  </div>
</div>

<script src="assets/register.js"></script>
</body>
</html>
