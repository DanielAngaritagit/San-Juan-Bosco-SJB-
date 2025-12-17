<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
// 1. Verificación de Seguridad
require_once '../php/verificar_sesion.php';
if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../inicia.html?status=unauthorized_role");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="icon" type="image/png" href="/SJB/multimedia/administrador/escudo.png">
    <link rel="stylesheet" href="../style/agregar_usuario.css"> <!-- Reutilizamos el estilo -->
    <link rel="icon" type="image/x-icon" href="../multimedia/inicio_sesion/escudo.png">
</head>
</head>
<body>
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">Cambio de contraseña - Administrador</span>
        </div>
    </div>

    <div class="main-container">
        <aside class="menu-container" id="menu-container">
            <div class="profile">
                <img src="" alt="Usuario" class="profile-pic">
                <h3 class="user-name">Cargando...</h3>
                <p class="user-role">Cargando...</p>
            </div>
            <nav class="menu">
            <ul>
                <li><a href="admin.php" class="menu-link"><img src="../multimedia/administrador/home.png" alt=""> Inicio</a></li>
                <li><a href="agregar_usuario.php" class="menu-link"><img src="../multimedia/administrador/agregar-usuario.png" alt=""> Agregar Usuario</a></li>
                <li><a href="asignacion_profesores.php" class="menu-link"><img src="../multimedia/administrador/asignar.png" alt=""> Asignación de Profesores</a></li>
                <li><a href="calendario.php" class="menu-link"><img src="../multimedia/administrador/calendario.png" alt=""> Calendario</a></li>
                <li><a href="perfil.php" class="menu-link"><img src="../multimedia/administrador/perfil-usuario.png" alt=""> Perfil</a></li>
                <li><a href="pqrsf.php" class="menu-link"><img src="../multimedia/administrador/pqrsf.png" alt=""> PQRSF</a></li>
                <li><a href="ayuda.php" class="menu-link"><img src="../multimedia/administrador/ayuda.png" alt=""> Ayuda</a></li>
                <li><a href="cambio_contrasena.php" class="menu-link"><img src="../multimedia/administrador/cambiar-la-contrasena.png" alt=""> Cambiar Contraseñas</a></li>
                <li><a href="../logout.php" class="menu-link"><img src="../multimedia/administrador/cerrar_sesion.png" alt=""> Cerrar Sesión</a></li>
            </ul>
            </nav>
        </aside>

        <main class="content-container">
            <div class="container">
                <header>
                    <h1>Restablecer Contraseña de Usuario</h1>
                    <p>Ingrese el número de documento del usuario y la nueva contraseña que desea asignarle.</p>
                </header>

                <div id="message-container" class="message" style="display: none;"></div>

                <div class="tab-content active">
                    <form id="form-reset-password">
                        <div class="form-group">
                            <label for="no_documento" class="required">No. de Documento del Usuario</label>
                            <input type="text" id="no_documento" name="no_documento" required placeholder="Ej: 1095302731">
                        </div>
                        <div class="form-group">
                            <label for="new_password" class="required">Nueva Contraseña</label>
                            <input type="text" id="new_password" name="new_password" required placeholder="La nueva contraseña para el usuario">
                        </div>
                        <button type="submit" class="btn-success"><i class="fas fa-key"></i> Restablecer Contraseña</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <footer>
        <p>© 2025 Colegio San Juan Bosco. Todos los derechos reservados.</p>
        <p>Autores y Desarrolladores: Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita</p>
    </footer>

    <script src="../js/user_profile_manager.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form-reset-password');
            const messageContainer = document.getElementById('message-container');

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                const button = form.querySelector('button[type="submit"]');
                
                button.disabled = true;
                button.innerHTML = 'Procesando...';

                fetch('../api/admin_reset_password.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    messageContainer.innerHTML = result.message;
                    if (result.success) {
                        messageContainer.className = 'message success';
                        form.reset();
                    } else {
                        messageContainer.className = 'message error';
                    }
                    messageContainer.style.display = 'flex';
                })
                .catch(error => {
                    messageContainer.textContent = 'Error de red: ' + error.message;
                    messageContainer.className = 'message error';
                    messageContainer.style.display = 'flex';
                })
                .finally(() => {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-key"></i> Restablecer Contraseña';
                });
            });
        });
    </script>
    <script src="../js/menu.js"></script>
</body>
</html>