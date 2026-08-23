<?php

function registrarLog(
    $conn,
    $idSensor,
    $evento,
    $ocorrencia = '',
    $tipoSensor = 'ambiental'
){

    $stmt = $conn->prepare("

        INSERT INTO logs
        (
            data_hora,
            id_sensor,
            tipo_sensor,
            evento,
            ocorrencia
        )

        VALUES
        (
            NOW(),
            ?,
            ?,
            ?,
            ?
        )

    ");

    if(!$stmt){

        return false;

    }

    $stmt->bind_param(
        "isss",
        $idSensor,
        $tipoSensor,
        $evento,
        $ocorrencia
    );

    return $stmt->execute();

}