# Perancangan Arsitektur API dan Integrasi Sub-Sistem

*(Catatan: Anda dapat menyalin seluruh teks di bawah ini ke dalam Microsoft Word Anda. Perhatikan tanda kurung siku seperti **[MASUKKAN GAMBAR...]** sebagai panduan letak gambar atau tabel asli Anda).*

---

## 1. Deskripsi Sistem dan Tujuan Utama

Sistem Peringatan Dini (*Smart Warning System*) ini dirancang sebagai solusi mitigasi bencana *real-time* yang secara otonom menghubungkan perangkat keras IoT di lapangan dengan server pusat komputasi dan layanan antarmuka warga. Tujuan utamanya adalah menyelesaikan masalah fundamental dalam penanggulangan bencana, yakni lambatnya respons, tingginya risiko *human-error*, dan kurangnya akurasi dalam pemantauan ketinggian debit air yang selama ini dilakukan secara manual. 

Dengan mengotomatisasi seluruh siklus operasional melalui integrasi *Application Programming Interface* (API)—mulai dari pembacaan sensor oleh perangkat keras hingga penyebaran peringatan massal ke berbagai sub-sistem (seperti *Web Dashboard* dan Telegram)—sistem ini menjamin ketersediaan informasi seketika (*instant availability*). Hal ini memastikan bahwa setiap anomali data yang berkaitan dengan fluktuasi tinggi muka air dan intensitas curah hujan dapat segera dianalisis dan direspons oleh pihak berwenang maupun masyarakat setempat tanpa membuang waktu kritis penyelamatan (*golden time*).

## 2. Tinjauan Arsitektur Sistem

Smart Warning System ini dibangun dengan mengedepankan konsep arsitektur terpusat (*centralized architecture*) berbasis *micro-services approach*. Pendekatan ini secara tegas memisahkan tugas pengumpulan data, pemrosesan logika komputasi, dan penyebaran peringatan ke dalam sub-sistem yang saling terisolasi namun terintegrasi erat. Secara konseptual, topologi arsitektur dibagi menjadi tiga pilar utama:

1. **Sub-Sistem Pengumpul Data (Perangkat IoT Node - ESP32):** Berfungsi sebagai stasiun akuisisi data fisik di lingkungan lapangan. Mikrokontroler ESP32 difokuskan untuk secara terus-menerus membaca nilai jarak permukaan air menggunakan gelombang suara dari sensor Ultrasonik (HC-SR04) dan membaca intensitas hujan menggunakan modul sensor Raindrop. Perangkat ini didesain agar sangat ringan (*lightweight*), di mana ia sama sekali tidak dibebani oleh tugas pemrosesan logika bahaya. Tugas tunggalnya adalah mengekapsulasi data mentah (*raw data*) ke dalam format JSON dan mentransmisikannya secara konsisten melalui pemanggilan API ke server.
2. **Sub-Sistem Pemrosesan Pusat (Server EWS - Laravel):** Merupakan otak atau jantung dari seluruh arsitektur sistem. Dibangun di atas fondasi *framework* PHP Laravel, server ini bertugas menerima parameter sensor yang dikirimkan oleh IoT Node. Server akan memvalidasi data, membandingkannya dengan parameter ambang batas bahaya (*threshold*), menyimpannya ke dalam basis data relasional (MySQL), dan sekaligus bertindak sebagai *Host* yang menyajikan antarmuka *Web Dashboard* interaktif untuk operator pengawas.
3. **Sub-Sistem Gateway Peringatan (Telegram & Notifikasi Web):** Lapisan integrasi *front-end* dan *third-party* yang memikul tanggung jawab krusial untuk mendistribusikan peringatan dini kepada pengguna akhir. Lapisan ini hanya akan bekerja (terpicu) berdasarkan instruksi langsung dari Sub-Sistem Pemrosesan Pusat saat kondisi darurat terverifikasi.

**[MASUKKAN GAMBAR BAGAN/TOPOLOGI ARSITEKTUR KESELURUHAN DI SINI]**

## 3. Perancangan Arsitektur API dan Endpoint

Integrasi lintas sub-sistem (baik perangkat keras maupun perangkat lunak) sangat bergantung pada keandalan *Application Programming Interface* (API). Arsitektur komunikasi yang digunakan dalam sistem ini berbasis protokol HTTP/HTTPS *stateless* dengan format pertukaran data standar JavaScript Object Notation (JSON). Terdapat dua *endpoint* API utama yang dirancang secara spesifik untuk menjaga sinkronisasi aliran data dari hulu ke hilir:

### 3.1. API Ingesti Data (Integrasi Arah Masuk: ESP32 ke Server)
Endpoint ini didesain khusus untuk mengelola pengiriman data telemetri dari perangkat IoT ke server pusat. Kecepatan dan kemampuan menangani konkurensi menjadi fokus utama pada arsitektur API ini.
*   **Protokol dan Endpoint API:** `POST /api/trigger-notif`
*   **Spesifikasi Teknis:** Menerima *request header* berupa `Content-Type: application/json`.
*   **Mekanisme Kerja Sub-Sistem:** ESP32 bertindak sebagai klien yang mengirimkan *request* secara periodik. Server menerima *payload* yang mencakup variabel `mac_address` (sebagai kunci otentikasi identitas perangkat), `distance` (variabel numerik jarak permukaan air dalam sentimeter), dan `rain_val` (variabel analog intensitas curah hujan dengan rentang nilai 0-4095). Setelah divalidasi, API kemudian mengeksekusi instruksi SQL untuk menyuntikkan ( *insert* ) data tersebut ke tabel `sensor_logs`. Apabila algoritma server mengindikasikan status anomali (Waspada/Bahaya), skrip API ini akan secara internal menginisiasi rangkaian perintah eksekusi peringatan ke sub-sistem Telegram.

**[MASUKKAN TABEL SPESIFIKASI API POST / INGEST DI SINI]**

### 3.2. API Sinkronisasi Dashboard (Integrasi Arah Keluar: Server ke Web UI)
API ini dirancang khusus untuk memenuhi kebutuhan sinkronisasi *Real-Time* pada antarmuka *Web Dashboard*, memastikan operator selalu melihat data paling mutakhir tanpa adanya *delay* yang berarti.
*   **Protokol dan Endpoint API:** `GET /data-sensor/latest`
*   **Spesifikasi Teknis:** Menggunakan metode GET yang mengembalikan nilai objek JSON.
*   **Mekanisme Kerja Sub-Sistem:** Alih-alih membebani *server* dengan koneksi WebSockets yang berat, sub-sistem antarmuka klien menggunakan metode *Asynchronous Polling* (Ajax/Fetch API) setiap 1000 milidetik (1 detik). Klien akan memanggil API ini, lalu server merespons dengan memberikan 1 baris rekaman data (*record*) terbaru. *Payload response* dari API ini mencakup nilai jarak, kondisi cuaca, dan stempel waktu (*timestamp*). Data terstruktur inilah yang memotori pustaka *JavaScript* pada *front-end* untuk menganimasi pergerakan jarum pada *Gauge Chart* dan mengubah status teks pada layar secara dinamis, sepenuhnya tanpa memerlukan proses muat ulang ( *refresh* ) halaman web.

**[MASUKKAN TABEL SPESIFIKASI API GET / LATEST DI SINI]**

## 4. Logika Bisnis dan Evaluasi Ambang Batas (Thresholding)

Arsitektur API tidak hanya sekadar menyimpan angka, melainkan diperkuat oleh logika komputasi operasional ( *Business Logic* ) internal yang mengubah angka mentah tersebut menjadi klasifikasi status informatif. Aturan pemrosesan multi-parameter ini bertindak sebagai penentu kebijakan ( *Policy Maker* ) dalam men- *trigger* peringatan:

### 4.1. Pemrosesan Kategori Ketinggian Air (Ultrasonik)
Algoritma backend mengelompokkan nilai numerik `distance` ke dalam tiga matriks status kebencanaan:
*   **Status AMAN (Indikator Hijau):** Terjadi jika nilai jarak ultrasonik terukur lebih besar dari `60 cm`. Dalam kondisi ini, debit air dinyatakan berada pada rentang fluktuasi normal. Sistem hanya akan melakukan pencatatan log ( *silent monitoring* ) tanpa memicu alarm.
*   **Status WASPADA / SIAGA (Indikator Kuning):** Terjadi jika parameter jarak permukaan air menyusut dan berada di rentang `<= 60 cm` dan `> 20 cm`. Ini merupakan indikasi awal adanya anomali kenaikan debit air. Sistem akan mulai menaikkan tingkat kesiagaan dan mengirimkan notifikasi peringatan pra-bencana.
*   **Status BAHAYA / DARURAT (Indikator Merah):** Terjadi jika kalkulasi jarak air menyentuh titik kritis, yakni `<= 20 cm` dari batas maksimal plafon sensor. Menandakan potensi genangan atau banjir telah mencapai tingkat yang sangat tinggi, sehingga memicu instruksi peringatan evakuasi secara instan.

### 4.2. Pemrosesan Kategori Intensitas Cuaca (Raindrop)
Sensor *raindrop* membaca tingkat resistensi sirkuit yang berubah saat terkena percikan air. Algoritma mengubah nilai kelembapan relatif menjadi status diskrit:
*   **Status CERAH:** Ditetapkan secara programatis apabila nilai analog (*raw*) dari ESP32 bernilai lebih dari atau sama dengan `1400`. Ini menandakan tidak adanya curah hujan signifikan yang mengenai permukaan panel sensor.
*   **Status HUJAN:** Ditetapkan apabila tetesan air yang menutupi panel membuat resistensi sirkuit menurun drastis, sehingga nilai analog yang terbaca merosot jauh lebih kecil dari `< 1400`. Sistem akan langsung menandai kondisi cuaca sebagai Hujan, terlepas dari ketinggian air saat itu.

**[MASUKKAN TABEL KATEGORI STATUS DAN AMBANG BATAS (THRESHOLD) DI SINI]**

## 5. Integrasi Sub-Sistem Peringatan Ganda (Dual-Layer Notification)

Inti dari nilai tambah (*value proposition*) arsitektur sistem ini terletak pada kemampuannya mengintegrasikan pengiriman informasi secara terstruktur dan masif. Strategi mitigasi diwujudkan melalui dua *layer* sub-sistem integrasi utama:

### 5.1. Integrasi Notifikasi Internal (In-App Toast Popup)
Demi memastikan kepekaan situasi (*situational awareness*) bagi operator yang sedang berada di depan layar monitor *Web Dashboard*, integrasi *Front-End* (*Alpine.js*) dirancang untuk selalu bereaksi terhadap *response* dari API. Saat perubahan status darurat terdeteksi, antarmuka akan memunculkan *In-App Toast Notification* secara melayang (*overlay*). Fitur ini secara pintar menampilkan gradasi warna peringatan kontras berdasarkan keparahan status (Warna merah solid untuk Bahaya, kuning/oranye untuk Waspada, dan biru untuk Hujan). Lebih jauh lagi, integrasi ini ditautkan dengan fungsionalitas *Browser Push Notification* API yang memanfaatkan modul bawaan Sistem Operasi (OS) guna memberikan umpan balik berupa peringatan audio ( *alert sound* ), memastikan operator sadar meski sedang membuka *tab* aplikasi lain.

### 5.2. Integrasi Gateway Eksternal (Telegram Bot API)
Ketika algoritma server mengklasifikasikan situasi masuk ke dalam domain peringatan, kelas modul `NotificationService` akan mengambil alih kendali. Modul ini bertugas mengkompilasi *string* pesan menggunakan format teks kaya *Markdown*—secara presisi memuat nilai Jarak, Status, Cuaca, dan Waktu kejadian. Paket *string* tersebut lalu dikapsulisasi dan dikirim sebagai HTTP POST *request* menuju infrastruktur *cloud API Telegram* terenkripsi. Pesan seketika disiarkan (*broadcast*) langsung ke dalam grup percakapan masyarakat, memastikan persebaran informasi menjangkau audiens secara luas seketika.

### 5.3. Mekanisme Proteksi Anti-Spam (API Rate-Limiting Cooldown)
Dalam sebuah kondisi riil bencana yang berkepanjangan, perangkat IoT akan tak henti-hentinya memanggil API setiap detiknya. Melakukan tembakan HTTP *request* peringatan ke API Telegram secara simultan setiap detik sangat dilarang keras dan dapat memicu pemblokiran akun (*Rate Limit Ban* atau *HTTP 429 Too Many Requests*) dari pihak Telegram. 

Untuk melindungi infrastruktur integrasi dari insiden tersebut, arsitektur dilengkapi dengan algoritma *Cooldown Timer 10 Detik*. Cara kerjanya sangat presisi:
1. Setiap notifikasi sukses, sistem menyisipkan rekam jejak (*log*) ke dalam tabel `alert_history`.
2. Saat sensor kembali mengirimkan data dalam kondisi bahaya satu detik kemudian, Laravel menggunakan library `Carbon` untuk menghitung diferensiasi waktu antara saat ini dan waktu pengiriman terakhir.
3. API akan **membuang** (*drop*) instruksi pengiriman notifikasi berulang jika status bahaya belum reda dan belum melewati masa jeda tunggu (*cooldown*) sekurang-kurangnya 10 detik. Sistem baru akan menembakkan peringatan ulang apabila 10 detik tersebut telah lampau, atau jika terdeteksi **eskalasi status secara drastis** (misal dari Waspada melonjak menjadi Bahaya).

**[MASUKKAN GAMBAR / SCREENSHOT NOTIFIKASI DI TELEGRAM DAN DI DALAM DASHBOARD DI SINI]**

## 6. Keamanan Komunikasi dan Pengujian Kinerja (Security & Testing)

Validasi kelayakan dan keberhasilan perancangan arsitektur API berserta integrasi sub-sistemnya diukur secara ketat melalui parameter keamanan dan kinerja operasional:

*   **Keamanan Arsitektur API:** Mekanisme otentikasi di lapisan pertama saat ini divalidasi memanfaatkan kecocokan identitas perangkat keras (*MAC Address*) yang divalidasi ke basis data pendaftaran alat. Guna mempersiapkan skalabilitas ke tingkat *Enterprise*, cetak biru arsitektur telah dirancang dengan kemampuan komprehensif untuk mendukung otentikasi modern berbasis *Header Bearer Token* maupun *API Key Verification*, guna memblokir upaya penyuntikan data palsu (*data spoofing/injection*) dari jaringan luar. Arsitektur ini juga merekomendasikan penggunaan *Transport Layer Security* (TLS) melalui HTTPS untuk mengamankan pertukaran data (*payload encryption*).
*   **Pengujian Integrasi Tanggap Darurat (User Acceptance Testing / UAT):** Rantai integrasi arsitektur EWS ini sengaja didesain agar sangat ringan tanpa beban *middleware* yang berlebihan. Hal ini dilakukan demi memenuhi satu metrik pamungkas: **Target latensi komunikasi *End-to-End* di bawah 5 detik**. Rangkaian pengujian (*testing*) dilakukan secara komprehensif untuk memvalidasi bahwa jeda waktu kumulatif—mulai dari titik nol sensor ultrasonik menangkap pantulan air, pengiriman data paket ESP32, pemrosesan algoritma *threshold* di server Laravel, eksekusi pemanggilan webhook API, hingga akhirnya notifikasi teks berdering di layar ponsel (*smartphone*) masyarakat melalui Telegram—mampu diselesaikan sepenuhnya sesuai dengan standar mitigasi kritis, yakni di bawah ambang batas respons 5 detik.

**[MASUKKAN TABEL ATAU DIAGRAM HASIL PENGUJIAN UAT JIKA ADA DI SINI]**
