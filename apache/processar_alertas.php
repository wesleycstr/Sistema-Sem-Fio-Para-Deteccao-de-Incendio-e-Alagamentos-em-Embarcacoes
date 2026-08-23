<?php

/*
=========================================================
PROCESSAMENTO DOS ALERTAS
=========================================================
*/


function processarAlertas($conn)
{

    /*
    =====================================================
    PROCESSAMENTO DOS SENSORES AMBIENTAIS
    =====================================================
    */

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


    if(!$result){

        error_log(
            "Erro ao consultar sensores ambientais: " .
            $conn->error
        );

        return;

    }


    /*
    =====================================================
    PROCESSA CADA SENSOR AMBIENTAL
    =====================================================
    */

    while($row = $result->fetch_assoc()){


        /*
        ================================================
        STATUS NORMAL
        ================================================
        */

        $status = 1;

        $ocorrencias = [];


        $idSensor =
            intval($row['id']);


        /*
        ================================================
        TEMPERATURA
        ================================================
        */

        if(
            isset($row['temperatura']) &&
            $row['temperatura'] !== null &&
            $row['temperatura_max'] !== null &&
            $row['temperatura'] > $row['temperatura_max']
        ){

            $status = 3;


            $ocorrencias[] =
                "Temperatura acima do limite máximo (" .
                $row['temperatura'] .
                "°C > " .
                $row['temperatura_max'] .
                "°C)";

        }


        /*
        ================================================
        UMIDADE ABAIXO DO LIMITE
        ================================================
        */

        if(
            isset($row['umidade']) &&
            $row['umidade'] !== null &&
            $row['umidade_min'] !== null &&
            $row['umidade'] < $row['umidade_min']
        ){

            /*
            Se não houver uma condição crítica,
            o status será ATENÇÃO.
            */

            if($status < 3){

                $status = 2;

            }


            $ocorrencias[] =
                "Umidade abaixo do limite mínimo (" .
                $row['umidade'] .
                "% < " .
                $row['umidade_min'] .
                "%)";

        }


        /*
        ================================================
        UMIDADE ACIMA DO LIMITE
        ================================================
        */

        if(
            isset($row['umidade']) &&
            $row['umidade'] !== null &&
            $row['umidade_max'] !== null &&
            $row['umidade'] > $row['umidade_max']
        ){

            if($status < 3){

                $status = 2;

            }


            $ocorrencias[] =
                "Umidade acima do limite máximo (" .
                $row['umidade'] .
                "% > " .
                $row['umidade_max'] .
                "%)";

        }


        /*
        ================================================
        GÁS / CO
        ================================================
        */

        if(
            isset($row['gas_co']) &&
            $row['gas_co'] !== null &&
            $row['gas_max'] !== null &&
            $row['gas_co'] > $row['gas_max']
        ){

            /*
            Gás acima do limite é condição crítica.
            */

            $status = 3;


            $ocorrencias[] =
                "Concentração de CO acima do limite máximo (" .
                $row['gas_co'] .
                " ppm > " .
                $row['gas_max'] .
                " ppm)";

        }


        /*
        ================================================
        MONTA A OCORRÊNCIA
        ================================================
        */

        $ocorrencia = implode(
            " | ",
            $ocorrencias
        );


        /*
        ================================================
        STATUS ANTERIOR
        ================================================
        */

        $statusAnterior =
            isset($row['status_atual'])
            ? intval($row['status_atual'])
            : -1;


        /*
        ================================================
        VERIFICA SE O STATUS MUDOU
        ================================================
        */

        if($status != $statusAnterior){


            /*
            ============================================
            DESCRIÇÃO DO EVENTO
            ============================================
            */

            switch($status){

                case 1:

                    $evento =
                        "Sensor voltou ao NORMAL";


                    if(empty($ocorrencia)){

                        $ocorrencia =
                            "Temperatura, umidade e concentração de CO dentro dos limites";

                    }

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

                    break;

            }


            /*
            ============================================
            REGISTRA LOG
            ============================================
            */

            registrarLog(

                $conn,

                $idSensor,

                $evento,

                $ocorrencia,

                'ambiental'

            );


            /*
            ============================================
            EXECUTA SCRIPT DO ALARME AMBIENTAL
            ============================================
            */

            if(
                $status == 3 &&
                intval($row['executar_script']) == 1 &&
                !empty($row['script_alarme'])
            ){

                $script =
                    escapeshellarg(
                        $row['script_alarme']
                    );


                /*
                Mantém o comando que já funciona
                nos sensores ambientais.
                */

                $comando =
                    "php $script";


                error_log(
                    "Executando script do sensor ambiental " .
                    $idSensor .
                    ": " .
                    $row['script_alarme']
                );


                exec(

                    $comando .
                    " > /dev/null 2>&1 &"

                );

            }

        }


        /*
        ================================================
        ATUALIZA TABELA ALARME
        ================================================
        */

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


        if(!$conn->query($sqlUpdate)){

            error_log(

                "Erro ao atualizar alarme do sensor " .
                $idSensor .
                ": " .
                $conn->error

            );

        }

    }


    /*
    =====================================================
    PROCESSAMENTO DOS SENSORES DE EVENTO
    =====================================================
    */

    processarAlertasEventos($conn);

}



/*
=========================================================
PROCESSAMENTO DOS SENSORES DE EVENTO
=========================================================
*/

function processarAlertasEventos($conn)
{

    /*
    =====================================================
    BUSCA OS EVENTOS
    =====================================================
    */

    $sqlEventos = "

        SELECT

            id,
            device_token,
            canal,
            nome_evento,
            nivel_evento,
            estado,
            estado_anterior,
            alarme_sonoro,
            executar_script,
            script_alarme

        FROM sensores_evento

    ";


    $resultEventos =
        $conn->query($sqlEventos);


    if(!$resultEventos){

        error_log(

            "Erro ao consultar sensores_evento: " .
            $conn->error

        );

        return;

    }


    /*
    =====================================================
    PROCESSA CADA EVENTO
    =====================================================
    */

    while(
        $evento =
        $resultEventos->fetch_assoc()
    ){


        /*
        ================================================
        DADOS DO EVENTO
        ================================================
        */

        $idEvento =
            intval($evento['id']);


        $estadoAtual =
            intval($evento['estado']);


        $estadoAnterior =
            intval($evento['estado_anterior']);


        $alarmeSonoro =
            intval($evento['alarme_sonoro']);


        $executarScript =
            intval($evento['executar_script']);


        $nomeEvento =
            $evento['nome_evento'];


        $nivelEvento =
            $evento['nivel_evento'];


        $scriptAlarme =
            trim(
                $evento['script_alarme'] ?? ''
            );


        /*
        =================================================
        EVENTO ATIVADO
        =================================================
        */

        if(
            $estadoAtual == 1 &&
            $estadoAnterior == 0
        ){

            /*
            =============================================
            OCORRÊNCIA
            =============================================
            */

            $ocorrencia =
                "Evento '" .
                $nomeEvento .
                "' ativado no canal " .
                $evento['canal'];


            /*
            =============================================
            REGISTRA LOG
            =============================================
            */

            registrarLog(

                $conn,

                $idEvento,

                "Evento ativado",

                $ocorrencia,

                'evento'

            );


            /*
            =============================================
            EXECUÇÃO DO SCRIPT
            =============================================
            */

            if(
                $executarScript == 1 &&
                $scriptAlarme != ''
            ){

                $script =
                    escapeshellarg(
                        $scriptAlarme
                    );


                /*
                Mantém o mesmo comando
                utilizado nos sensores ambientais.
                */

                $comando =
                    "php $script";


                error_log(

                    "Executando script do evento " .
                    $idEvento .
                    ": " .
                    $scriptAlarme

                );


                exec(

                    $comando .
                    " > /dev/null 2>&1 &"

                );

            }
            else{

                error_log(

                    "Script NÃO executado para evento " .
                    $idEvento .
                    " | executar_script=" .
                    $executarScript .
                    " | script=" .
                    $scriptAlarme

                );

            }


            /*
            =============================================
            ALARME SONORO
            =============================================
            */

            if($alarmeSonoro == 1){

                error_log(

                    "Alarme sonoro habilitado para evento " .
                    $idEvento

                );

            }


            /*
            =============================================
            DIAGNÓSTICO
            =============================================
            */

            error_log(

                "Evento ativado: " .
                $idEvento .
                " - " .
                $nomeEvento

            );

        }


        /*
        =================================================
        EVENTO NORMALIZADO
        =================================================

        estado_anterior = 1
        estado          = 0
        */

        if(
            $estadoAtual == 0 &&
            $estadoAnterior == 1
        ){

            /*
            =============================================
            OCORRÊNCIA
            =============================================
            */

            $ocorrencia =
                "Evento '" .
                $nomeEvento .
                "' normalizado no canal " .
                $evento['canal'];


            /*
            =============================================
            REGISTRA LOG
            =============================================
            */

            registrarLog(

                $conn,

                $idEvento,

                "Evento normalizado",

                $ocorrencia,

                'evento'

            );


            error_log(

                "Evento normalizado: " .
                $idEvento .
                " - " .
                $nomeEvento

            );

        }


        /*
        =================================================
        ATUALIZA ESTADO ANTERIOR
        =================================================
        */

        if(
            $estadoAtual != $estadoAnterior
        ){

            $stmt = $conn->prepare("

                UPDATE sensores_evento

                SET estado_anterior = ?

                WHERE id = ?

            ");


            if(!$stmt){

                error_log(

                    "Erro ao preparar atualização de " .
                    "estado_anterior do evento " .
                    $idEvento .
                    ": " .
                    $conn->error

                );

                continue;

            }


            $stmt->bind_param(

                "ii",

                $estadoAtual,

                $idEvento

            );


            if(!$stmt->execute()){

                error_log(

                    "Erro ao atualizar estado_anterior " .
                    "do evento " .
                    $idEvento .
                    ": " .
                    $stmt->error

                );

            }


            $stmt->close();

        }

    }

}

?>