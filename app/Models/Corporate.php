<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function perorangan(): BelongsToMany
    {
        return $this->belongsToMany(Perorangan::class, 'perorangan_corporate')->withPivot('user_id');
    }

    public function project(): HasMany
    {
        return $this->hasMany(Project::class, 'corporate_id');
    }

    public function sewa(): HasMany
    {
        return $this->HasMany(Sewa::class, 'sewa_id');
    }
}
