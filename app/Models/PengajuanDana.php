<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class PengajuanDana extends Model
{
    use HasFactory, HasUuids, SoftDeletes, LogsActivity, HasRoles;

    protected $guarded = ['id'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sewa(): BelongsTo
    {
        return $this->belongsTo(Sewa::class);
    }

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function kalibrasi(): BelongsTo
    {
        return $this->belongsTo(Kalibrasi::class);
    }

    public function detailPengajuans(): HasMany
    {
        return $this->hasMany(DetailPengajuan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Pengajuan');
    }

    public function roles()
    {
        return $this->belongsTo(Role::class, 'dalam_review');
    }

    public function updateTotalHarga()
    {
        $this->nilai = $this->detailPengajuans->sum(function ($detail) {
            return $detail->qty * $detail->harga_satuan;
        });

        $this->save();
    }

    public function updateNilai()
    {
        $total = $this->detailPengajuans->sum(function ($detail) {
            return $detail->qty * $detail->harga_satuan;
        });

        return $total;
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function approve()
    {
        $userRole = auth()->user()->roles;
        $roleName = auth()->user()->roles->first()?->name;
        $level = $this->level;
        $steps = $level->levelSteps()->orderBy('step')->pluck('role_id')->toArray();

        $currentIndex = array_search(auth()->user()->roles->first()?->id, $steps);
        if ($currentIndex !== false && isset($steps[$currentIndex + 1])) {
            // Masih ada step berikutnya
            $this->update(['dalam_review' => $steps[$currentIndex + 1]]);
            $this->update(['disetujui' => $roleName]);
            $this->update(['ditolak' => null]);
            $this->update(['alasan' => null]);
        } else {
            // Sudah final step
            $this->update([
                'dalam_review' => 'approved',
                'disetujui' => $roleName,
                'ditolak' => null,
                'alasan' => null,
            ]);
        }
    }

    public function reject($alasan = null)
    {
        $roleName = auth()->user()->roles->first()?->name;
        $level = $this->level;
        $steps = $level->levelSteps()->orderBy('step')->pluck('role_id')->toArray();

        $currentIndex = array_search(auth()->user()->roles->first()?->id, $steps);

        if ($currentIndex !== false && isset($steps[$currentIndex - 1])) {
            // Masih ada step sebelumnya → rollback ke step sebelumnya
            $this->update([
                'dalam_review' => $steps[$currentIndex - 1],
                'disetujui' => null, // Reset approval
                'ditolak' => $roleName, // Reset approval
                'alasan' => $alasan,
            ]);
        } else {
            // Sudah di step pertama → final reject
            $this->update([
                'dalam_review' => 'rejected',
                'disetujui' => null,
                'ditolak' => $roleName,
                'alasan' => $alasan,
            ]);
        }
    }

    public function statusPengeluarans()
    {
        return $this->morphMany(TransaksiPembayaran::class, 'payable');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function pengajuanable(): MorphTo
    {
        return $this->morphTo();
    }

    public function pengajuanDanas()
    {
        return $this->morphMany(PengajuanDana::class, 'pengajuanable');
    }

    public function getTargetLabelAttribute(): string
    {
        if (! $this->pengajuanable) {
            return 'Untuk: In-House (Internal)';
        }

        return 'Untuk ' . class_basename($this->pengajuanable_type) . ': ' . ($this->pengajuanable->nama ?? $this->pengajuanable->judul ?? '-');
    }

    public function updateDibayar()
    {
        $this->dibayar = $this->transaksiPembayarans()->sum('nilai');
        $this->save();
    }

    public function kategoriPengajuan()
    {
        return $this->belongsTo(KategoriPengajuan::class, 'katpengajuan_id', 'code');
    }

    protected static function booted(): void
    {
        static::saved(function (PengajuanDana $pengajuan) {
            $totalNilai = $pengajuan->detailPengajuans()->sum('total');
            $pengajuan->updateQuietly(['nilai' => $totalNilai]);
        });

        static::deleting(function (PengajuanDana $pengajuan) {
            $pengajuan->detailPengajuans()->delete();
        });
    }
}
