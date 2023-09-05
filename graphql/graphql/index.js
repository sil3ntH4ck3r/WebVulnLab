const express = require('express');
const { graphqlHTTP } = require('express-graphql');
const { buildSchema } = require('graphql');
const path = require('path');
const mysql = require('mysql2/promise');
const cookieParser = require('cookie-parser');

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


const createDefaultEntries = async () => {
  let connection;
  let maxAttempts = 5; // Número máximo de intentos
  let attempts = 0;   // Contador de intentos

  while (!connection && attempts < maxAttempts) {
    try {
      connection = await pool.getConnection();
    } catch (error) {
      console.error(`Error al conectar a la base de datos (intentos restantes: ${maxAttempts - attempts}):`, error);
      attempts++;
      await new Promise(resolve => setTimeout(resolve, 5000)); // Esperar 5 segundos antes de intentar de nuevo
    }
  }

  if (!connection) {
    console.error(`No se pudo conectar a la base de datos después de ${maxAttempts} intentos.`);
    return;
  }

  try {
    // Verificar si ya existe un usuario por defecto
    const [existingUser] = await connection.query('SELECT * FROM users WHERE id = ?', [1]);
    if (existingUser.length === 0) {
      // Si no existe, crea el usuario por defecto
      await connection.query('INSERT INTO users (id, username, password) VALUES (?, ?, ?)', [
        1,
        'admin', // Nombre de usuario por defecto
        '7fcf4ba391c48784edde599889d6e3f1e47a27db36ecc050cc92f259bfac38afad2c68a1ae804d77075e8fb722503f3eca2b2c1006ee6f6c7b7628cb45fffd1d'    // Contraseña por defecto
      ]);
    }

    // Verificar si ya existe un post por defecto
    const [existingPosts] = await connection.query('SELECT * FROM posts LIMIT 1');
    if (existingPosts.length === 0) {
      // Si no existe, crea el post por defecto
      await connection.query('INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)', [
        'Título por defecto',
        'Contenido por defecto',
        1, // ID del usuario por defecto
      ]);
    }
  } catch (error) {
    console.error('Error al crear los registros por defecto:', error);
  } finally {
    if (connection) {
      connection.release();
    }
  }
};

// Llama a la función para crear los registros por defecto al conectar a la base de datos
connectToDatabase().then(() => {
  createDefaultEntries();
});

// Llama a la función para crear el post por defecto al conectar a la base de datos
connectToDatabase().then(() => {
  createDefaultEntries();
});



// Define tu esquema GraphQL aquí
const schema = buildSchema(`
  type Query {
    getUserById(userId: Int!): User
    hello: String
    checkUsername(username: String!): Boolean
    login(username: String!, password: String!): User
    isLoggedIn(sessionCookie: String): Boolean
    profile(sessionCookie: String, id: Boolean, username: Boolean, password: Boolean, api: Boolean): Profile
    posts: [Post]
  }
  type Profile {
    id: ID
    username: String
    api: String
    password: String
  }

  type Mutation {
    addPost(userId: Int!, title: String!, content: String!): AddPostResponse
    createUser(username: String!, password: String!): User
  }

  type AddPostResponse {
    success: Boolean!
  }

  type User {
    id: ID
    username: String
    password: String
    sessionCookie: String
    api: String
  }
  type Post {
    id: ID
    title: String
    content: String
    user_id: Int
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
  profile: async ({ sessionCookie, id, username, password, api }) => {
    const connection = await pool.getConnection();
    try {
      const [rows] = await connection.query('SELECT * FROM users WHERE session_cookie = ?', [sessionCookie]);
  
      if (rows.length === 0) {
        throw new Error('Cookie no válida o usuario no encontrado.');
      }
  
      const user = rows[0];
      const profileData = {};
  
      if (id) {
        profileData.id = user.id;
      }
  
      if (username) {
        profileData.username = user.username;
      }
  
      if (password) {
        profileData.password = user.password;
      }
  
      if (api) {
        profileData.api = user.session_cookie;
      }
  
      return profileData;
    } catch (error) {
      throw new Error('Error al buscar el perfil del usuario: ' + error.message);
    } finally {
      connection.release();
    }
  },
    
  posts: async () => {
    const connection = await pool.getConnection();
    try {
      const [rows] = await connection.query('SELECT * FROM posts');
      return rows;
    } catch (error) {
      throw new Error('Error al obtener los posts.');
    } finally {
      connection.release();
    }
  },
  getUserById: async ({ userId }) => {
    const connection = await pool.getConnection();
    try {
      const [rows] = await connection.query('SELECT username FROM users WHERE id = ?', [userId]);
      if (rows.length === 0) {
        throw new Error('Usuario no encontrado');
      }
      return { username: rows[0].username };
    } catch (error) {
      throw new Error('Error al obtener el nombre de usuario: ' + error.message);
    } finally {
      connection.release();
    }
  },
  addPost: async ({ userId, title, content }) => {
    const connection = await pool.getConnection();
    try {
      // Insertar el nuevo post en la base de datos
      await connection.query('INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)', [title, content, userId]);
      return { success: true };
    } catch (error) {
      throw new Error('Error al agregar el nuevo post: ' + error.message);
    } finally {
      connection.release();
    }
  },

};

const checkAuthentication = async (req, res, next) => {
  if (req.method === 'GET') {
    const sessionCookie = req.cookies.sessionCookie;
    const isAuthenticated = await root.isLoggedIn({ sessionCookie });
    if (!isAuthenticated) {
      return res.status(401).send('Has de iniciar sesión para obtener acceso al playground');
    }
  }
  next();
};


const app = express();

app.use(cookieParser());

// Configura la ruta raíz para enviar el archivo HTML
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'public', 'index.html'));
});

app.use('/graphql', checkAuthentication, graphqlHTTP({
  schema: schema,
  rootValue: root,
  graphiql: true,
  customFormatErrorFn: (error) => {
    const formattedError = {
      message: error.message,
      locations: error.locations,
      path: error.path,
    };

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