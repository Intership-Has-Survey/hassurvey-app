<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class BankAccount extends Model
{
    //
    use HasUuids;

    protected $guarded = ['id'];
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
