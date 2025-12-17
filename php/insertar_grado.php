<?php
require_once 'conexion.php';

header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_grado = $_POST['nombre_grado'] ?? null;
    $id_usuario_docente = $_POST['id_usuario_docente'] ?? null; // This will be 301

    if ($nombre_grado && $id_usuario_docente) {
        try {
            $pdo = conectarBD();

            // 1. Obtener id_profesor a partir de id_log
            $stmt_profesor = $pdo->prepare("SELECT id_profesor FROM tab_profesores WHERE id_log = :id_log");
            $stmt_profesor->bindParam(':id_log', $id_usuario_docente, PDO::PARAM_INT);
            $stmt_profesor->execute();
            $profesor = $stmt_profesor->fetch(PDO::FETCH_ASSOC);

            if ($profesor) {
                $id_profesor_director = $profesor['id_profesor'];
                // Log for debugging
                error_log("DEBUG: Found id_profesor: " . $id_profesor_director . " for id_log: " . $id_usuario_docente);

                // Parse nombre_grado to get grado_numero and letra_seccion
                // Assuming nombre_grado is like "Grado 10" or "Grado 5A"
                preg_match('/(\d+)([A-Za-z])?/', $nombre_grado, $matches);
                $grado_numero = isset($matches[1]) ? (int)$matches[1] : null;
                $letra_seccion = isset($matches[2]) ? strtoupper($matches[2]) : 'A'; // Default to 'A' if no letter

                if ($grado_numero === null) {
                    $response['success'] = false;
                    $response['message'] = "Formato de 'nombre_grado' inválido. Se esperaba 'Grado X' o 'Grado XA'.";
                    echo json_encode($response);
                    exit;
                }

                // 2. Insertar el nuevo grado
                $stmt_grado = $pdo->prepare("INSERT INTO tab_grados (grado_numero, letra_seccion, profesor_lider_id) VALUES (:grado_numero, :letra_seccion, :id_profesor_director)");
                $stmt_grado->bindParam(':grado_numero', $grado_numero, PDO::PARAM_INT);
                $stmt_grado->bindParam(':letra_seccion', $letra_seccion, PDO::PARAM_STR);
                $stmt_grado->bindParam(':id_profesor_director', $id_profesor_director, PDO::PARAM_INT);

                if ($stmt_grado->execute()) {
                    $response['success'] = true;
                    $response['message'] = "Grado '$nombre_grado' insertado exitosamente con el docente director (id_usuario: $id_usuario_docente).";
                } else {
                    $response['success'] = false;
                    $response['message'] = "Error al insertar el grado.";
                    $response['error_info'] = $stmt_grado->errorInfo();
                }
            } else {
                $response['success'] = false;
                $response['message'] = "Error: No se encontró un profesor válido en tab_profesores para el id_log proporcionado ($id_usuario_docente). Asegúrese de que el profesor exista y esté correctamente registrado.";
                error_log("ERROR: No professor found in tab_profesores for id_log: " . $id_usuario_docente);
            }
        } catch (PDOException $e) {
            $response['success'] = false;
            $response['message'] = "Error de conexión o base de datos: " . $e->getMessage();
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Faltan parámetros. Se requieren 'nombre_grado' y 'id_usuario_docente'.";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Método de solicitud no permitido. Use POST.";
}

echo json_encode($response);
?>