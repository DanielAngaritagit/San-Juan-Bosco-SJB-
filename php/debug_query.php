<?php
header('Content-Type: application/json');

require_once 'conexion.php';

if (isset($_POST['sql'])) {
    $sql = $_POST['sql'];

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $result]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error executing query: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No SQL query provided.']);
}
?>