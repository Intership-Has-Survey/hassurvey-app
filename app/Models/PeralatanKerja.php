<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PeralatanKerja extends Model
{
    protected $table = 'peralatankerja';
    use HasFactory, HasUuids;
    protected $fillable = [
        'project_id',
        'nama_alat',
        'jenis_alat',
        'jumlah',
        'user_id',
        'keterangan',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
