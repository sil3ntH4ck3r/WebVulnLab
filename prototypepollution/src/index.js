const express = require('express');
const bodyParser = require('body-parser');
const app = express();

app.use(bodyParser.json());
app.use(express.static('public'));

app.post('/submit-password', (req, res) => {
  const password = req.body.password;
  var validpassword = {"type":"password"};

  if (password === "!UOx$aSwAd65BcTEyeVa!%PQ$FWj7JE3wvAp#8Ee3v4d$zOklH") {
    validpassword.valid = true;
  }

  var merge = function(target, source) {
    for(var attr in source) {
        if(typeof(target[attr]) === "object" && typeof(source[attr]) === "object") {
            merge(target[attr], source[attr]);
        } else {
            target[attr] = source[attr];
        }
    }
    return target;
  };

  merge({ "PasswordCharacters": "50" }, req.body);

  if (validpassword.valid) {
    res.json({ message: 'Contraseña correcta' });
  } else {
    res.json({ message: 'Contraseña incorrecta' });
  }
});

app.get('/protected-content', (req, res) => {
    const content = `
      <h2>Contenido protegido</h2>
      <p>Este es el contenido protegido que solo se muestra después de ingresar la contraseña correcta.</p>
    `;
    res.send(content);
});

app.listen(3000, () => {
  console.log('Laboratorio de Prototype Pollution en el puerto 3000');
});