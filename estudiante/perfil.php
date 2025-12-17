<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
$page_title = 'Perfil - Estudiante';
$page_stylesheet = '../style/perfil_estu.css';
require_once '../php/common_header.php';

// Verificación de rol
if ($_SESSION['rol'] !== 'estudiante') {
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
        <span class="module-name">Perfil - Estudiante</span>
    </div>
</div>

<div class="main-container">
    <aside class="menu-container" id="menu-container">
        <div class="profile">
            <img src="" alt="Usuario" class="profile-img" id="profile-img">
            <h3 id="profile-name">Cargando...</h3>
            <p id="profile-role">Cargando...</p>
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

    <main class="content-container">
        <div class="profile-container">
            <h1>Mi Perfil</h1>
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
                                <label for="nombres">Nombres</label>
                                <input type="text" id="nombres" name="nombres" required>
                            </div>
                            <div class="form-group">
                                <label for="apellidos">Apellidos</label>
                                <input type="text" id="apellidos" name="apellidos" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tipo_documento">Tipo de Documento</label>
                                <input type="text" id="tipo_documento" name="tipo_documento" class="readonly-field" readonly>
                            </div>
                            <div class="form-group">
                                <label for="no_documento">No. de Documento</label>
                                <input type="text" id="no_documento" name="no_documento" class="readonly-field" readonly>
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
                                <label for="sexo">Sexo</label>
                                <input type="text" id="sexo" name="sexo" class="readonly-field" readonly>
                            </div>
                            <div class="form-group">
                                <label for="rh">RH</label>
                                <input type="text" id="rh" name="rh" class="readonly-field" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" id="direccion" name="direccion">
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono">
                        </div>
                        <div class="form-group">
                            <label for="alergias">Alergias</label>
                            <input type="text" id="alergias" name="alergias" placeholder="Ninguna">
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

<script src="../js/perfil_estudiante.js"></script>

<?php
require_once '../php/common_footer.php';
?>
