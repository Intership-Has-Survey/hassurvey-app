<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPengajuan extends Model
{
    //\
    protected $table = 'kategori_pengajuan';
    protected $fillable = ['code', 'nama', 'parent_id'];

    public function parentKategori()
    {
        return $this->belongsTo(KategoriPengajuan::class, 'parent_id', 'code');
    }
    public function childrenKategori()
    {
        return $this->hasMany(KategoriPengajuan::class, 'parent_id', 'code');
    }

    public function halo()
    {
        //
        static::creating(function ($model) {
            // Ensure code is uppercase
            $model->code = strtoupper($model->code);

            if (empty($model->parent_id)) {
                // KATEGORI INDUK (format: 11, 12, 13, dst)
                $lastKategori = KategoriPengajuan::whereNull('parent_id')->orderBy('code', 'desc')->first();
                $number = $lastKategori ? intval($lastKategori->code) + 1 : 11;
                $model->code = strval($number);
            } else {
                // SUBKATEGORI (format: parent_id.nomor_urut)
                $lastSubKategori = KategoriPengajuan::where('parent_id', $model->parent_id)
                    ->orderBy('code', 'desc') // Urutkan berdasarkan code, bukan parent_id
                    ->first();

                if ($lastSubKategori) {
                    // Ambil bagian setelah titik terakhir
                    $lastCode = $lastSubKategori->code;
                    $lastParts = explode('.', $lastCode);
                    $lastNumber = end($lastParts); // Ambil bagian terakhir setelah titik
                    $number = intval($lastNumber) + 1;
                } else {
                    $number = 11; // Mulai dari 11 untuk subkategori pertama
                }

                $model->code = $model->parentKategori->code . '.' . $number;
            }
        });
    }

    protected static function booted()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->parent_id)) {
                // KATEGORI INDUK (format: 11, 12, 13, dst)
                $lastKategori = KategoriPengajuan::whereNull('parent_id')->orderBy('code', 'desc')->first();
                $number = $lastKategori ? intval($lastKategori->code) + 1 : 11;
                $model->code = strval($number);
            } else {
                // SUBKATEGORI (format: parent_code.nomor_urut)
                // dd($model);
                $parent = KategoriPengajuan::where('code', $model->parent_id)->first();

                if (!$parent) {
                    throw new \Exception('Parent kategori tidak ditemukan');
                }

                // Cari subkategori terakhir dengan parent yang sama
                $lastSubKategori = KategoriPengajuan::where('parent_id', $model->parent_id)
                    ->orderBy('code', 'desc')
                    ->first();

                if ($lastSubKategori && str_contains($lastSubKategori->code, '.')) {
                    // Ambil nomor urut dari code terakhir (format: 11.15 → ambil 15)
                    $parts = explode('.', $lastSubKategori->code);
                    $lastNumber = intval(end($parts));
                    $newNumber = $lastNumber + 1;
                } else {
                    // Subkategori pertama untuk parent ini
                    $newNumber = 11;
                }

                $model->code = $parent->code . '.' . $newNumber;
            }
        });
    }
}
