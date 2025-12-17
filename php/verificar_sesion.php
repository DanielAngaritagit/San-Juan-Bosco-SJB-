<?php
/**
 * Script de Verificación de Sesión.
 * 
 * Este script se debe incluir al principio de cualquier página que requiera
 * que el usuario esté autenticado.
 * 
 * Funcionalidades:
 * 1. Inicia o reanuda la sesión.
 * 2. Comprueba si la sesión ha expirado por inactividad.
 * 3. Verifica si las variables de sesión clave existen, lo que indica que el usuario ha iniciado sesión.
 * 4. Si alguna de las comprobaciones falla, destruye la sesión y redirige al usuario a la página de login.
 */

session_start(); // Inicia o reanuda la sesión existente.

// --- Lógica de Tiempo de Inactividad ---
$inactivity_time = 1800; // 30 minutos en segundos.

// Comprueba si la variable de última actividad existe y si ha pasado el tiempo de inactividad.
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactivity_time)) {
    // Si la sesión ha expirado, se limpian todas las variables de sesión.
    session_unset();
    // Se destruye la sesión.
    session_destroy();
    // Redirige a la página de inicio con un mensaje indicando que la sesión expiró.
    header('Location: ../inicia.html?status=session_expired');
    exit(); // Detiene la ejecución del script.
}

// Si el usuario está activo, se actualiza la marca de tiempo de la última actividad.
$_SESSION['last_activity'] = time();

// --- Verificación de Autenticación ---
// Comprueba si las variables de sesión 'id_log' y 'rol' fueron establecidas durante el login.
// CORRECCIÓN: Se usan 'id_log' y 'rol' en lugar de 'user_id' y 'user_role' para que coincida con login.php.
if (!isset($_SESSION['id_log']) || !isset($_SESSION['rol'])) {
    // Si las variables no existen, el usuario no está (o ya no está) autenticado.
    session_unset();
    session_destroy();
    // Redirige a la página de inicio con un mensaje de no autorizado.
    header('Location: ../inicia.html?status=unauthorized');
    exit(); // Detiene la ejecución del script.
}

// Opcional: Verificar si el session_id actual coincide con el de la base de datos
// Esto requiere una consulta a la base de datos y puede añadir sobrecarga.
// Por simplicidad, no lo incluiremos en esta versión básica, pero es una buena práctica de seguridad.

?>