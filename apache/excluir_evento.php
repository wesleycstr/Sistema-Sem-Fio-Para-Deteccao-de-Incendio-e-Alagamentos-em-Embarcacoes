<?php

include 'conexao.php';

$id =
intval($_POST['id'] ?? 0);

if($id <= 0){

    die("ID inválido.");

}

$stmt = $conn->prepare("

    DELETE FROM sensores_evento

    WHERE id = ?

");

$stmt->bind_param(
    "i",
    $id
);

if($stmt->execute()){

    if($stmt->affected_rows > 0){

        echo "Evento excluído com sucesso.";

    }else{

        echo "Evento não encontrado.";

    }

}else{

    echo "Erro ao excluir evento: " .
         $stmt->error;

}