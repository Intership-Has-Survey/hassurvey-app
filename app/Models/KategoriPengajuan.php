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

    private static function generateCode($model)
    {
        if (empty($model->parent_id)) {
            // KATEGORI INDUK
            $lastKategori = self::whereNull('parent_id')
                ->where('id', '!=', $model->id)
                ->orderByRaw('CAST(code AS UNSIGNED) DESC')
                ->first();

            $number = $lastKategori ? intval($lastKategori->code) + 1 : 11;
            return (string) $number;
        }

        // SUB KATEGORI
        $lastSubKategori = self::where('parent_id', $model->parent_id)
            ->where('id', '!=', $model->id)
            ->orderBy('code', 'desc')
            ->first();

        if ($lastSubKategori) {
            $lastParts = explode('.', $lastSubKategori->code);
            $lastNumber = (int) end($lastParts) + 1;
        } else {
            $lastNumber = 11;
        }

        return $model->parentKategori->code . '.' . $lastNumber;
    }


    // public function halo()
    // {
    //     static::saved(function ($model) {
    //         // Ensure code is uppercase
    //         $model->code = strtoupper($model->code);

    //         if (empty($model->parent_id)) {
    //             // KATEGORI INDUK (format: 11, 12, 13, dst)
    //             $lastKategori = KategoriPengajuan::whereNull('parent_id')->orderBy('code', 'desc')->first();
    //             $number = $lastKategori ? intval($lastKategori->code) + 1 : 11;
    //             $model->code = strval($number);
    //         } else {
    //             // SUBKATEGORI (format: parent_id.nomor_urut)
    //             $lastSubKategori = KategoriPengajuan::where('parent_id', $model->parent_id)
    //                 ->orderBy('code', 'desc') // Urutkan berdasarkan code, bukan parent_id
    //                 ->first();

    //             if ($lastSubKategori) {
    //                 // Ambil bagian setelah titik terakhir
    //                 $lastCode = $lastSubKategori->code;
    //                 $lastParts = explode('.', $lastCode);
    //                 $lastNumber = end($lastParts); // Ambil bagian terakhir setelah titik
    //                 $number = intval($lastNumber) + 1;
    //             } else {
    //                 $number = 11; // Mulai dari 11 untuk subkategori pertama
    //             }

    //             $model->code = $model->parentKategori->code . '.' . $number;
    //         }
    //     });
    // }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->code = self::generateCode($model);
        });

        static::updating(function ($model) {
            if ($model->isDirty('parent_id')) {
                $model->code = self::generateCode($model);
            }
        });
    }
}
