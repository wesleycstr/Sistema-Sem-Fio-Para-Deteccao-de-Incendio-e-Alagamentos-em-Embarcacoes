<?php
$host = "172.22.0.3";
$user = "root";
$password = "senha";
$database = "siscav";

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
