<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    //

    protected $guarded = [];

    public function accounts()
    {
        return $this->hasMany(BankAccount::class);
    }
    public $timestamps = false;
}
