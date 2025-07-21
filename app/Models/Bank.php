<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Bank extends Model
{
    //
    use HasUuids;
    protected $guarded = ['id'];

    public function accounts()
    {
        return $this->hasMany(BankAccount::class);
    }
}
