<?php
header('Content-Type: text/plain');

if (extension_loaded('pdo')) {
    echo "La extensión PDO está instalada y habilitada.\n";
    
    if (extension_loaded('pdo_pgsql')) {
        echo "El driver PDO para PostgreSQL (pdo_pgsql) está instalado y habilitado.\n";
        echo "\nDiagnóstico completado: El soporte para la base de datos parece estar correctamente configurado en PHP.";
    } else {
        echo "ERROR: El driver PDO para PostgreSQL (pdo_pgsql) NO está habilitado.\n";
        echo "Por favor, edite su archivo php.ini y descomente (quite el ';') de la línea: extension=pdo_pgsql\n";
    }
} else {
    echo "ERROR: La extensión PDO NO está instalada o habilitada.\n";
    echo "Por favor, edite su archivo php.ini y descomente (quite el ';') de la línea: extension=pdo\n";
}
?>
