<?php
function processarAlertas($conn){

    $sql = "

    SELECT

        sensores.id,

        sensores.temperatura_max,
        sensores.umidade_min,
        sensores.umidade_max,
        sensores.gas_max,

        dados.temperatura,
        dados.umidade,
        dados.gas_co,

        alarme.status AS status_atual

    FROM sensores

    LEFT JOIN dados
    ON dados.id = (

        SELECT id
        FROM dados d2
        WHERE d2.id_sensor = sensores.id
        ORDER BY data_hora DESC
        LIMIT 1

    )

    LEFT JOIN alarme
    ON alarme.id_sensor = sensores.id

    ";

    $result = $conn->query($sql);

    if(!$result){
        return;
    }

    while($row = $result->fetch_assoc()){

        $status = 1;

        $idSensor = $row['id'];

        /* TEMPERATURA */

        if(
            isset($row['temperatura']) &&
            $row['temperatura'] > $row['temperatura_max']
        ){
            $status = 3;
        }

        /* UMIDADE */

        if(
            isset($row['umidade']) &&
            (
                $row['umidade'] < $row['umidade_min']
                ||
                $row['umidade'] > $row['umidade_max']
            )
        ){
            $status = 2;
        }

        /* GÁS */

        if(
            isset($row['gas_co']) &&
            $row['gas_co'] > $row['gas_max']
        ){
            $status = 3;
        }

        /* STATUS ANTERIOR */

        $statusAnterior = isset($row['status_atual'])
            ? $row['status_atual']
            : -1;

        /* REGISTRA SOMENTE SE MUDOU */

        if($status != $statusAnterior){

    switch($status){

        case 1:
            $evento =
            "Sensor $idSensor voltou ao NORMAL";
            break;

        case 2:
            $evento =
            "Sensor $idSensor entrou em ATENÇÃO";
            break;

        case 3:
            $evento =
            "Sensor $idSensor entrou em ALARME";
            break;

        default:
            $evento =
            "Sensor $idSensor status desconhecido";

    }

            registrarLog($conn, $idSensor, $evento);

        }

        /* UPDATE */

        $sqlUpdate = "

        INSERT INTO alarme
        (id_sensor, status)

        VALUES
        ('$idSensor', '$status')

        ON DUPLICATE KEY UPDATE
        status='$status'

        ";

        $conn->query($sqlUpdate);

    }

}
?>
