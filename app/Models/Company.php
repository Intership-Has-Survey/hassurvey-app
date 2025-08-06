<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    use HasUuids;

    protected $fillable = ['name'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function daftarAlats()
    {
        return $this->hasMany(DaftarAlat::class);
    }
}
