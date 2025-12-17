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
    <title>Toma de Asistencia</title>
    <link rel="icon" type="image/png" href="/SJB/multimedia/profesor/escudo.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="../style/asistencia.css" rel="stylesheet">
</head>
<body>
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">Asistencia - Profesor</span>
        </div>
    </div>

    <div class="main-container">
        <aside class="menu-container" id="menu-container">
            <div class="profile">
                <img src="../multimedia/pagina_principal/usuario.png" alt="Usuario" class="profile-pic" id="profile-pic">
                <h3 class="user-name">Cargando...</h3>
                <p class="user-role">Cargando...</p>
            </div>
            <nav class="menu">
                <ul>
                    <li><a href="profesor.php" class="menu-link"><img src="../multimedia/profesor/home.png" alt=""> Inicio</a></li>
                    <li><a href="asistencia.php" class="menu-link active"><img src="../multimedia/profesor/asistencia.png" alt=""> Tomar Asistencia</a></li>
                    <li><a href="calendario.php" class="menu-link"><img src="../multimedia/profesor/calendario.png" alt=""> Calendario</a></li>
                    <li><a href="calificar.php" class="menu-link"><img src="../multimedia/profesor/calificaciones.png" alt=""> Calificar</a></li>
                    <li><a href="pqrsf.php" class="menu-link"><img src="../multimedia/profesor/pqrsf.png" alt=""> PQRSF</a></li>
                    <li><a href="perfil.php" class="menu-link"><img src="../multimedia/profesor/perfil-usuario.png" alt=""> Perfil</a></li>
                    <li><a href="ayuda.php" class="menu-link"><img src="../multimedia/profesor/ayuda.png" alt=""> Ayuda</a></li>
                    <li><a href="../logout.php" class="menu-link"><img src="../multimedia/profesor/cerrar_sesion.png" alt=""> Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content-container" id="content-container">
            <div class="container-fluid">
                <header class="mb-4">
                    <h1><i class="fas fa-check-circle"></i> Toma de Asistencia</h1>
                    <p class="text-muted">Seleccione un grado para ver la lista de estudiantes y registrar la asistencia del día.</p>
                </header>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div id="mensaje-asistencia" class="mb-3"></div>
                        
                        <div class="form-group">
                            <label for="grados-select"><strong>Seleccione un Grado:</strong></label>
                            <select id="grados-select" class="form-control form-control-lg">
                                <option value="">Cargando grados...</option>
                            </select>
                        </div>

                        <div id="lista-estudiantes-container" class="mt-4" style="display:none;">
                            <h3 id="grado-seleccionado-titulo" class="mb-3"></h3>
                            <form id="asistencia-form">
                                <div class="card">
                                    <div class="card-header font-weight-bold">
                                        <div class="student-row">
                                            <span>Nombre del Estudiante</span>
                                            <span>Estado de Asistencia</span>
                                        </div>
                                    </div>
                                    <div id="lista-estudiantes" class="list-group list-group-flush">
                                        <!-- La lista de estudiantes se poblará aquí -->
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg mt-3">Guardar Asistencia</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <footer>
        <p>© 2025 Colegio San Juan Bosco. Todos los derechos reservados.</p>
        <p>Autores y Desarrolladores: Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="../js/user_profile_manager.js?v=<?php echo time(); ?>"></script>
    <script src="../js/asistencia.js?v=<?php echo time(); ?>"></script>
    <script src="../js/inactivity_timer.js"></script>
    <script src="../js/menu.js"></script>
</body>
</html>