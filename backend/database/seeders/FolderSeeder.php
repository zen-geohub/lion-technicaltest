<?php

namespace Database\Seeders;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $root = Folder::create([
            'name' => 'Company Documents', 
            'slug' => Str::slug('Company Documents'),
            'parent_id' => null, 
            'created_by' => $admin->id
        ]);

        $sub = Folder::create([
            'name' => 'Contracts', 
            'slug' => Str::slug('Contracts'),
            'parent_id' => $root->id, 
            'created_by' => $admin->id
        ]);

        Folder::create([
            'name' => '2026', 
            'slug' => Str::slug('2026'),
            'parent_id' => $sub->id, 
            'created_by' => $admin->id
        ]);
    }
}
