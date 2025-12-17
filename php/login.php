<?php
/**
 * Script de autenticación de usuarios.
 * Maneja el inicio de sesión para todos los roles, verifica credenciales,
 * crea una sesión y registra el acceso.
 */

// --- Configuración de Errores y Headers ---
ini_set('display_errors', 0); // Desactivado en producción por seguridad.
ini_set('display_startup_errors', 0); // Desactivado en producción por seguridad.
error_reporting(E_ALL);

header('Content-Type: application/json'); // Establece que la respuesta será en formato JSON.

// --- Inclusión de Dependencias ---
require_once 'conexion.php'; // Carga la configuración de la conexión a la base de datos.

// --- Lógica Principal del Script ---
global $pdo;
$conn = $pdo; // Asigna la conexión PDO a una variable local.

try {
    // 1. Lectura y Validación de la Entrada (Input)
    $input = json_decode(file_get_contents('php://input'), true); // Lee el cuerpo de la solicitud POST en formato JSON.
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Formato JSON inválido'); // Error si el JSON no es válido.
    }

    // Verifica que los campos requeridos no estén vacíos.
    $required = ['usuario', 'contrasena', 'rol'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("El campo $field es requerido");
        }
    }

    // 2. Consulta a la Base de Datos
    // Prepara una consulta para buscar al usuario por su nombre de usuario y que esté activo.
    $stmt = $conn->prepare("SELECT id_log, usuario, contrasena, rol FROM login WHERE usuario = :usuario AND activo = TRUE LIMIT 1");
    $stmt->bindParam(':usuario', $input['usuario'], PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Obtiene el registro del usuario.

    // Si no se encuentra un usuario, lanza una excepción.
    if (!$user) {
        throw new Exception('Usuario o contraseña incorrectos');
    }

    // 3. Verificación de Contraseña y Rol
    // Verifica que la contraseña proporcionada coincida con el hash almacenado en la base de datos.
    // password_verify es seguro y compatible con hashes de password_hash() y crypt() con Blowfish.
    if (!password_verify($input['contrasena'], $user['contrasena'])) {
        throw new Exception('Usuario o contraseña incorrectos');
    }

    // Compara el rol del usuario en la BD con el rol seleccionado en el formulario de login.
    if (strtolower($user['rol']) !== strtolower($input['rol'])) {
        // Allow administrativo users to log in via the admin role form
        if (!(strtolower($user['rol']) === 'administrativo' && strtolower($input['rol']) === 'admin')) {
            throw new Exception('No tiene permisos para este rol');
        }
    }

    // Consolidate 'administrativo' role into 'admin' for session
    $session_rol = strtolower($user['rol']);
    if ($session_rol === 'administrativo') {
        $session_rol = 'admin';
    }

    // 4. Creación y Configuración de la Sesión
    session_start(); // Inicia la sesión.
    error_log("Login - session_id() after session_start(): " . session_id());
    // Almacena los datos más importantes del usuario en variables de sesión.
    $_SESSION['id_log'] = $user['id_log'];
    $_SESSION['usuario'] = $user['usuario'];
    $_SESSION['rol'] = $session_rol;
    $_SESSION['last_activity'] = time(); // Guarda la hora de la última actividad para control de inactividad.

    	$_SESSION['email'] = $user['email'];

    	// --- Lógica para verificar si un Admin/Profesor también es Acudiente ---
    	if ($user['rol'] === 'admin' || $user['rol'] === 'profesor') {
    		try {
    			$stmt_acudiente = $pdo->prepare("SELECT id_acudiente FROM tab_acudiente WHERE email = :email LIMIT 1");
    			$stmt_acudiente->bindParam(':email', $_SESSION['email']);
    			$stmt_acudiente->execute();
    			$acudiente_data = $stmt_acudiente->fetch(PDO::FETCH_ASSOC);

    			if ($acudiente_data) {
    				$_SESSION['is_acudiente'] = true;
    				$_SESSION['id_acudiente'] = $acudiente_data['id_acudiente'];
    			} else {
    				$_SESSION['is_acudiente'] = false;
    				$_SESSION['id_acudiente'] = null;
    			}
    		} catch (PDOException $e) {
    			error_log("Error al verificar rol de acudiente para usuario " . $_SESSION['usuario'] . ": " . $e->getMessage());
    			$_SESSION['is_acudiente'] = false; // En caso de error, no conceder acceso de acudiente
    			$_SESSION['id_acudiente'] = null;
    		}
    	} else {
    		$_SESSION['is_acudiente'] = false; // Otros roles no tienen acceso secundario de acudiente por defecto
    		$_SESSION['id_acudiente'] = null;
    	}
    	// --- Fin de la lógica de verificación de Acudiente ---

    	// Redireccionar según el rol

    // Lógica específica para el rol de profesor: obtener y guardar su ID de la tabla de profesores.
    if (strtolower($user['rol']) === 'profesor') {
        $stmtProfesor = $conn->prepare("SELECT id_profesor FROM tab_profesores WHERE id_log = :id_log");
        $stmtProfesor->bindParam(':id_log', $user['id_log'], PDO::PARAM_INT);
        $stmtProfesor->execute();
        $profesor = $stmtProfesor->fetch(PDO::FETCH_ASSOC);
        if ($profesor) {
            $_SESSION['id_profesor'] = $profesor['id_profesor'];
        }
    }

    // Lógica específica para el rol de estudiante: obtener y guardar su ID de la tabla de estudiantes.
    if (strtolower($user['rol']) === 'estudiante') {
        $stmtEstudiante = $conn->prepare("SELECT id_ficha FROM tab_estudiante WHERE no_documento = :no_documento");
        $stmtEstudiante->bindParam(':no_documento', $user['usuario'], PDO::PARAM_STR);
        $stmtEstudiante->execute();
        $estudiante = $stmtEstudiante->fetch(PDO::FETCH_ASSOC);
        if ($estudiante) {
            $_SESSION['id_estud'] = $estudiante['id_ficha'];
        }
    }

    // 5. Registro de Sesión y Acceso
    $session_id = session_id();
    $id_log = $user['id_log'];
    error_log("Login - id_log: " . $id_log . ", session_id to be inserted: " . $session_id);

    // Eliminar cualquier sesión activa anterior para este id_log
    $stmtDeleteActiveSession = $conn->prepare("DELETE FROM active_sessions WHERE id_log = :id_log");
    $stmtDeleteActiveSession->bindParam(':id_log', $id_log, PDO::PARAM_INT);
    $stmtDeleteActiveSession->execute();

    // Insertar la nueva sesión activa
    $stmtInsertActiveSession = $conn->prepare("INSERT INTO active_sessions (id_log, session_id, last_activity) VALUES (:id_log, :session_id, NOW())");
    $stmtInsertActiveSession->bindParam(':id_log', $id_log, PDO::PARAM_INT);
    $stmtInsertActiveSession->bindParam(':session_id', $session_id, PDO::PARAM_STR);
    $stmtInsertActiveSession->execute();

    // Inserta un registro en la tabla 'accesos' para auditoría.
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $agente = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $stmtAcceso = $conn->prepare("INSERT INTO accesos (usuario_id, direccion_ip, agente_usuario, tipo_acceso) VALUES (?, ?, ?, 'login')");
    $stmtAcceso->execute([$user['id_log'], $ip, $agente]);

    // 6. Respuesta Exitosa
    // Envía una respuesta JSON indicando éxito y la URL a la que debe redirigir el frontend.
    echo json_encode([
        'success' => true,
        'message' => 'Inicio de sesión exitoso',
        'redirect' => determinarRedireccion($user['rol'])
    ]);

} catch (Exception $e) {
    // --- Manejo de Errores ---
    error_log("Error en login: " . $e->getMessage()); // Registra el error en el log del servidor.
    // Envía una respuesta JSON indicando el fallo y el mensaje de error.
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Determina la URL de redirección basada en el rol del usuario.
 * @param string $rol El rol del usuario (ej. 'admin', 'profesor').
 * @return string La ruta relativa a la página del dashboard correspondiente.
 */
function determinarRedireccion($rol) {
    $rol = strtolower($rol);
    $redirecciones = [
        'admin' => 'admin/admin.php',
        'profesor' => 'profesor/profesor.php',
        'estudiante' => 'estudiante/estudiante.php',
        'padre' => 'padre/padre.php',
        'administrativo' => 'admin/admin.php'
    ];
    return $redirecciones[$rol] ?? 'inicia.html'; // Si el rol no se encuentra, redirige a la página de login.
}
?>