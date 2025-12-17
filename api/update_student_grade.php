<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
header('Content-Type: application/json');
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require_once '../php/conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['student_document_id']) || !isset($data['grade_number']) || !isset($data['section_letter'])) {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros: student_document_id, grade_number, section_letter.']);
    exit();
}

$student_document_id = $data['student_document_id'];
$grade_number = $data['grade_number'];
$section_letter = $data['section_letter'];

error_log("Updating student: " . $student_document_id . " to grade " . $grade_number . " section " . $section_letter);

try {
    $pdo->beginTransaction();

    // 1. Get id_seccion for the given grade and section
    $stmt_get_seccion = $pdo->prepare("SELECT id_seccion FROM tab_grados WHERE grado_numero = :grado_numero AND letra_seccion = :letra_seccion");
    $stmt_get_seccion->bindParam(':grado_numero', $grade_number, PDO::PARAM_INT);
    $stmt_get_seccion->bindParam(':letra_seccion', $section_letter, PDO::PARAM_STR);
    $stmt_get_seccion->execute();
    $seccion = $stmt_get_seccion->fetch(PDO::FETCH_ASSOC);

    if (!$seccion) {
        throw new Exception("El grado y sección especificados no existen.");
    }
    $id_seccion = $seccion['id_seccion'];
    error_log("id_seccion found: " . $id_seccion);

    // 2. Get id_ficha for the student using their document ID
    $stmt_get_id_ficha = $pdo->prepare("SELECT id_ficha FROM tab_estudiante WHERE no_documento = :no_documento");
    $stmt_get_id_ficha->bindParam(':no_documento', $student_document_id, PDO::PARAM_STR);
    $stmt_get_id_ficha->execute();
    $student_ficha = $stmt_get_id_ficha->fetch(PDO::FETCH_ASSOC);

    if (!$student_ficha) {
        throw new Exception("Estudiante con el documento '" . $student_document_id . "' no encontrado.");
    }
    $id_ficha = $student_ficha['id_ficha'];
    error_log("id_ficha found for student: " . $id_ficha);

    // 3. Update id_seccion in tab_estudiante
    $stmt_update_ficha = $pdo->prepare("UPDATE tab_estudiante SET id_seccion = :id_seccion WHERE id_ficha = :id_ficha");
    $stmt_update_ficha->bindParam(':id_seccion', $id_seccion, PDO::PARAM_INT);
    $stmt_update_ficha->bindParam(':id_ficha', $id_ficha, PDO::PARAM_INT);
    $stmt_update_ficha->execute();
    error_log("Executing UPDATE tab_estudiante SET id_seccion = " . $id_seccion . " WHERE id_ficha = " . $id_ficha);

    // 4. Get id_curso from tab_cursos based on grado_numero
    // Assuming one course per grade for simplicity, or picking the first one found
    $stmt_get_id_curso = $pdo->prepare("SELECT id_curso FROM tab_cursos WHERE grado = :grado_numero LIMIT 1");
    $stmt_get_id_curso->bindParam(':grado_numero', $grade_number, PDO::PARAM_INT);
    $stmt_get_id_curso->execute();
    $curso = $stmt_get_id_curso->fetch(PDO::FETCH_ASSOC);

    if (!$curso) {
        throw new Exception("No se encontró un curso para el grado especificado.");
    }
    $id_curso_from_tab_cursos = $curso['id_curso'];
    error_log("id_curso from tab_cursos: " . $id_curso_from_tab_cursos);

    // 5. Update id_curso in tab_matriculas
    $stmt_update_matricula = $pdo->prepare("UPDATE tab_matriculas SET id_curso = :id_curso WHERE id_estud = :id_ficha");
    $stmt_update_matricula->bindParam(':id_curso', $id_curso_from_tab_cursos, PDO::PARAM_INT);
    $stmt_update_matricula->bindParam(':id_ficha', $id_ficha, PDO::PARAM_INT);
    $stmt_update_matricula->execute();
    error_log("Executing UPDATE tab_matriculas SET id_curso = " . $id_curso_from_tab_cursos . " WHERE id_estud = " . $id_ficha);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Grado del estudiante actualizado exitosamente.']);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Error updating student grade: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el grado del estudiante: ' . $e->getMessage()]);
}

?>