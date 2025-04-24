<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Case;
use App\Models\Analysis;
use App\Models\SystemHealth;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch recent cases
        $recentCases = [];
        try {
            $recentCases = \App\Models\Cases::with(['firearm'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
        } catch (\Exception $e) {
            // Handle the error or log it
        }
        
        // Fetch pending analyses
        $pendingAnalyses = [];
        try {
            $pendingAnalyses = Analysis::with(['case'])
                            ->where('status', 'pending')
                            ->orderBy('priority', 'desc')
                            ->orderBy('due_date', 'asc')
                            ->take(5)
                            ->get();
        } catch (\Exception $e) {
            // Handle the error or log it
        }
        
        // System health metrics
        $systemHealth = [
            'storage' => $this->getStorageMetrics(),
            'activeUsers' => $this->getActiveUsers(),
            'processingQueue' => $this->getProcessingQueueMetrics(),
        ];
        
        return view('dashboard', [
            'recentCases' => $recentCases,
            'pendingAnalyses' => $pendingAnalyses,
            'systemHealth' => $systemHealth,
        ]);
    }
    
    private function getStorageMetrics()
    {
        // Placeholder implementation - in a real app, you would get actual storage metrics
        return [
            'used' => 25,
            'total' => 100,
            'percentage' => 25,
        ];
    }
    
    private function getActiveUsers()
    {
        // Count active users in the last 15 minutes
        return \App\Models\User::where('last_active_at', '>=', now()->subMinutes(15))->count();
    }
    
    private function getProcessingQueueMetrics()
    {
        // Placeholder implementation
        return [
            'pending' => Analysis::where('status', 'pending')->count(),
            'processing' => Analysis::where('status', 'processing')->count(),
        ];
    }
}
