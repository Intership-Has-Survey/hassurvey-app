# Dokumentasi HAS Survey App

Selamat datang di dokumentasi HAS Survey App. Di sini Anda dapat menemukan berbagai dokumentasi teknis dan panduan penggunaan aplikasi.

## 📚 Daftar Dokumentasi

### Quick Start Guides

-   [⚡ Quick Start: Export Layanan Pemetaan](QUICK_START_EXPORT.md) - Panduan singkat 5 langkah export data layanan pemetaan

### Features Documentation

-   [📊 Export Layanan Pemetaan](EXPORT_LAYANAN_PEMETAAN.md) - Panduan lengkap fitur export data layanan pemetaan ke Excel

### Developer Guides

-   [🚀 Deployment Guide: Export Feature](DEPLOYMENT_EXPORT_FEATURE.md) - Panduan teknis merge ke main & deployment

## 🚀 Quick Links

### Export Features

-   [Quick Start (5 Langkah)](QUICK_START_EXPORT.md) - Langsung pakai!
-   [Cara Export Lengkap](EXPORT_LAYANAN_PEMETAAN.md#cara-penggunaan)
-   [Pilihan Format Export](EXPORT_LAYANAN_PEMETAAN.md#pilihan-format-export)
-   [Troubleshooting Export](EXPORT_LAYANAN_PEMETAAN.md#troubleshooting)

### For Developers

-   [Pre-Merge Checklist](DEPLOYMENT_EXPORT_FEATURE.md#pre-merge-checklist) - Before merge ke main
-   [Deployment Steps](DEPLOYMENT_EXPORT_FEATURE.md#deployment-steps) - Step by step deployment
-   [Testing Requirements](DEPLOYMENT_EXPORT_FEATURE.md#testing-requirements) - What to test
-   [Rollback Plan](DEPLOYMENT_EXPORT_FEATURE.md#rollback-plan) - Jika ada masalah

## 📝 Catatan

Dokumentasi ini akan terus diupdate seiring dengan penambahan fitur baru. Pastikan selalu membaca dokumentasi terbaru sebelum menggunakan fitur tertentu.

## 🔧 Development

### Testing

Semua fitur yang terdokumentasi memiliki unit test. Untuk menjalankan test:

```bash
# Run all tests
php artisan test

# Run specific feature test
php artisan test --filter=ProjectExportTest
```

### Contributing

Jika Anda menambahkan fitur baru, pastikan untuk:

1. Membuat dokumentasi yang jelas
2. Menambahkan unit test
3. Update README ini dengan link ke dokumentasi baru

---

**Last Updated**: 21 Oktober 2025  
**Project**: HAS Survey App  
**Team**: Development Team
