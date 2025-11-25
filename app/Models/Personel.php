<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Personel extends Model
{
    protected $table = 'personel';
    use HasFactory, HasUuids, SoftDeletes, LogsActivity;
    protected $guarded = [];

    // public function projects()
    // {
    //     return $this->belongsToMany(Project::class);
    // }

    public function project()
    {
        return $this->belongsToMany(Project::class, 'personel_project')
            ->using(PersonelProject::class)
            ->withPivot('user_id', 'peran', 'tanggal_mulai', 'tanggal_berakhir')
            ->withTimestamps();
    }

    public function projects()
    {
        return $this->hasMany(PersonelProject::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    //membuat kolom baru tanpa tersimpan di database, bisa dipanggil di resources
    public function getStatusAttribute()
    {

        $activeProject = $this->project()->wherePivotNull('tanggal_berakhir')->first();

        if ($activeProject) {
            return 'Dalam Proyek: ' . $activeProject->nama_project;
        }

        return 'Tersedia';
    }

    public function pembayaranPersonel()
    {
        return $this->hasMany(PembayaranPersonel::class);
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
            ->useLogName('Personel');
    }
}
