<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Case extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'case_number',
        'firearm_type',
        'status',
        'priority',
        'notes',
        'assigned_to',
    ];
    
    public function images()
    {
        return $this->hasMany(Image::class);
    }
    
    public function analyses()
    {
        return $this->hasMany(Analysis::class);
    }
    
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
    
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
