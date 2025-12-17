<?php
/**
 * Script para añadir la columna 'cargo' a la tabla 'tab_administradores'.
 * Debe ejecutarse una sola vez para actualizar el esquema de la base de datos.
 */

// --- Configuración de Errores y Headers ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/plain'); // Usar texto plano para la salida

// --- Inclusión de Dependencias ---
require_once 'conexion.php'; // Asegúrate de que este archivo contenga la conexión PDO $pdo

try {
    // Iniciar una transacción para asegurar la atomicidad de la operación
    $pdo->beginTransaction();

    // SQL para añadir la columna 'cargo' a la tabla 'tab_administradores'
    // Se define como VARCHAR(50) NOT NULL con un valor por defecto para filas existentes
    $sql = "ALTER TABLE tab_administradores ADD COLUMN cargo VARCHAR(50) NOT NULL DEFAULT 'Sin Cargo'";

    // Ejecutar la consulta
    $pdo->exec($sql);

    // Confirmar la transacción
    $pdo->commit();

    echo "Columna 'cargo' añadida exitosamente a la tabla 'tab_administradores'.\n";

} catch (PDOException $e) {
    // Revertir la transacción en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error al añadir la columna 'cargo': " . $e->getMessage() . "\n";
    echo "Código de error: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "Error inesperado: " . $e->getMessage() . "\n";
}

// Cerrar la conexión (opcional, PHP lo hace automáticamente al finalizar el script)
$pdo = null;

?>
