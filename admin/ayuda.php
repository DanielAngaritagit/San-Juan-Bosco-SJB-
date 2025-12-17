<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
// 1. Verificación de Seguridad
require_once '../php/verificar_sesion.php';
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
    <title>Ayuda</title>
    <link rel="icon" type="image/png" href="/SJB/multimedia/administrador/escudo.png">
    <link rel="stylesheet" href="../style/ayuda_admin.css">
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
                <li><a href="../logout.php" class="menu-link"><img src="../multimedia/administrador/cerrar_sesion.png" alt=""> Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>

    <!-- Contenido Principal -->
    <div class="main-container">
        <main class="content-container">
            <h1>Centro de Ayuda - Administrador</h1>

            <div class="help-section">
                <h2>Rol de Administrador</h2>
                <p>Como administrador, tienes control total sobre la plataforma, incluyendo la gestión de usuarios, eventos, PQRSF y acceso a estadísticas generales. Este rol es fundamental para el buen funcionamiento y la organización del colegio dentro del sistema.</p>
            </div>

            <div class="help-section">
                <h2>Apartados Principales</h2>
                
                <h3>Inicio</h3>
                <p>El módulo de <b>Inicio</b> es tu panel de control principal, diseñado para administradores. Aquí encontrarás un resumen ejecutivo de las métricas más importantes del colegio, incluyendo el número total de estudiantes, padres y profesores registrados en el sistema. Además, se presentan gráficos interactivos que visualizan el rendimiento académico general y otras estadísticas relevantes para la toma de decisiones.</p>
                <p>Este módulo también te permite gestionar una <b>Lista de Pendientes</b> personal, donde puedes añadir, buscar, editar, marcar como completadas o eliminar tareas. Adicionalmente, dispones de una <b>Agenda</b> para gestionar eventos importantes.</p>
                <p>La seguridad de acceso está garantizada, ya que solo los usuarios autenticados con rol de administrador pueden acceder a este panel.</p>
                <ul>
                    <li><b>Estadísticas Clave:</b> Visualiza rápidamente el estado actual de la población estudiantil y docente.</li>
                    <li><b>Gráficos de Rendimiento:</b> Analiza tendencias y datos académicos para identificar áreas de mejora.</li>
                    <li><b>Lista de Pendientes:</b> Organiza tus actividades diarias y mantén un seguimiento de tus responsabilidades.</li>
                    <li><b>Agenda:</b> Gestiona y organiza eventos importantes.</li>
                    <li><b>Navegación:</b> Accede fácilmente a otras secciones administrativas como 'Agregar Usuario', 'Calendario', 'PQRSF' y 'Ayuda'.</li>
                </ul>

                <h3>Agregar Usuario</h3>
                <p>Este módulo te permite registrar nuevos usuarios en el sistema, asignándoles el rol correspondiente (Acudiente, Profesor o Estudiante). Es crucial para mantener la base de datos de usuarios actualizada y asegurar que todos los miembros de la comunidad educativa tengan acceso a sus respectivas funcionalidades.</p>
                <p>Cada tipo de usuario tiene un formulario específico que solicita la información necesaria para su registro. Asegúrate de completar todos los campos obligatorios para un registro exitoso. La información se envía a <code>guardar_usuario.php</code> y el módulo muestra mensajes de éxito o error.</p>
                <ul>
                    <li><b>Registro de Acudientes:</b> Ingresa los datos personales y de contacto de los padres o tutores. Se generará automáticamente una cuenta de acceso para ellos.</li>
                    <li><b>Registro de Profesores:</b> Añade la información del personal docente, incluyendo su especialidad y la materia que imparten. También se creará su cuenta de acceso.</li>
                    <li><b>Registro de Estudiantes:</b> Completa la ficha de datos del estudiante, incluyendo información socioeconómica y de salud. El sistema vinculará al estudiante con su acudiente y generará sus credenciales de acceso.</li>
                </ul>

                <h3>Calendario</h3>
                <p>El módulo de <b>Calendario</b> es una herramienta centralizada para la gestión de eventos académicos y administrativos. Como administrador, tienes la capacidad de crear, editar y eliminar eventos que serán visibles para los roles específicos (padres, estudiantes, profesores) o usuarios/cursos seleccionados.</p>
                <p>Este módulo ofrece vistas de mes y semana, permitiéndote navegar fácilmente entre periodos y volver al día actual. Puedes añadir nuevos eventos a través de un formulario modal, especificando el nombre, fechas y horas de inicio/fin, un color distintivo y los destinatarios del evento (padres, estudiantes, profesores, o incluso usuarios/cursos específicos).</p>
                <ul>
                    <li><b>Visualización de Calendario:</b> Muestra eventos en vistas de mes y semana.</li>
                    <li><b>Navegación:</b> Permite moverse entre meses/semanas y volver al día actual.</li>
                    <li><b>Crear Evento:</b> Añade nuevos eventos con detalles como nombre, fechas, horas, color y destinatarios.</li>
                    <li><b>Editar Evento:</b> Modifica eventos existentes directamente desde el calendario.</li>
                    <li><b>Eliminar Evento:</b> Elimina eventos que ya no sean relevantes.</li>
                    <li><b>Filtrado y Visualización:</b> Los usuarios de otros roles solo verán los eventos que les sean relevantes según la configuración de destinatarios.</li>
                </ul>

                <h3>PQRSF (Peticiones, Quejas, Reclamos, Sugerencias y Felicitaciones)</h3>
                <p>El módulo de <b>PQRSF</b> te permite gestionar de manera eficiente todas las comunicaciones recibidas de la comunidad educativa. Aquí puedes visualizar, procesar y responder a las peticiones, quejas, reclamos, sugerencias y felicitaciones enviadas por estudiantes, padres y profesores.</p>
                <p>Este módulo ofrece un listado de todas las PQRSF, con opciones para filtrarlas por tipo y estado, y buscar por palabras clave. También puedes crear nuevas PQRSF a través de un formulario modal, especificando el tipo, descripción, destinatario y adjuntando archivos. Además, puedes ver los detalles completos de cada PQRSF.</p>
                <ul>
                    <li><b>Ver Detalles:</b> Haz clic en cualquier entrada de PQRSF para acceder a su descripción completa, el nombre y contacto del solicitante, la fecha de creación y el destinatario asignado.</li>
                    <li><b>Cambiar Estado:</b> A medida que una PQRSF avanza en su proceso de resolución, puedes actualizar su estado (por ejemplo, de 'Pendiente' a 'En Proceso' o 'Resuelto'). Esto ayuda a mantener un seguimiento claro y transparente.</li>
                    <li><b>Añadir Comentarios/Respuestas:</b> Puedes registrar comentarios internos o enviar respuestas directas al solicitante para mantenerlo informado sobre el progreso o la resolución de su caso.</li>
                    <li><b>Filtrar y Buscar:</b> Utiliza las opciones de filtrado por tipo (Petición, Queja, etc.), estado o la barra de búsqueda para encontrar rápidamente PQRSF específicas.</li>
                    <li><b>Crear PQRSF:</b> Abre un formulario modal para registrar nuevas PQRSF con todos los detalles necesarios y adjuntar archivos.</li>
                </ul>

                <h3>Ayuda</h3>
                <p>Esta sección, que estás leyendo actualmente, está diseñada para proporcionarte una guía completa sobre el uso de la plataforma. Aquí encontrarás explicaciones detalladas de cada módulo y sus funcionalidades, consejos para la navegación y solución de problemas comunes. Si tienes alguna duda que no se resuelva aquí, por favor, contacta al soporte técnico.</p>
            </div>
        </main>
        
    </div>
    <footer>
        <p>© 2025 Colegio San Juan Bosco. Todos los derechos reservados.</p>
        <p>Autores y Desarrolladores: Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita</p>
    </footer>

    <script src="../js/user_profile_manager.js"></script>
    <script src="../js/ayuda.js"></script>
    <script src="../js/menu.js"></script>
</body>
</html>