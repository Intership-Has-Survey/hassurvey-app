<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    //
    protected $fillable = [
        'nomor',
        'urut',
        'bulan',
        'tahun',
        'company_id',
    ];
}
