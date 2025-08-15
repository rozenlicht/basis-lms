<?php

namespace App\Http\Controllers\Exports;

use App\Http\Controllers\Controller;
use App\Models\SourceMaterial;
use Illuminate\Http\Request;

class CompositionReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $sourceMaterials = $request->query('source_materials');
        $sourceMaterials = SourceMaterial::whereIn('id', $sourceMaterials)->get();
        return view('pdf.composition-report', compact('sourceMaterials'));
    }
}
