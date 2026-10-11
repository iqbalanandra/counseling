<div align="center">

# 🌿 Konseling-Yu

### _Sistem Informasi Bimbingan & Konseling Sekolah Berbasis Web_

Transformasi digital layanan konseling sekolah yang modern, aman, transparan, dan terintegrasi untuk mendukung perkembangan karakter serta potensi peserta didik secara holistik.

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-3.0-9553E9?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-3178C6?style=for-the-badge&logo=typescript&logoColor=white)](https://www.typescriptlang.org)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

[Fitur Utama](#-fitur-utama) •
[Hak Akses & Role](#-matriks-peran--hak-akses) •
[Teknologi](#-teknologi-yang-digunakan) •
[Cara Penginstallan](#-panduan-instalasi-sistem) •
[Akun Demo](#-akun-demo-bawaan) •
[Arsitektur Proyek](#-arsitektur-proyek)

---

</div>

## 📖 Tentang Konseling-Yu

**Konseling-Yu** adalah platform web bimbingan konseling komprehensif yang dirancang untuk menjembatani komunikasi antara **Siswa**, **Guru BK (Bimbingan Konseling)**, **Wali Kelas**, dan **Kepala Sekolah**.

Sistem ini memfasilitasi pengajuan konseling mandiri yang nyaman bagi siswa, pemantauan kedisiplinan dan tata tertib, pencatatan rekam jejak prestasi, serta menjaga kerahasiaan konseling (_privacy & confidentiality_) dengan standar etika bimbingan konseling profesional.

Dibangun dengan arsitektur **Domain-Driven Design (DDD)** dan **Clean Architecture**, Konseling-Yu menjamin keteraturan kode, keamanan data siswa, dan kemudahan dalam pengembangan berkelanjutan.

---

## ✨ Fitur Utama

### 🤝 1. Layanan Konseling Digital Terpadu (End-to-End Workflow)

- **4 Bidang Bimbingan Komprehensif**:
    - 🌸 **Bimbingan Pribadi**: Pendampingan emosional, manajemen stres, konsep diri, dan kepercayaan diri.
    - 👥 **Bimbingan Sosial**: Resolusi konflik pertemanan, hubungan interpersonal, dan adaptasi lingkungan.
    - 📚 **Bimbingan Belajar**: Strategi belajar efektif, kendala akademik, dan manajemen waktu.
    - 🧭 **Bimbingan Karier**: Pemetaan minat & bakat, penjurusan jenjang lanjutan (SMA/SMK), dan cita-cita masa depan.
- **Pengajuan Mandiri oleh Siswa**: Siswa dapat memilih tanggal, waktu luang yang fleksibel, serta menuliskan keluhan/cerita awal dengan nyaman.
- **Persetujuan & Penjadwalan Ulang (Reschedule)**: Guru BK dapat memverifikasi, menyetujui, maupun menjadwalkan ulang sesi konseling disertai catatan resmi.
- **Pencatatan Hasil Sesi (Session Recording)**: Rekam jejak hasil konseling, diagnosis permasalahan, saran tindak lanjut, dan evaluasi berkala.
- 🔒 **Jaminan Privasi Tingkat Tinggi (Confidentiality Protection)**: Catatan rahasia/rekam sensitif hanya dapat diakses secara eksklusif oleh Guru BK yang memiliki wewenang khusus (`counseling.view_private`).

### ⚖️ 2. Tata Tertib & Pelanggaran Siswa Berpoin

- **Kategorisasi Pelanggaran**: Pengelompokan pelanggaran berdasarkan tingkat bobot (Keterlambatan & Kehadiran, Kerapian & Seragam, Kedisiplinan & Ketertiban, serta Perilaku & Etika).
- **Akumulasi Poin Otomatis**: Perhitungan poin kumulatif per siswa secara otomatis untuk menentukan tindakan pembinaan atau surat pemanggilan orang tua.
- **Sanksi & Tindak Lanjut Terstruktur**: Rekam jejak pembinaan yang telah diberikan untuk menjamin evaluasi perilaku yang objektif.

### 🏆 3. Manajemen Apresiasi & Prestasi Siswa

- **Portofolio Prestasi Digital**: Pencatatan capaian siswa di berbagai bidang (Akademik, Olahraga, Seni & Budaya, Keagamaan, Kepemimpinan & Organisasi).
- **Poin Penghargaan Positif**: Poin prestasi yang dapat mengimbangi pemantauan karakter positif siswa di sekolah.
- **Riwayat Prestasi Terverifikasi**: Dokumentasi tingkat prestasi (Sekolah, Kecamatan, Kabupaten/Kota, Provinsi, hingga Nasional).

### 📊 4. Dashboard Interaktif Multi-Role

- **Widget Metrik Real-Time**: Ringkasan total siswa binaan, sesi konseling aktif, permohonan tertunda, dan statistik kasus.
- **Visualisasi Grafik**: Tren pengajuan konseling per bulan, sebaran bidang masalah siswa, serta grafik poin pelanggaran vs prestasi.
- **Tampilan Khusus Peran**: Setiap peran memiliki tampilan antarmuka yang disesuaikan secara dinamis dengan tugas dan wewenangnya.

### 📄 5. Laporan & Cetak PDF Resmi

- **Filter Dinamis**: Filter laporan berdasarkan rentang tanggal, tahun akademik, kelas, jenis konseling, maupun status penanganan.
- **Ekspor Dokumen PDF Siap Cetak**: Menggunakan engine [Barryvdh Laravel DomPDF](https://github.com/barryvdh/laravel-dompdf) dengan kop resmi sekolah dan tanda tangan verifikasi.

### 🛡️ 6. Keamanan, RBAC Dinamis & Audit Log

- **Role-Based Access Control (RBAC)**: Pengaturan izin (_permissions_) secara modular yang terbagi dalam grup fungsional.
- **Audit Trail Lengkap**: Pencatatan log aktivitas sistem (siapa melakukan apa, modul yang diakses, timestamp, dan IP address) untuk menjamin akuntabilitas data.
- **Sistem Notifikasi Dalam Aplikasi**: Notifikasi instan saat pengajuan disetujui, jadwal diubah, atau status konseling diperbarui.

### 🏫 7. Manajemen Profil & Identitas Sekolah (Superadmin)

- **Kustomisasi Identitas Instansi**: Superadmin dapat mengubah nama sekolah, nama portal aplikasi, slogan, NPSN, dan nama kepala sekolah secara fleksibel.
- **Unggah Logo Resmi**: Mendukung logo kustom sekolah (format PNG, JPG, WEBP, SVG) dengan pratinjau langsung yang terintegrasi ke seluruh halaman publik dan sidebar.
- **Alamat & Kanal Kontak Terpadu**: Pengaturan alamat lengkap, telepon, WhatsApp layanan BK, email resmi, dan tautan website yang langsung tampil di halaman depan (_Welcome_).

---

## 👥 Matriks Peran & Hak Akses

| Peran (Role)          | Ruang Lingkup & Wewenang Utama                                                                                                          |
| :-------------------- | :-------------------------------------------------------------------------------------------------------------------------------------- |
| **👑 Superadmin**     | Hak akses penuh: manajemen profil & logo sekolah, manajemen pengguna, konfigurasi role & permission, audit logs, dan pengaturan sistem. |
| **🧑‍🏫 Guru BK**        | Manajemen pengajuan bimbingan, persetujuan jadwal, pencatatan rekam rahasia (_confidential_), laporan BK, input pelanggaran & prestasi. |
| **🏫 Kepala Sekolah** | Monitoring agregat statistik sekolah, evaluasi tren permasalahan siswa, serta rekapitulasi laporan umum (tanpa rekam rahasia).          |
| **👨‍🏫 Wali Kelas**     | Pemantauan siswa kelas binaan, melihat riwayat konseling non-rahasia, pencatatan pelanggaran, dan input prestasi siswa kelas.           |
| **🎒 Siswa**          | Mengajukan permohonan konseling daring, memantau status jadwal, melihat riwayat prestasi dan poin tata tertib pribadi.                  |

---

## 💻 Teknologi yang Digunakan

| Komponen               | Teknologi                | Keterangan                                                               |
| :--------------------- | :----------------------- | :----------------------------------------------------------------------- |
| **Backend Framework**  | **Laravel 13**           | Arsitektur Domain-Driven Design (DDD) & Clean Architecture               |
| **Frontend Framework** | **Vue.js 3.5**           | Script Setup, TypeScript, & Composition API                              |
| **Monolith Bridge**    | **Inertia.js v3**        | Single Page Application (SPA) modern tanpa API boilerplate               |
| **CSS Styling**        | **Tailwind CSS v4**      | Modern styling system dengan animasi halus dan tema responsif            |
| **UI Components**      | **Reka UI & Lucide Vue** | Accessible headless primitives & icon set modern                         |
| **Autentikasi**        | **Laravel Fortify**      | Headless authentication backend yang kokoh & aman                        |
| **Routing Helper**     | **Laravel Wayfinder**    | Autogenerasi fungsi routing TypeScript dari route Laravel                |
| **PDF Engine**         | **DomPDF**               | Ekspor laporan berkas resmi sekolah                                      |
| **Database Engine**    | **MySQL / MariaDB**      | Database relasional untuk penyimpanan data terstruktur & relasi kompleks |
| **Testing**            | **Pest PHP**             | Framework pengujian unit & integrasi yang ekspresif                      |

---

## 🚀 Panduan Instalasi Sistem (Ramah Pemula)

Ikuti panduan berikut langkah demi langkah untuk menjalankan aplikasi **Konseling-Yu** di komputer lokal (_localhost_) Anda.

---

### 💻 Tahap 1: Persiapan Aplikasi di Komputer Anda (Prerequisites)

Sebelum mengunduh proyek ini, pastikan komputer atau laptop Anda sudah terpasang aplikasi-aplikasi pendukung berikut:

1. **Web Server & Database (XAMPP atau Laragon)**
    - **Laragon** _(Sangat direkomendasikan untuk pengguna Windows)_: Unduh di [laragon.org](https://laragon.org/download/). Sudah otomatis menyediakan PHP, MySQL, Apache, dan Composer.
    - **XAMPP**: Unduh di [apachefriends.org](https://www.apachefriends.org/) (Pilih versi dengan **PHP 8.3** atau lebih baru).
    - 👉 _Pastikan service **Apache** dan **MySQL** sudah dinyalakan (*Running*) sebelum melangkah ke tahap berikutnya._

2. **PHP `>= 8.3`**
    - Pastikan ekstensi berikut sudah aktif di file `php.ini` Anda:
      `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `gd`.

3. **Composer (Manajer Paket PHP)**
    - Unduh dan pasang dari [getcomposer.org](https://getcomposer.org/download/).
    - Cek di terminal/CMD: `composer -v`

4. **Node.js & NPM (Runtime Javascript)**
    - Unduh versi **LTS (minimal v20.x atau terbaru)** dari [nodejs.org](https://nodejs.org/).
    - Cek di terminal/CMD: `node -v` dan `npm -v`

5. **Git**
    - Unduh dari [git-scm.com](https://git-scm.com/).
    - Cek di terminal/CMD: `git -v`

---

### 🛠️ Tahap 2: Siapkan Database MySQL

Sebelum menghubungkan sistem, buat terlebih dahulu wadah database di MySQL:

1. Buka peramban (browser) dan buka **phpMyAdmin** di:
   👉 `http://localhost/phpmyadmin` (atau gunakan database client seperti _HeidiSQL_ / _DBeaver_).
2. Klik menu **Databases** (Basis Data).
3. Buat database baru dengan nama:
    ```text
    counseling
    ```
4. Pilih collation bawaan (misal `utf8mb4_general_ci` atau `utf8mb4_unicode_ci`), lalu klik **Create**.

---

### 📥 Tahap 3: Langkah-Langkah Pemasangan Sistem

Buka **Terminal** (PowerShell / Command Prompt / Git Bash), lalu jalankan perintah berikut secara berurutan:

#### 1. Clone Repository

Unduh source code proyek dari GitHub ke komputer Anda:

```bash
git clone https://github.com/iqbalanandra/counseling.git
```

Masuk ke dalam folder proyek:

```bash
cd counseling
```

#### 2. Salin File Konfigurasi `.env`

Duplikasi file template konfigurasi menjadi file `.env`:

- **Windows (PowerShell / Command Prompt):**
    ```bash
    copy .env.example .env
    ```
- **Linux / macOS / Git Bash:**
    ```bash
    cp .env.example .env
    ```

#### 3. Sesuaikan Konfigurasi Database MySQL di File `.env`

Buka file `.env` yang baru dibuat menggunakan Text Editor (seperti VS Code atau Notepad), lalu cari bagian konfigurasi database dan ubah menjadi:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=counseling
DB_USERNAME=root
DB_PASSWORD=
```

> 💡 **Catatan:**
>
> - Jika Anda menggunakan **XAMPP / Laragon standar**, username adalah `root` dan password dikosongkan (`DB_PASSWORD=`).
> - Jika MySQL Anda memiliki kata sandi khusus, masukkan password tersebut pada baris `DB_PASSWORD=`.

#### 4. Pasang Dependensi Backend (PHP)

Jalankan perintah ini untuk mengunduh semua pustaka Laravel yang diperlukan:

```bash
composer install
```

#### 5. Pasang Dependensi Frontend (Node.js)

Jalankan perintah ini untuk mengunduh pustaka Vue.js, Tailwind CSS, dan icon:

```bash
npm install
```

#### 6. Buat Kunci Enkripsi Aplikasi (App Key)

Generate security key untuk sistem Laravel:

```bash
php artisan key:generate
```

#### 7. Eksekusi Migrasi & Seeder ke Database MySQL

Jalankan perintah ini untuk membuat struktur tabel di MySQL dan mengisi data master serta akun demo secara otomatis:

```bash
php artisan migrate --seed
```

_(Tunggu hingga proses migrasi dan seeding selesai dengan keterangan SUCCESS)_.

#### 8. Buat Tautan Simbolik Berkas (Storage Link)

Jalankan perintah ini untuk menghubungkan direktori penyimpanan ke folder publik agar file logo sekolah dan lampiran berkas dapat diakses secara publik:

```bash
php artisan storage:link
```

---

### 🌐 Tahap 4: Menjalankan Aplikasi

Jalankan seluruh server pengembangan lokal dengan satu perintah:

```bash
composer run dev
```

> 📌 **Info:** Perintah di atas akan secara otomatis mengaktifkan server **Laravel**, **Queue Worker**, dan **Vite Frontend** secara bersamaan.
>
> _Alternatif jika ingin membuka di 2 jendela terminal terpisah:_
>
> - Terminal 1: `php artisan serve`
> - Terminal 2: `npm run dev`

Buka browser Anda dan akses alamat:
👉 **[http://localhost:8000](http://localhost:8000)**

Silakan masuk (_login_) menggunakan salah satu akun demo di bawah ini untuk mulai menjelajahi sistem!

---

## 🔑 Akun Demo Bawaan

Setelah menjalankan proses seeding (`--seed`), Anda dapat langsung mencoba masuk ke sistem menggunakan akun-akun demo berikut dengan kata sandi yang sama:

> 🔐 **Kata Sandi (Password) untuk Semua Akun:** `password`

| Peran (Role)       | Alamat Email             | Nama Pengguna               | Tugas & Hak Akses                                      |
| :----------------- | :----------------------- | :-------------------------- | :----------------------------------------------------- |
| **Superadmin**     | `superadmin@example.com` | Administrator Utama         | Akses penuh manajemen user, role matrix, dan audit log |
| **Guru BK**        | `bk@example.com`         | Dra. Hj. Nurjanah, M.Pd     | Verifikasi konseling, rekam hasil rahasia, laporan     |
| **Kepala Sekolah** | `kepala@example.com`     | Drs. Bambang Sutrisno, M.Pd | Monitoring agregat sekolah & rekapitulasi laporan      |
| **Wali Kelas**     | `walikelas@example.com`  | Ahmad Fauzi, S.Pd (Wali 7A) | Monitoring kelas 7A, rekam pelanggaran & prestasi      |
| **Siswa**          | `siswa@example.com`      | Ayu Lestari (Kelas 7A)      | Pengajuan konseling mandiri & cek riwayat bimbingan    |

---

## 🏗️ Arsitektur Proyek

Sistem ini menerapkan prinsip **Clean Architecture** yang terbagi rapi ke dalam lapisan berikut:

```
konseling-yu/
├── app/
│   ├── Domain/                 # Enterprise Business Rules & Entities
│   │   ├── Counseling/         # Domain Konseling, Permohonan, & Sesi
│   │   ├── SchoolClass/        # Domain Struktur Kelas & Rombel
│   │   ├── Student/            # Domain Siswa
│   │   └── Violation/          # Domain Tata Tertib & Prestasi
│   ├── Application/            # Use Cases, DTOs, & Services
│   ├── Infrastructure/         # Implementasi Database (Eloquent Repositories, PDF, Mail)
│   ├── Presentation/           # HTTP Controllers, Form Requests, View Models
│   └── Models/                 # Eloquent ORM Models
├── database/
│   ├── migrations/             # Skema Struktur Tabel Database
│   └── seeders/                # Data Master & Dummy Pengguna
├── resources/
│   ├── js/
│   │   ├── Components/         # Komponen UI Vue Reusable (Buttons, Dialogs, Tables)
│   │   ├── Layouts/            # App Layout, Authenticated Layout, Admin Layout
│   │   ├── Pages/              # Halaman Inertia Vue (Counseling, Students, Reports, dll.)
│   │   └── types/              # Deklarasi Type Definition TypeScript
│   └── views/                  # Blade Templates (Root template & PDF render)
└── routes/
    ├── web.php                 # Rute Aplikasi & Otorisasi Middleware
    └── auth.php                # Rute Otentikasi Fortify
```

---

## 🛠️ Perintah Berguna (Developer Cheatsheet)

```bash
# Menjalankan seluruh environment dev (Laravel + Vite + Queue)
composer run dev

# Menjalankan unit & feature testing (Pest)
composer test
# atau
php artisan test

# Melakukan perbaikan formatting kode PHP (Laravel Pint)
composer run lint

# Melakukan pengecekan tipe TypeScript & Vue
npm run types:check

# Kompilasi aset frontend untuk keperluan produksi
npm run build
```

---

## 🤝 Berkontribusi

Kontribusi selalu disambut dengan baik! Jika Anda memiliki saran perbaikan, fitur baru, atau menemukan kendala (bug):

1. **Fork** repository ini
2. Buat branch fitur baru (`git checkout -b fitur/FiturKeren`)
3. Commit perubahan Anda (`git commit -m 'feat: Tambahkan FiturKeren'`)
4. Push ke branch Anda (`git push origin fitur/FiturKeren`)
5. Buat **Pull Request**

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah lisensi terbuka [MIT License](LICENSE).
