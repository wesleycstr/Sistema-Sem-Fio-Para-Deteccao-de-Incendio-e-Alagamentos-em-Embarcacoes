<?php

include 'conexao.php';

$nome = $_POST['nome'] ?? '';

$localizacao =
$_POST['localizacao'] ?? '';

$token =
$_POST['token'] ?? '';

$chave =
$_POST['chave'] ?? '';

$tempMax =
$_POST['tempMax'] ?? 40;

$umiMin =
$_POST['umiMin'] ?? 20;

$umiMax =
$_POST['umiMax'] ?? 80;

$gasMax =
$_POST['gasMax'] ?? 100;

$alarmeSonoro =
$_POST['alarmeSonoro'] ?? 0;

/* validações */

if(empty($nome)){

    die("Nome obrigatório");

}

if(empty($localizacao)){

    die("Localização obrigatória");

}

if(empty($token)){

    die("Token obrigatório");

}

if(empty($chave)){

    die("Chave obrigatória");

}

/* insert */

$stmt = $conn->prepare("

INSERT INTO sensores

(

    nome,
    localizacao,
    chave_secreta,
    device_token,
    temperatura_max,
    umidade_min,
    umidade_max,
    gas_max,
    alarme_sonoro

)

VALUES

(

    ?, ?, ?, ?, ?, ?, ?, ?, ?

)

");

$stmt->bind_param(

    "ssssddddi",

    $nome,
    $localizacao,
    $token,
    $chave,
    $tempMax,
    $umiMin,
    $umiMax,
    $gasMax,
    $alarmeSonoro

);

if($stmt->execute()){

    /* cria status inicial */

    $idSensor =
    $stmt->insert_id;

    $conn->query("

    INSERT INTO alarme
    (id_sensor, status)

    VALUES
    ('$idSensor', 1)

    ");

    echo "Sensor cadastrado com sucesso";

}else{

    echo "Erro ao cadastrar";

}
