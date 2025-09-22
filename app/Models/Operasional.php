<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operasional extends Model
{
    use HasUuids, HasFactory, SoftDeletes;
    protected $table = 'operasionals';

    protected $fillable = [
        'nama',
        'visi_mati_id',
        'kewajiban_bayars',
        'penerima_operasionals',
        'company_id',
        'id',
    ];

    public function visiMati(): BelongsTo
    {
        return $this->belongsTo(VisiMati::class);
    }

    public function kewajibanBayars(): HasMany
    {
        return $this->hasMany(KewajibanBayar::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function penerimaOperasionals(): HasMany
    {
        return $this->hasMany(PenerimaOperasional::class);
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
