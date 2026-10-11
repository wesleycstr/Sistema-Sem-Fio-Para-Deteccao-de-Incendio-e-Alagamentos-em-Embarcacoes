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
//"https://192.168.0.100/api/sensores.php";
"http://192.168.0.100/api/sensores.php";

// =====================================================
// AUTENTICAÇÃO
// =====================================================
const char* DEVICE_TOKEN = "sensor_caixinha";

const char* SECRET_KEY = "8£7uNf-Z`4rn";

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

Adafruit_Sensor *aht_humidity = nullptr;
Adafruit_Sensor *aht_temp = nullptr;

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

  Serial.println("");
  Serial.println("WiFi conectado!");

  Serial.print("IP: ");
  Serial.println(WiFi.localIP());

  digitalWrite(ledPin, HIGH);
  digitalWrite(externalLedPin, LOW);
}

// =====================================================
// NTP
// =====================================================
void iniciarNTP() {

  configTime(gmtOffset_sec,
             daylightOffset_sec,
             ntpServer);

  Serial.println("Sincronizando horario NTP...");

  struct tm timeinfo;

  while (!getLocalTime(&timeinfo)) {
    Serial.println("Aguardando NTP...");
    delay(1000);
  }

  Serial.println("Horario sincronizado!");
}

// =====================================================
// SHA256
// =====================================================
String gerarHash(String payload,
                 String timestamp)
{
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

      aht_temp = aht.getTemperatureSensor();
      aht_humidity = aht.getHumiditySensor();

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

  float ratio = Rs / R0;

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
// =====================================================
// ENVIO HTTP
// =====================================================

void enviarDados(float temperatura,
                 float umidade,
                 float gas)
{
  if (WiFi.status() != WL_CONNECTED) {

    Serial.println("WiFi desconectado!");

    conectarWiFi();
  }

  //WiFiClient client;
  BearSSL::WiFiClientSecure client;

  HTTPClient http;

  // =================================================
  // JSON
  // =================================================

  DynamicJsonDocument doc(512);

  doc["sensor"] = DEVICE_TOKEN;
  doc["temperatura"] = temperatura;
  doc["umidade"] = umidade;
  doc["gas"] = gas;
  doc["ip"] = WiFi.localIP().toString();
  doc["heap"] = ESP.getFreeHeap();

  time_t now = time(nullptr);

  doc["timestamp"] = now;

  String payload;

  serializeJson(doc, payload);

  // =================================================
  // ASSINATURA
  // =================================================

  String timestamp = String(now);

  String assinatura =
    gerarHash(payload, timestamp);

  Serial.println("================================");
  Serial.println("ENVIO PARA O SERVIDOR");
  Serial.println("================================");

  Serial.println("Payload:");
  Serial.println(payload);

  Serial.println("Timestamp:");
  Serial.println(timestamp);

  Serial.println("Hash:");
  Serial.println(assinatura);

  // =================================================
  // HTTP
  // =================================================

  if (!http.begin(client, serverUrl)) {

    Serial.println("Falha ao iniciar HTTP");

    return;
  }

  http.addHeader(
    "Content-Type",
    "application/json"
  );

  http.addHeader(
    "X-Device-Token",
    DEVICE_TOKEN
  );

  http.addHeader(
    "X-Timestamp",
    timestamp
  );

  http.addHeader(
    "X-Signature",
    assinatura
  );

  // =================================================
  // POST
  // =================================================

  int httpCode =
    http.POST(payload);

  // =================================================
  // RESULTADO
  // =================================================

  Serial.print("HTTP Code: ");
  Serial.println(httpCode);

  if (httpCode > 0) {

    String resposta =
      http.getString();

    Serial.println("Resposta do servidor:");
    Serial.println(resposta);

  } else {

    Serial.print("Erro HTTP: ");
    Serial.println(
      http.errorToString(httpCode)
    );
  }

  http.end();
}

// =====================================================
// SETUP
// =====================================================
void setup() {

  Serial.begin(115200);

  pinMode(ledPin, OUTPUT);
  pinMode(externalLedPin, OUTPUT);

  digitalWrite(ledPin, HIGH);
  digitalWrite(externalLedPin, LOW);

  conectarWiFi();

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

    Serial.println("Reconectando WiFi...");

    conectarWiFi();
  }

  float temperatura = 0;
  float umidade = 0;

  // ===============================================
  // LEITURA AHT
  // ===============================================
  if (aht_ok &&
      aht_temp &&
      aht_humidity)
  {
    sensors_event_t humidity;
    sensors_event_t temp;

    aht_humidity->getEvent(&humidity);
    aht_temp->getEvent(&temp);

    temperatura = temp.temperature;
    umidade = humidity.relative_humidity;

    Serial.print("Temperatura: ");
    Serial.println(temperatura);

    Serial.print("Umidade: ");
    Serial.println(umidade);

  } else {

    Serial.println("AHT indisponivel");

    if (aht.begin()) {

      Serial.println("AHT recuperado!");

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
  float gas = lerMQ7();

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
