<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subcategorizable extends Model
{
    use HasUuids;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subcategorizables';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key is not an incrementing integer.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'visi_mati_id',
        'subcategorizable_id',
        'subcategorizable_type',
    ];

    /**
     * Get the parent subcategorizable model (morph to).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subcategorizable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the VisiMati that owns the subcategorizable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function visiMati(): BelongsTo
    {
        return $this->belongsTo(VisiMati::class, 'visi_mati_id', 'id');
    }
}
