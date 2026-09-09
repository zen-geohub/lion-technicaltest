<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'folder_id', 'title', 'department_id',
        'original_name', 'file_path', 'mime_type', 'size', 'uploaded_by',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
