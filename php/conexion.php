<?php
/**
 * Script de Conexión a la Base de Datos.
 * 
 * Este archivo es responsable de establecer la conexión con la base de datos PostgreSQL
 * utilizando las credenciales definidas en `config.php`.
 * Utiliza PDO (PHP Data Objects) para una conexión segura y compatible con múltiples bases de datos.
 */

// Incluye el archivo de configuración que contiene las constantes de la base de datos 
// (DB_HOST, DB_NAME, DB_USER, DB_PASS).
require_once 'config.php';

// Inicializa la variable $pdo a null. Esta variable contendrá el objeto de conexión.
$pdo = null;

// Se utiliza un bloque try-catch para manejar posibles errores de conexión.
try {
    // Crea una nueva instancia de PDO para conectarse a PostgreSQL.
    // El primer argumento es el DSN (Data Source Name), que especifica el tipo de base de datos,
    // el host y el nombre de la base de datos.
    // Los siguientes argumentos son el usuario y la contraseña.
    $pdo = new PDO("pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    
    // Establece el modo de error de PDO a Exception.
    // Esto significa que si ocurre un error en una consulta, PDO lanzará una PDOException,
    // lo que permite un manejo de errores más robusto y claro.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
    /**
     * Si la conexión falla, se captura la excepción.
     * En un entorno de producción, el error debería registrarse en un archivo de log en lugar de mostrarse.
     * Ejemplo: error_log("Error de conexión: " . $e->getMessage());
     */

    // Para las llamadas de API, se devuelve una respuesta JSON estandarizada indicando un error genérico.
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos. Por favor, inténtelo de nuevo más tarde.']);
    
    // Registra el error detallado en el log del servidor (en lugar de mostrarlo al usuario).
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    
    // Detiene la ejecución del script para prevenir más errores.
    exit();
}
?>