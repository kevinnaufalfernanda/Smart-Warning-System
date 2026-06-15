#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// --- KONFIGURASI WIFI ---
const char* ssid = "NAMA_WIFI_ANDA";
const char* password = "PASSWORD_WIFI_ANDA";

// --- KONFIGURASI API SERVER ---
// Ganti dengan IP Address Laptop Anda (buka CMD -> ketik ipconfig -> cari IPv4 Address)
const char* serverUrl = "http://192.168.1.xxx:8000"; 
const char* macAddress = "ESP32_001"; // ID Unik Perangkat

// --- KONFIGURASI PIN SENSOR ---
const int trigPin = 5;      // Pin Trigger Sensor Ultrasonik HC-SR04
const int echoPin = 18;     // Pin Echo Sensor Ultrasonik HC-SR04
const int rainPin = 34;     // Pin Analog Sensor Hujan (Tetesan Air)

// --- KONFIGURASI PIN AKTUATOR ---
const int buzzerPin = 21;   // Pin Buzzer
const int pumpPin = 22;     // Pin Relay Pompa Air
const int ledPin = 23;      // Pin LED Indikator

void setup() {
  Serial.begin(115200);

  // Set Mode Pin Sensor
  pinMode(trigPin, OUTPUT);
  pinMode(echoPin, INPUT);
  pinMode(rainPin, INPUT);

  // Set Mode Pin Aktuator
  pinMode(buzzerPin, OUTPUT);
  pinMode(pumpPin, OUTPUT);
  pinMode(ledPin, OUTPUT);

  // Matikan semua aktuator di awal
  digitalWrite(buzzerPin, LOW);
  digitalWrite(pumpPin, LOW);
  digitalWrite(ledPin, LOW);

  // Mulai Koneksi WiFi
  Serial.print("Menghubungkan ke WiFi");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Terhubung!");
  Serial.print("IP Address: ");
  Serial.println(WiFi.localIP());

  // Reset Status Aktuator di Web (Opsional - agar web tahu ESP baru menyala)
  resetActuatorsOnWeb();
}

void loop() {
  // 1. BACA DATA SENSOR (Syarat Ujian minimal 2 sensor)
  float distance = readUltrasonicDistance();
  int rainValue = analogRead(rainPin); // 0 (sangat basah) - 4095 (kering)

  Serial.println("--- BACA SENSOR ---");
  Serial.print("Jarak Air: "); Serial.print(distance); Serial.println(" cm");
  Serial.print("Intensitas Hujan (Analog): "); Serial.println(rainValue);

  // 2. KIRIM DATA KE SERVER (API /api/trigger-notif)
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String endpoint = String(serverUrl) + "/api/trigger-notif";
    http.begin(endpoint);
    http.addHeader("Content-Type", "application/json");

    // Format JSON untuk dikirim
    StaticJsonDocument<200> doc;
    doc["mac_address"] = macAddress;
    doc["distance"] = distance;
    doc["rain_val"] = rainValue;

    String requestBody;
    serializeJson(doc, requestBody);

    int httpResponseCode = http.POST(requestBody);
    Serial.print("HTTP POST Status Code: ");
    Serial.println(httpResponseCode);
    
    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("Response Server: " + response);
    }
    http.end();

    // 3. TARIK STATUS AKTUATOR DARI WEB (API /api/actuators)
    syncActuatorsFromWeb();
  } else {
    Serial.println("WiFi Terputus!");
  }

  // Jeda 5 detik sebelum pembacaan selanjutnya
  delay(5000); 
}

// Fungsi Membaca Jarak dari HC-SR04
float readUltrasonicDistance() {
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);

  long duration = pulseIn(echoPin, HIGH, 30000); // Timeout 30ms
  if (duration == 0) return 400.0; // Jika tidak ada pantulan
  
  // Kecepatan suara 0.034 cm/us
  float distance = duration * 0.034 / 2.0; 
  return distance;
}

// Fungsi Sinkronisasi Aktuator dari Web
void syncActuatorsFromWeb() {
  HTTPClient http;
  String endpoint = String(serverUrl) + "/api/actuators";
  http.begin(endpoint);
  
  int httpResponseCode = http.GET();
  if (httpResponseCode > 0) {
    String payload = http.getString();
    
    StaticJsonDocument<200> doc;
    DeserializationError error = deserializeJson(doc, payload);
    
    if (!error) {
      bool isBuzzerOn = doc["buzzer"];
      bool isPumpOn = doc["pompa"];
      bool isLedOn = doc["led"];

      // Eksekusi Pin Fisik
      digitalWrite(buzzerPin, isBuzzerOn ? HIGH : LOW);
      digitalWrite(pumpPin, isPumpOn ? HIGH : LOW);
      digitalWrite(ledPin, isLedOn ? HIGH : LOW);

      Serial.println("Sinkronisasi Aktuator Berhasil!");
    }
  }
  http.end();
}

// Fungsi Reset Aktuator di Web saat alat baru dinyalakan
void resetActuatorsOnWeb() {
  HTTPClient http;
  String endpoint = String(serverUrl) + "/api/actuators/reset";
  http.begin(endpoint);
  http.POST("");
  http.end();
  Serial.println("Status aktuator di-reset ke Web");
}
