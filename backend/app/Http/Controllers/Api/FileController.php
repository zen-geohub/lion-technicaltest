<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\File\StoreFileRequest;
use App\Http\Requests\File\UpdateFileRequest;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function __construct(protected FileService $files) {}

    public function index(Request $request)
    {
        return $this->files->list($request->only(['search', 'department_id', 'folder_id']));
    }

    public function store(StoreFileRequest $request)
    {
        $file = $this->files->upload(
            $request->only(['title', 'department_id', 'folder_id']),
            $request->file('file'),
            $request->user(),
        );

        return response()->json($file, 201);
    }

    public function show(File $file)
    {
        return $this->files->withDetails($file);
    }

    public function update(UpdateFileRequest $request, File $file)
    {
        $this->authorize('update', $file);

        $updated = $this->files->update(
            $file,
            $request->only(['title', 'department_id', 'folder_id']),
            $request->hasFile('file') ? $request->file('file') : null,
        );

        return response()->json($updated);
    }

    public function destroy(File $file)
    {
        $this->authorize('delete', $file);
        $this->files->delete($file);

        return response()->json(null, 204);
    }

    public function download(File $file)
    {
        return $this->files->download($file);
    }
}