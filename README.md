# LCU Certification and Training Monitoring Dashboard

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com)

Sistem Aplikasi Pemantauan Masa Berlaku Sertifikasi dan Pelatihan Kedinasan Pegawai berbasis web. Dikembangkan untuk Learning Center Unit (LCU) guna mengotomasi audit masa berlaku sertifikat, pengiriman notifikasi pengingat berjenjang, katalog modul pelatihan, serta manajemen impor dan ekspor data terstandarisasi.

---

## Fitur Utama

### 1. Monitoring Dashboard Interaktif
- KPI Real-Time: Ringkasan total pegawai, total sertifikasi, sertifikasi aktif (> 60 hari), akan expired (<= 60 hari), dan expired.
- Grafik Distribusi: Visualisasi proporsi masa berlaku dengan Chart.js donat interaktif.
- Highlight Modul: Ringkasan modul pelatihan dengan pemegang terbanyak beserta tautan ke direktori lengkap.
- Audit Log Terkini: Menampilkan riwayat perpanjangan sertifikasi secara langsung pada dashboard.

### 2. Direktori Katalog Jenis Sertifikasi (`/certificate-types`)
- Menu dedikasi pada sidebar untuk melihat seluruh 53 jenis modul pelatihan kedinasan (seperti Human Factor, Safety Management System, GMF Quality System, EASA Part 145, CASR Part 145, Fuel Tank Safety, dll).
- Indikator status badge warna (Aktif, Warning, Expired) untuk tiap jenis modul pelatihan.
- Halaman detail per modul untuk melihat seluruh daftar pemegang modul terkait.

### 3. Manajemen Sertifikasi dan Audit Trail (`/certifications`)
- Pencarian Kata Kunci Fleksibel: Pencarian substring nama pegawai, nomor pegawai, maupun nama sertifikasi secara menyeluruh.
- Unggah Berkas Bukti Fisik: Dukungan upload berkas scan sertifikat asli (PDF / JPG / PNG hingga 10MB) dengan fitur preview dan download.
- Audit Log Perpanjangan: Setiap pembaruan tanggal expired otomatis mencatat tanggal lama, tanggal baru, akun penanggung jawab, dan catatan perpanjangan pada tabel database.

### 4. Impor dan Ekspor Data Excel/CSV Multi-Format
- Download Template CSV: Template standar siap pakai untuk memudahkan proses impor batch data baru.
- Format Matriks Asli: 1 baris per pegawai dengan seluruh kolom jenis pelatihan (sesuai struktur file excel dinas).
- Format Tabel Data Bersih: 1 baris per sertifikasi, diformat dengan UTF-8 BOM dan pemisah titik koma agar otomatis terpisah per kolom di Microsoft Excel.
- Logika Impor Cerdas: Otomatis memperbarui tanggal expired jika data sudah ada, atau membuat record baru jika belum ada.

### 5. Portal Pegawai Mandiri (Self-Service)
- Pegawai dapat login menggunakan Nomor Pegawai atau Email.
- Dashboard personal untuk memantau seluruh sertifikasi kompetensi miliknya, sisa hari aktif, status renewal, serta mengunduh berkas sertifikat yang telah diunggah.

### 6. Otomasi Email Pengingat Berjenjang (Queued Mail)
- Mendukung 4 tahapan milestone pengingat:
  - H-60: Notifikasi persiapan renewal (2 bulan sebelum expired).
  - H-30: Peringatan kedua renewal (1 bulan sebelum expired).
  - H-5: Peringatan kritis (5 hari sebelum expired).
  - H+5: Eskalasi sertifikat telah melewati masa berlaku.
- Terintegrasi dengan Laravel Queue (ShouldQueue) untuk pemrosesan pengiriman asynchronous.

---

## Stack Teknologi

- Backend: Laravel 11.x, PHP 8.2+
- Frontend: Blade Templating, Tailwind CSS, Alpine.js, Lucide Icons
- Visualisasi Data: Chart.js
- Database: MySQL / MariaDB
- Data Parser: Python 3 (openpyxl) untuk migrasi dataset awal

---

## Panduan Instalasi dan Menjalankan Proyek

### 1. Clone Repository
```bash
git clone https://github.com/ragepanz/certification-dashboard.git
cd certification-dashboard
```

### 2. Install Dependensi PHP dan JavaScript
```bash
composer install
npm install && npm run build
```

### 3. Konfigurasi Environment (`.env`)
Salin file konfigurasi environment:
```bash
cp .env.example .env
```
Sesuaikan parameter koneksi database Anda pada file `.env` yang baru dibuat, lalu buat application key:
```bash
php artisan key:generate
```

### 4. Migrasi Database dan Seeding Data Awal
Siapkan database pada server MySQL Anda, kemudian jalankan perintah migrasi dan seeder:
```bash
php artisan migrate --seed
```


### 5. Buat Symlink Storage Publik
Untuk menghubungkan penyimpanan berkas dokumen sertifikat:
```bash
php artisan storage:link
```

### 6. Jalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser pada: `http://127.0.0.1:8000`

---

## Hak Akses Pengguna

- **Superadmin (LCU)**: Memiliki hak akses penuh untuk pemantauan dashboard, pengelolaan data sertifikasi, riwayat audit trail, katalog modul, impor/ekspor data, serta pengiriman notifikasi pengingat.
- **Pegawai (Employee)**: Memiliki hak akses mandiri ke portal personal untuk memantau status masa berlaku sertifikasi miliknya sendiri serta mengunduh berkas bukti sertifikat.

---


## Menjalankan Scheduler dan Queue Email (Opsional)

Jika otomatisasi pengiriman email ingin dijalankan di production:
```bash
# Menjalankan command reminder secara manual:
php artisan certification:send-reminders

# Menjalankan background worker antrean email:
php artisan queue:work
```

---

## Struktur Direktori Utama

```text
├── app/
│   ├── Console/Commands/       # Command pengingat sertifikasi
│   ├── Http/Controllers/       # Controller Dashboard, Sertifikasi, Jenis Modul, Pegawai, Laporan
│   ├── Http/Middleware/        # Middleware autentikasi Role
│   ├── Mail/                   # Mailable template email pengingat
│   └── Models/                 # Model User, Certification, CertificationLog, ReminderLog
├── database/
│   ├── migrations/             # Struktur skema tabel database
│   ├── seeders/                # Database seeder dan import parser
│   └── excel_import.json       # Dataset 207 pegawai & 3.323 sertifikasi
├── resources/
│   └── views/
│       ├── auth/               # Tampilan login multi-identifier
│       ├── certificate_types/  # Katalog direktori jenis sertifikasi
│       ├── certifications/     # Manajemen CRUD sertifikasi & audit log
│       ├── dashboard/          # Dashboard superadmin & pegawai
│       ├── emails/             # Desain email pengingat masa berlaku
│       ├── employees/          # Manajemen data pegawai
│       ├── layouts/            # Layout utama aplikasi
│       └── reports/            # Pusat laporan, ekspor CSV, dan print preview
└── routes/
    ├── web.php                 # Rute web aplikasi
    └── console.php             # Konfigurasi scheduler
```

---

## Lisensi
Proyek ini dilisensikan di bawah [MIT License](LICENSE).
