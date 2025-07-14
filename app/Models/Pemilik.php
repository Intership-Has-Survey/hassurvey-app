<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Pemilik extends Model
{
    use HasUuids, HasFactory, SoftDeletes;
    
    protected $table = 'pemilik';

    protected $fillable = [
        'nama',
        'NIK',
        'email',
        'telepon',
        'alamat',
        'user_id',
    ];

    public function daftarAlat()
    {
        return $this->hasMany(DaftarAlat::class, 'pemilik_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
