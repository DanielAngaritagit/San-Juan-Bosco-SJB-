/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
/**
 * Lógica de la página de inicio de sesión (inicia.html).
 * Maneja la selección de rol, la validación y envío del formulario de login,
 * y la funcionalidad del modal de recuperación de contraseña.
 */

document.addEventListener('DOMContentLoaded', function() {
    // --- Selección de Elementos del DOM ---
    const loginForm = document.getElementById('loginForm');
    const statusMessage = document.getElementById('statusMessage');
    const roleRadios = document.querySelectorAll('input[name="role"]');
    const formGroups = document.querySelectorAll('.form-group');
    const loginBtn = document.querySelector('.login-btn');
    const forgotPassword = document.querySelector('.forgot-password');
    const loginContainer = document.getElementById('loginContainer');
    const forgotForm = document.getElementById('forgotPasswordForm');

    // Si algún elemento crucial no existe, se detiene la ejecución para evitar errores.
    if (!loginForm || !statusMessage || !roleRadios.length || !formGroups.length || !loginBtn || !forgotPassword || !loginContainer) {
        return;
    }

    // --- Estado Inicial de la UI ---
    // Oculta los campos de usuario/contraseña y los botones hasta que se seleccione un rol.
    formGroups.forEach(group => {
        group.style.display = 'none';
        group.classList.remove('visible');
    });
    loginBtn.style.display = 'none';
    loginBtn.classList.remove('visible');
    forgotPassword.style.display = 'none';
    forgotPassword.classList.remove('visible');

    // --- Lógica de Selección de Rol ---
    roleRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                // Aplica clases y estilos dinámicos basados en el rol seleccionado.
                loginContainer.classList.add('transition-active');
                loginContainer.classList.remove('admin', 'profesor', 'estudiante', 'padre', 'administrativo');
                loginContainer.classList.add(this.value);

                // Cambia la clase en el body para afectar estilos globales como el título
                const body = document.body;
                body.classList.remove('role-admin', 'role-profesor', 'role-estudiante', 'role-padre', 'role-administrativo');
                body.classList.add('role-' + this.value);

                const roleColor = getRoleColor(this.value);
                loginContainer.style.borderColor = roleColor;
                loginBtn.style.background = roleColor;

                // Muestra los campos del formulario con una animación de aparición.
                formGroups.forEach(group => {
                    group.style.display = 'block';
                    setTimeout(() => group.classList.add('visible'), 50);
                });

                loginBtn.style.display = 'block';
                setTimeout(() => loginBtn.classList.add('visible'), 200);

                forgotPassword.style.display = 'block';
                setTimeout(() => forgotPassword.classList.add('visible'), 300);
            }
        });
    });

    // --- Envío del Formulario de Login ---
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault(); // Previene el envío tradicional del formulario.

        // Limpia el perfil de usuario del almacenamiento local para forzar una recarga de datos frescos.
        localStorage.removeItem('userProfile');

        // Valida el formulario antes de enviarlo.
        if (!validateForm()) {
            return;
        }

        // Recolecta los datos del formulario.
        const usuario = document.getElementById('username').value.trim();
        const contrasena = document.getElementById('password').value;
        const rolRadio = document.querySelector('input[name="role"]:checked');
        const rol = rolRadio ? rolRadio.value : '';

        statusMessage.textContent = 'Iniciando sesión...';
        statusMessage.className = 'status-message';

        try {
            // Realiza la petición a la API de login usando fetch.
            const response = await fetch('php/login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ usuario, contrasena, rol })
            });

            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }

            // Verifica que la respuesta sea JSON.
            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                throw new Error('Respuesta inesperada del servidor');
            }

            const data = await response.json();

            // Procesa la respuesta de la API.
            if (data && data.success) {
                // Si el login es exitoso, muestra un mensaje y redirige.
                statusMessage.textContent = `Bienvenido, ${usuario}! Redirigiendo...`;
                statusMessage.className = 'status-message success';
                setTimeout(() => {
                    window.location.href = data.redirect || 'login.php';
                }, 1500);
            } else {
                // Si falla, muestra el mensaje de error de la API.
                showError(data && data.message ? data.message : 'Usuario o contraseña incorrectos');
            }
        } catch (error) {
            showError('Error de conexión o formato de respuesta');
        }
    });

    setupRoleAccessibility();

    // --- Lógica para el Modal de Recuperación de Contraseña ---
    if (forgotForm) {
        // ... (La lógica del modal de recuperación sigue un patrón similar de fetch a la API)
    }
});

/**
 * Devuelve un color hexadecimal basado en el rol del usuario.
 * @param {string} role El rol del usuario.
 * @returns {string} El código de color.
 */
function getRoleColor(role) {
    switch(role) {
        case 'admin': return '#DABE43';
        case 'profesor': return '#569539';
        case 'estudiante': return '#6c757d';
        case 'padre': return '#0D164B';
        case 'administrativo': return '#9B5DE5';
        default: return '#0D164B';
    }
}

/**
 * Valida los campos del formulario de login del lado del cliente.
 * @returns {boolean} True si el formulario es válido, false en caso contrario.
 */
function validateForm() {
    const username = document.getElementById('username') ? document.getElementById('username').value.trim() : '';
    const password = document.getElementById('password') ? document.getElementById('password').value : '';
    const roleSelected = document.querySelector('input[name="role"]:checked');
    
    if (!roleSelected) {
        showError('Por favor selecciona tu rol');
        return false;
    }
    if (!username) {
        showError('Por favor ingresa tu usuario');
        return false;
    }
    if (!password) {
        showError('Por favor ingresa tu contraseña');
        return false;
    }
    return true;
}

/**
 * Muestra un mensaje de error en el área de estado.
 * @param {string} message El mensaje de error a mostrar.
 */
function showError(message) {
    const statusMessage = document.getElementById('statusMessage');
    if (statusMessage) {
        statusMessage.textContent = message;
        statusMessage.className = 'status-message error';
    }
}

/**
 * Alterna la visibilidad de la contraseña en el campo de input.
 */
function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleButton = document.querySelector('.password-toggle i'); // Assuming the <i> tag is directly inside the button

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleButton.classList.remove('fa-eye');
        toggleButton.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleButton.classList.remove('fa-eye-slash');
        toggleButton.classList.add('fa-eye');
    }
}

/**
 * Mejora la accesibilidad permitiendo seleccionar roles con el teclado.
 */
function setupRoleAccessibility() {
    // ... (Lógica para manejar eventos de teclado en los roles)
}

/**
 * Muestra el modal de recuperación de contraseña.
 */
function showForgotPassword() {
    const modal = document.getElementById('forgotPasswordModal');
    if (modal) modal.style.display = 'flex';
}

/**
 * Cierra y resetea el modal de recuperación de contraseña.
 */
function closeForgotPassword() {
    const modal = document.getElementById('forgotPasswordModal');
    if (modal) modal.style.display = 'none';
    // ... (Lógica para resetear el estado del formulario del modal)
}