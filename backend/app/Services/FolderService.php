<?php

namespace App\Services;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FolderService
{
    public function childrenOf(?int $parentId): Collection
    {
        return Folder::where('parent_id', $parentId)
            ->withCount(['children', 'files'])
            ->orderBy('name')
            ->get();
    }

    public function details(Folder $folder): array
    {
        $folder->load([
            'children', 
            'files.department',
            'files.uploader'
        ]);

        $trail = [];
        $current = $folder;

        while ($current) {
            array_unshift($trail, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
            ]);

            $current = $current->parent;
        }

        $path = '';

        $breadcrumb = array_map(function (array $item) use (&$path) {
            $path .= '/' . $item['slug'];

            return [
                ...$item,
                'path' => '/folders' . $path,
            ];
        }, $trail);

        return [
            'folder' => $folder,
            'breadcrumb' => $breadcrumb,
        ];
    }

    public function create(array $data, User $creator): Folder {
        $parentId = $data['parent_id'] ?? null;

        $exists = Folder::where('parent_id', $parentId)
            ->where('name', $data['name'])
            ->exists();
    
        if ($exists) {
            throw ValidationException::withMessages([
                'name' => 'A folder with this name already exists in this parent folder.',
            ]);
        }
    
        return Folder::create([
            ...$data,
            'slug' => Str::slug($data['name']),
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

    public function breadcrumb(Folder $folder): array
    {
        $trail = [];
        $current = $folder;

        while ($current) {
            array_unshift($trail, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
            ]);

            $current = $current->parent;
        }

        $path = '';

        return array_map(function (array $item) use (&$path) {
            $path .= '/' . $item['slug'];

            return [
                ...$item,
                'path' => '/folders' . $path,
            ];
        }, $trail);
    }

    public function findByPath(array $path): array
    {
        $parentId = null;

        foreach ($path as $slug) {
            $folder = Folder::where('parent_id', $parentId)
                ->where('slug', $slug)
                ->firstOrFail();

            $parentId = $folder->id;
        }

        return $this->details($folder);
    }
}