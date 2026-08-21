# Rekap Kebutuhan Sistem

## Employee Certification Monitoring & Reminder Dashboard

### 1. Gambaran Umum

Employee Certification Monitoring & Reminder Dashboard merupakan aplikasi berbasis web yang digunakan oleh Learning Center Unit (LCU) untuk memonitor masa berlaku sertifikasi pegawai, mengelola data sertifikasi, serta mengirimkan pengingat otomatis kepada pegawai yang sertifikasinya akan atau telah melewati masa berlaku.

Tujuan utama sistem ini adalah memodernisasi proses monitoring sertifikasi yang sebelumnya dilakukan menggunakan Microsoft Excel menjadi sistem terpusat yang lebih mudah dikelola, dipantau, dan dilaporkan.

---

## 2. Tujuan Sistem

* Menghilangkan ketergantungan terhadap monitoring menggunakan Excel.
* Memusatkan data sertifikasi pegawai dalam satu sistem.
* Membantu LCU memantau sertifikasi yang mendekati masa expired.
* Mengirimkan reminder otomatis kepada pegawai.
* Menyediakan laporan dan dashboard monitoring secara real-time.
* Mengurangi risiko sertifikasi yang terlewat masa berlakunya.

---

## 3. Pengguna Sistem

### Superadmin (LCU)

Memiliki akses untuk:

* Login ke sistem.
* Melihat seluruh data pegawai.
* Melihat seluruh data sertifikasi.
* Mengelola data sertifikasi pegawai.
* Memperbarui tanggal expired sertifikasi.
* Melihat dashboard monitoring.
* Melihat riwayat perubahan sertifikasi.
* Mengakses laporan.

### Pegawai

Memiliki akses untuk:

* Login ke sistem.
* Melihat profil pribadi.
* Melihat sertifikasi yang dimiliki.
* Melihat status sertifikasi pribadi.
* Menerima reminder melalui email.

Pegawai tidak dapat melihat data pegawai lain.

---

## 4. Sumber Data

### Tahap Awal

Data berasal dari file Excel yang digunakan perusahaan saat ini.

Proses:

```text
Excel Existing
      ↓
Import Data Awal
      ↓
Database Sistem
```

### Setelah Go-Live

Data dikelola langsung melalui dashboard dan tidak lagi bergantung pada Excel sebagai media utama monitoring.

---

## 5. Data Pegawai

Informasi yang saat ini diketahui:

* Nomor Pegawai
* Nama Pegawai
* Email
* Unit

Masih menunggu konfirmasi apakah terdapat atribut tambahan seperti:

* Jabatan
* Lokasi Kerja
* Status Pegawai

---

## 6. Data Sertifikasi

Satu pegawai dapat memiliki lebih dari satu sertifikasi.

Data yang disimpan:

* Nama Sertifikasi
* Tanggal Terbit
* Tanggal Expired

Sistem tidak menyimpan:

* File PDF sertifikat
* Gambar sertifikat
* Informasi vendor/penerbit sertifikasi

---

## 7. Dashboard Monitoring

Dashboard menampilkan informasi utama berupa:

### KPI

* Total Pegawai
* Total Sertifikasi
* Sertifikasi Akan Expired
* Sertifikasi Expired

### Monitoring Sertifikasi

Menampilkan:

* Nomor Pegawai
* Nama Pegawai
* Unit
* Nama Sertifikasi
* Tanggal Terbit
* Tanggal Expired
* Sisa Hari Menuju Expired
* Status Sertifikasi

### Fitur Filter

* Nama Pegawai
* Unit
* Sertifikasi
* Status Sertifikasi

---

## 8. Reminder & Notification

Reminder dikirim kepada pegawai melalui email.

Ketentuan:

* H-5 sebelum tanggal expired.
* H+5 setelah tanggal expired.

Dashboard juga menyediakan notifikasi untuk membantu LCU memonitor sertifikasi yang membutuhkan perhatian.

---

## 9. Pengelolaan Sertifikasi

LCU dapat:

* Melihat data sertifikasi.
* Mengubah tanggal expired sertifikasi.
* Melakukan perpanjangan masa berlaku sertifikasi dengan memperbarui tanggal expired.

Perubahan dilakukan langsung pada data sertifikasi yang sudah ada.

---

## 10. Audit Log & Riwayat Perubahan

Setiap perubahan data sertifikasi dicatat.

Contoh informasi yang disimpan:

* Tanggal perubahan
* Pengguna yang melakukan perubahan
* Tanggal expired lama
* Tanggal expired baru

Riwayat perubahan dapat dilihat dari halaman detail sertifikasi atau detail pegawai.

---

## 11. Authentication

Fitur autentikasi yang direncanakan:

* Login
* Ubah Password
* Reset Password

---

## 12. Laporan

Laporan dapat difilter berdasarkan:

* Nama Pegawai
* Unit
* Sertifikasi
* Status Sertifikasi

Format export masih menunggu konfirmasi:

* Excel
* PDF

---

## 13. Requirement yang Masih Menunggu Konfirmasi

1. Apakah Superadmin dapat mengelola akun pengguna (User Management)?
2. Definisi status "Akan Expired" menggunakan berapa hari?
3. Apakah data pegawai dapat dikelola melalui dashboard?
4. Apakah Import Excel masih diperlukan setelah sistem berjalan?
5. Atribut tambahan data pegawai yang perlu ditampilkan.
6. Mekanisme reminder email (sekali atau berulang).
7. Cakupan audit log selain perubahan sertifikasi.
8. Format export laporan yang dibutuhkan.
9. Detail implementasi histori perubahan sertifikasi.

---

## 14. Ruang Lingkup Sistem

### In Scope

* Login pengguna.
* Dashboard monitoring sertifikasi.
* Monitoring masa berlaku sertifikasi.
* Reminder email otomatis.
* Pengelolaan tanggal expired sertifikasi.
* Audit log perubahan sertifikasi.
* Pencarian dan filter data.
* Laporan dan export data.
* Hak akses Superadmin dan Pegawai.

### Out of Scope

* OCR dokumen.
* Upload PDF sertifikat.
* Penyimpanan gambar sertifikat.
* Integrasi HRIS.
* Integrasi sistem eksternal.
* Vendor sertifikasi.
* Approval workflow.
* Multi-level role selain yang telah ditentukan.
