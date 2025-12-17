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
        fetch('../api/get_notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notificationsPanel.innerHTML = data.data.map(notif => `
                        <div class="panel-item">
                            <p>${notif.mensaje}</p>
                            <small>${new Date(notif.fecha).toLocaleString()}</small>
                        </div>
                    `).join('');

                    document.getElementById('notification-count').textContent = data.data.length;
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Cargar mensajes de correo
    function loadCorreo() {
        fetch('../api/get_correo.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    correoPanel.innerHTML = data.data.map(msg => `
                        <div class="panel-item">
                            <p>${msg.mensaje}</p>
                            <small>${new Date(msg.fecha).toLocaleString()}</small>
                        </div>
                    `).join('');

                    document.getElementById('correo-count').textContent = data.data.length;
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Cargar notificaciones y mensajes al abrir los paneles
    notificationsButton.addEventListener('click', loadNotifications);
    correoButton.addEventListener('click', loadCorreo);
});

document.addEventListener('DOMContentLoaded', () => {
    // Elementos del DOM
    const menuToggle = document.getElementById('menu-toggle');
    const menuContainer = document.getElementById('menu-container');
    const contentContainer = document.querySelector('.content-container');

    // Validación inicial
    if (!menuToggle || !menuContainer) {
        console.error('Error: Elementos del menú no encontrados');
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
});