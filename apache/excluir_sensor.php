<?php

include 'conexao.php';

$id = $_POST['id'] ?? 0;

if(!$id){

    die("ID inválido");

}

/* remove alarmes */

$conn->query("

DELETE FROM alarme

WHERE id_sensor = '$id'

");

/* remove logs */

$conn->query("

DELETE FROM logs

WHERE id_sensor = '$id'

");

/* remove dados */

$conn->query("

DELETE FROM dados

WHERE id_sensor = '$id'

");

/* remove sensor */

$conn->query("

DELETE FROM sensores

WHERE id = '$id'

");

echo "Sensor excluído com sucesso";