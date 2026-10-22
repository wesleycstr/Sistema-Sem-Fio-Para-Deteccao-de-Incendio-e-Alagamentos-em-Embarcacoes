<?php

function registrarLog(
    $conn,
    $idSensor,
    $evento,
    $ocorrencia = '',
    $tipoSensor = 'ambiental'
) {
    if ($tipoSensor === 'evento') {
        $sql = "
            INSERT INTO logs (
                data_hora,
                id_sensor,
                id_sensor_evento,
                tipo_sensor,
                evento,
                ocorrencia
            ) VALUES (
                NOW(),
                NULL,
                ?,
                ?,
                ?,
                ?
            )
        ";
    } else {
        $sql = "
            INSERT INTO logs (
                data_hora,
                id_sensor,
                id_sensor_evento,
                tipo_sensor,
                evento,
                ocorrencia
            ) VALUES (
                NOW(),
                ?,
                NULL,
                ?,
                ?,
                ?
            )
        ";
    }

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log(
            "Erro ao preparar log: " . $conn->error
        );
        return false;
    }

    $stmt->bind_param(
        "isss",
        $idSensor,
        $tipoSensor,
        $evento,
        $ocorrencia
    );

    $resultado = $stmt->execute();

    if (!$resultado) {
        error_log(
            "Erro ao registrar log: " . $stmt->error
        );
    }

    $stmt->close();

    return $resultado;
}
