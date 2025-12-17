<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();
if (!isset($_SESSION['id_log']) || $_SESSION['rol'] !== 'profesor') {
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
    <link rel="icon" type="image/x-icon" href="../multimedia/inicio_sesion/escudo.png">
    <title>Ayuda</title>
    <link rel="icon" type="image/png" href="/SJB/multimedia/profesor/escudo.png">
        <link rel="stylesheet" href="../style/ayuda_prof.css">
</head>
<body class="theme-profesor">
    <!-- Barra Superior -->
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">Ayuda - Profesor</span>
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
                <li><a href="profesor.php" class="menu-link"><img src="../multimedia/profesor/home.png" alt=""> Inicio</a></li>
                <li><a href="asistencia.php" class="menu-link"><img src="../multimedia/profesor/asistencia.png" alt=""> Tomar Asistencia</a></li>
                <li><a href="calendario.php" class="menu-link"><img src="../multimedia/profesor/calendario.png" alt=""> Calendario</a></li>
                <li><a href="calificar.php" class="menu-link"><img src="../multimedia/profesor/calificaciones.png" alt=""> Calificar</a></li>
                <li><a href="pqrsf.php" class="menu-link"><img src="../multimedia/profesor/pqrsf.png" alt=""> PQRSF</a></li>
                <li><a href="perfil.php" class="menu-link"><img src="../multimedia/profesor/perfil-usuario.png" alt=""> Perfil</a></li>
                <li><a href="ayuda.php" class="menu-link active"><img src="../multimedia/profesor/ayuda.png" alt=""> Ayuda</a></li>
                <li><a href="../logout.php" class="menu-link"><img src="../multimedia/profesor/cerrar_sesion.png" alt=""> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <div class="main-container">
        <main class="content-container">
            <h1>Centro de Ayuda - Profesor</h1>

            <div class="help-section">
                <h2>Rol de Profesor</h2>
                <p>Como profesor, tienes acceso a herramientas para gestionar tus cursos, registrar calificaciones, comunicarte con estudiantes y padres, y seguir el progreso académico. Tu rol es clave para el desarrollo educativo de los estudiantes.</p>
            </div>

            <div class="help-section">
                <h2>Apartados Principales</h2>
                
                <h3>Inicio</h3>
                <p>El <b>Inicio</b> es tu panel de control, donde puedes ver un resumen de tus actividades, como las últimas matrículas en tus cursos, un gráfico del rendimiento de tus estudiantes (aprobados/reprobados) y los registros de asistencia recientes.</p>

                <h3>Calendario</h3>
                <p>Consulta el <b>Calendario</b> para ver los eventos académicos, reuniones y fechas importantes programadas por la administración. Esto te ayudará a mantenerte organizado y al tanto de las actividades del colegio.</p>

                <h3>Calificar</h3>
                <p>En la sección de <b>Calificar</b>, puedes registrar y actualizar las notas de tus estudiantes para cada una de las materias que impartes. Esta herramienta es fundamental para llevar un registro preciso del desempeño académico.</p>

                <h3>PQRSF</h3>
                <p>El módulo de <b>PQRSF</b> te permite comunicarte con la administración y los padres de familia. Puedes enviar peticiones, quejas, reclamos, sugerencias o felicitaciones, y también recibirás las que te sean asignadas.</p>

                <h3>Ayuda</h3>
                <p>Esta sección te proporciona una guía detallada sobre cómo utilizar las herramientas disponibles en tu perfil de profesor. Si tienes alguna duda, consulta esta sección o contacta al soporte técnico.</p>
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