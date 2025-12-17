<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
/**
 * Página principal del panel de Administrador.
 * 
 * Esta página es el punto de entrada al dashboard del administrador.
 * - Verifica la sesión y el rol del usuario.
 * - Muestra los widgets y herramientas principales del administrador.
 */

// 1. Verificación de Seguridad
// Incluye el script que verifica si hay una sesión activa y maneja la inactividad.
require_once '../php/verificar_sesion.php';

// Verifica si el rol del usuario es 'admin'. Si no, lo redirige a la página de inicio.
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
    <meta name="author" content="Andrea Gabriel Jaimes Oviedo, Keiner Daniel Bautista Angarita">
    <meta name="copyright" content="Colegio San Juan Bosco">
    <link rel="icon" type="image/x-icon" href="../multimedia/inicio_sesion/escudo.png">
    <title>Administrador</title>
    <link rel="icon" type="image/png" href="/SJB/multimedia/administrador/escudo.png">
    <link rel="stylesheet" href="../style/admin.css">
</head>
<body>
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">Inicio - Administrador</span>
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
                <?php if (isset($_SESSION['is_acudiente']) && $_SESSION['is_acudiente']): ?>
                    <li><a href="../padre/padre.php" class="menu-link"><img src="../multimedia/administrador/padre.png" alt=""> Acceder como Acudiente</a></li>
                <?php endif; ?>
                <li><a href="../logout.php" class="menu-link"><img src="../multimedia/administrador/cerrar_sesion.png" alt=""> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <div class="main-container">
        <div class="contenedor_p">
            <!-- Sección de Estadísticas -->
            <div class="estadisticas">
                <div class="stats-container">
                    <div class="stat-item">
                        <div class="stat-value"><span id="studentCount">0</span></div>
                        <img src="../multimedia/administrador/grados.png" alt="Estudiantes">
                        <div class="stat-label">Estudiantes</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><span id="parentCount">0</span></div>
                        <img src="../multimedia/administrador/padre.png" alt="Padres">
                        <div class="stat-label">Padres</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><span id="teacherCount">0</span></div>
                        <img src="../multimedia/administrador/profesor_s.png" alt="Profesores">
                        <div class="stat-label">Profesores</div>
                    </div>
                </div>

                

                
            </div>

            <!-- Gestión de Periodos Académicos -->
            <div class="card mt-4">
                <div class="card-header">Gestión de Periodos Académicos</div>
                <div class="card-body">
                    <form id="periodoForm">
                        <input type="hidden" id="periodoId" value="">
                        <div class="form-group">
                            <label for="nombrePeriodo">Seleccione el Periodo</label>
                            <select class="form-control" id="nombrePeriodo" required>
                                <option value="" disabled selected>Seleccione un periodo...</option>
                                <optgroup label="Bimestral">
                                    <option value="Periodo Bimestre 1">Periodo Bimestre 1</option>
                                    <option value="Periodo Bimestre 2">Periodo Bimestre 2</option>
                                    <option value="Periodo Bimestre 3">Periodo Bimestre 3</option>
                                    <option value="Periodo Bimestre 4">Periodo Bimestre 4</option>
                                </optgroup>
                                <optgroup label="Trimestral">
                                    <option value="Periodo Trimestre 1">Periodo Trimestre 1</option>
                                    <option value="Periodo Trimestre 2">Periodo Trimestre 2</option>
                                    <option value="Periodo Trimestre 3">Periodo Trimestre 3</option>
                                </optgroup>
                                
                            </select>
                            <div id="periodoFechas" style="margin-top: 10px; font-weight: bold;"></div>
                        </div>
                        <div class="form-group">
                            <label for="fechaInicio">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fechaInicio" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="fechaFin">Fecha de Fin</label>
                            <input type="date" class="form-control" id="fechaFin" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Periodo</button>
                        <button type="button" class="btn btn-secondary" id="cancelEdit">Cancelar Edición</button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">Periodos Existentes</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="periodosTableBody">
                                <!-- Periodos se cargarán aquí -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Calendario y Agenda -->
            <div class="calendar-agenda-container">

                <div class="agenda-container">
                    <h2>Agenda
                        <button id="add-event-btn" class="add-task-btn">
                            <i class="material-icons"></i> Nuevo evento
                        </button>
                    </h2>
                    <div id="add-event-form" class="add-task-form">
                        <div class="event-editor-grid">
                            <label for="event-date">Fecha</label>
                            <input type="date" id="event-date" class="task-input" required min="<?php echo date('Y-m-d'); ?>">
                            
                            <label for="event-time">Hora</label>
                            <input type="time" id="event-time" class="task-input" required>
                            
                            <label for="event-title">Título</label>
                            <input type="text" id="event-title" class="task-input" placeholder="Título" required>
                            
                            <label for="event-details">Detalles</label>
                            <textarea id="event-details" class="task-input event-details" placeholder="Detalles"></textarea>
                        </div>
                        <div class="form-buttons">
                            <button id="save-event-btn" class="save-btn">Guardar</button>
                            <button id="cancel-event-btn" class="cancel-btn">Cancelar</button>
                        </div>
                    </div>
                    <div class="eventos" id="events-container">
                        <!-- Eventos dinámicos -->
                    </div>
                </div>
            </div>
        </div>   
    </div> 
    <!-- Pie de Página -->
    <footer>
        <p>© 2025 Colegio San Juan Bosco. Todos los derechos reservados.</p>
        <p>Autores y Desarrolladores: Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita</p>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../lib/chart/chart.min.js"></script>
    <script src="../js/menu.js?v=<?php echo time(); ?>"></script>
    <script src="../js/user_profile_manager.js?v=<?php echo time(); ?>"></script>
    <script src="../js/main.js?v=<?php echo time(); ?>"></script>
    <script src="../js/periodos_academicos.js?v=<?php echo time(); ?>"></script>
</body>
</html>