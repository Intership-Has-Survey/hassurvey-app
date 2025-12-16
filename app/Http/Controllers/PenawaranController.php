<?php

namespace App\Http\Controllers;

use App\Models\Penawaran;
use Illuminate\Http\Request;

class PenawaranController extends Controller
{
    //
    public function preview($company, $invoiceId,)
    {
        // Validasi input
        $penawaran = Penawaran::where('id', $invoiceId)->with('detailPenawarans')->firstOrFail();
        return view('exports.tipe_penawaran', compact('penawaran'));
    }
}
