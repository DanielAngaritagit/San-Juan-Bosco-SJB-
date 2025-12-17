// Lógica del chatbot institucional

document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const userInput = document.getElementById('user-input');
    const sendButton = document.getElementById('send-button');
    const quickOptions = document.querySelectorAll('.quick-option');
    // Función para cargar conversaciones anteriores
    function loadConversations() {
        fetch('api/get_conversaciones.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.conversations.length > 0) {
                    data.conversations.forEach(conv => {
                        addMessage(conv.mensaje, conv.emisor === 'usuario');
                    });
                } else if (data.status === 'error') {
                    console.error('Error al cargar conversaciones:', data.message);
                }
            })
            .catch(error => console.error('Error en la solicitud fetch para cargar conversaciones:', error));
    }

    // Cargar conversaciones anteriores al iniciar
    loadConversations();

    // Función para agregar mensaje al chat
    function addMessage(message, isUser) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message');
        messageDiv.classList.add(isUser ? 'user-message' : 'bot-message');
        const messageContent = document.createElement('div');
        messageContent.classList.add('message-content');
        messageContent.textContent = message;
        messageDiv.appendChild(messageContent);
        chatMessages.appendChild(messageDiv);
        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    // Función para guardar el mensaje en la base de datos
    function saveMessage(message, sender) {
        fetch('api/guardar_chat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ message: message, sender: sender })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                console.error('Error al guardar el mensaje:', data.message);
            }
        })
        .catch(error => console.error('Error en la solicitud fetch:', error));
    }

    // Función para procesar el mensaje del usuario
    function processUserMessage() {
        const message = userInput.value.trim();
        if (message === '') return;

        addMessage(message, true);
        saveMessage(message, 'usuario'); // Guardar mensaje del usuario
        userInput.value = '';

        // Simular tiempo de respuesta
        setTimeout(() => {
            const response = getBotResponse(message);
            addMessage(response, false);
            saveMessage(response, 'bot'); // Guardar respuesta del bot
        }, 800);
    }
    // Función para obtener respuesta del bot
    function getBotResponse(message) {
        message = message.toLowerCase();
        // Respuestas basadas en palabras clave
        if (message.includes('hola') || message.includes('buenos días') || message.includes('buenas tardes')) {
            return '¡Hola! ¿En qué puedo ayudarte?';
        } else if (message.includes('admision') || message.includes('inscripcion') || message.includes('requisito')) {
            return 'Para el proceso de admisión, necesitarás: formulario de inscripción, documentos del estudiante, boletines de años anteriores y examen de ingreso. Te recomiendo visitar https://www.colegiosanjuanboscogiron.edu.co/wp/admisiones/ para más detalles.';
        } else if (message.includes('programa') || message.includes('académico') || message.includes('carrera') || message.includes('curso')) {
            return 'Ofrecemos educación desde preescolar hasta media técnica. Nuestros programas incluyen: educación básica primaria, básica secundaria y media técnica con especialidades en sistemas, comercio y más. Visita https://www.colegiosanjuanboscogiron.edu.co/wp/programas/ para más información.';
        } else if (message.includes('horario') || message.includes('hora') || message.includes('atención')) {
            return 'Nuestro horario de atención es de lunes a viernes de 7:00 am a 5:00 pm. Las clases se imparten de 7:00 am a 2:00 pm, con actividades extracurriculares en las tardes.';
        } else if (message.includes('contacto') || message.includes('teléfono') || message.includes('dirección') || message.includes('email')) {
            return 'Puedes contactarnos en: Teléfono: (607) 655 5555, Dirección: Carrera 25 # 15-45, Girón, Santander. Email: info@colegiosanjuanboscogiron.edu.co. También visítanos en https://www.colegiosanjuanboscogiron.edu.co/wp/contacto/';
        } else if (message.includes('extraescolar') || message.includes('actividad') || message.includes('deporte') || message.includes('arte')) {
            return 'Ofrecemos diversas actividades extracurriculares: fútbol, baloncesto, voleibol, música, teatro, danzas y club de ciencias. Consulta horarios y disponibilidad en la coordinación.';
        } else if (message.includes('coste') || message.includes('precio') || message.includes('matrícula') || message.includes('pensión')) {
            return 'Los costos de matrícula y pensión varían según el grado. Te invitamos a contactar a nuestra oficina de admisiones para obtener información detallada sobre costos y opciones de pago.';
        } else if (message.includes('gracias') || message.includes('agradezco') || message.includes('agradecido')) {
            return '¡De nada! Estoy aquí para ayudarte. No dudes en preguntar si necesitas más información.';
        } else {
            return 'Lo siento, no tengo información sobre eso. Puedes contactarnos directamente al (607) 655 5555 o visitar nuestro sitio web https://www.colegiosanjuanboscogiron.edu.co/wp/ para más detalles.';
        }
    }
    // Event listeners
    sendButton.addEventListener('click', processUserMessage);
    userInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            processUserMessage();
        }
    });
    quickOptions.forEach(option => {
        option.addEventListener('click', function() {
            userInput.value = this.getAttribute('data-question');
            processUserMessage();
        });
    });
});
