<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'conexion.php';

$id_profesor_a_borrar = 17;
$id_log_a_borrar = 71;

echo "<h1>Eliminando profesor conflictivo...</h1>";

try {
    $pdo->beginTransaction();

    // Borrar de la tabla de profesores
    $stmt_prof = $pdo->prepare("DELETE FROM tab_profesores WHERE id_profesor = ?");
    $stmt_prof->execute([$id_profesor_a_borrar]);
    $count_prof = $stmt_prof->rowCount();

    // Borrar de la tabla de login
    $stmt_log = $pdo->prepare("DELETE FROM login WHERE id_log = ?");
    $stmt_log->execute([$id_log_a_borrar]);
    $count_log = $stmt_log->rowCount();

    $pdo->commit();

    if ($count_prof > 0) {
        echo "<p style='color: green;'>Profesor con ID " . $id_profesor_a_borrar . " eliminado exitosamente.</p>";
    } else {
        echo "<p style='color: orange;'>Advertencia: No se encontró un profesor con ID " . $id_profesor_a_borrar . " para eliminar (quizás ya fue borrado).</p>";
    }

    if ($count_log > 0) {
        echo "<p style='color: green;'>Login asociado con ID " . $id_log_a_borrar . " eliminado exitosamente.</p>";
    } else {
        echo "<p style='color: orange;'>Advertencia: No se encontró un login con ID " . $id_log_a_borrar . " para eliminar.</p>";
    }

    echo "<h2>Paso completado.</h2>";
    echo "<p>Ahora, por favor, intenta ejecutar el script de actualización de materias de nuevo.</p>";
    echo '<a href="actualizar_materias.php">Ejecutar actualización de materias</a>';

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo '<h2 style="color: red;">Error durante la eliminación:</h2>';
    echo '<p>' . $e->getMessage() . '</p>';
}
?>
