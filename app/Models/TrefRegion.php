<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrefRegion extends Model
{
    protected $table = 'tref_regions';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
    ];
}
