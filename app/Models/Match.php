<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'analysis_id',
        'matched_case_id',
        'match_percentage',
        'confidence',
        'details',
        'flagged',
        'flag_reason',
        'flagged_by',
    ];
    
    protected $casts = [
        'match_percentage' => 'float',
        'confidence' => 'float',
        'details' => 'array',
        'flagged' => 'boolean',
    ];
    
    public function analysis()
    {
        return $this->belongsTo(Analysis::class);
    }
    
    public function matchedCase()
    {
        return $this->belongsTo(Case::class, 'matched_case_id');
    }
    
    public function flagger()
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }
}
