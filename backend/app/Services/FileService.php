<?php

namespace App\Services;

use App\Models\File;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function list(array $filters): LengthAwarePaginator
    {
        $query = File::with(['department', 'folder', 'uploader']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('original_name', 'ilike', "%{$search}%");
            });
        }

        if (! empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (! empty($filters['folder_id'])) {
            $query->where('folder_id', $filters['folder_id']);
        }

        return $query->latest()->paginate(10);
    }

    public function upload(array $data, UploadedFile $uploaded, User $uploader): File
    {
        $path = $uploaded->store('files', 'public');

        $file = File::create([
            'folder_id' => $data['folder_id'],
            'title' => $data['title'],
            'department_id' => $data['department_id'],
            'original_name' => $uploaded->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $uploaded->getClientMimeType(),
            'size' => $uploaded->getSize(),
            'uploaded_by' => $uploader->id,
        ]);

        return $file->load(['department', 'folder', 'uploader']);
    }

    public function withDetails(File $file): File
    {
        return $file->load(['department', 'folder', 'uploader']);
    }

    public function update(File $file, array $data, ?UploadedFile $replacement): File
    {
        if ($replacement) {
            Storage::disk('public')->delete($file->file_path);
            $data['file_path'] = $replacement->store('files', 'public');
            $data['original_name'] = $replacement->getClientOriginalName();
            $data['mime_type'] = $replacement->getClientMimeType();
            $data['size'] = $replacement->getSize();
        }

        $file->update($data);

        return $file->fresh(['department', 'folder', 'uploader']);
    }

    public function delete(File $file): void
    {
        $file->delete();
    }

    public function download(File $file)
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        return $storage->download($file->file_path, $file->original_name);
    }
}