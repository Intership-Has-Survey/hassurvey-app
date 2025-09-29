<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DetailPengajuan extends Model
{
    use HasFactory, HasUuids, LogsActivity;
    protected $guarded = [];

    public function pengajuanDana(): BelongsTo
    {
        return $this->belongsTo(PengajuanDana::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id', 'pengajuan_dana_id', 'deskripsi', 'qty', 'satuan', 'harga_satuan', 'total'])
            ->logOnlyDirty()
            ->useLogName('Detail Pengajuan');
    }
}
