<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusPembayaran extends Model
{
    use  HasUuids;
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
    }

    // public function project()
    // {
    //     return $this->belongsTo(Project::class);
    // }

    public function payable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function getTableRecordKey($record): string
    {
        return $record->id ?? $record->payable_id ?? uniqid();
    }
}
