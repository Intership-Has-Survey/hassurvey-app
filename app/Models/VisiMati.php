<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisiMati extends Model
{
    use HasUuids, SoftDeletes, HasFactory;
    protected $table = 'visi_mati';

    protected $fillable = [
        'nama',
        'deskripsi',
        'user_id',
        'sub_kategori',
        'tabungan',
        'operasional',
        'kewajiban_bayars',
        'penerima_operasionals',
        'pemasukans',
        'pengeluarans',
        'company_id',
    ];

    protected $casts = [
        'sub_kategori' => 'array',
    ];

    public function tabungan(): HasOne
    {
        return $this->hasOne(Tabungan::class);
    }

    public function operasional(): HasOne
    {
        return $this->hasOne(Operasional::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function pemasukans()
    {
        return $this->hasManyThrough(
            \App\Models\Pemasukan::class,
            \App\Models\Tabungan::class,
            'visi_mati_id', // Foreign key on Tabungan table...
            'tabungan_id', // Foreign key on Pemasukan table...
            'id', // Local key on VisiMati table...
            'id'  // Local key on Tabungan table...
        );
    }

    public function pengeluarans()
    {
        return $this->hasManyThrough(
            \App\Models\Pengeluaran::class,
            \App\Models\Tabungan::class,
            'visi_mati_id', // Foreign key on Tabungan table...
            'tabungan_id', // Foreign key on Pengeluaran table...
            'id', // Local key on VisiMati table...
            'id'  // Local key on Tabungan table...
        );
    }

    public function kewajibanBayars()
    {
        return $this->hasManyThrough(
            \App\Models\KewajibanBayar::class,
            \App\Models\Operasional::class,
            'visi_mati_id', // Foreign key on Operasional table...
            'operasional_id', // Foreign key on KewajibanBayar table...
            'id', // Local key on VisiMati table...
            'id'  // Local key on Operasional table...
        );
    }

    public function penerimaOperasionals()
    {
        return $this->hasManyThrough(
            \App\Models\PenerimaOperasional::class,
            \App\Models\Operasional::class,
            'visi_mati_id', // Foreign key on Operasional table...
            'operasional_id', // Foreign key on PenerimaOperasional table...
            'id', // Local key on VisiMati table...
            'id'  // Local key on Operasional table...
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (!$model->user_id && Auth::check()) {
                $model->user_id = Auth::id();
            }
        });

        static::created(function (VisiMati $visimati) {
            DB::transaction(function () use ($visimati) {
                if (in_array('tabungan', $visimati->sub_kategori ?? [])) {
                    if (!$visimati->tabungan) {
                        $tabungan = new \App\Models\Tabungan();
                        $tabungan->nama = 'Tabungan untuk VisiMati: ' . $visimati->nama;
                        $tabungan->target_nominal = 0;
                        $tabungan->target_tipe = 'orang';
                        $tabungan->visimati_id = $visimati->id;
                        $tabungan->save();
                    }
                }
                if (in_array('operasional', $visimati->sub_kategori ?? [])) {
                    if (!$visimati->operasional) {
                        $operasional = new \App\Models\Operasional();
                        $operasional->nama = 'Operasional untuk VisiMati: ' . $visimati->nama;
                        $operasional->visimati_id = $visimati->id;
                        $operasional->save();
                    }
                }
            });
        });
    }
}
