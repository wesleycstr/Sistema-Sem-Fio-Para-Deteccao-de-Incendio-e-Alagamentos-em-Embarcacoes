#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClientSecureBearSSL.h>
#include <ArduinoJson.h>
#include <Hash.h>
#include <time.h>

// =====================================================
// CONFIGURACOES WIFI
// =====================================================
// Preencha com as credenciais da rede usada pelo ESP8266.
const char* ssid = "Rede_IoT";
const char* password = "Sly@11011984";

// =====================================================
// SERVIDOR HTTPS ATALAIA
// =====================================================
// Presume que recebe_eventos.php esteja na raiz de /var/www/html.
// Se o arquivo estiver dentro de /api, altere para:
// https://atalaia/api/recebe_eventos.php
const char* serverUrl = "https://atalaia/recebe_eventos.php";

// =====================================================
// AUTENTICACAO
// =====================================================
// Estes valores precisam estar cadastrados no banco usado pelo endpoint.
const char* DEVICE_TOKEN = "Sensor_eventos";
const char* SECRET_KEY = "X9f2D8mQ4w";

// =====================================================
// CERTIFICADO DA AUTORIDADE CERTIFICADORA (CA)
// =====================================================
// Mantem a validacao TLS. Nao utilizar setInsecure().
static const char CA_CERT[] PROGMEM = R"EOF(
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

BearSSL::X509List atalaiaCA(CA_CERT);
BearSSL::WiFiClientSecure clienteHTTPS;
HTTPClient https;

// =====================================================
// NTP
// =====================================================
const char* ntpServer = "pool.ntp.org";
const long gmtOffset_sec = -3 * 3600;
const int daylightOffset_sec = 0;

// =====================================================
// CONFIGURACAO DOS EVENTOS
// =====================================================
struct Evento {
  byte canal;
  byte pino;
  bool ultimoEstado;
  unsigned long ultimaMudanca;
};

// INPUT_PULLUP: pino ligado ao GND = evento ativo (estado 1).
Evento eventos[] = {
  {1, D1, false, 0},
  {2, D2, false, 0},
  {3, D3, false, 0}
  // {4, D4, false, 0},
  // {5, D5, false, 0},
  // {6, D6, false, 0},
  // {7, D7, false, 0},
  // {8, D8, false, 0}
};
const byte TOTAL_EVENTOS = sizeof(eventos) / sizeof(eventos[0]);

const unsigned long INTERVALO_HEARTBEAT = 10000UL;
const unsigned long DEBOUNCE_MS = 50UL;
unsigned long ultimoHeartbeat = 0;

// =====================================================
// CONEXAO WIFI
// =====================================================
void conectarWiFi() {
  WiFi.mode(WIFI_STA);
  WiFi.setAutoReconnect(true);
  WiFi.persistent(false);
  WiFi.begin(ssid, password);

  Serial.print("Conectando ao Wi-Fi");
  unsigned long inicio = millis();
  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(250);
    yield();
    if (millis() - inicio > 30000UL) {
      Serial.println("\nAinda sem Wi-Fi; continuando a tentar...");
      inicio = millis();
    }
  }

  Serial.println("\nWi-Fi conectado!");
  Serial.print("IP: ");
  Serial.println(WiFi.localIP());
}

// =====================================================
// SINCRONIZACAO NTP
// =====================================================
bool iniciarNTP() {
  configTime(gmtOffset_sec, daylightOffset_sec, ntpServer, "time.google.com");
  Serial.println("Sincronizando horario NTP...");

  for (int tentativa = 0; tentativa < 30; tentativa++) {
    time_t agora = time(nullptr);
    if (agora > 1700000000) {
      Serial.println("Horario sincronizado!");
      Serial.print(ctime(&agora));
      return true;
    }
    delay(500);
    yield();
  }

  Serial.println("ERRO: NTP nao sincronizado. A validacao HTTPS pode falhar.");
  return false;
}

// =====================================================
// ASSINATURA
// SHA1(payload + timestamp + SECRET_KEY)
// Deve corresponder exatamente a recebe_eventos.php.
// =====================================================
String gerarHash(const String& payload, const String& timestamp) {
  String dados;
  dados.reserve(payload.length() + timestamp.length() + strlen(SECRET_KEY));
  dados = payload;
  dados += timestamp;
  dados += SECRET_KEY;
  return sha1(dados);
}

// =====================================================
// ENVIO HTTPS DOS EVENTOS
// Preserva o formato JSON do firmware anterior.
// =====================================================
bool enviarDados() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("Wi-Fi desconectado. Reconectando...");
    conectarWiFi();
  }

  time_t now = time(nullptr);
  if (now <= 1700000000) {
    Serial.println("ERRO: horario NTP invalido; envio cancelado.");
    return false;
  }

  DynamicJsonDocument doc(1024);
  JsonArray listaEventos = doc.createNestedArray("eventos");
  for (byte i = 0; i < TOTAL_EVENTOS; i++) {
    JsonObject evento = listaEventos.createNestedObject();
    evento["canal"] = eventos[i].canal;
    evento["estado"] = eventos[i].ultimoEstado ? 1 : 0;
  }

  doc["ip"] = WiFi.localIP().toString();
  doc["heap"] = ESP.getFreeHeap();
  doc["firmware"] = "1.1";
  doc["timestamp"] = (long)now;

  String payload;
  payload.reserve(256);
  serializeJson(doc, payload);

  String timestamp = String((unsigned long)now);
  String assinatura = gerarHash(payload, timestamp);

  Serial.println();
  Serial.println("======================================");
  Serial.println("Enviando eventos para o ATALAIA");
  Serial.println("======================================");
  Serial.print("Endpoint: "); Serial.println(serverUrl);
  Serial.print("Token: "); Serial.println(DEVICE_TOKEN);
  Serial.print("Payload: "); Serial.println(payload);

  https.setReuse(true);
  https.setTimeout(5000);

  if (!https.begin(clienteHTTPS, serverUrl)) {
    Serial.println("ERRO: nao foi possivel iniciar HTTPS.");
    return false;
  }

  https.addHeader("Content-Type", "application/json");
  https.addHeader("X-Device-Token", DEVICE_TOKEN);
  https.addHeader("X-Timestamp", timestamp);
  https.addHeader("X-Signature", assinatura);

  int httpCode = https.POST(payload);
  String resposta;
  if (httpCode > 0) {
    resposta = https.getString();
    Serial.printf("HTTP Code: %d\n", httpCode);
    Serial.print("Resposta: "); Serial.println(resposta);
  } else {
    Serial.print("Erro HTTPS: ");
    Serial.println(https.errorToString(httpCode));
    char sslError[128] = {0};
    clienteHTTPS.getLastSSLError(sslError, sizeof(sslError));
    Serial.print("Detalhe TLS: "); Serial.println(sslError);
  }

  https.end();
  bool sucesso = (httpCode >= 200 && httpCode < 300);
  Serial.println(sucesso ? "Envio aceito pelo servidor." : "Envio nao confirmado.");
  Serial.println("--------------------------------------");
  return sucesso;
}

// =====================================================
// SETUP
// =====================================================
void setup() {
  Serial.begin(115200);
  delay(300);

  Serial.println();
  Serial.println("======================================");
  Serial.println("ATALAIA - SENSOR DE EVENTOS");
  Serial.printf("Canais configurados: %u\n", TOTAL_EVENTOS);
  Serial.println("======================================");

  conectarWiFi();
  iniciarNTP();

  // Validacao do certificado contra a CA privada do ATALAIA.
  clienteHTTPS.setTrustAnchors(&atalaiaCA);
  clienteHTTPS.setTimeout(5000);

  for (byte i = 0; i < TOTAL_EVENTOS; i++) {
    pinMode(eventos[i].pino, INPUT_PULLUP);
    eventos[i].ultimoEstado = (digitalRead(eventos[i].pino) == LOW);
    eventos[i].ultimaMudanca = millis();
  }

  Serial.println("Enviando estado inicial...");
  enviarDados();
  ultimoHeartbeat = millis();
}

// =====================================================
// LOOP
// =====================================================
void loop() {
  ESP.wdtFeed();

  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("Wi-Fi desconectado. Reconectando...");
    conectarWiFi();
    iniciarNTP();
  }

  bool houveMudanca = false;
  for (byte i = 0; i < TOTAL_EVENTOS; i++) {
    bool estadoAtual = (digitalRead(eventos[i].pino) == LOW);

    if (estadoAtual != eventos[i].ultimoEstado) {
      if ((unsigned long)(millis() - eventos[i].ultimaMudanca) >= DEBOUNCE_MS) {
        bool estadoConfirmado = (digitalRead(eventos[i].pino) == LOW);
        if (estadoConfirmado != eventos[i].ultimoEstado) {
          eventos[i].ultimoEstado = estadoConfirmado;
          eventos[i].ultimaMudanca = millis();
          houveMudanca = true;
          Serial.printf("Mudanca no canal %u: estado %u\n",
                        eventos[i].canal, estadoConfirmado ? 1 : 0);
        }
      }
    } else {
      eventos[i].ultimaMudanca = millis();
    }
  }

  if (houveMudanca) {
    Serial.println("Enviando atualizacao por mudanca de evento...");
    enviarDados();
    ultimoHeartbeat = millis();
  }

  if ((unsigned long)(millis() - ultimoHeartbeat) >= INTERVALO_HEARTBEAT) {
    Serial.println("Heartbeat...");
    enviarDados();
    ultimoHeartbeat = millis();
  }

  delay(20);
  yield();
}