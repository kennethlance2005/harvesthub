// register.js — "Create an Account" form: submits a pending request,
// it does NOT log the person in or create the account immediately.

const form = document.getElementById('register-form');
const alertEl = document.getElementById('register-alert');
const successEl = document.getElementById('register-success');

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  alertEl.hidden = true;
  successEl.hidden = true;

  const password = document.getElementById('password').value;
  const confirmPassword = document.getElementById('confirm-password').value;

  if (password !== confirmPassword) {
    alertEl.textContent = 'Passwords do not match.';
    alertEl.hidden = false;
    return;
  }

  const formData = new URLSearchParams({
    action: 'signup_request',
    first_name: document.getElementById('first-name').value.trim(),
    last_name: document.getElementById('last-name').value.trim(),
    age: document.getElementById('age').value.trim(),
    location: document.getElementById('location').value.trim(),
    email: document.getElementById('email').value.trim(),
    password: password,
    confirm_password: confirmPassword,
  });

  const submitBtn = form.querySelector('button[type="submit"]');
  submitBtn.disabled = true;

  try {
    const res = await fetch('api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData,
    });
    const data = await res.json();

    if (data.ok) {
      form.reset();
      form.hidden = true;
      successEl.textContent = "Request sent! An administrator will review your account request, and you'll be able to log in once it's approved.";
      successEl.hidden = false;
    } else {
      alertEl.textContent = (data.errors || [data.error]).filter(Boolean).join(' ') || 'Could not submit request.';
      alertEl.hidden = false;
      submitBtn.disabled = false;
    }
  } catch (err) {
    alertEl.textContent = 'Network error. Please try again.';
    alertEl.hidden = false;
    submitBtn.disabled = false;
  }
});
