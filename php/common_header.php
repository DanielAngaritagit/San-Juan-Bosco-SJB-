<?php
// common_header.php

// Asegurarse de que la sesión está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario esté logueado
if (!isset($_SESSION['id_log'])) {
    header('Location: ../inicia.html?status=no_session');
    exit;
}

// Definir el rol actual para usarlo en el header
$current_rol = $_SESSION['rol'] ?? 'desconocido';

// Variable para el título de la página (se puede sobreescribir en cada página)
$page_title = $page_title ?? 'SJB'; 

// Variable para la hoja de estilos específica de la página
$page_stylesheet = $page_stylesheet ?? '';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <!-- Icono de la página -->
    <link rel="icon" type="image/png" href="/SJB/multimedia/<?= htmlspecialchars($current_rol) ?>/escudo.png">
    
    <!-- Estilos Comunes -->
    <link rel="stylesheet" href="../style/modal.css"> 

    <!-- Estilo específico de la página (si se define) -->
    <?php if (!empty($page_stylesheet)): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($page_stylesheet) ?>">
    <?php endif; ?>

</head>
<body>
