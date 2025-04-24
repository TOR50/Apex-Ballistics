<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class ActivityLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'case_id',
        'analysis_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'integrity_hash',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($log) {
            // Automatically record IP address and user agent
            $log->ip_address = request()->ip();
            $log->user_agent = request()->userAgent();
            
            // Generate integrity hash for audit trail security
            $log->integrity_hash = Hash::make(
                $log->user_id . 
                $log->action . 
                $log->description . 
                $log->ip_address . 
                $log->created_at
            );
        });
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function case()
    {
        return $this->belongsTo(Case::class);
    }
    
    public function analysis()
    {
        return $this->belongsTo(Analysis::class);
    }
}
