// staff.js — Garden Coordinator dashboard logic

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

async function postAction(action, params) {
  const res = await fetch('api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ action, ...params }),
  });
  return res.json();
}

async function loadApplications() {
  const res = await fetch('api.php?action=pending_applications');
  const data = await res.json();
  const listEl = document.getElementById('applications-list');
  const emptyEl = document.getElementById('applications-empty');
  if (!data.ok) return;

  if (data.applications.length === 0) {
    listEl.innerHTML = '';
    emptyEl.hidden = false;
    return;
  }
  emptyEl.hidden = true;

  listEl.innerHTML = data.applications.map(app => `
    <div class="action-row">
      <div>
        <div class="action-row-title">${escapeHtml(app.GardenerName)}</div>
        <div class="action-row-sub">Requesting ${escapeHtml(app.Label)}</div>
      </div>
      <div class="action-row-actions">
        <button class="btn btn-accent btn-sm approve-app" data-id="${app.AppID}">Approve</button>
        <button class="btn btn-ghost btn-sm reject-app" data-id="${app.AppID}">Reject</button>
      </div>
    </div>
  `).join('');

  listEl.querySelectorAll('.approve-app').forEach(btn =>
    btn.addEventListener('click', () => processApplication(btn.dataset.id, 'approve')));
  listEl.querySelectorAll('.reject-app').forEach(btn =>
    btn.addEventListener('click', () => processApplication(btn.dataset.id, 'reject')));
}

async function processApplication(appId, decision) {
  const data = await postAction('process_application', { app_id: appId, decision });
  if (data.ok) {
    showToast(`Application ${decision === 'approve' ? 'approved' : 'rejected'}.`, 'success');
    loadApplications();
    loadPlots();
  } else {
    showToast(data.error || 'Could not process application.', 'danger');
  }
}

async function loadResourceTxns() {
  const res = await fetch('api.php?action=pending_resource_txns');
  const data = await res.json();
  const listEl = document.getElementById('resource-txns-list');
  const emptyEl = document.getElementById('resource-txns-empty');
  if (!data.ok) return;

  if (data.transactions.length === 0) {
    listEl.innerHTML = '';
    emptyEl.hidden = false;
    return;
  }
  emptyEl.hidden = true;

  listEl.innerHTML = data.transactions.map(txn => `
    <div class="action-row">
      <div>
        <div class="action-row-title">${escapeHtml(txn.GardenerName)}</div>
        <div class="action-row-sub">${escapeHtml(String(txn.Qty))}x ${escapeHtml(txn.ResourceName)}</div>
      </div>
      <div class="action-row-actions">
        <button class="btn btn-accent btn-sm approve-txn" data-id="${txn.TxnID}">Approve</button>
        <button class="btn btn-ghost btn-sm reject-txn" data-id="${txn.TxnID}">Reject</button>
      </div>
    </div>
  `).join('');

  listEl.querySelectorAll('.approve-txn').forEach(btn =>
    btn.addEventListener('click', () => processResourceTxn(btn.dataset.id, 'approve')));
  listEl.querySelectorAll('.reject-txn').forEach(btn =>
    btn.addEventListener('click', () => processResourceTxn(btn.dataset.id, 'reject')));
}

async function processResourceTxn(txnId, decision) {
  const data = await postAction('process_resource_txn', { txn_id: txnId, decision });
  if (data.ok) {
    showToast(`Request ${decision === 'approve' ? 'approved' : 'rejected'}.`, 'success');
    loadResourceTxns();
  } else {
    showToast(data.error || 'Could not process request.', 'danger');
  }
}

async function loadPlots() {
  const res = await fetch('api.php?action=all_plots');
  const data = await res.json();
  if (!data.ok) return;

  document.getElementById('plots-table').innerHTML = data.plots.map(p => `
    <tr>
      <td>${escapeHtml(p.Label)}</td>
      <td><span class="badge ${p.Status === 'Occupied' ? 'badge-green' : 'badge-neutral'}">${escapeHtml(p.Status)}</span></td>
      <td>${p.GardenerName ? escapeHtml(p.GardenerName) : '<span class="text-muted">—</span>'}</td>
    </tr>
  `).join('');
}

async function loadResources() {
  const res = await fetch('api.php?action=all_resources');
  const data = await res.json();
  if (!data.ok) return;

  document.getElementById('resources-table').innerHTML = data.resources.map(resource => `
    <tr>
      <td>${escapeHtml(resource.Name)}</td>
      <td>${escapeHtml(String(resource.TotalQty))}</td>
      <td>${escapeHtml(String(resource.AvailableQty))}</td>
      <td>${resource.Borrowers ? escapeHtml(resource.Borrowers) : '<span class="text-muted">None</span>'}</td>
    </tr>
  `).join('');
}

document.addEventListener('DOMContentLoaded', () => {
  loadApplications();
  loadResourceTxns();
  loadPlots();
  loadResources();
});
