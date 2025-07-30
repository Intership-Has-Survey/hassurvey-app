<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operasional extends Model
{
    use HasUuids, HasFactory, SoftDeletes;
    protected $table = 'operasionals';

    protected $fillable = [
        'nama',
        'target',
    ];

    public function visiMati(): MorphToMany
    {
        return $this->morphToMany(VisiMati::class, 'subcategorizable', 'subcategorizables', 'subcategorizable_id', 'visi_mati_id');
    }

    public function kewajibanBayars(): HasMany
    {
        return $this->hasMany(KewajibanBayar::class);
    }
}
