<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use App\Models\StatusPekerjaan;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Project extends Model
{
    //
    use HasUuids;
    protected $guarded = ['id'];
    // Key type is string, not integer
    public function personels()
    {
        return $this->belongsToMany(Personel::class)->withPivot('peran');
    }

    public function Kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'id');
    }

    public function Sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'sales_id', 'id');
    }


    public function statusPekerjaan()
    {
        return $this->hasMany(StatusPekerjaan::class);
    }
}
