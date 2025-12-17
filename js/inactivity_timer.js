/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
let inactivityTimer;
let sessionCheckInterval;
const SESSION_CHECK_INTERVAL = 10 * 1000; // 10 segundos
const INACTIVITY_TIMEOUT = 3 * 60 * 1000; // 3 minutos

function resetTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(logoutUser, INACTIVITY_TIMEOUT);
}

function logoutUser() {
    let pathToLogout = '../logout.php?reason=inactivity';
    if (window.location.pathname.split('/').length <= 3) {
        pathToLogout = 'logout.php?reason=inactivity';
    }
    window.location.href = pathToLogout;
}

// --- Funciones para el manejo de sesiones concurrentes ---
const sessionConflictModal = document.getElementById('session-conflict-modal');
const closeOtherSessionBtn = document.getElementById('close-other-session-btn');
const keepThisSessionBtn = document.getElementById('keep-this-session-btn');

async function checkSessionStatus() {
    try {
        const response = await fetch('../api/check_session_status.php');
        const result = await response.json();

        if (result.status === 'taken_over') {
            // Pausar el chequeo de sesión para evitar múltiples alertas
            clearInterval(sessionCheckInterval);
            sessionConflictModal.style.display = 'block';
        } else if (result.status === 'invalid') {
            // Sesión inválida, redirigir a login
            clearInterval(sessionCheckInterval);
            alert('Tu sesión ha expirado o es inválida. Por favor, inicia sesión de nuevo.');
            logoutUser(); // Redirigir a logout para limpiar la sesión
        }
        // Si es 'valid', no hacer nada, el timer de inactividad sigue funcionando

    } catch (error) {
        console.error('Error al verificar el estado de la sesión:', error);
        // Podrías mostrar un mensaje de error o simplemente ignorar si es un problema de red temporal
    }
}

// Event Listeners para los botones del modal de conflicto de sesión
if (closeOtherSessionBtn) {
    closeOtherSessionBtn.addEventListener('click', async () => {
        try {
            const response = await fetch('../api/close_other_session.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ action: 'close_other' })
            });
            const result = await response.json();

            if (result.success) {
                alert('La sesión anterior ha sido cerrada. Esta sesión es ahora la activa.');
                sessionConflictModal.style.display = 'none';
                // Reiniciar el chequeo de sesión
                sessionCheckInterval = setInterval(checkSessionStatus, SESSION_CHECK_INTERVAL);
            } else {
                alert(`Error al cerrar la sesión anterior: ${result.message}`);
            }
        } catch (error) {
            console.error('Error al intentar cerrar la sesión anterior:', error);
            alert('Error de red al intentar cerrar la sesión anterior.');
        }
    });
}

if (keepThisSessionBtn) {
    keepThisSessionBtn.addEventListener('click', () => {
        sessionConflictModal.style.display = 'none';
        // Si el usuario elige mantener esta sesión, simplemente cerramos el modal
        // y reanudamos el chequeo de sesión. La otra sesión eventualmente será detectada como inválida.
        sessionCheckInterval = setInterval(checkSessionStatus, SESSION_CHECK_INTERVAL);
    });
}

// --- Event Listeners para Resetear el Temporizador de Inactividad ---
window.addEventListener('load', () => {
    resetTimer();
    // Iniciar el chequeo de sesión cuando la página carga
    sessionCheckInterval = setInterval(checkSessionStatus, SESSION_CHECK_INTERVAL);
});
document.addEventListener('mousemove', resetTimer);
document.addEventListener('mousedown', resetTimer); 
document.addEventListener('touchstart', resetTimer); 
document.addEventListener('click', resetTimer); 
document.addEventListener('keypress', resetTimer);
document.addEventListener('scroll', resetTimer, true); 
document.addEventListener('focus', resetTimer, true); 

// Llamada inicial para iniciar el temporizador (si no se ha iniciado ya por el evento load)
resetTimer();