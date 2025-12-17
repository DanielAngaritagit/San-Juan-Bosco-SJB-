// mod.js - Script para manejo de modales
document.addEventListener('DOMContentLoaded', function() {
    // ========== Modal de Detalles ==========
    function mostrarDetallesModal(nombres, apellidos, telefono, imagen) {
        // Actualizar contenido del modal
        document.getElementById('modalNombre').textContent = `${nombres} ${apellidos}`;
        document.getElementById('modalNombres').textContent = nombres;
        document.getElementById('modalApellidos').textContent = apellidos;
        document.getElementById('modalTelefono').textContent = telefono;
        document.getElementById('modalImg').src = `multimedia/${imagen}`;
        
        // Mostrar modal
        document.getElementById('modalGenerico').style.display = 'flex';
    }

    function cerrarDetallesModal() {
        document.getElementById('modalGenerico').style.display = 'none';
    }

    // Cerrar al hacer clic fuera
    document.getElementById('modalGenerico').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarDetallesModal();
        }
    });

    // ========== Modal PQR ==========
    // Elementos del DOM
    const pqrModal = document.getElementById('pqrModal');
    const abrirModalBtn = document.getElementById('abrirModal');
    const cerrarModalBtns = document.querySelectorAll('.cerrar-modal, .btn-cancelar');
    const adjuntarBtn = document.querySelector('.btn-adjuntar');
    const formPQR = document.querySelector('.form-pqr');

    // Abrir modal PQR
    if (abrirModalBtn && pqrModal) {
        abrirModalBtn.addEventListener('click', () => {
            pqrModal.style.display = 'block';
        });
    }

    // Cerrar modal PQR (múltiples métodos)
    if (cerrarModalBtns.length > 0 && pqrModal) {
        cerrarModalBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                pqrModal.style.display = 'none';
            });
        });
    }

    // Cerrar al hacer clic fuera
    if (pqrModal) {
        window.addEventListener('click', (e) => {
            if (e.target === pqrModal) {
                pqrModal.style.display = 'none';
            }
        });
    }

    // Adjuntar archivo
    if (adjuntarBtn) {
        adjuntarBtn.addEventListener('click', () => {
            document.querySelector('input[type="file"]').click();
        });
    }

    // Envío de formulario
    if (formPQR) {
        formPQR.addEventListener('submit', (e) => {
            e.preventDefault();
            // Lógica de envío aquí
            alert('Formulario enviado correctamente');
            pqrModal.style.display = 'none';
        });
    }

    // ========== Exponer función para detalles ==========
    window.mostrarDetallesModal = mostrarDetallesModal;
});

document.addEventListener('DOMContentLoaded', () => {
    // Elementos del DOM
    const menuToggle = document.getElementById('menu-toggle');
    const menuContainer = document.getElementById('menu-container');
    const personalBienestarCardsContainer = document.getElementById('personal-bienestar-cards-container');

    // Validación inicial
    if (!menuToggle || !menuContainer || !personalBienestarCardsContainer) {
        console.error('Error: Elementos del menú o contenedor de tarjetas de personal de bienestar no encontrados');
        return;
    }

    // Abrir/cerrar menú móvil
    menuToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        menuContainer.classList.toggle('active');
    });

    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (menuContainer.classList.contains('active') && 
            !e.target.closest('#menu-container') && 
            !e.target.closest('#menu-toggle')) {
            menuContainer.classList.remove('active');
        }
    });

    // Resetear menú en pantallas grandes
    window.addEventListener('resize', () => {
        if (window.innerWidth > 991) {
            menuContainer.classList.remove('active');
        }
    });

    // Función para cargar personal de bienestar dinámicamente
    function loadPersonalBienestar() {
        fetch('../api/get_personal_bienestar.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    personalBienestarCardsContainer.innerHTML = ''; // Limpiar contenido existente
                    data.data.forEach(personal => {
                        const card = `
                            <div class="card">
                                <img src="../multimedia/administrador/bienestar.png" alt="Personal de bienestar">
                                <div class="card-content">
                                    <h3>${personal.nombres} ${personal.apellidos}</h3>
                                    <p>Especialidad: ${personal.especialidad}</p>
                                    <button class="ver-mas" onclick="mostrarDetallesModal(
                                        '${personal.nombres}',
                                        '${personal.apellidos}',
                                        'N/A', // No hay teléfono en la tabla de profesores
                                        'bienestar.png'
                                    )">Ver más</button>
                                </div>
                            </div>
                        `;
                        personalBienestarCardsContainer.innerHTML += card;
                    });
                } else {
                    console.error('Error al cargar personal de bienestar:', data.message);
                    personalBienestarCardsContainer.innerHTML = '<p>Error al cargar el personal de bienestar.</p>';
                }
            })
            .catch(error => {
                console.error('Error de red al cargar personal de bienestar:', error);
                personalBienestarCardsContainer.innerHTML = '<p>Error de conexión al cargar el personal de bienestar.</p>';
            });
    }

    // Cargar personal de bienestar al cargar la página
    loadPersonalBienestar();
});

// mod.js - Script para manejo de modales
document.addEventListener('DOMContentLoaded', function() {
    // ========== Modal de Detalles ==========
    function mostrarDetallesModal(nombres, apellidos, telefono, imagen) {
        // Actualizar contenido del modal
        document.getElementById('modalNombre').textContent = `${nombres} ${apellidos}`;
        document.getElementById('modalNombres').textContent = nombres;
        document.getElementById('modalApellidos').textContent = apellidos;
        document.getElementById('modalTelefono').textContent = telefono;
        document.getElementById('modalImg').src = `../multimedia/administrador/${imagen}`;
        
        // Mostrar modal
        document.getElementById('modalGenerico').style.display = 'flex';
    }

    function cerrarModal() {
        document.getElementById('modalGenerico').style.display = 'none';
    }

    // Cerrar al hacer clic fuera
    document.getElementById('modalGenerico').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });

    // ========== Modal PQR ==========
    // Elementos del DOM
    const pqrModal = document.getElementById('pqrModal');
    const abrirModalBtn = document.getElementById('abrirModal');
    const cerrarModalBtns = document.querySelectorAll('.cerrar-modal, .btn-cancelar');
    const adjuntarBtn = document.querySelector('.btn-adjuntar');
    const formPQR = document.querySelector('.form-pqr');

    // Abrir modal PQR
    if (abrirModalBtn && pqrModal) {
        abrirModalBtn.addEventListener('click', () => {
            pqrModal.style.display = 'block';
        });
    }

    // Cerrar modal PQR (múltiples métodos)
    if (cerrarModalBtns.length > 0 && pqrModal) {
        cerrarModalBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                pqrModal.style.display = 'none';
            });
        });
    }

    // Cerrar al hacer clic fuera
    if (pqrModal) {
        window.addEventListener('click', (e) => {
            if (e.target === pqrModal) {
                pqrModal.style.display = 'none';
            }
        });
    }

    // Adjuntar archivo
    if (adjuntarBtn) {
        adjuntarBtn.addEventListener('click', () => {
            document.querySelector('input[type="file"]').click();
        });
    }

    // Envío de formulario
    if (formPQR) {
        formPQR.addEventListener('submit', (e) => {
            e.preventDefault();
            // Lógica de envío aquí
            alert('Formulario enviado correctamente');
            pqrModal.style.display = 'none';
        });
    }

    // ========== Exponer función para detalles ==========
    window.mostrarDetallesModal = mostrarDetallesModal;
    window.cerrarModal = cerrarModal; // Exponer cerrarModal también
});

document.addEventListener('DOMContentLoaded', function () {
    const correoButton = document.getElementById('Correo');
    const correoPanel = document.getElementById('correo-panel');
    const notificationsButton = document.getElementById('notifications-button');
    const notificationsPanel = document.getElementById('notifications-panel');

    // Mostrar/ocultar panel de correo
    correoButton.addEventListener('click', function(event) {
        event.stopPropagation();
        correoPanel.style.display = correoPanel.style.display === 'block' ? 'none' : 'block';
        notificationsPanel.style.display = 'none';
    });

    // Mostrar/ocultar panel de notificaciones
    notificationsButton.addEventListener('click', function(event) {
        event.stopPropagation();
        notificationsPanel.style.display = notificationsPanel.style.display === 'block' ? 'none' : 'block';
        correoPanel.style.display = 'none';
    });

    // Ocultar paneles al hacer clic fuera de ellos
    document.addEventListener('click', function() {
        correoPanel.style.display = 'none';
        notificationsPanel.style.display = 'none';
    });

    // Evitar que los paneles se cierren al hacer clic dentro de ellos
    correoPanel.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    notificationsPanel.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    // Cargar notificaciones
    function loadNotifications() {
        fetch('backend.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                notificationsPanel.innerHTML = data.map(notif => `
                    <div class="panel-item">
                        <p>${notif.mensaje}</p>
                        <small>${new Date(notif.fecha).toLocaleString()}</small>
                    </div>
                `).join('');

                document.getElementById('notification-count').textContent = data.length;
            })
            .catch(error => console.error('Error:', error));
    }

    // Cargar mensajes de correo
    function loadCorreo() {
        fetch('backend.php?type=correo')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                correoPanel.innerHTML = data.map(msg => `
                    <div class="panel-item">
                        <p>${msg.mensaje}</p>
                        <small>${new Date(msg.fecha).toLocaleString()}</small>
                    </div>
                `).join('');

                document.getElementById('correo-count').textContent = data.length;
            })
            .catch(error => console.error('Error:', error));
    }

    // Cargar notificaciones y mensajes al abrir los paneles
    notificationsButton.addEventListener('click', loadNotifications);
    correoButton.addEventListener('click', loadCorreo);
});

document.addEventListener('DOMContentLoaded', function () {
    const correoButton = document.getElementById('Correo');
    const correoPanel = document.getElementById('correo-panel');
    const notificationsButton = document.getElementById('notifications-button');
    const notificationsPanel = document.getElementById('notifications-panel');

    // Mostrar/ocultar panel de correo
    correoButton.addEventListener('click', function(event) {
        event.stopPropagation();
        correoPanel.style.display = correoPanel.style.display === 'block' ? 'none' : 'block';
        notificationsPanel.style.display = 'none';
    });

    // Mostrar/ocultar panel de notificaciones
    notificationsButton.addEventListener('click', function(event) {
        event.stopPropagation();
        notificationsPanel.style.display = notificationsPanel.style.display === 'block' ? 'none' : 'block';
        correoPanel.style.display = 'none';
    });

    // Ocultar paneles al hacer clic fuera de ellos
    document.addEventListener('click', function() {
        correoPanel.style.display = 'none';
        notificationsPanel.style.display = 'none';
    });

    // Evitar que los paneles se cierren al hacer clic dentro de ellos
    correoPanel.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    notificationsPanel.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    // Cargar notificaciones
    function loadNotifications() {
        fetch('backend.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                notificationsPanel.innerHTML = data.map(notif => `
                    <div class="panel-item">
                        <p>${notif.mensaje}</p>
                        <small>${new Date(notif.fecha).toLocaleString()}</small>
                    </div>
                `).join('');

                document.getElementById('notification-count').textContent = data.length;
            })
            .catch(error => console.error('Error:', error));
    }

    // Cargar mensajes de correo
    function loadCorreo() {
        fetch('backend.php?type=correo')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                correoPanel.innerHTML = data.map(msg => `
                    <div class="panel-item">
                        <p>${msg.mensaje}</p>
                        <small>${new Date(msg.fecha).toLocaleString()}</small>
                    </div>
                `).join('');

                document.getElementById('correo-count').textContent = data.length;
            })
            .catch(error => console.error('Error:', error));
    }

    // Cargar notificaciones y mensajes al abrir los paneles
    notificationsButton.addEventListener('click', loadNotifications);
    correoButton.addEventListener('click', loadCorreo);
});