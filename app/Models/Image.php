<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'path',
        'original_filename',
        'notes',
        'exif_data',
        'processed',
        'hash',
        'uploaded_by',
    ];
    
    protected $casts = [
        'exif_data' => 'array',
        'processed' => 'boolean',
    ];
    
    public function case()
    {
        return $this->belongsTo(Case::class);
    }
    
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    
    public function getFullPathAttribute()
    {
        return storage_path('app/' . $this->path);
    }
}
