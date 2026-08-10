<?php

include 'conexao.php';

$id =
intval($_POST['id'] ?? 0);

$canal =
intval($_POST['canal'] ?? 0);

$nomeEvento =
trim($_POST['nomeEvento'] ?? '');

$nivelEvento =
$_POST['nivelEvento'] ?? '';

$alarmeSonoro =
intval($_POST['alarmeSonoro'] ?? 0);

if($id <= 0){

    die("ID do evento inválido.");

}

if($canal <= 0){

    die("Canal inválido.");

}

if($nomeEvento == ""){

    die("Nome do evento obrigatório.");

}

if(
    $nivelEvento != "informacao" &&
    $nivelEvento != "atencao" &&
    $nivelEvento != "critico"
){

    die("Nível do evento inválido.");

}

$stmt = $conn->prepare("

    UPDATE sensores_evento

    SET

        canal = ?,
        nome_evento = ?,
        nivel_evento = ?,
        alarme_sonoro = ?

    WHERE id = ?

");

$stmt->bind_param(

    "issii",

    $canal,
    $nomeEvento,
    $nivelEvento,
    $alarmeSonoro,
    $id

);

if($stmt->execute()){

    echo "Evento alterado com sucesso.";

}else{

    echo "Erro ao alterar evento: " .
         $stmt->error;

}