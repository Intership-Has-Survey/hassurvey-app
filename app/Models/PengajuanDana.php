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

    public function detailPengajuans(): HasMany
    {
        return $this->hasMany(DetailPengajuan::class);
    }


    public function transaksiPembayarans(): HasMany
    {
        return $this->hasMany(TransaksiPembayaran::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['judul_pengajuan', 'status', 'deskripsi_pengajuan', 'nama_bank', 'nomor_rekening', 'nama_pemilik_rekening'])
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

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function approve()
    {
        $userRole = auth()->user()->roles;
        $roleName = auth()->user()->roles->first()?->name;
        // dd($userRole);
        // dd($roleName);
        // Cari level
        $level = $this->level;
        $steps = $level->levelSteps()->orderBy('step')->pluck('role_id')->toArray();

        $currentIndex = array_search(auth()->user()->roles->first()?->id, $steps);
        // dd($currentIndex);
        // dd($steps);
        // dd($userRole);

        if ($currentIndex !== false && isset($steps[$currentIndex + 1])) {
            // Masih ada step berikutnya
            $this->update(['dalam_review' => $steps[$currentIndex + 1]]);
        } else {
            // Sudah final step
            $this->update([
                'dalam_review' => null,
                'disetujui' => 'approved',
            ]);
        }
    }
    // public function approve()
    // {
    //     $userRole = auth()->user()->role;
    //     // Cari level
    //     $level = $this->level;
    //     $steps = $level->levelSteps()->orderBy('step')->pluck('role_id')->toArray();

    //     $currentIndex = array_search($userRole, $steps);

    //     if ($currentIndex !== false && isset($steps[$currentIndex + 1])) {
    //         // Masih ada step berikutnya
    //         $this->update(['dalam_review' => $steps[$currentIndex + 1]]);
    //     } else {
    //         // Sudah final step
    //         $this->update([
    //             'dalam_review' => null,
    //             'disetujui' => 'approved',
    //         ]);
    //     }
    // }
}
