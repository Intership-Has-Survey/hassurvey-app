<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Pengeluaran extends Model
{
    use HasUuids;
    protected $table = 'pengeluarans';

    protected $fillable = [
        'tabungan_id',
        'tanggal',
        'jumlah',
        'deskripsi',
        'pengeluaranable_id',
        'pengeluaranable_type',
        'visi_mati_id',
    ];

    public function tabungan(): BelongsTo
    {
        return $this->belongsTo(Tabungan::class);
    }

    public function pengeluaranable(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function booted()
    {
        static::creating(function ($pengeluaran) {
            // Pastikan tabungan_id ada
            if (!$pengeluaran->tabungan_id && $pengeluaran->visi_mati_id) {
                $tabungan = Tabungan::where('visi_mati_id', $pengeluaran->visi_mati_id)->first();
                if ($tabungan) {
                    $pengeluaran->tabungan_id = $tabungan->id;
                }
            }

            // Validasi saldo cukup
            if ($pengeluaran->tabungan_id) {
                $tabungan = Tabungan::find($pengeluaran->tabungan_id);
                $saldo = $tabungan->pemasukans()->sum('jumlah') - $tabungan->pengeluarans()->sum('jumlah');

                if ($pengeluaran->jumlah > $saldo) {
                    throw new \Exception('Saldo tabungan tidak mencukupi.');
                }
            }
        });

        static::updating(function ($pengeluaran) {
            if ($pengeluaran->tabungan_id) {
                $tabungan = Tabungan::find($pengeluaran->tabungan_id);

                $saldo = $tabungan->pemasukans()->sum('jumlah') -
                    $tabungan->pengeluarans()->where('id', '!=', $pengeluaran->id)->sum('jumlah');

                if ($pengeluaran->jumlah > $saldo) {
                    throw new \Exception('Saldo tabungan tidak mencukupi setelah update.');
                }
            }
        });
    }
}
