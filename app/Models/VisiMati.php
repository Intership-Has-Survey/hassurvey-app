<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class VisiMati extends Model
{
    use HasUuids, SoftDeletes, HasFactory;
    protected $table = 'visi_mati';

    protected $fillable = [
        'nama',
        'deskripsi',
        'subcategorizables',
    ];

    protected $casts = [
        'subcategorizables' => 'array',
    ];

    public function tabungans()
    {
        return $this->hasMany(Tabungan::class);
    }

    public function operasionals()
    {
        return $this->hasMany(Operasional::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($pemilik) {
            if (!$pemilik->user_id && Auth::check()) {
                $pemilik->user_id = Auth::id();
            }
        });
    }
}
