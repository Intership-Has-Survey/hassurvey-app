<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use App\Models\AcaraSetting;
use Carbon\Carbon;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AcaraController extends Controller
{
    public function preview($company, $acaraId)
    {
        $acara = Acara::with('project')
            ->where('id', $acaraId)
            ->firstOrFail();

        return view('exports.acara', compact('acara'));
    }

    public function test($company)
    {

        $acaraSetting = AcaraSetting::where('company_id', $company)->firstOrFail();
        return view('exports.acara_preview', compact('acaraSetting'));
    }
}
