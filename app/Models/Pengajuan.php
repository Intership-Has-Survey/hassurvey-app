<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Pengajuan extends Model
{
    use HasRoles, HasUuids;
    //
    protected $guarded = ['id'];
}
