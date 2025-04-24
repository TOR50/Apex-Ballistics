<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Analysis;
use App\Models\ActivityLog;

class AnalysisController extends Controller
{
    public function show($id)
    {
        $analysis = Analysis::with(['case', 'matches', 'results'])->findOrFail($id);
        
        return view('analysis.show', [
            'analysis' => $analysis,
            'matches' => $analysis->matches,
            'results' => $analysis->results,
            'case' => $analysis->case
        ]);
    }
    
    public function adjustThreshold($id, Request $request)
    {
        $analysis = Analysis::findOrFail($id);
        $threshold = $request->input('threshold', 75);
        
        // Apply threshold to get filtered results
        $filteredResults = $analysis->results()->where('match_percentage', '>=', $threshold)->get();
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'case_id' => $analysis->case_id,
            'analysis_id' => $analysis->id,
            'action' => 'adjust_threshold',
            'description' => 'Adjusted match threshold to ' . $threshold . '%',
        ]);
        
        return response()->json([
            'results' => $filteredResults,
            'threshold' => $threshold,
            'count' => $filteredResults->count()
        ]);
    }
    
    public function flagMatch($id, Request $request)
    {
        $analysis = Analysis::findOrFail($id);
        $matchId = $request->input('match_id');
        $flag = $request->input('flag', true);
        $reason = $request->input('reason', '');
        
        $match = $analysis->matches()->findOrFail($matchId);
        $match->update([
            'flagged' => $flag,
            'flag_reason' => $reason,
            'flagged_by' => auth()->id()
        ]);
        
        // Log this action in the audit trail
        ActivityLog::create([
            'user_id' => auth()->id(),
            'case_id' => $analysis->case_id,
            'analysis_id' => $analysis->id,
            'action' => $flag ? 'flag_match' : 'unflag_match',
            'description' => ($flag ? 'Flagged' : 'Unflagged') . ' match #' . $matchId . ($reason ? ': ' . $reason : ''),
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Match ' . ($flag ? 'flagged' : 'unflagged') . ' successfully'
        ]);
    }
}
