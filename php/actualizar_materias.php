<?php
// --- ACTUALIZADOR DE MATERIAS ---
// Este script borra las materias existentes y las reemplaza con el nuevo plan de estudios.
// Visita esta página en tu navegador para ejecutar la actualización.

header('Content-Type: text/html; charset=utf-8');
require_once 'conexion.php';

$nuevas_materias = [
    ['CN-BIO', 'Biología', 'Ciencias Naturales y Educación Ambiental: Biología.'],
    ['CN-FIS', 'Física', 'Ciencias Naturales y Educación Ambiental: Física.'],
    ['CN-QUI', 'Química', 'Ciencias Naturales y Educación Ambiental: Química.'],
    ['CS-HIS', 'Historia', 'Ciencias Sociales: Historia.'],
    ['CS-GEO', 'Geografía', 'Ciencias Sociales: Geografía.'],
    ['CS-CON', 'Constitución Política', 'Ciencias Sociales: Constitución Política.'],
    ['CS-DEM', 'Democracia', 'Ciencias Sociales: Democracia.'],
    ['EA-ART', 'Artes Plásticas', 'Educación Artística: Artes plásticas.'],
    ['EA-MUS', 'Música', 'Educación Artística: Música.'],
    ['EA-DAN', 'Danzas', 'Educación Artística: Danzas.'],
    ['ET-VAL', 'Educación Ética y en Valores Humanos', 'Educación Ética y en Valores Humanos.'],
    ['EF-DEP', 'Educación Física, Recreación y Deportes', 'Educación Física, Recreación y Deportes.'],
    ['ER-REL', 'Educación Religiosa', 'Educación Religiosa.'],
    ['HU-LCA', 'Lengua Castellana', 'Humanidades: Lengua castellana.'],
    ['HU-IEX', 'Idiomas Extranjeros', 'Humanidades: Idiomas extranjeros.'],
    ['MA-ARI', 'Aritmética', 'Matemáticas: Aritmética.'],
    ['MA-ALG', 'Álgebra', 'Matemáticas: Álgebra.'],
    ['MA-GEO', 'Geometría', 'Matemáticas: Geometría.'],
    ['MA-TRI', 'Trigonometría', 'Matemáticas: Trigonometría.'],
    ['MA-CAL', 'Cálculo', 'Matemáticas: Cálculo.'],
    ['TE-INF', 'Tecnología e Informática', 'Tecnología e Informática.'],
    ['CP-PAZ', 'Cátedra de la Paz', 'Cátedra de la Paz.']
];

try {
    $pdo->beginTransaction();

    echo "<h1>Actualizando Plan de Estudios...</h1>";

    // 1. Desvincular profesores de sus materias actuales
    $stmt_unlink = $pdo->exec("UPDATE tab_profesores SET id_materia = NULL");
    echo "<p>Profesores desvinculados de materias antiguas: " . $stmt_unlink . " filas afectadas.</p>";

    // 2. Borrar materias antiguas
    $stmt_delete = $pdo->exec("DELETE FROM tab_materias");
    echo "<p>Materias antiguas eliminadas: " . $stmt_delete . " filas afectadas.</p>";
    
    // 3. Reiniciar la secuencia del ID para que comience en 1
    $pdo->exec("ALTER SEQUENCE tab_materias_id_materia_seq RESTART WITH 1");
    echo "<p>Secuencia de ID reiniciada.</p>";


    // 4. Insertar nuevas materias
    $sql_insert = "INSERT INTO tab_materias (codigo, nombre, descripcion, fecha_creacion) VALUES (?, ?, ?, ?)";
    $stmt_insert = $pdo->prepare($sql_insert);

    $fecha_creacion = date('Y-m-d');
    $conteo_inserciones = 0;

    foreach ($nuevas_materias as $materia) {
        $stmt_insert->execute([$materia[0], $materia[1], $materia[2], $fecha_creacion]);
        $conteo_inserciones++;
    }
    echo "<p>Nuevas materias insertadas: " . $conteo_inserciones . ".</p>";

    // 5. Confirmar transacción
    $pdo->commit();

    echo '<h2 style="color: green;">¡Actualización completada con éxito!</h2>';
    echo '<p>El listado de materias en el formulario de "Agregar Usuario" ahora debería mostrar el nuevo plan de estudios.</p>';
    echo '<p><b>Importante:</b> Los profesores existentes han sido desvinculados de sus materias. Deberás asignárselas de nuevo desde el panel de "Asignación de Profesores".</p>';
    echo '<a href="../admin/agregar_usuario.php">Volver al formulario</a>';

} catch (Exception $e) {
    // Revertir en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo '<h2 style="color: red;">Error durante la actualización:</h2>';
    echo '<p>' . $e->getMessage() . '</p>';
}

?>
