# Quick Start: Export Layanan Pemetaan ke Excel

> Panduan cepat untuk menggunakan fitur export layanan pemetaan

## 🚀 Cara Cepat Export

### Langkah 1: Buka Halaman Proyek

```
Dashboard → Layanan → Pemetaan
```

### Langkah 2: Klik "Export ke Excel"

Tombol hijau di bagian atas (header), sebelah kanan tombol "Tambah Layanan Pemetaan Baru"

### Langkah 3: Pilih Format

Pilih salah satu:

-   **Export Kolom Tabel** - Quick export (kolom yang terlihat di tabel)
-   **Export Lengkap** - Semua data (22 kolom lengkap)
-   **Export Ringkas** - Data penting saja (9 kolom)

### Langkah 4: Pilih Tipe File

-   **XLSX** - Recommended (Excel modern)
-   **XLS** - Excel lama
-   **CSV** - Text format

### Langkah 5: Klik Export

File otomatis terdownload! ✅

---

## 📊 Pilih Format yang Tepat

| Kebutuhan                 | Format          | Alasan                     |
| ------------------------- | --------------- | -------------------------- |
| Quick view untuk meeting  | **Ringkas**     | Hanya 9 kolom penting      |
| Sharing dengan team       | **Kolom Tabel** | Sesuai tampilan di layar   |
| Laporan manajemen lengkap | **Lengkap**     | 22 kolom detail            |
| Analisis data             | **Lengkap**     | Semua data termasuk lokasi |
| Follow-up sales           | **Ringkas**     | Fokus ke status & klien    |

---

## 💡 Tips

### Filter Dulu, Baru Export

1. Pilih tab status yang diinginkan (Prospect, Closing, dll)
2. Set filter date range jika perlu
3. Baru klik export
4. Data yang di-export akan sesuai filter!

### Nama File Default

Format: `export-proyek-pemetaan-[type]-2025-10-21-143050`

Mau custom? Isi nama file saat export!

### Format Data

-   **Angka**: 15.000.000 (separator titik)
-   **Tanggal**: 21/10/2025 14:30:50
-   **Boolean**: Ya / Tidak
-   **Kosong**: N/A atau -

---

## ❓ Problem?

### Export tidak muncul data

→ Check filter yang aktif, mungkin terlalu ketat

### Button export tidak ada

→ Clear cache: `php artisan optimize:clear`

### Error saat download

→ Coba format file lain (XLSX → CSV)

---

## 📖 Dokumentasi Lengkap

Lihat [EXPORT_LAYANAN_PEMETAAN.md](EXPORT_LAYANAN_PEMETAAN.md) untuk:

-   Penjelasan detail setiap kolom
-   Troubleshooting lengkap
-   Technical implementation
-   Testing guide

---

**Butuh bantuan?** Contact development team atau baca dokumentasi lengkap di atas.

**Last Updated**: 21 Oktober 2025
