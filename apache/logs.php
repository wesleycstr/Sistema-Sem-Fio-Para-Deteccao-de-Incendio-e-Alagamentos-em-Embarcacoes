<?php

function registrarLog($conn, $idSensor, $evento){

    $evento = $conn->real_escape_string($evento);

    $sql = "

    INSERT INTO logs
    (id_sensor, evento)

    VALUES

    ('$idSensor', '$evento')

    ";

    $conn->query($sql);

}
