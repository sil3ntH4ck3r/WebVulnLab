document.addEventListener('DOMContentLoaded', () => {
  // Referencias DOM
  const encryptTab = document.getElementById('encryptTab');
  const decryptTab = document.getElementById('decryptTab');
  const encryptPanel = document.getElementById('encryptPanel');
  const decryptPanel = document.getElementById('decryptPanel');
  const encryptBtn = document.getElementById('encryptBtn');
  const decryptBtn = document.getElementById('decryptBtn');
  const messageInput = document.getElementById('message');
  const dataInput = document.getElementById('data');
  const encryptResult = document.getElementById('encryptResult');
  const decryptResult = document.getElementById('decryptResult');
  const encryptedText = document.getElementById('encryptedText');
  const decryptedText = document.getElementById('decryptedText');
  const copyEncryptBtn = document.getElementById('copyEncryptBtn');
  const copyDecryptBtn = document.getElementById('copyDecryptBtn');

  // Cambio de pestañas
  encryptTab.addEventListener('click', () => {
    setActiveTab('encrypt');
  });

  decryptTab.addEventListener('click', () => {
    setActiveTab('decrypt');
  });

  function setActiveTab(tabName) {
    // Resetear clases activas
    encryptTab.classList.remove('active');
    decryptTab.classList.remove('active');
    encryptPanel.classList.remove('active');
    decryptPanel.classList.remove('active');

    // Establecer clase activa según la pestaña seleccionada
    if (tabName === 'encrypt') {
      encryptTab.classList.add('active');
      encryptPanel.classList.add('active');
    } else {
      decryptTab.classList.add('active');
      decryptPanel.classList.add('active');
    }
  }

  // Funcionalidad de cifrado
  encryptBtn.addEventListener('click', async () => {
    const message = messageInput.value.trim();
    
    if (!message) {
      showNotification('Por favor, introduce un mensaje para cifrar', 'error');
      return;
    }

    // Mostrar animación de carga
    const btnText = encryptBtn.querySelector('.btn-text');
    const loader = encryptBtn.querySelector('.loader');
    btnText.style.opacity = '0.5';
    loader.style.display = 'block';

    try {
      // Animación de cifrado
      createEncryptionAnimation(messageInput);
      
      const response = await fetch('/encrypt', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message })
      });

      const result = await response.json();
      
      if (result.success) {
        encryptedText.textContent = result.data;
        encryptResult.style.display = 'block';
        showNotification('¡Mensaje cifrado exitosamente!', 'success');
      } else {
        showNotification('Error al cifrar el mensaje', 'error');
      }
    } catch (error) {
      showNotification('Error de conexión', 'error');
      console.error(error);
    } finally {
      // Ocultar animación de carga
      btnText.style.opacity = '1';
      loader.style.display = 'none';
    }
  });

  // Funcionalidad de descifrado
  decryptBtn.addEventListener('click', async () => {
    const data = dataInput.value.trim();
    
    if (!data) {
      showNotification('Por favor, introduce datos para descifrar', 'error');
      return;
    }

    // Mostrar animación de carga
    const btnText = decryptBtn.querySelector('.btn-text');
    const loader = decryptBtn.querySelector('.loader');
    btnText.style.opacity = '0.5';
    loader.style.display = 'block';

    try {
      // Animación de descifrado
      createEncryptionAnimation(dataInput);
      
      const response = await fetch('/decrypt', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ data })
      });

      const result = await response.json();
      
      if (result.success) {
        decryptedText.textContent = result.data;
        decryptResult.style.display = 'block';
        showNotification('¡Mensaje descifrado exitosamente!', 'success');
      } else {
        showNotification('Error al descifrar: ' + result.error, 'error');
      }
    } catch (error) {
      showNotification('Error de conexión', 'error');
      console.error(error);
    } finally {
      // Ocultar animación de carga
      btnText.style.opacity = '1';
      loader.style.display = 'none';
    }
  });

  // Funcionalidad de copiar al portapapeles
  copyEncryptBtn.addEventListener('click', () => {
    copyToClipboard(encryptedText.textContent);
  });

  copyDecryptBtn.addEventListener('click', () => {
    copyToClipboard(decryptedText.textContent);
  });

  // Función para copiar al portapapeles
  function copyToClipboard(text) {
    navigator.clipboard.writeText(text)
      .then(() => {
        showNotification('¡Copiado al portapapeles!', 'success');
      })
      .catch(err => {
        showNotification('Error al copiar', 'error');
        console.error('Error al copiar: ', err);
      });
  }

  // Función para mostrar notificaciones
  function showNotification(message, type) {
    // Eliminar notificaciones existentes
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => {
      notification.remove();
    });

    // Crear nueva notificación
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    
    let icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
    notification.innerHTML = `<i class="fas fa-${icon}"></i> ${message}`;
    
    document.body.appendChild(notification);
    
    // Mostrar con animación
    setTimeout(() => {
      notification.classList.add('show');
    }, 10);
    
    // Ocultar después de un tiempo
    setTimeout(() => {
      notification.classList.remove('show');
      setTimeout(() => {
        notification.remove();
      }, 300);
    }, 3000);
  }

  // Animación de cifrado/descifrado
  function createEncryptionAnimation(inputElement) {
    const container = inputElement.parentElement;
    
    // Crear 10 partículas de animación
    for (let i = 0; i < 10; i++) {
      const particle = document.createElement('div');
      particle.className = 'encrypt-particle';
      particle.style.cssText = `
        position: absolute;
        width: 8px;
        height: 8px;
        background-color: var(--secondary);
        border-radius: 50%;
        pointer-events: none;
        opacity: 0.8;
        z-index: 10;
        top: ${Math.random() * 100}%;
        left: ${Math.random() * 100}%;
        animation: particleMove 1s forwards ease-out;
      `;
      
      container.appendChild(particle);
      
      // Auto-eliminación después de la animación
      setTimeout(() => {
        particle.remove();
      }, 1000);
    }

    // Definir la animación de partículas en CSS
    if (!document.getElementById('particleAnimation')) {
      const style = document.createElement('style');
      style.id = 'particleAnimation';
      style.textContent = `
        @keyframes particleMove {
          0% {
            transform: scale(0.3) translate(0, 0);
            opacity: 0.8;
          }
          100% {
            transform: scale(0) translate(${Math.random() > 0.5 ? '+' : '-'}${20 + Math.random() * 50}px, ${Math.random() > 0.5 ? '+' : '-'}${20 + Math.random() * 50}px);
            opacity: 0;
          }
        }
      `;
      document.head.appendChild(style);
    }
  }

  // Inicialización: mostrar panel de cifrado por defecto
  setActiveTab('encrypt');
});
