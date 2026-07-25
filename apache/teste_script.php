<?php

include 'conexao.php';

$evento = "Script de alarme executado com sucesso em " . date("d/m/Y H:i:s");

$sql = "INSERT INTO logs (id_sensor, data_hora, evento)
        VALUES (NULL, NOW(), ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $evento);

if ($stmt->execute()) {
    echo "OK";
} else {
    echo "Erro: " . $stmt->error;
}

$stmt->close();
$conn->close();