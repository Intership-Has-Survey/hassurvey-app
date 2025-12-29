# Hassurvey App - Dokumentasi Instalasi

Aplikasi ini adalah sistem manajemen survei berbasis Laravel dengan Filament sebagai admin panel dan Livewire untuk interaktivitas frontend.

## Persyaratan Sistem

Sebelum menjalankan aplikasi ini, pastikan sistem Anda memenuhi persyaratan berikut:

-   **PHP**: Versi 8.2 atau lebih tinggi
-   **Composer**: Untuk manajemen dependensi PHP
-   **Node.js**: Versi 18 atau lebih tinggi (untuk build frontend)
-   **npm**: Untuk manajemen dependensi JavaScript
-   **Database**: SQLite (default), MySQL, atau PostgreSQL

## Langkah Instalasi

### 1. Clone atau Salin Repository

Salin seluruh folder proyek ke device tujuan Anda. Bisa menggunakan "git clone https://github.com/Intership-Has-Survey/hassurvey-app.git" untuk mempermudah atau dengan cara lain

### 2. Install Dependensi

Buka terminal di folder proyek dan jalankan:

1. composer install

2. npm install

### 3. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Edit file `.env` sesuai kebutuhan Anda, terutama:

-   `APP_NAME`: Nama aplikasi
-   `APP_URL`: URL aplikasi (misalnya `http://localhost:8000`)
-   `DB_CONNECTION`: Jenis database (sqlite, mysql, dll.)
-   Jika menggunakan MySQL:
    -   `DB_HOST`: Host database
    -   `DB_PORT`: Port database
    -   `DB_DATABASE`: Nama database
    -   `DB_USERNAME`: Username database
    -   `DB_PASSWORD`: Password database

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Jalankan Migrasi Database

```bash
php artisan migrate
```

### 6. Seed Database (Opsional)

Jika ingin mengisi data awal:

```bash
php artisan db:seed
```

### 7. Build Assets Frontend

```bash
npm run build
```

Atau untuk development dengan hot reload:

```bash
npm run dev
```

### 8. Link Storage

```bash
php artisan storage:link
```

### 9. Jalankan Aplikasi

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000` (atau port yang ditentukan).

## Troubleshooting

### Error: "Class not found"

Jalankan `composer dump-autoload`

### Error Database Connection

Periksa konfigurasi di `.env` dan pastikan database server berjalan.

### Assets Tidak Dimuat

Pastikan `npm run build` sudah dijalankan dan file di `public/build/` ada.

### Permission Issues

Pastikan folder `storage/` dan `bootstrap/cache/` writable.

## Fitur Utama

-   Manajemen Acara (Events)
-   Sistem Kalibrasi
-   Invoice dan Penawaran
-   Dashboard Admin dengan Filament
-   Export Data ke Excel/PDF

## Kontak

Jika ada pertanyaan, hubungi tim development.

## Daftar Plugin yang digunakan

Bisa dicek di situs resmi Filament terkait daftar plugin

1. althinect/filament-spatie-roles-permissions 2.3.2
2. barryvdh/laravel-dompdf 3.1.1
3. codewithkyrian/filament-date-range
4. rmsramos/activitylog
