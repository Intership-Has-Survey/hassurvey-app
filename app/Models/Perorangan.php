<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    public function corporates()
    {
        return $this->belongsToMany(Corporate::class, 'perorangan_corporate')
            ->using(PeroranganCorporate::class)
            ->withPivot('user_id')
            ->withTimestamps();
    }
}
