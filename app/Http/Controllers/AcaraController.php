<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class AcaraController extends Controller
{
    public function preview($company, $projectId,)
    {
        // Validasi input
        $project = Project::where('id', $projectId)->firstOrFail();
        return view('exports.acara', compact('project'));
    }
}
