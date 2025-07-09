<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DaftarAlat extends Model
{
    use HasUuids, HasFactory;

    protected $primaryKey = 'uuid';

    protected $table = 'daftar_alat';

    protected $fillable = [
        'user_id',
        'nama_alat',
        'jenis_alat',
        'merk',
        'kondisi',
        'status',
        'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
