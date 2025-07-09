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
        'jenis_personel',
        'nama_personel',
        'keterangan',
        'user_id',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function getStatusAttribute()
    {
        $project = $this->projects()
            ->where('status_pekerjaan_lapangan', '!=', 'selesai')
            ->first();

        return $project
            ? 'dalam project ' . $project->nama_project
            : 'tersedia';
    }
}
