<?php

function processarAlertas($conn){

    // =====================================================
    // PROCESSAMENTO DOS SENSORES AMBIENTAIS
    // =====================================================

    $sql = "

    SELECT

        sensores.id,

        sensores.temperatura_max,
        sensores.umidade_min,
        sensores.umidade_max,
        sensores.gas_max,
        sensores.executar_script,
        sensores.script_alarme,

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

    if($result){

        while($row = $result->fetch_assoc()){

            $status = 1;

            $idSensor = $row['id'];

            // =================================================
            // TEMPERATURA
            // =================================================

            if(
                isset($row['temperatura']) &&
                $row['temperatura'] > $row['temperatura_max']
            ){

                $status = 3;

            }

            // =================================================
            // UMIDADE
            // =================================================

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

            // =================================================
            // GÁS
            // =================================================

            if(
                isset($row['gas_co']) &&
                $row['gas_co'] > $row['gas_max']
            ){

                $status = 3;

            }

            // =================================================
            // STATUS ANTERIOR
            // =================================================

            $statusAnterior =
                isset($row['status_atual'])
                ? $row['status_atual']
                : -1;

            // =================================================
            // MUDANÇA DE STATUS
            // =================================================

            if($status != $statusAnterior){

                switch($status){

                    case 1:

                        $evento =
                            "Sensor voltou ao NORMAL";

                        break;

                    case 2:

                        $evento =
                            "Sensor entrou em ATENÇÃO";

                        break;

                    case 3:

                        $evento =
                            "Sensor entrou em ALARME";

                        break;

                    default:

                        $evento =
                            "Sensor status desconhecido";

                }

                registrarLog(
                    $conn,
                    $idSensor,
                    $evento
                );

                // =============================================
                // EXECUTA SCRIPT DO ALARME AMBIENTAL
                // =============================================

                if(
                    $status == 3 &&
                    $row['executar_script'] == 1 &&
                    !empty($row['script_alarme'])
                ){

                    $script =
                        escapeshellarg(
                            $row['script_alarme']
                        );

                    $comando =
                        "php $script";

                    exec(
                        $comando .
                        " > /dev/null 2>&1 &"
                    );

                }

            }

            // =================================================
            // ATUALIZA ALARME
            // =================================================

            $sqlUpdate = "

            INSERT INTO alarme
            (
                id_sensor,
                status
            )

            VALUES
            (
                '$idSensor',
                '$status'
            )

            ON DUPLICATE KEY UPDATE

            status='$status'

            ";

            $conn->query($sqlUpdate);

        }

    }


    // =====================================================
    // PROCESSAMENTO DOS EVENTOS
    // =====================================================

    $sqlEventos = "

    SELECT

        id,
        device_token,
        canal,
        nome_evento,
        nivel_evento,
        estado,
        estado_anterior,
        alarme_sonoro

    FROM sensores_evento

    ";

    $resultEventos =
        $conn->query($sqlEventos);

    if(!$resultEventos){

        return;

    }


    while($evento = $resultEventos->fetch_assoc()){

        $idEvento =
            intval($evento['id']);

        $estado =
            intval($evento['estado']);

        $estadoAnterior =
            intval($evento['estado_anterior']);

        $alarmeSonoro =
            intval($evento['alarme_sonoro']);

        $nomeEvento =
            $evento['nome_evento'];

        $nivelEvento =
            $evento['nivel_evento'];


        // =================================================
        // NOVO EVENTO
        // estado anterior = 0
        // estado atual    = 1
        // =================================================

        if(
            $estado == 1 &&
            $estadoAnterior == 0
        ){

            // =============================================
            // REGISTRA NO LOG
            // =============================================

            $mensagem =
                "Evento {$nomeEvento} ativado";

            /*
             * IMPORTANTE:
             *
             * Neste momento não usamos registrarLog()
             * porque o ID pertence a sensores_evento
             * e não a sensores.
             *
             * Podemos criar posteriormente uma estrutura
             * de log específica para eventos.
             */

            error_log(
                $mensagem
            );


            // =============================================
            // ALARME SONORO
            // =============================================

            if($alarmeSonoro == 1){

                /*
                 * O processamento do som será feito pelo
                 * painel através do stream.php.
                 *
                 * Aqui apenas identificamos que o evento
                 * deve gerar alarme.
                 */

            }

        }


        // =================================================
        // EVENTO ENCERRADO
        // estado anterior = 1
        // estado atual    = 0
        // =================================================

        if(
            $estado == 0 &&
            $estadoAnterior == 1
        ){

            $mensagem =
                "Evento {$nomeEvento} normalizado";

            error_log(
                $mensagem
            );

        }


        // =================================================
        // ATUALIZA ESTADO ANTERIOR
        // =================================================

        if($estadoAnterior != $estado){

            $stmt = $conn->prepare("

                UPDATE sensores_evento

                SET estado_anterior = ?

                WHERE id = ?

            ");

            if($stmt){

                $stmt->bind_param(
                    "ii",
                    $estado,
                    $idEvento
                );

                $stmt->execute();

                $stmt->close();

            }

        }

    }

}

?>