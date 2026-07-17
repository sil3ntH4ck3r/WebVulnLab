let currentUser = null;

async function api(method, path) {
  const opts = { method, headers: { 'Content-Type': 'application/json' } };
  const res = await fetch(path, opts);
  const data = await res.json();
  if (!res.ok) throw { status: res.status, message: data.error || 'Request failed', data };
  return data;
}

function escapeHtml(str) {
  const d = document.createElement('div');
  d.textContent = str;
  return d.innerHTML;
}

function qs(obj) {
  return Object.entries(obj).map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`).join('&');
}

function formatDate(iso) {
  return new Date(iso).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function statusBadge(s) {
  const m = { open: 'badge-open', closed: 'badge-closed', investigating: 'badge-investigating' };
  return `<span class="badge ${m[s] || ''}">${s}</span>`;
}

function clsBadge(c) {
  const m = { C1: 'badge-c1', C2: 'badge-c2', C3: 'badge-c3' };
  return `<span class="badge ${m[c] || ''}">${c}</span>`;
}

function severityHtml(s) {
  return `<span class="severity-dot severity-${s}"></span>${s}`;
}

function findingsTable(results) {
  if (!results.length) return '<div class="empty-state"><p>No findings match.</p></div>';
  let html = '<table><thead><tr><th>Sev</th><th>Title</th><th>Status</th><th>Department</th><th>Class</th><th>Assignee</th><th>Date</th></tr></thead><tbody>';
  for (const f of results) {
    html += `<tr class="clickable" onclick="navTo('/finding/${f.id}')">
      <td>${severityHtml(f.severity)}</td>
      <td><strong>${escapeHtml(f.title)}</strong></td>
      <td>${statusBadge(f.status)}</td>
      <td>${escapeHtml(f.department)}</td>
      <td>${clsBadge(f.classification)}</td>
      <td style="font-size:0.78em;">${escapeHtml(f.assignee)}</td>
      <td style="white-space:nowrap;">${formatDate(f.createdAt)}</td>
    </tr>`;
  }
  html += '</tbody></table>';
  return html;
}

async function init() {
  try {
    const me = await api('GET', '/api/me');
    currentUser = me;
    document.getElementById('logoutLink').style.display = 'inline';
    document.getElementById('navUser').textContent = `${me.name} (${me.role}, C${me.clearance})`;
  } catch {
    currentUser = null;
  }
  document.getElementById('logoutLink').addEventListener('click', async (e) => {
    e.preventDefault();
    await api('POST', '/api/logout');
    currentUser = null;
    navTo('/');
  });
  document.querySelectorAll('[data-nav]').forEach(a => {
    a.addEventListener('click', e => { e.preventDefault(); navTo(a.getAttribute('href')); });
  });
  navTo(window.location.pathname + window.location.search);
  window.addEventListener('popstate', () => navTo(window.location.pathname + window.location.search, false));
}

function navTo(path, push = true) {
  if (push) window.history.pushState({}, '', path);
  if (!currentUser && !path.startsWith('/')) return navTo('/', push);
  if (path === '/' || path === '') return renderLogin();
  if (path.startsWith('/search')) return renderSearch();
  if (path.startsWith('/dashboard')) return renderDashboard();
  if (path.startsWith('/finding/')) return renderFinding(path);
  renderLogin();
}

function renderLogin() {
  document.getElementById('app').innerHTML = `
    <div class="card" style="max-width:400px;margin:80px auto;">
      <h1>RSQL Audit Platform</h1>
      <p style="color:#78909c;font-size:0.85em;margin-bottom:20px;">Sign in to access audit findings.</p>
      <div id="loginError"></div>
      <div class="form-group"><label>Email</label><input type="email" id="loginEmail" value="analyst@rsqli.local"></div>
      <div class="form-group"><label>Password</label><input type="password" id="loginPassword" value="analyst123"></div>
      <button onclick="doLogin()" style="width:100%;">Sign In</button>
    </div>`;
}

async function doLogin() {
  try {
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    const path = `/api/users?q=${encodeURIComponent(`email==${email};password==${password}`)}`;
    const u = await api('GET', path);
    currentUser = u;
    document.getElementById('logoutLink').style.display = 'inline';
    document.getElementById('navUser').textContent = `${u.name} (${u.role}, C${u.clearance})`;
    navTo('/dashboard');
  } catch (e) {
    document.getElementById('loginError').innerHTML = `<div class="alert alert-error">${escapeHtml(e.message)}</div>`;
  }
}

async function renderDashboard() {
  const app = document.getElementById('app');
  app.innerHTML = '<h1>Loading...</h1>';
  try {
    const data = await api('GET', '/api/findings');
    let html = `<div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
      <h1 style="margin-bottom:0;">Findings</h1>
      <a href="/search" class="btn btn-sm" data-nav onclick="navTo('/search')">Search</a>
    </div>`;

    html += `<div class="stats-row">
      <div class="stat-card success"><div class="stat-value">${data.total}</div><div class="stat-label">Total Matching</div></div>
      <div class="stat-card ${data.results.length < data.total ? 'warning' : 'success'}"><div class="stat-value">${data.results.length}</div><div class="stat-label">Visible (C${data.userClearance})</div></div>
    </div>`;

    html += `<div class="card" style="padding:0;overflow-x:auto;">${findingsTable(data.results)}</div>`;

    app.innerHTML = html;
  } catch (e) {
    app.innerHTML = `<div class="alert alert-error">${escapeHtml(e.message)}</div>`;
  }
}

function renderSearch() {
  const app = document.getElementById('app');
  app.innerHTML = `
    <h1>Search</h1>
    <div class="card">
      <div class="form-row">
        <div class="form-group">
          <label>Status</label>
          <select id="f-status">
            <option value="">All</option>
            <option value="open">Open</option>
            <option value="closed">Closed</option>
            <option value="investigating">Investigating</option>
          </select>
        </div>
        <div class="form-group">
          <label>Min Severity</label>
          <select id="f-sevMin">
            <option value="">None</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
          </select>
        </div>
        <div class="form-group">
          <label>Max Severity</label>
          <select id="f-sevMax">
            <option value="">None</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
          </select>
        </div>
        <div class="form-group">
          <label>Department</label>
          <select id="f-dept">
            <option value="">All</option>
            <option value="engineering">Engineering</option>
            <option value="security">Security</option>
            <option value="compliance">Compliance</option>
            <option value="executive">Executive</option>
          </select>
        </div>
      </div>
      <button onclick="applySearch()" class="btn-sm">Apply</button>
    </div>
    <div id="searchResults"><div class="card">${findingsTable([])}</div></div>
  `;
  applySearch();
}

function getSearchRSQL() {
  const parts = [];
  const status = document.getElementById('f-status').value;
  const sevMin = document.getElementById('f-sevMin').value;
  const sevMax = document.getElementById('f-sevMax').value;
  const dept = document.getElementById('f-dept').value;
  if (status) parts.push(`status==${status}`);
  if (sevMin) parts.push(`severity=gt=${Math.max(0, parseInt(sevMin) - 1)}`);
  if (sevMax) parts.push(`severity=lt=${parseInt(sevMax) + 1}`);
  if (dept) parts.push(`department==${dept}`);
  return parts.join(';');
}

async function applySearch() {
  const rsql = getSearchRSQL();
  const container = document.getElementById('searchResults');
  if (!container) return;
  container.innerHTML = '<p style="color:#78909c;">Loading...</p>';
  try {
    const data = await api('GET', `/api/findings?${qs({ q: rsql })}`);
    let html = '';
    if (data.total !== data.results.length) {
      html += `<div class="alert alert-warning" style="font-size:0.85em;">
        ${data.total} total matches, ${data.results.length} visible (C${data.userClearance}).
        ${data.total - data.results.length} finding(s) require higher clearance.
      </div>`;
    }
    html += `<div class="stats-row">
      <div class="stat-card success"><div class="stat-value">${data.total}</div><div class="stat-label">Matching</div></div>
      <div class="stat-card ${data.results.length < data.total ? 'warning' : 'success'}"><div class="stat-value">${data.results.length}</div><div class="stat-label">Visible (C${data.userClearance})</div></div>
    </div>`;
    html += `<div class="card" style="padding:0;overflow-x:auto;">${findingsTable(data.results)}</div>`;
    container.innerHTML = html;
  } catch (e) {
    container.innerHTML = `<div class="alert alert-error">${escapeHtml(e.message)}</div>`;
  }
}

async function renderFinding(path) {
  const id = path.replace('/finding/', '');
  const app = document.getElementById('app');
  app.innerHTML = '<h1>Loading...</h1>';
  try {
    const f = await api('GET', `/api/findings/${id}`);
    let html = `<a href="javascript:history.back()" class="btn btn-sm btn-secondary" style="margin-bottom:12px;">&larr; Back</a>`;
    html += `<div class="card"><h1>${escapeHtml(f.title)}</h1>`;
    html += `<div class="two-col">`;
    html += `<div class="detail-field"><div class="field-label">Status</div><div class="field-value">${statusBadge(f.status)}</div></div>`;
    html += `<div class="detail-field"><div class="field-label">Severity</div><div class="field-value">${severityHtml(f.severity)}</div></div>`;
    html += `<div class="detail-field"><div class="field-label">Department</div><div class="field-value">${escapeHtml(f.department)}</div></div>`;
    html += `<div class="detail-field"><div class="field-label">Classification</div><div class="field-value">${clsBadge(f.classification)}</div></div>`;
    html += `<div class="detail-field"><div class="field-label">Assignee</div><div class="field-value">${escapeHtml(f.assignee)}</div></div>`;
    html += `<div class="detail-field"><div class="field-label">Date</div><div class="field-value">${formatDate(f.createdAt)}</div></div>`;
    html += `</div>`;
    if (f.details) {
      html += `<div class="detail-field" style="margin-top:16px;"><div class="field-label">Details</div><div class="field-value" style="line-height:1.6;">${escapeHtml(f.details)}</div></div>`;
    }
    if (f.details && f.details.startsWith('FLAG{')) {
      html += `<div style="background:#1b2838;color:#4fc3f7;font-family:'Courier New',monospace;padding:14px 18px;border-radius:4px;font-size:1em;margin-top:16px;word-break:break-all;">${escapeHtml(f.details)}</div>`;
    }
    html += '</div>';
    app.innerHTML = html;
  } catch (e) {
    if (e.status === 403) {
      const required = e.data && e.data.required || 'higher';
      const yourClearance = e.data && e.data.yourClearance || 'C1';
      app.innerHTML = `
        <div class="card" style="text-align:center;padding:40px;">
          <h1 style="color:#c62828;">403 Forbidden</h1>
          <p style="color:#78909c;margin:12px 0;">This finding requires ${escapeHtml(required)} clearance.</p>
          <p style="color:#78909c;font-size:0.85em;">Your clearance: ${escapeHtml(yourClearance)}</p>
          <a href="javascript:history.back()" class="btn btn-sm" style="margin-top:12px;">Go Back</a>
        </div>`;
    } else {
      app.innerHTML = `<div class="alert alert-error">${escapeHtml(e.message)}</div>`;
    }
  }
}

document.addEventListener('DOMContentLoaded', init);
