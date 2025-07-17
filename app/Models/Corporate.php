<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

// PERBAIKAN: Nama kelas diubah menjadi PascalCase (Corporate)
class Corporate extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'corporate';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function perorangan()
    {
        // PERBAIKAN: Menggunakan nama kelas Perorangan yang sudah diperbaiki
        return $this->belongsToMany(Perorangan::class, 'perorangan_corporate')
            ->using(PeroranganCorporate::class) // Memberitahu Eloquent untuk menggunakan model pivot kustom
            ->withPivot('user_id')
            ->withTimestamps();
    }

    public function sewa(): MorphMany
    {
        // Logikanya sama persis dengan di model Perorangan
        return $this->morphMany(Sewa::class, 'customer');
    }

    public function projects(): MorphMany
    {
        // A Perorangan can have many projects.
        // The 'customer' parameter refers to the 'customer_type' and 'customer_id' columns
        // on the 'projects' table.
        return $this->morphMany(Project::class, 'customer');
    }
}
