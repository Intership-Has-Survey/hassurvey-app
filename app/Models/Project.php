<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

    public function personels()
    {
        return $this->belongsToMany(Personel::class, 'personel_project') // <- ini penting
            ->withPivot('peran', 'user_id')
            ->withTimestamps();
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class);
    }

    protected static function boot()
    {
        parent::boot();
    }

    public function statusPekerjaan()
    {
        return $this->belongsTo(User::class);
    }

    public function StatusPembayaran()
    {
        return $this->hasMany(StatusPembayaran::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
