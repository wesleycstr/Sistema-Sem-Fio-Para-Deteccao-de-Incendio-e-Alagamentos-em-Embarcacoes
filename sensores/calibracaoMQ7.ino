#include <ESP8266WiFi.h>

const int mq7Pin = A0;

// =========================
// CONFIGURAÇÕES DO SENSOR
// =========================
float RL = 10000.0;   // resistor de carga (10k ohms)
float Vc = 3.3;       // tensão do ESP8266

// Fator do datasheet (ar limpo)
float cleanAirFactor = 9.83;

// =========================
// VARIÁVEIS
// =========================
float R0 = 0;

// =========================
// SETUP
// =========================
void setup() {
  Serial.begin(115200);
  delay(2000);

  Serial.println("=================================");
  Serial.println("CALIBRACAO DO MQ-7");
  Serial.println("=================================");
  Serial.println("IMPORTANTE:");
  Serial.println("- Deixe o sensor em AR LIMPO");
  Serial.println("- Aguarde aquecimento (~10 a 20 min)");
  Serial.println("=================================");

  delay(10000); // tempo para você se preparar
}

// =========================
// LOOP (CALIBRAÇÃO)
// =========================
void loop() {

  int amostras = 50;
  float somaRs = 0;

  Serial.println("Coletando amostras...");

  for (int i = 0; i < amostras; i++) {
    
    int adcValue = analogRead(mq7Pin);

    if (adcValue == 0) adcValue = 1;

    float Vout = adcValue * (Vc / 1023.0);

    float Rs = RL * ((Vc / Vout) - 1);

    somaRs += Rs;

    Serial.print("Amostra ");
    Serial.print(i);
    Serial.print(" - Rs: ");
    Serial.println(Rs);

    delay(200);
  }

  float mediaRs = somaRs / amostras;

  // =========================
  // CALCULO DO R0
  // =========================
  R0 = mediaRs / cleanAirFactor;

  Serial.println("=================================");
  Serial.println("RESULTADO DA CALIBRACAO");
  Serial.println("=================================");
  
  Serial.print("Rs medio: ");
  Serial.println(mediaRs);

  Serial.print("R0 calculado: ");
  Serial.println(R0);

  Serial.println("=================================");
  Serial.println("COPIE ESTE VALOR PARA SEU CODIGO:");
  
  Serial.print("float R0 = ");
  Serial.print(R0);
  Serial.println(";");

  Serial.println("=================================");

  // Espera antes de recalibrar novamente
  delay(10000);
}
