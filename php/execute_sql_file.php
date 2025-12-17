<?php
header('Content-Type: application/json');

require_once 'conexion.php';

if (isset($_POST['file'])) {
    $file_path = __DIR__ . '/../' . $_POST['file'];

    if (file_exists($file_path)) {
        $sql = file_get_contents($file_path);
        try {
            $pdo->exec($sql);
            echo json_encode(['success' => true, 'message' => 'SQL script executed successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error executing SQL script: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'SQL script file not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No SQL script file provided.']);
}
?>