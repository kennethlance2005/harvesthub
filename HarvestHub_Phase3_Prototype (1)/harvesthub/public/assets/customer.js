// customer.js — Community Gardener dashboard logic

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

// ---------- Plot ----------

async function loadPlot() {
  const res = await fetch('api.php?action=my_plot');
  const data = await res.json();
  const el = document.getElementById('plot-status');
  if (!data.ok) return;

  if (data.plot) {
    el.innerHTML = `
      <p class="form-success" style="display:block;"><strong>${escapeHtml(data.plot.Label)}</strong> is assigned to you.</p>
      <p class="text-muted" style="font-size: 0.85rem;">Log your crops and resource needs using the panels alongside this one.</p>
    `;
    return;
  }

  if (data.pending_application) {
    el.innerHTML = `
      <p class="form-alert" style="display:block; background:#f3e9d6; color:#8a5a1e;">
        Application for <strong>${escapeHtml(data.pending_application.Label)}</strong> is pending Coordinator approval.
      </p>
    `;
    return;
  }

  if (data.available_plots.length === 0) {
    el.innerHTML = `<p class="text-muted" style="font-size: 0.9rem;">No plots available right now. Check back later.</p>`;
    return;
  }

  el.innerHTML = `
    <p class="text-muted" style="font-size: 0.9rem;">You don't have a plot yet. Apply for one below:</p>
    <div class="inline-form">
      <select id="plot-select" style="flex: 1;">
        ${data.available_plots.map(p => `<option value="${p.PltID}">${escapeHtml(p.Label)}</option>`).join('')}
      </select>
      <button class="btn btn-accent btn-sm" id="apply-plot-btn">Apply</button>
    </div>
  `;

  document.getElementById('apply-plot-btn').addEventListener('click', async () => {
    const pltId = document.getElementById('plot-select').value;
    const result = await postAction('apply_plot', { plt_id: pltId });
    if (result.ok) {
      showToast('Application submitted!', 'success');
      loadPlot();
    } else {
      showToast(result.error || 'Could not submit application.', 'danger');
    }
  });
}

// ---------- Crop Log ----------

async function loadCropLog() {
  const res = await fetch('api.php?action=my_croplog');
  const data = await res.json();
  const el = document.getElementById('croplog-list');
  if (!data.ok) return;

  if (data.logs.length === 0) {
    el.innerHTML = '<p class="text-muted" style="font-size: 0.88rem;">No entries yet.</p>';
    return;
  }

  el.innerHTML = data.logs.map(log => `
    <div style="border-bottom: 1px solid var(--line); padding: 10px 0;">
      <div style="display:flex; justify-content: space-between;">
        <strong style="font-size: 0.9rem;">${escapeHtml(log.CropName)}</strong>
        <span class="text-muted" style="font-size: 0.72rem;">${escapeHtml(log.LoggedAt)}</span>
      </div>
      ${log.MaintenanceNotes ? `<div class="text-muted" style="font-size: 0.85rem;">${escapeHtml(log.MaintenanceNotes)}</div>` : ''}
      ${log.HarvestYield ? `<div style="font-size: 0.85rem;">Yield: ${escapeHtml(log.HarvestYield)}</div>` : ''}
    </div>
  `).join('');
}

document.getElementById('croplog-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const alertEl = document.getElementById('croplog-alert');
  alertEl.hidden = true;

  const cropName = document.getElementById('crop-name').value.trim();
  if (cropName === '') {
    alertEl.textContent = 'Crop name is required.';
    alertEl.hidden = false;
    return;
  }

  const result = await postAction('croplog_create', {
    crop_name: cropName,
    notes: document.getElementById('crop-notes').value.trim(),
    yield: document.getElementById('crop-yield').value.trim(),
  });

  if (result.ok) {
    e.target.reset();
    loadCropLog();
  } else {
    alertEl.textContent = result.error || 'Could not save entry.';
    alertEl.hidden = false;
  }
});

// ---------- Resources ----------

async function loadResources() {
  const res = await fetch('api.php?action=resources');
  const data = await res.json();
  if (!data.ok) return;

  document.getElementById('resource-select').innerHTML = data.resources.map(r => `
    <option value="${r.ResourceID}" ${r.AvailableQty === 0 ? 'disabled' : ''}>
      ${escapeHtml(r.Name)} (${r.AvailableQty} left)
    </option>
  `).join('');
}

async function loadMyRequests() {
  const res = await fetch('api.php?action=my_resource_requests');
  const data = await res.json();
  const el = document.getElementById('my-requests-list');
  if (!data.ok) return;

  if (data.requests.length === 0) {
    el.innerHTML = '<p class="text-muted" style="font-size: 0.85rem;">No requests yet.</p>';
    return;
  }

  const badgeClass = { Requested: 'badge-brown', Approved: 'badge-green', Rejected: 'badge-neutral' };
  el.innerHTML = data.requests.map(r => `
    <div style="display:flex; justify-content: space-between; align-items:center; border-bottom: 1px solid var(--line); padding: 6px 0;">
      <span style="font-size: 0.85rem;">${escapeHtml(String(r.Qty))}x ${escapeHtml(r.Name)}</span>
      <span class="badge ${badgeClass[r.Status] || 'badge-neutral'}">${escapeHtml(r.Status)}</span>
    </div>
  `).join('');
}

document.getElementById('resource-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const alertEl = document.getElementById('resource-alert');
  alertEl.hidden = true;

  const resourceId = document.getElementById('resource-select').value;
  const qty = document.getElementById('resource-qty').value;

  const result = await postAction('resource_request', { resource_id: resourceId, qty });
  if (result.ok) {
    showToast('Resource requested!', 'success');
    loadResources();
    loadMyRequests();
  } else {
    alertEl.textContent = result.error || 'Could not submit request.';
    alertEl.hidden = false;
  }
});

document.addEventListener('DOMContentLoaded', () => {
  loadPlot();
  loadCropLog();
  loadResources();
  loadMyRequests();
});
