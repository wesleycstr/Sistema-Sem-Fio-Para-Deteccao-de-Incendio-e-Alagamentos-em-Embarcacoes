<?php

include 'conexao.php';

header("Content-Type: text/plain; charset=UTF-8");

$sql = "

UPDATE sensores_evento

SET executar_script = 0

";

if($conn->query($sql)){

    echo "Execução de scripts desativada em todos os sensores de eventos.";

}else{

    echo "Erro ao desativar os scripts: " .
         $conn->error;

}

?>