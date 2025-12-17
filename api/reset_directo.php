<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');

require_once '../php/conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['action']) || !isset($data['email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Acción o correo no especificados.']);
    exit;
}

$action = $data['action'];
$email = $data['email'];

// --- Acción 1: Obtener la pregunta de seguridad ---
if ($action === 'get_question') {
    try {
        $sql = "SELECT sr.pregunta FROM login l JOIN tab_seguridad_respuestas sr ON l.id_log = sr.id_log WHERE l.usuario = :email LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && !empty($result['pregunta'])) {
            echo json_encode(['success' => true, 'pregunta' => $result['pregunta']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Este usuario no existe o no tiene una pregunta de seguridad configurada.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error del servidor.']);
    }
    exit;
}

// --- Acción 2: Restablecer la contraseña ---
if ($action === 'reset_password') {
    if (!isset($data['respuesta']) || !isset($data['nueva_contrasena'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Faltan la respuesta o la nueva contraseña.']);
        exit;
    }

    $respuesta_usuario = $data['respuesta'];
    $nueva_contrasena = $data['nueva_contrasena'];
    $recaptcha_response = $data['g-recaptcha-response'];

    // Clave secreta de reCAPTCHA (¡NO LA COMPARTAS!)
    $recaptcha_secret = '6LdJKJUrAAAAACEbwQul22xY47yvfHbnrXmIkMZx';

    // Verificar reCAPTCHA
    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    $verify_data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($verify_data)
        ]
    ];
    $context  = stream_context_create($options);
    $verify_result = file_get_contents($verify_url, false, $context);
    $captcha_success = json_decode($verify_result, true);

    if (!$captcha_success['success']) {
        echo json_encode(['success' => false, 'message' => 'Verificación reCAPTCHA fallida. Por favor, inténtalo de nuevo.']);
        exit;
    }

    if (strlen($nueva_contrasena) < 8) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres.']);
        exit;
    }

    try {
        $sql = "SELECT l.id_log, sr.respuesta_hash FROM login l JOIN tab_seguridad_respuestas sr ON l.id_log = sr.id_log WHERE l.usuario = :email LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user_data || !password_verify($respuesta_usuario, $user_data['respuesta_hash'])) {
            echo json_encode(['success' => false, 'message' => 'La respuesta de seguridad es incorrecta.']);
            exit;
        }

        // Si la respuesta es correcta, actualizamos la contraseña
        $id_log = $user_data['id_log'];
        $nueva_contrasena_hash = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

        $update_sql = "UPDATE login SET contrasena = :contrasena WHERE id_log = :id_log";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bindParam(':contrasena', $nueva_contrasena_hash, PDO::PARAM_STR);
        $update_stmt->bindParam(':id_log', $id_log, PDO::PARAM_INT);
        $update_stmt->execute();

        echo json_encode(['success' => true, 'message' => '¡Contraseña actualizada con éxito!']);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error del servidor al actualizar.']);
    }
    exit;
}

// Si la acción no es válida
echo json_encode(['success' => false, 'message' => 'Acción no reconocida.']);
?>