<?php

include 'conexao.php';

// ======================================================
// DADOS RECEBIDOS
// ======================================================

$tipoSensor =
$_POST['tipoSensor'] ?? 'ambiente';

$localizacao =
trim($_POST['localizacao'] ?? '');

$token =
trim($_POST['token'] ?? '');

$chave =
trim($_POST['chave'] ?? '');

$alarmeSonoro =
intval($_POST['alarmeSonoro'] ?? 0);

/* CAMPOS SENSOR AMBIENTE */

$tempMax =
floatval($_POST['tempMax'] ?? 40);

$umiMin =
floatval($_POST['umiMin'] ?? 20);

$umiMax =
floatval($_POST['umiMax'] ?? 80);

$gasMax =
floatval($_POST['gasMax'] ?? 100);

/* CAMPOS SENSOR EVENTO */

$nomeEvento =
trim($_POST['nomeEvento'] ?? '');

$nivelEvento =
$_POST['nivelEvento'] ?? 'critico';

$canal = intval($_POST['canal'] ?? 0);

// ======================================================
// VALIDAÇÕES
// ======================================================

if($localizacao==""){

    die("Informe a localização.");

}

if($token==""){

    die("Informe o identificador.");

}

if($chave==""){

    die("Informe a chave secreta.");

}

// ======================================================
// SENSOR AMBIENTE
// ======================================================

if($tipoSensor=="ambiente"){

$stmt = $conn->prepare("

INSERT INTO sensores

(

localizacao,

chave_secreta,

device_token,

temperatura_max,

umidade_min,

umidade_max,

gas_max,

alarme_sonoro,

offline,

ordem,

ordem_cards

)

VALUES

(

?,?,?, ?,?,?,?, ?,0,0,0

)

");

$stmt->bind_param(

"sssddddi",

$localizacao,

$chave,

$token,

$tempMax,

$umiMin,

$umiMax,

$gasMax,

$alarmeSonoro

);

if($stmt->execute()){

$idSensor = $stmt->insert_id;

$conn->query("

INSERT INTO alarme

(id_sensor,status)

VALUES

($idSensor,1)

");

echo "Sensor de ambiente cadastrado com sucesso.";

}else{

echo "Erro ao cadastrar.<br>";

echo $stmt->error;

}

exit;

}

// ======================================================
// SENSOR DE EVENTO
// ======================================================

if($tipoSensor=="evento"){

if($nomeEvento==""){

die("Informe o nome do evento.");

}

$stmt = $conn->prepare("

INSERT INTO sensores_evento

(

device_token,

canal,

chave_secreta,

localizacao,

nome_evento,

nivel_evento,

alarme_sonoro,

offline,

ordem_eventos

)

VALUES

(

?,?,?,?,?,?,?,0,0

)

");

$stmt->bind_param(

"sissssi",

$token,

$canal,

$chave,

$localizacao,

$nomeEvento,

$nivelEvento,

$alarmeSonoro

);

if($stmt->execute()){

echo "Sensor de evento cadastrado com sucesso.";

}else{

echo "Erro ao cadastrar.<br>";

echo $stmt->error;

}

exit;

}

echo "Tipo de sensor inválido.";