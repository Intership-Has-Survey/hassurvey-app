# ✅ Checklist: Merge Export Feature ke Main

> Quick checklist untuk developer sebelum merge ke `main`

**Feature**: Export Layanan Pemetaan  
**Date**: 21 Oktober 2025  
**Developer**: \_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_

---

## 📋 Pre-Merge Checklist

### ✅ Code Quality

```
[ ] No linter errors
[ ] Code formatted properly
[ ] No console.log or dd() left behind
[ ] Comments updated/removed as needed
```

### ✅ Testing

```
[ ] Run: php artisan test --filter=ProjectExportTest
    Result: 5 tests passed (13 assertions)

[ ] Manual test: Export Kolom Tabel - Works ✓
[ ] Manual test: Export Lengkap - Works ✓
[ ] Manual test: Export Ringkas - Works ✓

[ ] Test with filter active - Works ✓
[ ] Test multi-tenancy - Works ✓
```

### ✅ Files Changed

```
Modified:
[ ] app/Filament/Resources/ProjectResource/Pages/ListProjects.php
[ ] database/migrations/2025_07_21_020111_create_banks_table.php

New:
[ ] tests/Feature/ProjectExportTest.php
[ ] docs/EXPORT_PROYEK_PEMETAAN.md
[ ] docs/QUICK_START_EXPORT.md
[ ] docs/DEPLOYMENT_EXPORT_FEATURE.md
[ ] docs/CHECKLIST_MERGE_TO_MAIN.md
[ ] docs/README.md
```

### ✅ Dependencies

```
[ ] pxlrbt/filament-excel installed
[ ] composer dump-autoload executed
[ ] No new .env variables needed
```

### ✅ Documentation

```
[ ] README.md updated
[ ] Feature documentation complete
[ ] Quick start guide created
[ ] Deployment guide created
```

---

## 🚀 Git Workflow

### Step 1: Commit

```bash
[ ] git status
[ ] git add .
[ ] git commit -m "feat: Add export to Excel feature for Layanan Pemetaan"
[ ] git push origin feature/export-proyek-pemetaan
```

### Step 2: Pull Request

```
[ ] Create PR to main
[ ] Title: feat: Add Export to Excel Feature for Layanan Pemetaan
[ ] Description complete (see DEPLOYMENT_EXPORT_FEATURE.md)
[ ] Screenshots attached
[ ] Request review from:
    [ ] Tech Lead
    [ ] Senior Developer
```

### Step 3: Review

```
[ ] Code reviewed by: _______________
[ ] Approved by: _______________
[ ] All comments addressed
```

### Step 4: Merge

```
[ ] PR approved
[ ] Conflicts resolved (if any)
[ ] Squash commits (if needed)
[ ] Merge to main
```

---

## 🔄 Deployment (Production)

### Pre-Deployment

```
[ ] Backup database created
[ ] Backup .env created
[ ] Deployment time scheduled: _______________
[ ] Team notified
```

### Deployment

```
[ ] php artisan down
[ ] git pull origin main
[ ] composer install --no-dev --optimize-autoloader
[ ] php artisan optimize:clear
[ ] php artisan config:cache
[ ] php artisan route:cache
[ ] php artisan view:cache
[ ] php artisan filament:cache-components
[ ] php artisan up
```

### Verification

```
[ ] Application accessible
[ ] Login works
[ ] Navigate to Layanan Pemetaan page
[ ] Export button visible
[ ] Export Kolom Tabel works
[ ] Export Lengkap works
[ ] Export Ringkas works
[ ] No errors in logs
```

---

## 📊 Post-Deployment

### Monitoring (First Hour)

```
[ ] Check logs: tail -f storage/logs/laravel.log
[ ] No errors reported
[ ] Export function working for users
[ ] Performance acceptable (page load <2s)
```

### User Testing

```
[ ] QA tested - Approved by: _______________
[ ] Product Owner tested - Approved by: _______________
[ ] End user sample tested (3+ users) - OK
```

### Sign-off

```
[ ] All checks passed
[ ] Monitoring in place
[ ] Documentation shared with team
[ ] Feature announcement sent

Signed: _______________
Date: _______________
```

---

## 🆘 If Something Goes Wrong

### Immediate Actions

```
[ ] Enable maintenance mode: php artisan down
[ ] Check logs: tail -f storage/logs/laravel.log
[ ] Identify issue
```

### Rollback (if needed)

```
[ ] git revert [commit-hash]
[ ] php artisan optimize:clear
[ ] php artisan up
[ ] Notify team
[ ] Create incident report
```

---

## 📞 Contacts

**Tech Lead**: \_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_  
**DevOps**: \_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_  
**Product Owner**: \_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_

---

## 📝 Notes

```
_______________________________________________________________
_______________________________________________________________
_______________________________________________________________
_______________________________________________________________
_______________________________________________________________
```

---

**Status**: [ ] Ready to Merge [ ] Issues Found [ ] Merged ✓

**Print this checklist or save as template for future deployments**

---

_For detailed documentation, see: [DEPLOYMENT_EXPORT_FEATURE.md](DEPLOYMENT_EXPORT_FEATURE.md)_

