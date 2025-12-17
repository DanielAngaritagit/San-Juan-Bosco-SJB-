<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();

if (!isset($_SESSION['id_log']) || $_SESSION['rol'] !== 'profesor') {
    header('Location: ../inicia.html');
    exit;
}

$id_profesor_logueado = $_SESSION['id_log'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profesor</title>
    <link rel="icon" type="image/png" href="/SJB/multimedia/profesor/escudo.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link href="../style/profesor.css" rel="stylesheet">
</head>
<body>
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">Inicio - Profesor</span>
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
                <li><a href="profesor.php" class="menu-link active"><img src="../multimedia/profesor/home.png" alt=""> Inicio</a></li>
                <li><a href="asistencia.php" class="menu-link"><img src="../multimedia/profesor/asistencia.png" alt=""> Tomar Asistencia</a></li>
                <li><a href="calendario.php" class="menu-link"><img src="../multimedia/profesor/calendario.png" alt=""> Calendario</a></li>
                <li><a href="calificar.php" class="menu-link"><img src="../multimedia/profesor/calificaciones.png" alt=""> Calificar</a></li>
                <li><a href="pqrsf.php" class="menu-link"><img src="../multimedia/profesor/pqrsf.png" alt=""> PQRSF</a></li>
                <li><a href="perfil.php" class="menu-link"><img src="../multimedia/profesor/perfil-usuario.png" alt=""> Perfil</a></li>
                <li><a href="ayuda.php" class="menu-link"><img src="../multimedia/profesor/ayuda.png" alt=""> Ayuda</a></li>
                <?php if (isset($_SESSION['is_acudiente']) && $_SESSION['is_acudiente']): ?>
                    <li><a href="../padre/padre.php" class="menu-link"><img src="../multimedia/profesor/padre.png" alt=""> Acceder como Acudiente</a></li>
                <?php endif; ?>
                <li><a href="../logout.php" class="menu-link"><img src="../multimedia/profesor/cerrar_sesion.png" alt=""> Cerrar Sesión</a></li>
            </ul>
        </nav>
        </aside>

        <main class="content-container" id="content-container">
            <div class="container-fluid">
                <header class="mb-4">
                    <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                    <p class="text-muted">Bienvenido, aquí tiene un resumen de su actividad.</p>
                </header>

                <div id="director-info" style="display:none;">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chalkboard-teacher"></i> Grado a Cargo: <span id="grado-cargo"></span></h5>
                        </div>
                        <div class="card-body">
                            <p>Como director de grupo, tienes acceso a información detallada de tu grado.</p>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user-graduate"></i> Estudiantes del Grado</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Apellido</th>
                                            <th>Documento</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody id="estudiantes-grado-body">
                                        <tr><td colspan="4" class="text-center">Cargando estudiantes...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <button id="show-all-students" class="btn btn-secondary btn-block mt-3" style="display:none;">Mostrar todos los estudiantes</button>
                        </div>
                    </div>
                </div>

                <!-- Widgets de Estadísticas -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card bg-primary text-white mb-4">
                            <div class="card-body">
                                <i class="fas fa-users"></i>
                                <h5 class="card-title">Estudiantes</h5>
                                <p class="card-text" id="total-estudiantes">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-info text-white mb-4">
                            <div class="card-body">
                                <i class="fas fa-book-open"></i>
                                <h5 class="card-title">Cursos</h5>
                                <p class="card-text" id="total-cursos">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card bg-success text-white mb-4">
                            <div class="card-body">
                                <i class="fas fa-check-circle"></i>
                                <h5 class="card-title">Promedio</h5>
                                <p class="card-text" id="promedio-general">0.0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Rendimiento por Estudiante</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="rendimientoCursosChart"></canvas>
                            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/user_profile_manager.js?v=<?php echo time(); ?>"></script>
    <script src="../js/prof_dashboard.js?v=<?php echo time(); ?>"></script>
    <script src="../js/inactivity_timer.js"></script>
    <input type="hidden" id="idProfesorLogueado" value="<?php echo $id_profesor_logueado; ?>">
    
    
    <script src="../js/menu.js"></script>
</body>
</html>