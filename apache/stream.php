<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'conexao.php';

include 'logs.php';

include 'processar_alertas.php';

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

while (ob_get_level() > 0) {
    ob_end_flush();
}

ob_implicit_flush(true);

while(true){

processarAlertas($conn);


$sql = "

SELECT 
    sensores.id,
    sensores.device_token,
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

ORDER BY sensores.ordem

";

$result = $conn->query($sql);

$html = "";
$cards = "";
$erroGeral = false;

while($row = $result->fetch_assoc()){

    $classe = "";
    $cor = "#161616";
    $statusTexto = "DESCONHECIDO";

    $temperatura = isset($row['temperatura'])
    ? number_format($row['temperatura'], 2, '.', '')
    : '--';

    $umidade = isset($row['umidade'])
    ? number_format($row['umidade'], 2, '.', '')
    : '--';

    $gas = isset($row['gas_co'])
    ? number_format($row['gas_co'], 2, '.', '')
    : '--';

    if(empty($row['data_hora'])){

    $offline = true;

    }else{

        $ultimaAtualizacao = strtotime($row['data_hora']);

        $offline = (time() - $ultimaAtualizacao) > 10;
    }

    /* estado atual salvo no banco */

    $sqlOffline = "

    SELECT offline

    FROM sensores

    WHERE id='{$row['id']}'

    ";

    $resultOffline = $conn->query($sqlOffline);

    $rowOffline = $resultOffline->fetch_assoc();

    $offlineAnterior = $rowOffline['offline'];

    if($offline){

        if($offlineAnterior == 0){

        registrarLog(
            $conn,
            $row['id'],
            //"Sensor {$row['nome']} ficou OFFLINE"
            "Sensor ficou OFFLINE"
        );

        $conn->query("

        UPDATE sensores

        SET offline = 1

        WHERE id='{$row['id']}'

        ");

}
        $cor = "#424242";
        $statusTexto = "OFFLINE";
        $classe = "";
        $erroGeral = true;

    }else{
        if($offlineAnterior == 1){

            registrarLog(
                $conn,
                $row['id'],
                //"Sensor {$row['nome']} voltou ONLINE"
                "Sensor voltou ONLINE"
            );

            $conn->query("

            UPDATE sensores

            SET offline = 0

            WHERE id='{$row['id']}'

            ");

        }

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


    }

           /* ===========================
   COR DOS CARDS
=========================== */

$classeCard = "normal";

if($offline){

    $classeCard = "offline";

}else{

    switch($row['status']){

        case 1:
            $classeCard = "normal";
            break;

        case 2:
            $classeCard = "atencao";
            break;

        case 3:
            $classeCard = "alarme";
            break;

    }

}

/* ===========================
   CARD DO SENSOR
=========================== */

$cards .= "
<div class='cardSensor $classeCard'
onclick='abrirModal({$row['id']})'>


<div class='cardTitulo'>

{$row['localizacao']}

</div>

<div class='cardValor'>
🌡 {$temperatura} °C
</div>

<div class='cardValor'>
💧 {$umidade} %
</div>

<div class='cardValor'>
☁ {$gas} ppm
</div>

<div class='cardValor'>
🕒 {$row['data_hora']}
</div>

<div class='cardStatus'>
{$statusTexto}
</div>

</div>

";


    $html .= "

<tr
    data-id='{$row['id']}'
    class='$classe'
    style='background-color:$cor'>

        <td class='dragHandle'
        style='
        width:35px;
        text-align:center;
        cursor:grab;
        font-size:20px;
        '>

        ☰

        </td>

        <td>{$row['device_token']}</td>

        <td>{$row['localizacao']}</td>

        <td>{$temperatura} °C</td>

        <td>{$umidade} %</td>

        <td>{$gas}</td>

        <td>{$statusTexto}</td>

        <td>{$row['data_hora']}</td>

        <td>

        <button class='btnConfig'
        onclick='abrirModal({$row['id']})'>

        ⚙️

        </button>

        </td>

        <td>

        <button
        class='btnExcluir'
        onclick='excluirSensor({$row['id']})'>

        🗑️

        </button>

        </td>

</td>

    </tr>

    ";
}

/* consulta relacionada a tabela eventos*/
$htmlEventos = "";

$sql="SELECT

id,
device_token,
localizacao,
nome_evento,
nivel_evento,
estado,
data_hora,
offline

FROM sensores_evento

ORDER BY ordem_eventos";

$resultEventos = $conn->query($sql);

while($row = $resultEventos->fetch_assoc()){

    $ultimaAtualizacao = strtotime($row['data_hora']);

    $agora = time();

    $diferenca = $agora - $ultimaAtualizacao;

    $offline = $diferenca > 15;


    if($offline){

    $erroGeral = true;

    $conn->query("

        UPDATE sensores_evento

        SET offline = 1

        WHERE id='{$row['id']}'

    ");

    }else{

        $conn->query("

            UPDATE sensores_evento

            SET offline = 0

            WHERE id='{$row['id']}'

        ");

    }

    $status = $offline
        ? "OFFLINE"
        : "ONLINE";

/* =====================================
   DEFINE A COR DA LINHA
===================================== */

$cor = "#2e7d32";   // Verde (normal)

if($offline){

    // Dispositivo sem comunicação
    $cor = "#616161";

}else{

    // Evento ATIVO
    if($row['estado'] == 1){

        switch($row['nivel_evento']){

            case "informacao":

                $cor = "#1565c0";   // Azul
                break;

            case "atencao":

                $cor = "#f9a825";   // Amarelo
                break;

            case "critico":

                $cor = "#c62828";   // Vermelho
                break;

            default:

                $cor = "#2e7d32";
        }

    }

}
    $htmlEventos .= "

<tr data-id='{$row['id']}'
style='background:$cor;'>

<td class='dragHandle'>

☰

</td>

<td>{$icone} {$row['nome_evento']}</td>

<td>{$row['localizacao']}</td>

<td>{$row['nivel_evento']}</td>

<td>{$status}</td>

<td>{$row['data_hora']}</td>

<td>

<button
class='btnConfig'
onclick='abrirModalEvento({$row['id']})'>

⚙️

</button>

</td>

<td>

<button
class='btnExcluir'
onclick='excluirEvento({$row['id']})'>

🗑️

</button>

</td>

</tr>

";

}

/* mensagem de erro */

if($erroGeral){

    $aviso = "

    <tr>

        <td colspan='10'
            style='background:#b71c1c;
                   color:white;
                   text-align:center;
                   font-weight:bold;
                   font-size:18px;'>

            ⚠ EXISTEM DISPOSITIVOS SEM COMUNICAÇÃO

        </td>

    </tr>

    ";

    $html = $aviso.$html;

    $htmlEventos = $aviso.$htmlEventos;

}

$dados = [

    "tabela" =>

        str_replace("\n","",$html),

    "eventos" =>

        str_replace("\n","",$htmlEventos),

    "cards" =>

        str_replace("\n","",$cards)

];

echo "data: " . json_encode($dados) . "\n\n";

flush();

sleep(1);

}
?>