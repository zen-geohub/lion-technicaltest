<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Folder\StoreFolderRequest;
use App\Http\Requests\Folder\UpdateFolderRequest;
use App\Models\Folder;
use App\Services\FolderService;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function __construct(protected FolderService $folders) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->folders->childrenOf($request->query('parent_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFolderRequest $request)
    {
        $folder = $this->folders->create($request->validated(), $request->user());

        return response()->json($folder, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Folder $folder)
    {
        return $this->folders->details($folder);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFolderRequest $request, Folder $folder)
    {
        $this->authorize('update', $folder);
        $updated = $this->folders->update($folder, $request->validated());

        return response()->json($updated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Folder $folder)
    {
        $this->authorize('delete', $folder);
        $this->folders->delete($folder);

        return response()->json(null, 204);
    }
}
