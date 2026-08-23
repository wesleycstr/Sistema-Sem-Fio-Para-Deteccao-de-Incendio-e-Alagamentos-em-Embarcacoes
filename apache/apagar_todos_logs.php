<?php

include 'conexao.php';

header(
    "Content-Type: application/json; charset=UTF-8"
);


/*
 * APAGA TODOS OS LOGS
 */

$sql = "TRUNCATE TABLE logs";


if($conn->query($sql)){

    echo json_encode([
        "sucesso" => true,
        "mensagem" =>
            "Todos os logs foram apagados com sucesso."
    ]);

}else{

    echo json_encode([
        "sucesso" => false,
        "mensagem" =>
            "Erro ao apagar os logs: " .
            $conn->error
    ]);

}

?>