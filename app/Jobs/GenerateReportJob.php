<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Report;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ReportVersion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReportGenerated;
use Exception;
use Log;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $report;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Update status to processing
            $this->report->status = 'processing';
            $this->report->save();
            
            Log::info("Generating report: " . $this->report->id);
            
            // Generate the report based on template and format
            $reportContent = $this->generateReportContent();
            
            // Create PDF
            $pdf = PDF::loadView('reports.templates.pdf', [
                'report' => $this->report,
                'content' => $reportContent
            ]);
            
            // Save PDF to storage
            $fileName = 'report_' . $this->report->id . '_v' . ($this->report->version + 1) . '.pdf';
            $path = 'reports/' . $this->report->case_id . '/' . $fileName;
            Storage::put($path, $pdf->output());
            
            // Create a new version record
            $version = new ReportVersion([
                'report_id' => $this->report->id,
                'version' => $this->report->version + 1,
                'file_path' => $path,
                'created_by' => $this->report->created_by,
                'changes' => 'Initial version',
            ]);
            $version->save();
            
            // Update report with new file path and version
            $this->report->file_path = $path;
            $this->report->version = $version->version;
            $this->report->status = 'completed';
            $this->report->save();
            
            // Log activity
            ActivityLog::create([
                'user_id' => $this->report->created_by,
                'case_id' => $this->report->case_id,
                'action' => 'generate_report',
                'description' => 'Generated report: ' . $this->report->title . ' (v' . $this->report->version . ')',
            ]);
            
            // Send notification email
            $user = User::find($this->report->created_by);
            Mail::to($user->email)->send(new ReportGenerated($this->report, $user));
            
            Log::info("Successfully generated report: " . $this->report->id);
        } catch (Exception $e) {
            Log::error("Failed to generate report: " . $e->getMessage());
            
            // Update status to failed
            $this->report->status = 'failed';
            $this->report->save();
            
            // Log error
            ActivityLog::create([
                'user_id' => $this->report->created_by,
                'case_id' => $this->report->case_id,
                'action' => 'generate_report_error',
                'description' => 'Error generating report: ' . $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Generate the report content based on template and sections
     */
    private function generateReportContent()
    {
        $case = $this->report->case;
        $template = $this->report->template;
        $sections = $this->report->sections;
        
        // Build report content based on selected sections
        $content = [];
        
        foreach ($sections as $section) {
            switch ($section) {
                case 'case_summary':
                    $content['case_summary'] = $this->generateCaseSummary($case);
                    break;
                case 'ballistics_analysis':
                    $content['ballistics_analysis'] = $this->generateBallisticsAnalysis($case);
                    break;
                case 'match_results':
                    $content['match_results'] = $this->generateMatchResults($case);
                    break;
                case 'images':
                    $content['images'] = $this->generateImageGallery($case);
                    break;
                case 'conclusions':
                    $content['conclusions'] = $this->generateConclusions($case);
                    break;
            }
        }
        
        return $content;
    }
    
    private function generateCaseSummary($case)
    {
        return [
            'title' => 'Case Summary',
            'case_number' => $case->case_number,
            'firearm_type' => $case->firearm_type,
            'opened_date' => $case->created_at->format('Y-m-d'),
            'status' => $case->status,
        ];
    }
    
    private function generateBallisticsAnalysis($case)
    {
        // Get the latest analysis
        $analysis = $case->analyses()->latest()->first();
        
        if (!$analysis) {
            return [
                'title' => 'Ballistics Analysis',
                'message' => 'No analysis has been performed yet.'
            ];
        }
        
        return [
            'title' => 'Ballistics Analysis',
            'algorithm' => $analysis->algorithm,
            'performed_at' => $analysis->created_at->format('Y-m-d H:i:s'),
            'duration' => $analysis->duration,
            'performed_by' => $analysis->initiator->name,
        ];
    }
    
    // Additional helper methods for report generation
    // ...
}
