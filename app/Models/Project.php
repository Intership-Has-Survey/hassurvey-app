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
    protected $fillable = [
        'nama_project',
        'kategori_id',
        'sumber',
        'sales_id',
        'customer_id',
        'lokasi',
        'status',
        'nilai_project',
        'tanggal_informasi_masuk',
        'status_pekerjaan_lapangan',
        'status_pembayaran',
        'user_id',
    ];

    public function personels()
    {
        return $this->belongsToMany(Personel::class)->withPivot('peran');
    }

    /**
     * Relasi ke Kategori (BelongsTo).
     * Nama method 'kategori' harus sama dengan yang di ->relationship('kategori', ...).
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Relasi ke Sales (BelongsTo).
     * Nama method 'sales' harus sama dengan yang di ->relationship('sales', ...).
     */
    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate UUID automatically if not set
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function statusPekerjaan()
    {
        return $this->belongsTo(User::class);
    }

    public function personels()
    {
        return $this->hasMany(Personel::class);
    }
}
