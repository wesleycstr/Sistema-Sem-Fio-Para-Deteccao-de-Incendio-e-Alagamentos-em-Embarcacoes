<?php

include 'conexao.php';

$id = $_POST['id'];

$tempMax = $_POST['tempMax'];

$umiMin = $_POST['umiMin'];

$umiMax = $_POST['umiMax'];

$gasMax = $_POST['gasMax'];

$alarmeSonoro = $_POST['alarmeSonoro'];

$sql = "

UPDATE sensores

SET

temperatura_max='$tempMax',
umidade_min='$umiMin',
umidade_max='$umiMax',
gas_max='$gasMax',
alarme_sonoro='$alarmeSonoro'

WHERE id='$id'

";

$conn->query($sql);

echo "OK";

?>
