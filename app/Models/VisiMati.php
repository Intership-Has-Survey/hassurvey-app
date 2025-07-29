<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class VisiMati extends Model
{
    protected $table = 'visi_mati';

    protected $fillable = [
        'nama',
        'deskripsi',
        'subcategorizables',
    ];

    protected $casts = [
        'subcategorizables' => 'array',
    ];

    public function tabungans(): MorphToMany
    {
        return $this->morphToMany(Tabungan::class, 'subcategorizable', 'subcategorizables', 'visi_mati_id', 'subcategorizable_id');
    }

    public function operasionals(): MorphToMany
    {
        return $this->morphToMany(Operasional::class, 'subcategorizable', 'subcategorizables', 'visi_mati_id', 'subcategorizable_id');
    }
}
