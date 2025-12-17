<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
// 1. Verificación de Seguridad
require_once '../php/verificar_sesion.php';
if ($_SESSION['rol'] !== 'profesor') {
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
    <meta name="author" content="Colegio San Juan Bosco">
    <meta name="copyright" content="Colegio San Juan Bosco">
    <link rel="icon" type="image/x-icon" href="../multimedia/inicio_sesion/escudo.png">
    <title>Calendario</title>
    <link rel="icon" type="image/png" href="/SJB/multimedia/profesor/escudo.png">
        <link rel="stylesheet" href="../style/cal_prof.css">
</head>
<body>
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/profesor/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">Calendario - Profesor</span>
        </div>
    </div>
    
    <div class="menu-container" id="menu-container">
        <div class="profile">
            <img src="../multimedia/pagina_principal/usuario.png" alt="Usuario" class="profile-pic" id="profile-pic">
            <h3 class="user-name">Cargando...</h3>
            <p class="user-role">Cargando...</p>
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
    </div>
    <div class="main-container">
        <div class="content-container">
            <div class="calendar-container">
                <div class="calendar-header">
                    <h2 class="calendar-title" id="monthYear"></h2>
                    <div class="calendar-controls">
                        <div class="view-toggle">
                            <button class="btn btn-view-active" onclick="switchView('month')">Mes</button>
                            <button class="btn" onclick="switchView('week')">Semana</button>
                        </div>
                        <button class="btn btn-icon" onclick="previous()">‹</button>
                        <button class="btn btn-primary" onclick="goToToday()">Hoy</button>
                        <button class="btn btn-icon" onclick="next()">›</button>
                        <button class="btn btn-primary" onclick="showEventForm()">+ Evento</button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="calendar" id="monthView">
                        <thead>
                            <tr class="calendar-header-row">
                                <th>Dom</th><th>Lun</th><th>Mar</th><th>Mié</th>
                                <th>Jue</th><th>Vie</th><th>Sáb</th>
                            </tr>
                        </thead>
                        <tbody id="calendar-body"></tbody>
                    </table>

                    <table class="calendar week-view" id="weekView" style="display: none;">
                        <thead>
                            <tr>
                                <th class="time-column"></th>
                                <th class="week-day-header">Dom</th>
                                <th class="week-day-header">Lun</th>
                                <th class="week-day-header">Mar</th>
                                <th class="week-day-header">Mié</th>
                                <th class="week-day-header">Jue</th>
                                <th class="week-day-header">Vie</th>
                                <th class="week-day-header">Sáb</th>
                            </tr>
                        </thead>
                        <tbody id="week-body"></tbody>
                    </table>
                </div>
            </div>

            <!-- === INICIO DEL MODAL CORREGIDO === -->
            <div id="eventModal" class="modal">
                <div class="modal-content">
                    <!-- Cabecera del Modal (fuera del form) -->
                    <div class="modal-header">
                        <h3 class="modal-title" id="modalTitle">Nuevo Evento</h3>
                        <span class="close" onclick="hideEventForm()">×</span>
                    </div>

                    <!-- El FORMULARIO ahora envuelve el BODY y el FOOTER -->
                    <form id="eventForm" onsubmit="saveEvent(event)">
                        
                        <!-- Cuerpo del Modal (con los inputs) -->
                        <div class="modal-body">
                            <input type="hidden" id="eventIndex" value="-1">

                            <div class="form-group">
                                <label for="eventName">Nombre del Evento:</label>
                                <input type="text" class="form-control" id="eventName" required>
                            </div>

                            <div class="form-group">
                                <label for="eventDescription">Descripción:</label>
                                <textarea class="form-control" id="eventDescription" rows="3"></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="startDate">Fecha Inicio:</label>
                                    <input type="date" class="form-control" id="startDate" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="endDate">Fecha Fin:</label>
                                    <input type="date" class="form-control" id="endDate" required min="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="startTime">Hora Inicio:</label>
                                    <input type="time" class="form-control" id="startTime" required>
                                </div>
                                <div class="form-group">
                                    <label for="endTime">Hora Fin:</label>
                                    <input type="time" class="form-control" id="endTime" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Color del Evento:</label>
                                <div class="color-options">
                                    <div class="color-option selected" style="background-color: #FFB6C1" data-color="#FFB6C1"></div>
                                    <div class="color-option" style="background-color: #B0E0E6" data-color="#B0E0E6"></div>
                                    <div class="color-option" style="background-color: #98FB98" data-color="#98FB98"></div>
                                    <div class="color-option" style="background-color: #E6E6FA" data-color="#E6E6FA"></div>
                                    <div class="color-option" style="background-color: #FFFACD" data-color="#FFFACD"></div>
                                </div>
                                <input type="hidden" id="selectedColor" value="#FFB6C1" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Destinatarios del Evento:</label>
                                <div class="destinatarios-options">
                                    <div class="destinatario-item">
                                        <input type="checkbox" id="dest_padre" name="destinatarios" value="padre">
                                        <label for="dest_padre">Padres</label>
                                    </div>
                                    <div class="destinatario-item">
                                        <input type="checkbox" id="dest_estudiante" name="destinatarios" value="estudiante">
                                        <label for="dest_estudiante">Estudiantes</label>
                                    </div>
                                    <div class="destinatario-item">
                                        <input type="checkbox" id="dest_profesor" name="destinatarios" value="profesor">
                                        <label for="dest_profesor">Profesores</label>
                                    </div>
                                </div>
                            </div>
                            <div id="lista-padres-container" class="destinatarios-list-container" style="display: none;"></div>
                            <div id="lista-estudiantes-container" class="destinatarios-list-container" style="display: none;"></div>
                            <div id="lista-profesores-container" class="destinatarios-list-container" style="display: none;"></div>
                        </div>

                        <!-- Pie de página del Modal (con los botones, DENTRO del form) -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" id="deleteBtn" onclick="deleteEvent()" style="display: none;">Eliminar</button>
                            <div>
                                <button type="button" class="btn btn-outline" onclick="hideEventForm()">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>
                    </form> <!-- El FORMULARIO CIERRA AQUÍ -->
                </div>
            </div>
            <!-- === FIN DEL MODAL CORREGIDO === -->
        </div>
    </div>

    <footer>
        <p>© 2025 Colegio San Juan Bosco. Todos los derechos reservados.</p>
        <p>Autores y Desarrolladores: Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita</p>
    </footer>

    <script src="../js/user_profile_manager.js"></script>
    <script src="../js/cale.js"></script>
    <script src="../js/menu.js"></script>
    
</body>
</html>