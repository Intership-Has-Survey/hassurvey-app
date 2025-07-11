<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

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
        return $this->belongsToMany(Personel::class, 'personel_project')
            ->withPivot('user_id', 'peran')
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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function statusPekerjaan()
    {
        return $this->belongsTo(StatusPekerjaan::class);
    }

    public function daftarAlat()
    {
        return $this->belongsToMany(DaftarAlat::class, 'daftar_alat_project', 'project_id', 'daftar_alat_id')
            ->withPivot(['status', 'user_id'])
            ->withTimestamps();
    }

    public function StatusPembayaran()
    {
        return $this->hasMany(StatusPembayaran::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
