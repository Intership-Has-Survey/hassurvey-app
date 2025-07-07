<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\StatusPekerjaan;

class Project extends Model
{
    //
    protected $guarded = ['id'];

    public $incrementing = false;      // Disable auto-incrementing
    protected $keyType = 'string';     // Key type is string, not integer

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
