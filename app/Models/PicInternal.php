<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PicInternal extends Model
{
    //
    use HasUuids, SoftDeletes, LogsActivity;
    protected $table = 'pic_internals';
    protected $guarded = ['id'];

    public function projects()
    {
        return $this->hasMany(Project::class, 'pic_internal_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('Pic Internal');
    }

    public function provinsiRegion()
    {
        return $this->belongsTo(TrefRegion::class, 'provinsi', 'code');
    }

    // Relationship for kota
    public function kotaRegion()
    {
        return $this->belongsTo(TrefRegion::class, 'kota', 'code');
    }

    // Relationship for kecamatan
    public function kecamatanRegion()
    {
        return $this->belongsTo(TrefRegion::class, 'kecamatan', 'code');
    }

    // Relationship for desa
    public function desaRegion()
    {
        return $this->belongsTo(TrefRegion::class, 'desa', 'code');
    }
}
