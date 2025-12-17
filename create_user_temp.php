<?php
$data = [
    "rol" => "profesor",
    "primer_nombre_p" => "Anonimo",
    "segundo_nombre_p" => "",
    "primer_apellido_p" => "Profesor",
    "segundo_apellido_p" => "",
    "tipo_documento_p" => "CC",
    "no_documento_p" => "anonimoprofesor",
    "fecha_nacimiento_p" => "1975-01-01",
    "fecha_expedicion_p" => "1995-01-01",
    "email_p" => "anonimo.profesor@example.com",
    "telefono_p" => "1234567890",
    "especialidad_p" => "Matemáticas",
    "id_materia_p" => 1, // Assuming 1 is a valid id_materia
    "nacionalidad_p" => "Desconocida",
    "direccion_p_prof" => "Calle Falsa 101",
    "rh_p" => "A+",
    "alergias_p" => "Ninguna",
    "titulo_academico_p" => "Licenciado",
    "eps_p" => "Sura",
    "estado_civil_p" => "Casado(a)"
];

$json_data = json_encode($data);

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => $json_data,
    ],
];
$context  = stream_context_create($options);
$result = file_get_contents('http://localhost/SJB/php/guardar_usuario.php', false, $context);

if ($result === FALSE) {
    echo "Error al enviar la solicitud.\n";
} else {
    echo $result;
}
?>