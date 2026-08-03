<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

include "conexao.php";

// ======================================================
// CABEÇALHOS
// ======================================================

$deviceToken = $_SERVER['HTTP_X_DEVICE_TOKEN'] ?? '';
$timestamp   = $_SERVER['HTTP_X_TIMESTAMP'] ?? '';
$signature   = $_SERVER['HTTP_X_SIGNATURE'] ?? '';


if(
    empty($deviceToken) ||
    empty($timestamp) ||
    empty($signature)
){
    http_response_code(401);

    die(json_encode([
        "erro"=>"Cabeçalhos inválidos."
    ]));
}

// ======================================================
// BUSCA SENSOR
// ======================================================

$stmt = $conn->prepare("

SELECT

id,
chave_secreta

FROM sensores_evento

WHERE device_token=?

LIMIT 1

");

$stmt->bind_param("s",$deviceToken);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows==0){

    http_response_code(401);

    die(json_encode([
        "erro"=>"Sensor nao encontrado."
    ]));
}

$sensor = $result->fetch_assoc();

$idSensor = $sensor["id"];

$secretKey = $sensor["chave_secreta"];

// ======================================================
// LÊ JSON
// ======================================================

$payload = file_get_contents("php://input");

$dados = json_decode($payload,true);

if(!$dados){

    http_response_code(400);

    die(json_encode([
        "erro"=>"JSON invalido."
    ]));
}

// ======================================================
// VALIDA TIMESTAMP
// ======================================================

if(abs(time()-intval($timestamp)) > 60){

    http_response_code(401);

    die(json_encode([
        "erro"=>"Timestamp expirado."
    ]));
}

// ======================================================
// VALIDA ASSINATURA
// ======================================================

$hash = sha1(

    $payload .

    $timestamp .

    $secretKey

);

if($hash != $signature){

    http_response_code(401);

    die(json_encode([
        "erro"=>"Assinatura invalida."
    ]));
}

// ======================================================
// VERIFICA EVENTOS
// ======================================================

if(!isset($dados["eventos"])){

    http_response_code(400);

    die(json_encode([
        "erro"=>"Nenhum evento recebido."
    ]));
}

// ======================================================
// PROCESSA EVENTOS
// ======================================================

foreach($dados["eventos"] as $evento){

    $canal = intval($evento["canal"]);

    $estado = intval($evento["estado"]);

    $stmt = $conn->prepare("

        UPDATE sensores_evento

        SET
            estado = ?,
            data_hora = NOW()

        WHERE
            device_token = ?
            AND canal = ?

    ");

    $stmt->bind_param(

        "isi",

        $estado,
        $deviceToken,
        $canal

    );

    $stmt->execute();

}

// ======================================================
// RESPOSTA
// ======================================================

echo json_encode([

    "status"=>"OK",

    "sensor"=>$idSensor,

    "eventos"=>count($dados["eventos"])

]);

?>