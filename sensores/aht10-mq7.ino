#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClientSecureBearSSL.h>
#include <Adafruit_AHTX0.h>
#include <ArduinoJson.h>
#include <Hash.h>
#include <time.h>

// =====================================================
// CONFIGURAÇÕES WIFI
// =====================================================

const char* ssid = "Rede_IoT";
const char* password = "Sly@11011984";

// =====================================================
// SERVIDOR HTTPS
// =====================================================

const char* serverUrl =
  "https://atalaia/api/sensores.php";

// =====================================================
// AUTENTICAÇÃO DO SENSOR
// =====================================================

const char* DEVICE_TOKEN = "sensor_verde";
const char* SECRET_KEY = "eAvwoPBH254y";

// =====================================================
// CERTIFICADO DA CA ATALAIA
// =====================================================

static const char ATALAIA_ROOT_CA[] PROGMEM = R"EOF(
-----BEGIN CERTIFICATE-----
MIIFrTCCA5WgAwIBAgIUFSd0JmlWPaHB6CdCYkFMzZUtU8YwDQYJKoZIhvcNAQEL
BQAwZjELMAkGA1UEBhMCQlIxCzAJBgNVBAgMAlJKMRAwDgYDVQQHDAdOaXRlcm9p
MRAwDgYDVQQKDAdBVEFMQUlBMQwwCgYDVQQLDANJb1QxGDAWBgNVBAMMD0FUQUxB
SUEgUm9vdCBDQTAeFw0yNjEwMDYyMjAzNDNaFw0zNjEwMDMyMjAzNDNaMGYxCzAJ
BgNVBAYTAkJSMQswCQYDVQQIDAJSSjEQMA4GA1UEBwwHTml0ZXJvaTEQMA4GA1UE
CgwHQVRBTEFJQTEMMAoGA1UECwwDSW9UMRgwFgYDVQQDDA9BVEFMQUlBIFJvb3Qg
Q0EwggIiMA0GCSqGSIb3DQEBAQUAA4ICDwAwggIKAoICAQD1I6cD8SEtNsx7gs3G
FJbHETCQjPqMzE/dS5BwcsKeeyKsSO0sg/8vNxw7PRAavkalQ+FPW8t56UVY/l9S
VVYVll8Z1G4daqgQYNCGb+hTzU/QUquUFZD4XEWCsejoJRTBxbM3cl9LhSU7Llr2
JiV/iU+EetCCDSZfkRNmztYKKRuB/rbCgiK2/GhkCRXX7istPZqj8nukxFkKCde3
al836aZuwB7OKZBXxiuAAbLKSpabdHz8Y7tV2VcPAp7XhsF4ebFHNs2193w8gqpF
gElkWZVZuywpVtvPGJ4dWfCwb2iB2YbFfo3IqpQSSpqdTd6wbcjECCfkbaI2YCYO
ErOdhQErlnqj8gL8ZZKuM0XgSVL7LUzciip/YQp0N6fCtgzF/+HxHwf0i0Mtp7Ej
EMEm36PIIE7PYzPjnneq2F46eFo0rx6zxBpuxcdU0aUahg8ekpNhFbfLIXQZ7TOK
LMncGJuJrVXCuFMwPpqA69uJNHG7+t6T8ZF5nmRWCyhfzHFeexVAlMLt1lIpGLJG
HxhMjSUe8+l+A+AF9vhsfIT1RQKji5VaMUNbpJ2URhzhS2/z+fbVOxp8nlA8IkKF
wogvnMAvOj1b2Bx4MvAhWIa1Mx9MIEZwLYEQNp+8D/T4y8HmOIYKttW5TH5LUBpy
L8Ke09btk95qKhGIM2WtmS3uCwIDAQABo1MwUTAdBgNVHQ4EFgQU3LnVA0m7m6Xn
t4PS6s0l11T4apUwHwYDVR0jBBgwFoAU3LnVA0m7m6Xnt4PS6s0l11T4apUwDwYD
VR0TAQH/BAUwAwEB/zANBgkqhkiG9w0BAQsFAAOCAgEAzLOo2CxoeLnb8PDiOUwo
8N/Wfr67ea8nNpx/eik5aMrZcwtrEWPBZJIlqhtDpYgASfEXcGRpgVubtH3IWsD1
n6sjjAtZ/9Ti+3Qzl6hFGm5DENSlhpsk5tiopeOHU8bhW5ECAxJs+AUCJKC5fZrB
+ndlhL9wUF2b1PA0NtSHzDsXOo8+CMknVc0feBfxAURdLsaIstKzXXwdJ/195EKW
RZqDPUr5IsM+zApWVMmuze8Rfr1zfg5D7XIZYcdXQErdHiDpRgCPyxk+SHCFWhmQ
8SCzk8QOdfBeDSVuFyxgPGxl9Eb5mPiWBJbkpr12dyGc0HEFg/n3vgbnKQEoo32c
kpcMbO1WnaI7KPlO9wh5f4caRD28qniWiyXCerET3dJ3m6LlXoWzPi88nLZejO3G
qeo/QqcBFRXJWSeTyo9nlpJTVLiU5PcqqSnDVmwuXJU+8pUtKZMAmiFH8cXIfMFE
WfJHurarVEIl7EusCVg8CWDs8LDTscP3m1L/Sxbw1JhwB0YdWBDVNJNuwGz8pNH3
fCQjNLzOgR+EXUjk357/hiBf6cKCyL8XAKGLa1+NDlJaQBtw3ddvXrw0PTcAcr2P
ZNEfzdckMNVPJM8ZU5xOXStZFRjS3PA5DK//zR/WJyjvJ/SiSa8xAgpl8hHjcn8E
fd2TYys7rHIeSBB9y9ALU5Q=
-----END CERTIFICATE-----
)EOF";

// O objeto CA precisa permanecer vivo enquanto o cliente
// BearSSL estiver sendo utilizado.
static BearSSL::X509List atalaiaCA(ATALAIA_ROOT_CA);

// =====================================================
// PINOS
// =====================================================

const int ledPin = LED_BUILTIN;
const int externalLedPin = D3;
const int mq7Pin = A0;

// =====================================================
// MQ7
// =====================================================

float RL = 10000.0;
float R0 = 10587.52;

// =====================================================
// SENSOR AHT
// =====================================================

Adafruit_AHTX0 aht;

Adafruit_Sensor* aht_humidity = nullptr;
Adafruit_Sensor* aht_temp = nullptr;

bool aht_ok = false;

// =====================================================
// NTP
// =====================================================

const char* ntpServer = "pool.ntp.org";
const long gmtOffset_sec = -3 * 3600;
const int daylightOffset_sec = 0;

// =====================================================
// FUNÇÃO WIFI
// =====================================================

void conectarWiFi() {

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  Serial.println("Conectando ao WiFi...");

  while (WiFi.status() != WL_CONNECTED) {

    digitalWrite(ledPin, LOW);
    digitalWrite(externalLedPin, HIGH);
    delay(300);

    digitalWrite(ledPin, HIGH);
    digitalWrite(externalLedPin, LOW);
    delay(300);

    Serial.print(".");
  }

  Serial.println();
  Serial.println("WiFi conectado!");

  Serial.print("IP: ");
  Serial.println(WiFi.localIP());

  digitalWrite(ledPin, HIGH);
  digitalWrite(externalLedPin, LOW);

  IPAddress ipServidor;

if (WiFi.hostByName("atalaia", ipServidor)) {
    Serial.print("IP resolvido para atalaia: ");
    Serial.println(ipServidor);
} else {
    Serial.println("Falha ao resolver o nome atalaia");
}
}



// =====================================================
// NTP
// =====================================================


void iniciarNTP() {

  configTime(
    gmtOffset_sec,
    daylightOffset_sec,
    ntpServer
  );

  Serial.println("Sincronizando horario NTP...");

  struct tm timeinfo;

  while (!getLocalTime(&timeinfo)) {
    Serial.println("Aguardando NTP...");
    delay(1000);
  }

  Serial.println("Horario sincronizado!");

  char dataHora[25];

  strftime(
    dataHora,
    sizeof(dataHora),
    "%d/%m/%Y %H:%M:%S",
    &timeinfo
  );

  Serial.print("Data/hora: ");
  Serial.println(dataHora);
}


// =====================================================
// SHA-1
// Mantido compatível com a API atual
// =====================================================

String gerarHash(
  String payload,
  String timestamp
) {

  String dados =
    payload +
    timestamp +
    SECRET_KEY;

  return sha1(dados);
}

// =====================================================
// AHT
// =====================================================

void iniciarAHT() {

  int tentativas = 0;

  while (!aht_ok && tentativas < 5) {

    Serial.println("Iniciando AHT...");

    if (aht.begin()) {

      aht_ok = true;

      aht_temp =
        aht.getTemperatureSensor();

      aht_humidity =
        aht.getHumiditySensor();

      Serial.println("AHT iniciado!");

    } else {

      Serial.println("Falha no AHT");

      tentativas++;

      delay(2000);
    }
  }

  if (!aht_ok) {
    Serial.println("Continuando sem AHT");
  }
}

// =====================================================
// LEITURA MQ7
// =====================================================

float lerMQ7() {

  int mq7Value = analogRead(mq7Pin);

  if (mq7Value == 0)
    mq7Value = 1;

  float Vout =
    mq7Value * (3.3 / 1023.0);

  float Rs =
    RL * ((3.3 / Vout) - 1);

  float ratio =
    Rs / R0;

  float gasConcentration =
    99.042 * pow(ratio, -1.518);

  Serial.print("MQ7 ADC: ");
  Serial.println(mq7Value);

  Serial.print("CO ppm: ");
  Serial.println(gasConcentration);

  return gasConcentration;
}

// =====================================================
// ENVIO HTTPS SEGURO
// =====================================================

void enviarDados(
  float temperatura,
  float umidade,
  float gas
) {

  if (WiFi.status() != WL_CONNECTED) {

    Serial.println("WiFi desconectado!");
    conectarWiFi();
  }

  // ===================================================
  // CLIENTE TLS
  // ===================================================

  BearSSL::WiFiClientSecure client;

  // Valida o certificado do servidor contra a
  // ATALAIA Root CA.
  //
  // NÃO usar setInsecure().
  client.setTrustAnchors(&atalaiaCA);

  client.setBufferSizes(1024, 1024);

  HTTPClient https;

  // ===================================================
  // JSON
  // ===================================================

  DynamicJsonDocument doc(512);

  // A API verifica que o campo "sensor" seja igual
  // ao X-DEVICE-TOKEN.
  doc["sensor"] = DEVICE_TOKEN;

  doc["temperatura"] = temperatura;
  doc["umidade"] = umidade;
  doc["gas"] = gas;

  doc["ip"] =
    WiFi.localIP().toString();

  doc["heap"] =
    ESP.getFreeHeap();

  time_t now =
    time(nullptr);

  doc["timestamp"] =
    now;

  String payload;

  serializeJson(
    doc,
    payload
  );

  // ===================================================
  // ASSINATURA
  // ===================================================

  String timestamp =
    String(now);

  String assinatura =
    gerarHash(
      payload,
      timestamp
    );

  // ===================================================
  // DEBUG
  // ===================================================

  Serial.println();
  Serial.println("================================");
  Serial.println("ENVIO HTTPS PARA O SERVIDOR");
  Serial.println("================================");

  Serial.print("Heap antes do TLS: ");
  Serial.println(ESP.getFreeHeap());

  Serial.println("Payload:");
  Serial.println(payload);

  Serial.println("Timestamp:");
  Serial.println(timestamp);

  Serial.println("Hash:");
  Serial.println(assinatura);

  // ===================================================
  // INICIA HTTPS
  // ===================================================

  if (!https.begin(client, serverUrl)) {

    Serial.println(
      "Falha ao iniciar HTTPS"
    );

    return;
  }

  https.setTimeout(15000);

  // ===================================================
  // HEADERS
  // ===================================================

  https.addHeader(
    "Content-Type",
    "application/json"
  );

  https.addHeader(
    "X-Device-Token",
    DEVICE_TOKEN
  );

  https.addHeader(
    "X-Timestamp",
    timestamp
  );

  https.addHeader(
    "X-Signature",
    assinatura
  );

  // ===================================================
  // POST
  // ===================================================

  int httpCode =
    https.POST(payload);

  // ===================================================
  // RESULTADO
  // ===================================================

  if (httpCode > 0) {

    Serial.print("HTTP Code: ");
    Serial.println(httpCode);

    String resposta =
      https.getString();

    Serial.println("Resposta:");
    Serial.println(resposta);

    if (httpCode == 200) {

      Serial.println(
        "HTTPS + autenticacao OK!"
      );

      digitalWrite(
        ledPin,
        LOW
      );

      digitalWrite(
        externalLedPin,
        HIGH
      );

      delay(200);

      digitalWrite(
        ledPin,
        HIGH
      );

      digitalWrite(
        externalLedPin,
        LOW
      );

    } else {

      Serial.println(
        "Servidor retornou erro."
      );
    }

  } else {

    Serial.print(
      "Erro HTTPS: "
    );

    Serial.println(
      https.errorToString(httpCode)
    );

    char sslError[128];

    client.getLastSSLError(
      sslError,
      sizeof(sslError)
    );

    Serial.print(
      "Erro BearSSL: "
    );

    Serial.println(
      sslError
    );

    // ===============================================
    // INDICAÇÃO DE ERRO
    // ===============================================

    for (int i = 0; i < 3; i++) {

      digitalWrite(
        ledPin,
        LOW
      );

      digitalWrite(
        externalLedPin,
        HIGH
      );

      delay(300);

      digitalWrite(
        ledPin,
        HIGH
      );

      digitalWrite(
        externalLedPin,
        LOW
      );

      delay(300);
    }
  }

  https.end();

  Serial.print(
    "Heap depois do HTTPS: "
  );

  Serial.println(
    ESP.getFreeHeap()
  );
}

// =====================================================
// SETUP
// =====================================================

void setup() {

  Serial.begin(115200);

  pinMode(
    ledPin,
    OUTPUT
  );

  pinMode(
    externalLedPin,
    OUTPUT
  );

  digitalWrite(
    ledPin,
    HIGH
  );

  digitalWrite(
    externalLedPin,
    LOW
  );

  conectarWiFi();

  // Deve ocorrer antes da primeira conexão HTTPS,
  // pois o BearSSL usa a hora para validar o certificado.
  iniciarNTP();

  iniciarAHT();
}

// =====================================================
// LOOP
// =====================================================

void loop() {

  ESP.wdtFeed();

  // ===============================================
  // RECONECTAR WIFI
  // ===============================================

  if (WiFi.status() != WL_CONNECTED) {

    Serial.println(
      "Reconectando WiFi..."
    );

    conectarWiFi();

    // Após reconectar, sincroniza novamente.
    iniciarNTP();
  }

  float temperatura = 0;
  float umidade = 0;

  // ===============================================
  // LEITURA AHT
  // ===============================================

  if (
    aht_ok &&
    aht_temp &&
    aht_humidity
  ) {

    sensors_event_t humidity;
    sensors_event_t temp;

    aht_humidity->getEvent(
      &humidity
    );

    aht_temp->getEvent(
      &temp
    );

    temperatura =
      temp.temperature;

    umidade =
      humidity.relative_humidity;

    Serial.print(
      "Temperatura: "
    );

    Serial.println(
      temperatura
    );

    Serial.print(
      "Umidade: "
    );

    Serial.println(
      umidade
    );

  } else {

    Serial.println(
      "AHT indisponivel"
    );

    if (aht.begin()) {

      Serial.println(
        "AHT recuperado!"
      );

      aht_ok = true;

      aht_temp =
        aht.getTemperatureSensor();

      aht_humidity =
        aht.getHumiditySensor();
    }
  }

  // ===============================================
  // LEITURA MQ7
  // ===============================================

  float gas =
    lerMQ7();

  // ===============================================
  // ENVIO
  // ===============================================

  enviarDados(
    temperatura,
    umidade,
    gas
  );

  // ===============================================
  // INTERVALO
  // ===============================================

  delay(5000);
}
