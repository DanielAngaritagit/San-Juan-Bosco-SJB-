<?php
require_once 'config.php';

header('Content-Type: text/plain');

echo "Iniciando inserción de notas de prueba...\n";

try {
    $pdo = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexión a la base de datos establecida.\n";

    // --- DATOS DE PRUEBA --- //
    // ¡IMPORTANTE: Reemplaza estos valores con IDs válidos de tu base de datos!
    $student_id = 4; // ID de un estudiante existente (ej. 1 al 10)
    $grades_to_insert = [
        ['id_curso' => 1, 'id_profesor' => 1, 'calificacion' => 4.5, 'periodo' => 1], // Matemáticas Básicas, Periodo 1
        ['id_curso' => 2, 'id_profesor' => 2, 'calificacion' => 3.8, 'periodo' => 1], // Ciencias Naturales, Periodo 1
        ['id_curso' => 1, 'id_profesor' => 1, 'calificacion' => 3.2, 'periodo' => 2], // Matemáticas Básicas, Periodo 2
        ['id_curso' => 2, 'id_profesor' => 2, 'calificacion' => 4.0, 'periodo' => 2], // Ciencias Naturales, Periodo 2
        ['id_curso' => 3, 'id_profesor' => 3, 'calificacion' => 2.8, 'periodo' => 1]  // Literatura Universal, Periodo 1
    ];

    $stmt = $pdo->prepare("INSERT INTO tab_calificaciones (id_estud, id_curso, id_profesor, calificacion, periodo) VALUES (:id_estud, :id_curso, :id_profesor, :calificacion, :periodo)");

    foreach ($grades_to_insert as $grade) {
        $stmt->bindParam(':id_estud', $student_id);
        $stmt->bindParam(':id_curso', $grade['id_curso']);
        $stmt->bindParam(':id_profesor', $grade['id_profesor']);
        $stmt->bindParam(':calificacion', $grade['calificacion']);
        $stmt->bindParam(':periodo', $grade['periodo']);
        $stmt->execute();
        echo "Nota insertada para estudiante {$student_id}, curso {$grade['id_curso']}, profesor {$grade['id_profesor']}, calificación {$grade['calificacion']}, periodo {$grade['periodo']}.\n";
    }

    echo "\nInserción de notas de prueba completada exitosamente.\n";

} catch (PDOException $e) {
    echo "\nError de base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "\nError: " . $e->getMessage() . "\n";
}

echo "Fin del script.\n";
?>