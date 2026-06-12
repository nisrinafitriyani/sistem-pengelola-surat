# Pengelolaan Surat Penawaran dan Persetujuan (IMG)

Aplikasi berbasis web untuk mengelola alur kerja dokumen perusahaan mulai dari pembuatan Surat Penawaran hingga Invoice. Dibangun menggunakan framework **Laravel 12** dan **Filament PHP**.

## 🚀 Fitur Utama

1. **Manajemen Klien (Clients)**
   - Mendata informasi klien (Nama, Alamat, Kontak, PIC).
2. **Surat Penawaran Harga (Quotations)**
   - Pembuatan SPH (Surat Penawaran Harga) dengan format nomor surat otomatis.
   - Perhitungan otomatis sub-total dan total keseluruhan (termasuk Terbilang dalam Rupiah).
   - Cetak PDF Surat Penawaran lengkap dengan tanda tangan digital & stempel.
3. **Surat Persetujuan (Approvals / PO / WO)**
   - Mengelola status persetujuan dari Surat Penawaran.
4. **Surat Jalan (Delivery Notes)**
   - Pembuatan dokumen Surat Jalan untuk pengiriman barang/material berdasarkan data Persetujuan.
   - Cetak PDF Surat Jalan terintegrasi dengan tanda tangan penerima, driver, dan admin kantor.
5. **Berita Acara Serah Terima / BAST (Handovers)**
   - Dokumentasi serah terima pekerjaan/proyek.
   - Cetak PDF BAST.
6. **Invoice (Tagihan)**
   - Pembuatan dokumen tagihan berdasarkan proyek dan penawaran yang sudah disetujui.
   - Cetak PDF Invoice lengkap dengan detail pembayaran (Bank, No Rekening, dll) dan tanda tangan.

## 🛠️ Teknologi yang Digunakan

* **PHP 8.2+**
* **Laravel 12.x** - Framework PHP Backend
* **Filament PHP 5.x** - Admin Panel & TALL Stack (Tailwind, Alpine, Laravel, Livewire)
* **Barryvdh/Laravel-DomPDF** - Ekstensi untuk pembuatan file PDF
* **MySQL** - Database Relasional

## ⚙️ Panduan Instalasi (Development)

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi di lingkungan lokal:

1. **Clone repositori ini** (Jika menggunakan Git):
   ```bash
   git clone <url-repo-anda>
   cd "pengelolaan surat penawaran dan persetujuan"
   ```

2. **Install Dependensi Composer:**
   ```bash
   composer install
   ```

3. **Pengaturan File Environment:**
   Duplikat file `.env.example` menjadi `.env`, kemudian sesuaikan konfigurasi database Anda.
   ```bash
   cp .env.example .env
   ```

4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

5. **Symlink Storage:**
   Wajib dijalankan agar gambar tanda tangan dan stempel bisa diakses secara publik dan muncul di PDF.
   ```bash
   php artisan storage:link
   ```

6. **Migrasi dan Seeding Database:**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser pada `http://127.0.0.1:8000`. Untuk login ke panel admin, buka `http://127.0.0.1:8000/admin`.

## 📝 Catatan Penting untuk Template PDF

* Saat melakukan instalasi atau perpindahan server baru, pastikan untuk **selalu** menjalankan `php artisan storage:link` agar sistem bisa memuat gambar (stempel dan tanda tangan) di dalam file PDF.
* Template PDF didesain menggunakan HTML & Vanilla CSS inline agar kompatibel dengan sistem konversi DomPDF. Modifikasi tampilan PDF dapat dilakukan di folder `resources/views/pdf/`.

---
*Dibuat untuk kebutuhan administrasi dokumen internal PT. INDO MUTIARA GLOBAL.*
