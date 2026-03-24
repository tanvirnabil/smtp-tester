<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SMTP Tester</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f0f2f5;color:#333;min-height:100vh}
  header{background:#1a1a2e;color:#fff;padding:16px 20px;display:flex;align-items:center;gap:12px}
  header h1{font-size:1.2rem;font-weight:600}
  header span{font-size:1.4rem}
  .tabs{display:flex;background:#fff;border-bottom:2px solid #e0e0e0;overflow-x:auto}
  .tab{padding:12px 20px;cursor:pointer;font-size:.9rem;font-weight:500;color:#666;white-space:nowrap;border-bottom:3px solid transparent;margin-bottom:-2px;transition:.2s}
  .tab.active{color:#1a1a2e;border-bottom-color:#1a1a2e}
  .tab:hover:not(.active){color:#333;background:#f9f9f9}
  .page{display:none;padding:20px;max-width:900px;margin:0 auto}
  .page.active{display:block}
  .card{background:#fff;border-radius:10px;padding:20px;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.08)}
  .card h3{font-size:1rem;margin-bottom:16px;color:#1a1a2e;border-bottom:1px solid #f0f0f0;padding-bottom:10px}
  label{display:block;font-size:.82rem;font-weight:600;color:#555;margin-bottom:4px;margin-top:12px}
  label:first-of-type{margin-top:0}
  input,select,textarea{width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:6px;font-size:.9rem;outline:none;transition:.2s}
  input:focus,select:focus,textarea:focus{border-color:#1a1a2e;box-shadow:0 0 0 3px rgba(26,26,46,.08)}
  textarea{resize:vertical;min-height:100px;font-family:monospace;font-size:.85rem}
  .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  @media(max-width:500px){.row{grid-template-columns:1fr}}
  .btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border:none;border-radius:6px;font-size:.88rem;font-weight:600;cursor:pointer;transition:.2s}
  .btn-primary{background:#1a1a2e;color:#fff}
  .btn-primary:hover{background:#2d2d4e}
  .btn-danger{background:#e74c3c;color:#fff}
  .btn-danger:hover{background:#c0392b}
  .btn-sm{padding:5px 12px;font-size:.8rem}
  .btn-outline{background:#fff;border:1px solid #ddd;color:#333}
  .btn-outline:hover{background:#f5f5f5}
  .mt{margin-top:14px}
  .alert{padding:12px 16px;border-radius:8px;font-size:.88rem;margin-top:12px;display:none}
  .alert.show{display:flex;align-items:flex-start;gap:10px}
  .alert-success{background:#eafaf1;border:1px solid #a9dfbf;color:#1e8449}
  .alert-error{background:#fdf2f2;border:1px solid #f5c6cb;color:#c0392b}
  .alert-icon{font-size:1.1rem;flex-shrink:0}
  table{width:100%;border-collapse:collapse;font-size:.85rem}
  th{text-align:left;padding:8px 10px;background:#f8f8f8;color:#555;font-weight:600;border-bottom:2px solid #eee}
  td{padding:8px 10px;border-bottom:1px solid #f0f0f0;vertical-align:middle}
  tr:last-child td{border-bottom:none}
  .badge{display:inline-block;padding:2px 8px;border-radius:20px;font-size:.75rem;font-weight:600}
  .badge-success{background:#eafaf1;color:#1e8449}
  .badge-error{background:#fdf2f2;color:#c0392b}
  .badge-default{background:#f0f0f0;color:#666}
  .empty{text-align:center;color:#aaa;padding:30px;font-size:.9rem}
  .spinner{display:none;width:16px;height:16px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin .6s linear infinite}
  @keyframes spin{to{transform:rotate(360deg)}}
  .loading .spinner{display:inline-block}
  .loading .btn-text{display:none}
  .tag{font-size:.75rem;background:#e8eaf6;color:#3949ab;padding:2px 7px;border-radius:4px}
  .error-detail{font-size:.78rem;color:#888;margin-top:3px;word-break:break-word}
  .pass-wrap{position:relative}
  .pass-wrap input{padding-right:40px}
  .pass-toggle{position:absolute;right:10px;top:50%;transform:translateY(-50%);cursor:pointer;color:#888;font-size:.85rem;user-select:none}
</style>
</head>
<body>

<header>
  <span>📧</span>
  <h1>SMTP Tester</h1>
</header>

<div class="tabs">
  <div class="tab active" onclick="showTab('send')">Send Test</div>
  <div class="tab" onclick="showTab('configs')">SMTP Configs</div>
  <div class="tab" onclick="showTab('templates')">Templates</div>
  <div class="tab" onclick="showTab('logs')">Send Logs</div>
</div>

<!-- SEND TEST -->
<div id="tab-send" class="page active">
  <div class="card">
    <h3>Send Test Email</h3>
    <label>SMTP Config</label>
    <select id="send-config">
      <option value="">— Select SMTP Config —</option>
    </select>
    <label>To Address</label>
    <input type="email" id="send-to" placeholder="recipient@example.com">
    <label>Template (optional)</label>
    <select id="send-template" onchange="loadTemplate()">
      <option value="">— Select Template —</option>
    </select>
    <label>Subject</label>
    <input type="text" id="send-subject" placeholder="Test email subject">
    <label>Body</label>
    <textarea id="send-body" rows="6" placeholder="Email body (HTML supported)"></textarea>
    <div class="mt">
      <button class="btn btn-primary" id="send-btn" onclick="sendMail()">
        <span class="spinner"></span>
        <span class="btn-text">Send Email</span>
      </button>
    </div>
    <div class="alert" id="send-result"></div>
  </div>
</div>

<!-- SMTP CONFIGS -->
<div id="tab-configs" class="page">
  <div class="card">
    <h3 id="cfg-form-title">Add SMTP Config</h3>
    <input type="hidden" id="cfg-edit-id" value="">
    <label>Config Name</label>
    <input type="text" id="cfg-name" placeholder="e.g. Gmail, SendGrid, Office365">
    <div class="row">
      <div>
        <label>SMTP Host</label>
        <input type="text" id="cfg-host" placeholder="smtp.gmail.com">
      </div>
      <div>
        <label>Port</label>
        <input type="number" id="cfg-port" placeholder="587">
      </div>
    </div>
    <label>Encryption</label>
    <select id="cfg-enc">
      <option value="tls">TLS (STARTTLS) — Port 587</option>
      <option value="ssl">SSL — Port 465</option>
      <option value="none">None — Port 25</option>
    </select>
    <label>Username / Email</label>
    <input type="text" id="cfg-user" placeholder="you@example.com">
    <label>Password</label>
    <div class="pass-wrap">
      <input type="password" id="cfg-pass" placeholder="SMTP password or app password">
      <span class="pass-toggle" onclick="togglePass('cfg-pass',this)">Show</span>
    </div>
    <div class="row">
      <div>
        <label>From Email <span style="font-weight:400;color:#aaa">(optional, defaults to username)</span></label>
        <input type="text" id="cfg-from-email" placeholder="noreply@yourdomain.com">
      </div>
      <div>
        <label>From Name <span style="font-weight:400;color:#aaa">(optional)</span></label>
        <input type="text" id="cfg-from-name" placeholder="My Company">
      </div>
    </div>
    <div class="mt" style="display:flex;gap:10px;flex-wrap:wrap">
      <button class="btn btn-primary" id="cfg-save-btn" onclick="saveConfig()">Save Config</button>
      <button class="btn btn-outline" id="cfg-cancel-btn" style="display:none" onclick="cancelEditConfig()">Cancel</button>
    </div>
    <div class="alert" id="cfg-result"></div>
  </div>
  <div class="card">
    <h3>Saved Configs</h3>
    <div id="configs-list"><div class="empty">No configs saved yet.</div></div>
  </div>
</div>

<!-- TEMPLATES -->
<div id="tab-templates" class="page">
  <div class="card">
    <h3>Add Template</h3>
    <label>Template Name</label>
    <input type="text" id="tpl-name" placeholder="My Template">
    <label>Subject</label>
    <input type="text" id="tpl-subject" placeholder="Email subject">
    <label>Body (HTML supported)</label>
    <textarea id="tpl-body" rows="6" placeholder="Email body..."></textarea>
    <div class="mt">
      <button class="btn btn-primary" onclick="saveTemplate()">Save Template</button>
    </div>
    <div class="alert" id="tpl-result"></div>
  </div>
  <div class="card">
    <h3>Templates</h3>
    <div id="templates-list"><div class="empty">Loading...</div></div>
  </div>
</div>

<!-- LOGS -->
<div id="tab-logs" class="page">
  <div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
      <h3 style="border:none;padding:0;margin:0">Send Logs (last 50)</h3>
      <button class="btn btn-outline btn-sm" onclick="clearLogs()">Clear Logs</button>
    </div>
    <div id="logs-list" style="margin-top:14px"><div class="empty">Loading...</div></div>
  </div>
</div>

<!-- View Config Modal -->
<div id="view-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center;padding:16px">
  <div style="background:#fff;border-radius:12px;width:100%;max-width:480px;box-shadow:0 8px 32px rgba(0,0,0,.2)">
    <div style="padding:16px 20px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center">
      <strong id="modal-title" style="font-size:1rem"></strong>
      <span onclick="closeModal()" style="cursor:pointer;font-size:1.3rem;color:#888;line-height:1">&times;</span>
    </div>
    <div style="padding:20px">
      <pre id="modal-body" style="background:#f6f8fa;border:1px solid #e0e0e0;border-radius:8px;padding:14px;font-size:.82rem;line-height:1.7;overflow-x:auto;white-space:pre-wrap;word-break:break-all;margin:0"></pre>
      <button class="btn btn-primary mt" onclick="copyConfig()" id="copy-btn" style="width:100%">📋 Copy to Clipboard</button>
    </div>
  </div>
</div>

<script>
function showTab(name) {
  document.querySelectorAll('.tab').forEach((t,i)=>t.classList.toggle('active', ['send','configs','templates','logs'][i]===name));
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  document.getElementById('tab-'+name).classList.add('active');
  if(name==='configs') loadConfigs();
  if(name==='templates') loadTemplates();
  if(name==='logs') loadLogs();
}

function api(data, cb) {
  const fd = new FormData();
  Object.entries(data).forEach(([k,v])=>fd.append(k,v));
  fetch('send.php',{method:'POST',body:fd}).then(r=>r.json()).then(cb).catch(e=>cb({status:'error',message:e.message}));
}

function showAlert(id, status, msg) {
  const el = document.getElementById(id);
  el.className = 'alert show alert-'+(status==='success'?'success':'error');
  el.innerHTML = '<span class="alert-icon">'+(status==='success'?'✅':'❌')+'</span><span>'+msg+'</span>';
}

function togglePass(id, el) {
  const inp = document.getElementById(id);
  inp.type = inp.type==='password'?'text':'password';
  el.textContent = inp.type==='password'?'Show':'Hide';
}

// ---- SEND ----
function sendMail() {
  const btn = document.getElementById('send-btn');
  btn.classList.add('loading');
  api({
    action:'send',
    config_id: document.getElementById('send-config').value,
    to: document.getElementById('send-to').value,
    subject: document.getElementById('send-subject').value,
    body: document.getElementById('send-body').value
  }, r => {
    btn.classList.remove('loading');
    showAlert('send-result', r.status, r.message);
  });
}

function loadTemplate() {
  const id = document.getElementById('send-template').value;
  if(!id) return;
  api({action:'get_template',id}, t => {
    if(t.subject) document.getElementById('send-subject').value = t.subject;
    if(t.body) document.getElementById('send-body').value = t.body;
  });
}

function populateSendDropdowns() {
  api({action:'get_configs'}, rows => {
    const s = document.getElementById('send-config');
    const cur = s.value;
    s.innerHTML = '<option value="">— Select SMTP Config —</option>';
    rows.forEach(r => s.innerHTML += `<option value="${r.id}">${r.name} (${r.host}:${r.port})</option>`);
    if(cur) s.value = cur;
  });
  api({action:'get_templates'}, rows => {
    const s = document.getElementById('send-template');
    s.innerHTML = '<option value="">— Select Template —</option>';
    rows.forEach(r => s.innerHTML += `<option value="${r.id}">${r.name}${r.is_default=='1'?' ⭐':''}</option>`);
  });
}

// ---- CONFIGS ----
function viewConfig(id) {
  api({action:'get_config',id}, r => {
    if(!r.id) return;
    document.getElementById('modal-title').textContent = '🔐 ' + r.name;
    document.getElementById('modal-body').textContent =
      `Host:       ${r.host}\nPort:       ${r.port}\nEncryption: ${r.encryption.toUpperCase()}\nUsername:   ${r.username}\nPassword:   ${r.password}\nFrom Email: ${r.from_email || '(same as username)'}\nFrom Name:  ${r.from_name || '(not set)'}`;
    document.getElementById('copy-btn').textContent = '📋 Copy to Clipboard';
    const m = document.getElementById('view-modal');
    m.style.display = 'flex';
  });
}
function closeModal() { document.getElementById('view-modal').style.display = 'none'; }
function copyConfig() {
  const text = document.getElementById('modal-body').textContent;
  const btn = document.getElementById('copy-btn');
  try {
    if(navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text).then(()=>{ btn.textContent='✅ Copied!'; setTimeout(()=>btn.textContent='📋 Copy to Clipboard',2000); });
    } else {
      const ta = document.createElement('textarea');
      ta.value = text; ta.style.position='fixed'; ta.style.opacity='0';
      document.body.appendChild(ta); ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
      btn.textContent='✅ Copied!'; setTimeout(()=>btn.textContent='📋 Copy to Clipboard',2000);
    }
  } catch(e) { btn.textContent='❌ Copy failed'; }
}
document.getElementById('view-modal').addEventListener('click', function(e){ if(e.target===this) closeModal(); });

function editConfig(id) {
  api({action:'get_config',id}, r => {
    if(!r.id) return;
    document.getElementById('cfg-edit-id').value = r.id;
    document.getElementById('cfg-name').value = r.name;
    document.getElementById('cfg-host').value = r.host;
    document.getElementById('cfg-port').value = r.port;
    document.getElementById('cfg-enc').value = r.encryption;
    document.getElementById('cfg-user').value = r.username;
    document.getElementById('cfg-pass').value = r.password;
    document.getElementById('cfg-from-email').value = r.from_email || '';
    document.getElementById('cfg-from-name').value = r.from_name || '';
    document.getElementById('cfg-form-title').textContent = 'Edit SMTP Config';
    document.getElementById('cfg-save-btn').textContent = 'Update Config';
    document.getElementById('cfg-cancel-btn').style.display = 'inline-flex';
    document.getElementById('cfg-form-title').scrollIntoView({behavior:'smooth'});
  });
}

function cancelEditConfig() {
  document.getElementById('cfg-edit-id').value = '';
  ['cfg-name','cfg-host','cfg-port','cfg-user','cfg-pass','cfg-from-email','cfg-from-name'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('cfg-form-title').textContent = 'Add SMTP Config';
  document.getElementById('cfg-save-btn').textContent = 'Save Config';
  document.getElementById('cfg-cancel-btn').style.display = 'none';
}

function saveConfig() {
  const fields = ['cfg-name','cfg-host','cfg-port','cfg-user','cfg-pass'];
  for(const f of fields) if(!document.getElementById(f).value.trim()){showAlert('cfg-result','error','All fields are required.');return;}
  const editId = document.getElementById('cfg-edit-id').value;
  const data = {
    action: editId ? 'update_config' : 'save_config',
    name: document.getElementById('cfg-name').value,
    host: document.getElementById('cfg-host').value,
    port: document.getElementById('cfg-port').value,
    encryption: document.getElementById('cfg-enc').value,
    username: document.getElementById('cfg-user').value,
    password: document.getElementById('cfg-pass').value,
    from_email: document.getElementById('cfg-from-email').value,
    from_name: document.getElementById('cfg-from-name').value
  };
  if(editId) data.id = editId;
  api(data, r => {
    if(r.status==='success'){
      cancelEditConfig();
      showAlert('cfg-result','success', editId ? 'Config updated.' : 'Config saved.');
      loadConfigs();
      populateSendDropdowns();
    } else showAlert('cfg-result','error',r.message);
  });
}

function loadConfigs() {
  api({action:'get_configs'}, rows => {
    const el = document.getElementById('configs-list');
    if(!rows.length){el.innerHTML='<div class="empty">No configs saved yet.</div>';return;}
    el.innerHTML = '<table><thead><tr><th>Name</th><th>Host</th><th>Port</th><th>Enc</th><th>User</th><th></th></tr></thead><tbody>'
      + rows.map(r=>`<tr>
          <td><strong>${r.name}</strong></td>
          <td>${r.host}</td>
          <td>${r.port}</td>
          <td><span class="badge badge-default">${r.encryption.toUpperCase()}</span></td>
          <td style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${r.username}</td>
          <td><button class="btn btn-outline btn-sm" onclick="viewConfig(${r.id})" style="margin-right:4px">View</button><button class="btn btn-outline btn-sm" onclick="editConfig(${r.id})" style="margin-right:4px">Edit</button><button class="btn btn-danger btn-sm" onclick="deleteConfig(${r.id})">Delete</button></td>
        </tr>`).join('')
      + '</tbody></table>';
  });
}

function deleteConfig(id) {
  if(!confirm('Delete this SMTP config?')) return;
  api({action:'delete_config',id}, r => { if(r.status==='success'){loadConfigs();populateSendDropdowns();} });
}

// ---- TEMPLATES ----
function saveTemplate() {
  if(!document.getElementById('tpl-name').value.trim()||!document.getElementById('tpl-subject').value.trim()||!document.getElementById('tpl-body').value.trim()){
    showAlert('tpl-result','error','All fields are required.');return;
  }
  api({
    action:'save_template',
    name: document.getElementById('tpl-name').value,
    subject: document.getElementById('tpl-subject').value,
    body: document.getElementById('tpl-body').value
  }, r => {
    if(r.status==='success'){
      ['tpl-name','tpl-subject','tpl-body'].forEach(id=>document.getElementById(id).value='');
      showAlert('tpl-result','success','Template saved.');
      loadTemplates();
      populateSendDropdowns();
    } else showAlert('tpl-result','error',r.message);
  });
}

function loadTemplates() {
  api({action:'get_templates'}, rows => {
    const el = document.getElementById('templates-list');
    if(!rows.length){el.innerHTML='<div class="empty">No templates yet.</div>';return;}
    el.innerHTML = '<table><thead><tr><th>Name</th><th>Subject</th><th></th></tr></thead><tbody>'
      + rows.map(r=>`<tr>
          <td><strong>${r.name}</strong> ${r.is_default=='1'?'<span class="tag">default</span>':''}</td>
          <td>${r.subject}</td>
          <td>${r.is_default=='1'?'':'<button class="btn btn-danger btn-sm" onclick="deleteTemplate('+r.id+')">Delete</button>'}</td>
        </tr>`).join('')
      + '</tbody></table>';
  });
}

function deleteTemplate(id) {
  if(!confirm('Delete this template?')) return;
  api({action:'delete_template',id}, r => { if(r.status==='success'){loadTemplates();populateSendDropdowns();} });
}

// ---- LOGS ----
function loadLogs() {
  api({action:'get_logs'}, rows => {
    const el = document.getElementById('logs-list');
    if(!rows.length){el.innerHTML='<div class="empty">No logs yet.</div>';return;}
    el.innerHTML = '<table><thead><tr><th>Time</th><th>Config</th><th>To</th><th>Subject</th><th>Status</th></tr></thead><tbody>'
      + rows.map(r=>`<tr>
          <td style="white-space:nowrap;font-size:.78rem">${r.sent_at}</td>
          <td>${r.config_name||'—'}</td>
          <td>${r.to_address}</td>
          <td>${r.subject}</td>
          <td>
            <span class="badge badge-${r.status==='delivered'?'success':'error'}">${r.status}</span>
            ${r.error_message?`<div class="error-detail">${r.error_message}</div>`:''}
          </td>
        </tr>`).join('')
      + '</tbody></table>';
  });
}

function clearLogs() {
  if(!confirm('Clear all logs?')) return;
  api({action:'clear_logs'}, ()=>loadLogs());
}

// Init
populateSendDropdowns();
</script>
</body>
</html>
