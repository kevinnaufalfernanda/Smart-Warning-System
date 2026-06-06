# 📋 MASTER CONTEXT — Smart Warning System (EWS)

> **Terakhir Diperbarui:** 8 Mei 2026
> **Tujuan File Ini:** Grounding context agar percakapan di room chat berbeda tetap nyambung tanpa kehilangan konteks.

---

## 1. Deskripsi Proyek

**Smart Warning System** adalah Sistem Peringatan Dini (Early Warning System / EWS) bencana banjir berbasis IoT. Sistem ini menghubungkan perangkat keras ESP32 di lapangan dengan server web Laravel sebagai pusat pemrosesan data, dan menggunakan Telegram Bot serta In-App Notification sebagai jalur penyiaran peringatan ke warga.

**Alur kerja utama:**
1. **ESP32** membaca sensor (Ultrasonik HC-SR04 untuk jarak air, Raindrop untuk hujan)
2. ESP32 mengirim data via HTTP POST ke **Laravel Backend** (`POST /api/trigger-notif`)
3. Laravel memvalidasi, menyimpan ke **MySQL**, dan mengevaluasi threshold
4. Jika status BAHAYA/WASPADA/HUJAN → kirim notifikasi ke **Telegram Bot** + **In-App Toast Popup**
5. **Web Dashboard** melakukan polling setiap 1 detik ke `GET /data-sensor/latest` untuk update real-time

---

## 2. Tech Stack

### Backend
| Komponen | Teknologi | Versi |
|---|---|---|
| Framework | **Laravel** | **12.x** |
| Bahasa | PHP | ^8.2 |
| Database | MySQL (Remote) | - |
| Web Server (Dev) | Laravel Herd | - |
| Task Scheduler | Laravel Artisan Command (`sensor:check-bahaya`) | - |

### Frontend
| Komponen | Teknologi | Versi |
|---|---|---|
| Template Engine | **Blade** (.blade.php) | - |
| CSS Framework | **Tailwind CSS** | **4.0** |
| JavaScript | **Alpine.js** (CDN) | - |
| Build Tool | **Vite** | 7.x |
| HTTP Client (JS) | **Fetch API** / Axios | - |
| Bundler Plugin | laravel-vite-plugin | 2.x |

### Hardware (IoT)
| Komponen | Teknologi |
|---|---|
| Mikrokontroler | **ESP32** |
| Sensor Jarak | **HC-SR04** (Ultrasonik) |
| Sensor Hujan | **Raindrop Sensor** (Analog) |
| Koneksi | Wi-Fi → HTTP POST ke Laravel |

### Integrasi Eksternal
| Komponen | Teknologi |
|---|---|
| Notifikasi | **Telegram Bot API** |
| Notifikasi In-App | Alpine.js Toast Popup + Browser Push Notification |

### Development Tools
| Tool | Keterangan |
|---|---|
| Local Server | **Laravel Herd** (domain: `smart-warning-system.test`) |
| Package Manager | Composer (PHP), npm (Node) |
| Version Control | Git (GitHub — 2 remote: origin + collaborator) |
| OS | Windows |

---

## 3. Struktur Database (MySQL Remote)

Database: `iot_db` di host `10.21.106.6`

| Tabel | Fungsi |
|---|---|
| `sensor_logs` | Menyimpan semua data pembacaan sensor (jarak, hujan, status, kondisi) |
| `devices` | Mendaftar perangkat ESP32 berdasarkan `mac_address` |
| `thresholds` | Konfigurasi ambang batas per stasiun (opsional, ada fallback default) |
| `alert_history` | Riwayat semua alert yang pernah dikirim |
| `notification_logs` | Log pengiriman notifikasi (Telegram status: Sent/Failed) |

### Kolom Penting di `sensor_logs`
- `device_id` — FK ke tabel devices
- `distance_cm` — Jarak permukaan air (cm)
- `rain_intensity_raw` — Nilai analog sensor hujan (0-4095)
- `flood_condition` — HUJAN / CERAH
- `flood_status` — AMAN / WASPADA / BAHAYA
- `created_at` — Timestamp

---

## 4. Logika Bisnis — Threshold / Ambang Batas

### Ketinggian Air (Ultrasonik)
| Nilai Jarak | Status | Warna Indikator | Aksi |
|---|---|---|---|
| `> 60 cm` | **AMAN** | 🟢 Hijau | Silent monitoring |
| `> 20 cm` dan `<= 60 cm` | **WASPADA** | 🟡 Kuning | Telegram + Toast |
| `<= 20 cm` | **BAHAYA** | 🔴 Merah | Telegram + Toast + Alarm |

### Intensitas Hujan (Raindrop)
| Nilai Analog | Status | Aksi |
|---|---|---|
| `>= 1400` | **CERAH** | Tidak ada intervensi |
| `< 1400` | **HUJAN** | Telegram + Toast |

---

## 5. API Endpoints

### `POST /api/trigger-notif` — Data Ingestion (ESP32 → Server)
- **Input (JSON):** `mac_address`, `distance`, `rain_val`
- **Proses:** Validasi device → Evaluasi threshold → Insert `sensor_logs` → Cek cooldown → Kirim Telegram jika darurat
- **Anti-Spam:** Cooldown 10 detik per stasiun (kecuali terjadi eskalasi status)
- **CSRF:** Dinonaktifkan untuk endpoint ini

### `GET /data-sensor/latest` — Dashboard Polling
- **Output (JSON):** `jarak`, `hujan`, `waktu`, `raw_pesan`
- **Dipanggil:** Setiap 1 detik oleh frontend (Fetch API)

---

## 6. Struktur File Penting

```
Smart-Warning-System/
├── app/
│   ├── Console/Commands/
│   │   └── CheckBahayaStatus.php      # Artisan: sensor:check-bahaya (auto-scan cron)
│   └── Services/
│       └── NotificationService.php     # Telegram Bot API sender
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php              # Layout utama (sidebar + navbar)
│   ├── dashboard.blade.php            # Halaman utama (gauge, stats, notifikasi)
│   ├── login.blade.php                # Halaman login
│   ├── peringatan.blade.php           # Halaman daftar peringatan
│   ├── riwayat.blade.php              # Halaman riwayat data sensor
│   ├── perangkat.blade.php            # Halaman manajemen perangkat
│   └── pengaturan.blade.php           # Halaman pengaturan
├── routes/
│   └── web.php                        # Semua routes (termasuk API trigger-notif)
├── .env                               # Konfigurasi DB + Telegram token
├── composer.json                      # PHP dependencies (Laravel 12)
├── package.json                       # Node dependencies (Tailwind 4, Vite 7)
└── vite.config.js                     # Vite build config
```

---

## 7. Konfigurasi Aktif (.env)

```env
APP_URL=http://Smart-Warning-System.test

DB_CONNECTION=mysql
DB_HOST=10.21.106.6
DB_PORT=3306
DB_DATABASE=iot_db
DB_USERNAME=angga
DB_PASSWORD=Angga100

TELEGRAM_BOT_TOKEN=8568893986:AAHFOiXAkE5MCePxRFCZfQbHJ9YQTSX9w7s
TELEGRAM_CHAT_ID=1248624904
```

> **Catatan:** DB Host pernah berubah beberapa kali sesuai jaringan kampus/lab. IP terakhir: `10.21.106.6`. Sebelumnya pernah: `10.65.140.6`.

---

## 8. Halaman Web Dashboard

| Route | Halaman | Deskripsi |
|---|---|---|
| `/` | Login | Halaman login sederhana |
| `/dashboard` | Dashboard | Gauge real-time (status air + cuaca), stats cards, daftar notifikasi terkini |
| `/peringatan` | Peringatan | Tabel semua data BAHAYA + WASPADA |
| `/riwayat` | Riwayat | Tabel semua data sensor (200 terakhir) |
| `/perangkat` | Perangkat | Manajemen device ESP32 |
| `/pengaturan` | Pengaturan | Settings aplikasi |

---

## 9. Fitur Dashboard UI/UX (Versi Final)

Dashboard telah melalui **overhaul UI/UX premium** dengan fitur berikut:

- **Dual Gauge Indicator:** Dua lingkaran besar berdampingan — Status Air (kiri) dan Kondisi Cuaca (kanan) — dengan animasi pulse "hidup"
- **Ikon Dinamis Cuaca:** Ikon hujan/matahari berubah otomatis sesuai data sensor
- **Glassmorphism Notification List:** Daftar notifikasi dengan efek transparan, border warna sesuai status, dan hover effect
- **Premium Stats Cards:** 3 kartu (Perangkat, Data Masuk, Peringatan) dengan ikon vektor, glow background, dan efek 3D hover
- **In-App Toast Notification:** Popup melayang real-time saat status BAHAYA/WASPADA terdeteksi, dengan warna gradasi kontras
- **Browser Push Notification:** Alert sound + notifikasi OS saat tab tidak aktif

---

## 10. Fitur Notifikasi Telegram

### Mekanisme Pengiriman
1. **Real-time via API** — Setiap kali ESP32 mengirim data bahaya ke `/api/trigger-notif`, Laravel langsung kirim ke Telegram
2. **Auto-Scan Cron (Backup)** — Artisan command `sensor:check-bahaya` polling DB setiap menit untuk mendeteksi data BAHAYA/HUJAN yang mungkin terlewat

### Anti-Spam / Cooldown
- Cooldown 10 detik per stasiun per level status yang sama
- Cooldown di-bypass jika terjadi **eskalasi status** (misal: WASPADA → BAHAYA)

### Format Pesan Telegram
```
🚨 *PERINGATAN DINI!* 🚨

Status: *BAHAYA*
Ketinggian Air (Jarak): 18 cm
Kondisi Cuaca: HUJAN
Waktu: 2026-05-07 22:30:00

Harap segera ambil tindakan!
```

---

## 11. Kronologi Percakapan (Ringkas)

### Sesi 1 — 10 Apr 2026: Inisiasi & Finalisasi Dashboard Awal
- Setup awal proyek Laravel + koneksi ESP32
- Pembuatan dashboard dengan gauge chart, parsing data sensor
- Implementasi real-time polling (AJAX setiap 1 detik)
- Penambahan In-App Toast Notification untuk alert BAHAYA

### Sesi 2 — 28 Apr 2026: Integrasi Telegram + Fix Login
- **Fix:** Konfigurasi `.env` DB host yang salah (diubah ke IP remote MySQL)
- **Fitur Baru:** Integrasi Telegram Bot API (`NotificationService.php`)
- **Fitur Baru:** Endpoint `POST /api/trigger-notif` untuk ESP32
- **Fitur Baru:** Auto-Scan command (`sensor:check-bahaya`) sebagai backup notifikasi
- Panduan setup Telegram Bot Token + Chat ID

### Sesi 3 — 3 Mei 2026: Dashboard UI/UX Overhaul + Navigasi Fix
- **Perombakan besar:** Desain dashboard dipoles premium (gauge dual, glassmorphism, stats cards 3D, animasi pulse)
- **Fix:** Link navigasi "Peringatan" dan "Riwayat" yang error
- **Fix:** Konfigurasi IP jaringan yang berubah

### Sesi 4 — 5 Mei 2026: Git Collaboration + Dokumen Arsitektur
- **Setup Git:** Dual remote (origin pribadi + collaborator) untuk kolaborasi
- **Fix:** Error autentikasi Git saat push ke repository collaborator
- **Dokumen:** Pembuatan dokumen arsitektur formal (`full_revised_architecture.md`) untuk kebutuhan laporan akademik
- **Dokumen:** Lampiran tabel spesifikasi API, threshold, diagram Mermaid, dan template hasil UAT
- **Review:** Validasi kesesuaian implementasi vs dokumen perancangan (~95% sinkron)

### Sesi 5 — 6 Mei 2026: Fix Routing Adminer
- **Fix:** Dashboard ter-redirect ke Adminer (database manager) akibat konflik konfigurasi Herd
- Solusi: perbaikan konfigurasi Nginx/Herd agar domain `smart-warning-system.test` mengarah ke Laravel, bukan Adminer

### Sesi 6 — 7 Mei 2026: Fix Database Connection Timeout
- **Issue:** Koneksi ke MySQL remote `10.21.106.6` timeout
- **Diagnosa:** Troubleshoot konektivitas jaringan (port 3306)
- **Solusi:** Verifikasi bahwa laptop terhubung ke jaringan yang benar dan DB host reachable

---

## 12. Issue yang Pernah Terjadi & Solusinya

| Issue | Penyebab | Solusi |
|---|---|---|
| DB Connection Timeout | IP DB host berubah / beda jaringan | Update `DB_HOST` di `.env` sesuai IP jaringan aktif |
| Dashboard redirect ke Adminer | Konfigurasi Herd/Nginx konflik | Perbaiki konfigurasi site di Herd |
| Git push ditolak | Permission denied ke repo collaborator | Setup SSH key atau gunakan token |
| `php artisan serve` gagal listen | Port 8000-8008 sudah dipakai | Kill proses lama atau gunakan Herd saja |
| Telegram notifikasi spam | Tidak ada rate limiting | Implementasi cooldown 10 detik |
| ESP32 gagal kirim HTTP ke Telegram langsung | Firewall kampus blokir | Pindahkan pengiriman Telegram ke sisi Laravel backend |

---

## 13. Catatan Penting untuk Konteks Lanjutan

1. **Web server lokal pakai Laravel Herd** (bukan `php artisan serve`). Domain: `http://smart-warning-system.test`
2. **Database MySQL ada di server remote**, bukan localhost. IP bisa berubah tergantung jaringan kampus.
3. **ESP32 mengirim data lewat HTTP POST** ke Laravel, bukan langsung ke MySQL.
4. **Semua route ada di `routes/web.php`** (termasuk API), bukan di `routes/api.php`.
5. **Sudah ada dokumen arsitektur formal** di `full_revised_architecture.md` dan `lampiran_gambar_tabel.md` — ini untuk kebutuhan laporan/skripsi.
6. **Tailwind CSS v4** digunakan (bukan v3), yang memiliki syntax berbeda.
7. **Alpine.js** dimuat via CDN di layout `app.blade.php`.

---

*File ini dibuat otomatis pada 8 Mei 2026 sebagai grounding context untuk melanjutkan pengembangan di sesi percakapan baru.*
