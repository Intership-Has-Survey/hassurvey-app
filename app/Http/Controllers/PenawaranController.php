<?php

namespace App\Http\Controllers;

use App\Models\Penawaran;
use Illuminate\Http\Request;
use App\Models\PenawaranSetting;

class PenawaranController extends Controller
{
    //
    public function preview($company, $penawaranId,)
    {
        // Validasi input
        $penawaran = Penawaran::where('id', $penawaranId)->with('detailPenawarans')->firstOrFail();
        $penawaranSetting = PenawaranSetting::where('company_id', $company)->firstOrFail();
        return view('exports.tipe_penawaran', compact('penawaran', 'penawaranSetting'));
    }
}
