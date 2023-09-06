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
    if (!isLoggedIn) {
        window.location.href = 'index.html';
    } else {
        const query = `
            query GetProfile($sessionCookie: String!) {
                profile(sessionCookie: $sessionCookie, id: true, username: true, api: true) {
                    id
                    username
                    api
                }
            }
        `;

        const variables = {
            sessionCookie: sessionCookie,
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
            // Handle errors here
            console.error(jsonResponse.errors);
            return;
        }

        const profile = jsonResponse.data.profile;

        // Asignar valores a los elementos HTML
        const profileIdElement = document.getElementById('profile-id');
        const profileUsernameElement = document.getElementById('profile-username');
        const profileApiKeyElement = document.getElementById('profile-api');

        if (profileIdElement && profileUsernameElement && profileApiKeyElement) {
            // Asignar valores a los elementos HTML
            profileIdElement.textContent = profile.id;
            profileUsernameElement.textContent = profile.username;
            profileApiKeyElement.textContent = profile.api;
        }
    }
  }
  
  window.addEventListener('load', init);

  // Obtén una referencia al botón de cierre de sesión
const logoutButton = document.getElementById('logout-button');

// Agrega un evento de clic al botón de cierre de sesión
logoutButton.addEventListener('click', function () {
  // Borra todas las cookies
  const cookies = document.cookie.split(';');
  for (let i = 0; i < cookies.length; i++) {
    const cookie = cookies[i];
    const eqPos = cookie.indexOf('=');
    const cookieName = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
    document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
  }

  // Redirige al usuario a "index.html" después de cerrar la sesión
  window.location.href = 'index.html';
});