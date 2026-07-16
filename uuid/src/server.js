const express = require('express');
const session = require('express-session');
const { v1: uuidv1 } = require('uuid');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 80;

app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, 'public')));
app.use(session({
  secret: 'fd8a2b9c3e7d1f4a6b0c5d8e3f2a7b9c1d4e6f',
  resave: false,
  saveUninitialized: true,
  cookie: { httpOnly: true, sameSite: 'lax' }
}));

const users = {};
const tokens = {};
const mailboxes = {};

users['admin@uuid.local'] = {
  password: 'e8d4a6f2c1b9a7f3e5d0c8b6a4f2e7d1c9b0a3f5'
};

function auth(req, res, next) {
  if (!req.session.user) return res.redirect('/login');
  next();
}

function guest(req, res, next) {
  if (req.session.user) return res.redirect('/dashboard');
  next();
}

function h(s) {
  if (!s) return '';
  return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function page(title, body, user) {
  const nav = user
    ? `<span class="nav-user">${h(user)}</span>
       <a href="/dashboard">Dashboard</a>
       <a href="/mailbox">Mailbox</a>
       <form action="/logout" method="POST" class="inline">
         <button type="submit" class="link-btn">Logout</button>
       </form>`
    : `<a href="/login">Sign In</a><a href="/register">Create Account</a>`;
  return `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>${h(title)} - SecureAuth</title>
<link rel="stylesheet" href="/style.css">
</head>
<body>
<header class="header">
<div class="header-inner">
<a href="/" class="logo">Secure<span>Auth</span></a>
<nav class="nav">${nav}</nav>
</div>
</header>
<main class="main">
<div class="card">${body}</div>
</main>
</body>
</html>`;
}

app.get('/', (req, res) => {
  res.redirect('/login');
});

app.get('/forgot', (req, res) => {
  res.send(page('Reset Password', `
    <h1>Forgot your password?</h1>
    <p>Enter your email address and we will send you a link to reset your password.</p>
    <form id="rf" class="form">
      <div class="field">
        <label for="e">Email address</label>
        <input type="email" id="e" name="email" placeholder="you@example.com" required autocomplete="email">
      </div>
      <button type="submit" class="btn">Send Reset Link</button>
    </form>
    <div id="rm" class="message" style="display:none"></div>
    <div class="links"><a href="/login">Back to Sign In</a></div>
    <script>
    document.getElementById('rf').addEventListener('submit',async function(e){
      e.preventDefault();
      const email=document.getElementById('e').value;
      const btn=this.querySelector('button');
      const msg=document.getElementById('rm');
      btn.disabled=true; btn.textContent='Sending...';
      try {
        const r=await fetch('/reset-password',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({email})});
        const d=await r.json();
        msg.textContent=d.message; msg.style.display='block';
        msg.className='message success';
      } catch(e){
        msg.textContent='An error occurred. Please try again.'; msg.style.display='block';
        msg.className='message error';
      }
      btn.disabled=false; btn.textContent='Send Reset Link';
    });
    </script>`, req.session.user));
});

app.get('/register', guest, (req, res) => {
  res.send(page('Create Account', `
    <h1>Create Account</h1>
    <p>Register to access your account.</p>
    <form action="/register" method="POST" class="form">
      <div class="field">
        <label for="e">Email address</label>
        <input type="email" id="e" name="email" placeholder="you@example.com" required autocomplete="email">
      </div>
      <div class="field">
        <label for="p">Password</label>
        <input type="password" id="p" name="password" placeholder="At least 6 characters" required autocomplete="new-password" minlength="6">
      </div>
      <div class="field">
        <label for="c">Confirm password</label>
        <input type="password" id="c" name="confirm" placeholder="Confirm your password" required autocomplete="new-password">
      </div>
      <button type="submit" class="btn">Create Account</button>
    </form>
    <div class="links">Already have an account? <a href="/login">Sign In</a></div>`, null));
});

app.post('/register', guest, (req, res) => {
  const { email, password, confirm } = req.body;
  if (!email || !password || !confirm) return res.send(page('Create Account', `
    <div class="message error">All fields are required.</div>
    <a href="/register" class="btn" style="margin-top:16px">Try Again</a>`, null));
  if (password !== confirm) return res.send(page('Create Account', `
    <div class="message error">Passwords do not match.</div>
    <a href="/register" class="btn" style="margin-top:16px">Try Again</a>`, null));
  if (password.length < 6) return res.send(page('Create Account', `
    <div class="message error">Password must be at least 6 characters.</div>
    <a href="/register" class="btn" style="margin-top:16px">Try Again</a>`, null));
  if (users[email]) return res.send(page('Create Account', `
    <div class="message error">An account with this email already exists.</div>
    <a href="/register" class="btn" style="margin-top:16px">Try Again</a>`, null));
  users[email] = { password };
  res.redirect('/login');
});

app.get('/login', guest, (req, res) => {
  res.send(page('Sign In', `
    <h1>Sign In</h1>
    <p>Welcome back. Enter your credentials to access your account.</p>
    <form action="/login" method="POST" class="form">
      <div class="field">
        <label for="e">Email address</label>
        <input type="email" id="e" name="email" placeholder="you@example.com" required autocomplete="email">
      </div>
      <div class="field">
        <label for="p">Password</label>
        <input type="password" id="p" name="password" placeholder="Enter your password" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn">Sign In</button>
    </form>
    <div class="links"><a href="/forgot">Forgot your password?</a> <span style="margin:0 8px;color:#ccc">|</span> <a href="/register">Create Account</a></div>
    <div style="margin-top:24px;padding-top:16px;border-top:1px solid #e5e7eb;text-align:center;font-size:13px;color:#9ca3af">If any user has any problem, write an email to <a href="mailto:admin@uuid.com">admin@uuid.local</a></div>`, null));
});

app.post('/login', guest, (req, res) => {
  const { email, password } = req.body;
  const user = users[email];
  if (!user || user.password !== password) return res.send(page('Sign In', `
    <div class="message error">Invalid email or password.</div>
    <a href="/login" class="btn" style="margin-top:16px">Try Again</a>`, null));
  req.session.user = email;
  res.redirect('/dashboard');
});

app.post('/logout', (req, res) => {
  req.session.destroy();
  res.redirect('/login');
});

app.get('/dashboard', auth, (req, res) => {
  const email = req.session.user;
  const admin = email === 'admin@uuid.local';
  res.send(page('Dashboard', `
    <h1>Welcome${admin ? ', Administrator' : ''}</h1>
    <div class="info">
      <div class="info-row"><span class="info-label">Email</span><span class="info-value">${h(email)}</span></div>
      <div class="info-row"><span class="info-label">Role</span><span class="info-value">${admin ? 'Administrator' : 'User'}</span></div>
    </div>
    <div class="actions"><a href="/mailbox" class="btn btn-secondary">View Mailbox</a></div>`, email));
});

app.get('/mailbox', auth, (req, res) => {
  const email = req.session.user;
  const mails = mailboxes[email] || [];
  if (!mails.length) return res.send(page('Mailbox', `
    <h1>Mailbox</h1>
    <div class="empty"><p>No messages yet.</p></div>`, email));
  const items = mails.map(m => `
    <div class="mail-item">
      <div class="mail-from">SecureAuth &lt;noreply@secureauth.com&gt;</div>
      <div class="mail-subject">${h(m.subject)}</div>
      <div class="mail-body">We received a request to reset your password.<br><br>
      <a href="/reset/${h(m.token)}" class="reset-link">https://uuid.local/reset/${h(m.token)}</a><br><br>
      If you did not request this, please ignore this email.</div>
      <div class="mail-time">${h(m.receivedAt)}</div>
    </div>`).join('');
  res.send(page('Mailbox', `<h1>Mailbox</h1><div class="mail-list">${items}</div>`, email));
});

app.post('/reset-password', (req, res) => {
  const { email } = req.body;
  if (!email) return res.status(400).json({ error: 'Email is required' });
  const token = uuidv1();
  tokens[token] = { email, used: false, createdAt: new Date() };
  if (!mailboxes[email]) mailboxes[email] = [];
  mailboxes[email].push({ token, subject: 'Password Reset Request', receivedAt: new Date().toISOString() });
  res.json({ message: 'If an account with this email exists, a reset link has been sent.' });
});

app.get('/reset/:token', (req, res) => {
  const { token } = req.params;
  const d = tokens[token];
  if (!d) return res.status(404).send(page('Invalid Link', `
    <h1>Invalid reset link</h1>
    <p>This password reset link is invalid or has expired.</p>
    <a href="/forgot" class="btn">Request New Link</a>`, req.session.user));
  if (d.used) return res.status(400).send(page('Link Expired', `
    <h1>Link already used</h1>
    <p>This password reset link has already been used.</p>
    <a href="/forgot" class="btn">Request New Link</a>`, req.session.user));
  res.send(page('Reset Password', `
    <h1>Reset your password</h1>
    <p>Enter your new password below.</p>
    <form action="/reset/${h(token)}" method="POST" class="form">
      <div class="field">
        <label for="p">New password</label>
        <input type="password" id="p" name="password" placeholder="At least 6 characters" required minlength="6">
      </div>
      <div class="field">
        <label for="c">Confirm new password</label>
        <input type="password" id="c" name="confirm" placeholder="Confirm your new password" required>
      </div>
      <button type="submit" class="btn">Reset Password</button>
    </form>`, req.session.user));
});

app.post('/reset/:token', (req, res) => {
  const { token } = req.params;
  const { password, confirm } = req.body;
  const d = tokens[token];
  if (!d) return res.status(404).json({ error: 'Invalid reset link' });
  if (d.used) return res.status(400).json({ error: 'This link has already been used' });
  if (!password || password.length < 6) return res.status(400).send(page('Reset Password', `
    <div class="message error">Password must be at least 6 characters.</div>
    <a href="/reset/${h(token)}" class="btn" style="margin-top:16px">Try Again</a>`, req.session.user));
  if (password !== confirm) return res.status(400).send(page('Reset Password', `
    <div class="message error">Passwords do not match.</div>
    <a href="/reset/${h(token)}" class="btn" style="margin-top:16px">Try Again</a>`, req.session.user));
  d.used = true;
  if (users[d.email]) users[d.email].password = password;
  res.send(page('Password Reset', `
    <h1>Password reset successful</h1>
    <p>Your password has been updated. You can now sign in with your new password.</p>
    <a href="/login" class="btn">Sign In</a>`, req.session.user));
});

app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
