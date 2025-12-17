<?php
/*
Gaes #2, Andrea Gabriela Jaimes Oviedo y Keiner Daniel Bautista Angarita, Sistema de gestion y optimizacion escolar San Juan Bosco
*/
// Este archivo contiene el encabezado HTML común para todas las páginas de administrador,
// incluyendo la barra de navegación superior y los elementos del <head>.
$page_name = basename($_SERVER['PHP_SELF'], '.php');
$page_name = ucwords(str_replace(array('_', '-'), ' ', $page_name));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Andrea Gabriel Jaimes Oviedo, Keiner Daniel Bautista Angarita">
    <meta name="copyright" content="Colegio San Juan Bosco">
    <link rel="icon" type="image/x-icon" href="../multimedia/inicio_sesion/escudo.png">
    <title><?php echo $page_name; ?> - Administrador</title>
    <link rel="stylesheet" href="../style/admin.css">
    <link rel="stylesheet" href="../style/admin-responsive.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="top-bar">
        <button id="menu-toggle" class="menu-toggle">
            <img src="../multimedia/administrador/menu.png" alt="Menú">
        </button>
        <div class="project-info">
            <span class="project-name">San Juan Bosco</span>
            <span class="module-name"><?php echo $page_name; ?> - Administrador</span>
        </div>
        
    </div>