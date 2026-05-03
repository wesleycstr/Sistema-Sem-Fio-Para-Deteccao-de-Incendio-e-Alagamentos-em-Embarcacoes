<?php

include 'conexao.php';
include 'logs.php';

$sql = "

SELECT COUNT(*) AS total

FROM alarme

INNER JOIN sensores
ON sensores.id = alarme.id_sensor

WHERE
alarme.status = 3
AND sensores.alarme_sonoro = 1

";

$result = $conn->query($sql);

$row = $result->fetch_assoc();

echo json_encode([
    "alarme" => $row['total'] > 0
]);

?>
