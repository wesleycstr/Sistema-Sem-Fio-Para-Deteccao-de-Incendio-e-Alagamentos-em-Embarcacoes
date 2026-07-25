<?php

include 'conexao.php';

$sql = "
SELECT 
  sensores.id,
  sensores.localizacao,
  alarme.status
FROM sensores
LEFT JOIN alarme 
ON sensores.id = alarme.id_sensor
";

$result = $conn->query($sql);

while($row = $result->fetch_assoc()){

    $classe = "";
    $cor = "#161616";
    $statusTexto = "Desconhecido";

    switch($row['status']){

        case 1:
            $cor = "#2e7d32";
            $statusTexto = "NORMAL";
            break;

        case 2:
            $cor = "#f9a825";
            $statusTexto = "ATENÇÃO";
            break;

        case 3:
            $classe = "alarme";
            $statusTexto = "ALARME";
            break;
    }

    echo "
    <tr class='$classe' style='background-color:$cor'>
        <td>{$row['id']}</td>
        <td>{$row['localizacao']}</td>
        <td>{$statusTexto}</td>
    </tr>
    ";
}

?>