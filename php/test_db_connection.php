<?php
// This script tests the database connection.
// It will be deleted automatically after execution.

echo "Attempting to connect to the database...\n";

try {
    // Use the existing connection logic.
    require_once 'conexion.php';

    if ($pdo) {
        echo "Connection object created. Performing a test query...\n";
        
        // Perform a simple, non-destructive query.
        $stmt = $pdo->query("SELECT 1");
        
        if ($stmt) {
            echo "SUCCESS: Database connection is configured correctly.\n";
        } else {
            echo "FAILURE: Test query failed.\n";
        }
    } else {
        echo "FAILURE: The PDO object in conexion.php is null.\n";
    }

} catch (Exception $e) {
    // The catch block in conexion.php already handles this, 
    // but we add one here for extra safety and clear output.
    echo "FAILURE: An exception occurred: " . $e->getMessage() . "\n";
}
?>
