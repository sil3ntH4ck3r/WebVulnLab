const express = require('express');
const session = require('express-session');
const path = require('path');
const { parseRSQL } = require('./rsql');
const { users, findings } = require('./data');

const app = express();
const PORT = 80;

app.set('query parser', 'simple');
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, 'public')));

app.use(session({
  secret: 'rsqli-lab-secret-2026',
  resave: false,
  saveUninitialized: true,
  cookie: { httpOnly: true, maxAge: 24 * 60 * 60 * 1000 }
}));

function requireAuth(req, res, next) {
  if (!req.session.userId) {
    return res.status(401).json({ error: 'Authentication required' });
  }
  const user = users.find(u => u.id === req.session.userId);
  if (!user) {
    return res.status(401).json({ error: 'User not found' });
  }
  req.currentUser = user;
  next();
}

app.get('/api/users', (req, res) => {
  try {
    const filterParam = req.query['filter[users]'];
    const loginQuery = req.query.q;

    if (loginQuery) {
      const filterFn = parseRSQL(loginQuery);
      const matching = users.filter(filterFn);
      if (matching.length === 0) {
        return res.status(401).json({ error: 'No matching user' });
      }
      const user = matching[0];
      req.session.userId = user.id;
      return res.json({
        id: user.id, email: user.email, name: user.name,
        role: user.role, clearance: user.clearance,
      });
    }

    if (filterParam) {
      const filterFn = parseRSQL(filterParam);
      const matching = users.filter(filterFn).map(u => ({
        id: u.id, email: u.email, name: u.name,
        role: u.role, clearance: u.clearance
      }));
      return res.json({ total: matching.length, data: matching });
    }

    return res.status(403).json({ error: 'Forbidden' });
  } catch (err) {
    res.status(err.statusCode || 500).json({ error: err.message });
  }
});



app.get('/api/me', requireAuth, (req, res) => {
  res.json({
    id: req.currentUser.id, email: req.currentUser.email,
    name: req.currentUser.name, role: req.currentUser.role,
    clearance: req.currentUser.clearance
  });
});

app.post('/api/logout', (req, res) => {
  req.session.destroy();
  res.json({ message: 'Logged out' });
});

app.get('/api/findings', requireAuth, (req, res) => {
  try {
    const filterEntries = req.query['filter[results]'];

    if (filterEntries) {
      const filterFn = parseRSQL(filterEntries);
      const results = findings.filter(filterFn);
      return res.json({
        total: results.length,
        entities: results.map(s => sanitizeFinding(s, true)),
        filterApplied: filterEntries
      });
    }

    const rsqlQuery = req.query.q || '';
    const filterFn = parseRSQL(rsqlQuery);
    const allMatching = findings.filter(filterFn);
    const visible = allMatching.filter(f => {
      const level = parseInt(f.classification.substring(1));
      return level <= req.currentUser.clearance;
    });

    res.json({
      total: allMatching.length,
      results: visible.map(s => sanitizeFinding(s, false)),
      userClearance: req.currentUser.clearance
    });
  } catch (err) {
    res.status(err.statusCode || 500).json({ error: err.message });
  }
});

app.get('/api/findings/:id', requireAuth, (req, res) => {
  const finding = findings.find(f => f.id === req.params.id);
  if (!finding) {
    return res.status(404).json({ error: 'Finding not found' });
  }
  const level = parseInt(finding.classification.substring(1));
  if (level > req.currentUser.clearance) {
    return res.status(403).json({
      error: 'Insufficient clearance',
      required: finding.classification,
      yourClearance: `C${req.currentUser.clearance}`
    });
  }
  res.json(finding);
});

function sanitizeFinding(f, showDetails) {
  const base = {
    id: f.id, title: f.title, status: f.status,
    severity: f.severity, department: f.department,
    classification: f.classification, assignee: f.assignee,
    createdAt: f.createdAt
  };
  if (showDetails) {
    base.details = f.details;
  }
  return base;
}

app.get('*', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

app.listen(PORT, () => {
  console.log(`RSQL Lab running on port ${PORT}`);
});
