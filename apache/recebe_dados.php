<?php

$host = "mariadb";
$user = "root";
$pass = "senha";
$db   = "siscav";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

/* recebe dados via GET ou POST */
$sensor = $_REQUEST['sensor'] ?? null;
$temp   = $_REQUEST['temp'] ?? null;
$umid   = $_REQUEST['umid'] ?? null;
$gas_co   = $_REQUEST['gas'] ?? null;

/* verifica se os dados existem */
if ($sensor === null || $temp === null) {
    die("Erro: parâmetros sensor e temperatura são obrigatórios");
}

/* insere no banco */
$sql = "INSERT INTO dados (id_sensor, temperatura, umidade, gas_co)
        VALUES ('$sensor','$temp', '$umid', '$gas_co')";

if ($conn->query($sql) === TRUE) {
    echo "OK";
} else {
    echo "Erro SQL: " . $conn->error;
}

$conn->close();

?>
