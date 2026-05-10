<?php

// ======================================================
// CONFIGURAÇÕES
// ======================================================

$host = "mariadb";
$user = "root";
$pass = "senha";
$db   = "siscav";

// ======================================================
// TOKENS DOS DISPOSITIVOS
// ======================================================

$devices = [

    "sensor_azul" => [
        "secret" => "CHAVE_SUPER_SECRETA_123"
    ],

];

// ======================================================
// CONEXÃO MYSQL
// ======================================================

$conn = new mysqli(
    $host,
    $user,
    $pass,
    $db
);

if ($conn->connect_error) {

    http_response_code(500);

    die("Erro banco");
}

// ======================================================
// SOMENTE POST
// ======================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    die("Metodo invalido");
}

// ======================================================
// HEADERS
// ======================================================

$token =
$_SERVER['HTTP_X_DEVICE_TOKEN'] ?? '';

$timestamp =
$_SERVER['HTTP_X_TIMESTAMP'] ?? '';

$signature =
$_SERVER['HTTP_X_SIGNATURE'] ?? '';

// ======================================================
// VALIDA TOKEN
// ======================================================

if (!isset($devices[$token])) {

    http_response_code(403);

    die("Token invalido");
}

$secret =
$devices[$token]['secret'];

// ======================================================
// ANTI REPLAY
// ======================================================

$current_time = time();

if (abs($current_time - intval($timestamp)) > 30) {

    http_response_code(401);

    die("Timestamp invalido");
}

// ======================================================
// PAYLOAD JSON
// ======================================================

$payload =
file_get_contents("php://input");

$data =
json_decode($payload, true);

if (!$data) {

    http_response_code(400);

    die("JSON invalido");
}

// ======================================================
// VALIDA HMAC
// ======================================================

$local_signature =
sha1(
    $payload .
    $timestamp .
    $secret
);

if (
    !hash_equals(
        $local_signature,
        $signature
    )
) {

    http_response_code(401);

    die("Assinatura invalida");
}

// ======================================================
// CAMPOS
// ======================================================

$sensor =
$data['sensor'] ?? '';

$temperatura =
$data['temperatura'] ?? 0;

$umidade =
$data['umidade'] ?? 0;

$gas =
$data['gas'] ?? 0;

// ======================================================
// VALIDAÇÕES
// ======================================================

if ($sensor == '') {

    http_response_code(400);

    die("Sensor ausente");
}

// temperatura plausível
if (
    $temperatura < -50 ||
    $temperatura > 100
) {

    http_response_code(400);

    die("Temperatura invalida");
}

// umidade plausível
if (
    $umidade < 0 ||
    $umidade > 100
) {

    http_response_code(400);

    die("Umidade invalida");
}

// ======================================================
// INSERT SEGURO
// ======================================================

$stmt =
$conn->prepare(

"INSERT INTO dados
(id_sensor,
 temperatura,
 umidade,
 gas_co)

VALUES (?, ?, ?, ?)"
);

$stmt->bind_param(
    "sddd",
    $sensor,
    $temperatura,
    $umidade,
    $gas
);

// ======================================================
// EXECUTA
// ======================================================

if ($stmt->execute()) {

    echo "OK";

} else {

    http_response_code(500);

    echo "Erro SQL";
}

$stmt->close();

$conn->close();

?>
