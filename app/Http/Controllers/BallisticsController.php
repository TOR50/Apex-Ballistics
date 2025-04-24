<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BallisticsController extends Controller
{
    public function index()
    {
        return view('ballistics.home'); // Using the new ballistics-specific home view
    }

    public function analyze(Request $request)
    {
        // Placeholder logic for ballistics analysis
        $data = $request->all();
        return response()->json(['message' => 'Analysis complete', 'data' => $data]);
    }

    /**
     * Show the ballistics calculator view.
     */
    public function calculator()
    {
        return view('ballistics.calculator');
    }
    
    /**
     * Show the load data view.
     */
    public function loadData()
    {
        return view('ballistics.load-data');
    }
}
