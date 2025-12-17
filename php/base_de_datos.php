<?php
/*echo "Entré a conectarme";*/
$contraseña         = "06022006"; // <-- ¡CAMBIA ESTO EN PRODUCCIÓN! Gestionar de forma segura.
$usuario            = "postgres"; // <-- ¡CAMBIA ESTO EN PRODUCCIÓN! Gestionar de forma segura.
$nombreBaseDeDatos  = "gr_sjb_server";
#Puede ser 127.0.0.1 o el nombre de tu equipo; o la IP de un servidor remoto
$server = "localhost";
$puerto = "5432";
try
{
    $base_de_datos = new PDO("pgsql:host=$server;port=$puerto;dbname=$nombreBaseDeDatos", $usuario, $contraseña);
    $base_de_datos->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch (Exception $e)
{
    // En un entorno de producción, se debería registrar el error y no mostrarlo al usuario.
    // error_log("Error de conexión a la base de datos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor. Por favor, inténtalo de nuevo más tarde.']);
    exit();
}