<?php

include 'conexao.php';

header("Content-Type: application/json");

$id = intval($_GET['id'] ?? 0);

if($id <= 0){

    echo json_encode([
        "erro" => "ID inválido."
    ]);

    exit;
}

$stmt = $conn->prepare("

    SELECT

        id,
        device_token,
        canal,
        localizacao,
        nome_evento,
        nivel_evento,
        alarme_sonoro

    FROM sensores_evento

    WHERE id = ?

    LIMIT 1

");

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){

    echo json_encode([
        "erro" => "Evento não encontrado."
    ]);

    exit;
}

$evento = $result->fetch_assoc();

echo json_encode($evento);