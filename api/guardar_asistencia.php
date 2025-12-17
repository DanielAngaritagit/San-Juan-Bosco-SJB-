<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
// api/guardar_asistencia.php

header('Content-Type: application/json');
require_once '../php/verificar_sesion.php';
require_once '../php/conexion.php';

function compressImage($sourcePath, $destinationPath, $quality = 75) {
    $info = getimagesize($sourcePath);
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $image = imagecreatefrompng($sourcePath);
            // Preserve transparency for PNG
            imagealphablending($image, false);
            imagesavealpha($image, true);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($sourcePath);
            break;
        default:
            return false; // Unsupported image type
    }

    if (!$image) {
        return false; // Failed to create image resource
    }

    // Save the compressed image
    if ($mime == 'image/jpeg') {
        imagejpeg($image, $destinationPath, $quality);
    } elseif ($mime == 'image/png') {
        // PNG quality is 0-9, where 0 is no compression, 9 is max compression.
        // Convert 0-100 quality to 0-9 for PNG.
        $pngQuality = floor(($quality / 100) * 9);
        imagepng($image, $destinationPath, $pngQuality);
    } else { // GIF
        imagegif($image, $destinationPath); // GIF has no quality parameter
    }

    imagedestroy($image); // Free up memory
    return true;
}

if ($_SESSION['rol'] !== 'profesor') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit();
}

$response = ['success' => false, 'message' => 'Error desconocido.'];
$attendance_data_json = $_POST['attendance_data'] ?? '';
$attendance_records = json_decode($attendance_data_json, true);

if (empty($attendance_records) || !is_array($attendance_records)) {
    http_response_code(400);
    $response['message'] = 'No se recibieron datos de asistencia válidos.';
    echo json_encode($response);
    exit();
}

$excusa_medica_url = NULL; // Initialize to NULL

// Handle file upload if present
if (isset($_FILES['excusa_medica']) && $_FILES['excusa_medica']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['excusa_medica'];
    $uploadDir = '../uploads/excusas_medicas/';
    
    // Ensure directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf']; // Allow PDF for medical excuses
    
    if (in_array($fileExt, $allowedExtensions)) {
        $fileNameNew = uniqid('excusa_', true) . '.' . $fileExt;
        $fileDestination = $uploadDir . $fileNameNew;

        // If it's an image, compress it. Otherwise, just move it.
        if (in_array($fileExt, ['jpg', 'jpeg', 'png'])) {
            if (compressImage($file['tmp_name'], $fileDestination, 80)) {
                $excusa_medica_url = 'uploads/excusas_medicas/' . $fileNameNew;
            } else {
                $response['message'] = 'Error al comprimir la imagen de la excusa médica.';
                echo json_encode($response);
                exit();
            }
        } else { // For PDF
            if (move_uploaded_file($file['tmp_name'], $fileDestination)) {
                $excusa_medica_url = 'uploads/excusas_medicas/' . $fileNameNew;
            } else {
                $response['message'] = 'Error al mover el archivo PDF de la excusa médica.';
                echo json_encode($response);
                exit();
            }
        }
    } else {
        $response['message'] = 'Tipo de archivo de excusa médica no permitido.';
        echo json_encode($response);
        exit();
    }
}

try {
    $id_log = $_SESSION['id_log'];
    
    // Obtener el id_profesor a partir del id_log
    $stmt_profesor = $pdo->prepare("SELECT id_profesor FROM tab_profesores WHERE id_log = :id_log");
    $stmt_profesor->bindParam(':id_log', $id_log, PDO::PARAM_INT);
    $stmt_profesor->execute();
    $id_profesor = $stmt_profesor->fetchColumn();

    if (!$id_profesor) {
        throw new Exception("No se pudo encontrar el perfil del profesor.");
    }

    $pdo->beginTransaction();

    // Prepare the SQL statement to include excusa_medica_url
    $sql = "INSERT INTO tab_asistencia (id_estud, id_profesor, estado, fecha_hora, excusa_medica_url) VALUES (:id_estud, :id_profesor, :estado, NOW(), :excusa_medica_url)";
    $stmt = $pdo->prepare($sql);

    foreach ($attendance_records as $asistencia) {
        if (!isset($asistencia['id_estud']) || !isset($asistencia['estado'])) {
            throw new Exception("Datos de asistencia incompletos.");
        }
        
        // If the attendance status is 'justificado', associate the uploaded URL
        $current_excusa_url = ($asistencia['estado'] === 'justificado') ? $excusa_medica_url : NULL;

        $stmt->execute([
            ':id_estud' => $asistencia['id_estud'],
            ':id_profesor' => $id_profesor,
            ':estado' => $asistencia['estado'],
            ':excusa_medica_url' => $current_excusa_url // Use the URL here
        ]);
    }

    $pdo->commit();
    
    $response['success'] = true;
    $response['message'] = 'Asistencia guardada correctamente.';

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    $response['message'] = 'Error al guardar la asistencia: ' . $e->getMessage();
}

echo json_encode($response);
?>