const express = require('express');
const bodyParser = require('body-parser');
const serialize = require('node-serialize');
const crypto = require('crypto');
const path = require('path');
const app = express();

app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());
app.use(express.static(path.join(__dirname, 'public')));

const algorithm = 'aes-256-cbc';
const key = crypto.createHash('sha256').update(String('sup3rs3cr3tp@$$w0rd')).digest('base64').substr(0, 32);

process.on('uncaughtException', (err) => {
  console.error('Excepción no capturada:', err);
});
  
process.on('unhandledRejection', (reason, promise) => {
  console.error('Rechazo no manejado en:', promise, 'razón:', reason);
});

// Configuración para servir archivos estáticos
app.use('/public', express.static('public'));

// Ruta principal
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

app.post('/encrypt', (req, res) => {
  const message = req.body.message || "";
  const iv = crypto.randomBytes(16);
  const cipher = crypto.createCipheriv(algorithm, key, iv);
  let encrypted = cipher.update(message, 'utf8', 'hex');
  encrypted += cipher.final('hex');
  
  const obj = { iv: iv.toString('hex'), msg: encrypted };
  const serialized = serialize.serialize(obj);

  res.json({
    success: true,
    data: serialized
  });
});

app.post('/decrypt', (req, res) => {
  const data = req.body.data || "";
  try {
    const obj = serialize.unserialize(data);
    const iv = Buffer.from(obj.iv, 'hex');
    
    const decipher = crypto.createDecipheriv(algorithm, key, iv);
    let decrypted = decipher.update(obj.msg, 'hex', 'utf8');
    decrypted += decipher.final('utf8');
    
    res.json({
      success: true,
      data: decrypted
    });
  } catch (err) {
    res.json({
      success: false,
      error: err.message
    });
  }
});

app.listen(80, () => {
  console.log('Escuchando en el puerto 80');
});