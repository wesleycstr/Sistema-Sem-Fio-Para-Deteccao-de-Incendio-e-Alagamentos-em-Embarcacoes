<?php

include "conexao.php";

$dados = json_decode(file_get_contents("php://input"), true);

if(!is_array($dados)){
    die("JSON inválido");
}

$conn->begin_transaction();

$ordem = 1;

foreach($dados as $id){

    $stmt = $conn->prepare("
        UPDATE sensores
        SET ordem = ?
        WHERE id = ?
    ");

    if(!$stmt){
        die("Erro prepare: " . $conn->error);
    }

    $stmt->bind_param("ii", $ordem, $id);

    if(!$stmt->execute()){
        die("Erro execute: " . $stmt->error);
    }

    echo "Sensor $id -> Ordem $ordem<br>";

    $ordem++;

}

$conn->commit();

echo "<br>OK";