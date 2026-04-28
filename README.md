# SIKAR — Sistem Informasi Kendaraan

SIKAR adalah sistem manajemen pemesanan kendaraan berbasis web yang digunakan secara internal di lingkungan perusahaan. Sistem ini menghubungkan tiga peran utama: **Penumpang** yang memesan kendaraan, **Sopir** yang menjalankan perjalanan, dan **Kepala Sopir** yang bertugas mengatur penugasan dan memantau keseluruhan operasional.

Sistem ini dibangun menggunakan **Laravel 12** di sisi backend dengan **Tailwind CSS** dan **Vite** di sisi frontend, serta dilengkapi dengan notifikasi push realtime menggunakan **Firebase Cloud Messaging (FCM)**.

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Struktur Peran Pengguna](#struktur-peran-pengguna)
- [Alur Status Order](#alur-status-order)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi di Komputer Lokal](#instalasi-di-komputer-lokal)
- [Deployment ke Server Perusahaan](#deployment-ke-server-perusahaan)
- [Konfigurasi Firebase](#konfigurasi-firebase)
- [Struktur Folder Penting](#struktur-folder-penting)
- [API Endpoint](#api-endpoint)

---

## Fitur Utama

### Penumpang
- Membuat order perjalanan baru (isi tempat penjemputan, tujuan, waktu, dan keterangan)
- Memantau status order secara realtime
- Membatalkan order yang masih berstatus *pending*
- Mengkonfirmasi perjalanan setelah selesai
- Memberikan rating bintang untuk sopir
- Melihat riwayat order (selesai, dibatalkan, atau ditolak)
- Melihat ketersediaan sopir dan kendaraan
- Menerima notifikasi push saat order diproses atau ditolak

### Sopir
- Toggle status kehadiran kerja (masuk/pulang kerja)
- Melihat daftar order yang ditugaskan kepadanya
- Memulai perjalanan (mengubah status order menjadi *on-process*)
- Melihat riwayat perjalanan yang sudah selesai
- Melihat leaderboard berdasarkan jumlah order selesai dan rating hari ini
- Menerima notifikasi push saat mendapatkan penugasan baru

### Kepala Sopir
- Melihat semua order masuk yang perlu di-assign
- Menugaskan sopir dan kendaraan ke order tertentu
- Menolak order (dengan notifikasi otomatis ke penumpang)
- Mengkonfirmasi selesainya perjalanan
- Memantau ketersediaan sopir dan kendaraan
- Melihat presensi harian sopir
- Melihat leaderboard performa sopir
- Export rekap order ke file Excel (.xlsx) dengan filter tanggal
- Export presensi sopir ke file Excel (.xlsx) dengan filter tanggal
- Menerima notifikasi push saat ada order baru masuk

### Sistem Notifikasi (FCM)
- Notifikasi real-time ke Kepala Sopir saat ada order baru dari penumpang
- Notifikasi ke Sopir saat mendapat penugasan dari Kepala Sopir
- Notifikasi ke Penumpang saat order diproses maupun ditolak

---

## Teknologi yang Digunakan

| Kategori | Teknologi |
|---|---|
| Backend Framework | Laravel 12 (PHP ^8.2) |
| Frontend Styling | Tailwind CSS 4 |
| Build Tool | Vite 7 |
| Database | MySQL |
| Queue | Database Queue |
| Push Notification | Firebase Cloud Messaging (FCM) v1 API |
| Export Excel | Maatwebsite Excel 3.1 |
| HTTP Client (FCM) | Google Auth Library |
| Frontend JS | Axios, Firebase SDK 12 |
| Testing | PestPHP 4 |

---

## Struktur Peran Pengguna

Autentikasi sistem ini tidak menggunakan username/password biasa. Setiap pengguna login menggunakan kombinasi **nama** dan **token unik** yang sudah diatur di database. Tidak ada pendaftaran mandiri — akun dibuat oleh administrator langsung di database.

Terdapat tiga role yang tersedia:

| Role | Akses |
|---|---|
| `penumpang` | Halaman pemesanan, pemantauan, riwayat, ketersediaan |
| `sopir` | Halaman pesanan, riwayat, peringkat (leaderboard) |
| `kepala_sopir` | Halaman pesanan, riwayat, ketersediaan, peringkat, presensi |

Setelah login, sistem otomatis mengarahkan pengguna ke halaman yang sesuai dengan role-nya.

---

## Alur Status Order

Setiap order yang dibuat akan melewati beberapa tahap status berikut:

```
[Penumpang buat order]
        ↓
    PENDING  ──────────────────────────────→  CANCELED (dibatalkan penumpang)
        ↓                                          ↑
    ASSIGNED (Kepala Sopir assign sopir) ──→  REJECTED (ditolak Kepala Sopir)
        ↓
   ON-PROCESS (Sopir mulai perjalanan)
        ↓
   CONFIRMED (Penumpang atau Kepala Sopir konfirmasi selesai)
```

Penjelasan singkat:
- **pending** — Order baru masuk, menunggu penugasan
- **assigned** — Sudah ditugaskan ke sopir dan kendaraan, belum berangkat
- **on-process** — Sopir sudah memulai perjalanan
- **confirmed** — Perjalanan selesai dan dikonfirmasi
- **canceled** — Dibatalkan oleh penumpang (hanya bisa saat pending)
- **rejected** — Ditolak oleh kepala sopir

---

## Persyaratan Sistem

Pastikan perangkat (lokal maupun server) memiliki:

- PHP 8.2 atau lebih baru
- Composer 2.x
- Node.js 18 atau lebih baru dan npm
- MySQL 8.0 atau lebih baru
- Git
- Ekstensi PHP yang aktif: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`, `fileinfo`, `zip`

---

## Instalasi di Komputer Lokal

Ikuti langkah-langkah berikut untuk menjalankan SIKAR di komputer lokal kamu.

### 1. Clone Repository

```bash
git clone https://github.com/novisushmita/sikar-v2.git
cd sikar-v2
```

### 2. Install Dependency PHP

```bash
composer install
```

### 3. Salin File Environment

```bash
cp .env.example .env
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Konfigurasi Database

Buka file `.env` dan sesuaikan bagian database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sikar
DB_USERNAME=root
DB_PASSWORD=
```

Pastikan database dengan nama `sikar` sudah dibuat terlebih dahulu di MySQL:

```sql
CREATE DATABASE sikar;
```

### 6. Jalankan Migrasi dan Seeder

```bash
php artisan migrate
php artisan db:seed
```

Seeder akan mengisi data awal termasuk data pengguna, sopir, dan kendaraan.

### 7. Konfigurasi Firebase (untuk notifikasi)

Lihat bagian [Konfigurasi Firebase](#konfigurasi-firebase) di bawah, kemudian tambahkan baris ini ke `.env`:

```env
FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json
```

### 8. Install Dependency Frontend

```bash
npm install
```

### 9. Jalankan Aplikasi

Gunakan perintah berikut untuk menjalankan semua proses sekaligus (server, queue, dan Vite):

```bash
composer dev
```

Atau jalankan secara terpisah di terminal berbeda:

```bash
# Terminal 1 — Laravel server
php artisan serve

# Terminal 2 — Queue worker (untuk notifikasi)
php artisan queue:listen --tries=1

# Terminal 3 — Vite (hot reload frontend)
npm run dev
```

Aplikasi bisa diakses di: `http://localhost:8000`

---

## Deployment ke Server Perusahaan

Pilih panduan sesuai sistem operasi server perusahaan kamu:

- [Linux (Ubuntu/Debian)](#-linux-ubuntudebian)
- [Windows Server — menggunakan XAMPP](#-windows-server--menggunakan-xampp)
- [Windows Server — menggunakan IIS](#-windows-server--menggunakan-iis)
- [Windows Server — instalasi manual](#-windows-server--instalasi-manual-tanpa-bundel)

---

## 🐧 Linux (Ubuntu/Debian)

Pastikan server sudah bisa diakses via SSH dan memiliki akses sudo.

### Tahap 1 — Persiapan Server

Install semua dependensi yang dibutuhkan:

```bash
# Update package list
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 dan ekstensi yang diperlukan
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml \
  php8.2-curl php8.2-zip php8.2-bcmath php8.2-tokenizer php8.2-fileinfo \
  php8.2-ctype php8.2-openssl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js 18 (via NodeSource)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Git
sudo apt install -y git
```

### Tahap 2 — Clone Project ke Server

Letakkan project di direktori web server, misalnya `/var/www/`:

```bash
cd /var/www
sudo git clone https://github.com/novisushmita/sikar-v2.git sikar
cd sikar
```

### Tahap 3 — Install Dependency

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### Tahap 4 — Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` sesuai dengan konfigurasi server perusahaan:

```env
APP_NAME=Sikar
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-perusahaan.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sikar_prod
DB_USERNAME=sikar_user
DB_PASSWORD=password_yang_kuat

SESSION_DRIVER=file
QUEUE_CONNECTION=database
CACHE_STORE=database

FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json
```

> **Penting:** Pastikan `APP_DEBUG=false` di production agar pesan error tidak terekspos ke pengguna.

### Tahap 5 — Migrasi dan Seeder

```bash
php artisan migrate --force
php artisan db:seed --force
```

### Tahap 6 — Atur Permission Folder

```bash
sudo chown -R www-data:www-data /var/www/sikar
sudo chmod -R 755 /var/www/sikar
sudo chmod -R 775 /var/www/sikar/storage
sudo chmod -R 775 /var/www/sikar/bootstrap/cache
```

### Tahap 7 — Konfigurasi Nginx

Buat file konfigurasi Nginx baru:

```bash
sudo nano /etc/nginx/sites-available/sikar
```

Isi dengan konfigurasi berikut (sesuaikan `server_name` dan path):

```nginx
server {
    listen 80;
    server_name domain-perusahaan.com;
    root /var/www/sikar/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan konfigurasi dan restart Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/sikar /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Tahap 8 — Jalankan Queue Worker sebagai Service

Queue worker harus berjalan terus di background untuk memastikan notifikasi Firebase terkirim. Buat systemd service:

```bash
sudo nano /etc/systemd/system/sikar-queue.service
```

Isi file tersebut:

```ini
[Unit]
Description=SIKAR Queue Worker
After=network.target

[Service]
User=www-data
WorkingDirectory=/var/www/sikar
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --timeout=90
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

Aktifkan dan jalankan service:

```bash
sudo systemctl daemon-reload
sudo systemctl enable sikar-queue
sudo systemctl start sikar-queue

# Cek statusnya
sudo systemctl status sikar-queue
```

### Tahap 9 — Optimasi Performa (Production)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Tahap 10 — Proses Update (saat ada perubahan kode)

Setiap kali ada perubahan kode yang di-push ke repository, jalankan perintah berikut di server:

```bash
cd /var/www/sikar

git pull origin main
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

sudo systemctl restart sikar-queue
```

---

## 🪟 Windows Server

Panduan ini menggunakan **XAMPP** sebagai web server (Apache + PHP + MySQL) karena paling mudah disetup di Windows. Alternatif lain yang bisa dipakai adalah Laragon atau IIS, tapi XAMPP paling umum digunakan.

### Tahap 1 — Install Software yang Dibutuhkan

Download dan install semua software berikut secara berurutan:

**a. XAMPP (PHP 8.2 + Apache + MySQL)**
Download di: https://www.apachefriends.org
Pilih versi yang menyertakan PHP 8.2. Install di `C:\xampp`.

**b. Composer**
Download installer di: https://getcomposer.org/Composer-Setup.exe
Jalankan installer, arahkan ke path `php.exe` milik XAMPP, biasanya di `C:\xampp\php\php.exe`.

**c. Node.js 18**
Download di: https://nodejs.org (pilih versi LTS)
Install seperti biasa, npm sudah ikut otomatis.

**d. Git**
Download di: https://git-scm.com/download/win
Install dengan opsi default, pastikan "Git Bash" ikut terinstall.

### Tahap 2 — Clone Project

Buka **Git Bash** atau **Command Prompt**, lalu jalankan:

```bash
cd C:\xampp\htdocs
git clone https://github.com/novisushmita/sikar-v2.git sikar
cd sikar
```

### Tahap 3 — Install Dependency

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### Tahap 4 — Konfigurasi Environment

```bash
copy .env.example .env
php artisan key:generate
```

Edit file `.env` sesuai konfigurasi server:

```env
APP_NAME=Sikar
APP_ENV=production
APP_DEBUG=false
APP_URL=http://domain-perusahaan.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sikar_prod
DB_USERNAME=sikar_user
DB_PASSWORD=password_yang_kuat

SESSION_DRIVER=file
QUEUE_CONNECTION=database
CACHE_STORE=database

FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json
```

> **Penting:** Pastikan `APP_DEBUG=false` di production agar pesan error tidak terekspos ke pengguna.

### Tahap 5 — Buat Database di MySQL

Buka **XAMPP Control Panel**, start **Apache** dan **MySQL**, lalu buka phpMyAdmin di browser: `http://localhost/phpmyadmin`

Buat database baru dengan nama sesuai `DB_DATABASE` di `.env` kamu, misalnya `sikar_prod`.

### Tahap 6 — Migrasi dan Seeder

```bash
php artisan migrate --force
php artisan db:seed --force
```

### Tahap 7 — Konfigurasi Virtual Host Apache

Buka file konfigurasi virtual host Apache di XAMPP:

```
C:\xampp\apache\conf\extra\httpd-vhosts.conf
```

Tambahkan konfigurasi berikut di bagian paling bawah file:

```apache
<VirtualHost *:80>
    ServerName domain-perusahaan.com
    DocumentRoot "C:/xampp/htdocs/sikar/public"

    <Directory "C:/xampp/htdocs/sikar/public">
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "C:/xampp/apache/logs/sikar-error.log"
    CustomLog "C:/xampp/apache/logs/sikar-access.log" combined
</VirtualHost>
```

Kemudian aktifkan modul `mod_rewrite` di Apache. Buka file:

```
C:\xampp\apache\conf\httpd.conf
```

Cari baris berikut dan hapus tanda `#` di depannya jika ada:

```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

Restart Apache lewat XAMPP Control Panel.

### Tahap 8 — Atur Permission Folder

Pastikan folder `storage` dan `bootstrap/cache` bisa ditulis oleh Apache. Klik kanan folder-folder berikut di Windows Explorer, masuk ke **Properties → Security**, dan berikan akses **Full Control** untuk user `Everyone` atau user yang digunakan Apache (biasanya `SYSTEM`):

```
C:\xampp\htdocs\sikar\storage
C:\xampp\htdocs\sikar\bootstrap\cache
```

### Tahap 9 — Jalankan Queue Worker sebagai Windows Service

Queue worker perlu berjalan terus di background. Di Windows, caranya adalah menggunakan **Task Scheduler** agar otomatis berjalan saat server menyala.

Buka **Task Scheduler** (cari di Start Menu), lalu buat task baru dengan pengaturan berikut:

- **General tab:**
  - Name: `SIKAR Queue Worker`
  - Centang "Run whether user is logged on or not"
  - Centang "Run with highest privileges"

- **Triggers tab:** Pilih "At startup"

- **Actions tab:**
  - Action: Start a program
  - Program: `C:\xampp\php\php.exe`
  - Arguments: `artisan queue:work --sleep=3 --tries=3 --timeout=90`
  - Start in: `C:\xampp\htdocs\sikar`

- **Settings tab:** Centang "If the task is already running, do not start a new instance"

Klik OK dan jalankan task-nya secara manual untuk pertama kali.

### Tahap 10 — Optimasi Performa (Production)

Buka Command Prompt di folder project, lalu jalankan:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Tahap 11 — Proses Update (saat ada perubahan kode)

Setiap kali ada perubahan kode yang di-push ke repository, buka Command Prompt di folder project dan jalankan:

```bash
cd C:\xampp\htdocs\sikar

git pull origin main
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Setelah itu, buka **Task Scheduler**, cari task `SIKAR Queue Worker`, klik kanan → **End** lalu **Run** untuk restart queue worker.

---

## 🪟 Windows Server — Menggunakan IIS

IIS (Internet Information Services) adalah web server bawaan Windows Server. Kalau server perusahaan kamu adalah Windows Server resmi (2016/2019/2022), IIS biasanya sudah tersedia dan tinggal diaktifkan. Pendekatan ini lebih "enterprise" dibanding XAMPP karena terintegrasi langsung dengan sistem operasi.

### Tahap 1 — Aktifkan IIS di Windows Server

Buka **Server Manager → Add Roles and Features**, lalu centang:
- **Web Server (IIS)**
- Di bawah **Application Development**, centang: **CGI**

Klik Next sampai selesai dan tunggu instalasi.

### Tahap 2 — Install PHP 8.2

IIS tidak menyertakan PHP, jadi harus diinstall terpisah.

Download PHP 8.2 (versi **Non-Thread Safe / NTS x64**) dari: https://windows.php.net/download

Ekstrak ke folder `C:\php`. Kemudian:

1. Salin file `php.ini-production` menjadi `php.ini` di folder yang sama
2. Buka `php.ini` dengan Notepad, cari baris berikut dan hapus tanda titik koma (`;`) di depannya untuk mengaktifkan ekstensi yang dibutuhkan:

```ini
extension=curl
extension=fileinfo
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=zip
extension=bcmath
```

3. Tambahkan path PHP ke **System Environment Variables**:
   - Buka **Control Panel → System → Advanced System Settings → Environment Variables**
   - Di bagian **System Variables**, pilih `Path` → klik Edit → klik New
   - Tambahkan: `C:\php`
   - Klik OK di semua jendela

Verifikasi PHP sudah terpasang dengan membuka Command Prompt dan ketik:

```bash
php -v
```

### Tahap 3 — Konfigurasi PHP di IIS dengan FastCGI

1. Buka **IIS Manager** (cari di Start Menu)
2. Klik nama server di panel kiri → klik **Handler Mappings**
3. Klik **Add Module Mapping** di panel kanan, isi:
   - Request path: `*.php`
   - Module: `FastCgiModule`
   - Executable: `C:\php\php-cgi.exe`
   - Name: `PHP_via_FastCGI`
4. Klik OK → pilih **Yes** saat ditanya apakah ingin membuat FastCGI application

### Tahap 4 — Install MySQL

Download MySQL Community Server dari: https://dev.mysql.com/downloads/mysql/

Install dengan opsi **Developer Default** atau minimal **Server Only**. Catat username (`root`) dan password yang kamu buat saat instalasi.

Setelah terinstall, buka **MySQL Command Line Client** dan buat database:

```sql
CREATE DATABASE sikar_prod;
CREATE USER 'sikar_user'@'localhost' IDENTIFIED BY 'password_yang_kuat';
GRANT ALL PRIVILEGES ON sikar_prod.* TO 'sikar_user'@'localhost';
FLUSH PRIVILEGES;
```

### Tahap 5 — Install Composer, Node.js, dan Git

- **Composer**: https://getcomposer.org/Composer-Setup.exe — arahkan ke `C:\php\php.exe`
- **Node.js 18 LTS**: https://nodejs.org
- **Git**: https://git-scm.com/download/win

### Tahap 6 — Clone Project

Buka **Command Prompt** sebagai Administrator:

```bash
cd C:\inetpub\wwwroot
git clone https://github.com/novisushmita/sikar-v2.git sikar
cd sikar
```

### Tahap 7 — Install Dependency dan Build Frontend

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### Tahap 8 — Konfigurasi Environment

```bash
copy .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME=Sikar
APP_ENV=production
APP_DEBUG=false
APP_URL=http://domain-perusahaan.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sikar_prod
DB_USERNAME=sikar_user
DB_PASSWORD=password_yang_kuat

SESSION_DRIVER=file
QUEUE_CONNECTION=database
CACHE_STORE=database

FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json
```

### Tahap 9 — Migrasi Database

```bash
php artisan migrate --force
php artisan db:seed --force
```

### Tahap 10 — Buat Site Baru di IIS

1. Buka **IIS Manager**
2. Klik kanan **Sites** di panel kiri → **Add Website**
3. Isi:
   - Site name: `sikar`
   - Physical path: `C:\inetpub\wwwroot\sikar\public`
   - Port: `80`
   - Host name: `domain-perusahaan.com` (opsional, bisa dikosongkan)
4. Klik OK

### Tahap 11 — Tambahkan web.config untuk URL Rewriting

Laravel butuh URL rewriting agar semua request diarahkan ke `index.php`. Buat file `web.config` di dalam folder `C:\inetpub\wwwroot\sikar\public\`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
  <system.webServer>
    <rewrite>
      <rules>
        <rule name="Laravel Routes" stopProcessing="true">
          <match url="^(.*)$" ignoreCase="false" />
          <conditions>
            <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
            <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
          </conditions>
          <action type="Rewrite" url="index.php" />
        </rule>
      </rules>
    </rewrite>
  </system.webServer>
</configuration>
```

> Untuk URL Rewrite bisa jalan, kamu perlu menginstall modul **URL Rewrite** untuk IIS terlebih dahulu. Download di: https://www.iis.net/downloads/microsoft/url-rewrite

### Tahap 12 — Atur Permission Folder

Klik kanan folder-folder berikut di Windows Explorer → **Properties → Security → Edit**, tambahkan user `IIS_IUSRS` dengan permission **Modify**:

```
C:\inetpub\wwwroot\sikar\storage
C:\inetpub\wwwroot\sikar\bootstrap\cache
```

### Tahap 13 — Jalankan Queue Worker via Task Scheduler

Buka **Task Scheduler**, buat task baru:

- **General:** Name: `SIKAR Queue Worker`, centang "Run whether user is logged on or not" dan "Run with highest privileges"
- **Triggers:** At startup
- **Actions:**
  - Program: `C:\php\php.exe`
  - Arguments: `artisan queue:work --sleep=3 --tries=3 --timeout=90`
  - Start in: `C:\inetpub\wwwroot\sikar`

### Tahap 14 — Optimasi Performa

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Tahap 15 — Proses Update (saat ada perubahan kode)

```bash
cd C:\inetpub\wwwroot\sikar

git pull origin main
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Restart queue worker lewat Task Scheduler: cari task `SIKAR Queue Worker` → klik kanan → **End** → **Run**.

---

## 🪟 Windows Server — Instalasi Manual (Tanpa Bundel)

Pendekatan ini menginstall setiap komponen secara terpisah tanpa paket bundel seperti XAMPP. Hasilnya lebih bersih, ringan, dan mudah diupgrade per komponen. Web server yang digunakan adalah **Apache** (sama seperti XAMPP tapi diinstall sendiri).

### Tahap 1 — Install PHP 8.2

Download PHP 8.2 **Thread Safe (TS) x64** dari: https://windows.php.net/download

> Untuk Apache, gunakan versi **Thread Safe**. Untuk IIS gunakan Non-Thread Safe. Jangan tertukar.

Ekstrak ke `C:\php`. Kemudian:

1. Salin `php.ini-production` → `php.ini`
2. Buka `php.ini`, aktifkan ekstensi berikut dengan menghapus tanda `;` di depannya:

```ini
extension=curl
extension=fileinfo
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=zip
extension=bcmath
```

3. Juga aktifkan baris ini (untuk Apache):

```ini
extension=php_apache2_4.dll
```

4. Tambahkan `C:\php` ke **System Environment Variables → Path**

### Tahap 2 — Install Apache

Download Apache 2.4 untuk Windows dari: https://www.apachelounge.com/download/

Ekstrak ke `C:\Apache24`. Kemudian:

1. Buka file `C:\Apache24\conf\httpd.conf`
2. Cari baris `ServerRoot` dan pastikan isinya:

```apache
ServerRoot "C:/Apache24"
```

3. Tambahkan baris berikut di bagian bawah file untuk menghubungkan PHP ke Apache:

```apache
LoadModule php_module "C:/php/php8apache2_4.dll"
AddHandler application/x-httpd-php .php
PHPIniDir "C:/php"
```

4. Install Apache sebagai Windows Service. Buka **Command Prompt sebagai Administrator**:

```bash
cd C:\Apache24\bin
httpd.exe -k install
```

5. Jalankan Apache:

```bash
httpd.exe -k start
```

Atau buka **Services** di Windows (tekan `Win+R`, ketik `services.msc`) dan start service **Apache2.4**.

### Tahap 3 — Install MySQL

Download dan install MySQL Community Server dari: https://dev.mysql.com/downloads/mysql/

Setelah terinstall, buka **MySQL Command Line Client**:

```sql
CREATE DATABASE sikar_prod;
CREATE USER 'sikar_user'@'localhost' IDENTIFIED BY 'password_yang_kuat';
GRANT ALL PRIVILEGES ON sikar_prod.* TO 'sikar_user'@'localhost';
FLUSH PRIVILEGES;
```

### Tahap 4 — Install Composer, Node.js, dan Git

- **Composer**: https://getcomposer.org/Composer-Setup.exe — arahkan ke `C:\php\php.exe`
- **Node.js 18 LTS**: https://nodejs.org
- **Git**: https://git-scm.com/download/win

### Tahap 5 — Clone Project

```bash
cd C:\Apache24\htdocs
git clone https://github.com/novisushmita/sikar-v2.git sikar
cd sikar
```

### Tahap 6 — Install Dependency dan Build Frontend

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### Tahap 7 — Konfigurasi Environment

```bash
copy .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME=Sikar
APP_ENV=production
APP_DEBUG=false
APP_URL=http://domain-perusahaan.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sikar_prod
DB_USERNAME=sikar_user
DB_PASSWORD=password_yang_kuat

SESSION_DRIVER=file
QUEUE_CONNECTION=database
CACHE_STORE=database

FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json
```

### Tahap 8 — Migrasi Database

```bash
php artisan migrate --force
php artisan db:seed --force
```

### Tahap 9 — Konfigurasi Virtual Host Apache

Buka `C:\Apache24\conf\httpd.conf`, cari baris berikut dan pastikan tidak ada tanda `#` di depannya:

```apache
LoadModule rewrite_module modules/mod_rewrite.so
Include conf/extra/httpd-vhosts.conf
```

Kemudian buka `C:\Apache24\conf\extra\httpd-vhosts.conf` dan tambahkan:

```apache
<VirtualHost *:80>
    ServerName domain-perusahaan.com
    DocumentRoot "C:/Apache24/htdocs/sikar/public"

    <Directory "C:/Apache24/htdocs/sikar/public">
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "C:/Apache24/logs/sikar-error.log"
    CustomLog "C:/Apache24/logs/sikar-access.log" combined
</VirtualHost>
```

Restart Apache:

```bash
cd C:\Apache24\bin
httpd.exe -k restart
```

### Tahap 10 — Atur Permission Folder

Klik kanan folder berikut → **Properties → Security → Edit**, berikan akses **Modify** untuk user `Everyone` atau user yang menjalankan Apache:

```
C:\Apache24\htdocs\sikar\storage
C:\Apache24\htdocs\sikar\bootstrap\cache
```

### Tahap 11 — Jalankan Queue Worker via Task Scheduler

Buka **Task Scheduler**, buat task baru:

- **General:** Name: `SIKAR Queue Worker`, centang "Run whether user is logged on or not" dan "Run with highest privileges"
- **Triggers:** At startup
- **Actions:**
  - Program: `C:\php\php.exe`
  - Arguments: `artisan queue:work --sleep=3 --tries=3 --timeout=90`
  - Start in: `C:\Apache24\htdocs\sikar`

### Tahap 12 — Optimasi Performa

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Tahap 13 — Proses Update (saat ada perubahan kode)

```bash
cd C:\Apache24\htdocs\sikar

git pull origin main
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Restart queue worker lewat Task Scheduler: cari task `SIKAR Queue Worker` → klik kanan → **End** → **Run**.

---

## Konfigurasi Firebase

SIKAR menggunakan Firebase Cloud Messaging (FCM) untuk mengirim notifikasi push ke browser. Ikuti langkah berikut untuk mengaturnya:

1. Buka [Firebase Console](https://console.firebase.google.com) dan pilih project `sikar-a9a0d` (atau buat project baru jika diperlukan).
2. Masuk ke **Project Settings → Service Accounts**.
3. Klik **Generate new private key** dan download file JSON-nya.
4. Simpan file tersebut ke dalam project dengan nama dan path berikut:

```
storage/app/firebase-credentials.json
```

5. Pastikan file ini **tidak pernah di-commit ke Git**. Cek bahwa `storage/app/firebase-credentials.json` sudah masuk ke `.gitignore`.

> File ini bersifat rahasia dan memberikan akses penuh ke Firebase project. Jangan disebarkan atau di-upload ke repository publik maupun privat.

---

## Struktur Folder Penting

```
sikar-v2/
├── app/
│   ├── Exports/                # Kelas export Excel (order & presensi)
│   ├── Http/
│   │   ├── Controllers/        # Semua controller (Auth, Penumpang, Sopir, dll)
│   │   └── Middleware/         # Middleware autentikasi token
│   ├── Models/                 # Eloquent models (Order, Sopir, Pengguna, dll)
│   ├── Services/
│   │   └── FcmService.php      # Service pengiriman notifikasi FCM
│   └── Traits/                 # Reusable logic (leaderboard, ketersediaan)
├── database/
│   ├── migrations/             # Struktur tabel database
│   └── seeders/                # Data awal (pengguna, sopir, kendaraan)
├── resources/
│   └── views/
│       ├── login.blade.php
│       ├── penumpang/          # Halaman untuk role penumpang
│       ├── sopir/              # Halaman untuk role sopir
│       └── kepalasopir/        # Halaman untuk role kepala sopir
├── routes/
│   ├── api.php                 # Semua API endpoint
│   └── web.php                 # Route untuk halaman web
└── storage/
    └── app/
        └── firebase-credentials.json  # (tidak ada di repo, harus diisi manual)
```

---

## API Endpoint

Semua endpoint API memerlukan autentikasi melalui token. Token bisa dikirim via header `Authorization: Bearer <token>`, body request, atau session.

### Umum

| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | `/api/login` | Login dengan nama dan token |
| POST | `/api/logout` | Logout dan hapus session |
| POST | `/api/me` | Data pengguna yang sedang login |
| GET | `/api/dashboard` | Dashboard sesuai role |

### Penumpang

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/penumpang/orders` | Daftar order aktif (bisa filter by status) |
| GET | `/api/penumpang/orders/{id}` | Detail satu order |
| POST | `/api/penumpang/create` | Buat order baru |
| DELETE | `/api/penumpang/cancel/{id}` | Batalkan order (hanya saat pending) |
| POST | `/api/penumpang/confirm/{id}` | Konfirmasi selesai perjalanan |
| POST | `/api/penumpang/review` | Submit rating sopir |
| GET | `/api/penumpang/mobil` | Daftar kendaraan tersedia |
| GET | `/api/penumpang/sopir` | Daftar sopir tersedia |

### Sopir

| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | `/api/sopir/kerja` | Toggle status masuk/pulang kerja |
| GET | `/api/sopir/kerja` | Cek status kerja sopir |
| GET | `/api/sopir/orders` | Daftar order yang ditugaskan |
| POST | `/api/sopir/start/{id}` | Mulai perjalanan |
| GET | `/api/sopir/leaderboard` | Leaderboard performa sopir |

### Kepala Sopir

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/api/kepalasopir/order` | Semua order (bisa filter by status) |
| POST | `/api/kepalasopir/assign` | Assign sopir dan kendaraan ke order |
| DELETE | `/api/kepalasopir/reject/{id}` | Tolak order |
| POST | `/api/kepalasopir/confirm/{id}` | Konfirmasi selesai perjalanan |
| GET | `/api/kepalasopir/sopir` | Daftar sopir tersedia |
| GET | `/api/kepalasopir/mobil` | Daftar kendaraan tersedia |
| GET | `/api/kepalasopir/sopirmasuk` | Data presensi sopir |
| GET | `/api/kepalasopir/leaderboard` | Leaderboard sopir |
| GET | `/api/kepalasopir/export-presensi-sopir` | Export presensi sopir ke Excel |

---

## Catatan Tambahan

- **Autentikasi berbasis token** — Sistem ini tidak menggunakan Laravel Sanctum atau Passport. Token disimpan langsung di kolom `token` tabel `pengguna` dan divalidasi melalui middleware custom `AuthenticateWithToken`.
- **Queue untuk notifikasi** — Pengiriman notifikasi FCM diproses secara sinkron di dalam transaksi, namun kesalahan FCM tidak akan membatalkan proses utama (menggunakan try-catch terpisah). Pastikan queue worker tetap berjalan di production.
- **Tidak ada fitur registrasi** — Akun pengguna hanya bisa dibuat langsung di database oleh administrator.
- **Session lifetime** — Session berlaku selama 7 hari (10080 menit) sesuai konfigurasi default.