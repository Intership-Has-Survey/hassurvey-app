<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisiMati extends Model
{
    use HasUuids, SoftDeletes, HasFactory;
    protected $table = 'visi_mati';

    protected $fillable = [
        'nama',
        'deskripsi',
        'user_id',
        'company_id',
        'sub_kategori',
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

    }
}
