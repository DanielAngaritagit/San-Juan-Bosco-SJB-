<!-- Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Andrea Gabriel Jaimes Oviedo, Keiner Daniel Bautista Angarita">
    <meta name="author" content="Colegio San Juan Bosco">
    <meta name="copyright" content="Colegio San Juan Bosco">
    <link rel="icon" type="image/x-icon" href="../multimedia/inicio_sesion/escudo.png">
    <title>Estudiante</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../style/estu.css">
    <style>
        /* Estilos para tablas personalizadas */
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden; /* Para que los bordes redondeados se apliquen correctamente */
        }

        .custom-table th,
        .custom-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .custom-table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .custom-table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .custom-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Estilos para tablas tipo acordeón */
        .custom-table-accordion .accordion-header {
            cursor: pointer;
            background-color: #e9ecef;
            font-weight: bold;
        }

        .custom-table-accordion .accordion-content {
            display: none;
            padding: 10px 15px;
            background-color: #fdfdfd;
            border-top: 1px solid #e0e0e0;
        }

        .custom-table-accordion .accordion-header.active + .accordion-content {
            display: table-row-group; /* O block, dependiendo de la estructura */
        }

        /* Estilos para la responsividad de tablas */
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-responsive > .custom-table {
            margin-bottom: 0; /* Eliminar margen inferior si está dentro de table-responsive */
        }

        /* Pequeños ajustes para mejorar la apariencia general */
        .custom-table thead th:first-child {
            border-top-left-radius: 8px;
        }

        .custom-table thead th:last-child {
            border-top-right-radius: 8px;
        }

        .custom-table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 8px;
        }

        .custom-table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 8px;
        }
        /* Estilos para las tarjetas de resumen (summary-cards) */
        .summary-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px; /* Espacio entre las tarjetas */
            justify-content: space-around;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .summary-cards .card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 25px;
            display: flex;
            align-items: center;
            flex-basis: calc(33% - 20px); /* Aproximadamente 3 tarjetas por fila con espacio */
            min-width: 280px; /* Ancho mínimo para evitar que se hagan demasiado pequeñas */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .summary-cards .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .summary-cards .card-icon {
            font-size: 2.5rem;
            color: #007bff; /* Color primario de Bootstrap */
            margin-right: 20px;
            padding: 15px;
            background-color: #e9f5ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .summary-cards .card-info h3 {
            margin: 0 0 8px 0;
            font-size: 1.2rem;
            color: #333;
        }

        .summary-cards .card-info p {
            margin: 0;
            font-size: 1.8rem;
            font-weight: bold;
            color: #0056b3;
        }

        /* Colores específicos para cada tipo de tarjeta si se desea */
        .overall-average-card .card-icon {
            color: #28a745; /* Verde */
            background-color: #e6ffe6;
        }
        .overall-average-card .card-info p {
            color: #28a745;
        }

        .period-status-card .card-icon {
            color: #ffc107; /* Amarillo */
            background-color: #fff8e6;
        }
        .period-status-card .card-info p {
            color: #ffc107;
        }

        .overall-performance-summary-card .card-icon {
            color: #17a2b8; /* Azul claro */
            background-color: #e0f7fa;
        }
        .overall-performance-summary-card .card-info p {
            color: #17a2b8;
        }

        /* Media queries para responsividad */
        @media (max-width: 992px) {
            .summary-cards .card {
                flex-basis: calc(50% - 20px); /* 2 tarjetas por fila en pantallas medianas */
            }
        }

        @media (max-width: 768px) {
            .summary-cards .card {
                flex-basis: 100%; /* 1 tarjeta por fila en pantallas pequeñas */
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
            <span class="module-name">Inicio - Estudiante</span>
        </div>
    </div>

    <div class="main-container">
        <div class="menu-container" id="menu-container">
            <div class="profile">
                <img src="../multimedia/pagina_principal/usuario.png" alt="Usuario" class="profile-pic">
                <div class="profile-info">
                    <h3 class="user-name">Cargando...</h3>
                    <p class="user-role">Cargando...</p>
                </div>
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

        <div class="content-container">
            <div class="dashboard-header">
                <h1 id="student-name">Cargando nombre...</h1>
                <p id="student-details" class="student-details-container">Cargando detalles...</p>
            </div>

            <div class="summary-cards">
                <div class="card overall-average-card">
                    <div class="card-icon"><i class="fas fa-graduation-cap"></i></div>
                    <div class="card-info">
                        <h3>Promedio General</h3>
                        <p id="overall-average">0.00</p>
                    </div>
                </div>
                <div class="card period-status-card">
                    <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="card-info">
                        <h3>Estado del Período</h3>
                        <p id="period-status">Cargando...</p>
                    </div>
                </div>
                <div class="card overall-performance-summary-card">
                    <div class="card-icon"><i class="fas fa-star"></i></div>
                    <div class="card-info">
                        <h3>Resumen de Rendimiento</h3>
                        <p id="overall-performance-summary">Cargando...</p>
                    </div>
                </div>
            </div>

            <div class="academic-performance-section">
                <h2>Rendimiento Académico por Materia</h2>
                <div id="academic-performance-container">
                    <p>Cargando rendimiento académico...</p>
                </div>
            </div>

            <div class="detailed-grades-section">
                <h2>Notas Detalladas</h2>
                <button class="print-button" onclick="window.print()">📄 Imprimir Reporte</button>
                <div id="detailed-grades-container">
                    <p>Cargando notas detalladas...</p>
                </div>
            </div>

            <div class="chart-section">
                <h2>Calificaciones por Materia</h2>
                <div>
                    <canvas id="subjectGradesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2025 Colegio San Juan Bosco. Todos los derechos reservados.</p>
        <p>Autores y Desarrolladores: Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita</p>
    </footer>

    <script src="../lib/chart/chart.min.js"></script>
    <script src="../js/user_profile_manager.js?v=<?php echo time(); ?>"></script>
    <script src="../js/estu.js?v=<?php echo time(); ?>"></script>
    <script src="../js/inactivity_timer.js"></script>
    <script src="../js/menu.js"></script>
</body>
</html>