# Sistem Manajemen Inventaris - PT Telkomsel

Aplikasi web untuk mengelola inventaris kantor (master barang, peminjaman, dan laporan) dibangun dengan **Laravel 11**, dibuat untuk Menjawab masalah: kehilangan data aset, duplikasi pencatatan barang, sulitnya memantau stok, dan lambatnya pembuatan laporan — semua dikelola dalam satu sistem terpusat berbasis role (Admin, Staff, Manager).

---

## Daftar Isi

1. [Tech Stack](#tech-stack)
2. [Struktur Proyek Ini](#struktur-proyek-ini)
3. [Cara Instalasi](#cara-instalasi)
4. [Cara Menjalankan Project](#cara-menjalankan-project)
5. [Akun Login Testing](#akun-login-testing)
6. [Fitur Aplikasi](#fitur-aplikasi)
7. [Arsitektur Aplikasi](#arsitektur-aplikasi)
8. [Alur Database (ERD)](#alur-database-erd)
9. [Alasan Penggunaan Fitur Laravel](#alasan-penggunaan-fitur-laravel)
10. [Menjalankan dengan Docker](#menjalankan-dengan-docker-opsional)
11. [Deployment](#deployment)
12. [Troubleshooting](#troubleshooting)

---

## Tech Stack

| Komponen        | Teknologi                                   |
|-----------------|-----------------------------------------------|
| Backend         | PHP 8.2+, Laravel 11                          |
| Database        | MySQL 8 (kompatibel juga dengan PostgreSQL)   |
| Frontend        | Blade + Bootstrap 5 (CDN, tanpa build step)   |
| Chart           | Chart.js (CDN)                                |
| Auth            | Session-based (web) + Laravel Sanctum (API)   |
| Export          | barryvdh/laravel-dompdf, maatwebsite/excel    |
| Container       | Docker & docker-compose                       |

---

## Struktur Proyek Ini

Paket ini adalah **struktur project Laravel 11 yang lengkap** (bukan sekadar overlay) — sudah termasuk `artisan`, `public/index.php`, seluruh `config/*.php`, migrations, seeders, views, routes, dan database.sqlite`.

```
app/                    -> Models, Controllers, Middleware, Requests, Notifications, Exports
config/                 -> seluruh konfigurasi Laravel (termasuk inventaris.php khusus aplikasi ini)
database/
  migrations/           -> seluruh migration (termasuk migration bawaan Laravel: users, cache, jobs, dsb.)
  seeders/              -> seeder data dummy
  database.sqlite       -> database SQLite siap pakai
resources/views/        -> seluruh tampilan (Blade)
routes/                 -> web.php, api.php, console.php
bootstrap/              -> app.php, providers.php
public/                 -> index.php, .htaccess, css/
artisan, composer.json, .env, .env.example, phpunit.xml, dst.
```
## Cara Instalasi

### Prasyarat
- PHP >= 8.2 dengan ekstensi umum (mbstring, pdo_sqlite, openssl, tokenizer, xml, ctype, json, gd) — **sudah terpenuhi otomatis jika memakai [Laravel Herd](https://herd.laravel.com/)**
- Composer 2.x (juga sudah tersedia bawaan di Laravel Herd)
- Node.js **tidak wajib** — seluruh aset (Bootstrap, Chart.js) dimuat lewat CDN.

### Langkah 1 — Install dependency

Dari dalam folder project ini:

```bash
composer install
```

### Langkah 2 — Environment

File `.env` **sudah disertakan** dan sudah dikonfigurasi memakai SQLite + `APP_KEY` yang sudah digenerate, jadi secara teknis Anda bisa langsung lanjut ke Langkah 3. Namun disarankan generate ulang key milik Anda sendiri:

```bash
php artisan key:generate
```

### Langkah 3 — Database

**Tidak ada langkah tambahan!** File `database/database.sqlite` sudah berisi seluruh tabel + data dummy (8 kategori, 52 barang, 90 riwayat peminjaman, 3 akun demo). Laravel akan otomatis.

> Ingin mulai dari data kosong / data baru? Hapus isi `database/database.sqlite` (buat file kosong baru) lalu jalankan `php artisan migrate --seed`.
>
> Ingin pakai MySQL/PostgreSQL sebagai gantinya? Ubah bagian `DB_CONNECTION` di `.env` (lihat komentar di `.env.example`), buat database-nya, lalu jalankan `php artisan migrate --seed` (atau import `database.sql` yang disertakan).

### Langkah 4 — Link storage (untuk upload gambar barang)

```bash
php artisan storage:link
```

---

## Cara Menjalankan Project

**Opsi A — Laravel Herd (macOS/Windows):**
Cukup pastikan folder project ini berada di direktori yang di-*park* oleh Herd (atau tambahkan sebagai site baru dari Herd UI). Herd otomatis menjalankan PHP-nya lewat domain `.test` — misalnya `http://inventaris-telkomsel.test`.

**Opsi B — `php artisan serve` (universal, tanpa Herd):**
```bash
php artisan serve
```
Buka browser ke **http://localhost:8000**

Aplikasi akan otomatis redirect ke halaman login.

---

## Akun Login Testing

| Role    | Email                     | Password   | Hak Akses                                   |
|---------|----------------------------|------------|-----------------------------------------------|
| Admin   | `admin@telkomsel.test`     | `password` | Full access ke seluruh fitur                  |
| Staff   | `staff@telkomsel.test`     | `password` | Kelola master barang & peminjaman             |
| Manager | `manager@telkomsel.test`   | `password` | Hanya melihat dashboard, laporan, & riwayat   |

--- 

## Fitur Aplikasi

### 1. Autentikasi
- Login, Register (otomatis role **staff**), Logout
- Forgot Password / Reset Password (link dikirim lewat email; pada `.env` default `MAIL_MAILER=log` sehingga link reset password bisa dilihat di `storage/logs/laravel.log`)

### 2. Role Management
3 role dengan hak akses berbeda, diterapkan lewat **custom middleware** `role:` pada setiap grup route (lihat `routes/web.php` dan `app/Http/Middleware/RoleMiddleware.php`):
- **Admin**: akses penuh (CRUD barang, kategori, peminjaman, semua laporan)
- **Staff**: kelola master barang & peminjaman (tanpa akses laporan/export)
- **Manager**: hanya melihat dashboard, daftar barang, riwayat peminjaman, dan export laporan

### 3. Master Data Barang
CRUD lengkap dengan: kode barang, nama, kategori, stok, lokasi penyimpanan, kondisi (baik/rusak ringan/rusak berat), upload gambar, pencarian (nama/kode/lokasi), filter kategori & kondisi, serta pagination.

### 4. Peminjaman Barang
- Tambah peminjaman (mendukung banyak barang sekaligus dalam satu transaksi peminjaman — form dinamis tambah/hapus baris barang)
- Validasi stok otomatis (tidak bisa meminjam melebihi stok tersedia)
- Pengembalian barang (stok otomatis dikembalikan + mencatat kondisi barang saat kembali)
- Riwayat peminjaman dengan filter status & pencarian nama peminjam
- Status otomatis: `dipinjam`, `dikembalikan`, `terlambat` (ditandai otomatis oleh scheduled task harian di `routes/console.php` jika melewati batas kembali)

### 5. Dashboard
- Total barang, barang tersedia, barang dipinjam, jumlah jenis barang
- Grafik peminjaman per bulan (Chart.js) untuk tahun berjalan
- Daftar peminjaman terbaru
- **Notifikasi stok menipis** (banner peringatan + email otomatis ke Admin & Manager saat stok barang mencapai ambang batas, diatur lewat `LOW_STOCK_THRESHOLD` di `.env`)

## Arsitektur Aplikasi

Aplikasi ini mengikuti pola **MVC (Model-View-Controller)** standar Laravel, dengan beberapa lapisan tambahan untuk menjaga kode tetap rapi:

```
Request (Browser / API Client)
        |
        v
   routes/web.php atau routes/api.php
        |
        v
   Middleware (auth, role:xxx)
        |
        v
   FormRequest (validasi input: StoreProductRequest, dll.)
        |
        v
   Controller (logika bisnis & orkestrasi)
        |
        |--> Model / Eloquent ORM (query & relasi database)
        |
        `--> View (Blade) -- untuk web
             atau JSON Response -- untuk API
```

**Alasan pemisahan tanggung jawab:**
- **FormRequest** dipakai agar validasi tidak mencampuri logika controller dan mudah diuji ulang (contoh: `StoreProductRequest`, `UpdateProductRequest`, `StoreBorrowingRequest`).
- **Model** menyimpan logika terkait data itu sendiri (contoh: `Product::scopeSearch()`, `Product::scopeLowStock()`, `Borrowing::statusBadgeColor()`) agar controller tetap ramping.
- **Middleware `RoleMiddleware`** memusatkan aturan otorisasi berbasis role di satu tempat, sehingga mudah diaudit dan diubah tanpa menyentuh controller.
- **DB Transaction** (`DB::transaction`) dipakai di proses peminjaman & pengembalian barang agar perubahan stok dan pencatatan peminjaman selalu konsisten (atomik) — jika salah satu barang gagal diproses (misal stok tidak cukup), seluruh transaksi dibatalkan.

## Menjalankan dengan Docker (Opsional)

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

Akses aplikasi di **http://localhost:8000**, dan phpMyAdmin di **http://localhost:8080** (user: `root`, password: `root_secret`).
