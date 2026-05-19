```php
<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

// =====================================================
// CONEXÃO
// =====================================================

include '../conexao.php';

// =====================================================
// HEADERS
// =====================================================

$deviceToken =

$_SERVER['HTTP_X_DEVICE_TOKEN']
?? '';

$timestamp =

$_SERVER['HTTP_X_TIMESTAMP']
?? '';

$assinatura =

$_SERVER['HTTP_X_SIGNATURE']
?? '';

// =====================================================
// VALIDAR HEADERS
// =====================================================

if(
    empty($deviceToken)
    ||
    empty($timestamp)
    ||
    empty($assinatura)
){

    http_response_code(401);

    echo json_encode([

        "status" => false,
        "erro" => "Headers ausentes"

    ]);

    exit;
}

// =====================================================
// ANTI-REPLAY
// =====================================================

$agora = time();

if(

    abs($agora - (int)$timestamp)

    > 30

){

    http_response_code(401);

    echo json_encode([

        "status" => false,
        "erro" => "Pacote expirado"

    ]);

    exit;
}

// =====================================================
// BUSCAR SENSOR
// =====================================================

$stmt = $conn->prepare("

SELECT

    id,
    nome,
    chave_secreta

FROM sensores

WHERE device_token = ?

LIMIT 1

");

$stmt->bind_param(
    "s",
    $deviceToken
);

$stmt->execute();

$result =
$stmt->get_result();

// =====================================================
// SENSOR EXISTE?
// =====================================================

if($result->num_rows == 0){

    http_response_code(401);

    echo json_encode([

        "status" => false,
        "erro" => "Sensor inválido"

    ]);

    exit;
}

$sensor =
$result->fetch_assoc();

$idSensor =
$sensor['id'];

$secretKey =
$sensor['chave_secreta'];

// =====================================================
// PAYLOAD
// =====================================================

$payload =
file_get_contents("php://input");

if(empty($payload)){

    http_response_code(400);

    echo json_encode([

        "status" => false,
        "erro" => "Payload vazio"

    ]);

    exit;
}

// =====================================================
// VALIDAR HMAC
// =====================================================

$hashServidor = sha1(

    $payload .
    $timestamp .
    $secretKey

);

// =====================================================
// COMPARAÇÃO SEGURA
// =====================================================

if(

    !hash_equals(
        $hashServidor,
        $assinatura
    )

){

    http_response_code(401);

    echo json_encode([

        "status" => false,
        "erro" => "Assinatura inválida"

    ]);

    exit;
}

// =====================================================
// JSON
// =====================================================

$data =
json_decode($payload, true);

// =====================================================
// VALIDAR JSON
// =====================================================

if(!$data){

    http_response_code(400);

    echo json_encode([

        "status" => false,
        "erro" => "JSON inválido"

    ]);

    exit;
}

// =====================================================
// CAMPOS
// =====================================================

$temperatura =
$data['temperatura'] ?? 0;

$umidade =
$data['umidade'] ?? 0;

$gas =
$data['gas'] ?? 0;

$ip =
$data['ip'] ?? '';

$heap =
$data['heap'] ?? 0;

// =====================================================
// VALIDAÇÕES BÁSICAS
// =====================================================

if(
    !is_numeric($temperatura)
    ||
    !is_numeric($umidade)
    ||
    !is_numeric($gas)
){

    http_response_code(400);

    echo json_encode([

        "status" => false,
        "erro" => "Dados inválidos"

    ]);

    exit;
}

// =====================================================
// INSERIR DADOS
// =====================================================

$stmt = $conn->prepare("

INSERT INTO dados

(

    id_sensor,
    temperatura,
    umidade,
    gas_co

)

VALUES

(

    ?, ?, ?, ?

)

");

$stmt->bind_param(

    "iddd",

    $idSensor,
    $temperatura,
    $umidade,
    $gas

);

// =====================================================
// EXECUTAR
// =====================================================

if($stmt->execute()){

    echo json_encode([

        "status" => true,
        "mensagem" => "Dados recebidos",

        "sensor" => $deviceToken,

        "temperatura" => $temperatura,

        "umidade" => $umidade,

        "gas" => $gas,

        "ip" => $ip,

        "heap" => $heap

    ]);

}else{

    http_response_code(500);

    echo json_encode([

        "status" => false,
        "erro" => "Erro banco de dados"

    ]);

}
?>
```
