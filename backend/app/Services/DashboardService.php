<?php

namespace App\Services;

use App\Models\Department;
use App\Models\File;
use App\Models\Folder;

class DashboardService
{
    public function stats(): array
    {
        return [
            'total_folders' => Folder::count(),
            'total_files' => File::count(),
            'total_departments' => Department::count(),
            'latest_files' => File::with(['department', 'folder', 'uploader'])
                ->latest()
                ->limit(10)
                ->get(),
        ];
    }
}