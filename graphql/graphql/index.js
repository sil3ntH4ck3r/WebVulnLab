const express = require('express');
const { graphqlHTTP } = require('express-graphql');
const { buildSchema } = require('graphql');
const path = require('path');
const mysql = require('mysql2/promise');

const dbConfig = {
  host: 'graphql_db_server_v2',
  user: 'root',
  password: 'mysecretpassword',
  database: 'mydatabase',
};

let pool = null;

// Función para conectarse a la base de datos con espera activa
const connectToDatabase = async () => {
  while (!pool) {
    try {
      pool = await mysql.createPool(dbConfig);
      console.log('Conexión exitosa a la base de datos.');
    } catch (error) {
      console.error('Error al conectar a la base de datos:', error);
      console.log('Reintentando la conexión en 5 segundos...');
      await new Promise(resolve => setTimeout(resolve, 5000));
    }
  }
};


// Llama a la función de conexión a la base de datos
connectToDatabase();


// Define tu esquema GraphQL aquí
const schema = buildSchema(`
  type Query {
    hello: String
    checkUsername(username: String!): Boolean
    login(username: String!, password: String!): User
    isLoggedIn(sessionCookie: String): Boolean
    profile(sessionCookie: String): User
  }

  type Mutation {
    createUser(username: String!, password: String!): User
  }

  type User {
    id: ID
    username: String
    password: String
    sessionCookie: String
  }  
`);

// Define resolvers para tu esquema GraphQL
const root = {
  hello: () => 'Hola, mundo!',

  checkUsername: async ({ username }) => {
    const connection = await pool.getConnection();
    try {
      const [rows] = await connection.query('SELECT COUNT(*) as count FROM users WHERE username = ?', [username]);
      return rows[0].count > 0; // Devuelve true si el usuario existe, false si no
    } catch (error) {
      throw new Error('Error al verificar el nombre de usuario.');
    } finally {
      connection.release();
    }
  },

  createUser: async ({ username, password }) => {
    // Verificar si el usuario ya existe
    const userExists = await root.checkUsername({ username });

    if (userExists) {
      throw new Error('El nombre de usuario ya está en uso.');
    }

    // Continuar con la lógica de creación de usuario si no existe
    const connection = await pool.getConnection();
    try {
      const [result] = await connection.query('INSERT INTO users (username, password) VALUES (?, ?)', [username, password]);
      return { id: result.insertId, username };
    } catch (error) {
      throw new Error('Error al crear el usuario.');
    } finally {
      connection.release();
    }
  },

  login: async ({ username, password }) => {
    const connection = await pool.getConnection();
    try {
      const [rows] = await connection.query('SELECT * FROM users WHERE username = ?', [username]);
    
      const user = rows[0];

      // Verifica si el objeto 'user' está definido
      if (!user) {
        throw new Error('Usuario o contraseña incorrecto.');
      }
  
      if (user.password === password) {
        function makeid(length) {
          let result = '';
          const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
          const charactersLength = characters.length;
          for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
          }
          return result;
        }
        const sessionCookie = makeid(10);
        // Guardar la cookie en la base de datos
        await connection.query('UPDATE users SET session_cookie = ? WHERE id = ?', [sessionCookie, user.id]);
        return { id: user.id, username: user.username, sessionCookie };
      }else {
        throw new Error('Usuario o contraseña incorrecto.');
      }
    } catch (error) {
      throw new Error(error);
    } finally {
      connection.release();
    }
  },

  isLoggedIn: async ({ sessionCookie }) => {

    if (!sessionCookie) {
      return false;
    }

    const connection = await pool.getConnection();
    try {
      const [rows] = await connection.query('SELECT * FROM users WHERE session_cookie = ?', [sessionCookie]);
      return rows.length > 0;
    } catch (error) {
      throw new Error('Error al verificar si el usuario está logueado.');
    } finally {
      connection.release();
    }
  },
  profile: async ({ sessionCookie }) => {
    const connection = await pool.getConnection();
    try {
      const [rows] = await connection.query('SELECT * FROM users WHERE session_cookie = ?', [sessionCookie]);

      if (rows.length === 0) {
        throw new Error('Cookie no válida o usuario no encontrado.');
      }

      const user = rows[0];

      return {
        id: user.id,
        username: user.username,
        api: user.session_cookie,
      };

    } catch (error) {
      throw new Error('Error al buscar el perfil del usuario: ' + error.message);
    } finally {
      connection.release();
    }
  },

};


const app = express();

// Configura la ruta raíz para enviar el archivo HTML
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

app.use('/graphql', graphqlHTTP({
  schema: schema,
  rootValue: root,
  graphiql: true,
  customFormatErrorFn: (error) => {
    const formattedError = {
      message: error.message,
      locations: error.locations,
      path: error.path,
    };

    // Agregar información adicional del error si está disponible
    if (error.originalError) {
      formattedError.originalError = {
        message: error.originalError.message,
        stack: error.originalError.stack,
      };

    }

    return formattedError;
  },
}));

app.use(express.static(path.join(__dirname, 'public')));

const port = 3000;

// Inicia el servidor web Express en el puerto 3000
app.listen(port, () => {
  console.log(`Servidor web Express escuchando en el puerto ${port}`);
});