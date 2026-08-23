<?php

include 'conexao.php';

header("Content-Type: text/plain; charset=UTF-8");

$sql = "

UPDATE sensores_evento

SET alarme_sonoro = 0

";

if($conn->query($sql)){

    echo "Alarme sonoro desativado em todos os sensores de eventos.";

}else{

    echo "Erro ao desativar os alarmes: " .
         $conn->error;

}

?>