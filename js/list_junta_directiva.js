document.addEventListener('DOMContentLoaded', () => {
    // Elementos del DOM
    const menuToggle = document.getElementById('menu-toggle');
    const menuContainer = document.getElementById('menu-container');
    const juntaDirectivaCardsContainer = document.getElementById('junta-directiva-cards-container');

    // Validación inicial
    if (!menuToggle || !menuContainer || !juntaDirectivaCardsContainer) {
        console.error('Error: Elementos del menú o contenedor de tarjetas de junta directiva no encontrados');
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

    // Función para cargar miembros de la junta directiva dinámicamente
    function loadJuntaDirectiva() {
        fetch('../api/get_junta_directiva.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    juntaDirectivaCardsContainer.innerHTML = ''; // Limpiar contenido existente
                    data.data.forEach(miembro => {
                        const card = `
                            <div class="card">
                                <img src="../multimedia/administrador/junta_directiva.png" alt="Junta Directiva">
                                <div class="card-content">
                                    <h3>${miembro.nombres}</h3>
                                    <p>Rol: ${miembro.especialidad}</p>
                                    <button class="ver-mas" onclick="mostrarDetallesModal(
                                        '${miembro.nombres}',
                                        '',
                                        'N/A',
                                        'junta_directiva.png'
                                    )">Ver más</button>
                                </div>
                            </div>
                        `;
                        juntaDirectivaCardsContainer.innerHTML += card;
                    });
                } else {
                    console.error('Error al cargar la junta directiva:', data.message);
                    juntaDirectivaCardsContainer.innerHTML = '<p>Error al cargar la junta directiva.</p>';
                }
            })
            .catch(error => {
                console.error('Error de red al cargar la junta directiva:', error);
                juntaDirectivaCardsContainer.innerHTML = '<p>Error de conexión al cargar la junta directiva.</p>';
            });
    }

    // Cargar miembros de la junta directiva al cargar la página
    loadJuntaDirectiva();
});

// mod.js - Script para manejo de modales (copiado de list_profesor.js o list_estudiantes.js)
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