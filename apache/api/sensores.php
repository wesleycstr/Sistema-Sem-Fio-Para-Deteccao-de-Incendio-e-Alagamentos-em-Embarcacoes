<?php

// =====================================================
// CONFIGURAÇÕES
// =====================================================

header("Content-Type: application/json; charset=utf-8");

$host = "mariadb";
$user = "root";
$pass = "senha";
$db   = "atalaia";

// =====================================================
// CONEXÃO COM O BANCO
// =====================================================

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {

    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "erro" => "Erro ao conectar ao banco de dados"
    ]);

    exit;
}

$conn->set_charset("utf8mb4");

// =====================================================
// MÉTODO HTTP
// =====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        "ok" => false,
        "erro" => "Método não permitido"
    ]);

    exit;
}

// =====================================================
// HEADERS DE AUTENTICAÇÃO
// =====================================================

$token = $_SERVER['HTTP_X_DEVICE_TOKEN'] ?? '';
$timestamp = $_SERVER['HTTP_X_TIMESTAMP'] ?? '';
$signature = $_SERVER['HTTP_X_SIGNATURE'] ?? '';


// =====================================================
// VERIFICAÇÃO DOS HEADERS
// =====================================================

if ($token === '' || $timestamp === '' || $signature === '') {

    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "erro" => "Headers de autenticação ausentes"
    ]);

    exit;
}


// =====================================================
// VALIDAÇÃO DO TIMESTAMP
// =====================================================

if (!ctype_digit($timestamp)) {

    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "erro" => "Timestamp inválido"
    ]);

    exit;
}

$current_time = time();

$device_time = (int)$timestamp;


// Aceita diferença máxima de 30 segundos

if (abs($current_time - $device_time) > 30) {

    http_response_code(401);

    echo json_encode([
        "ok" => false,
        "erro" => "Timestamp expirado ou inválido"
    ]);

    exit;
}


// =====================================================
// LEITURA DO PAYLOAD JSON
// =====================================================

$payload = file_get_contents("php://input");

if ($payload === false || trim($payload) === '') {

    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "erro" => "Payload vazio"
    ]);

    exit;
}


// =====================================================
// DECODIFICAÇÃO DO JSON
// =====================================================

$data = json_decode($payload, true);

if (!is_array($data)) {

    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "erro" => "JSON inválido"
    ]);

    exit;
}


// =====================================================
// LOCALIZAÇÃO DO SENSOR PELO DEVICE TOKEN
// =====================================================
//
// O token enviado pelo ESP é utilizado para localizar
// o sensor na tabela "sensores".
//
// Dessa forma não precisamos manter uma lista de
// sensores diretamente neste arquivo PHP.
// =====================================================

$stmt = $conn->prepare(
    "SELECT
        id,
        device_token,
        chave_secreta
     FROM sensores
     WHERE device_token = ?
     LIMIT 1"
);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "erro" => "Erro ao preparar consulta do sensor"
    ]);

    exit;
}

$stmt->bind_param("s", $token);

$stmt->execute();

$result = $stmt->get_result();

$sensor_db = $result->fetch_assoc();

$stmt->close();


// =====================================================
// VERIFICAÇÃO DO SENSOR
// =====================================================

if (!$sensor_db) {

    http_response_code(403);

    echo json_encode([
        "ok" => false,
        "erro" => "Device Token inválido"
    ]);

    exit;
}


// =====================================================
// DADOS DO SENSOR
// =====================================================

$id_sensor = (int)$sensor_db['id'];

$secret = $sensor_db['chave_secreta'];


// =====================================================
// VERIFICAÇÃO DA ASSINATURA
// =====================================================
//
// ATENÇÃO:
//
// Esta é a mesma lógica utilizada atualmente pelo
// firmware:
//
// SHA1(payload + timestamp + chave_secreta)
//
// Não estamos usando HMAC-SHA256 nesta etapa para
// manter compatibilidade com o código atual do ESP.
// =====================================================

$local_signature = sha1(
    $payload .
    $timestamp .
    $secret
);


// =====================================================
// COMPARAÇÃO SEGURA DA ASSINATURA
// =====================================================

if (!hash_equals($local_signature, $signature)) {

    http_response_code(401);

    echo json_encode([
        "ok" => false,
        "erro" => "Assinatura inválida"
    ]);

    exit;
}


// =====================================================
// OBTENÇÃO DOS DADOS
// =====================================================

$sensor = $data['sensor'] ?? '';

$temperatura = $data['temperatura'] ?? null;

$umidade = $data['umidade'] ?? null;

$gas = $data['gas'] ?? null;


// =====================================================
// VERIFICAÇÃO DO CAMPO SENSOR
// =====================================================

if ($sensor === '') {

    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "erro" => "Campo sensor ausente"
    ]);

    exit;
}


// =====================================================
// VERIFICAÇÃO DO TOKEN X SENSOR
// =====================================================
//
// O campo "sensor" enviado dentro do JSON deve
// corresponder ao Device Token utilizado no header.
//
// Isso evita que um dispositivo autenticado tente
// enviar dados identificando-se como outro sensor.
// =====================================================

if ($sensor !== $token) {

    http_response_code(403);

    echo json_encode([
        "ok" => false,
        "erro" => "Sensor não corresponde ao Device Token"
    ]);

    exit;
}


// =====================================================
// VALIDAÇÃO DOS VALORES
// =====================================================

if (!is_numeric($temperatura)) {

    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "erro" => "Temperatura inválida"
    ]);

    exit;
}


if (!is_numeric($umidade)) {

    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "erro" => "Umidade inválida"
    ]);

    exit;
}


if (!is_numeric($gas)) {

    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "erro" => "Valor de gás inválido"
    ]);

    exit;
}


// Converte para números

$temperatura = (float)$temperatura;

$umidade = (float)$umidade;

$gas = (float)$gas;


// =====================================================
// LIMITES DE VALIDAÇÃO
// =====================================================

if ($temperatura < -50 || $temperatura > 100) {

    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "erro" => "Temperatura fora da faixa permitida"
    ]);

    exit;
}


if ($umidade < 0 || $umidade > 100) {

    http_response_code(400);

    echo json_encode([
        "ok" => false,
        "erro" => "Umidade fora da faixa permitida"
    ]);

    exit;
}


// =====================================================
// INSERÇÃO NO BANCO
// =====================================================
//
// IMPORTANTE:
//
// dados.id_sensor é INT.
//
// Portanto, não inserimos "sensor_azul" diretamente.
// Primeiro localizamos o ID numérico do sensor na
// tabela sensores e utilizamos esse ID.
// =====================================================

$stmt = $conn->prepare(
    "INSERT INTO dados
    (
        id_sensor,
        temperatura,
        umidade,
        gas_co
    )
    VALUES (?, ?, ?, ?)"
);


if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "erro" => "Erro ao preparar inserção dos dados"
    ]);

    exit;
}


// id_sensor = inteiro
// temperatura = double
// umidade = double
// gas = double

$stmt->bind_param(
    "iddd",
    $id_sensor,
    $temperatura,
    $umidade,
    $gas
);


// =====================================================
// EXECUÇÃO
// =====================================================

if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "erro" => "Erro ao inserir dados",
        "detalhe" => $stmt->error
    ]);

    $stmt->close();

    $conn->close();

    exit;
}


// =====================================================
// SUCESSO
// =====================================================

$stmt->close();

$conn->close();


http_response_code(200);

echo json_encode([
    "ok" => true,
    "mensagem" => "Dados recebidos com sucesso",
    "id_sensor" => $id_sensor,
    "sensor" => $sensor,
    "temperatura" => $temperatura,
    "umidade" => $umidade,
    "gas" => $gas
]);

?>
