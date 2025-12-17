<?php
/**
 * Script de Diagnóstico del Sistema SJB
 *
 * Este archivo verifica el correcto funcionamiento de los componentes clave del sistema,
 * incluyendo la conexión a la base de datos, la versión de PHP, las extensiones requeridas
 * y los permisos de escritura en directorios importantes.
 */

// Incluir el archivo de configuración para obtener las credenciales de la base de datos
// y otras configuraciones necesarias.
$config_loaded = false;
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
    $config_loaded = true;
} else {
    // Si config.php no existe, definimos constantes de ejemplo para que el script no falle,
    // pero la verificación de DB no será real.
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'sjb_database');
    define('DB_USER', 'sjb_user');
    define('DB_PASS', 'sjb_password');
}

// Función para mostrar el estado de una verificación
function display_status($check_name, $status, $message = '') {
    $icon = $status ? '✅' : '❌';
    $color = $status ? 'green' : 'red';
    echo "<tr>\n";
    echo "    <td>{$check_name}</td>\n";
    echo "    <td><span style=\"color: {$color};\">{$icon} " . ($status ? 'OK' : 'FALLÓ') . "</span></td>\n";
    echo "    <td>{$message}</td>\n";
    echo "</tr>\n";
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico del Sistema SJB</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #0056b3;
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #555;
        }
        .green { color: green; font-weight: bold; }
        .red { color: red; font-weight: bold; }
        .warning { color: orange; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Diagnóstico del Sistema SJB</h1>
        <table>
            <thead>
                <tr>
                    <th>Verificación</th>
                    <th>Estado</th>
                    <th>Mensaje</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // 1. Verificación de carga de config.php
                display_status('Carga de config.php', $config_loaded, $config_loaded ? 'Archivo de configuración encontrado y cargado.' : 'Archivo config.php no encontrado. Las credenciales de DB pueden ser incorrectas.');

                // 2. Verificación de la versión de PHP
                $php_version_ok = version_compare(PHP_VERSION, '7.4.0', '>='); // PHP 7.4 o superior
                display_status('Versión de PHP', $php_version_ok, 'Versión actual: ' . PHP_VERSION . '. Se recomienda PHP 7.4 o superior.');

                // 3. Verificación de extensiones PHP requeridas
                $required_extensions = ['pdo_pgsql', 'session', 'json', 'mbstring', 'openssl', 'gd'];
                foreach ($required_extensions as $ext) {
                    $ext_status = extension_loaded($ext);
                    display_status("Extensión PHP: {$ext}", $ext_status, $ext_status ? 'Instalada.' : 'No instalada. Puede causar problemas.');
                }

                // 4. Verificación de conexión a la base de datos (PostgreSQL)
                $db_connection_status = false;
                $db_message = '';
                if ($config_loaded) {
                    try {
                        $pdo = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $db_connection_status = true;
                        $db_message = 'Conexión a la base de datos PostgreSQL exitosa.';
                    } catch (PDOException $e) {
                        $db_message = 'Error de conexión a la base de datos: ' . $e->getMessage();
                    }
                } else {
                    $db_message = 'No se pudo verificar la conexión a la base de datos porque config.php no fue encontrado.';
                }
                display_status('Conexión a la Base de Datos', $db_connection_status, $db_message);

                // 5. Verificación de permisos de escritura en directorios clave
                $upload_dir = '../../uploads'; // Ruta relativa desde php/ a uploads/
                $upload_path = realpath(__DIR__ . '/' . $upload_dir);
                $upload_writable = false;
                $upload_message = '';

                if ($upload_path !== false) {
                    $upload_writable = is_writable($upload_path);
                    $upload_message = $upload_writable ? 'El directorio es escribible.' : 'El directorio NO es escribible. Puede causar problemas con la subida de archivos.';
                }
                else {
                    $upload_message = 'El directorio de subidas (' . $upload_dir . ') no existe o la ruta es incorrecta.';
                }
                display_status('Permisos de Escritura (uploads/)', $upload_writable, $upload_message);

                ?>
            </tbody>
        </table>
        <p style="margin-top: 30px; text-align: center; font-size: 0.9em; color: #777;">
            Este diagnóstico proporciona una visión general. Para un análisis más profundo, revisa los logs del servidor.
        </p>
    </div>
</body>
</html>