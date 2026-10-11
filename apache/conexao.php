<?php
//date_default_timezone_set('America/Sao_Paulo');
date_default_timezone_set('UTC');

$host = "mariadb";
$user = "root";
$password = "senha";
$database = "atalaia";

// Criar conexão
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}else{
   // echo "Conexão estabelecida";
}

// Definir charset (importante para acentos)
$conn->set_charset("utf8mb4");
?>
