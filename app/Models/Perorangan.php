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

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_perorangan')
            ->withPivot('project_id', 'perorangan_id', 'peran')
            ->withTimestamps();
    }

    public function sewa()
    {
        return $this->belongsToMany(Sewa::class, 'sewa_perorangan')
            ->withPivot('sewa_id', 'perorangan_id', 'peran')
            ->withTimestamps();
    }

    public function alatcustomer()
    {
        return $this->belongsToMany(AlatCustomer::class, 'alat_customers_perorangan')
            ->withPivot('alat_customers_id', 'perorangan_id', 'peran')
            ->withTimestamps();
    }

    public function penjualans()
    {
        return $this->belongsToMany(Penjualan::class, 'penjualan_perorangan')
            ->withPivot('penjualan_id', 'perorangan_id', 'peran')
            ->withTimestamps();
    }

    public function kalibrasi()
    {
        return $this->belongsToMany(Kalibrasi::class, 'kalibrasi_perorangan')
            ->withPivot('kalibrasi_id', 'perorangan_id', 'peran')
            ->withTimestamps();
    }

    public function corporates()
    {
        return $this->belongsToMany(Corporate::class, 'perorangan_corporate')
            ->using(PeroranganCorporate::class)
            ->withPivot('user_id')
            ->withTimestamps();
    }
}
