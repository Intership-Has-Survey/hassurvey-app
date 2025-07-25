<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class level extends Model
{
    //
    use HasRoles, HasUuids;


    protected $guarded = ['id'];

    public function pengajuanDana()
    {
        return $this->hasOne(PengajuanDana::class);
    }

    public function levelsteps()
    {
        return $this->hasMany(LevelStep::class);
    }
}
