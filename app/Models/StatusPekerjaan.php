<?php

namespace App\Models;

// app/Models/StatusPekerjaan.php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StatusPekerjaan extends Model
{
    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->getKey()) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
