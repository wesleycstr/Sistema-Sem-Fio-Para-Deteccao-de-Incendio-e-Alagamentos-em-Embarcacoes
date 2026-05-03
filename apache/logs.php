<?php

function registrarLog($conn, $evento){

    $evento = $conn->real_escape_string($evento);

    $sql = "

    INSERT INTO logs
    (evento)

    VALUES

    ('$evento')

    ";

    $conn->query($sql);

}
?>
