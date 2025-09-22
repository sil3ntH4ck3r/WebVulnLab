const express = require('express');
const bodyParser = require('body-parser');
const path = require('path');
const { fork, exec } = require('child_process');
const app = express();

app.use(bodyParser.json());
app.use(express.static('public'));

const containsProtoKey = (obj) => {
  const stack = [obj];
  while (stack.length) {
    const current = stack.pop();
    if (current && typeof current === 'object') {
      if (Object.prototype.hasOwnProperty.call(current, '__proto__')) {
        return true;
      }
      for (const key of Object.keys(current)) {
        stack.push(current[key]);
      }
    }
  }
  return false;
};

app.post('/submit-password', (req, res) => {
  const password = req.body.password;
  var validpassword = {"type":"password"};

  if (password === "!UOx$aSwAd65BcTEyeVa!%PQ$FWj7JE3wvAp#8Ee3v4d$zOklH") {
    validpassword.valid = true;
  }

  var merge = function(target, source) {
    const _merge = (t, s, depth) => {
      if (depth > 20 || !s) return t;
      for (const attr of Object.keys(s)) {
        try {
          const ta = t ? t[attr] : undefined;
          const sa = s[attr];
          if ((typeof ta === 'object' || typeof ta === 'function') && ta !== null && typeof sa === 'object' && sa !== null) {
            _merge(ta, sa, depth + 1);
          } else {
            t[attr] = sa;
          }
        } catch (_) {
          t[attr] = s[attr];
        }
      }
      return t;
    };
    return _merge(target, source, 0);
  };

  if (containsProtoKey(req.body)) {
    return res.status(400).json({ message: 'Petición bloqueada' });
  }

  merge({ "PasswordCharacters": "50" }, req.body);

  if (validpassword.valid) {
    res.json({ message: 'Contraseña correcta', valid: !!validpassword.valid });
  } else {
    res.json({ message: 'Contraseña incorrecta', valid: !!validpassword.valid });
  }
});

app.get('/protected-content', (req, res) => {
    const content = `
      <h2>Contenido protegido</h2>
      <p>Este es el contenido protegido que solo se muestra después de ingresar la contraseña correcta.</p>
    `;
    res.send(content);
});

app.post('/generate-report', (req, res) => {
  const options = {};

  var merge = function(target, source) {
    const _merge = (t, s, depth) => {
      if (depth > 20 || !s) return t;
      for (const attr of Object.keys(s)) {
        try {
          const ta = t ? t[attr] : undefined;
          const sa = s[attr];
          if ((typeof ta === 'object' || typeof ta === 'function') && ta !== null && typeof sa === 'object' && sa !== null) {
            _merge(ta, sa, depth + 1);
          } else {
            t[attr] = sa;
          }
        } catch (_) {
          t[attr] = s[attr];
        }
      }
      return t;
    };
    return _merge(target, source, 0);
  };

  try {
    merge(options, req.body);
    const scriptPath = path.join(__dirname, 'scripts', 'report-worker.js');
    const childOptions = {
      execArgv: Array.isArray(options.execArgv) ? options.execArgv : [],
      env: Object.assign({}, process.env, options.env || {})
    };
    if (childOptions.env && 'NODE_OPTIONS' in childOptions.env) {
      delete childOptions.env.NODE_OPTIONS;
    }

    const child = fork(scriptPath, [], childOptions);

    child.on('exit', (code, signal) => {
      res.json({ message: 'Informe generado, se puede consultar en /report.json', code, signal, optionsUsed: { provided: options, child: childOptions } });
    });

    child.on('error', (err) => {
      res.status(500).json({ message: 'Error generando informe', error: err.message, optionsUsed: { provided: options, child: childOptions } });
    });
  } catch (e) {
    res.status(500).json({ message: 'Error al procesar la solicitud', error: e.message });
  }
});

app.listen(80, () => {
  console.log('Laboratorio de Prototype Pollution en el puerto 80');
});