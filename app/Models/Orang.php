<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Orang extends Model
{
    protected $table = 'orangs';

    protected $fillable = [
        'nama',
        'nik',
        'alamat',
    ];

    public function tabungans(): MorphMany
    {
        return $this->morphMany(Tabungan::class, 'detailable');
    }
}
