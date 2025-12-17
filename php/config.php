<?php
/**
 * Archivo de Configuración de la Base de Datos.
 * 
 * Este archivo define las constantes utilizadas por `conexion.php` para conectarse a la base de datos.
 * Lee las credenciales desde variables de entorno para mayor seguridad.
 * Proporciona valores predeterminados para facilitar el entorno de desarrollo local.
 */

// Lee la variable de entorno o usa un valor predeterminado si no está definida.
// Esto permite que el entorno de desarrollo local funcione sin configuración adicional,
// pero el servidor de producción usará las variables de entorno seguras.

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'postgres');
define('DB_PASS', getenv('DB_PASS') ?: '06022006');
define('DB_NAME', getenv('DB_NAME') ?: 'gr_sjb_server');

?>