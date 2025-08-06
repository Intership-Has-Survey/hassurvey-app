<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
