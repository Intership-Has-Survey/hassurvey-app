<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Operasional extends Model
{
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
