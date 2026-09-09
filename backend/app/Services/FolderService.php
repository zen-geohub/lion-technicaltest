<?php

namespace App\Services;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class FolderService
{
    public function childrenOf(?int $parentId): Collection
    {
        return Folder::where('parent_id', $parentId)
            ->withCount(['children', 'files'])
            ->orderBy('name')
            ->get();
    }

    public function details(Folder $folder): Folder
    {
        return $folder->load([
            'children', 
        ]);
    }

    public function create(array $data, User $creator): Folder {
        return Folder::create([
            ...$data,
            'created_by' => $creator->id,
        ]);
    }

    public function update(Folder $folder, array $data): Folder
    {
        $folder->update($data);

        return $folder->fresh();
    }

    public function delete(Folder $folder): void
    {
        $folder->delete();
    }
}