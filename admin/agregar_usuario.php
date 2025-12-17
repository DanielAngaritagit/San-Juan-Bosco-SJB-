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

// --- Obtener listado de materias ---
require_once '../php/conexion.php';
$materias = [];
try {
    $stmt = $pdo->query("SELECT id_materia, nombre FROM tab_materias ORDER BY nombre");
    $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Log the error for debugging
    error_log("Error al obtener materias: " . $e->getMessage());
    // Optionally, display a message to the user (for development/debugging)
    // echo "<p style='color: red;'>Error al cargar materias: " . $e->getMessage() . "</p>";
    $materias = []; // Ensure $materias is empty if an error occurs
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link rel="icon" type="image/x-icon" href="../multimedia/inicio_sesion/escudo.png">
    <link rel="stylesheet" href="../style/agregar_usuario.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: .25rem;
            font-size: .875em;
            color: #dc3545;
        }
        .form-group input.is-invalid, .form-group select.is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
</head>
<body class="theme-acudiente">

    <!-- El top-bar y menú lateral se mantienen igual -->
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">Agrega Usuarios - Administrador</span>
        </div>
    </div>

    <div class="menu-container" id="menu-container">
        <div class="profile">
            <img src="../multimedia/pagina_principal/usuario.png" alt="Usuario" class="profile-pic">
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

    <!-- Contenido Principal con nueva estructura -->
    <div class="main-container">
        <div class="container">
            <header>
                <h1>Gestión de Usuarios</h1>
                <p>Seleccione el rol y complete el formulario para registrar un nuevo usuario.</p>
            </header>

            <div class="tabs">
                <button class="tab-btn active" data-tab="acudiente" data-theme="theme-acudiente">Acudiente</button>
                <button class="tab-btn" data-tab="profesor" data-theme="theme-profesor">Profesor</button>
                <button class="tab-btn" data-tab="estudiante" data-theme="theme-estudiante">Estudiante</button>
                <button class="tab-btn" data-tab="admin" data-theme="theme-admin">Administrador</button>
            </div>

            <div id="message-container" class="message" style="display: none;"></div>

            <!-- Formulario para Acudiente -->
            <div id="acudiente" class="tab-content active">
                <h2>Registro de Acudiente</h2>
                <form id="form-acudiente">
                    <div class="form-header"><p>Datos Personales. Los campos con (*) son obligatorios.</p></div>
                    <div class="form-grid">
                        <!-- Core Identification & Contact (Most Important) -->
                        <div class="form-group">
                            <label for="primer_nombre" class="required">Primer Nombre</label>
                            <input type="text" id="primer_nombre" name="primer_nombre" required placeholder="Ej: Ana" oninput="validateLettersOnly(this, 'primer_nombre-error')">
                            <div id="primer_nombre-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="segundo_nombre">Segundo Nombre</label>
                            <input type="text" id="segundo_nombre" name="segundo_nombre" placeholder="Ej: María" oninput="validateLettersOnly(this, 'segundo_nombre-error')">
                            <div id="segundo_nombre-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="primer_apellido" class="required">Primer Apellido</label>
                            <input type="text" id="primer_apellido" name="primer_apellido" required placeholder="Ej: Pérez" oninput="validateLettersOnly(this, 'primer_apellido-error')">
                            <div id="primer_apellido-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="segundo_apellido">Segundo Apellido</label>
                            <input type="text" id="segundo_apellido" name="segundo_apellido" placeholder="Ej: Gómez" oninput="validateLettersOnly(this, 'segundo_apellido-error')">
                            <div id="segundo_apellido-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="tipo_documento" class="required">Tipo de Documento</label>
                            <select id="tipo_documento" name="tipo_documento" required onchange="validateDocumentLength('tipo_documento', 'no_documento', 'no_documento-error')">
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="PA">Pasaporte</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="no_documento" class="required">No. de Documento</label>
                            <input type="text" id="no_documento" name="no_documento" required placeholder="Ej: 1234567890" oninput="validateDocumentLength('tipo_documento', 'no_documento', 'no_documento-error')">
                            <div id="no_documento-error" class="invalid-feedback">La longitud del documento no es válida para el tipo seleccionado.</div>
                        </div>
                        <div class="form-group">
                            <label for="fecha_nacimiento" class="required">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required onchange="validateMinAge(this, 'fecha_nacimiento-error', 15)">
                            <div id="fecha_nacimiento-error" class="invalid-feedback">El acudiente debe ser mayor de 15 años.</div>
                        </div>
                        <div class="form-group"><label for="fecha_expedicion">Fecha de Expedición</label><input type="date" id="fecha_expedicion" name="fecha_expedicion" onchange="validateNotFutureYear(this, 'fecha_expedicion-error')">
                            <div id="fecha_expedicion-error" class="invalid-feedback">El año de expedición no puede ser superior al actual.</div></div>
                        <div class="form-group">
                            <label for="email" class="required">Correo Electrónico</label>
                            <input type="email" id="email" name="email" required placeholder="ejemplo@correo.com" oninput="validateEmail(this, 'email-error')">
                            <div id="email-error" class="invalid-feedback">El formato del correo no es válido.</div>
                        </div>
                        <div class="form-group">
                            <label for="telefono" class="required">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" required placeholder="Ej: 3101234567" oninput="validateNumbersOnly(this, 'telefono-error')">
                            <div id="telefono-error" class="invalid-feedback">Solo se permiten números.</div>
                        </div>
                        <div class="form-group"><label for="parentesco" class="required">Parentesco</label><select id="parentesco" name="parentesco" required><option value="" disabled selected>Seleccione...</option><option value="Padre">Padre</option><option value="Madre">Madre</option><option value="Tutor">Tutor</option><option value="Otro">Otro</option></select></div>
                        <div class="form-group"><label for="sexo" class="required">Sexo</label><select id="sexo" name="sexo" required><option value="Masculino">Masculino</option><option value="Femenino">Femenino</option><option value="Otro">Otro</option></select></div>
                        <div class="form-group">
                            <label for="nacionalidad" class="required">Nacionalidad</label>
                            <input type="text" id="nacionalidad" name="nacionalidad" required placeholder="Ej: Colombiana" oninput="validateLettersOnly(this, 'nacionalidad-error')">
                            <div id="nacionalidad-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group"><label for="direccionp" class="required">Dirección Principal</label><input type="text" id="direccionp" name="direccionp" required placeholder="Ej: Calle 10 #20-30"></div>
                        <div class="form-group">
                            <label for="barrio" class="required">Barrio</label>
                            <input type="text" id="barrio" name="barrio" required placeholder="Ej: Chapinero" oninput="validateLettersOnly(this, 'barrio-error')">
                            <div id="barrio-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="lugar_recidencia" class="required">Lugar de Residencia</label>
                            <input type="text" id="lugar_recidencia" name="lugar_recidencia" required placeholder="Ej: Bogotá" oninput="validateLettersOnly(this, 'lugar_recidencia-error')">
                            <div id="lugar_recidencia-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group"><label for="rh" class="required">RH</label><input type="text" id="rh" name="rh" required placeholder="Ej: O+"></div>
                        <div class="form-group"><label for="alergias" class="required">Alergias</label><input type="text" id="alergias" name="alergias" required placeholder="Ej: Ninguna"></div>
                        <div class="form-group">
                            <label for="ocupacion" class="required">Ocupación</label>
                            <input type="text" id="ocupacion" name="ocupacion" required placeholder="Ej: Desarrollador" oninput="validateLettersOnly(this, 'ocupacion-error')">
                            <div id="ocupacion-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="ciudad_expedicion" class="required">Ciudad de Expedición</label>
                            <input type="text" id="ciudad_expedicion" name="ciudad_expedicion" required placeholder="Ej: Bogotá" oninput="validateLettersOnly(this, 'ciudad_expedicion-error')">
                            <div id="ciudad_expedicion-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="profesion">Profesión</label>
                            <input type="text" id="profesion" name="profesion" placeholder="Ej: Ingeniería" oninput="validateLettersOnly(this, 'profesion-error')">
                            <div id="profesion-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group"><label for="empresa">Empresa</label><input type="text" id="empresa" name="empresa" placeholder="Ej: Acme Inc."></div>
                        <div class="form-group"><label for="religion">Religión</label><input type="text" id="religion" name="religion" placeholder="Ej: Católica"></div>
                        <div class="form-group"><label for="nivel_estudio">Nivel de Estudio</label><input type="text" id="nivel_estudio" name="nivel_estudio" placeholder="Ej: Profesional"></div>
                        <div class="form-group"><label for="afiliado">Afiliado a Salud</label><select id="afiliado" name="afiliado"><option value="Si">Sí</option><option value="No">No</option></select></div>
                        <div class="form-group"><label for="afi_detalles">Detalles de Afiliación</label><input type="text" id="afi_detalles" name="afi_detalles" placeholder="Ej: Plan Complementario"></div>
                        <div class="form-group"><label for="eps">EPS</label><input type="text" id="eps" name="eps" placeholder="Ej: Sura"></div>
                        <div class="form-group"><label for="estado_civil">Estado Civil</label><select id="estado_civil" name="estado_civil"><option value="Soltero(a)">Soltero(a)</option><option value="Casado(a)">Casado(a)</option><option value="Otro">Otro</option></select></div>
                        
                        <div class="form-group policy-check"><input type="checkbox" id="politica-acudiente" name="politica" required><label for="politica-acudiente">Acepto la <a href="../multimedia/Politica_Tratamiento_Datos_Personales.pdf" target="_blank">Política de Tratamiento de Datos</a>.</label></div>
                    </div>
                    <button type="submit" class="btn-submit" id="btn-acudiente" disabled>Guardar Acudiente</button>
                </form>
            </div>

            <!-- Formulario para Profesor -->
            <div id="profesor" class="tab-content">
                <h2>Registro de Profesor</h2>
                <form id="form-profesor">
                    <div class="form-header"><p>Datos Personales y Profesionales. Los campos con (*) son obligatorios.</p></div>
                        <div class="form-group">
                            <label for="primer_nombre_p" class="required">Primer Nombre</label>
                            <input type="text" id="primer_nombre_p" name="primer_nombre_p" required placeholder="Ej: Juan" oninput="validateLettersOnly(this, 'primer_nombre_p-error')">
                            <div id="primer_nombre_p-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="segundo_nombre_p">Segundo Nombre</label>
                            <input type="text" id="segundo_nombre_p" name="segundo_nombre_p" placeholder="Ej: Carlos" oninput="validateLettersOnly(this, 'segundo_nombre_p-error')">
                            <div id="segundo_nombre_p-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="primer_apellido_p" class="required">Primer Apellido</label>
                            <input type="text" id="primer_apellido_p" name="primer_apellido_p" required placeholder="Ej: Rodríguez" oninput="validateLettersOnly(this, 'primer_apellido_p-error')">
                            <div id="primer_apellido_p-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="segundo_apellido_p">Segundo Apellido</label>
                            <input type="text" id="segundo_apellido_p" name="segundo_apellido_p" placeholder="Ej: López" oninput="validateLettersOnly(this, 'segundo_apellido_p-error')">
                            <div id="segundo_apellido_p-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="tipo_documento_p" class="required">Tipo de Documento</label>
                            <select id="tipo_documento_p" name="tipo_documento_p" required onchange="validateDocumentLength('tipo_documento_p', 'no_documento_p', 'no_documento_p-error')">
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="CE">Cédula de Extranjería</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="no_documento_p" class="required">No. de Documento</label>
                            <input type="text" id="no_documento_p" name="no_documento_p" required placeholder="Ej: 1098765432" oninput="validateDocumentLength('tipo_documento_p', 'no_documento_p', 'no_documento_p-error')">
                            <div id="no_documento_p-error" class="invalid-feedback">La longitud del documento no es válida.</div>
                        </div>
                        <div class="form-group">
                            <label for="fecha_nacimiento_p" class="required">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento_p" name="fecha_nacimiento_p" required onchange="validateMinAge(this, 'fecha_nacimiento_p-error', 22)">
                            <div id="fecha_nacimiento_p-error" class="invalid-feedback">El profesor debe ser mayor de 22 años.</div>
                        </div>
                        <div class="form-group"><label for="fecha_expedicion_p">Fecha de Expedición</label><input type="date" id="fecha_expedicion_p" name="fecha_expedicion_p" onchange="validateNotFutureYear(this, 'fecha_expedicion_p-error')">
                            <div id="fecha_expedicion_p-error" class="invalid-feedback">El año de expedición no puede ser superior al actual.</div></div>
                        <div class="form-group">
                            <label for="email_p" class="required">Correo Electrónico</label>
                            <input type="email" id="email_p" name="email_p" required placeholder="profesor@correo.com" oninput="validateEmail(this, 'email_p-error')">
                            <div id="email_p-error" class="invalid-feedback">El formato del correo no es válido.</div>
                        </div>
                        <div class="form-group">
                            <label for="telefono_p" class="required">Teléfono</label>
                            <input type="text" id="telefono_p" name="telefono_p" required placeholder="Ej: 3219876543" oninput="validateNumbersOnly(this, 'telefono_p-error')">
                            <div id="telefono_p-error" class="invalid-feedback">Solo se permiten números.</div>
                        </div>
                        <div class="form-group">
                            <label for="especialidad_p" class="required">Especialidad</label>
                            <input type="text" id="especialidad_p" name="especialidad_p" required placeholder="Ej: Cálculo" oninput="validateLettersOnly(this, 'especialidad_p-error')">
                            <div id="especialidad_p-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group"><label for="id_materia_p" class="required">Materia Principal</label><select id="id_materia_p" name="id_materia_p" required><option value="">Seleccione una materia</option><?php foreach ($materias as $materia): ?><option value="<?= htmlspecialchars($materia['id_materia']); ?>"><?= htmlspecialchars($materia['nombre']); ?></option><?php endforeach; ?></select></div>
                        <div class="form-group">
                            <label for="nacionalidad_p" class="required">Nacionalidad</label>
                            <input type="text" id="nacionalidad_p" name="nacionalidad_p" required placeholder="Ej: Colombiana" oninput="validateLettersOnly(this, 'nacionalidad_p-error')">
                            <div id="nacionalidad_p-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group"><label for="direccion_p_prof">Dirección</label><input type="text" id="direccion_p_prof" name="direccion_p_prof" placeholder="Ej: Av. Siempre Viva 742"></div>
                        <div class="form-group"><label for="rh_p" class="required">RH</label><input type="text" id="rh_p" name="rh_p" required placeholder="Ej: O+"></div>
                        <div class="form-group"><label for="alergias_p">Alergias</label><input type="text" id="alergias_p" name="alergias_p" placeholder="Ej: Ninguna"></div>
                        <div class="form-group">
                            <label for="titulo_academico_p">Título Académico</label>
                            <input type="text" id="titulo_academico_p" name="titulo_academico_p" placeholder="Ej: Licenciado en Matemáticas" oninput="validateLettersOnly(this, 'titulo_academico_p-error')">
                            <div id="titulo_academico_p-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group"><label for="eps_p">EPS</label><input type="text" id="eps_p" name="eps_p" placeholder="Ej: Sura">
                        <div class="form-group"><label for="estado_civil_p">Estado Civil</label><select id="estado_civil_p" name="estado_civil_p"><option value="Soltero(a)">Soltero(a)</option><option value="Casado(a)">Casado(a)</option><option value="Otro">Otro</option></select></div>
                        <div class="form-group policy-check"><input type="checkbox" id="politica-profesor" name="politica" required><label for="politica-profesor">Acepto la <a href="../multimedia/Politica_Tratamiento_Datos_Personales.pdf" target="_blank">Política de Tratamiento de Datos</a>.</label></div>
                    </div>
                    <button type="submit" class="btn-submit" id="btn-profesor" disabled>Guardar Profesor</button>
                </form>
            </div>

            <!-- Formulario para Estudiante -->
            <div id="estudiante" class="tab-content">
                <h2>Registro de Estudiante</h2>
                <form id="form-estudiante">
                    <div class="form-header"><p>Datos Personales del Estudiante. Los campos con (*) son obligatorios.</p></div>
                    <div class="form-grid">
                        <!-- Core Identification & Academic (Most Important) -->
                        <div class="form-group">
                            <label for="primer_nombre_e" class="required">Primer Nombre</label>
                            <input type="text" id="primer_nombre_e" name="primer_nombre_e" required placeholder="Ej: Luis" oninput="validateLettersOnly(this, 'primer_nombre_e-error')">
                            <div id="primer_nombre_e-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="segundo_nombre_e">Segundo Nombre</label>
                            <input type="text" id="segundo_nombre_e" name="segundo_nombre_e" placeholder="Ej: Miguel" oninput="validateLettersOnly(this, 'segundo_nombre_e-error')">
                            <div id="segundo_nombre_e-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="primer_apellido_e" class="required">Primer Apellido</label>
                            <input type="text" id="primer_apellido_e" name="primer_apellido_e" required placeholder="Ej: González" oninput="validateLettersOnly(this, 'primer_apellido_e-error')">
                            <div id="primer_apellido_e-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="segundo_apellido_e">Segundo Apellido</label>
                            <input type="text" id="segundo_apellido_e" name="segundo_apellido_e" placeholder="Ej: García" oninput="validateLettersOnly(this, 'segundo_apellido_e-error')">
                            <div id="segundo_apellido_e-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="tipo_documento_e" class="required">Tipo de Documento</label>
                            <select id="tipo_documento_e" name="tipo_documento_e" required onchange="validateDocumentLength('tipo_documento_e', 'no_documento_e', 'no_documento_e-error')">
                                <option value="TI">Tarjeta de Identidad</option>
                                <option value="RC">Registro Civil</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="no_documento_e" class="required">No. de Documento</label>
                            <input type="text" id="no_documento_e" name="no_documento_e" required placeholder="Ej: 1002003004" oninput="validateDocumentLength('tipo_documento_e', 'no_documento_e', 'no_documento_e-error')">
                            <div id="no_documento_e-error" class="invalid-feedback">La longitud del documento no es válida.</div>
                        </div>
                        <div class="form-group">
                            <label for="fecha_nacimiento_e" class="required">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento_e" name="fecha_nacimiento_e" required onchange="validateAgeRange(this, 'fecha_nacimiento_e-error', 5, 20)">
                            <div id="fecha_nacimiento_e-error" class="invalid-feedback">La edad del estudiante debe estar entre 5 y 20 años.</div>
                        </div>
                        <div class="form-group"><label for="fecha_expedicion_e">Fecha de Expedición</label><input type="date" id="fecha_expedicion_e" name="fecha_expedicion_e" onchange="validateNotFutureYear(this, 'fecha_expedicion_e-error')">
                            <div id="fecha_expedicion_e-error" class="invalid-feedback">El año de expedición no puede ser superior al actual.</div></div>
                        <div class="form-group">
                            <label for="id_grado_e" class="required">Grado</label>
                            <select id="id_grado_e" name="id_grado_e" required>
                                <option value="" disabled selected>Cargando grados...</option>
                            </select>
                            <div id="id_grado_e-error" class="invalid-feedback">Por favor, selecciona un grado.</div>
                        </div>

                        <div class="form-group">
                            <label for="no_documento_acudiente_e" class="required">No. Documento del Acudiente</label>
                            <input type="text" id="no_documento_acudiente_e" name="no_documento_acudiente_e" required placeholder="Ej: 1234567890" oninput="validateNumbersOnly(this, 'no_documento_acudiente_e-error')">
                            <div id="no_documento_acudiente_e-error" class="invalid-feedback">Solo se permiten números.</div>
                        </div>
                        <div class="form-group"><label for="sexo_e" class="required">Sexo</label><select id="sexo_e" name="sexo_e" required><option value="Masculino">Masculino</option><option value="Femenino">Femenino</option></select></div>
                        <div class="form-group">
                            <label for="email_e" class="required">Correo Electrónico</label>
                            <input type="email" id="email_e" name="email_e" required placeholder="estudiante@correo.com" oninput="validateEmail(this, 'email_e-error')">
                            <div id="email_e-error" class="invalid-feedback">El formato del correo no es válido.</div>
                        </div>
                        <div class="form-group">
                            <label for="telefono_e" class="required">Teléfono</label>
                            <input type="text" id="telefono_e" name="telefono_e" required placeholder="Ej: 3123456789" oninput="validateNumbersOnly(this, 'telefono_e-error')">
                            <div id="telefono_e-error" class="invalid-feedback">Solo se permiten números.</div>
                        </div>
                        <div class="form-group"><label for="direccion_e">Dirección</label><input type="text" id="direccion_e" name="direccion_e" placeholder="Ej: Calle 123"></div>
                        <div class="form-group"><label for="barrio_e">Barrio</label><input type="text" id="barrio_e" name="barrio_e" placeholder="Ej: Chapinero"></div>
                        <div class="form-group">
                            <label for="estratosocieconomico_e" class="required">Estrato Socioeconómico</label>
                            <input type="number" id="estratosocieconomico_e" name="estratosocieconomico_e" required placeholder="Ej: 3" oninput="validateNumberRange(this, 'estratosocieconomico_e-error', 1, 6)">
                            <div id="estratosocieconomico_e-error" class="invalid-feedback">El estrato debe ser un número entre 1 y 6.</div>
                        </div>
                        <div class="form-group">
                            <label for="gruposisben_e" class="required">Grupo Sisbén</label>
                            <input type="text" id="gruposisben_e" name="gruposisben_e" required placeholder="Ej: A1">
                        </div>
                        <div class="form-group"><label for="nacionalidad_e">Nacionalidad</label><input type="text" id="nacionalidad_e" name="nacionalidad_e" placeholder="Ej: Colombiana"></div>
                        <div class="form-group"><label for="pais_ori_e">País de Origen</label><input type="text" id="pais_ori_e" name="pais_ori_e" placeholder="Ej: Colombia"></div>
                        <div class="form-group"><label for="ciudad_nacimiento_e">Ciudad de Nacimiento</label><input type="text" id="ciudad_nacimiento_e" name="ciudad_nacimiento_e" placeholder="Ej: Bogotá"></div>
                        <div class="form-group"><label for="ciudad_expedicion_e" class="required">Ciudad de Expedición</label><input type="text" id="ciudad_expedicion_e" name="ciudad_expedicion_e" required placeholder="Ej: Bogotá"></div>
                        <div class="form-group"><label for="rh_e" class="required">RH</label><input type="text" id="rh_e" name="rh_e" required placeholder="Ej: A+"></div>
                        <div class="form-group"><label for="eps_e" class="required">EPS</label><input type="text" id="eps_e" name="eps_e" required placeholder="Ej: Sura"></div>
                        <div class="form-group"><label for="vivecon_e" class="required">Vive Con</label><input type="text" id="vivecon_e" name="vivecon_e" required placeholder="Ej: Padres"></div>
                        <div class="form-group"><label for="etnia_e" class="required">Etnia</label><input type="text" id="etnia_e" name="etnia_e" required placeholder="Ej: Mestizo"></div>
                        <div class="form-group"><label for="situacionsocial_e" class="required">Situación Social</label><input type="text" id="situacionsocial_e" name="situacionsocial_e" required placeholder="Ej: Normal"></div>
                        <div class="form-group"><label for="alergias_e">Alergias</label><input type="text" id="alergias_e" name="alergias_e" placeholder="Ej: Ninguna"></div>
                        <div class="form-group"><label for="enfermedad_e">Enfermedades Conocidas</label><input type="text" id="enfermedad_e" name="enfermedad_e" placeholder="Ej: Asma" value="Ninguna"></div>
                        <div class="form-group"><label for="discapacidad_e">Discapacidad</label><input type="text" id="discapacidad_e" name="discapacidad_e" placeholder="Ej: Ninguna" value="Ninguna"></div>
                        <div class="form-group"><label for="desplazado_e" class="required">¿Es Desplazado?</label><select id="desplazado_e" name="desplazado_e" required><option value="No" selected>No</option><option value="Si">Sí</option></select></div>
                        <div class="form-group"><label for="numhermanos_e">Nº de Hermanos</label><input type="number" id="numhermanos_e" name="numhermanos_e" value="0" placeholder="Ej: 2"></div>
                        <div class="form-group"><label for="hermanoscole_e">Hermanos en el Colegio</label><input type="number" id="hermanoscole_e" name="hermanoscole_e" value="0" placeholder="Ej: 1"></div>
                        <div class="form-group policy-check"><input type="checkbox" id="politica-estudiante" name="politica" required><label for="politica-estudiante">Acepto la <a href="../multimedia/Politica_Tratamiento_Datos_Personales.pdf" target="_blank">Política de Tratamiento de Datos</a>.</label></div>
                    </div>
                    <button type="submit" class="btn-submit" id="btn-estudiante" disabled>Guardar Estudiante</button>
                </form>
            </div>
            
            <!-- Formulario para Administrador -->
            <div id="admin" class="tab-content">
                <h2>Registro de Administrador</h2>
                <form id="form-admin">
                    <div class="form-header"><p>Datos Personales del Administrador. Los campos con (*) son obligatorios.</p></div>
                    <div class="form-grid">
                        <!-- Core Identification & Contact (Most Important) -->
                        <div class="form-group">
                            <label for="primer_nombre_a" class="required">Primer Nombre</label>
                            <input type="text" id="primer_nombre_a" name="primer_nombre_a" required placeholder="Ej: Admin" oninput="validateLettersOnly(this, 'primer_nombre_a-error')">
                            <div id="primer_nombre_a-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="segundo_nombre_a">Segundo Nombre</label>
                            <input type="text" id="segundo_nombre_a" name="segundo_nombre_a" placeholder="Ej: David" oninput="validateLettersOnly(this, 'segundo_nombre_a-error')">
                            <div id="segundo_nombre_a-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="primer_apellido_a" class="required">Primer Apellido</label>
                            <input type="text" id="primer_apellido_a" name="primer_apellido_a" required placeholder="Ej: Jones" oninput="validateLettersOnly(this, 'primer_apellido_a-error')">
                            <div id="primer_apellido_a-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="segundo_apellido_a">Segundo Apellido</label>
                            <input type="text" id="segundo_apellido_a" name="segundo_apellido_a" placeholder="Ej: Smith" oninput="validateLettersOnly(this, 'segundo_apellido_a-error')">
                            <div id="segundo_apellido_a-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group">
                            <label for="tipo_documento_a" class="required">Tipo de Documento</label>
                            <select id="tipo_documento_a" name="tipo_documento_a" required onchange="validateDocumentLength('tipo_documento_a', 'no_documento_a', 'no_documento_a-error')">
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="CE">Cédula de Extranjería</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="no_documento_a" class="required">No. de Documento</label>
                            <input type="text" id="no_documento_a" name="no_documento_a" required placeholder="Ej: 9876543210" oninput="validateDocumentLength('tipo_documento_a', 'no_documento_a', 'no_documento_a-error')">
                            <div id="no_documento_a-error" class="invalid-feedback">La longitud del documento no es válida.</div>
                        </div>
                        <div class="form-group">
                            <label for="fecha_nacimiento_a" class="required">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento_a" name="fecha_nacimiento_a" required onchange="validateMinAge(this, 'fecha_nacimiento_a-error', 22)">
                            <div id="fecha_nacimiento_a-error" class="invalid-feedback">El administrador debe ser mayor de 22 años.</div>
                        </div>
                        <div class="form-group"><label for="fecha_expedicion_a">Fecha de Expedición</label><input type="date" id="fecha_expedicion_a" name="fecha_expedicion_a" onchange="validateNotFutureYear(this, 'fecha_expedicion_a-error')">
                            <div id="fecha_expedicion_a-error" class="invalid-feedback">El año de expedición no puede ser superior al actual.</div></div>
                        <div class="form-group">
                            <label for="email_a" class="required">Correo Electrónico</label>
                            <input type="email" id="email_a" name="email_a" required placeholder="admin@correo.com" oninput="validateEmail(this, 'email_a-error')">
                            <div id="email_a-error" class="invalid-feedback">El formato del correo no es válido.</div>
                        </div>
                        <div class="form-group">
                            <label for="telefono_a" class="required">Teléfono</label>
                            <input type="text" id="telefono_a" name="telefono_a" required placeholder="Ej: 3004445566" oninput="validateNumbersOnly(this, 'telefono_a-error')">
                            <div id="telefono_a-error" class="invalid-feedback">Solo se permiten números.</div>
                        </div>
                        <div class="form-group">
                            <label for="cargo_a" class="required">Cargo</label>
                            <input type="text" id="cargo_a" name="cargo_a" required placeholder="Ej: Coordinador Académico" oninput="validateLettersOnly(this, 'cargo_a-error')">
                            <div id="cargo_a-error" class="invalid-feedback">Solo se permiten letras.</div>
                        </div>
                        <div class="form-group"><label for="direccion_a" class="required">Dirección</label><input type="text" id="direccion_a" name="direccion_a" required placeholder="Ej: Calle Falsa 123"></div>
                        <div class="form-group"><label for="eps_a">EPS</label><input type="text" id="eps_a" name="eps_a" placeholder="Ej: Sura">
                        <div class="form-group"><label for="estado_civil_a">Estado Civil</label><select id="estado_civil_a" name="estado_civil_a"><option value="Soltero(a)">Soltero(a)</option><option value="Casado(a)">Casado(a)</option><option value="Otro">Otro</option></select></div>
                        <div class="form-group policy-check"><input type="checkbox" id="politica-admin" name="politica" required><label for="politica-admin">Acepto la <a href="../multimedia/Politica_Tratamiento_Datos_Personales.pdf" target="_blank">Política de Tratamiento de Datos</a>.</label></div>
                    </div>
                    <button type="submit" class="btn-submit" id="btn-admin" disabled>Guardar Administrador</button>
                </form>
            </div>

        </div>
    </div>

    <footer>
        <p>© 2025 Colegio San Juan Bosco. Todos los derechos reservados.</p>
        <p>Autores y Desarrolladores: Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita</p>
    </footer>
    <script src="../js/agregar_usuario.js"></script>
    <script src="../js/user_profile_manager.js"></script>
    <script src="../js/menu.js"></script>
    <script>
        // Script para manejar los tabs y temas del nuevo diseño
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab-btn');
            const contents = document.querySelectorAll('.tab-content');
            const body = document.body;

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Quitar clase activa de todos los tabs y contenidos
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));

                    // Aplicar clase activa al tab y contenido seleccionados
                    tab.classList.add('active');
                    document.getElementById(tab.dataset.tab).classList.add('active');

                    // Cambiar el tema del body
                    body.className = ''; // Limpiar clases de tema anteriores
                    body.classList.add(tab.dataset.theme);
                });
            });
        });
    </script>
</body>
</html>
