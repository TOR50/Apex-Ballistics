<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'case_id',
        'status',
        'algorithm',
        'initiated_by',
        'completed_at',
        'duration',
        'priority',
        'due_date',
    ];
    
    protected $casts = [
        'completed_at' => 'datetime',
        'due_date' => 'datetime',
    ];
    
    public function case()
    {
        return $this->belongsTo(Case::class);
    }
    
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }
    
    public function results()
    {
        return $this->hasMany(AnalysisResult::class);
    }
    
    public function matches()
    {
        return $this->hasMany(Match::class);
    }
}
