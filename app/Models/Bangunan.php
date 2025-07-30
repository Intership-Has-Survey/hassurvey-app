<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Bangunan extends Model
{
    use HasUuids;
    protected $table = 'bangunans';

    protected $fillable = [
        'nama',
        'alamat',
    ];

    public function pengeluarans(): MorphMany
    {
        return $this->morphMany(Pengeluaran::class, 'pengeluaranable');
    }
}
