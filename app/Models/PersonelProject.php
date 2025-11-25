<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PersonelProject extends Pivot
{
    //

    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    // Jika Anda menggunakan custom table name
    protected $table = 'personel_project';

    public function pembayaranPersonel()
    {
        //model, foreign key di model pembayaranpersonel, local key di model ini
        return $this->hasMany(PembayaranPersonel::class, 'personel_project_id', 'id');
    }
    public function personel()
    {
        return $this->belongsTo(Personel::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
