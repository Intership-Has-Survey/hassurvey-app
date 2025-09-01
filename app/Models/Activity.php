<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;
use Filament\Facades\Filament;

class Activity extends SpatieActivity
{
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    protected static function booted()
    {
        static::creating(function ($activity) {
            if (Filament::getTenant()) {
                $activity->company_id = Filament::getTenant()->id;
            }
        });
    }
}
