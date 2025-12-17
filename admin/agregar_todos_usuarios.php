<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
// 1. Verificación de Seguridad
// Incluye el script que verifica si hay una sesión activa y maneja la inactividad.
require_once '../php/verificar_sesion.php';

// Verifica si el rol del usuario es 'admin'. Si no, lo redirige.
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
    // Manejar el error si la consulta falla
    // Por ahora, el array $materias quedará vacío y el select no se poblará
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
    <title>Agregar Todos los Usuarios</title>
    <link rel="stylesheet" href="../style/agregar_usuario.css">
</head>
<body class="theme-acudiente">
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name">Agregar Usuarios - Administrador</span>
        </div>
    </div>

    <!-- Menú Lateral -->
    <div class="menu-container" id="menu-container">
        <div class="profile">
            <img src="../multimedia/pagina_principal/usuario.png" alt="Usuario" class="profile-pic">
            <h3 class="user-name">Cargando...</h3>
            <p class="user-role">Cargando...</p>
        </div>
        <nav class="menu">
            <ul>
                <li><a href="admin.php" class="menu-link"><img src="../multimedia/administrador/home.png" alt=""> Inicio</a></li>
                <li><a href="agregar_usuario.php" class="menu-link"><img src="../multimedia/administrador/agregar-usuario.png" alt=""> Agregar Usuario (Tabbed)</a></li>
                <li><a href="agregar_todos_usuarios.php" class="menu-link"><img src="../multimedia/administrador/agregar-usuario.png" alt=""> Agregar Todos los Usuarios</a></li>
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
        <div class="content-container">
            <div class="container">
                <header>
                    <h1>Gestión de Usuarios</h1>
                    <p>Complete los formularios para registrar nuevos usuarios en el sistema.</p>
                </header>

                <div id="message-container" class="message" style="display: none;"></div>

                <!-- Formulario para Acudiente -->
                <div id="acudiente">
                    <h2><i class="fas fa-user-shield"></i> Registro de Acudiente</h2>
                    <form id="form-acudiente">
                        <div class="form-header"><p>Datos Personales. Los campos con (*) son obligatorios.</p></div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="primer_nombre" class="required">Primer Nombre</label>
                                <input type="text" id="primer_nombre" name="primer_nombre" required oninput="validateLettersOnly(this, 'primer_nombre-error'); validateLength(this, 'primer_nombre-length-error', 50)" placeholder="Ej: Ana">
                                <div class="invalid-feedback" id="primer_nombre-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="primer_nombre-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="segundo_nombre">Segundo Nombre</label>
                                <input type="text" id="segundo_nombre" name="segundo_nombre" oninput="validateLettersOnly(this, 'segundo_nombre-error'); validateLength(this, 'segundo_nombre-length-error', 50)" placeholder="Ej: María">
                                <div class="invalid-feedback" id="segundo_nombre-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="segundo_nombre-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="primer_apellido" class="required">Primer Apellido</label>
                                <input type="text" id="primer_apellido" name="primer_apellido" required oninput="validateLettersOnly(this, 'primer_apellido-error'); validateLength(this, 'primer_apellido-length-error', 50)" placeholder="Ej: Pérez">
                                <div class="invalid-feedback" id="primer_apellido-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="primer_apellido-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="segundo_apellido">Segundo Apellido</label>
                                <input type="text" id="segundo_apellido" name="segundo_apellido" oninput="validateLettersOnly(this, 'segundo_apellido-error'); validateLength(this, 'segundo_apellido-length-error', 50)" placeholder="Ej: Gómez">
                                <div class="invalid-feedback" id="segundo_apellido-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="segundo_apellido-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tipo_documento" class="required">Tipo de Documento</label>
                                <select id="tipo_documento" name="tipo_documento" required><option value="CC">Cédula de Ciudadanía</option><option value="CE">Cédula de Extranjería</option><option value="PA">Pasaporte</option></select>
                            </div>
                            <div class="form-group">
                                <label for="no_documento" class="required">No. de Documento</label>
                                <input type="text" id="no_documento" name="no_documento" required oninput="validateNumbersOnly(this, 'no_documento-error'); validateLength(this, 'no_documento-length-error', 10)" placeholder="Ej: 1234567890">
                                <div class="invalid-feedback" id="no_documento-error">Solo se permiten números.</div>
                                <div class="invalid-feedback" id="no_documento-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="ciudad_expedicion" class="required">Ciudad de Expedición</label>
                                <input type="text" id="ciudad_expedicion" name="ciudad_expedicion" required oninput="validateLength(this, 'ciudad_expedicion-length-error', 100)" placeholder="Ej: Bogotá">
                                <div class="invalid-feedback" id="ciudad_expedicion-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="fecha_nacimiento" class="required">Fecha de Nacimiento</label>
                                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required onchange="validateAgeRange(this, 'fecha_nacimiento-error', 14, 80)">
                                <div class="invalid-feedback" id="fecha_nacimiento-error">El acudiente debe tener entre 14 y 80 años.</div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="sexo" class="required">Sexo</label>
                                <select id="sexo" name="sexo" required>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="rh" class="required">RH</label>
                                <input type="text" id="rh" name="rh" required oninput="validateLength(this, 'rh-length-error', 5)" placeholder="Ej: O+">
                                <div class="invalid-feedback" id="rh-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email" class="required">Correo Electrónico</label>
                                <input type="email" id="email" name="email" required oninput="validateEmail(this, 'email-error'); validateLength(this, 'email-length-error', 100)" placeholder="ejemplo@correo.com">
                                <div class="invalid-feedback" id="email-error">Por favor, ingrese un correo válido.</div>
                                <div class="invalid-feedback" id="email-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="telefono" class="required">Teléfono</label>
                                <input type="text" id="telefono" name="telefono" required oninput="validateNumbersOnly(this, 'telefono-error'); validateLength(this, 'telefono-length-error', 20)" placeholder="Ej: 3101234567">
                                <div class="invalid-feedback" id="telefono-error">Solo se permiten números.</div>
                                <div class="invalid-feedback" id="telefono-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="direccionp" class="required">Dirección Principal</label>
                                <input type="text" id="direccionp" name="direccionp" required oninput="validateLength(this, 'direccionp-length-error', 255)" placeholder="Ej: Calle 10 #20-30">
                                <div class="invalid-feedback" id="direccionp-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="barrio">Barrio</label>
                                <input type="text" id="barrio" name="barrio" oninput="validateLength(this, 'barrio-length-error', 100)" placeholder="Ej: Chapinero">
                                <div class="invalid-feedback" id="barrio-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="lugar_recidencia">Lugar de Residencia</label>
                                <input type="text" id="lugar_recidencia" name="lugar_recidencia" oninput="validateLength(this, 'lugar_recidencia-length-error', 100)" placeholder="Ej: Bogotá">
                                <div class="invalid-feedback" id="lugar_recidencia-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nacionalidad">Nacionalidad</label>
                                <input type="text" id="nacionalidad" name="nacionalidad" oninput="validateLength(this, 'nacionalidad-length-error', 50)" placeholder="Ej: Colombiana">
                                <div class="invalid-feedback" id="nacionalidad-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="alergias">Alergias</label>
                                <input type="text" id="alergias" name="alergias" oninput="validateLength(this, 'alergias-length-error', 255)" placeholder="Ej: Maní, Polvo">
                                <div class="invalid-feedback" id="alergias-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="profesion">Profesión</label>
                                <input type="text" id="profesion" name="profesion" oninput="validateLength(this, 'profesion-length-error', 50)" placeholder="Ej: Ingeniería de Sistemas">
                                <div class="invalid-feedback" id="profesion-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="empresa">Empresa donde labora</label>
                                <input type="text" id="empresa" name="empresa" oninput="validateLength(this, 'empresa-length-error', 50)" placeholder="Ej: Acme Inc.">
                                <div class="invalid-feedback" id="empresa-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="ocupacion" class="required">Ocupación</label>
                                <input type="text" id="ocupacion" name="ocupacion" required oninput="validateLength(this, 'ocupacion-length-error', 50)" placeholder="Ej: Desarrollador de Software">
                                <div class="invalid-feedback" id="ocupacion-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="religion" class="required">Religión</label>
                                <input type="text" id="religion" name="religion" required oninput="validateLength(this, 'religion-length-error', 50)" placeholder="Ej: Católica">
                                <div class="invalid-feedback" id="religion-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nivel_estudio" class="required">Nivel de Estudio</label>
                                <select id="nivel_estudio" name="nivel_estudio" required>
                                    <option value="" disabled selected>Seleccione...</option>
                                    <option value="Primaria">Primaria</option>
                                    <option value="Secundaria">Secundaria</option>
                                    <option value="Técnico">Técnico</option>
                                    <option value="Universitario">Universitario</option>
                                    <option value="Postgrado">Postgrado</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="afiliado" class="required">¿Está Afiliado a Salud?</label>
                                <select id="afiliado" name="afiliado" required>
                                    <option value="Si">Sí</option>
                                    <option value="No" selected>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="afi_detalles">Detalles de Afiliación (EPS)</label>
                                <input type="text" id="afi_detalles" name="afi_detalles" oninput="validateLength(this, 'afi_detalles-length-error', 50)" placeholder="Ej: Sura EPS">
                                <div class="invalid-feedback" id="afi_detalles-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="parentesco" class="required">Parentesco</label>
                                <select id="parentesco" name="parentesco" required>
                                    <option value="" disabled selected>Seleccione...</option>
                                    <option value="Padre">Padre</option>
                                    <option value="Madre">Madre</option>
                                    <option value="Tutor">Tutor</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="estado_civil">Estado Civil</label>
                                <select id="estado_civil" name="estado_civil">
                                    <option value="Soltero(a)">Soltero(a)</option>
                                    <option value="Casado(a)">Casado(a)</option>
                                    <option value="Unión Libre">Unión Libre</option>
                                    <option value="Viudo(a)">Viudo(a)</option>
                                    <option value="Divorciado(a)">Divorciado(a)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group policy-check">
                            <input type="checkbox" id="politica-acudiente" name="politica" required>
                            <label for="politica-acudiente">He leído y acepto la <a href="../multimedia/Politica_Tratamiento_Datos_Personales.pdf" target="_blank">Política de Tratamiento de Datos Personales</a>.</label>
                        </div>
                        <button type="submit" class="btn-success" id="btn-acudiente" disabled><i class="fas fa-save"></i> Guardar Acudiente</button>
                    </form>
                </div>

                <!-- Formulario para Profesor -->
                <div id="profesor">
                    <h2><i class="fas fa-chalkboard-teacher"></i> Registro de Profesor</h2>
                    <form id="form-profesor">
                         <div class="form-header"><p>Datos Personales. Los campos con (*) son obligatorios.</p></div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="primer_nombre_p" class="required">Primer Nombre</label>
                                <input type="text" id="primer_nombre_p" name="primer_nombre_p" required oninput="validateLettersOnly(this, 'primer_nombre_p-error'); validateLength(this, 'primer_nombre_p-length-error', 50)" placeholder="Ej: Juan">
                                <div class="invalid-feedback" id="primer_nombre_p-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="primer_nombre_p-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="segundo_nombre_p">Segundo Nombre</label>
                                <input type="text" id="segundo_nombre_p" name="segundo_nombre_p" oninput="validateLettersOnly(this, 'segundo_nombre_p-error'); validateLength(this, 'segundo_nombre_p-length-error', 50)" placeholder="Ej: Carlos">
                                <div class="invalid-feedback" id="segundo_nombre_p-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="segundo_nombre_p-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="primer_apellido_p" class="required">Primer Apellido</label>
                                <input type="text" id="primer_apellido_p" name="primer_apellido_p" required oninput="validateLettersOnly(this, 'primer_apellido_p-error'); validateLength(this, 'primer_apellido_p-length-error', 50)" placeholder="Ej: Rodríguez">
                                <div class="invalid-feedback" id="primer_apellido_p-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="primer_apellido_p-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="segundo_apellido_p">Segundo Apellido</label>
                                <input type="text" id="segundo_apellido_p" name="segundo_apellido_p" oninput="validateLettersOnly(this, 'segundo_apellido_p-error'); validateLength(this, 'segundo_apellido_p-length-error', 50)" placeholder="Ej: López">
                                <div class="invalid-feedback" id="segundo_apellido_p-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="segundo_apellido_p-length-error"></div>
                            </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tipo_documento_p" class="required">Tipo de Documento</label>
                                <select id="tipo_documento_p" name="tipo_documento_p" required><option value="CC">Cédula de Ciudadanía</option><option value="CE">Cédula de Extranjería</option><option value="PA">Pasaporte</option></select>
                            </div>
                            <div class="form-group">
                                <label for="no_documento_p" class="required">No. de Documento</label>
                                <input type="text" id="no_documento_p" name="no_documento_p" required oninput="validateNumbersOnly(this, 'no_documento_p-error'); validateLength(this, 'no_documento_p-length-error', 10)" placeholder="Ej: 1098765432">
                                <div class="invalid-feedback" id="no_documento_p-error">Solo se permiten números.</div>
                                <div class="invalid-feedback" id="no_documento_p-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fecha_nacimiento_p" class="required">Fecha de Nacimiento</label>
                                <input type="date" id="fecha_nacimiento_p" name="fecha_nacimiento_p" required onchange="validateMinAge(this, 'fecha_nacimiento_p-error', 18)">
                                <div class="invalid-feedback" id="fecha_nacimiento_p-error">El profesor debe ser mayor de edad.</div>
                            </div>
                            <div class="form-group">
                                <label for="email_p" class="required">Correo Electrónico</label>
                                <input type="email" id="email_p" name="email_p" required oninput="validateEmail(this, 'email_p-error'); validateLength(this, 'email_p-length-error', 100)" placeholder="ejemplo@correo.com">
                                <div class="invalid-feedback" id="email_p-error">Por favor, ingrese un correo válido.</div>
                                <div class="invalid-feedback" id="email_p-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefono_p">Teléfono</label>
                                <input type="text" id="telefono_p" name="telefono_p" oninput="validateNumbersOnly(this, 'telefono_p-error'); validateLength(this, 'telefono_p-length-error', 15)" placeholder="Ej: 3219876543">
                                <div class="invalid-feedback" id="telefono_p-error">Solo se permiten números.</div>
                                <div class="invalid-feedback" id="telefono_p-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="direccion_p">Dirección</label>
                                <input type="text" id="direccion_p" name="direccion_p" oninput="validateLength(this, 'direccion_p-length-error', 100)" placeholder="Ej: Av. Siempre Viva 742">
                                <div class="invalid-feedback" id="direccion_p-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="titulo_academico_p">Título Académico</label>
                                <input type="text" id="titulo_academico_p" name="titulo_academico_p" oninput="validateLength(this, 'titulo_academico_p-length-error', 50)" placeholder="Ej: Licenciado en Matemáticas">
                                <div class="invalid-feedback" id="titulo_academico_p-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="especialidad_p" class="required">Especialidad</label>
                                <input type="text" id="especialidad_p" name="especialidad_p" required oninput="validateLength(this, 'especialidad_p-length-error', 50)" placeholder="Ej: Cálculo Diferencial">
                                <div class="invalid-feedback" id="especialidad_p-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="id_materia_p" class="required">Materia Principal</label>
                                <select id="id_materia_p" name="id_materia_p" required>
                                    <option value="">Seleccione una materia</option>
                                    <?php foreach ($materias as $materia): ?>
                                        <option value="<?php echo htmlspecialchars($materia['id_materia']); ?>">
                                            <?php echo htmlspecialchars($materia['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group policy-check">
                            <input type="checkbox" id="politica-profesor" name="politica" required>
                            <label for="politica-profesor">He leído y acepto la <a href="../multimedia/Politica_Tratamiento_Datos_Personales.pdf" target="_blank">Política de Tratamiento de Datos Personales</a>.</label>
                        </div>
                        <button type="submit" class="btn-success" id="btn-profesor" disabled><i class="fas fa-save"></i> Guardar Profesor</button>
                    </form>
                </div>

                <!-- Formulario para Estudiante -->
                <div id="estudiante">
                    <h2><i class="fas fa-user-graduate"></i> Registro de Estudiante</h2>
                    <form id="form-estudiante">
                        <div class="form-header"><p>Datos Personales. Los campos con (*) son obligatorios.</p></div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="primer_nombre_e" class="required">Primer Nombre</label>
                                <input type="text" id="primer_nombre_e" name="primer_nombre_e" required oninput="validateLettersOnly(this, 'primer_nombre_e-error'); validateLength(this, 'primer_nombre_e-length-error', 50)" placeholder="Ej: Luis">
                                <div class="invalid-feedback" id="primer_nombre_e-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="primer_nombre_e-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="segundo_nombre_e">Segundo Nombre</label>
                                <input type="text" id="segundo_nombre_e" name="segundo_nombre_e" oninput="validateLettersOnly(this, 'segundo_nombre_e-error'); validateLength(this, 'segundo_nombre_e-length-error', 50)" placeholder="Ej: Miguel">
                                <div class="invalid-feedback" id="segundo_nombre_e-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="segundo_nombre_e-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="primer_apellido_e" class="required">Primer Apellido</label>
                                <input type="text" id="primer_apellido_e" name="primer_apellido_e" required oninput="validateLettersOnly(this, 'primer_apellido_e-error'); validateLength(this, 'primer_apellido_e-length-error', 50)" placeholder="Ej: González">
                                <div class="invalid-feedback" id="primer_apellido_e-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="primer_apellido_e-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="segundo_apellido_e">Segundo Apellido</label>
                                <input type="text" id="segundo_apellido_e" name="segundo_apellido_e" oninput="validateLettersOnly(this, 'segundo_apellido_e-error'); validateLength(this, 'segundo_apellido_e-length-error', 50)" placeholder="Ej: García">
                                <div class="invalid-feedback" id="segundo_apellido_e-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="segundo_apellido_e-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tipo_documento_e" class="required">Tipo de Documento</label>
                                <select id="tipo_documento_e" name="tipo_documento_e" required><option value="TI">Tarjeta de Identidad</option><option value="RC">Registro Civil</option><option value="CE">Cédula de Extranjería</option></select>
                            </div>
                            <div class="form-group">
                                <label for="no_documento_e" class="required">No. de Documento</label>
                                <input type="text" id="no_documento_e" name="no_documento_e" required oninput="validateNumbersOnly(this, 'no_documento_e-error'); validateLength(this, 'no_documento_e-length-error', 10)" placeholder="Ej: 1002003004">
                                <div class="invalid-feedback" id="no_documento_e-error">Solo se permiten números.</div>
                                <div class="invalid-feedback" id="no_documento_e-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fecha_nacimiento_e" class="required">Fecha de Nacimiento</label>
                                <input type="date" id="fecha_nacimiento_e" name="fecha_nacimiento_e" required onchange="validateAgeRange(this, 'fecha_nacimiento_e-error', 5, 18)">
                                <div class="invalid-feedback" id="fecha_nacimiento_e-error">El estudiante debe tener entre 5 y 18 años.</div>
                            </div>
                            <div class="form-group">
                                <label for="sexo_e" class="required">Sexo</label>
                                <select id="sexo_e" name="sexo_e" required>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="direccion_e" class="required">Dirección</label>
                                <input type="text" id="direccion_e" name="direccion_e" required oninput="validateLength(this, 'direccion_e-length-error', 100)" placeholder="Ej: Carrera 5 # 15-25">
                                <div class="invalid-feedback" id="direccion_e-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="telefono_e">Teléfono</label>
                                <input type="text" id="telefono_e" name="telefono_e" oninput="validateNumbersOnly(this, 'telefono_e-error'); validateLength(this, 'telefono_e-length-error', 15)" placeholder="Ej: 3001112233">
                                <div class="invalid-feedback" id="telefono_e-error">Solo se permiten números.</div>
                                <div class="invalid-feedback" id="telefono_e-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email_e" class="required">Correo Electrónico</label>
                                <input type="email" id="email_e" name="email_e" required oninput="validateEmail(this, 'email_e-error'); validateLength(this, 'email_e-length-error', 100)" placeholder="ejemplo@correo.com">
                                <div class="invalid-feedback" id="email_e-error">Por favor, ingrese un correo válido.</div>
                                <div class="invalid-feedback" id="email_e-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="no_documento_acudiente_e" class="required">No. Documento del Acudiente</label>
                                <input type="text" id="no_documento_acudiente_e" name="no_documento_acudiente_e" required oninput="validateNumbersOnly(this, 'no_documento_acudiente_e-error'); validateLength(this, 'no_documento_acudiente_e-length-error', 20)" placeholder="Ej: 1234567890">
                                <div class="invalid-feedback" id="no_documento_acudiente_e-error">Solo se permiten números.</div>
                                <div class="invalid-feedback" id="no_documento_acudiente_e-length-error"></div>
                            </div>
                        </div>
                        <div class="form-group policy-check">
                            <input type="checkbox" id="politica-estudiante" name="politica" required>
                            <label for="politica-estudiante">He leído y acepto la <a href="../multimedia/Politica_Tratamiento_Datos_Personales.pdf" target="_blank">Política de Tratamiento de Datos Personales</a>.</label>
                        </div>
                        <button type="submit" class="btn-success" id="btn-estudiante" disabled><i class="fas fa-save"></i> Guardar Estudiante</button>
                    </form>
                </div>

                <!-- Formulario para Administrador -->
                <div id="admin">
                    <h2><i class="fas fa-user-tie"></i> Registro de Administrador</h2>
                    <form id="form-admin">
                        <div class="form-header"><p>Datos Personales. Los campos con (*) son obligatorios.</p></div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="primer_nombre_a" class="required">Primer Nombre</label>
                                <input type="text" id="primer_nombre_a" name="primer_nombre_a" required oninput="validateLettersOnly(this, 'primer_nombre_a-error'); validateLength(this, 'primer_nombre_a-length-error', 50)" placeholder="Ej: Carlos">
                                <div class="invalid-feedback" id="primer_nombre_a-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="primer_nombre_a-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="segundo_nombre_a">Segundo Nombre</label>
                                <input type="text" id="segundo_nombre_a" name="segundo_nombre_a" oninput="validateLettersOnly(this, 'segundo_nombre_a-error'); validateLength(this, 'segundo_nombre_a-length-error', 50)" placeholder="Ej: Andrés">
                                <div class="invalid-feedback" id="segundo_nombre_a-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="segundo_nombre_a-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="primer_apellido_a" class="required">Primer Apellido</label>
                                <input type="text" id="primer_apellido_a" name="primer_apellido_a" required oninput="validateLettersOnly(this, 'primer_apellido_a-error'); validateLength(this, 'primer_apellido_a-length-error', 50)" placeholder="Ej: Restrepo">
                                <div class="invalid-feedback" id="primer_apellido_a-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="primer_apellido_a-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="segundo_apellido_a">Segundo Apellido</label>
                                <input type="text" id="segundo_apellido_a" name="segundo_apellido_a" oninput="validateLettersOnly(this, 'segundo_apellido_a-error'); validateLength(this, 'segundo_apellido_a-length-error', 50)" placeholder="Ej: Duque">
                                <div class="invalid-feedback" id="segundo_apellido_a-error">Solo se permiten letras y espacios.</div>
                                <div class="invalid-feedback" id="segundo_apellido_a-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="tipo_documento_a" class="required">Tipo de Documento</label>
                                <select id="tipo_documento_a" name="tipo_documento_a" required><option value="CC">Cédula de Ciudadanía</option><option value="CE">Cédula de Extranjería</option><option value="PA">Pasaporte</option></select>
                            </div>
                            <div class="form-group">
                                <label for="no_documento_a" class="required">No. de Documento</label>
                                <input type="text" id="no_documento_a" name="no_documento_a" required oninput="validateNumbersOnly(this, 'no_documento_a-error'); validateLength(this, 'no_documento_a-length-error', 10)" placeholder="Ej: 1000200030">
                                <div class="invalid-feedback" id="no_documento_a-error">Solo se permiten números.</div>
                                <div class="invalid-feedback" id="no_documento_a-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fecha_nacimiento_a" class="required">Fecha de Nacimiento</label>
                                <input type="date" id="fecha_nacimiento_a" name="fecha_nacimiento_a" required oninput="validateMinAge(this, 'fecha_nacimiento_a-error', 18)">
                                <div class="invalid-feedback" id="fecha_nacimiento_a-error">El administrador debe ser mayor de edad.</div>
                            </div>
                            <div class="form-group">
                                <label for="email_a" class="required">Correo Electrónico</label>
                                <input type="email" id="email_a" name="email_a" required oninput="validateEmail(this, 'email_a-error'); validateLength(this, 'email_a-length-error', 100)" placeholder="ejemplo@correo.com">
                                <div class="invalid-feedback" id="email_a-error">Por favor, ingrese un correo válido.</div>
                                <div class="invalid-feedback" id="email_a-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefono_a">Teléfono</label>
                                <input type="text" id="telefono_a" name="telefono_a" oninput="validateNumbersOnly(this, 'telefono_a-error'); validateLength(this, 'telefono_a-length-error', 15)" placeholder="Ej: 3004445566">
                                <div class="invalid-feedback" id="telefono_a-error">Solo se permiten números.</div>
                                <div class="invalid-feedback" id="telefono_a-length-error"></div>
                            </div>
                            <div class="form-group">
                                <label for="direccion_a">Dirección</label>
                                <input type="text" id="direccion_a" name="direccion_a" oninput="validateLength(this, 'direccion_a-length-error', 100)" placeholder="Ej: Calle 100 # 50-60">
                                <div class="invalid-feedback" id="direccion_a-length-error"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="cargo_a" class="required">Cargo</label>
                                <input type="text" id="cargo_a" name="cargo_a" required oninput="validateLength(this, 'cargo_a-error', 50)" placeholder="Ej: Gerente de TI">
                                <div class="invalid-feedback" id="cargo_a-error"></div>
                            </div>
                        </div>
                        <div class="form-group policy-check">
                            <input type="checkbox" id="politica-admin" name="politica" required>
                            <label for="politica-admin">He leído y acepto la <a href="../multimedia/Politica_Tratamiento_Datos_Personales.pdf" target="_blank">Política de Tratamiento de Datos Personales</a>.</label>
                        </div>
                        <button type="submit" class="btn-success" id="btn-admin" disabled><i class="fas fa-save"></i> Guardar Administrador</button>
                    </form>
                </div>

                <div class="system-info">
                    <p><i class="fas fa-info-circle"></i> Las credenciales de inicio de sesión se generarán automáticamente.</p>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2025 Colegio San Juan Bosco. Todos los derechos reservados.</p>
        <p>Autores y Desarrolladores: Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita</p>
    </footer>
    
    <script src="../js/user_profile_manager.js"></script>
    <script src="../js/agregar_usuario.js"></script>
</body>
</html>