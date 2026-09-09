<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Pagination\LengthAwarePaginator;

class DepartmentService
{
    public function list(): LengthAwarePaginator
    {
        return Department::orderBy('name')->paginate(10);
    }

    public function create(array $data): Department
    {
        return Department::create($data);
    }

    public function update(Department $department, array $data): Department
    {
        $department->update($data);

        return $department->fresh();
    }

    public function delete(Department $department): void
    {
        $department->delete();
    }
}