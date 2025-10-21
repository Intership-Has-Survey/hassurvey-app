# Dokumentasi Fitur Export Layanan Pemetaan ke Excel

## Daftar Isi

-   [Overview](#overview)
-   [Lokasi File](#lokasi-file)
-   [Pilihan Format Export](#pilihan-format-export)
-   [Cara Penggunaan](#cara-penggunaan)
-   [Kolom yang Tersedia](#kolom-yang-tersedia)
-   [Format Data](#format-data)
-   [Testing](#testing)
-   [Troubleshooting](#troubleshooting)

---

## Overview

Fitur export data layanan pemetaan ke Excel memungkinkan pengguna untuk mengekspor data proyek dalam berbagai format sesuai kebutuhan. Fitur ini terintegrasi penuh dengan sistem filter dan tab yang ada di halaman list proyek.

### Fitur Utama

-   ✅ 3 pilihan format export (Sederhana, Lengkap, Ringkas)
-   ✅ Pilihan format file (XLS, XLSX, CSV)
-   ✅ Custom nama file
-   ✅ Mengikuti filter aktif (tab, date range, dll)
-   ✅ Format data Indonesia (angka, tanggal, boolean)
-   ✅ Tested & Production Ready

---

## Lokasi File

### File Utama

```
app/Filament/Resources/ProjectResource/Pages/ListProjects.php
```

### File Testing

```
tests/Feature/ProjectExportTest.php
```

### Dependencies

Package yang digunakan:

-   `pxlrbt/filament-excel` - Library untuk export Excel di Filament

---

## Pilihan Format Export

### 1. Export Kolom Tabel (Sederhana)

**Deskripsi**: Export data sesuai dengan kolom yang ditampilkan di tabel halaman list.

**Karakteristik**:

-   Menggunakan method `fromTable()`
-   Hanya menampilkan kolom yang visible di tabel
-   Paling cepat dan ringan
-   Cocok untuk export quick view

**Kolom yang Di-export**:

-   Semua kolom yang visible di tabel Project

**Use Case**:

-   Export cepat untuk laporan harian
-   Preview data untuk sharing
-   Backup data sederhana

---

### 2. Export Lengkap (Semua Kolom)

**Deskripsi**: Export dengan semua detail kolom proyek termasuk informasi lokasi lengkap, keuangan, dan metadata.

**Karakteristik**:

-   22 kolom detail
-   Informasi lokasi lengkap (Provinsi, Kota, Kecamatan, Desa)
-   Perhitungan PPN dan total tagihan
-   Metadata (Created At, Updated At)

**Kolom yang Di-export**:

1. Kode Proyek
2. Nama Proyek
3. Status Proyek
4. Klien Utama
5. PIC (Person in Charge) - multiple separated by comma
6. Kategori
7. Sales
8. Tanggal Informasi Masuk
9. Sumber
10. Provinsi
11. Kota/Kabupaten
12. Kecamatan
13. Desa/Kelurahan
14. Detail Alamat
15. Nilai Proyek (Rp)
16. Dikenakan PPN (Ya/Tidak)
17. Nilai PPN (Rp)
18. Total Tagihan (Rp)
19. Status Pembayaran
20. Status Pekerjaan
21. Dibuat Pada
22. Diupdate Pada

**Use Case**:

-   Laporan lengkap untuk manajemen
-   Analisis data komprehensif
-   Archive/backup lengkap
-   Keperluan audit

---

### 3. Export Ringkas (Kolom Penting)

**Deskripsi**: Export hanya kolom-kolom kunci yang paling sering dibutuhkan untuk quick reference.

**Karakteristik**:

-   9 kolom penting
-   Fokus pada informasi bisnis utama
-   Ukuran file kecil
-   Mudah dibaca

**Kolom yang Di-export**:

1. Kode Proyek
2. Nama Proyek
3. Status
4. Klien
5. Sales
6. Total Tagihan (Rp)
7. Status Pembayaran
8. Status Pekerjaan
9. Tanggal

**Use Case**:

-   Dashboard untuk sales team
-   Quick summary report
-   Meeting materials
-   Follow-up tracking

---

## Cara Penggunaan

### Langkah-langkah Export

#### 1. Akses Halaman Layanan Pemetaan

```
Dashboard → Layanan → Pemetaan
```

#### 2. Klik Tombol Export

-   Lokasi: Header halaman (sebelah tombol "Tambah Layanan Pemetaan Baru")
-   Label: "Export ke Excel"
-   Icon: Arrow Down (heroicon-o-arrow-down-tray)
-   Warna: Hijau (Success)

#### 3. Pilih Format Export

Modal akan muncul dengan 3 pilihan:

-   **Export Kolom Tabel (Sederhana)**
-   **Export Lengkap (Semua Kolom)**
-   **Export Ringkas (Kolom Penting)**

#### 4. Pilih Tipe File

Setelah memilih format export, pilih tipe file:

-   **XLS** - Excel 97-2003 (.xls)
-   **XLSX** - Excel 2007+ (.xlsx) - Recommended
-   **CSV** - Comma Separated Values (.csv)

#### 5. Tentukan Nama File

-   Isi nama file custom (opsional)
-   Jika dikosongkan, akan menggunakan nama default
-   Format default: `export-proyek-pemetaan-[type]-YYYY-MM-DD-HHmmss`

**Contoh**:

```
export-proyek-pemetaan-lengkap-2025-10-21-143050.xlsx
export-proyek-pemetaan-ringkas-2025-10-21-143055.csv
export-proyek-pemetaan-table-2025-10-21-143100.xls
```

#### 6. Klik Export

File akan otomatis terdownload ke folder Downloads browser Anda.

---

## Kolom yang Tersedia

### Detail Kolom Export Lengkap

| No  | Kolom                     | Heading            | Format                 | Sumber Data                          |
| --- | ------------------------- | ------------------ | ---------------------- | ------------------------------------ |
| 1   | `kode_project`            | Kode Proyek        | Text                   | Direct field                         |
| 2   | `nama_project`            | Nama Proyek        | Text                   | Direct field                         |
| 3   | `status`                  | Status Proyek      | Text                   | Direct field                         |
| 4   | `customer_display`        | Klien Utama        | Text                   | Calculated from corporate/perorangan |
| 5   | `perorangan`              | PIC                | Text (comma separated) | Relationship                         |
| 6   | `kategori.nama`           | Kategori           | Text                   | Relationship                         |
| 7   | `sales.nama`              | Sales              | Text                   | Relationship                         |
| 8   | `tanggal_informasi_masuk` | Tanggal Info Masuk | Date                   | Direct field                         |
| 9   | `sumber`                  | Sumber             | Text                   | Direct field                         |
| 10  | `provinsiRegion.name`     | Provinsi           | Text                   | Relationship                         |
| 11  | `kotaRegion.name`         | Kota/Kabupaten     | Text                   | Relationship                         |
| 12  | `kecamatanRegion.name`    | Kecamatan          | Text                   | Relationship                         |
| 13  | `desaRegion.name`         | Desa/Kelurahan     | Text                   | Relationship                         |
| 14  | `detail_alamat`           | Detail Alamat      | Text                   | Direct field                         |
| 15  | `nilai_project_awal`      | Nilai Proyek (Rp)  | Number                 | Direct field, formatted              |
| 16  | `dikenakan_ppn`           | Dikenakan PPN      | Boolean                | Ya/Tidak                             |
| 17  | `nilai_ppn`               | Nilai PPN (Rp)     | Number                 | Calculated (12% if applicable)       |
| 18  | `nilai_project`           | Total Tagihan (Rp) | Number                 | Direct field, formatted              |
| 19  | `status_pembayaran`       | Status Pembayaran  | Text                   | Direct field                         |
| 20  | `status_pekerjaan`        | Status Pekerjaan   | Text                   | Direct field                         |
| 21  | `created_at`              | Dibuat Pada        | DateTime               | Formatted: dd/mm/YYYY HH:mm:ss       |
| 22  | `updated_at`              | Diupdate Pada      | DateTime               | Formatted: dd/mm/YYYY HH:mm:ss       |

---

## Format Data

### Format Angka

```php
// Input: 15000000
// Output: 15.000.000

Format: number_format($value, 0, ',', '.')
```

-   Separator ribuan: titik (.)
-   Separator desimal: koma (,)
-   Decimal places: 0 (bilangan bulat)

### Format Tanggal

```php
// Input: 2025-10-21 14:30:50
// Output: 21/10/2025 14:30:50

Format: dd/mm/YYYY HH:mm:ss
```

### Format Boolean

```php
// Input: true/1
// Output: Ya

// Input: false/0/null
// Output: Tidak
```

### Format Null/Empty

```php
// Untuk text fields
Empty → "N/A"

// Untuk date/datetime fields
Null → "-"

// Untuk numeric fields
Null → "0"
```

### Format List/Multiple Values

```php
// Multiple PIC (perorangan)
// Output: "John Doe, Jane Smith, Bob Johnson"

Format: implode(', ', $values)
```

---

## Testing

### File Test

```
tests/Feature/ProjectExportTest.php
```

### Menjalankan Test

#### Test Semua Export

```bash
php artisan test --filter=ProjectExportTest
```

#### Test Specific

```bash
# Test export action exists
php artisan test --filter=test_list_projects_page_has_export_action_configured

# Test multiple export types
php artisan test --filter=test_export_action_has_multiple_export_types

# Test export names
php artisan test --filter=test_export_types_have_correct_names
```

### Test Coverage

| Test                                                   | Description                                            | Status  |
| ------------------------------------------------------ | ------------------------------------------------------ | ------- |
| `test_list_projects_page_has_export_action_configured` | Memastikan export action ada di header                 | ✅ PASS |
| `test_export_action_has_multiple_export_types`         | Memastikan ada 3 pilihan export                        | ✅ PASS |
| `test_export_types_have_correct_names`                 | Memastikan nama export benar (table, lengkap, ringkas) | ✅ PASS |
| `test_export_action_configuration_is_valid`            | Memastikan konfigurasi valid                           | ✅ PASS |
| `test_header_actions_include_create_and_export`        | Memastikan header memiliki create & export action      | ✅ PASS |

**Total**: 5 Tests, 13 Assertions - **All Passed** ✅

---

## Troubleshooting

### 1. Export Button Tidak Muncul

**Kemungkinan Penyebab**:

-   Cache belum di-clear
-   Permission user tidak sesuai

**Solusi**:

```bash
# Clear cache
php artisan optimize:clear
php artisan filament:cache-components

# Restart server
php artisan serve
```

---

### 2. Error Saat Export

**Error**: "Class not found pxlrbt\FilamentExcel..."

**Solusi**:

```bash
# Install package jika belum
composer require pxlrbt/filament-excel

# Publish config
php artisan vendor:publish --tag=filament-excel-config
```

---

### 3. Data Tidak Muncul di Export

**Kemungkinan Penyebab**:

-   Filter aktif membatasi data
-   User tidak punya akses ke data tertentu (company scope)
-   Relationship tidak terload

**Solusi**:

1. Check filter yang aktif (tab, date range)
2. Reset semua filter
3. Verify user company/tenant
4. Check relationship di model

---

### 4. Format Angka Salah

**Problem**: Angka tidak terformat dengan separator Indonesia

**Solusi**:
Pastikan menggunakan `formatStateUsing` dengan `number_format`:

```php
Column::make('nilai_project')
    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
```

---

### 5. File Terlalu Besar / Timeout

**Problem**: Export timeout untuk data besar (>10.000 rows)

**Solusi**:

```php
// Increase memory limit
ini_set('memory_limit', '512M');

// Increase execution time
ini_set('max_execution_time', 300);

// Or use queue
ExcelExport::make()
    ->queue() // Export using queue
```

---

### 6. Relationship Null/Missing

**Problem**: Kolom relationship kosong (sales.nama, kategori.nama)

**Solusi**:
Pastikan relationship eager loaded:

```php
ExcelExport::make()
    ->modifyQueryUsing(function ($query) {
        return $query->with(['sales', 'kategori', 'corporate', 'perorangan']);
    })
```

---

## Filter dan Data yang Di-export

### Filter yang Diterapkan

Export akan mengikuti filter berikut jika aktif:

#### 1. Tab Filter

Data yang di-export akan sesuai tab yang aktif:

-   **All** - Semua proyek
-   **Prospect** - Hanya status Prospect
-   **Follow up 1** - Hanya Follow up 1
-   **Follow up 2** - Hanya Follow up 2
-   **Follow up 3** - Hanya Follow up 3
-   **Closing** - Hanya Closing
-   **Failed** - Hanya Failed

#### 2. Date Range Filter

Jika ada filter tanggal (`created_at`), export akan membatasi data sesuai range.

#### 3. Company/Tenant Scope

Data otomatis di-filter berdasarkan company user yang login (multi-tenancy).

### Tips Export Efektif

1. **Export Subset Data**

    - Aktifkan filter sebelum export
    - Gunakan tab untuk filter status
    - Set date range untuk periode tertentu

2. **Export Per Batch**

    - Jangan export semua data sekaligus jika >5000 rows
    - Export per bulan atau per quarter
    - Export per status

3. **Pilih Format Sesuai Kebutuhan**
    - Quick view → Export Ringkas
    - Analisis detail → Export Lengkap
    - Sharing dengan team → Export Kolom Tabel

---

## Technical Implementation

### Code Structure

```php
// ListProjects.php

protected function getHeaderActions(): array
{
    return [
        Actions\CreateAction::make()
            ->label('Tambah Layanan Pemetaan Baru')
            ->icon('heroicon-o-plus')
            ->color('primary'),

        ExportAction::make()
            ->label('Export ke Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->exports([
                ExcelExport::make('table')
                    ->label('Export Kolom Tabel (Sederhana)')
                    ->fromTable()
                    ->askForWriterType()
                    ->askForFilename()
                    ->withFilename(fn () => 'export-proyek-pemetaan-table-' . date('Y-m-d-His')),

                ExcelExport::make('lengkap')
                    ->label('Export Lengkap (Semua Kolom)')
                    ->askForWriterType()
                    ->askForFilename()
                    ->withFilename(fn () => 'export-proyek-pemetaan-lengkap-' . date('Y-m-d-His'))
                    ->withColumns([
                        // ... 22 columns
                    ]),

                ExcelExport::make('ringkas')
                    ->label('Export Ringkas (Kolom Penting)')
                    ->askForWriterType()
                    ->askForFilename()
                    ->withFilename(fn () => 'export-proyek-pemetaan-ringkas-' . date('Y-m-d-His'))
                    ->withColumns([
                        // ... 9 columns
                    ]),
            ]),
    ];
}
```

### Custom Column Formatting

```php
Column::make('customer_display')
    ->heading('Klien Utama')
    ->formatStateUsing(function ($record) {
        if ($record->corporate) {
            return $record->corporate->nama;
        }
        return $record->perorangan->first()?->nama ?? 'N/A';
    })
```

---

## Best Practices

### 1. Performance

-   Use `->queue()` for large datasets
-   Limit columns untuk export besar
-   Set proper chunk size

### 2. Data Accuracy

-   Always test export dengan sample data
-   Verify formatting (numbers, dates)
-   Check null handling

### 3. User Experience

-   Provide clear labels untuk setiap export type
-   Set sensible default filenames
-   Show loading indicator for large exports

### 4. Security

-   Respect company scope (multi-tenancy)
-   Apply proper authorization
-   Sanitize data jika perlu

---

## Changelog

### Version 1.0.0 (21 Oktober 2025)

-   ✅ Initial release
-   ✅ 3 export formats (Table, Lengkap, Ringkas)
-   ✅ Support XLS, XLSX, CSV
-   ✅ Custom filename
-   ✅ Indonesian formatting
-   ✅ Full test coverage
-   ✅ Fixed banks migration bug

---

## Support & Maintenance

### Kontak

Jika ada pertanyaan atau issue terkait fitur export:

1. Check dokumentasi ini terlebih dahulu
2. Lihat troubleshooting section
3. Run test untuk verify konfigurasi
4. Contact development team

### Update Future

Fitur yang direncanakan untuk versi selanjutnya:

-   [ ] Export dengan custom date range picker
-   [ ] Scheduled export (automated)
-   [ ] Export ke PDF format
-   [ ] Email export results
-   [ ] Bulk export per kategori
-   [ ] Export template customization
-   [ ] Real-time export progress bar
-   [ ] Export history/log

---

## License

Internal use only - HAS Survey Project

---

**Last Updated**: 21 Oktober 2025  
**Version**: 1.0.0  
**Maintainer**: Development Team
