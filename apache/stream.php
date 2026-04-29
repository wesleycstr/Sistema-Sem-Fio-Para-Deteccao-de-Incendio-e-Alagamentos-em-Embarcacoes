<?php

include 'conexao.php';

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

while (ob_get_level() > 0) {
    ob_end_flush();
}

ob_implicit_flush(true);

while(true){

$sql = "

SELECT 
    sensores.id,
    sensores.localizacao,
    alarme.status,
    dados.temperatura,
    dados.umidade,
    dados.gas_co,
    dados.data_hora

FROM sensores

LEFT JOIN alarme
ON sensores.id = alarme.id_sensor

LEFT JOIN dados
ON dados.id = (

    SELECT id
    FROM dados d2
    WHERE d2.id_sensor = sensores.id
    ORDER BY data_hora DESC
    LIMIT 1

)

ORDER BY sensores.id

";

$result = $conn->query($sql);

$html = "";

while($row = $result->fetch_assoc()){

    $classe = "";
    $cor = "#161616";
    $statusTexto = "DESCONHECIDO";

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

    $temperatura = $row['temperatura'] ?? '--';
    $umidade = $row['umidade'] ?? '--';
    $gas = $row['gas_co'] ?? '--';

    $html .= "

    <tr class='$classe' style='background-color:$cor'>

        <td>{$row['id']}</td>

        <td>{$row['localizacao']}</td>

        <td>{$temperatura} °C</td>

        <td>{$umidade} %</td>

        <td>{$gas}</td>

        <td>{$statusTexto}</td>

        <td>{$row['data_hora']}</td>

    </tr>

    ";
}

echo "data: " . str_replace("\n", "", $html) . "\n\n";

flush();

sleep(1);

}
?>
