<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tabungan extends Model
{
    use HasUuids, HasFactory, SoftDeletes;
    protected $table = 'tabungans';

    protected $fillable = [
        'nama',
        'target_nominal',
        'target_tipe',
        'visi_mati_id',
        'pemasukans',
        'pengeluarans',
        'company_id',
        'id',
    ];

    public function visiMati(): BelongsTo
    {
        return $this->belongsTo(VisiMati::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function pemasukans(): HasMany
    {
        return $this->hasMany(Pemasukan::class);
    }

    public function pengeluarans(): HasMany
    {
        return $this->hasMany(Pengeluaran::class);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (blank($model->company_id)) {
                $model->company_id = \Filament\Facades\Filament::getTenant()?->getKey();
            }
        });
    }

}
