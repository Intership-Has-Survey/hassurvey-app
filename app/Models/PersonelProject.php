<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonelProject extends Model
{

    use HasUuids, HasFactory;

    protected $table = 'personel_project';
    protected $fillable = [
        'project_id',
        'personel_id',
        'tanggal_mulai',
        'tanggal_berakhir',
        'peran',
        'user_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function personel()
    {
        return $this->belongsTo(Personel::class);
    }
}
