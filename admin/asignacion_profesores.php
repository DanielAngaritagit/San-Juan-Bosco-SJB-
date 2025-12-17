<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
// 1. Verificación de Seguridad
// Incluye el script que verifica si hay una sesión activa y maneja la inactividad.
require_once '../php/verificar_sesion.php';

// Verifica si el rol del usuario es 'admin'. Si no, lo redirige.
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
    <title>Asignación de Profesores</title>
    <link rel="icon" type="image/x-icon" href="../multimedia/inicio_sesion/escudo.png">
        <link rel="stylesheet" href="../style/gestion_academica.css"> <!-- Module specific styles -->
        <style>
            /* ------------------------- Barra Superior ------------------------- */
            .top-bar {
                background-color: #DABE43;
                height: 50px;
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0 20px;
                position: sticky;
                top: 0;
                z-index: 1000;
            }

            .project-info {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                flex-grow: 1;
                text-align: center;
                line-height: 1.2;
            }

            /* ------------------------- Menú Lateral ------------------------- */
            .menu-container {
                width: 250px;
                background: #fff;
                height: calc(100vh - 50px);
                position: fixed;
                top: 50px;
                left: 0;
                transform: translateX(0); /* Por defecto visible en pantallas grandes */
                transition: transform 0.3s ease;
                z-index: 1000;
                overflow-y: auto;
                box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            }

            .menu-container.active {
                transform: translateX(0) !important; /* Asegura que esté visible cuando está activo */
                background-color: #f0f8ff !important; /* Color distintivo para depuración */
            }

            .menu-toggle {
                background-color: #DABE43;
                border: none;
                border-radius: 50%;
                cursor: pointer;
                display: none; /* Oculto por defecto en pantallas grandes, se muestra en media query */
                position: absolute;
                left: 20px;
                z-index: 1001 !important;
            }

            .menu-toggle img {
                width: 24px;
                height: 24px;
            }

            .profile {
                text-align: center;
                padding: 25px;
            }

            .profile img {
                width: 100px;
                height: 100px;
                border-radius: 50%;
            }

            .menu ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .menu ul li a {
                text-decoration: none;
                color: #333;
                display: flex;
                align-items: center;
                padding: 12px 25px;
                font-size: 15px;
            }

            .menu ul li a:hover {
                background: #f5f5f5;
            }

            .menu ul li a img {
                width: 20px;
                height: 20px;
                margin-right: 12px;
            }

            /* ------------------------- Contenido Principal ------------------------- */
            .main-container {
                margin-left: 250px; /* Por defecto con margen para el menú */
                padding: 20px;
                transition: margin-left 0.3s ease;
                flex: 1;
                position: relative;
                z-index: 1;
            }

            .main-container.active {
                margin-left: 250px !important; /* Asegura el margen cuando el menú está activo */
            }

            /* Media query para pantallas pequeñas */
            @media (max-width: 992px) {
                .menu-container {
                    transform: translateX(-100%) !important; /* Oculto por defecto en pantallas pequeñas */
                    width: 280px;
                    top: 0;
                    height: 100vh;
                }

                .menu-container.active {
                    transform: translateX(0) !important; /* Visible cuando está activo en pantallas pequeñas */
                }

                .main-container {
                    margin-left: 0 !important; /* Sin margen en pantallas pequeñas */
                }

                .main-container.active {
                    margin-left: 0 !important; /* Sin margen en pantallas pequeñas, incluso si el menú está activo */
                }

                .menu-toggle {
                    display: block !important; /* Visible en pantallas pequeñas */
                }
            }
        </style>
</head>
<body>
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">Asignacion de profesores - Administrador</span>
        </div>
    </div>

    <!-- Menú Lateral -->
    <div class="menu-container" id="menu-container">
        <div class="profile">
            <img src="../multimedia/pagina_principal/usuario.png" alt="Usuario" class="profile-pic" id="profile-pic">
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
    </div>

    <div class="main-container">
        <div class="content-container">
            <h1 class="main-title">Gestión de Asignación de Profesores</h1>

            <div class="form-section">
                <h2>Asignar Profesor a Curso</h2>
                <form id="assignmentForm">
                    <div class="form-group">
                        <label for="profesorSelect">Profesor:</label>
                        <select id="profesorSelect" class="form-control" required></select>
                    </div>
                    <div class="form-group">
                        <label for="cursoSelect">Grado/Sección:</label>
                        <select id="cursoSelect" class="form-control" required></select>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Asignación</button>
                </form>
            </div>

            <div class="table-section">
                <h2>Asignaciones Existentes</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Profesor</th>
                            <th>Especialidad</th>
                            <th>Grado</th>
                            <th>Sección</th>
                            <th>Materia</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="assignmentsTableBody">
                        <!-- Data will be loaded here by JavaScript -->
                    </tbody>
                </table>
            </div>    
        </div>
    </div>

    <footer>
        <p>© 2025 Colegio San Juan Bosco. Todos los derechos reservados.</p>
        <p>Autores y Desarrolladores: Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../js/user_profile_manager.js"></script>
    <script src="../js/asignacion_profesores.js"></script>
    <script src="../js/menu.js"></script>
</body>
</html>