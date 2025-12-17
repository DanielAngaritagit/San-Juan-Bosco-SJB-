<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();
if (!isset($_SESSION['id_log']) || $_SESSION['rol'] !== 'padre') {
    header('Location: ../inicia.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Andrea Gabriel Jaimes Oviedo, Keiner Daniel Bautista Angarita">
    <meta name="copyright" content="Colegio San Juan Bosco">
    <link rel="icon" type="image/png" href="../multimedia/padre/escudo.png">
    <title>Ayuda - Padre</title>
    <link rel="stylesheet" href="../style/ayuda_padre.css">
</head>
<body class="theme-padre">
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">Ayuda - Padre</span>
        </div>
    </div>

    <!-- Menú Lateral -->
    <div class="menu-container" id="menu-container">
        <div class="profile">
            <img src="../multimedia/pagina_principal/usuario.png" alt="Usuario" class="profile-img" id="profile-img">
            <h3 id="profile-name">Cargando...</h3>
            <p id="profile-role">Cargando...</p>
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

    <!-- Contenido Principal -->
    <div class="main-container">
        <main class="content-container">
            <h1>Centro de Ayuda - Padre</h1>

            <div class="help-section">
                <h2>Rol de Padre</h2>
                <p>Como padre o acudiente, tu rol es fundamental para el seguimiento y apoyo del proceso educativo de tu hijo. La plataforma te brinda las herramientas necesarias para estar al tanto de su rendimiento académico, comunicarte con el colegio y participar activamente en su educación.</p>
            </div>

            <div class="help-section">
                <h2>Apartados Principales</h2>
                
                <h3>Inicio</h3>
                <p>El <b>Inicio</b> es tu portal principal para el seguimiento académico de tu hijo. Aquí podrás ver un resumen de sus calificaciones, su promedio general, la materia en la que más se destaca y aquella en la que podría necesitar más apoyo. Esta vista te permite tener un panorama claro y rápido de su progreso.</p>

                <h3>Calendario</h3>
                <p>En el <b>Calendario</b>, podrás consultar todos los eventos importantes del colegio, como reuniones, fechas de exámenes, actividades extracurriculares y días festivos. Esta herramienta te ayudará a estar siempre informado y a planificar con anticipación.</p>

                <h3>PQRSF</h3>
                <p>El sistema de <b>PQRSF</b> (Peticiones, Quejas, Reclamos, Sugerencias y Felicitaciones) es tu canal de comunicación directo con el colegio. A través de este módulo, puedes enviar tus inquietudes o sugerencias a la administración o a los profesores, y hacer seguimiento de tus solicitudes.</p>

                <h3>Ayuda</h3>
                <p>Esta sección está diseñada para resolver tus dudas sobre el uso de la plataforma. Si necesitas ayuda para navegar en el sistema o entender alguna de sus funcionalidades, aquí encontrarás la información que necesitas.</p>
            </div>
        </main>
    </div>

    <!-- Pie de Página -->
    <footer>
        <p>© 2025 Colegio San Juan Bosco. Todos los derechos reservados.</p>
        <p>Autores y Desarrolladores: Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita</p>
    </footer>

    <script src="../js/user_profile_manager.js"></script>
    <script src="../js/ayuda.js"></script>
    <script src="../js/menu.js"></script>
</body>
</html>