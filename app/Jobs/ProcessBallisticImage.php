<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Image;
use App\Models\ActivityLog;
use Exception;
use Log;

class ProcessBallisticImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $image;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info("Processing ballistic image: " . $this->image->id);
            
            // Extract EXIF data
            $exifData = $this->extractExifData($this->image->full_path);
            $this->image->exif_data = $exifData;
            
            // Generate image hash for integrity verification
            $hash = hash_file('sha256', $this->image->full_path);
            $this->image->hash = $hash;
            
            // Perform image preprocessing
            $this->preprocessImage($this->image->full_path);
            
            // Mark as processed
            $this->image->processed = true;
            $this->image->save();
            
            // Log activity
            ActivityLog::create([
                'user_id' => $this->image->uploaded_by,
                'case_id' => $this->image->case_id,
                'action' => 'process_image',
                'description' => 'Successfully processed image: ' . $this->image->original_filename,
            ]);
            
            Log::info("Successfully processed ballistic image: " . $this->image->id);
        } catch (Exception $e) {
            Log::error("Failed to process ballistic image: " . $e->getMessage());
            
            // Log error
            ActivityLog::create([
                'user_id' => $this->image->uploaded_by,
                'case_id' => $this->image->case_id,
                'action' => 'process_image_error',
                'description' => 'Error processing image: ' . $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Extract EXIF data from image
     */
    private function extractExifData($imagePath)
    {
        // Using exif_read_data if available
        if (function_exists('exif_read_data')) {
            $exifData = @exif_read_data($imagePath, 'ANY_TAG', true);
            return $exifData ?: [];
        }
        
        return [];
    }
    
    /**
     * Preprocess image for analysis
     */
    private function preprocessImage($imagePath)
    {
        // This would connect to a Python service for image preprocessing
        // For now, we'll simulate the process
        
        // Create a processed version of the image
        $processedPath = pathinfo($imagePath, PATHINFO_DIRNAME) . '/processed_' . pathinfo($imagePath, PATHINFO_BASENAME);
        
        // Add processing logic here
        // For simulation purposes, just create a copy
        copy($imagePath, $processedPath);
        
        return $processedPath;
    }
}
