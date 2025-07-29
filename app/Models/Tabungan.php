<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tabungan extends Model
{
    protected $table = 'tabungans';

    protected $fillable = [
        'nama',
        'target',
        'detailable_id',
        'detailable_type',
    ];

    public function visiMati(): MorphToMany
    {
        return $this->morphToMany(VisiMati::class, 'subcategorizable', 'subcategorizables', 'subcategorizable_id', 'visi_mati_id');
    }

    public function detailable(): MorphTo
    {
        return $this->morphTo();
    }

    public function pemasukans(): HasMany
    {
        return $this->hasMany(Pemasukan::class);
    }

    public function pengeluarans(): HasMany
    {
        return $this->hasMany(Pengeluaran::class);
    }
}
