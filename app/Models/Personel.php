<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personel extends Model
{
    protected $table = 'personel';
    use HasFactory, HasUuids, SoftDeletes;
    protected $guarded = [];

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusAttribute()
    {
        $project = $this->projects()
            ->where('status_pekerjaan', '!=', 'selesai')
            ->first();

        return $project
            ? 'dalam project ' . $project->nama_project
            : 'tersedia';
    }
}
