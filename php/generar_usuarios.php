<?php
header('Content-Type: text/plain');

$usuarios = [
    ['usuario' => 'estudiante_test', 'contrasena' => 'testpass', 'rol' => 'estudiante'],
    ['usuario' => 'padre_test', 'contrasena' => 'testpass', 'rol' => 'padre'],
    ['usuario' => 'profesor_test', 'contrasena' => 'testpass', 'rol' => 'profesor'],
];

echo "-- Copia y ejecuta las siguientes sentencias SQL en tu base de datos:\n\n";

foreach ($usuarios as $user) {
    $usuario = $user['usuario'];
    $contrasena_hasheada = password_hash($user['contrasena'], PASSWORD_DEFAULT);
    $rol = $user['rol'];

    echo "INSERT INTO login (usuario, contrasena, rol) VALUES ('" . $usuario . "', '" . $contrasena_hasheada . "', '" . $rol . "');\n";
}

?>