<?php
header('Content-Type: text/html; charset=UTF-8');
ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once 'conexion.php';

echo "<!DOCTYPE html><html lang=\"es\"><head><meta charset=\"UTF-8\"><title>Fix Estudiante Sequence</title></head><body>";

echo "<h1>Herramienta para Corregir Secuencia de Estudiante</h1>";
echo "<p style=\"color: red; font-weight: bold;\">ADVERTENCIA: Esta es una herramienta de depuración. Elimínela inmediatamente después de usarla.</p>";

try {
    // Iniciar una transacción para asegurar la atomicidad
    $pdo->beginTransaction();

    // Obtener el máximo id_estud actual
    $stmt_max_id = $pdo->query("SELECT MAX(id_estud) FROM tab_ficha_datos_estudiante");
    $max_id = $stmt_max_id->fetchColumn();

    // Calcular el nuevo valor para la secuencia
    $new_sequence_value = ($max_id !== null) ? $max_id + 1 : 1;

    // Establecer el valor de la secuencia
    $stmt_setval = $pdo->prepare("SELECT setval('tab_ficha_datos_estudiante_id_estud_seq', :new_val, false)");
    $stmt_setval->bindParam(':new_val', $new_sequence_value, PDO::PARAM_INT);
    $stmt_setval->execute();

    $pdo->commit();

    echo "<p style=\"color: green;\">Secuencia 'tab_ficha_datos_estudiante_id_estud_seq' actualizada exitosamente a: <strong>" . $new_sequence_value . "</strong></p>";
    echo "<p>Ahora, por favor, intente insertar el estudiante de nuevo.</p>";

} catch (PDOException $e) {
    $pdo->rollBack();
    echo "<p style=\"color: red;\">Error de base de datos al corregir la secuencia: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<p style=\"color: red;\">Error inesperado: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>
