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

async function getPosts(sessionCookie) {
    const query = `
      query {
        posts {
          id
          title
          content
          user_id
        }
      }
    `;

    const response = await fetch('/graphql', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ query }),
    });

    const jsonResponse = await response.json();

    if (jsonResponse.errors) {
        // Maneja los errores aquí
        console.error(jsonResponse.errors);
        return null;
    }

    return jsonResponse.data.posts;
}

async function init() {
    
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
          query {
            posts {
              id
              title
              content
              user_id
            }
          }
        `;
    
        // Nota: En este punto, no necesitas definir variables para la solicitud de posts
    
        const response = await fetch('/graphql', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
          body: JSON.stringify({ query }), // No incluyas variables aquí
        });
    
        const jsonResponse = await response.json();
    
        if (jsonResponse.errors) {
          // Handle errors here
          console.error(jsonResponse.errors);
          return;
        }
    
        const posts = jsonResponse.data.posts;
    
        // Procesa y muestra los datos de los posts
        displayPosts(posts);
      }
    }

    async function displayPosts(posts) {
        const postListElement = document.getElementById('post-list');
    
        // Limpia el contenido anterior
        postListElement.innerHTML = '';
    
        // Itera sobre los posts y crea elementos HTML para cada uno
        for (const post of posts) {
            const postElement = document.createElement('div');
            postElement.classList.add('post');
    
            const titleElement = document.createElement('h2');
            titleElement.textContent = post.title;
    
            const creatorElement = document.createElement('p');
            const username = await getUsername(post.user_id);
            creatorElement.textContent = `Creado por: ${username}`;
    
            const contentElement = document.createElement('p');
            contentElement.textContent = post.content;
    
            // Agrega los elementos al post
            postElement.appendChild(titleElement);
            postElement.appendChild(creatorElement);
            postElement.appendChild(contentElement);
    
            // Agrega el post al listado
            postListElement.appendChild(postElement);
        }
    }
    
      
      // Función para obtener el nombre de usuario mediante una solicitud GraphQL
      async function getUsername(userId) {
        const query = `
          query GetUsername($userId: Int!) {
            getUserById(userId: $userId) {
              username
            }
          }
        `;
      
        const variables = {
          userId,
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
          // Maneja los errores aquí
          console.error(jsonResponse.errors);
          return 'Usuario Desconocido';
        }
      
        const username = jsonResponse.data.getUserById.username;
        return username;
      }
  
      window.addEventListener('load', init);

      // Agregar un evento para escuchar el envío del formulario
      document.getElementById('send-post').addEventListener('click', async function() {
        event.preventDefault(); // Evitar el envío predeterminado del formulario

        const form = document.getElementById('add-post-form');
        
        const sessionCookie = getCookie('sessionCookie');
        const title = form.elements.title.value;
        const content = form.elements.content.value;

        if (title.trim() === '' || content.trim() === '') {
            alert('Los campos Título y Contenido no pueden estar vacíos.');
            return;
          }
      
        // Obtener el ID del usuario
        const userId = parseInt(await getUserId(sessionCookie), 10); // Convierte a número entero
      
        if (!isNaN(userId)) { // Verifica que sea un número válido
            // Realizar una solicitud GraphQL para agregar el nuevo post
            const success = await addNewPost(sessionCookie, userId, title, content);
          
            if (success) {
              // Actualizar la lista de posts después de agregar uno nuevo
              await updatePostsList();
          
              // Limpiar el formulario después de agregar el post
              form.reset();
            }
        } else {
            // Manejar el caso en el que no se pueda obtener el ID del usuario
            console.error('No se pudo obtener el ID del usuario o no es un número válido.');
        }
      });
      
      // Función para obtener el ID del usuario
      async function getUserId(sessionCookie) {

        const query = `
          query GetProfile($sessionCookie: String!) {
            profile(sessionCookie: $sessionCookie, id: true) {
              id
            }
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
      
        if (jsonResponse.errors) {
          // Manejar errores aquí
          console.error(jsonResponse.errors);
          return null;
        }
      
        return jsonResponse.data.profile.id;
      }
      
      // Función para agregar un nuevo post
      async function addNewPost(sessionCookie, userId, title, content) {
        const query = `
          mutation AddPost($userId: Int!, $title: String!, $content: String!) {
            addPost(userId: $userId, title: $title, content: $content) {
              success
            }
          }
        `;
      
        const variables = {
          userId,
          title,
          content,
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
          // Manejar errores aquí
          console.error(jsonResponse.errors);
          return false;
        }
      
        return jsonResponse.data.addPost.success;
      }
      
      // Función para actualizar la lista de posts después de agregar uno nuevo
      async function updatePostsList() {
        // Realizar una solicitud GraphQL para obtener la lista actualizada de posts
        const sessionCookie = getCookie('sessionCookie');
        const posts = await getPosts(sessionCookie);
      
        if (posts) {
          // Actualizar la representación en la página web con los nuevos posts
          window.location.href = 'posts.html';
        }
      }
const newPostButton = document.getElementById('new-post-button');
const newPostArea = document.getElementById('new-post-area');
const cancelButton = document.getElementById('cancel-post');

// Agrega un controlador de eventos para mostrar el área de nuevo post
newPostButton.addEventListener('click', function () {
  newPostArea.style.display = 'block';
});

// Agrega un controlador de eventos para ocultar el área de nuevo post (cancelar)
cancelButton.addEventListener('click', function () {
  newPostArea.style.display = 'none';
});