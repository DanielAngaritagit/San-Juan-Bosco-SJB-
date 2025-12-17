<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
session_start();
header('Content-Type: application/json');

require_once '../php/conexion.php'; // Brings in $pdo

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

if (!isset($_SESSION['id_log'])) {
    echo json_encode(['success' => false, 'message' => 'No has iniciado sesión.']);
    exit;
}

$id_log = $_SESSION['id_log'];

if (isset($_FILES['profile_pic'])) {
    $file = $_FILES['profile_pic'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Error al subir el archivo. Código: ' . $file['error']]);
        exit;
    }

    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExt, $allowed)) {
        if ($file['size'] < 5000000) { // 5MB limit
            $fileNameNew = "profile_" . $id_log . "." . $fileExt;
            $uploadPath = '../uploads/';
            $fileDestination = $uploadPath . $fileNameNew;

            // Ensure the uploads directory exists and is writable
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            if (!is_writable($uploadPath)) {
                echo json_encode(['success' => false, 'message' => 'Error: El directorio de subida no tiene permisos de escritura.']);
                exit;
            }

            // Create a temporary path for the uploaded file
            $tempFilePath = $file['tmp_name'];

            // Define the final destination path
            $fileDestination = $uploadPath . $fileNameNew;

            // Compress and save the image
            if (compressImage($tempFilePath, $fileDestination, 80)) { // 80% quality for JPEG/PNG
                $filePathInDb = 'uploads/' . $fileNameNew;

                // --- DATABASE UPDATE LOGIC ---
                try {
                    $query = "UPDATE login SET foto_url = :foto_url WHERE id_log = :id_log";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':foto_url', $filePathInDb);
                    $stmt->bindParam(':id_log', $id_log, PDO::PARAM_INT);
                    
                    if ($stmt->execute()) {
                        echo json_encode(['success' => true, 'new_pic_url' => $filePathInDb]);
                    } else {
                        // If DB update fails, try to delete the uploaded file
                        unlink($fileDestination);
                        echo json_encode(['success' => false, 'message' => 'El archivo se subió pero no se pudo actualizar la base de datos.']);
                    }
                } catch (PDOException $e) {
                    // Also try to delete the file on DB error
                    unlink($fileDestination);
                    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
                }
                // --- END OF DB UPDATE ---

            } else {
                echo json_encode(['success' => false, 'message' => 'Error al procesar y comprimir la imagen.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande (límite 5MB).']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No se seleccionó ningún archivo.']);
}
?>