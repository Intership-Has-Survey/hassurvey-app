<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Permission\Traits\HasRoles;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Level extends Model
{
    //
    use HasRoles, HasUuids, SoftDeletes, LogsActivity;


    protected $guarded = ['id'];

    public function pengajuanDana()
    {
        return $this->hasOne(PengajuanDana::class);
    }

    public function levelsteps()
    {
        return $this->hasMany(LevelStep::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Level Pengajuan');
    }
}
