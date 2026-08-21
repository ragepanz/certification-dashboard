# 🎓 LCU Certification & Training Monitoring Dashboard

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)

Sistem Aplikasi Pemantauan Masa Berlaku Sertifikasi dan Pelatihan Kedinasan Pegawai berbasis web. Dikembangkan khusus untuk **Learning Center Unit (LCU)** guna mengotomasi audit masa berlaku sertifikat, pengiriman notifikasi pengingat berjenjang, katalog modul pelatihan, hingga ekspor & impor data terstandarisasi.

---

## 🌟 Fitur Utama

### 1. 📊 Monitoring Dashboard Interaktif
- **KPI Real-Time**: Ringkasan total pegawai, total sertifikasi, sertifikasi aktif (> 60 hari), akan expired (≤ 60 hari), dan expired.
- **Grafik Distribusi**: Visualisasi proporsi masa berlaku dengan Chart.js donat interaktif.
- **Highlight Modul Terpopuler**: Ringkasan cepat modul pelatihan dengan pemegang terbanyak beserta pintasan ke direktori lengkap.
- **Audit Log Terkini**: Menampilkan 5 riwayat perpanjangan terakhir secara langsung di dashboard.

### 2. 🗂️ Direktori Katalog Jenis Sertifikasi (`/certificate-types`)
- Menu dedikasi di sidebar untuk melihat seluruh **53+ jenis modul pelatihan** kedinasan (misal: *Human Factor, Safety Management System, GMF Quality System, EASA Part 145, CASR Part 145, Fuel Tank Safety*, dll).
- Indikator status badge warna (🟢 Aktif, 🟡 Warning, 🔴 Expired) untuk tiap jenis modul.
- Halaman detail per modul untuk melihat seluruh daftar pemegang modul tersebut.

### 3. 📑 Manajemen Sertifikasi & Audit Trail (`/certifications`)
- **Pencarian Kata Kunci Fleksibel**: Pencarian substring nama pegawai, nomor pegawai, maupun nama sertifikasi tanpa pembatasan kaku.
- **Unggah Berkas Bukti Fisik**: Dukungan upload file scan sertifikat asli (PDF / JPG / PNG hingga 10MB) dengan fitur preview & download.
- **Audit Log Perpanjangan**: Setiap pembaruan tanggal expired otomatis mencatat tanggal lama, tanggal baru, user penanggung jawab, dan catatan perpanjangan di tabel `certification_logs`.

### 4. 📥 Impor & Ekspor Data Excel/CSV Multi-Format
- **Download Template CSV**: Template siap isi untuk memudahkan proses impor batch data baru.
- **2 Pilihan Format Ekspor**:
  1. **Format Matriks Asli (Excel Asli)**: 1 Baris = 1 Pegawai dengan 50+ kolom jenis pelatihan (persis layout file excel `Training Dinas TN.xlsx`).
  2. **Format Tabel Data Bersih**: 1 Baris = 1 Sertifikasi, diformat dengan UTF-8 BOM dan pemisah `;` agar langsung rapi di Microsoft Excel.
- **Logika Impor Cerdas**: Otomatis memperbarui tanggal expired jika data sudah ada, atau membuat record baru jika belum ada.

### 5. 👥 Portal Pegawai Mandiri (Self-Service)
- Pegawai dapat login menggunakan **Nomor Pegawai** atau **Email**.
- Dashboard khusus pegawai untuk memantau seluruh sertifikasi kompetensi miliknya, sisa hari aktif, status renewal, serta mengunduh berkas sertifikat yang telah diunggah.

### 6. 📧 Otomasi Email Pengingat Berjenjang (Queued Mail)
- Mendukung 4 tahapan milestone pengingat:
  - **H-60**: Notifikasi persiapan renewal (2 bulan sebelum expired).
  - **H-30**: Peringatan kedua renewal (1 bulan sebelum expired).
  - **H-5**: Peringatan kritis (5 hari sebelum expired).
  - **H+5**: Eskalasi sertifikat telah melewati masa berlaku.
- Terintegrasi dengan **Laravel Queue (`ShouldQueue`)** untuk pengiriman asynchronous tanpa membebani performa web server.

---

## 🛠️ Stack Teknologi

- **Backend**: Laravel 11.x, PHP 8.2+
- **Frontend**: Blade Templating, Tailwind CSS (Glassmorphism Dark Theme), Alpine.js, Lucide Icons
- **Chart & Visualisasi**: Chart.js
- **Database**: MySQL / MariaDB (Didukung Laragon / phpMyAdmin)
- **Data Parser**: Python 3 (openpyxl) untuk migrasi dataset awal

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

### 1. Clone Repository
```bash
git clone https://github.com/ragepanz/certification-dashboard.git
cd certification-dashboard
```

### 2. Install Dependensi PHP & JavaScript
```bash
composer install
npm install && npm run build
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database dan app key:
```ini
APP_NAME="LCU Certification Dashboard"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sertification
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
```

Generate application key:
```bash
php artisan key:generate
```

### 4. Migrasi Database & Seeding Data Awal
Buat database bernama `sertification` di MySQL/phpMyAdmin, kemudian jalankan:
```bash
php artisan migrate --seed
```
> **Catatan:** Seeder akan otomatis memproses file `database/excel_import.json` yang memuat 207 pegawai dan 3.323+ riwayat sertifikasi dari data dinas asli.

### 5. Buat Symlink Storage Publik
Untuk memastikan berkas sertifikat yang diunggah dapat diakses dan diunduh:
```bash
php artisan storage:link
```

### 6. Jalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser di: **`http://127.0.0.1:8000`**

---

## 🔑 Kredensial Login Bawaan

| Role | Username / Login Identifier | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Superadmin (LCU)** | `admin@lcu.com` | `password` | Akses penuh dashboard pemantauan, impor/ekspor, edit data, audit trail, dan kirim reminder |
| **Pegawai (Employee)** | Nomor Pegawai (cth: `533380`, `580791`, `783543`) atau Email | `password` | Akses portal personal untuk memantau status sertifikasi miliknya sendiri |

---

## 📅 Menjalankan Scheduler & Queue Email (Opsional)

Jika fitur otomatisasi email pengingat harian ingin diaktifkan di production:
```bash
# Menjalankan command reminder secara manual:
php artisan certification:send-reminders

# Menjalankan queue worker untuk pemrosesan antrean email:
php artisan queue:work
```

---

## 📁 Struktur Direktori Utama

```text
├── app/
│   ├── Console/Commands/       # Command cron pengingat sertifikasi
│   ├── Http/Controllers/       # Controller Dashboard, Sertifikasi, Jenis Modul, Pegawai, Laporan
│   ├── Http/Middleware/        # Middleware autentikasi Role Superadmin vs Pegawai
│   ├── Mail/                   # Mailable template email pengingat (H-60, H-30, H-5, H+5)
│   └── Models/                 # Model User, Certification, CertificationLog, ReminderLog
├── database/
│   ├── migrations/             # Struktur skema tabel database
│   ├── seeders/                # Database seeder dan import parser
│   └── excel_import.json       # Dataset 207 pegawai & 3.323 sertifikasi
├── resources/
│   └── views/
│       ├── auth/               # Tampilan login multi-identifier
│       ├── certificate_types/  # Katalog direktori 53+ jenis sertifikasi
│       ├── certifications/     # Manajemen CRUD sertifikasi & audit log
│       ├── dashboard/          # Dashboard superadmin & dashboard pegawai
│       ├── emails/             # Desain email HTML pengingat masa berlaku
│       ├── employees/          # Manajemen data pegawai
│       ├── layouts/            # Layout utama aplikasi (Sidebar navigation)
│       └── reports/            # Pusat laporan, ekspor CSV, dan print preview
└── routes/
    ├── web.php                 # Rute web aplikasi
    └── console.php             # Konfigurasi scheduler
```

---

## 📄 Lisensi
Proyek ini dilisensikan di bawah [MIT License](LICENSE).
