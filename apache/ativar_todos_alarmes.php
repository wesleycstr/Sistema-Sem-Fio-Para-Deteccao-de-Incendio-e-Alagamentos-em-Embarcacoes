<?php

include 'conexao.php';

header("Content-Type: text/plain; charset=UTF-8");

$sql = "

UPDATE sensores

SET alarme_sonoro = 1

";

if($conn->query($sql)){

    echo "Alarme sonoro ativado em todos os sensores ambientais.";

}else{

    echo "Erro ao ativar os alarmes: " .
         $conn->error;

}

?>