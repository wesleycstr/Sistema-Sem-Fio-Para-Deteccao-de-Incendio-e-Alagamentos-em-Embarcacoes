<?php

include 'conexao.php';

header("Content-Type: text/plain; charset=UTF-8");

$sql = "

UPDATE sensores_evento

SET executar_script = 1

";

if($conn->query($sql)){

    echo "Execução de scripts ativada em todos os sensores de eventos.";

}else{

    echo "Erro ao ativar os scripts: " .
         $conn->error;

}

?>