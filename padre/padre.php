<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();
if (!isset($_SESSION['id_log']) || ($_SESSION['rol'] !== 'padre' && (!isset($_SESSION['is_acudiente']) || !$_SESSION['is_acudiente']))) {
    header('Location: ../inicia.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Andrea Gabriel Jaimes Oviedo, Keiner Daniel Bautista Angarita, Lizeth Zoraya Quimbayo Ortiz">
    <meta name="copyright" content="Colegio San Juan Bosco">
    <link rel="icon" type="image/png" href="../multimedia/padre/escudo.png">
    <title>Seguimiento Académico</title>
        <link rel="stylesheet" href="../style/padre_moderno.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">Inicio - Padre</span>
        </div>
        
    </div>

    <div class="main-container">
        <div class="menu-container" id="menu-container">
            <div class="profile">
                <img src="../multimedia/pagina_principal/usuario.png" alt="Usuario" class="profile-pic">
                <h3 class="user-name">Cargando...</h3>
                <p class="user-role">Cargando...</p>
            </div>
            <nav class="menu">
            <ul>
                <li><a href="padre.php" class="menu-link"><img src="../multimedia/padre/home.png" alt=""> Inicio</a></li>
                <li><a href="calendario.php" class="menu-link"><img src="../multimedia/padre/calendario.png" alt=""> Calendario</a></li>
                <li><a href="pqrsf.php" class="menu-link"><img src="../multimedia/padre/pqrsf.png" alt=""> PQRSF</a></li>
                <li><a href="perfil.php" class="menu-link"><img src="../multimedia/padre/perfil-usuario.png" alt=""> Perfil</a></li>
                <li><a href="ayuda.php" class="menu-link active"><img src="../multimedia/padre/ayuda.png" alt=""> Ayuda</a></li>
                <li><a href="../logout.php" class="menu-link"><img src="../multimedia/padre/cerrar_sesion.png" alt=""> Cerrar Sesión</a></li>
            </ul>
        </nav>
        </div>

        <main class="content-container">
            <!-- Contenido de notas de hijos -->
            <div class="container">
                <div class="child-selector-container" style="margin-bottom: 20px;">
                    <label for="child-selector" style="font-weight: bold; margin-right: 10px;">Seleccione un estudiante:</label>
                    <select id="child-selector" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;"></select>
                </div>

                <div class="summary-cards">
                    <div class="card">
                        <h3>Promedio General</h3>
                        <p id="overall-average"></p>
                    </div>
                    <div class="card">
                        <h3>Mejor Materia</h3>
                        <p id="best-subject"></p>
                    </div>
                    <div class="card">
                        <h3>Desempeño General</h3>
                        <p><span id="overall-performance" class="performance-tag"></span></p>
                    </div>
                    <div class="card">
                        <h3>Área a Mejorar</h3>
                        <p id="area-to-improve"></p>
                    </div>
                </div>

                <div class="student-header">
                    <img src="../multimedia/administrador/estudiante.png" alt="Foto del estudiante" class="student-photo" id="student-photo-img">
                    <div class="student-info">
                        <h2 id="student-name"></h2>
                        <p id="student-grade"></p>
                        <p id="student-age"></p>
                    </div>
                </div>

                <div id="grades-accordion-container" style="margin-top: 20px;">
                    <!-- El acordeón de calificaciones se insertará aquí -->
                </div>
            </div>
        </main>
    </div>

    <footer>
        <p>© 2025 Colegio San Juan Bosco. Todos los derechos reservados.</p>
        <p>Autores y Desarrolladores: Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita</p>
    </footer>

    <script src="../js/user_profile_manager.js?v=<?php echo time(); ?>"></script>
    <script src="../js/padre.js?v=<?php echo time(); ?>"></script> <!-- padre.js contiene la lógica específica del dashboard -->
    <script src="../js/inactivity_timer.js"></script>
    <script src="../js/menu.js"></script>
</body>
</html>