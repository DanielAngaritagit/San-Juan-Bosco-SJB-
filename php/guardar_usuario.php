<?php
/**
 * Script para registrar nuevos usuarios en el sistema (Padres, Profesores, Estudiantes).
 * Recibe datos en formato JSON, los valida, sanitiza y crea los registros correspondientes
 * en múltiples tablas de la base de datos dentro de una transacción.
 */

// --- Configuración de Errores y Headers ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// --- Inclusión de Dependencias ---
require_once 'conexion.php';

// =============================================================================
// --- Funciones Auxiliares (Helper Functions) ---
// =============================================================================

/**
 * Limpia y sanitiza un array de datos de entrada.
 * @param array $data Datos del formulario.
 * @return array Datos sanitizados.
 */
function sanitize_input($data) {
    $sanitized_data = [];
    foreach ($data as $key => $value) {
        $sanitized_data[$key] = htmlspecialchars(trim($value));
    }
    return $sanitized_data;
}

/**
 * Valida los datos específicos para un nuevo padre.
 * @param array $data Datos del padre.
 * @return array Un array de mensajes de error. Vacío si no hay errores.
 */
function validate_padre($data) {
    $errors = [];
    if (empty($data['primer_nombre'])) $errors[] = 'El primer nombre es requerido.';
    if (empty($data['primer_apellido'])) $errors[] = 'El primer apellido es requerido.';
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El email no es válido.';
    }
    // Aquí se pueden añadir más reglas de validación.
    return $errors;
}

// ... (Funciones de validación para profesor y estudiante, si son necesarias) ...

/**
 * Crea un nuevo registro en la tabla 'login' con una contraseña por defecto.
 * @param PDO $pdo Conexión a la base de datos.
 * @param string $usuario El nombre de usuario (generalmente el número de documento).
 * @param string $rol El rol del nuevo usuario.
 * @return array Un array con el id de login y la contraseña en texto plano.
 */
function create_login($pdo, $usuario, $rol, $nombres, $apellidos) {
    $primeras_dos_nombre = substr($nombres, 0, 2);
    $primeras_dos_apellido = substr($apellidos, 0, 2);
    $raw_password = $primeras_dos_nombre . $usuario . $primeras_dos_apellido;
    
    $default_password = password_hash($raw_password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO login (usuario, contrasena, rol) VALUES (?, ?, ?)");
    $stmt->execute([$usuario, $default_password, $rol]);
    
    return [
        'id_log' => $pdo->lastInsertId('login_id_log_seq'),
        'raw_password' => $raw_password
    ];
}

/**
 * Crea un nuevo registro en la tabla 'tab_acudiente'.
 * @param PDO $pdo Conexión a la base de datos.
 * @param int $id_log El ID de login asociado.
 * @param array $data Los datos del padre.
 */
function create_padre($pdo, $id_log, $data) {
    $sql = "INSERT INTO tab_acudiente (
        id_log, nombres, apellidos, tipo_documento, no_documento, fecha_expedicion, email, telefono, direccionp, parentesco, ciudad_expedicion, fecha_nacimiento, sexo, rh, lugar_recidencia, ocupacion, religion, nivel_estudio, afiliado, afi_detalles, profesion, empresa, nacionalidad, barrio, alergias, estado_civil, eps
    ) VALUES (
        :id_log, :nombres, :apellidos, :tipo_documento, :no_documento, :fecha_expedicion, :email, :telefono, :direccionp, :parentesco, :ciudad_expedicion, :fecha_nacimiento, :sexo, :rh, :lugar_recidencia, :ocupacion, :religion, :nivel_estudio, :afiliado, :afi_detalles, :profesion, :empresa, :nacionalidad, :barrio, :alergias, :estado_civil, :eps
    )";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id_log' => $id_log,
        ':nombres' => trim(($data['primer_nombre'] ?? '') . ' ' . ($data['segundo_nombre'] ?? '')),
        ':apellidos' => trim(($data['primer_apellido'] ?? '') . ' ' . ($data['segundo_apellido'] ?? '')),
        ':tipo_documento' => $data['tipo_documento'],
        ':no_documento' => $data['no_documento'],
        ':fecha_expedicion' => $data['fecha_expedicion'] ?? null,
        ':email' => $data['email'],
        ':telefono' => $data['telefono'],
        ':direccionp' => $data['direccionp'],
        ':parentesco' => $data['parentesco'],
        ':ciudad_expedicion' => $data['ciudad_expedicion'],
        ':fecha_nacimiento' => $data['fecha_nacimiento'],
        ':sexo' => $data['sexo'],
        ':rh' => $data['rh'],
        ':lugar_recidencia' => $data['lugar_recidencia'],
        ':ocupacion' => $data['ocupacion'],
        ':religion' => $data['religion'],
        ':nivel_estudio' => $data['nivel_estudio'],
        ':afiliado' => $data['afiliado'],
        ':afi_detalles' => $data['afi_detalles'],
        ':profesion' => $data['profesion'],
        ':empresa' => $data['empresa'],
        ':nacionalidad' => $data['nacionalidad'],
        ':barrio' => $data['barrio'],
        ':alergias' => $data['alergias'],
        ':estado_civil' => $data['estado_civil'],
        ':eps' => $data['eps'] ?? null
    ]);
}

function create_estudiante($pdo, $id_log, $data) {
    $stmt_acudiente = $pdo->prepare("SELECT id_acudiente FROM tab_acudiente WHERE no_documento = :no_documento");
    $stmt_acudiente->execute([':no_documento' => $data['no_documento_acudiente_e']]);
    $id_acudiente = $stmt_acudiente->fetchColumn();

    if (!$id_acudiente) {
        throw new Exception("El acudiente con el documento especificado no fue encontrado.");
    }

    // Get grado_numero from tab_grados using id_seccion
    $stmt_grado_info = $pdo->prepare("SELECT grado_numero FROM tab_grados WHERE id_seccion = :id_seccion");
    $stmt_grado_info->execute([':id_seccion' => $data['id_grado_e']]);
    $grado_numero = $stmt_grado_info->fetchColumn();

    if (!$grado_numero) {
        throw new Exception("Grado no encontrado para la sección seleccionada.");
    }

        $sql_estudiante = "INSERT INTO tab_estudiante (
            id_acudiente, nombres, apellido1, apellido2, tipo_documento, no_documento,
            ciudad_expedicion, fecha_nacimiento, fecha_expedicion, pais_ori, nacionalidad,
            sexo, rh, ciudad_nacimiento, direccion, barrio, telefonos, email, vivecon,
            estratosocieconomico, gruposisben, numhermanos, hermanoscole,
            enfermedad, eps, alergias, discapacidad, etnia,
            desplazado, fecha, id_seccion, grado
        ) VALUES (
            :id_acudiente, :nombres, :apellido1, :apellido2, :tipo_documento, :no_documento,
            :ciudad_expedicion, :fecha_nacimiento, :fecha_expedicion, :pais_ori, :nacionalidad,
            :sexo, :rh, :ciudad_nacimiento, :direccion, :barrio, :telefonos, :email, :vivecon,
            :estratosocieconomico, :gruposisben, :numhermanos, :hermanoscole,
            :enfermedad, :eps, :alergias, :discapacidad, :etnia,
            :desplazado, :fecha, :id_seccion, :grado
        )";
    $stmt = $pdo->prepare($sql_estudiante);

    $stmt->execute([
        ':id_acudiente' => $id_acudiente,
        ':nombres' => trim(($data['primer_nombre_e'] ?? '') . ' ' . ($data['segundo_nombre_e'] ?? '')),
        ':apellido1' => $data['primer_apellido_e'],
        ':apellido2' => $data['segundo_apellido_e'],
        ':tipo_documento' => $data['tipo_documento_e'],
        ':no_documento' => $data['no_documento_e'],
        ':sexo' => $data['sexo_e'],
        ':direccion' => $data['direccion_e'],
        ':telefonos' => $data['telefono_e'],
        ':email' => $data['email_e'],
        ':fecha_nacimiento' => $data['fecha_nacimiento_e'],
        ':fecha_expedicion' => $data['fecha_expedicion_e'],
        ':ciudad_expedicion' => $data['ciudad_expedicion_e'],
        ':pais_ori' => $data['pais_ori_e'],
        ':nacionalidad' => $data['nacionalidad_e'],
        ':rh' => $data['rh_e'],
        ':ciudad_nacimiento' => $data['ciudad_nacimiento_e'],
        ':barrio' => $data['barrio_e'],
        ':vivecon' => $data['vivecon_e'],
        ':estratosocieconomico' => $data['estratosocieconomico_e'],
        ':gruposisben' => $data['gruposisben_e'],
        ':numhermanos' => $data['numhermanos_e'] ?? 0,
        ':hermanoscole' => $data['hermanoscole_e'] ?? 0,
        ':enfermedad' => $data['enfermedad_e'] ?? 'Ninguna',
        ':eps' => $data['eps_e'],
        ':alergias' => $data['alergias_e'],
        ':discapacidad' => $data['discapacidad_e'] ?? 'Ninguna',
        ':etnia' => $data['etnia_e'],
        ':desplazado' => $data['desplazado_e'] ?? 'No',
        ':fecha' => date('Y-m-d'), // Current date
        ':id_seccion' => $data['id_grado_e'],
        ':grado' => $grado_numero
    ]);
}

function create_profesor($pdo, $id_log, $data) {
    $sql = "INSERT INTO tab_profesores (
        id_log, nombres, apellidos, email, telefono, direccion, titulo_academico, tipo_documento, no_documento, fecha_expedicion, fecha_nacimiento, especialidad, id_materia, nacionalidad, rh, alergias, eps, estado_civil
    ) VALUES (
        :id_log, :nombres, :apellidos, :email, :telefono, :direccion, :titulo_academico, :tipo_documento, :no_documento, :fecha_expedicion, :fecha_nacimiento, :especialidad, :id_materia, :nacionalidad, :rh, :alergias, :eps, :estado_civil
    )";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id_log' => $id_log,
        ':nombres' => trim(($data['primer_nombre_p'] ?? '') . ' ' . ($data['segundo_nombre_p'] ?? '')),
        ':apellidos' => trim(($data['primer_apellido_p'] ?? '') . ' ' . ($data['segundo_apellido_p'] ?? '')),
        ':email' => $data['email_p'] ?? null,
        ':telefono' => $data['telefono_p'] ?? null,
        ':direccion' => $data['direccion_p_prof'] ?? null,
        ':titulo_academico' => $data['titulo_academico_p'] ?? null,
        ':tipo_documento' => $data['tipo_documento_p'] ?? null,
        ':no_documento' => $data['no_documento_p'] ?? null,
        ':fecha_expedicion' => $data['fecha_expedicion_p'] ?? null,
        ':fecha_nacimiento' => $data['fecha_nacimiento_p'] ?? null,
        ':especialidad' => $data['especialidad_p'] ?? null,
        ':id_materia' => $data['id_materia_p'] ?? null,
        ':nacionalidad' => $data['nacionalidad_p'] ?? null,
        ':rh' => $data['rh_p'] ?? null,
        ':alergias' => $data['alergias_p'] ?? null,
        ':eps' => $data['eps_p'] ?? null,
        ':estado_civil' => $data['estado_civil_p'] ?? null
    ]);
}

function create_admin($pdo, $id_log, $data) {
    $sql = "INSERT INTO tab_administradores (
        id_log, nombres, apellidos, tipo_documento, no_documento, fecha_expedicion, fecha_nacimiento, email, telefono, direccion, cargo, eps, estado_civil
    ) VALUES (
        :id_log, :nombres, :apellidos, :tipo_documento, :no_documento, :fecha_expedicion, :fecha_nacimiento, :email, :telefono, :direccion, :cargo, :eps, :estado_civil
    )";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id_log' => $id_log,
        ':nombres' => trim(($data['primer_nombre_a'] ?? '') . ' ' . ($data['segundo_nombre_a'] ?? '')),
        ':apellidos' => trim(($data['primer_apellido_a'] ?? '') . ' ' . ($data['segundo_apellido_a'] ?? '')),
        ':tipo_documento' => $data['tipo_documento_a'] ?? null,
        ':no_documento' => $data['no_documento_a'] ?? null,
        ':fecha_expedicion' => $data['fecha_expedicion_a'] ?? null,
        ':fecha_nacimiento' => $data['fecha_nacimiento_a'] ?? null,
        ':email' => $data['email_a'] ?? null,
        ':telefono' => $data['telefono_a'] ?? null,
        ':direccion' => $data['direccion_a'] ?? null,
        ':cargo' => $data['cargo_a'] ?? null,
        ':eps' => $data['eps_a'] ?? null,
        ':estado_civil' => $data['estado_civil_a'] ?? null
    ]);
}

// =============================================================================
// --- Lógica Principal (Main Logic) ---
// =============================================================================

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data) {
        throw new Exception('Error: No se recibieron datos o el formato es incorrecto.');
    }

    $data = sanitize_input($data);
    $rol = $data['rol'] ?? '';

    if ($rol === 'acudiente') {
        $rol = 'padre';
    }

    if (empty($rol)) {
        throw new Exception('Error: El rol del usuario es obligatorio.');
    }

    $pdo->beginTransaction();

    // Reset the sequence for the login table to prevent duplicate key errors
    $pdo->exec("SELECT setval('login_id_log_seq', (SELECT COALESCE(MAX(id_log), 1) FROM login), true);");
    $pdo->exec("SELECT setval('tab_acudiente_id_acudiente_seq', (SELECT COALESCE(MAX(id_acudiente), 1) FROM tab_acudiente), true);");
    $pdo->exec("SELECT setval('tab_profesores_id_profesor_seq', (SELECT COALESCE(MAX(id_profesor), 1) FROM tab_profesores), true);");
    $pdo->exec("SELECT setval('tab_estudiante_id_ficha_seq', (SELECT COALESCE(MAX(id_ficha), 1) FROM tab_estudiante), true);");
    $pdo->exec("SELECT setval('tab_administradores_id_administrador_seq', (SELECT COALESCE(MAX(id_administrador), 1) FROM tab_administradores), true);");

    $errors = [];
    switch ($rol) {
        case 'padre':
            $errors = validate_padre($data);
            break;
        case 'profesor':
            // Add validation for professor if necessary
            break;
        case 'estudiante':
            $grado_numero = $data['id_grado_e'] ?? null;
            if (empty($grado_numero)) {
                throw new Exception("El grado del estudiante es obligatorio.");
            }

            $secciones = ['A', 'B', 'C'];
            $id_seccion_asignada = null;

            foreach ($secciones as $letra) {
                // Buscar el id_seccion para el grado y la letra de la sección
                $stmt_seccion = $pdo->prepare("SELECT id_seccion FROM tab_grados WHERE grado_numero = :grado_numero AND letra_seccion = :letra_seccion");
                $stmt_seccion->execute([':grado_numero' => $grado_numero, ':letra_seccion' => $letra]);
                $id_seccion = $stmt_seccion->fetchColumn();

                if ($id_seccion) {
                    // Contar estudiantes en esta sección
                    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM tab_estudiante WHERE id_seccion = :id_seccion");
                    $stmt_count->execute([':id_seccion' => $id_seccion]);
                    $current_students = $stmt_count->fetchColumn();

                    if ($current_students < 30) {
                        $id_seccion_asignada = $id_seccion;
                        break; // Sección encontrada, salir del bucle
                    }
                }
            }

            if (is_null($id_seccion_asignada)) {
                throw new Exception("Todas las secciones para el grado " . htmlspecialchars($grado_numero) . " están llenas (límite de 30 estudiantes por sección).");
            }

            // Sobrescribir id_grado_e con el id_seccion real para que create_estudiante funcione
            $data['id_grado_e'] = $id_seccion_asignada;
            break;
        case 'admin':
            if (empty($data['cargo_a'])) $errors[] = 'El cargo es requerido.';
            break;
        default:
            throw new Exception("Rol no reconocido para el registro.");
    }

    if (!empty($errors)) {
        throw new Exception(implode("\n", $errors));
    }

    $usuario_login = '';
    switch ($rol) {
        case 'padre':
            $usuario_login = $data['no_documento'];
            break;
        case 'profesor':
            $usuario_login = $data['no_documento_p'];
            break;
        case 'estudiante':
            $usuario_login = $data['no_documento_e'];
            break;
        case 'admin':
            $usuario_login = $data['no_documento_a'];
            break;
    }

    $stmtCheckUser = $pdo->prepare("SELECT COUNT(*) FROM login WHERE usuario = :usuario");
    $stmtCheckUser->bindParam(':usuario', $usuario_login, PDO::PARAM_STR);
    $stmtCheckUser->execute();
    if ($stmtCheckUser->fetchColumn() > 0) {
        throw new Exception("El usuario con el número de documento '" . htmlspecialchars($usuario_login) . "' ya existe.");
    }

    $nombres_to_pass = '';
    $apellidos_to_pass = '';

    switch ($rol) {
        case 'padre':
            $nombres_to_pass = $data['primer_nombre'];
            $apellidos_to_pass = $data['primer_apellido'];
            break;
        case 'profesor':
            $nombres_to_pass = $data['primer_nombre_p'];
            $apellidos_to_pass = $data['primer_apellido_p'];
            break;
        case 'estudiante':
            $nombres_to_pass = $data['primer_nombre_e'];
            $apellidos_to_pass = $data['primer_apellido_e'];
            break;
        case 'admin':
            $nombres_to_pass = $data['primer_nombre_a'];
            $apellidos_to_pass = $data['primer_apellido_a'];
            break;
    }

    $login_info = create_login($pdo, $usuario_login, $rol, $nombres_to_pass, $apellidos_to_pass);
    $id_log = $login_info['id_log'];

    switch ($rol) {
        case 'padre':
            create_padre($pdo, $id_log, $data);
            break;
        case 'profesor':
            create_profesor($pdo, $id_log, $data);
            break;
        case 'estudiante':
            create_estudiante($pdo, $id_log, $data);
            break;
        case 'admin':
            create_admin($pdo, $id_log, $data);
            break;
    }

    $pdo->commit();
    $response_data = [
        'success' => true, 
        'message' => 'Usuario registrado exitosamente.',
        'usuario' => $usuario_login,
        'raw_password' => $login_info['raw_password']
    ];

    if ($rol === 'estudiante') {
        $stmt_grado = $pdo->prepare("SELECT grado_numero, letra_seccion FROM tab_grados WHERE id_seccion = :id_seccion");
        $stmt_grado->execute([':id_seccion' => $data['id_grado_e']]); // id_grado_e was overridden with the assigned section id
        $grado_info = $stmt_grado->fetch(PDO::FETCH_ASSOC);
        if ($grado_info) {
            $response_data['grado_asignado'] = "Grado {$grado_info['grado_numero']} - Sección {$grado_info['letra_seccion']}";
        }
    }

    echo json_encode($response_data);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log($e->getMessage(), 3, 'debug.log');
    header('Content-Type: application/json', true, 400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>