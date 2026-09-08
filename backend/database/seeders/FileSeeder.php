<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $folder = Folder::first();
        $department = Department::first();
        $admin = User::where('role', 'admin')->first();

        $path = 'files/sample.txt';
        Storage::disk('public')->put($path, 'Sample seeded file content.');

        File::create([
            'folder_id' => $folder->id,
            'title' => 'Sample Document',
            'department_id' => $department->id,
            'original_name' => 'sample.txt',
            'file_path' => $path,
            'mime_type' => 'text/plain',
            'size' => 27,
            'uploaded_by' => $admin->id,
        ]);
    }
}
