<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PembayaranPersonel extends Model
{
    //
    use HasUuids;
    protected $guarded = ['id'];

    // public function payable()
    // {
    //     return $this->morphTo();
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // protected function getTableRecordKey($record): string
    // {
    //     return $record->id ?? $record->payable_id ?? uniqid();
    // }

    public function statusPengeluarans()
    {
        return $this->morphMany(TransaksiPembayaran::class, 'payable');
    }

    public function personel()
    {
        return $this->belongsTo(Personel::class);
    }
}
