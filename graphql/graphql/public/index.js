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
        document.getElementById('contenido').style.display = 'none';
    }
}

window.addEventListener('load', init);

document.getElementById('registrarse').addEventListener('click', function() {
    window.location.href = 'register.html';
});
  
  document.getElementById('iniciar-sesion').addEventListener('click', function() {
    window.location.href = 'login.html';
});
  