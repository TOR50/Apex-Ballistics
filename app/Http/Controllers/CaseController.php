<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Case;
use App\Models\Image;
use App\Models\ActivityLog;
use App\Jobs\ProcessBallisticImage;
use App\Jobs\GenerateReportJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CaseController extends Controller
{
    public function showUploadForm()
    {
        return view('cases.upload');
    }
    
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,tiff|max:10240', // 10MB max
            'case_number' => 'required|string|max:50',
            'firearm_type' => 'required|string|max:100',
            'crime_scene_notes' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Store image
            $path = $request->file('image')->store('evidence');
            
            // Create case if it doesn't exist
            $case = Case::firstOrCreate(
                ['case_number' => $request->case_number],
                ['firearm_type' => $request->firearm_type]
            );
            
            // Create image record
            $image = new Image([
                'path' => $path,
                'original_filename' => $request->file('image')->getClientOriginalName(),
                'notes' => $request->crime_scene_notes,
            ]);
            
            $case->images()->save($image);
            
            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'case_id' => $case->id,
                'action' => 'upload_image',
                'description' => 'Uploaded image: ' . $image->original_filename,
            ]);
            
            // Queue image processing job
            ProcessBallisticImage::dispatch($image);
            
            return redirect()->route('case.show', ['id' => $case->id])
                ->with('success', 'Image uploaded successfully and processing has begun.');
        
        } catch (\Exception $e) {
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $case = Case::with(['images', 'activityLogs.user'])->findOrFail($id);
        
        return view('cases.show', [
            'case' => $case,
            'images' => $case->images,
            'activityLogs' => $case->activityLogs()->orderBy('created_at', 'desc')->get()
        ]);
    }
    
    public function runAnalysis($id, Request $request)
    {
        $case = Case::findOrFail($id);
        
        // Start analysis job
        $analysis = $case->analyses()->create([
            'status' => 'processing',
            'initiated_by' => auth()->id(),
            'algorithm' => $request->input('algorithm', 'default'),
        ]);
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'case_id' => $case->id,
            'action' => 'start_analysis',
            'description' => 'Started analysis with ' . $analysis->algorithm . ' algorithm',
        ]);
        
        return redirect()->route('analysis.show', ['id' => $analysis->id]);
    }
    
    public function exportReport($id, Request $request)
    {
        $case = Case::findOrFail($id);
        $format = $request->input('format', 'pdf');
        
        // Queue report generation
        $job = new GenerateReportJob($case, $format, auth()->user());
        $this->dispatch($job);
        
        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'case_id' => $case->id,
            'action' => 'export_report',
            'description' => 'Requested ' . strtoupper($format) . ' report export',
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Report generation has been queued. You will be notified when it\'s ready.'
        ]);
    }
}
