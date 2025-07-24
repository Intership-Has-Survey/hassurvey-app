<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Merk extends Model
{
    use HasUuids, HasFactory, SoftDeletes;
    protected $table = 'merk';

    protected $guarded = [];

    public function daftarAlat()
    {
        return $this->hasMany(DaftarAlat::class, 'merk_id');
    }

    protected static function booted(): void
    {
        static::creating(function ($merk) {
            if (!$merk->user_id && Auth::check()) {
                $merk->user_id = Auth::id();
            }
        });
    }
}
