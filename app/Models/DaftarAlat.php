<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth; // <-- Import class Auth
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class DaftarAlat extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'daftar_alat';

    protected $guarded = [];

    protected $casts = [
        'kondisi' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     * Ini akan secara otomatis mengatur nilai default saat data baru dibuat.
     */
    protected static function booted(): void
    {
        static::creating(function ($daftarAlat) {
            // Atur user_id jika belum ada dan user sedang login
            if (!$daftarAlat->user_id && Auth::check()) {
                $daftarAlat->user_id = Auth::id();
            }
            // Atur nilai default untuk kondisi jika belum diatur
            if (is_null($daftarAlat->kondisi)) {
                $daftarAlat->kondisi = true; // Default: Baik
            }
            // Atur nilai default untuk status jika belum diatur
            if (is_null($daftarAlat->status)) {
                $daftarAlat->status = true; // Default: Tersedia
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenisAlat()
    {
        return $this->belongsTo(JenisAlat::class, 'jenis_alat_id');
    }
    public function merk()
    {
        return $this->belongsTo(Merk::class, 'merk_id');
    }

    public function scopeTersedia($query)
    {
        return $query->where('status', true)->where('kondisi', true);
    }

    public function pemilik()
    {
        return $this->belongsTo(Pemilik::class, 'pemilik_id');
    }

    public function sewa()
    {
        return $this->belongsToMany(Sewa::class, 'riwayat_sewa', 'daftar_alat_id', 'sewa_id')
            // SOLUSI: Memberitahu Eloquent untuk menggunakan model pivot kustom kita
            ->using(RiwayatSewa::class)
            ->withPivot(['tgl_keluar', 'tgl_masuk', 'harga_perhari', 'biaya_sewa', 'user_id']) // Pastikan semua kolom pivot ada
            ->withTimestamps();
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'riwayat_sewa', 'daftar_alat_id', 'sewa_id')
            // SOLUSI: Memberitahu Eloquent untuk menggunakan model pivot kustom kita
            ->using(RiwayatSewa::class)
            ->withPivot(['tgl_keluar', 'tgl_masuk', 'harga_perhari', 'biaya_sewa', 'user_id']) // Pastikan semua kolom pivot ada
            ->withTimestamps();
    }
}
