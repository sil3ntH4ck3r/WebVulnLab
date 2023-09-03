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


document.getElementById('login-form').addEventListener('submit', async (event) => {
    event.preventDefault();      

    const username = document.getElementById('login-username').value;
    const password = document.getElementById('login-password').value;

    const hashedPassword = sha512(password);
  
    const query = `
      query Login($username: String!, $password: String!) {
        login(username: $username, password: $password) {
          id
          username
          sessionCookie
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
        document.getElementById('login-mensaje').innerText = jsonResponse.errors[0].message;
    } else if (jsonResponse.data.login && jsonResponse.data.login.id !== null && jsonResponse.data.login.username !== null) {
        document.getElementById('login-mensaje').innerText = 'Inicio de sesión exitoso.';

        const sessionCookie = jsonResponse.data.login.sessionCookie;
        document.cookie = `sessionCookie=${sessionCookie}; max-age=${30 * 24 * 60 * 60}; path=/`;
    } else {
        document.getElementById('login-mensaje').innerText = 'Error desconocido.';
    }
      
      
  });