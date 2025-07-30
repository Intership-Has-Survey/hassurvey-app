<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisiMati extends Model
{
    use HasUuids, SoftDeletes, HasFactory;
    protected $table = 'visi_mati';

    protected $fillable = [
        'nama',
        'deskripsi',
        'user_id',
    ];

    public function tabungan(): HasOne
    {
        return $this->hasOne(Tabungan::class);
    }

    public function operasional(): HasOne
    {
        return $this->hasOne(Operasional::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (!$model->user_id && Auth::check()) {
                $model->user_id = Auth::id();
            }
        });
    }
}
