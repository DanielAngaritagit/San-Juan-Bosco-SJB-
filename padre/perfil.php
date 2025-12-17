<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
$page_title = 'Perfil - Padre';
$page_stylesheet = '../style/perfil_padre.css';
require_once '../php/common_header.php';

// Verificación de rol
if ($_SESSION['rol'] !== 'padre') {
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
        <span class="module-name">Perfil - Padre</span>
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
                <li><a href="padre.php" class="menu-link"><img src="../multimedia/padre/home.png" alt=""> Inicio</a></li>
                <li><a href="calendario.php" class="menu-link"><img src="../multimedia/padre/calendario.png" alt=""> Calendario</a></li>
                <li><a href="pqrsf.php" class="menu-link"><img src="../multimedia/padre/pqrsf.png" alt=""> PQRSF</a></li>
                <li><a href="perfil.php" class="menu-link"><img src="../multimedia/padre/perfil-usuario.png" alt=""> Perfil</a></li>
                <li><a href="ayuda.php" class="menu-link active"><img src="../multimedia/padre/ayuda.png" alt=""> Ayuda</a></li>
                <li><a href="../logout.php" class="menu-link"><img src="../multimedia/padre/cerrar_sesion.png" alt=""> Cerrar Sesión</a></li>
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
                                <label for="primer_nombre">Primer Nombre</label>
                                <input type="text" id="primer_nombre" name="primer_nombre" placeholder="Ej: Juan" required>
                            </div>
                            <div class="form-group">
                                <label for="segundo_nombre">Segundo Nombre</label>
                                <input type="text" id="segundo_nombre" name="segundo_nombre" placeholder="Ej: Carlos">
                            </div>
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
                        </div>
                        <div class="form-group">
                            <label for="ciudad_expedicion">Ciudad Expedición (Doc)</label>
                            <input type="text" id="ciudad_expedicion" name="ciudad_expedicion" placeholder="Ej: Bogotá" class="readonly-field" readonly>
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
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="rh">RH</label>
                                <input type="text" id="rh" name="rh" placeholder="Ej: O+" class="readonly-field" readonly>
                            </div>
                            <div class="form-group">
                                <label for="alergias">Alergias</label>
                                <input type="text" id="alergias" name="alergias" placeholder="Ej: Ninguna">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="parentesco">Parentesco</label>
                                <input type="text" id="parentesco" name="parentesco" placeholder="Ej: Padre, Madre, Tutor">
                            </div>
                            <div class="form-group">
                                <label for="sexo">Sexo</label>
                                <input type="text" id="sexo" name="sexo" placeholder="Ej: Masculino, Femenino">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nacionalidad">Nacionalidad</label>
                                <input type="text" id="nacionalidad" name="nacionalidad" placeholder="Ej: Colombiana">
                            </div>
                            
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="celular">Celular</label>
                                <input type="tel" id="celular" name="celular" placeholder="Ej: 3001234567">
                            </div>
                            <div class="form-group">
                                <label for="estado_civil">Estado Civil</label>
                                <input type="text" id="estado_civil" name="estado_civil" placeholder="Ej: Soltero, Casado">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="direccionp">Dirección Principal</label>
                            <input type="text" id="direccionp" name="direccionp" placeholder="Ej: Calle 123 #45-67">
                        </div>
                        <div class="form-group">
                            <label for="lugar_recidencia">Lugar de Residencia</label>
                            <input type="text" id="lugar_recidencia" name="lugar_recidencia" placeholder="Ej: Bogotá">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="ocupacion">Ocupación</label>
                                <input type="text" id="ocupacion" name="ocupacion" placeholder="Ej: Ingeniero">
                            </div>
                            <div class="form-group">
                                <label for="nivel_estudio">Nivel de Estudio</label>
                                <input type="text" id="nivel_estudio" name="nivel_estudio" placeholder="Ej: Profesional">
                            </div>
                        </div>
                         <div class="form-row">
                            <div class="form-group">
                                <label for="religion">Religión</label>
                                <input type="text" id="religion" name="religion" placeholder="Ej: Católica">
                            </div>
                            <div class="form-group">
                                <label for="afiliado">Afiliado a Salud</label>
                                <input type="text" id="afiliado" name="afiliado" placeholder="Ej: Si / No">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="afi_detalles">Detalles Afiliación</label>
                            <input type="text" id="afi_detalles" name="afi_detalles" placeholder="Ej: EPS Sura">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="empresa">Empresa donde trabaja</label>
                                <input type="text" id="empresa" name="empresa" placeholder="Ej: Mi Empresa S.A.S.">
                            </div>
                            <div class="form-group">
                                <label for="cargo">Cargo</label>
                                <input type="text" id="cargo" name="cargo" placeholder="Ej: Gerente">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección (Trabajo)</label>
                            <input type="text" id="direccion" name="direccion" placeholder="Ej: Carrera 7 # 71-21">
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
