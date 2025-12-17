// js/session_checker.js
(function() {
    const INACTIVITY_TIMEOUT = 60 * 1000; // 30 segundos en milisegundos
    let inactivityTimer;

    function resetTimer() {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(logoutUser, INACTIVITY_TIMEOUT);
        // Opcional: Si se necesita mantener la sesión activa en el servidor
        // con cada interacción, se podría hacer un fetch aquí.
        // Por ahora, la lógica de PHP ya maneja la inactividad del servidor.
    }

    function logoutUser() {
        // Redirigir al inicio de sesión con un mensaje de inactividad
        window.location.href = '../inicia.html?error=inactive_frontend';
    }

    // Eventos para detectar la actividad del usuario
    document.addEventListener('mousemove', resetTimer);
    document.addEventListener('keydown', resetTimer);
    document.addEventListener('click', resetTimer);
    document.addEventListener('scroll', resetTimer); // Añadir scroll para más cobertura

    // Iniciar el temporizador cuando la página carga
    resetTimer();
})();