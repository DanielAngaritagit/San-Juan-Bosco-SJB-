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
        <link rel="stylesheet" href="../style/pqrsf_prof.css">
    <title>PQRSF</title>
</head>
<body>
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">PQRSF- Profesor</span>
        </div>
    </div>

    <aside class="menu-container" id="menu-container">
        <div class="profile">
            <img src="../multimedia/pagina_principal/usuario.png" alt="Usuario" class="profile-img" id="profile-img">
            <h3 id="profile-name">Profesor</h3>
            <p id="profile-role">Rol: Profesor</p>
        </div>
        <nav class="menu">
        <ul>
            <li><a href="profesor.php" class="menu-link"><img src="../multimedia/profesor/home.png" alt=""> Inicio</a></li>
            <li><a href="asistencia.php" class="menu-link"><img src="../multimedia/profesor/asistencia.png" alt=""> Tomar Asistencia</a></li>
            <li><a href="calendario.php" class="menu-link"><img src="../multimedia/profesor/calendario.png" alt=""> Calendario</a></li>
            <li><a href="calificar.php" class="menu-link"><img src="../multimedia/profesor/calificaciones.png" alt=""> Calificar</a></li>
            <li><a href="pqrsf.php" class="menu-link active"><img src="../multimedia/profesor/pqrsf.png" alt=""> PQRSF</a></li>
            <li><a href="perfil.php" class="menu-link"><img src="../multimedia/profesor/perfil-usuario.png" alt=""> Perfil</a></li>
            <li><a href="ayuda.php" class="menu-link"><img src="../multimedia/profesor/ayuda.png" alt=""> Ayuda</a></li>
            <li><a href="../logout.php" class="menu-link"><img src="../multimedia/profesor/cerrar_sesion.png" alt=""> Cerrar Sesión</a></li>
        </ul>
    </nav>
    </aside>
    <div class="main-container">
        <main class="content-container" id="content-container">
            <div class="container-fluid">
                <header class="mb-4">
                    <h1><i class="fas fa-envelope-open-text"></i> Gestión de PQRSF</h1>
                    <p class="text-muted">Consulte, filtre y gestione las PQRSF registradas.</p>
                </header>

                <div class="pqrsf-container">
                    <div class="filtros-container">
                        <input type="text" class="form-control buscar-input" placeholder="Buscar PQRSF..." id="buscarInput" aria-label="Campo para buscar PQRSF">
                        <select class="form-control select-field" id="filtroTipo" aria-label="Filtrar por tipo de PQRSF">
                            <option value="">Todos los tipos</option>
                            <option value="Petición">Petición</option>
                            <option value="Queja">Queja</option>
                            <option value="Reclamo">Reclamo</option>
                            <option value="Sugerencia">Sugerencia</option>
                            <option value="Felicitación">Felicitación</option>
                        </select>
                        <select class="form-control select-field" id="filtroEstado" aria-label="Filtrar por estado de PQRSF">
                            <option value="">Todos los estados</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="En proceso">En proceso</option>
                            <option value="Resuelto">Resuelto</option>
                        </select>
                        <button class="btn btn-primary btn-filtrar" id="btnFiltrar">Filtrar</button>
                        <button class="btn btn-success btn-nueva-pqrsf" id="abrirModal">Nueva PQRSF</button>
                    </div>

                    <div class="pqrsf-list-container">
                        
                        <table class="table table-hover table-striped" id="tablaPQRSF" aria-live="polite">
                            <thead class="thead-dark">
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
            </div>
        </main>
    </div>

    <!-- Modal para crear/editar una nueva PQRSF -->
    <div id="pqrsfModal" class="modal" style="display:none;" role="dialog" aria-labelledby="tituloModalPQRSF" aria-modal="true">
        <div class="modal-contenido">
            <h2 id="tituloModalPQRSF">NUEVA PQRSF</h2>
            <!-- CORRECCIÓN: Se añaden atributos 'name' a los campos del formulario -->
            <form id="formPQRSF" novalidate>
                <input type="hidden" id="pqrsfIdEditar" name="id_pqrsf_editar">
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
                <div class="grupo-form">
                    <input type="checkbox" id="aceptarTerminos" name="aceptarTerminos" required>
                    <label for="aceptarTerminos">Acepto los Términos y Condiciones</label>
                </div>
                
                <div class="modal-acciones">
                    <button type="submit" class="btn-enviar" id="btn-guardar-pqrsf">Guardar</button>
                    <button type="button" class="btn-cancelar" id="cancelarModalPQRSF">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para ver detalles de una PQRSF -->
    <div id="modalVerPQRSF" class="modal" style="display:none;" role="dialog" aria-labelledby="tituloModalVerPQRSF" aria-modal="true">
        <div class="modal-contenido">
            <h2 id="tituloModalVerPQRSF">Detalles de la PQRSF</h2>
            <div class="detalles-contenido">
                <p><strong>ID:</strong> <span id="verId"></span></p>
                <p><strong>Tipo:</strong> <span id="verTipo"></span></p>
                <p><strong>Descripción:</strong></p>
                <p id="verDescripcion"></p>
                <p><strong>Destinatario:</strong> <span id="verDestinatario"></span></p>
                <p><strong>Fecha de Creación:</strong> <span id="verFecha"></span></p>
                <p><strong>Estado:</strong> <span id="verEstado"></span></p>
                <p><strong>Archivo Adjunto:</strong> <a href="#" id="verArchivoAdjunto" target="_blank" style="display:none;"></a></p>
            </div>
            <div class="modal-acciones">
                <button type="button" class="btn-cancelar" onclick="document.getElementById('modalVerPQRSF').style.display='none'">Cerrar</button>
            </div>
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