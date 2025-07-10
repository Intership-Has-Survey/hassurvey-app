<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DaftarAlat extends Model
{
    use HasUuids, HasFactory, softDeletes;

    protected $primaryKey = 'id';

    protected $table = 'daftar_alat';

    protected $fillable = [
        'user_id',
        'nama_alat',
        'jenis_alat',
        'merk',
        'kondisi',
        'status',
        'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeTersedia($query)
    {
        return $query->whereDoesntHave('projects', function ($q) {
            $q->wherePivot('status', 'Terpakai');
        });
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'daftar_alat_project', 'daftar_alat_id', 'project_id')
            ->withPivot(['status', 'user_id'])
            ->withTimestamps();
    }



}
