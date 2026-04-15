# Portofolio FT - Academic Course Portfolio Management System

Sistem Manajemen Portofolio Mata Kuliah untuk **Fakultas Teknik, Universitas Dian Nuswantoro**.

Aplikasi ini membantu dosen dalam membuat, mengelola, dan mengekspor portofolio perkuliahan yang mendokumentasikan seluruh siklus akademik, selaras denganCapaian Pembelajaran Lulusan (CPL), Indikator Pencapaian (PI), dan Capaian Pembelajaran Mata Kuliah (CPMK).

---

## ✨ Features

### Admin Master Data Management

- **Users** - CRUD pengguna (dosen, mahasiswa, admin) + Excel import
- **Kurikulum** - Manajemen tahun ajaran & nama kurikulum
- **Prodi** - Manajemen program studi
- **Mata Kuliah (MK)** - Katalog mata kuliah + Excel import
- **CPL** - Capaian Pembelajaran Lulusan + Excel import
- **PI** - Indikator Pencapaian + Excel import
- **Mapping MK-CPL-PI** - Relasi mata kuliah dengan capaian pembelajaran
- **Perkuliahan** - Penjadwalan perkuliahan (dosen + mata kuliah + kelas + semester)

### Portfolio Management (10-Step Wizard)

1. **Upload RPS** - Rencana Pembelajaran Semester
2. **Info MK** - Topik perkuliahan & mata kuliah prasyarat
3. **CPL & PI** - Review capaian pembelajaran (read-only, inherited dari mapping)
4. **CPMK & Sub-CPMK** - Definisi capaian pembelajaran tingkat mata kuliah
5. **Pemetaan** - Mapping relasi CPL → CPMK → Sub-CPMK
6. **Rancangan Asesmen** - Desain tugas, UTS, UAS dengan upload file
7. **Rancangan Soal** - Distribusi soal per asesmen per CPMK
8. **Pelaksanaan** - Upload kontrak kuliah, realisasi mengajar, kehadiran
9. **Hasil Asesmen** - Upload jawaban mahasiswa & lembar nilai
10. **Evaluasi** - Evaluasi & kesimpulan per CPMK

### PDF Export

- Generate portofolio lengkap dalam format PDF
- Cover page, daftar isi, tabel data terstruktur
- Merge file PDF eksternal (RPS, asesmen, jawaban, dll)

### Excel Import

- Import bulk data untuk Users, MK, CPL, PI, Mapping, Perkuliahan
- Template Excel tersedia untuk setiap modul

---

## 🛠 Tech Stack

| Layer                 | Technology                               |
| --------------------- | ---------------------------------------- |
| **Backend Framework** | CodeIgniter 4.7+                         |
| **Language**          | PHP 8.2+                                 |
| **Database**          | MySQL 5.7+                               |
| **Frontend**          | Bootstrap 5, jQuery, DataTables, Select2 |
| **PDF Generation**    | DomPDF, FPDI, TCPDF, PDFParser           |
| **Excel Processing**  | PhpSpreadsheet                           |
| **Charts**            | Chart.js                                 |

---

## 📋 Requirements

### PHP Extensions

- `intl`
- `mbstring`
- `json` (enabled by default)
- `mysqlnd` (for MySQL)
- `libcurl` (for HTTP requests)
- `gd` or `imagick` (for PDF/image processing)

### Software

- PHP >= 8.2
- Composer
- MySQL 5.7+ / MariaDB 10.3+
- Web Server (Apache/Nginx) or PHP built-in server

---

## 🚀 Installation

### 1. Clone Repository

```bash
git clone <repository-url>
cd portofolio-ft
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Setup

```bash
copy env .env
```

### 4. Configure `.env`

Edit file `.env` dan sesuaikan:

```env
app.baseURL = 'http://localhost:8080'

database.default.hostname = localhost
database.default.username = root
database.default.password =
database.default.database = portofolio_v2
database.default.DBDriver = MySQLi
```

---

## 🗄 Database Setup

### 1. Create Database

```sql
CREATE DATABASE portofolio_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

### 2. Import Database

Import SQL file yang tersedia (jika ada) atau jalankan migrations:

```bash
php spark migrate
```

### 3. Create Additional Tables

Jalankan SQL tambahan jika diperlukan:

```bash
# Contoh: tabel pemetaan
mysql -u root portofolio_v2 < database/pemetaan_table.sql
```

---

## ⚙️ Configuration

### Virtual Host (Recommended)

**Apache** (httpd-vhosts.conf):

```apache
<VirtualHost *:80>
    ServerName portofolio.test
    DocumentRoot "D:/laragon/www/portofolio-ft/public"
    <Directory "D:/laragon/www/portofolio-ft/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx**:

```nginx
server {
    listen 80;
    server_name portofolio.test;
    root D:/laragon/www/portofolio-ft/public;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

### Development Server

```bash
php spark serve
```

Akses: `http://localhost:8080`

---

## 📖 Usage

### Login

Buka `http://localhost:8080` atau domain yang sudah dikonfigurasi.
Login menggunakan **NPP** (Nomor Pokok Pegawai) dan **password**.

### Admin Dashboard

Setelah login sebagai admin, akses modul:

- `/admin/dashboard` - Dashboard utama
- `/admin/users` - Manajemen pengguna
- `/admin/kurikulum` - Manajemen kurikulum
- `/admin/prodi` - Manajemen program studi
- `/admin/mk` - Manajemen mata kuliah
- `/admin/cpl` - Manajemen CPL
- `/admin/pi` - Manajemen PI
- `/admin/mapping_cpl` - Mapping MK-CPL-PI
- `/admin/perkuliahan` - Manajemen perkuliahan

### Lecturer (Dosen)

Setelah login sebagai dosen:

- `/dashboard` - Dashboard portofolio
- `/admin/portofolio` - Daftar portofolio
- `/admin/portofolio/form/{id}` - Edit portofolio (multi-step wizard)
- `/cetak/{id}` - Preview & generate PDF portofolio

---

## 👥 User Roles

| Role      | Access Level                                              |
| --------- | --------------------------------------------------------- |
| **admin** | Full access - Kelola semua master data & semua portofolio |
| **dosen** | Create & edit portofolio untuk perkuliahan yang diampu    |

---

## 📝 Portfolio Mata Kuliah Steps

| Step | Nama                  | Deskripsi                                                 |
| ---- | --------------------- | --------------------------------------------------------- |
| 1    | **Upload RPS**        | Upload dokumen Rencana Pembelajaran Semester (PDF)        |
| 2    | **Info MK**           | Isi topik perkuliahan & mata kuliah prasyarat             |
| 3    | **CPL & PI**          | Review capaian pembelajaran (auto-populated dari mapping) |
| 4    | **CPMK & Sub-CPMK**   | Definisikan capaian pembelajaran tingkat mata kuliah      |
| 5    | **Pemetaan**          | Mapping relasi antara CPL, CPMK, dan Sub-CPMK             |
| 6    | **Rancangan Asesmen** | Desain asesmen (Tugas/UTS/UAS) + upload soal & rubrik     |
| 7    | **Rancangan Soal**    | Plan distribusi soal per asesmen per CPMK                 |
| 8    | **Pelaksanaan**       | Upload kontrak kuliah, realisasi mengajar, kehadiran      |
| 9    | **Hasil Asesmen**     | Upload jawaban mahasiswa & lembar nilai                   |
| 10   | **Evaluasi**          | Tulis evaluasi & kesimpulan per CPMK                      |

---

## 📁 Project Structure

```
portofolio-ft/
├── app/
│   ├── Config/
│   │   ├── Routes.php              # Route definitions
│   │   ├── Database.php            # Database configuration
│   │   └── ...
│   ├── Controllers/
│   │   ├── BaseController.php
│   │   ├── Login.php               # Authentication
│   │   ├── Portofolio.php          # Portfolio CRUD & wizard
│   │   ├── Cetak.php               # PDF generation
│   │   └── Admin/
│   │       ├── Dashboard.php
│   │       ├── UsersController.php
│   │       ├── KurikulumController.php
│   │       ├── ProdiController.php
│   │       ├── MKController.php
│   │       ├── CplController.php
│   │       ├── PiController.php
│   │       ├── MkCplPiController.php
│   │       └── PerkuliahanController.php
│   ├── Models/                     # 23 models
│   │   ├── PortofolioModel.php
│   │   ├── Users.php
│   │   ├── CPL.php
│   │   ├── Pi.php
│   │   ├── MK.php
│   │   ├── CPMK.php
│   │   ├── SubCPMK.php
│   │   ├── RPS.php
│   │   ├── RancanganAsesmen.php
│   │   ├── Pelaksanaan.php
│   │   ├── HasilAsesmen.php
│   │   ├── Evaluasi.php
│   │   └── ...
│   └── Views/
│       ├── template.php            # Admin layout
│       ├── login.php
│       ├── admin/
│       │   ├── dashboard.php
│       │   ├── users.php
│       │   ├── kurikulum.php
│       │   ├── portofolio/
│       │   │   ├── index.php       # Portfolio list
│       │   │   └── form.php        # Multi-step wizard (3222 lines)
│       │   └── cetak/
│       │       └── cetak-portofolio.php  # PDF template
│       └── ...
├── public/                         # Web root
│   └── index.php
├── writable/                       # Uploads, logs, cache
│   └── uploads/
├── database/
│   └── pemetaan_table.sql
├── composer.json
├── spark
└── README.md
```

---

## 📄 PDF Generation

### Libraries Used

| Library                      | Purpose                              |
| ---------------------------- | ------------------------------------ |
| **dompdf/dompdf ^3.1**       | HTML to PDF conversion               |
| **setasign/fpdi ^2.6**       | Import/merge existing PDFs           |
| **setasign/fpdi-tcpdf ^2.3** | FPDI adapter for TCPDF backend       |
| **tecnickcom/tcpdf ^6.10**   | PDF manipulation backend             |
| **smalot/pdfparser ^2.12**   | Parse PDF content & marker detection |

### How It Works

1. HTML template (`cetak-portofolio.php`) dirender dengan data portofolio
2. DomPDF mengkonversi HTML ke PDF
3. FPDI merge external PDF files (RPS, asesmen, dll) ke PDF utama
4. Marker-based insertion system (`INSERT_PDF_RPS`, `INSERT_PDF_TUGAS`, dll) menempatkan file di posisi yang benar
5. Final PDF di-serve sebagai download atau preview

### Access

- **Preview:** `/cetak/{id_portofolio}`
- **Download PDF:** `/cetak/pdf/{id_portofolio}`
- **View Inline:** `/cetak/file/{id_portofolio}/{step}`

---

## 📊 Excel Import

### Supported Modules

- Users (Dosen/Mahasiswa)
- Mata Kuliah
- CPL
- PI
- Mapping MK-CPL-PI
- Perkuliahan

### How to Import

1. Download template Excel dari modul terkait
2. Isi data sesuai format template
3. Upload file Excel melalui tombol "Import"
4. Data akan divalidasi & diproses otomatis

**Library:** PhpSpreadsheet

---

## 🐛 Troubleshooting

### Permission Issues (Windows)

```bash
# Pastikan writable folder dapat ditulis
icacls writable /grant Users:F /T
```

### PDF Not Generating

- Pastikan extension `gd` atau `imagick` aktif di `php.ini`
- Cek `writable/logs/` untuk error logs
- Pastikan file RPS/asesmen tersedia & path benar

### Excel Import Fails

- Pastikan format Excel sesuai template
- Cek data type & format cell
- Validasi data sebelum import

### Database Connection Error

- Cek kredensial di `.env`
- Pastikan MySQL service berjalan
- Verifikasi database `portofolio_v2` sudah dibuat

### Clear Cache

```bash
php spark cache:clear
```

---

## 📞 Support

Untuk pertanyaan atau bantuan:

- Dokumentasi CodeIgniter 4: https://codeigniter.com/user_guide/
- Forum CodeIgniter: https://forum.codeigniter.com/

---

## 📝 Notes

- Proyek ini menggunakan **CodeIgniter 4** dengan struktur MVC standar
- Authentication menggunakan **session-based** login
- File upload disimpan di `writable/uploads/`
- CSS framework: **Bootstrap 5**
- DataTables untuk tabel interaktif
- Select2 untuk dropdown enhancement

---
