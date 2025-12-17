<?php
require_once 'conexion.php';

try {
    $pdo->beginTransaction();

    // 1. Create the ENUM type
    $pdo->exec("CREATE TYPE tipo_evaluacion_enum AS ENUM (
        'Evaluacion Escrita',
        'Evaluacion Oral',
        'Evaluacion Practica',
        'Proyecto',
        'Participacion en Clase',
        'Trabajo en Clase',
        'Tarea o trabajo en casa',
        'Evaluacion Cognitiva'
    );");
    echo "<p>ENUM 'tipo_evaluacion_enum' creado exitosamente.</p>";

    // 2. Alter the table to use the new ENUM type
    $pdo->exec("ALTER TABLE tab_calificaciones
        ALTER COLUMN tipo_evaluacion TYPE tipo_evaluacion_enum
        USING tipo_evaluacion::tipo_evaluacion_enum;");
    echo "<p>Columna 'tipo_evaluacion' en 'tab_calificaciones' modificada exitosamente.</p>";

    $pdo->commit();
    echo "<p>Todos los cambios aplicados exitosamente.</p>";

} catch (PDOException $e) {
    $pdo->rollBack();
    echo "<p>Error al aplicar los cambios: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<p>Error inesperado: " . $e->getMessage() . "</p>";
}
?>