<?php
/**
 * Script de Cierre de Sesión (Logout).
 * 
 * Este script se encarga de finalizar de forma segura la sesión de un usuario.
 * 
 * Funcionalidades:
 * 1. Inicia la sesión para acceder a sus variables.
 * 2. Registra el evento de logout en la base de datos para auditoría.
 * 3. Limpia todas las variables de la sesión.
 * 4. Elimina la cookie de sesión del navegador.
 * 5. Destruye la sesión en el servidor.
 * 6. Devuelve una respuesta JSON para que el frontend redirija al usuario.
 */

session_start(); // Inicia o reanuda la sesión para poder acceder a las variables.

// --- Registro de Auditoría ---
// Es una buena práctica registrar cuándo un usuario cierra sesión.
require_once 'conexion.php'; // Conexión a la BD.

if (isset($_SESSION['id_log']) && $pdo) {
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $agente = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $stmt = $pdo->prepare("INSERT INTO accesos (usuario_id, direccion_ip, agente_usuario, tipo_acceso) VALUES (?, ?, ?, 'logout')");
        $stmt->execute([$_SESSION['id_log'], $ip, $agente]);
    } catch (PDOException $e) {
        // Si falla el registro, no se detiene el proceso de logout, 
        // pero se podría registrar el error en un log del sistema.
        error_log("Error al registrar logout: " . $e->getMessage());
    }
}

// --- Finalización de la Sesión ---

// Limpia todas las variables de la sesión (ej. $_SESSION['id_log'], $_SESSION['rol']).
$_SESSION = array();

// Borra la cookie de sesión del navegador del cliente.
// Esto asegura que la sesión no pueda ser reutilizada.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruye la sesión en el servidor.
session_destroy();

// --- Respuesta al Cliente ---
// En lugar de redirigir directamente desde PHP, se envía una respuesta JSON.
// Esto le da al frontend (JavaScript) el control para realizar la redirección.
header('Content-Type: application/json');
echo json_encode(['success' => true, 'redirect' => '../inicia.html']);
exit;

?>