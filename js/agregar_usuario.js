/**
 * user_profile_manager.js
 * 
 * Este script gestiona de forma centralizada la carga, almacenamiento y visualización
 * de los datos del perfil del usuario (nombre, rol, foto) utilizando localStorage
 * para asegurar consistencia en toda la aplicación.
 */

/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
document.addEventListener('DOMContentLoaded', function() {
    // Carga el perfil tan pronto como el DOM esté listo.
    loadAndDisplayUserProfile();





    // LÓGICA DE FORMULARIOS DE AGREGAR USUARIO
    function handleFormSubmit(formId, rol) {
        const form = document.getElementById(formId);
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            let isFormValid = true;
            form.querySelectorAll('[required]').forEach(input => {
                if (!input.checkValidity()) {
                    isFormValid = false;
                    input.classList.add('is-invalid');
                    const errorId = input.id + '-error';
                    const errorElement = document.getElementById(errorId);
                    if (errorElement) {
                        errorElement.style.display = 'block';
                        errorElement.textContent = input.validationMessage;
                    }
                } else {
                    input.classList.remove('is-invalid');
                    const errorId = input.id + '-error';
                    const errorElement = document.getElementById(errorId);
                    if (errorElement) {
                        errorElement.style.display = 'none';
                    }
                }
            });

            // --- Specific Date Logic Validation (Keep existing validation, but ensure fields are populated by auto-calc) ---
            // The auto-calculation should ideally make these validations pass if dates are selected.
            // If a user manually changes a date, these validations will still catch issues.
            if (formId === 'form-estudiante') {
                const fechaNacimiento = form.querySelector('#fecha_nacimiento_e').value;
                const fechaExpedicion = form.querySelector('#fecha_expedicion_e').value;
                // Removed validateExpeditionDateLogic as it's now handled by auto-calculation and general validation
            } else if (formId === 'form-acudiente') {
                const fechaNacimiento = form.querySelector('#fecha_nacimiento').value;
                const fechaExpedicion = form.querySelector('#fecha_expedicion').value;
                // Removed validateExpeditionDateLogic
            } else if (formId === 'form-profesor') {
                const fechaNacimiento = form.querySelector('#fecha_nacimiento_p').value;
                const fechaExpedicion = form.querySelector('#fecha_expedicion_p').value;
                // Removed validateExpeditionDateLogic
            } else if (formId === 'form-admin') { // Added for admin form
                const fechaNacimiento = form.querySelector('#fecha_nacimiento_a').value;
                const fechaExpedicion = form.querySelector('#fecha_expedicion_a').value;
                // Removed validateExpeditionDateLogic
            }
            // --- End Specific Date Logic Validation ---

            if (!isFormValid) {
                showMessage('Por favor, complete todos los campos obligatorios y corrija los errores de formato.', 'error');
                return;
            }

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            data.rol = rol;

            fetch('../php/guardar_usuario.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    let successMessage = result.message;
                    if (result.usuario) {
                        let passwordDisplay = result.raw_password ? `<strong>${result.raw_password}</strong>` : 'Generada automáticamente';
                        successMessage += `<br>Usuario: <strong>${result.usuario}</strong><br>Contraseña por defecto: ${passwordDisplay}`;
                    }
                    if (result.grado_asignado) {
                        successMessage += `<br>Asignado a: <strong>${result.grado_asignado}</strong>`;
                    }
                    // Add the security message here
                    successMessage += `<br><span style="color: orange; font-weight: bold;">Por motivo de seguridad cambie la contraseña.</span>`;
                    showMessage(successMessage, 'success');
                    form.reset();
                    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('.is-invalid'));
                    form.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
                    const button = form.querySelector('button[type="submit"]');
                    if(button) button.disabled = true;
                } else {
                    showMessage(result.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Ocurrió un error de red: ' + error.message, 'error');
            });
        });
    }

    function showMessage(message, type) {
        const messageContainer = document.getElementById('message-container');
        if (!messageContainer) {
            console.error('Error: El elemento #message-container no se encontró en el DOM.');
            alert(message);
            return;
        }
        messageContainer.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-times-circle'}"></i> <div>${message}</div>`;
        messageContainer.className = 'message ' + type;
        messageContainer.style.display = 'flex';
    }

    window.validateLettersOnly = function(input, errorId) {
        const errorElement = document.getElementById(errorId);
        const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]*$/;
        if (regex.test(input.value)) {
            input.classList.remove('is-invalid');
            errorElement.style.display = 'none';
        } else {
            input.classList.add('is-invalid');
            errorElement.style.display = 'block';
        }
    }

    window.validateNumbersOnly = function(input, errorId) {
        const errorElement = document.getElementById(errorId);
        const regex = /^\d*$/;
        if (regex.test(input.value)) {
            input.classList.remove('is-invalid');
            errorElement.style.display = 'none';
        } else {
            input.classList.add('is-invalid');
            errorElement.style.display = 'block';
        }
    }

    window.validateEmail = function(input, errorId) {
        const errorElement = document.getElementById(errorId);
        const regex = /^[^@\s]+@[^\s@]+\.[^\s@]+$/;
        if (regex.test(input.value)) {
            input.classList.remove('is-invalid');
            errorElement.style.display = 'none';
        } else {
            input.classList.add('is-invalid');
            errorElement.style.display = 'block';
        }
    }

    window.validateMinAge = function(input, errorId, minAge) {
        const errorElement = document.getElementById(errorId);
        const birthDate = new Date(input.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        if (age >= minAge) {
            input.classList.remove('is-invalid');
            errorElement.style.display = 'none';
        } else {
            input.classList.add('is-invalid');
            errorElement.style.display = 'block';
        }
    }

    window.validateAgeRange = function(input, errorId, minAge, maxAge) {
        const errorElement = document.getElementById(errorId);
        const birthDate = new Date(input.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        if (age >= minAge && age <= maxAge) {
            input.classList.remove('is-invalid');
            errorElement.style.display = 'none';
        } else {
            input.classList.add('is-invalid');
            errorElement.style.display = 'block';
        }
    }

    window.validateDocumentLength = function(docTypeInputId, docNumberInputId, errorId) {
        const docTypeInput = document.getElementById(docTypeInputId);
        const docNumberInput = document.getElementById(docNumberInputId);
        const errorElement = document.getElementById(errorId);
        const docType = docTypeInput.value;
        const docNumber = docNumberInput.value; // Get the value here
        const docLength = docNumber.length;

        let isValid = false;
        switch (docType) {
            case 'CC':
            case 'TI':
                isValid = docLength === 10;
                break;
            case 'CE':
            case 'PA':
                isValid = docLength >= 7 && docLength <= 10;
                break;
            case 'RC': // Added for student
                isValid = docLength === 10;
                break;
            default:
                isValid = true; // No validation for other types
        }

        if (isValid) {
            docNumberInput.classList.remove('is-invalid');
            errorElement.style.display = 'none';
        } else {
            docNumberInput.classList.add('is-invalid');
            errorElement.style.display = 'block';
        }
    }

    window.validateNumberRange = function(input, errorId, min, max) {
        const errorElement = document.getElementById(errorId);
        const value = parseInt(input.value, 10);

        if (isNaN(value) || value < min || value > max) {
            input.classList.add('is-invalid');
            errorElement.style.display = 'block';
        } else {
            input.classList.remove('is-invalid');
            errorElement.style.display = 'none';
        }
    }

    window.validateNotFutureYear = function(input, errorId) {
        const errorElement = document.getElementById(errorId);
        const inputYear = new Date(input.value).getFullYear();
        const currentYear = new Date().getFullYear();

        if (inputYear > currentYear) {
            input.classList.add('is-invalid');
            errorElement.style.display = 'block';
        } else {
            input.classList.remove('is-invalid');
            errorElement.style.display = 'none';
        }
    }

    window.validateLength = function(input, errorId, maxLength) {
        const errorElement = document.getElementById(errorId);
        if (input.value.length > maxLength) {
            errorElement.textContent = `Solo se permiten ${maxLength} caracteres.`;
            errorElement.style.display = 'block';
        } else {
            input.classList.remove('is-invalid');
            errorElement.style.display = 'none';
        }
    }

    function setupPolicyCheck(checkboxId, buttonId) {
        const checkbox = document.getElementById(checkboxId);
        const button = document.getElementById(buttonId);
        if (checkbox && button) {
            checkbox.addEventListener('change', function() {
                button.disabled = !this.checked;
            });
        }
    }

    // Function to fetch and populate grades dropdown
    async function populateGradesDropdown() {
        const gradeSelect = document.getElementById('id_grado_e');
        if (!gradeSelect) return;

        gradeSelect.innerHTML = '<option value="" disabled selected>Cargando grados...</option>'; // Reset and show loading

        try {
            const response = await fetch('../api/get_unique_grados.php');
            const result = await response.json();

            if (result.success && Array.isArray(result.data)) {
                gradeSelect.innerHTML = '<option value="" disabled selected>Selecciona un grado</option>'; // Default option
                result.data.forEach(grado => {
                    if (grado.grado_numero > 0) { // Exclude grade 0
                        const option = document.createElement('option');
                        option.value = grado.grado_numero;
                        option.textContent = `Grado ${grado.grado_numero}`;
                        gradeSelect.appendChild(option);
                    }
                });
            } else {
                gradeSelect.innerHTML = '<option value="" disabled selected>Error al cargar grados</option>';
                console.error('Error al cargar grados:', result.message);
            }
        } catch (error) {
            gradeSelect.innerHTML = '<option value="" disabled selected>Error de red</option>';
            console.error('Error de red al cargar grados:', error);
        }
    }

    // Lógica para habilitar/deshabilitar botones de envío
    setupPolicyCheck('politica-acudiente', 'btn-acudiente');
    setupPolicyCheck('politica-profesor', 'btn-profesor');
    setupPolicyCheck('politica-estudiante', 'btn-estudiante');
    setupPolicyCheck('politica-admin', 'btn-admin');

    handleFormSubmit('form-acudiente', 'acudiente');
    handleFormSubmit('form-profesor', 'profesor');
    handleFormSubmit('form-estudiante', 'estudiante');
    handleFormSubmit('form-admin', 'admin');

    // js/session_checker.js content
    const INACTIVITY_TIMEOUT = 60 * 1000; // 30 segundos en milisegundos
    let inactivityTimer;

    function resetTimer() {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(logoutUser, INACTIVITY_TIMEOUT);
    }

    function logoutUser() {
        window.location.href = '../inicia.html?error=inactive_frontend';
    }

    document.addEventListener('mousemove', resetTimer);
    document.addEventListener('keydown', resetTimer);
    document.addEventListener('click', resetTimer);
    document.addEventListener('scroll', resetTimer);

    resetTimer();

    // --- LÓGICA PARA MANEJAR LAS PESTAÑAS (TABS) ---
    const tabs = document.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.tab-content');
    const body = document.body;

    if (tabs.length > 0 && contents.length > 0) {
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Quitar clase activa de todos los tabs y contenidos
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));

                // Aplicar clase activa al tab y contenido seleccionados
                tab.classList.add('active');
                const activeContent = document.getElementById(tab.dataset.tab);
                if (activeContent) {
                    activeContent.classList.add('active');
                }

                // Cambiar el tema del body para que coincida con la pestaña activa
                if (tab.dataset.theme) {
                    body.className = body.className.replace(/theme-[\w-]+/g, '');
                    body.classList.add(tab.dataset.theme);
                }

                // If student tab is clicked, populate grades
                if (tab.dataset.tab === 'estudiante') {
                    populateGradesDropdown();
                }
            });
        });
    }








});