<?php
require_once 'verificar_sesion.php';

// Si verificar_sesion.php no termina la ejecución (es decir, la sesión es válida),
// devolvemos una respuesta de éxito.
header('Content-Type: application/json');
echo json_encode(['success' => true, 'status' => 'active']);
?>