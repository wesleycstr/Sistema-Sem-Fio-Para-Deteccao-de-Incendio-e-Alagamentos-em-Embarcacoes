<?php

include 'conexao.php';


// =====================================================
// DADOS RECEBIDOS
// =====================================================

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

$executarScript =
    intval($_POST['executarScript'] ?? 0);

$scriptAlarme =
    trim($_POST['scriptAlarme'] ?? '');


// =====================================================
// VALIDAÇÕES
// =====================================================

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


// =====================================================
// UPDATE
// =====================================================

$stmt = $conn->prepare("

    UPDATE sensores_evento

    SET

        canal = ?,
        nome_evento = ?,
        nivel_evento = ?,
        alarme_sonoro = ?,
        executar_script = ?,
        script_alarme = ?

    WHERE id = ?

");


if(!$stmt){

    die(
        "Erro ao preparar SQL: " .
        $conn->error
    );

}


$stmt->bind_param(
    "issiisi",
    $canal,
    $nomeEvento,
    $nivelEvento,
    $alarmeSonoro,
    $executarScript,
    $scriptAlarme,
    $id

);


if($stmt->execute()){

    echo "Evento alterado com sucesso.";

}else{

    echo
        "Erro ao alterar evento: " .
        $stmt->error;

}


$stmt->close();

?>