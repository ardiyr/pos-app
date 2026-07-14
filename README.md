# Web POS System

A modern, fast, and secure Point of Sale (POS) Web Application built with CodeIgniter 4, Tailwind CSS, Alpine.js, and PostgreSQL.

## 🚀 Fitur Utama
- **Kasir Modern (Point of Sale):** Keranjang belanja interaktif dengan pencarian barang otomatis.
- **Manajemen Produk:** Sistem inventaris (CRUD) dengan pelacakan sisa stok otomatis.
- **Riwayat Transaksi:** Riwayat lengkap penjualan, cetak ulang struk (*invoice*), dan pembatalan (*void*).
- **Auto-Restock:** Pembatalan transaksi secara cerdas akan mengembalikan stok barang ke inventaris.
- **Autentikasi Aman:** Sistem Login berlapis berbasis database dengan hash keamanan *Bcrypt*.
- **Role Management:** Hak akses Admin (bisa merombak akun kasir) dan Kasir (hanya bisa bertransaksi).
- **Estetika Elegan:** UI Premium menggunakan Tailwind CSS dan Notifikasi *SweetAlert2*.

## 🛠️ Tech Stack
- **Backend:** PHP (CodeIgniter 4)
- **Database:** PostgreSQL
- **Frontend:** HTML5, Tailwind CSS (via CDN)
- **Reaktivitas:** Alpine.js (via CDN)
- **Notifikasi:** SweetAlert2 (via CDN)

---

## 📖 Cara Instalasi (Instruksi Setup)

Aplikasi ini sangat mudah dijalankan di lingkungan *local development* seperti Laragon atau XAMPP.

### 1. Persyaratan Sistem
- PHP >= 8.1
- Ekstensi PHP yang dibutuhkan: `intl`, `mbstring`, `pdo_pgsql`, `pgsql`
- PostgreSQL Database

### 2. Kloning Repository
Buka terminal dan jalankan:
```bash
git clone https://github.com/USERNAME/pos-app.git
cd pos-app
```
*(Ganti `USERNAME` dengan username GitHub Anda)*

### 3. Konfigurasi Database
1. Buka PgAdmin atau command line PostgreSQL, lalu buat database baru bernama `pos_db`.
2. Ganti nama file `env` menjadi `.env` di dalam folder *root* aplikasi.
3. Buka `.env` dan atur konfigurasi database Anda, pastikan:
```env
CI_ENVIRONMENT = development

database.default.hostname = localhost
database.default.database = pos_db
database.default.username = postgres
database.default.password = password
database.default.DBDriver = Postgre
```
*(Sesuaikan username dan password sesuai instalasi PostgreSQL Anda)*

### 4. Instalasi & Migrasi Database
Aplikasi ini tidak memerlukan instalasi dependensi Composer/NPM jika Anda hanya ingin menjalankannya langsung. Namun, Anda harus menjalankan migrasi tabel:

```bash
php spark migrate
php spark db:seed UserSeeder
```
*Perintah di atas akan merangkai struktur tabel dan membuat satu akun admin default.*

### 5. Jalankan Aplikasi
Jika Anda menggunakan **Laragon**, pastikan Anda meletakkan folder `pos-app` di dalam `C:\laragon\www` dan Anda bisa langsung membukanya di browser:
👉 **[http://pos-app.test](http://pos-app.test)**

Jika tidak menggunakan Laragon, gunakan server bawaan CodeIgniter:
```bash
php spark serve
```
Buka browser di **[http://localhost:8080](http://localhost:8080)**.

### 6. Akun Login Default
- **Username:** `admin`
- **Password:** `password`

Silakan login, lalu masuk ke "Manajemen User" untuk menambahkan kasir baru atau mengubah sandi admin.

---
Dibuat dengan ❤️ untuk sistem kasir yang satset dan elegan.
