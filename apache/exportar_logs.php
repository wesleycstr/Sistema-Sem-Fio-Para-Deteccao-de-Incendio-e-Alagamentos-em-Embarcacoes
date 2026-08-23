<?php

include 'conexao.php';


/*
=========================================================
CONSULTA DOS LOGS
=========================================================
*/

$sql = "

SELECT

    /* =================================================
       DADOS DO LOG
    ================================================= */

    logs.id AS log_id,

    logs.data_hora,

    logs.id_sensor,

    logs.tipo_sensor,

    logs.evento,

    logs.ocorrencia,


    /* =================================================
       SENSOR AMBIENTAL
    ================================================= */

    sensores.device_token AS token_ambiental,

    sensores.localizacao AS localizacao_ambiental,

    sensores.temperatura_max,

    sensores.umidade_min,

    sensores.umidade_max,

    sensores.gas_max,

    sensores.alarme_sonoro AS alarme_sonoro_ambiental,

    sensores.executar_script AS executar_script_ambiental,

    sensores.script_alarme AS script_alarme_ambiental,


    /* =================================================
       SENSOR DE EVENTO
    ================================================= */

    sensores_evento.device_token AS token_evento,

    sensores_evento.canal,

    sensores_evento.localizacao AS localizacao_evento,

    sensores_evento.nome_evento,

    sensores_evento.nivel_evento,

    sensores_evento.estado AS estado_evento,

    sensores_evento.alarme_sonoro AS alarme_sonoro_evento,

    sensores_evento.executar_script AS executar_script_evento,

    sensores_evento.script_alarme AS script_alarme_evento


FROM logs


/* =====================================================
   SENSOR AMBIENTAL
===================================================== */

LEFT JOIN sensores

ON sensores.id = logs.id_sensor

AND logs.tipo_sensor = 'ambiental'


/* =====================================================
   SENSOR DE EVENTO
===================================================== */

LEFT JOIN sensores_evento

ON sensores_evento.id = logs.id_sensor

AND logs.tipo_sensor = 'evento'


ORDER BY logs.data_hora DESC

";


$result = $conn->query($sql);


if(!$result){

    die(
        "Erro ao consultar os logs: " .
        $conn->error
    );

}


/*
=========================================================
CABEÇALHOS DO DOWNLOAD
=========================================================
*/

header(
    'Content-Type: text/csv; charset=UTF-8'
);


header(
    'Content-Disposition: attachment; filename="logs_atalaia.csv"'
);


header('Pragma: no-cache');

header('Expires: 0');


/*
=========================================================
BOM UTF-8
=========================================================
*/

echo "\xEF\xBB\xBF";


/*
=========================================================
ABRE SAÍDA CSV
=========================================================
*/

$output = fopen(
    'php://output',
    'w'
);


/*
=========================================================
CABEÇALHO DO CSV
=========================================================
*/

fputcsv(

    $output,

    [

        'ID Log',

        'Data/Hora',

        'Tipo do Sensor',

        'ID Sensor',

        'Device Token',

        'Localização',

        'Canal',

        'Nome do Evento',

        'Nível do Evento',

        'Estado do Evento',

        'Temperatura Máxima',

        'Umidade Mínima',

        'Umidade Máxima',

        'Gás Máximo',

        'Alarme Sonoro',

        'Execução de Script',

        'Caminho do Script',

        'Evento',

        'Ocorrência'

    ],

    ';'

);


/*
=========================================================
PROCESSA OS REGISTROS
=========================================================
*/

while($row = $result->fetch_assoc()){


    /*
    =====================================================
    VARIÁVEIS PADRÃO
    =====================================================
    */

    $deviceToken = '';

    $localizacao = '';

    $canal = '';

    $nomeEvento = '';

    $nivelEvento = '';

    $estadoEvento = '';

    $temperaturaMax = '';

    $umidadeMin = '';

    $umidadeMax = '';

    $gasMax = '';

    $alarmeSonoro = '';

    $executarScript = '';

    $scriptAlarme = '';


    /*
    =====================================================
    SENSOR AMBIENTAL
    =====================================================
    */

    if(
        $row['tipo_sensor'] === 'ambiental'
    ){

        $deviceToken =
            $row['token_ambiental'] ?? '';


        $localizacao =
            $row['localizacao_ambiental'] ?? '';


        $temperaturaMax =
            $row['temperatura_max'] ?? '';


        $umidadeMin =
            $row['umidade_min'] ?? '';


        $umidadeMax =
            $row['umidade_max'] ?? '';


        $gasMax =
            $row['gas_max'] ?? '';


        /*
        ================================================
        ALARME SONORO
        ================================================
        */

        if(
            intval(
                $row['alarme_sonoro_ambiental']
            ) == 1
        ){

            $alarmeSonoro =
                'ATIVADO';

        }else{

            $alarmeSonoro =
                'DESATIVADO';

        }


        /*
        ================================================
        SCRIPT
        ================================================
        */

        if(
            intval(
                $row['executar_script_ambiental']
            ) == 1
        ){

            $executarScript =
                'ATIVADO';

        }else{

            $executarScript =
                'DESATIVADO';

        }


        $scriptAlarme =
            $row['script_alarme_ambiental'] ?? '';

    }


    /*
    =====================================================
    SENSOR DE EVENTO
    =====================================================
    */

    if(
        $row['tipo_sensor'] === 'evento'
    ){

        $deviceToken =
            $row['token_evento'] ?? '';


        $localizacao =
            $row['localizacao_evento'] ?? '';


        $canal =
            $row['canal'] ?? '';


        $nomeEvento =
            $row['nome_evento'] ?? '';


        $nivelEvento =
            $row['nivel_evento'] ?? '';


        /*
        ================================================
        ESTADO DO EVENTO
        ================================================
        */

        if(
            $row['estado_evento'] !== null
        ){

            if(
                intval(
                    $row['estado_evento']
                ) == 1
            ){

                $estadoEvento =
                    'ATIVO';

            }else{

                $estadoEvento =
                    'NORMAL';

            }

        }


        /*
        ================================================
        ALARME SONORO
        ================================================
        */

        if(
            intval(
                $row['alarme_sonoro_evento']
            ) == 1
        ){

            $alarmeSonoro =
                'ATIVADO';

        }else{

            $alarmeSonoro =
                'DESATIVADO';

        }


        /*
        ================================================
        SCRIPT
        ================================================
        */

        if(
            intval(
                $row['executar_script_evento']
            ) == 1
        ){

            $executarScript =
                'ATIVADO';

        }else{

            $executarScript =
                'DESATIVADO';

        }


        $scriptAlarme =
            $row['script_alarme_evento'] ?? '';

    }


    /*
    =====================================================
    ESCREVE A LINHA NO CSV
    =====================================================
    */

    fputcsv(

        $output,

        [

            /* ID LOG */

            $row['log_id'],


            /* DATA/HORA */

            $row['data_hora'],


            /* TIPO */

            $row['tipo_sensor'],


            /* ID SENSOR */

            $row['id_sensor'],


            /* TOKEN */

            $deviceToken,


            /* LOCALIZAÇÃO */

            $localizacao,


            /* CANAL */

            $canal,


            /* NOME EVENTO */

            $nomeEvento,


            /* NÍVEL */

            $nivelEvento,


            /* ESTADO */

            $estadoEvento,


            /* TEMPERATURA */

            $temperaturaMax,


            /* UMIDADE MÍNIMA */

            $umidadeMin,


            /* UMIDADE MÁXIMA */

            $umidadeMax,


            /* GÁS */

            $gasMax,


            /* ALARME */

            $alarmeSonoro,


            /* SCRIPT */

            $executarScript,


            /* CAMINHO */

            $scriptAlarme,


            /* EVENTO */

            $row['evento'],


            /* OCORRÊNCIA */

            $row['ocorrencia']

        ],

        ';'

    );

}


/*
=========================================================
FINALIZA
=========================================================
*/

fclose($output);

exit;

?>