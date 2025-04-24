<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalysisResult extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'analysis_id',
        'result_type',
        'data',
        'match_percentage',
        'confidence',
        'image_path',
    ];
    
    protected $casts = [
        'data' => 'array',
        'match_percentage' => 'float',
        'confidence' => 'float',
    ];
    
    public function analysis()
    {
        return $this->belongsTo(Analysis::class);
    }
}
