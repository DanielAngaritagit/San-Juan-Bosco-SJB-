<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();
require_once '../php/conexion.php';

if (!isset($_SESSION['id_log']) || $_SESSION['rol'] !== 'profesor') {
    header('Location: ../inicia.html');
    exit;
}

$id_profesor_logueado = null;
if (isset($_SESSION['id_profesor'])) {
    $id_profesor_logueado = $_SESSION['id_profesor'];
} else {
    $stmtProfesor = $pdo->prepare("SELECT id_profesor FROM tab_profesores WHERE id_log = :id_log");
    $stmtProfesor->bindParam(':id_log', $_SESSION['id_log'], PDO::PARAM_INT);
    $stmtProfesor->execute();
    $profesor = $stmtProfesor->fetch(PDO::FETCH_ASSOC);
    if ($profesor) {
        $_SESSION['id_profesor'] = $profesor['id_profesor'];
        $id_profesor_logueado = $profesor['id_profesor'];
    } else {
        // Handle the case where the professor is not found
        // You can redirect to an error page or display a message
        die('Error: No se pudo encontrar el ID del profesor.');
    }
}

// Obtener los tipos de evaluación del ENUM
$stmtTipos = $pdo->query("SELECT unnest(enum_range(NULL::tipo_evaluacion_enum)) AS tipo");
$tipos_evaluacion = $stmtTipos->fetchAll(PDO::FETCH_COLUMN);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificar</title>
    <link rel="icon" type="image/png" href="/SJB/multimedia/profesor/escudo.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="../style/calificar.css" rel="stylesheet">
</head>
<body>
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">Calificar - Profesor</span>
        </div>
    </div>

    <div class="main-container">
        <aside class="menu-container" id="menu-container">
            <div class="profile">
                <img src="../multimedia/pagina_principal/usuario.png" alt="Usuario" class="profile-pic" id="profile-pic">
                <div class="profile-info">
                    <h3 class="user-name">Cargando...</h3>
                    <p class="user-role">Cargando...</p>
                </div>
            </div>
            <nav class="menu">
            <ul>
                <li><a href="profesor.php" class="menu-link"><img src="../multimedia/profesor/home.png" alt=""> Inicio</a></li>
                <li><a href="asistencia.php" class="menu-link"><img src="../multimedia/profesor/asistencia.png" alt=""> Tomar Asistencia</a></li>
                <li><a href="calendario.php" class="menu-link"><img src="../multimedia/profesor/calendario.png" alt=""> Calendario</a></li>
                <li><a href="calificar.php" class="menu-link active"><img src="../multimedia/profesor/calificaciones.png" alt=""> Calificar</a></li>
                <li><a href="pqrsf.php" class="menu-link"><img src="../multimedia/profesor/pqrsf.png" alt=""> PQRSF</a></li>
                <li><a href="perfil.php" class="menu-link"><img src="../multimedia/profesor/perfil-usuario.png" alt=""> Perfil</a></li>
                <li><a href="ayuda.php" class="menu-link"><img src="../multimedia/profesor/ayuda.png" alt=""> Ayuda</a></li>
                <li><a href="../logout.php" class="menu-link"><img src="../multimedia/profesor/cerrar_sesion.png" alt=""> Cerrar Sesión</a></li>
            </ul>
        </nav>
        </aside>

        <main class="content-container" id="content-container">
            <div class="container-fluid">
                <header class="mb-4 text-center">
                    <h1><i class="fas fa-pencil-alt"></i> Módulo de Calificación</h1>
                    <p class="text-muted">Seleccione curso, estudiante y tipo de evaluación para registrar una nueva nota.</p>
                </header>

                <!-- Formulario de Calificación -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Registrar Nueva Calificación</h5>
                    </div>
                    <div class="card-body">
                        <form id="calificacionForm">
                            <div class="form-row">
                                <div class="col-12 col-md-4 form-group">
                                    <label for="gradoSelect"><i class="fas fa-chalkboard"></i> Grado</label>
                                    <select class="form-control" id="gradoSelect" required>
                                        <option value="" selected disabled>Cargando grados...</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4 form-group">
                                    <label for="materiaSelect"><i class="fas fa-book"></i> Materia</label>
                                    <select class="form-control" id="materiaSelect" required disabled>
                                        <option value="" selected disabled>Seleccione un grado primero</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4 form-group">
                                    <label for="estudianteSelect"><i class="fas fa-user-graduate"></i> Estudiante</label>
                                    <select class="form-control" id="estudianteSelect" required disabled>
                                        <option value="" selected disabled>Seleccione una materia primero</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 col-md-4 form-group">
    <label for="tipoEvaluacionSelect"><i class="fas fa-clipboard-list"></i> Tipo de Evaluación</label>
    <select class="form-control" id="tipoEvaluacionSelect" required>
        <option value="" selected disabled>Seleccione un tipo</option>
        <?php foreach ($tipos_evaluacion as $tipo): ?>
            <option value="<?php echo htmlspecialchars($tipo); ?>"><?php echo htmlspecialchars($tipo); ?></option>
        <?php endforeach; ?>
    </select>
</div>
                                <div class="col-12 col-md-4 form-group"><label for="calificacionInput"><i class="fas fa-star-half-alt"></i> Calificación (0.0 - 5.0)</label><input type="text" class="form-control" id="calificacionInput" step="0.1" min="0" max="5" required placeholder="Ej: 4.5"></div>
                                <div class="col-12 col-md-4 form-group"><label for="fechaCalificacion"><i class="fas fa-calendar-alt"></i> Fecha</label><input type="date" class="form-control" id="fechaCalificacion" required min="<?php echo date('Y-m-d'); ?>"></div>
                            </div>
                            <div class="form-group"><label for="comentarioText"><i class="fas fa-comment"></i> Comentarios (Opcional)</label><textarea class="form-control" id="comentarioText" rows="3" placeholder="Añade una observación sobre el desempeño del estudiante..."></textarea></div>
                            <input type="hidden" id="idCursoReal" name="idCursoReal">
                            <button type="submit" class="btn btn-success btn-block"><i class="fas fa-save"></i> Guardar Calificación</button>
                        </form>
                    </div>
                </div>

                <!-- Tabla de Calificaciones Recientes -->
                <div class="card shadow-sm">
                     <div class="card-header"><h5 class="mb-0"><i class="fas fa-history"></i> Calificaciones Recientes</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th id="header-grado" class="text-center">Grado</th>
                                        <th id="header-estudiante" style="display: none;">Estudiante</th>
                                        <th id="header-detalles" style="display: none;" colspan="5">Detalles de Calificaciones</th>
                                    </tr>
                                </thead>
                                <tbody id="calificacionesRecientesBody"><tr><td colspan="6" class="text-center">No hay calificaciones recientes.</td></tr></tbody>
                            </table>
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
    <script src="../js/calificar.js"></script>
    <input type="hidden" id="idProfesorLogueado" value="<?php echo $id_profesor_logueado; ?>">
    <script>
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('menu-container').classList.toggle('active');
            document.getElementById('content-container').classList.toggle('full-width');
        });
    </script>
    <script src="../js/menu.js"></script>
</body>
</html>