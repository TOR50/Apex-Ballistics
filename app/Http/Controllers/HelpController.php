<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tutorial;
use App\Models\FeatureRequest;
use Illuminate\Support\Facades\Http;

class HelpController extends Controller
{
    public function index()
    {
        $popularTopics = [
            'Evidence Upload Best Practices',
            'Understanding Match Probabilities',
            'Chain of Custody Documentation',
            'Court-Ready Report Generation',
            'Firearm Identification Basics',
        ];
        
        return view('help.index', [
            'popularTopics' => $popularTopics,
        ]);
    }
    
    public function api()
    {
        // Generate Swagger/OpenAPI documentation
        return view('help.api');
    }
    
    public function tutorials()
    {
        $tutorials = Tutorial::orderBy('created_at', 'desc')->get();
        
        return view('help.tutorials', [
            'tutorials' => $tutorials
        ]);
    }
    
    public function requestFeature(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:1000',
            'priority' => 'required|in:low,medium,high',
        ]);
        
        // Save request locally
        $featureRequest = FeatureRequest::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'requested_by' => auth()->id(),
        ]);
        
        // Create GitHub issue via API
        if (config('services.github.token')) {
            $response = Http::withToken(config('services.github.token'))
                ->post('https://api.github.com/repos/apex-ballistics/brt/issues', [
                    'title' => '[Feature Request] ' . $request->title,
                    'body' => $request->description . "\n\nRequested by: " . auth()->user()->name,
                    'labels' => ['feature-request', $request->priority]
                ]);
                
            if ($response->successful()) {
                $featureRequest->github_issue_id = $response->json()['number'];
                $featureRequest->github_issue_url = $response->json()['html_url'];
                $featureRequest->save();
            }
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Feature request submitted successfully',
            'feature_request_id' => $featureRequest->id,
            'github_issue_url' => $featureRequest->github_issue_url ?? null,
        ]);
    }
}
