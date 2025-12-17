<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
$page_title = 'Perfil - Administrador';
$page_stylesheet = '../style/perfil_admin.css';
require_once '../php/common_header.php';

// Verificación de rol
if ($_SESSION['rol'] !== 'admin') {
    header("Location: ../inicia.html?status=unauthorized_role");
    exit();
}
?>

<div class="top-bar">
    <button id="menu-toggle" class="menu-toggle">
        <img src="../multimedia/administrador/menu.png" alt="Menú">
    </button>
    <div class="project-info">
        <span class="project-name">San Juan Bosco</span>
        <span class="module-name">Perfil - Administrador</span>
    </div>
</div>

<div class="main-container">
    <aside class="menu-container" id="menu-container">
        <div class="profile">
            <img src="" alt="Usuario" class="profile-pic">
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
    </aside>

    <main class="content-container">
        <div class="profile-container">
            <div class="profile-card">
                <div class="profile-header">
                    <img src="" alt="Foto de Perfil" id="profile-img-main" class="profile-pic">
                    <h2 id="profile-name-main">Cargando...</h2>
                    <p id="profile-role-main">Cargando...</p>
                    <button id="change-pic-btn" class="btn">Cambiar Foto</button>
                    <input type="file" id="profile-pic-input" accept="image/*" style="display: none;">
                </div>
                <div class="profile-body">
                    <form id="profile-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="primer_nombre">Primer Nombre</label>
                                <input type="text" id="primer_nombre" name="primer_nombre" placeholder="Ej: Juan" required>
                            </div>
                            <div class="form-group">
                                <label for="segundo_nombre">Segundo Nombre</label>
                                <input type="text" id="segundo_nombre" name="segundo_nombre" placeholder="Ej: Carlos">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="primer_apellido">Primer Apellido</label>
                                <input type="text" id="primer_apellido" name="primer_apellido" placeholder="Ej: Pérez" required>
                            </div>
                            <div class="form-group">
                                <label for="segundo_apellido">Segundo Apellido</label>
                                <input type="text" id="segundo_apellido" name="segundo_apellido" placeholder="Ej: Gómez">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tipo_documento">Tipo de Documento</label>
                                <input type="text" id="tipo_documento" name="tipo_documento" class="readonly-field" placeholder="Ej: Cédula de Ciudadanía" readonly>
                            </div>
                            <div class="form-group">
                                <label for="no_documento">No. de Documento</label>
                                <input type="text" id="no_documento" name="no_documento" class="readonly-field" placeholder="Ej: 1234567890" readonly>
                            </div>
                            <div class="form-group">
                                <label for="fecha_expedicion">Fecha de Expedición</label>
                                <input type="date" id="fecha_expedicion" name="fecha_expedicion" class="readonly-field" readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="readonly-field" readonly>
                            </div>
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" id="telefono" name="telefono" placeholder="Ej: 3001234567">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="sexo">Sexo</label>
                                <input type="text" id="sexo" name="sexo" class="readonly-field" readonly>
                            </div>
                            <div class="form-group">
                                <label for="estado_civil">Estado Civil</label>
                                <input type="text" id="estado_civil" name="estado_civil" class="readonly-field" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" id="direccion" name="direccion" placeholder="Ej: Calle Falsa 123">
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="rh">RH</label>
                                <input type="text" id="rh" name="rh" placeholder="Ej: O+">
                            </div>
                            <div class="form-group">
                                <label for="alergias">Alergias</label>
                                <input type="text" id="alergias" name="alergias" placeholder="Ej: Ninguna">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>

            <div class="password-card">
                <h3>Cambiar Contraseña</h3>
                <form id="password-form">
                    <div class="form-group">
                        <label for="current-password">Contraseña Actual</label>
                        <input type="password" id="current-password" name="current-password" required>
                    </div>
                    <div class="form-group">
                        <label for="new-password">Nueva Contraseña</label>
                        <input type="password" id="new-password" name="new-password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirmar Contraseña</label>
                        <input type="password" id="confirm-password" name="confirm-password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                </form>
            </div>
        </div>
    </main>
</div>
    <footer>
        <p>© 2025 Colegio San Juan Bosco. Todos los derechos reservados.</p>
        <p>Autores y Desarrolladores: Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita</p>
    </footer>

<script src="../js/perfil_manager.js"></script>

<?php
require_once '../php/common_footer.php';
?>
