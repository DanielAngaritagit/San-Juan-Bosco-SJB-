<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();
if (!isset($_SESSION['id_log']) || $_SESSION['rol'] !== 'estudiante') {
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
    <link rel="icon" type="image/png" href="/SJB/multimedia/estudiante/escudo.png">
    <link rel="stylesheet" href="../style/ayuda_estu.css">
</head>
<body class="theme-estudiante">
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">Ayuda - Estudiante</span>
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
                <li><a href="estudiante.php" class="menu-link"><img src="../multimedia/estudiante/home.png" alt=""> Inicio</a></li>
                <li><a href="calendario.php" class="menu-link"><img src="../multimedia/estudiante/calendario.png" alt=""> Calendario</a></li>
                <li><a href="pqrsf.php" class="menu-link"><img src="../multimedia/estudiante/pqrsf.png" alt=""> PQRSF</a></li>
                <li><a href="perfil.php" class="menu-link"><img src="../multimedia/estudiante/perfil-usuario.png" alt=""> Perfil</a></li>
                <li><a href="ayuda.php" class="menu-link"><img src="../multimedia/estudiante/ayuda.png" alt=""> Ayuda</a></li>
                <li><a href="../logout.php" class="menu-link"><img src="../multimedia/estudiante/cerrar_sesion.png" alt=""> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <div class="main-container">
        <main class="content-container">
            <h1>Centro de Ayuda - Estudiante</h1>

            <div class="help-section">
                <h2>Rol de Estudiante</h2>
                <p>Como estudiante, esta plataforma te ayuda a llevar un control de tu rendimiento académico, estar al tanto de los eventos del colegio y comunicarte de manera efectiva con tus profesores y la administración. ¡Aprovecha estas herramientas para tener éxito en tus estudios!</p>
            </div>

            <div class="help-section">
                <h2>Apartados Principales</h2>
                
                <h3>Inicio</h3>
                <p>En el <b>Inicio</b>, encontrarás un resumen de tu desempeño académico. Podrás ver tus calificaciones en cada materia, tu promedio general y un gráfico que muestra la evolución de tu rendimiento a lo largo del tiempo. Esta es una excelente manera de ver tu progreso y saber en qué áreas necesitas concentrarte más.</p>

                <h3>Calendario</h3>
                <p>El <b>Calendario</b> te mantendrá informado sobre todas las actividades importantes del colegio, como fechas de exámenes, entrega de trabajos, eventos deportivos y culturales. ¡No te pierdas nada!</p>

                <h3>PQRSF</h3>
                <p>¿Tienes alguna pregunta, sugerencia o problema? El módulo de <b>PQRSF</b> es tu canal para comunicarte con el colegio. Desde aquí puedes enviar tus peticiones, quejas, reclamos, sugerencias o felicitaciones de manera formal y recibir una respuesta.</p>

                <h3>Ayuda</h3>
                <p>Si tienes alguna duda sobre cómo usar la plataforma, esta sección de <b>Ayuda</b> está para ti. Aquí encontrarás explicaciones sobre cada una de las herramientas a tu disposición. Si no encuentras lo que buscas, no dudes en preguntar a tus profesores o al personal administrativo.</p>
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