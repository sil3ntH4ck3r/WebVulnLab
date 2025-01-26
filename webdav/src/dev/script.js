document.addEventListener('DOMContentLoaded', () => {
    const chatBox = document.getElementById('chat-box');
    const userInput = document.getElementById('user-input');
    const sendButton = document.getElementById('send-button');

    const responses = {
        'hola': '¡Hola! ¿Cómo puedo ayudarte?',
        'adiós': '¡Adiós! Que tengas un buen día.',
        '¿cómo estás?': 'Soy un bot, pero estoy aquí para ayudarte.',
        'default': 'Lo siento, no entiendo tu mensaje.'
    };

    sendButton.addEventListener('click', () => {
        const userText = userInput.value.trim().toLowerCase();
        if (userText) {
            addMessage(userText, 'user');
            userInput.value = '';
            setTimeout(() => {
                const botResponse = responses[userText] || responses['default'];
                addMessage(botResponse, 'bot');
            }, 500);
        }
    });

    userInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendButton.click();
        }
    });

    function addMessage(text, sender) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', `${sender}-message`);
        const messageContent = document.createElement('div');
        messageContent.classList.add('message-content');
        messageContent.textContent = text;
        messageElement.appendChild(messageContent);
        chatBox.appendChild(messageElement);
        chatBox.scrollTop = chatBox.scrollHeight;
    }
});
