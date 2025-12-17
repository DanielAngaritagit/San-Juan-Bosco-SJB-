<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../lib/PHPMailer/src/Exception.php';
require '../lib/PHPMailer/src/PHPMailer.php';
require '../lib/PHPMailer/src/SMTP.php';
require_once '../php/conexion.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Método de solicitud no permitido.';
    echo json_encode($response);
    exit;
}


$input = json_decode(file_get_contents('php://input'), true);
$documento = $input['documento'] ?? '';
$correo = $input['correo'] ?? '';

if (empty($documento) || empty($correo)) {
    $response['message'] = 'El número de documento y el correo son requeridos.';
    echo json_encode($response);
    exit;
}

try {
    $pdo->beginTransaction();

    // Buscar el usuario por número de documento en la tabla login

    $stmt_login = $pdo->prepare("SELECT id_log, rol, email FROM login WHERE usuario = :documento");
    $stmt_login->bindParam(':documento', $documento, PDO::PARAM_STR);
    $stmt_login->execute();
    $user_login = $stmt_login->fetch(PDO::FETCH_ASSOC);

    if (!$user_login) {
        $response['message'] = 'El número de documento no se encuentra registrado.';
        $pdo->rollBack();
        echo json_encode($response);
        exit;
    }

    $id_log = $user_login['id_log'];
    $rol = $user_login['rol'];
    $user_email = $user_login['email'];
    $user_nombres = '';
    $user_apellidos = '';

    // Validar que el correo ingresado coincida con el registrado
    if (strtolower(trim($user_email)) !== strtolower(trim($correo))) {
        $response['message'] = 'El correo no coincide con el registrado para este documento.';
        $pdo->rollBack();
        echo json_encode($response);
        exit;
    }

    // Obtener nombres y apellidos de la tabla de rol específica
    switch ($rol) {
        case 'admin':
            $stmt_profile = $pdo->prepare("SELECT nombres, apellidos FROM tab_administradores WHERE id_log = :id_log");
            break;
        case 'profesor':
            $stmt_profile = $pdo->prepare("SELECT nombres, apellidos FROM tab_profesores WHERE id_log = :id_log");
            break;
        case 'estudiante':
            $stmt_profile = $pdo->prepare("SELECT nombres, apellido1 AS apellidos FROM tab_estudiante WHERE id_ficha = :id_log"); // Assuming id_log is id_ficha for students
            break;
        case 'padre':
            $stmt_profile = $pdo->prepare("SELECT nombres, apellidos FROM tab_acudiente WHERE id_log = :id_log");
            break;
        default:
            $response['message'] = 'Rol de usuario no soportado para recuperación de contraseña.';
            $pdo->rollBack();
            echo json_encode($response);
            exit;
    }

    if (isset($stmt_profile)) {
        $stmt_profile->bindParam(':id_log', $id_log, PDO::PARAM_INT);
        $stmt_profile->execute();
        $user_profile = $stmt_profile->fetch(PDO::FETCH_ASSOC);

        if ($user_profile) {
            $user_nombres = $user_profile['nombres'];
            $user_apellidos = $user_profile['apellidos'];
        }
    }

    if (empty($user_email)) {
        $response['message'] = 'Este usuario no tiene una dirección de correo electrónico registrada para la recuperación.';
        $pdo->rollBack();
        echo json_encode($response);
        exit;
    }

    // Generar un token seguro
    $token = bin2hex(random_bytes(32));
    $fecha_expiracion = new DateTime('+1 hour');
    $fecha_expiracion_str = $fecha_expiracion->format('Y-m-d H:i:s');

    // Guardar el token en la base de datos
    $stmt_token = $pdo->prepare("INSERT INTO tab_password_reset (id_log, token, fecha_expiracion) VALUES (?, ?, ?)");
    $stmt_token->execute([$id_log, $token, $fecha_expiracion_str]);

    // Enviar el correo electrónico con PHPMailer
    $mail = new PHPMailer(true);

    // Configuración del servidor (ajustar según sea necesario)
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Gmail
    $mail->SMTPAuth = true;
    $mail->Username = 'soportesjb2024@gmail.com'; // Tu correo de Gmail
    $mail->Password = 'mvpr engr rorb jmfl'; // Tu contraseña de aplicación de Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Remitente y destinatario
    $mail->setFrom('soportesjb2024@gmail.com', 'Soporte Colegio San Juan Bosco');
    $mail->addAddress($user_email, $user_nombres . ' ' . $user_apellidos);

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Recuperacion de Contrasena - Colegio San Juan Bosco';
    $reset_link = "http://localhost/SJB/restablecer.html?token=$token";
    $mail->Body    = "Hola " . $user_nombres . ",<br><br>Has solicitado restablecer tu contraseña. Por favor, haz clic en el siguiente enlace para continuar:<br><br><a href=\"" . $reset_link . "\">Restablecer Contraseña</a><br><br>Si no solicitaste esto, puedes ignorar este correo.<br><br>Gracias,<br>Equipo de Soporte del Colegio San Juan Bosco";
    $mail->AltBody = "Hola " . $user_nombres . ",\n\nHas solicitado restablecer tu contraseña. Por favor, copia y pega el siguiente enlace en tu navegador para continuar:\n\n$reset_link\n\nSi no solicitaste esto, puedes ignorar este correo.\n\nGracias,\nEquipo de Soporte del Colegio San Juan Bosco";

    $mail->send();

    $pdo->commit();
    $response['success'] = true;
    $response['message'] = 'Si el documento está registrado, se han enviado las instrucciones de recuperación a tu correo.';

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $response['message'] = 'Error al procesar la solicitud: ' . $e->getMessage();
    error_log('PHPMailer Error: ' . $e->getMessage());
}

echo json_encode($response);
?>