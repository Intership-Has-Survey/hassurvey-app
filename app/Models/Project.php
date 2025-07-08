<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Models\StatusPekerjaan;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Project extends Model
{
    //
    use HasUuids;
    protected $guarded = ['id'];

    public $incrementing = false;      // Disable auto-incrementing
    protected $keyType = 'string';     // Key type is string, not integer

    public function Kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'id');
    }

    public function Sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate UUID automatically if not set
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function statusPekerjaan()
    {
        return $this->hasMany(StatusPekerjaan::class);
    }
}
