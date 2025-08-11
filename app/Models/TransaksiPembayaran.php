<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiPembayaran extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    protected $guarded = [];

    // public function pengajuanDana(): BelongsTo
    // {
    //     return $this->belongsTo(PengajuanDana::class);
    // }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }

    protected function getTableRecordKey($record): string
    {
        return $record->id ?? $record->payable_id ?? uniqid();
    }
}
