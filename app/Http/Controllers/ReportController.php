<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\ReportTemplate;
use App\Models\Case;
use App\Jobs\GenerateReportJob;
use App\Models\ActivityLog;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['case', 'createdBy'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        
        $templates = ReportTemplate::all();
        
        return view('reports.index', [
            'reports' => $reports,
            'templates' => $templates
        ]);
    }
    
    public function create()
    {
        $templates = ReportTemplate::all();
        $cases = Case::orderBy('created_at', 'desc')->get();
        
        return view('reports.create', [
            'templates' => $templates,
            'cases' => $cases
        ]);
    }
    
    public function generate(Request $request)
    {
        $request->validate([
            'case_id' => 'required|exists:cases,id',
            'template_id' => 'required|exists:report_templates,id',
            'sections' => 'required|array',
            'title' => 'required|string|max:255',
        ]);
        
        $case = Case::findOrFail($request->case_id);
        $template = ReportTemplate::findOrFail($request->template_id);
        
        // Create report record
        $report = Report::create([
            'case_id' => $case->id,
            'template_id' => $template->id,
            'title' => $request->title,
            'sections' => json_encode($request->sections),
            'status' => 'queued',
            'created_by' => auth()->id(),
        ]);
        
        // Queue report generation job
        GenerateReportJob::dispatch($report);
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'case_id' => $case->id,
            'action' => 'queue_report',
            'description' => 'Queued report generation: ' . $request->title,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Report queued for generation. You will be notified when it\'s ready.',
            'report_id' => $report->id
        ]);
    }
    
    public function share($id, Request $request)
    {
        $report = Report::findOrFail($id);
        
        // Check permissions
        $this->authorize('share', $report);
        
        $request->validate([
            'email' => 'required|email',
            'permission' => 'required|in:view,edit',
        ]);
        
        // Logic to share the report
        $report->shares()->create([
            'email' => $request->email,
            'permission' => $request->permission,
            'shared_by' => auth()->id(),
        ]);
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'case_id' => $report->case_id,
            'action' => 'share_report',
            'description' => 'Shared report with ' . $request->email . ' (' . $request->permission . ' permission)',
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Report shared successfully'
        ]);
    }
}
