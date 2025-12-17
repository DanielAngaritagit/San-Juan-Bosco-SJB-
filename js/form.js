document.addEventListener('DOMContentLoaded', function () {
    // =================== NOTIFICACIONES Y CORREO ===================
    const correoButton = document.getElementById('Correo');
    const correoPanel = document.getElementById('correo-panel');
    const notificationsButton = document.getElementById('notifications-button');
    const notificationsPanel = document.getElementById('notifications-panel');

    // Mostrar/ocultar panel de correo
    if (correoButton && correoPanel) {
        correoButton.addEventListener('click', function(event) {
            event.stopPropagation();
            correoPanel.style.display = correoPanel.style.display === 'block' ? 'none' : 'block';
            if (notificationsPanel) notificationsPanel.style.display = 'none';
        });
    }

    // Mostrar/ocultar panel de notificaciones
    if (notificationsButton && notificationsPanel) {
        notificationsButton.addEventListener('click', function(event) {
            event.stopPropagation();
            notificationsPanel.style.display = notificationsPanel.style.display === 'block' ? 'none' : 'block';
            if (correoPanel) correoPanel.style.display = 'none';
        });
    }

    // Ocultar paneles al hacer clic fuera de ellos
    document.addEventListener('click', function() {
        if (correoPanel) correoPanel.style.display = 'none';
        if (notificationsPanel) notificationsPanel.style.display = 'none';
    });

    // Evitar que los paneles se cierren al hacer clic dentro
    if (correoPanel) {
        correoPanel.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }

    if (notificationsPanel) {
        notificationsPanel.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }

    // Cargar notificaciones
    function loadNotifications() {
        fetch('backend.php')
            .then(response => {
                if (!response.ok) throw new Error('Error en la red');
                return response.json();
            })
            .then(data => {
                if (!notificationsPanel) return;

                if (data.error) {
                    console.error(data.error);
                    notificationsPanel.innerHTML = `<p class="error">${data.error}</p>`;
                    return;
                }

                notificationsPanel.innerHTML = data.map(notif => `
                    <div class="panel-item">
                        <p>${notif.mensaje || 'Sin mensaje'}</p>
                        <small>${notif.fecha ? new Date(notif.fecha).toLocaleString() : 'Fecha no disponible'}</small>
                    </div>
                `).join('');

                const countElement = document.getElementById('notification-count');
                if (countElement) countElement.textContent = data.length;
            })
            .catch(error => {
                console.error('Error al cargar notificaciones:', error);
                if (notificationsPanel) notificationsPanel.innerHTML = `<p class="error">Error al cargar notificaciones</p>`;
            });
    }

    // Cargar mensajes de correo
    function loadCorreo() {
        fetch('backend.php?type=correo')
            .then(response => {
                if (!response.ok) throw new Error('Error en la red');
                return response.json();
            })
            .then(data => {
                if (!correoPanel) return;

                if (data.error) {
                    console.error(data.error);
                    correoPanel.innerHTML = `<p class="error">${data.error}</p>`;
                    return;
                }

                correoPanel.innerHTML = data.map(msg => `
                    <div class="panel-item">
                        <p>${msg.mensaje || 'Sin mensaje'}</p>
                        <small>${msg.fecha ? new Date(msg.fecha).toLocaleString() : 'Fecha no disponible'}</small>
                    </div>
                `).join('');

                const countElement = document.getElementById('correo-count');
                if (countElement) countElement.textContent = data.length;
            })
            .catch(error => {
                console.error('Error al cargar correos:', error);
                if (correoPanel) correoPanel.innerHTML = `<p class="error">Error al cargar mensajes</p>`;
            });
    }

    // Eventos para cargar datos
    if (notificationsButton) {
        notificationsButton.addEventListener('click', loadNotifications);
    }

    if (correoButton) {
        correoButton.addEventListener('click', loadCorreo);
    }

    // =================== MENÚ MÓVIL ===================
    const menuToggle = document.getElementById('menu-toggle');
    const menuContainer = document.getElementById('menu-container');

    if (menuToggle && menuContainer) {
        menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            menuContainer.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (menuContainer.classList.contains('active') && 
                !e.target.closest('#menu-container') && 
                !e.target.closest('#menu-toggle')) {
                menuContainer.classList.remove('active');
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 991) {
                menuContainer.classList.remove('active');
            }
        });
    } else {
        console.error('Elementos del menú no encontrados');
    }

    // =================== FORMULARIO DE PROFESOR ===================
    const btnAnadir = document.querySelector('.btn');
    
    if (btnAnadir) {
        btnAnadir.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Obtener todos los inputs
            const inputs = document.querySelectorAll('.input-row input, .date-input input');
            let isValid = true;
            const profesorData = {
                ids: [],
                nombres: [],
                apellidos: [],
                celulares: [],
                especialidades: [],
                fechaContratacion: ''
            };

            // Validar campos y recolectar datos
            inputs.forEach((input, index) => {
                if (!input.value.trim()) {
                    input.style.borderColor = '#e74c3c';
                    isValid = false;
                } else {
                    input.style.borderColor = '#ddd';
                    
                    // Organizar los datos según el campo
                    if (index < 2) profesorData.ids.push(input.value.trim());
                    else if (index < 4) profesorData.nombres.push(input.value.trim());
                    else if (index < 6) profesorData.apellidos.push(input.value.trim());
                    else if (index < 8) profesorData.celulares.push(input.value.trim());
                    else if (index < 10) profesorData.especialidades.push(input.value.trim());
                    else profesorData.fechaContratacion = input.value.trim();
                }
            });

            if (!isValid) {
                alert('Por favor complete todos los campos obligatorios');
                return;
            }

            // Aquí iría la lógica para enviar el formulario
            console.log('Datos del profesor:', profesorData);
            
            // Simular envío al servidor
            fetch('guardar_profesor.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(profesorData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Profesor añadido correctamente');
                    // Limpiar el formulario
                    inputs.forEach(input => input.value = '');
                } else {
                    alert('Error al guardar: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al conectar con el servidor');
            });
        });
    }

    // =================== MÁSCARA PARA FECHA ===================
    const fechaInput = document.querySelector('.date-input input');
    if (fechaInput) {
        fechaInput.addEventListener('input', function(e) {
            // Eliminar cualquier carácter que no sea número
            let value = this.value.replace(/\D/g, '');
            
            // Aplicar formato dd/mm/aaaa
            if (value.length > 2 && value.length <= 4) {
                value = value.replace(/^(\d{2})/, '$1/');
            } else if (value.length > 4) {
                value = value.replace(/^(\d{2})(\d{2})/, '$1/$2/');
            }
            
            // Limitar a 10 caracteres (dd/mm/aaaa)
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            
            this.value = value;
        });
    }

    // =================== VALIDACIÓN DE CELULAR ===================
    const celularInputs = document.querySelectorAll('input[type="tel"]');
    celularInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Permitir solo números y limitar a 10 dígitos
            this.value = this.value.replace(/\D/g, '').substring(0, 10);
        });
    });
});