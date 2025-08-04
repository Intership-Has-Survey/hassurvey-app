<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Orang extends Model
{
    use HasUuids;
    protected $table = 'orangs';

    protected $fillable = [
        'nama',
        'nik',
        'alamat',
    ];

    public function pengeluarans(): MorphMany
    {
        return $this->morphMany(Pengeluaran::class, 'pengeluaranable');
    }
}
