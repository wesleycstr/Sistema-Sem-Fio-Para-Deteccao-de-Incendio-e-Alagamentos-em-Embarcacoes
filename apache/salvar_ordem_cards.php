<?php

include "conexao.php";

$dados = json_decode(file_get_contents("php://input"), true);

if(!is_array($dados)){
    exit;
}

$conn->begin_transaction();

$ordem = 1;

foreach($dados as $id){

    $stmt = $conn->prepare("

        UPDATE sensores

        SET ordem_cards = ?

        WHERE id = ?

    ");

    $stmt->bind_param("ii",$ordem,$id);

    $stmt->execute();

    $ordem++;

}

$conn->commit();

echo "OK";