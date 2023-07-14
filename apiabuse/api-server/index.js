const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const jwt = require('jsonwebtoken');
const nodemailer = require('nodemailer');
const { Pool } = require('pg');

const pool = new Pool({
  user: 'admin',
  host: 'app-db',
  database: 'apiabuse_database',
  password: 'admin',
  port: 5432,
});

const app = express();
app.use(cors());
app.use(bodyParser.json());

const mailTransporter = nodemailer.createTransport({
  host: 'mailhog-container', // La dirección IP de tu servidor MailHog
  port: 1025, // El puerto de tu servidor MailHog
});

app.post('/api/v2/register', async (req, res) => {
  const { email, password } = req.body;
  try {
    const result = await pool.query('INSERT INTO users (email, password) VALUES ($1, $2) RETURNING id', [email, password]);

    // Enviar correo electrónico de bienvenida
    const mailOptions = {
      from: 'noreply@apiabuse.com',
      to: email,
      subject: '¡Bienvenido a nuestra aplicación!',
      html: `
        <h1>¡Bienvenido a nuestra aplicación!</h1>
        <p>Gracias por registrarte en nuestra aplicación. Esperamos que disfrutes de todas las funcionalidades que ofrecemos.</p>
        <p>No dudes en ponerte en contacto con nosotros si tienes alguna pregunta o problema.</p>
        <p>¡Que tengas un buen día!</p>
      `,
    };

    await mailTransporter.sendMail(mailOptions);

    res.status(201).json({ userId: result.rows[0].id });
  } catch (error) {
    if (error.constraint === 'users_email_key') {
      // Error causado por la violación de restricción única en la columna email
      res.status(409).json({ error: 'El correo electrónico ya está registrado' });
    } else {
      res.status(500).json({ error: error.message });
    }
  }
});

app.post('/api/v2/login', async (req, res) => {
  const { email, password } = req.body;
  try {
    const result = await pool.query('SELECT * FROM users WHERE email = $1 AND password = $2', [email, password]);
    if (result.rows.length > 0) {
      // El inicio de sesión es exitoso
      const user = result.rows[0];
      const token = jwt.sign({ userId: user.id, email: user.email }, 'secreto', { expiresIn: '1h' }); // Genera el token JWT
      res.status(200).json({ message: 'Inicio de sesión exitoso', token });
    } else {
      // Credenciales inválidas
      res.status(401).json({ error: 'Credenciales inválidas' });
    }
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.get('/api/v2/user', async (req, res) => {
  const token = req.headers.authorization.split(' ')[1]; // Obtén el token del encabezado de autorización
  try {
    const decoded = jwt.verify(token, 'secreto'); // Verifica y decodifica el token JWT

    // Consulta la base de datos para obtener el saldo del usuario con el correo electrónico del token
    const userResult = await pool.query('SELECT * FROM users WHERE email = $1', [decoded.email]);
    const user = userResult.rows[0];

    // Devuelve el contenido descifrado del JWT y el saldo del usuario en la respuesta
    res.status(200).json({ userId: decoded.userId, email: decoded.email, balance: user.balance });
  } catch (error) {
    res.status(401).json({ error: 'Token inválido' });
  }
})

app.post('/api/v2/send-verification-email', async (req, res) => {
  const { email } = req.body;
  try {
    const result = await pool.query('SELECT * FROM users WHERE email = $1', [email]);
    if (result.rows.length > 0) {
      // La dirección de correo electrónico ya está registrada
      const code = Math.floor(1000 + Math.random() * 9000); // Genera un código de 4 dígitos
      await pool.query('INSERT INTO verification_codes (email, code) VALUES ($1, $2)', [email, code]); // Almacena el código en la base de datos
      const mailOptions = {
        from: 'noreply@apiabuse.com',
        to: email,
        subject: 'Código de verificación de cambio de contraseña',
        html: `
          <h1>Código de verificación de cambio de contraseña</h1>
          <p>Introduce el siguiente código de 4 dígitos en la página de cambio de contraseña:</p>
          <h2>${code}</h2>
        `,
      };
      await mailTransporter.sendMail(mailOptions); // Envía el correo electrónico de verificación
      res.status(200).json({ message: 'Correo electrónico de verificación enviado', email });
    } else {
      // La dirección de correo electrónico no está registrada
      res.status(404).json({ error: 'El correo electrónico no está registrado' });
    }
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.post('/api/v2/change-password', async (req, res) => {
  const { pin, newPassword, email } = req.body;
  try {
    const userResult = await pool.query('SELECT * FROM users WHERE email = $1', [email]);
    const user = userResult.rows[0];
    if (user.blocked) {
      // La cuenta está bloqueada
      res.status(401).json({ error: 'La cuenta está bloqueada' });
      return;
    }
    const codeResult = await pool.query('SELECT * FROM verification_codes WHERE email = $1 AND code = $2 AND expiration_time > NOW() - INTERVAL \'5 minutes\'', [email, pin]);
    if (codeResult.rows.length > 0) {
      const code = codeResult.rows[0];
      if (code.expiration_time < new Date()) {
        // El código de verificación ha caducado
        res.status(401).json({ error: 'El código de verificación ha caducado' });
        return;
      }
      // El código de verificación es válido
      await pool.query('UPDATE users SET password = $1 WHERE email = $2', [newPassword, email]); // Actualiza la contraseña en la base de datos
      await pool.query('DELETE FROM verification_codes WHERE email = $1', [email]); // Elimina el código de verificación de la base de datos
      res.status(200).json({ message: 'Contraseña cambiada exitosamente' });
    } else {
      // El código de verificación es inválido
      const failedAttempts = user.failed_attempts + 1;
      await pool.query('UPDATE users SET failed_attempts = $1 WHERE email = $2', [failedAttempts, email]); // Actualiza el número de intentos fallidos en la base de datos
      if (failedAttempts >= 3) {
        // La cuenta está bloqueada después de 3 intentos fallidos
        await pool.query('UPDATE users SET blocked = true WHERE email = $1', [email]);
        res.status(401).json({ error: 'Demasiados intentos' });
      } else {
        res.status(401).json({ error: 'Código de verificación inválido' });
      }
    }
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.post('/api/v1/change-password', async (req, res) => {
  const { pin, newPassword, email } = req.body;
  try {
    const result = await pool.query('SELECT * FROM verification_codes WHERE email = $1 AND code = $2', [email, pin]);
    if (result.rows.length > 0) {
      // El código de verificación es válido
      await pool.query('UPDATE users SET password = $1 WHERE email = $2', [newPassword, email]); // Actualiza la contraseña en la base de datos
      await pool.query('DELETE FROM verification_codes WHERE email = $1', [email]); // Elimina el código de verificación de la base de datos
      res.status(200).json({ message: 'Contraseña cambiada exitosamente' });
    } else {
      // El código de verificación es inválido
      res.status(401).json({ error: 'Código de verificación inválido' });
    }
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.get('/api/v1/products', async (req, res) => {
  try {
    const result = await pool.query('SELECT * FROM products');
    res.status(200).json(result.rows);
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.post('/api/v1/products', async (req, res) => {
  const { name, description, price } = req.body;

  // Verifica si los campos requeridos se proporcionan correctamente
  if (!name || !description || !price) {
    res.status(400).json({ error: 'Los campos name, description y price son requeridos' });
    return;
  }

  try {
    // Inserta el nuevo producto en la tabla "products"
    const result = await pool.query('INSERT INTO products (name, description, price) VALUES ($1, $2, $3) RETURNING *', [name, description, price]);
    const newProduct = result.rows[0];

    res.status(201).json(newProduct);
  } catch (error) {
    console.error('Error al agregar un nuevo producto:', error);
    res.status(500).json({ error: error.message });
  }
});

app.post('/api/v1/purchase', async (req, res) => {
  const { productId, price, quantity } = req.body;
  const token = req.headers.authorization.split(' ')[1];

  try {
    // Decodifica el token JWT para obtener el correo electrónico del usuario
    const decoded = jwt.verify(token, 'secreto');
    const userEmail = decoded.email;

    // Consulta la base de datos para obtener el usuario con el correo electrónico extraído del token
    const userResult = await pool.query('SELECT * FROM users WHERE email = $1', [userEmail]);
    const user = userResult.rows[0];

    // Verifica si el saldo del usuario es suficiente para realizar la compra
    const totalCost = price * quantity;
    if (user.balance < totalCost) {
      res.status(400).json({ error: 'Saldo insuficiente' });
      return;
    }

    // Actualiza el saldo del usuario restando el costo total de la compra
    const newBalance = user.balance - totalCost;
    await pool.query('UPDATE users SET balance = $1 WHERE email = $2', [newBalance, userEmail]);

    // Registra la compra en la base de datos (opcional)
    // ...

    res.status(200).json({ message: 'Compra realizada con éxito', newBalance });
  } catch (error) {
    res.status(401).json({ error: 'Token inválido' });
  }
})

async function initializeDatabase() {
  try {
    await pool.query(`
      CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        failed_attempts INTEGER DEFAULT 0,
        blocked BOOLEAN DEFAULT false,
        balance NUMERIC(10, 2) DEFAULT 100
      );
  `);
    await pool.query(`
      CREATE TABLE IF NOT EXISTS verification_codes (
        id SERIAL PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        code INTEGER NOT NULL,
        expiration_time TIMESTAMP NOT NULL DEFAULT NOW() + INTERVAL '5 minutes'
      );
    `);
    await pool.query(`
      CREATE TABLE IF NOT EXISTS products (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price NUMERIC(10, 2) NOT NULL
      );
    `);

    // Insert products into the "products" table
    await pool.query(`
      INSERT INTO products (name, description, price)
      VALUES
      ('Camiseta de algodón', 'Camiseta de manga corta hecha de suave algodón', 14.99),
      ('Zapatillas deportivas', 'Zapatillas cómodas y duraderas para actividades deportivas', 49.99),
      ('Bolso de cuero', 'Bolso elegante y espacioso hecho de cuero genuino', 79.99);
    `);

    console.log('Tablas creadas o ya existentes');
  } catch (error) {
    console.error('Error al crear las tablas:', error);
    process.exit(1);
  }
}

initializeDatabase().then(() => {
  const PORT = process.env.PORT || 3001;
  app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
  });
});