#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClientSecureBearSSL.h>
#include <ArduinoJson.h>
#include <Hash.h>
#include <time.h>

// =====================================================
// CONFIGURAÇÕES WIFI
// =====================================================
//const char* ssid = "Rede_IoT";
//const char* password = "Sly@11011984";
const char* ssid = "brisa_2112083";
const char* password = "sly@110184";

// =====================================================
// SERVIDOR HTTPS
// =====================================================
const char* serverUrl =
//"https://192.168.1.100/api/sensores.php";
"https://192.168.32.55/recebe_eventos.php";

// =====================================================
// AUTENTICAÇÃO
// =====================================================
const char* DEVICE_TOKEN = "Sensor_eventos";
const char* SECRET_KEY = "X9f2D8mQ4w";

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

    Serial.print(".");

    delay(500);

    ESP.wdtFeed();
}
  Serial.println("");
  Serial.println("WiFi conectado!");

  Serial.print("IP: ");
  Serial.println(WiFi.localIP());

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
    ESP.wdtFeed();
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

struct Evento{

    byte canal;

    byte pino;

    bool ultimoEstado;

    unsigned long ultimaMudanca;

};

Evento eventos[] = { 
{1, D1, false},
//{2, D2, false},
//{3, D3, false},
//{4, D4, false},
//{5, D5, false},
//{6, D6, false},
//{7, D7, false},
//{8, D8, false}
};

const byte TOTAL_EVENTOS =
sizeof(eventos)/sizeof(eventos[0]);

// =====================================================
// ENVIO HTTPS DOS EVENTOS
// =====================================================
// =====================================================
// ENVIO HTTPS DOS EVENTOS
// =====================================================
void enviarDados()
{
    // ==========================================
    // GARANTE CONEXÃO WIFI
    // ==========================================

    if (WiFi.status() != WL_CONNECTED)
    {
        Serial.println("WiFi desconectado.");

        conectarWiFi();
    }

    BearSSL::WiFiClientSecure client;
    client.setInsecure();

    HTTPClient https;

    // ==========================================
    // MONTA JSON
    // ==========================================

    DynamicJsonDocument doc(1024);

    JsonArray listaEventos =
        doc.createNestedArray("eventos");

    for(byte i = 0; i < TOTAL_EVENTOS; i++)
    {
        JsonObject evento =
            listaEventos.createNestedObject();

        evento["canal"] =
            eventos[i].canal;

        evento["estado"] =
            eventos[i].ultimoEstado ? 1 : 0;
    }

    doc["ip"] =
        WiFi.localIP().toString();

    doc["heap"] =
        ESP.getFreeHeap();

    doc["firmware"] =
        "1.0";

    time_t now =
        time(nullptr);

    doc["timestamp"] =
        now;

    String payload;

    serializeJson(doc, payload);

    // ==========================================
    // ASSINATURA
    // ==========================================

    String timestamp =
        String(now);

    String assinatura =
        gerarHash(payload, timestamp);

    // ==========================================
    // DEBUG
    // ==========================================

    Serial.println();
    Serial.println("======================================");
    Serial.println("Enviando eventos");
    Serial.println("======================================");

    Serial.println("Payload:");

    serializeJsonPretty(doc, Serial);

    Serial.println();

    Serial.print("Timestamp: ");

    Serial.println(timestamp);

    Serial.print("Hash: ");

    Serial.println(assinatura);

    // ==========================================
    // HTTPS
    // ==========================================

    https.begin(client, serverUrl);

    https.addHeader(
        "Content-Type",
        "application/json");

    https.addHeader(
        "X-Device-Token",
        DEVICE_TOKEN);

    https.addHeader(
        "X-Timestamp",
        timestamp);

    https.addHeader(
        "X-Signature",
        assinatura);

    // ==========================================
    // ENVIO
    // ==========================================

// ==========================================
// ENVIO
// ==========================================

int httpCode =
    https.POST(payload);

// ==========================================
// RESULTADO
// ==========================================

if(httpCode > 0)
{
    Serial.print("HTTP Code: ");

    Serial.println(httpCode);

    String resposta =
        https.getString();

    Serial.println("Resposta:");

    Serial.println(resposta);

    if(httpCode == HTTP_CODE_OK)
    {
        Serial.println("Envio realizado com sucesso.");
    }
    else
    {
        Serial.println("Servidor respondeu com erro.");
    }
}
else
{
    Serial.print("Erro HTTPS: ");

    Serial.println(
        https.errorToString(httpCode)
    );
}

https.end();

    Serial.println("--------------------------------------");
    Serial.println();
}

// =====================================================
// SETUP
// =====================================================
unsigned long ultimoHeartbeat = 0;

void setup() {

  Serial.begin(115200);

  conectarWiFi();

  iniciarNTP();

  for(byte i=0;i<TOTAL_EVENTOS;i++){

      pinMode(eventos[i].pino, INPUT_PULLUP);

      eventos[i].ultimoEstado =
          digitalRead(eventos[i].pino) == LOW;

  }

  Serial.println("Enviando estado inicial...");

  enviarDados();

  ultimoHeartbeat = millis();
}


const unsigned long INTERVALO_HEARTBEAT = 10000; // 10 segundos

void loop()
{
    ESP.wdtFeed();

    // ==========================================
    // GARANTE CONEXÃO WIFI
    // ==========================================

    if(WiFi.status() != WL_CONNECTED)
    {
        Serial.println("Reconectando WiFi...");

        conectarWiFi();
    }

    bool houveMudanca = false;

    // ==========================================
    // VERIFICA TODOS OS EVENTOS
    // ==========================================

    for(byte i = 0; i < TOTAL_EVENTOS; i++)
    {
        bool estadoAtual =
            digitalRead(eventos[i].pino) == LOW;

      if(estadoAtual != eventos[i].ultimoEstado)
      {
          if(millis() - eventos[i].ultimaMudanca > 50)
          {
              eventos[i].ultimoEstado = estadoAtual;
              eventos[i].ultimaMudanca = millis();
              houveMudanca = true;
          }
      }
    }

    // ==========================================
    // ENVIA IMEDIATAMENTE SE ALGUM EVENTO MUDOU
    // ==========================================

    if(houveMudanca)
    {
        Serial.println("Enviando atualização...");

        enviarDados();

        ultimoHeartbeat = millis();
    }

    // ==========================================
    // HEARTBEAT
    // ==========================================

    if(millis() - ultimoHeartbeat >= INTERVALO_HEARTBEAT)
    {
        Serial.println("Heartbeat...");

        enviarDados();

        ultimoHeartbeat = millis();
    }

    delay(100);
}
