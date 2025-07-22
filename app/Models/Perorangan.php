<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Perorangan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'perorangan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * Get the user that owns the record.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The corporate entities that this person is associated with.
     */
    public function corporates(): BelongsToMany
    {
        return $this->belongsToMany(Corporate::class, 'perorangan_corporate');
    }

    /**
     * Get all of the perorangan's rentals.
     */
    public function sewa(): MorphMany
    {
        return $this->morphMany(Sewa::class, 'customer');
    }

    /**
     * Get all of the projects for the Perorangan.
     *
     * SOLUSI: Mengubah relasi menjadi morphMany agar sesuai dengan struktur polimorfik.
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_perorangan');
    }
}
