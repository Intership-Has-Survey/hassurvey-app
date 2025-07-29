<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bangunan extends Model
{
    protected $table = 'bangunans';

    protected $fillable = [
        'nama',
        'alamat',
    ];

    public function tabungans(): MorphMany
    {
        return $this->morphMany(Tabungan::class, 'detailable');
    }

    public function kewajibanBayars(): HasMany
    {
        return $this->hasMany(KewajibanBayar::class);
    }
}
