<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'case_id',
        'template_id',
        'title',
        'sections',
        'status',
        'created_by',
        'file_path',
        'version',
    ];
    
    protected $casts = [
        'sections' => 'array',
    ];
    
    public function case()
    {
        return $this->belongsTo(Case::class);
    }
    
    public function template()
    {
        return $this->belongsTo(ReportTemplate::class);
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function shares()
    {
        return $this->hasMany(ReportShare::class);
    }
    
    public function versions()
    {
        return $this->hasMany(ReportVersion::class);
    }
}
