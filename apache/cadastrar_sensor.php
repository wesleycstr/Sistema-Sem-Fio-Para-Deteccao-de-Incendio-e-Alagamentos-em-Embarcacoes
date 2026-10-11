<?php

// ======================================================
// CONEXÃO
// ======================================================

include 'conexao.php';

header('Content-Type: text/plain; charset=UTF-8');

// ======================================================
// DADOS RECEBIDOS
// ======================================================

$tipoSensor = $_POST['tipoSensor'] ?? 'ambiente';

$localizacao = trim($_POST['localizacao'] ?? '');

$token = trim($_POST['token'] ?? '');

$chave = trim($_POST['chave'] ?? '');

$alarmeSonoro = intval($_POST['alarmeSonoro'] ?? 0);


// ======================================================
// CAMPOS SENSOR DE AMBIENTE
// ======================================================

$tempMax = floatval($_POST['tempMax'] ?? 40);

$umiMin = floatval($_POST['umiMin'] ?? 20);

$umiMax = floatval($_POST['umiMax'] ?? 80);

$gasMax = floatval($_POST['gasMax'] ?? 100);


// ======================================================
// CAMPOS SENSOR DE EVENTO
// ======================================================

$nomeEvento = trim($_POST['nomeEvento'] ?? '');

$nivelEvento = $_POST['nivelEvento'] ?? 'critico';

$canal = intval($_POST['canal'] ?? 0);


// ======================================================
// EXECUÇÃO DE SCRIPTS
// ======================================================

$executarScript = intval($_POST['executarScript'] ?? 0);

$scriptAlarme = trim($_POST['scriptAlarme'] ?? '');


// ======================================================
// VALIDAÇÃO DO TIPO DE SENSOR
// ======================================================

if ($tipoSensor !== 'ambiente' && $tipoSensor !== 'evento') {

    die("Tipo de sensor inválido.");

}


// ======================================================
// VALIDAÇÕES GERAIS
// ======================================================

if ($localizacao === '') {

    die("Informe a localização.");

}

if ($token === '') {

    die("Informe o identificador.");

}

if ($chave === '') {

    die("Informe a chave secreta.");

}


// ======================================================
// VALIDAÇÃO DO ALARME SONORO
// ======================================================

$alarmeSonoro = ($alarmeSonoro == 1) ? 1 : 0;


// ======================================================
// VALIDAÇÃO DA EXECUÇÃO DE SCRIPT
// ======================================================

$executarScript = ($executarScript == 1) ? 1 : 0;


// ======================================================
// SENSOR DE AMBIENTE
// ======================================================

if ($tipoSensor === 'ambiente') {

    // --------------------------------------------------
    // VALIDAÇÕES
    // --------------------------------------------------

    if ($tempMax < -50 || $tempMax > 100) {

        die("Temperatura máxima inválida.");

    }

    if ($umiMin < 0 || $umiMin > 100) {

        die("Umidade mínima inválida.");

    }

    if ($umiMax < 0 || $umiMax > 100) {

        die("Umidade máxima inválida.");

    }

    if ($umiMin > $umiMax) {

        die("A umidade mínima não pode ser maior que a umidade máxima.");

    }

    if ($gasMax < 0) {

        die("Limite máximo de CO inválido.");

    }


    // --------------------------------------------------
    // VERIFICA SE O TOKEN JÁ EXISTE
    // --------------------------------------------------

    $stmt = $conn->prepare("
        SELECT id
        FROM sensores
        WHERE device_token = ?
        LIMIT 1
    ");

    if (!$stmt) {

        die("Erro ao preparar consulta: " . $conn->error);

    }

    $stmt->bind_param("s", $token);

    $stmt->execute();

    $stmt->store_result();

    if ($stmt->num_rows > 0) {

        $stmt->close();

        die("Já existe um sensor cadastrado com este identificador.");

    }

    $stmt->close();


    // --------------------------------------------------
    // CADASTRA O SENSOR
    // --------------------------------------------------

    $stmt = $conn->prepare("
        INSERT INTO sensores
        (
            device_token,
            localizacao,
            chave_secreta,
            temperatura_max,
            umidade_min,
            umidade_max,
            gas_max,
            alarme_sonoro,
            executar_script,
            script_alarme,
            offline,
            ordem
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            0,
            0
        )
    ");

    if (!$stmt) {

        die("Erro ao preparar cadastro: " . $conn->error);

    }


    $stmt->bind_param(
        "sssddddiss",
        $token,
        $localizacao,
        $chave,
        $tempMax,
        $umiMin,
        $umiMax,
        $gasMax,
        $alarmeSonoro,
        $executarScript,
        $scriptAlarme
    );


    // --------------------------------------------------
    // EXECUTA CADASTRO
    // --------------------------------------------------

    if (!$stmt->execute()) {

        $erro = $stmt->error;

        $stmt->close();

        die("Erro ao cadastrar sensor: " . $erro);

    }


    // ID DO SENSOR
    $idSensor = $stmt->insert_id;

    $stmt->close();


    // --------------------------------------------------
    // CRIA REGISTRO INICIAL DO ALARME
    // --------------------------------------------------

    $stmtAlarme = $conn->prepare("
        INSERT INTO alarme
        (
            id_sensor,
            status,
            cor
        )
        VALUES
        (
            ?,
            1,
            '#2e7d32'
        )
    ");

    if (!$stmtAlarme) {

        die(
            "Sensor cadastrado, mas ocorreu um erro ao preparar o registro de alarme: "
            . $conn->error
        );

    }


    $stmtAlarme->bind_param("i", $idSensor);


    if (!$stmtAlarme->execute()) {

        $erro = $stmtAlarme->error;

        $stmtAlarme->close();

        die(
            "Sensor cadastrado, mas ocorreu um erro ao criar o estado inicial do alarme: "
            . $erro
        );

    }

    $stmtAlarme->close();


    // --------------------------------------------------
    // RESPOSTA
    // --------------------------------------------------

    echo "Sensor de ambiente cadastrado com sucesso.";

    exit;
}


// ======================================================
// SENSOR DE EVENTO
// ======================================================

if ($tipoSensor === 'evento') {


    // --------------------------------------------------
    // VALIDAÇÕES
    // --------------------------------------------------

    if ($nomeEvento === '') {

        die("Informe o nome do evento.");

    }


    $niveisValidos = [
        'informacao',
        'atencao',
        'critico'
    ];

    if (!in_array($nivelEvento, $niveisValidos, true)) {

        die("Nível do evento inválido.");

    }


    if ($canal < 0) {

        die("Canal inválido.");

    }


    // --------------------------------------------------
    // VERIFICA SE O TOKEN JÁ EXISTE
    // --------------------------------------------------

    /*
     * Um mesmo device_token não deve ser duplicado
     * dentro dos sensores de evento.
     */

    $stmt = $conn->prepare("
        SELECT id
        FROM sensores_evento
        WHERE device_token = ?
        LIMIT 1
    ");

    if (!$stmt) {

        die("Erro ao preparar consulta: " . $conn->error);

    }

    $stmt->bind_param("s", $token);

    $stmt->execute();

    $stmt->store_result();

    if ($stmt->num_rows > 0) {

        $stmt->close();

        die(
            "Já existe um evento cadastrado com este identificador."
        );

    }

    $stmt->close();


    // --------------------------------------------------
    // CADASTRA O SENSOR DE EVENTO
    // --------------------------------------------------

    $stmt = $conn->prepare("
        INSERT INTO sensores_evento
        (
            device_token,
            canal,
            localizacao,
            nome_evento,
            nivel_evento,
            estado,
            estado_anterior,
            data_hora,
            alarme_sonoro,
            executar_script,
            script_alarme,
            offline,
            ordem_eventos
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            0,
            0,
            NULL,
            ?,
            ?,
            ?,
            0,
            0
        )
    ");

    if (!$stmt) {

        die("Erro ao preparar cadastro do evento: " . $conn->error);

    }


    $stmt->bind_param(
        "sisssiss",
        $token,
        $canal,
        $localizacao,
        $nomeEvento,
        $nivelEvento,
        $alarmeSonoro,
        $executarScript,
        $scriptAlarme
    );


    // --------------------------------------------------
    // EXECUTA CADASTRO
    // --------------------------------------------------

    if (!$stmt->execute()) {

        $erro = $stmt->error;

        $stmt->close();

        die("Erro ao cadastrar sensor de evento: " . $erro);

    }

    $stmt->close();


    // --------------------------------------------------
    // RESPOSTA
    // --------------------------------------------------

    echo "Sensor de evento cadastrado com sucesso.";

    exit;
}


// ======================================================
// FINAL
// ======================================================

echo "Nenhum tipo de sensor foi processado.";
