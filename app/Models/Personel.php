<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Personel extends Model
{
    protected $table = 'personel';
    use HasFactory, HasUuids;
    protected $fillable = [
        'project_id',
        'jenis_personel',
        'nama_personel',
        'keterangan',
        'user_id',
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
