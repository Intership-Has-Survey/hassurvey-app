<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class LevelStep extends Model
{
    //
    use HasRoles;
    use HasUuids;
    protected $guarded = ['id'];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function roles()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
