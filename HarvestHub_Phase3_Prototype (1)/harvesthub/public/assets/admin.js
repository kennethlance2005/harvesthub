// admin.js — Admin dashboard logic

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

function showToast(message, type = 'success') {
  const toastEl = document.createElement('div');
  toastEl.className = `toast${type === 'danger' ? ' toast-danger' : ''}`;
  toastEl.textContent = message;
  document.getElementById('toast-container').appendChild(toastEl);
  setTimeout(() => toastEl.remove(), 3500);
}

async function loadStats() {
  const res = await fetch('api.php?action=stats');
  const data = await res.json();
  if (!data.ok) return;

  const cards = [
    ['Gardeners', data.stats.gardeners],
    ['Coordinators', data.stats.coordinators],
    ['Plots Occupied', data.stats.plots_occupied],
    ['Plots Available', data.stats.plots_available],
    ['Pending Applications', data.stats.pending_applications],
    ['Pending Resource Requests', data.stats.pending_resource_txns],
    ['Active Listings', data.stats.active_listings],
    ['Completed Trades', data.stats.completed_trades],
  ];

  document.getElementById('stats-row').innerHTML = cards.map(([label, value]) => `
    <div class="stat-card">
      <div class="stat-value">${value}</div>
      <div class="stat-label">${label}</div>
    </div>
  `).join('');
}

async function loadAccounts() {
  const res = await fetch('api.php?action=accounts');
  const data = await res.json();
  if (!data.ok) return;

  document.getElementById('gardeners-table').innerHTML = data.gardeners.map(g => `
    <tr>
      <td>${escapeHtml(g.Name)}</td>
      <td>${escapeHtml(g.Email)}</td>
      <td class="text-right">
        <button class="btn btn-ghost btn-sm delete-btn" data-table="gardener" data-id="${g.id}" data-name="${escapeHtml(g.Name)}">Remove</button>
      </td>
    </tr>
  `).join('') || '<tr><td colspan="3" class="text-muted">No gardeners yet.</td></tr>';

  document.getElementById('coordinators-table').innerHTML = data.coordinators.map(c => `
    <tr>
      <td>${escapeHtml(c.Name)}</td>
      <td>${escapeHtml(c.Email)}</td>
      <td>${escapeHtml(c.Shift)}</td>
      <td class="text-right">
        <button class="btn btn-ghost btn-sm delete-btn" data-table="coordinator" data-id="${c.id}" data-name="${escapeHtml(c.Name)}">Remove</button>
      </td>
    </tr>
  `).join('') || '<tr><td colspan="4" class="text-muted">No coordinators yet.</td></tr>';

  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', () => openDeleteModal(btn.dataset.table, btn.dataset.id, btn.dataset.name));
  });
}

// ---------- Delete confirmation modal ----------

const deleteModal = document.getElementById('delete-modal');
const deleteModalBody = document.getElementById('delete-modal-body');
const deleteCancelBtn = document.getElementById('delete-cancel');
const deleteConfirmBtn = document.getElementById('delete-confirm');
let pendingDelete = null;

function openDeleteModal(table, id, name) {
  pendingDelete = { table, id };
  deleteModalBody.textContent = `Remove ${name}'s account? This cannot be undone.`;
  deleteModal.hidden = false;
  deleteConfirmBtn.focus();
}
function closeDeleteModal() {
  deleteModal.hidden = true;
  pendingDelete = null;
}
deleteCancelBtn.addEventListener('click', closeDeleteModal);
deleteModal.addEventListener('click', (e) => { if (e.target === deleteModal) closeDeleteModal(); });
document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !deleteModal.hidden) closeDeleteModal(); });

deleteConfirmBtn.addEventListener('click', async () => {
  if (!pendingDelete) return;
  const { table, id } = pendingDelete;
  closeDeleteModal();

  const res = await fetch('api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ action: 'delete_account', table, id }),
  });
  const data = await res.json();
  if (data.ok) {
    showToast('Account removed.', 'success');
    loadAccounts();
    loadStats();
  } else {
    showToast(data.error || 'Could not remove account.', 'danger');
  }
});

// ---------- Add coordinator ----------

document.getElementById('add-coord-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const alertEl = document.getElementById('coord-alert');
  const successEl = document.getElementById('coord-success');
  alertEl.hidden = true;
  successEl.hidden = true;

  const formData = new URLSearchParams({
    action: 'add_coordinator',
    name: document.getElementById('coord-name').value.trim(),
    email: document.getElementById('coord-email').value.trim(),
    password: document.getElementById('coord-password').value,
    shift: document.getElementById('coord-shift').value,
  });

  const res = await fetch('api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: formData,
  });
  const data = await res.json();

  if (data.ok) {
    successEl.textContent = 'Coordinator account created.';
    successEl.hidden = false;
    e.target.reset();
    loadAccounts();
    loadStats();
  } else {
    alertEl.textContent = (data.errors || [data.error]).join(' ');
    alertEl.hidden = false;
  }
});

document.addEventListener('DOMContentLoaded', () => {
  loadStats();
  loadAccounts();
});
