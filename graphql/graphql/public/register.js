async function init() {
  function getCookie(cookieName) {
      const cookies = document.cookie.split(';');
      for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].trim();
        if (cookie.startsWith(`${cookieName}=`)) {
          return cookie.substring(cookieName.length + 1);
        }
      }
      return null;
    }
  
    const sessionCookie = getCookie('sessionCookie');
  
  async function checkLoggedIn(sessionCookie) {
      const query = `
        query IsLoggedIn($sessionCookie: String) {
          isLoggedIn(sessionCookie: $sessionCookie)
        }
      `;
    
      const variables = {
        sessionCookie,
      };
    
      const response = await fetch('/graphql', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ query, variables }),
      });
    
      const jsonResponse = await response.json();
      return jsonResponse.data.isLoggedIn;
  }
  
  const isLoggedIn = await checkLoggedIn(sessionCookie);
  if (isLoggedIn) {
      window.location.href = 'index.html';
  }
}

window.addEventListener('load', init);

document.getElementById('registro-form').addEventListener('submit', async (event) => {
    event.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password-confirm').value;

    if (password !== passwordConfirm) {
      document.getElementById('registro-mensaje').innerText = ' Error: Las contraseñas no coinciden.';
      return;
    }

    // Verificar si el usuario ya existe
    const isUsernameTaken = await checkUsername(username);

    if (isUsernameTaken) {
      document.getElementById('registro-mensaje').innerText = 'Error: El nombre de usuario ya está en uso.';
      return;
    }

    // Hash 
    const hashedPassword = sha512(password);

    // Lógica para registrar al usuario en la base de datos
    const query = `
      mutation CreateUser($username: String!, $password: String!) {
        createUser(username: $username, password: $password) {
          id
        }
      }
    `;

    const variables = {
      username,
      password: hashedPassword,
    };

    const response = await fetch('/graphql', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ query, variables }),
    });

    const jsonResponse = await response.json();

    if (jsonResponse.errors) {
      document.getElementById('registro-mensaje').innerText = 'Error: Error al registrar el usuario.';
    } else {
      document.getElementById('registro-mensaje').innerText = 'Usuario registrado exitosamente.';
    }
  });

// Función para verificar si el nombre de usuario está en uso
async function checkUsername(username) {
    const query = `
      query CheckUsername($username: String!) {
        checkUsername(username: $username)
      }
    `;
  
    const variables = {
      username,
    };
  
    const response = await fetch('/graphql', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ query, variables }),
    });
  
    const jsonResponse = await response.json();
  
    // Verificar si la respuesta contiene errores
    if (jsonResponse.errors) {
      console.error('Error en la consulta GraphQL:', jsonResponse.errors);
      throw new Error('Error en la consulta GraphQL');
    }
  
    // Si no hay errores, retornar el resultado
    return jsonResponse.data.checkUsername;
  }  