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
    <link rel="stylesheet" href="../style/pqrsf_estu.css">
    <title>PQRSF</title>
    <link rel="icon" type="image/png" href="/SJB/multimedia/estudiante/escudo.png">
</head>
<body>
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">PQRSF - Estudiante</span>
        </div>
    </div>
    
    <aside class="menu-container" id="menu-container">
        <div class="profile">
            <img src="../multimedia/pagina_principal/usuario.png" alt="Foto de perfil del Usuario" id="profile-img"> <!-- Se recomienda un placeholder -->
            <div class="profile-info">
                <h3 id="profile-name">Cargando...</h3>
                <p id="profile-role">Cargando...</p>
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
    </aside>
    <div class="main-container">
        <main class="content-container" aria-label="Contenido principal">
            <div class="pqrsf-container">
                <h1>PQRSF REGISTRADAS</h1>

                <div class="filtros-container">
                    <input type="text" class="input-field buscar-input" placeholder="Buscar PQRSF..." id="buscarInput" aria-label="Campo para buscar PQRSF">
                    <select class="select-field" id="filtroTipo" aria-label="Filtrar por tipo de PQRSF">
                        <option value="">Todos los tipos</option>
                        <option value="Petición">Petición</option>
                        <option value="Queja">Queja</option>
                        <option value="Reclamo">Reclamo</option>
                        <option value="Sugerencia">Sugerencia</option>
                        <option value="Felicitación">Felicitación</option>
                        <option value="Solicitud sobre Datos Personales (Habeas Data)">Solicitud sobre Datos Personales (Habeas Data)</option>
                    </select>
                    <select class="select-field" id="filtroEstado" aria-label="Filtrar por estado de PQRSF">
                        <option value="">Todos los estados</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="En proceso">En proceso</option>
                        <option value="Resuelto">Resuelto</option>
                    </select>
                    <button class="btn btn-filtrar" id="btnFiltrar">Filtrar</button>
                    <button class="btn btn-nueva-pqrsf" id="abrirModal">Nueva PQRSF</button>
                </div>

                <div class="pqrsf-list-container">
                    <table class="pqrsf-table" id="tablaPQRSF" aria-live="polite">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Sobre</th>
                                <th>Fecha Envío</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoTabla">
                            <!-- Los datos se cargarán aquí dinámicamente con JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para crear/editar una nueva PQRSF -->
    <div id="pqrsfModal" class="modal" style="display:none;" role="dialog" aria-labelledby="tituloModalPQRSF" aria-modal="true">
        <div class="modal-contenido">
            <h2 id="tituloModalPQRSF">NUEVA PQRSF</h2>
            <form id="formPQRSF" novalidate>
                <div class="grupo-form">
                    <label for="nombres">Nombre Solicitante *</label>
                    <input type="text" id="nombres" name="nombres" required>
                </div>
                <div class="grupo-form">
                    <label for="tipoPQRSF">Tipo *</label>
                    <select id="tipoPQRSF" name="tipo" required>
                        <option value="">Seleccionar tipo</option>
                        <option value="Petición">Petición</option>
                        <option value="Queja">Queja</option>
                        <option value="Reclamo">Reclamo</option>
                        <option value="Sugerencia">Sugerencia</option>
                        <option value="Felicitación">Felicitación</option>
                        <option value="Solicitud sobre Datos Personales (Habeas Data)">Solicitud sobre Datos Personales (Habeas Data)</option>
                    </select>
                </div>
                <div class="grupo-form">
                    <label for="contacto_solicitante">Contacto (Email o Teléfono) *</label>
                    <input type="text" id="contacto_solicitante" name="contacto_solicitante" required>
                </div>
                <div class="grupo-form">
                    <label for="descripcion">Descripción *</label>
                    <textarea id="descripcion" name="descripcion" required></textarea>
                </div>
                <div class="grupo-form">
                    <label for="destinatarioPQRSF">¿Sobre quién es la PQRSF? *</label>
                    <select id="destinatarioPQRSF" name="pqrsf_about_category" required>
                        <option>Seleccionar</option>
                        <option value="Profesor">Profesor</option>
                        <option value="Estudiante">Estudiante</option>
                        <option value="Coordinador">Coordinador</option>
                        <option value="Personal del colegio">Personal del colegio</option>
                        <option value="Estructura del colegio">Estructura del colegio</option>
                    </select>
                </div>
                <div class="grupo-form" id="destinatario-especifico-container" style="display:none;">
                    <label for="destinatarioEspecificoPQRSF">Seleccione Específicamente *</label>
                    <select id="destinatarioEspecificoPQRSF" name="destinatario_especifico">
                        <!-- Opciones se cargarán dinámicamente -->
                    </select>
                </div>
                <div class="grupo-form">
                    <label for="archivoAdjunto" class="btn-adjuntar">Adjuntar archivo (PDF, Word, imagen o video)</label>
                    <input type="file" id="archivoAdjunto" name="archivoAdjunto" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.mp4,.webm,.mov" hidden>
                    <span id="nombreArchivo" class="nombre-archivo"></span>
                </div>
                
                <div class="grupo-form checkbox-container">
                    <input type="checkbox" id="aceptarTerminos" name="aceptarTerminos" required>
                    <label for="aceptarTerminos">He leído y acepto la <a href="../multimedia/Politica_Tratamiento_Datos_Personales.pdf" target="_blank">Política de Tratamiento de Datos Personales</a>.</label>
                </div>
                <div class="modal-acciones">
                    <button type="submit" class="btn-enviar" id="btn-guardar-pqrsf" disabled>Guardar</button>
                    <button type="button" class="btn-cancelar" id="cancelarModalPQRSF">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>© 2025 Colegio San Juan Bosco. Todos los derechos reservados.</p>
        <p>Autores y Desarrolladores: Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita</p>
    </footer>
    
    <script src="../js/user_profile_manager.js"></script>
    <script src="../js/pqrsf.js"></script>
    <script src="../js/menu.js"></script>
</body>
</html>