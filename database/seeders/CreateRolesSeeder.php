<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CreateRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $editorRole = Role::firstOrCreate(['name' => 'Editor']);
        $writerRole = Role::firstOrCreate(['name' => 'Writer']);

        // News Permissions
        Permission::firstOrCreate(['name' => 'create-news']);
        Permission::firstOrCreate(['name' => 'view-own-news']);
        Permission::firstOrCreate(['name' => 'view-all-news']);
        Permission::firstOrCreate(['name' => 'edit-own-news']);
        Permission::firstOrCreate(['name' => 'edit-all-news']);
        Permission::firstOrCreate(['name' => 'delete-own-news']);
        Permission::firstOrCreate(['name' => 'delete-all-news']);
        Permission::firstOrCreate(['name' => 'publish-news']);
        Permission::firstOrCreate(['name' => 'reject-news']);
        Permission::firstOrCreate(['name' => 'manage-categories']);
        Permission::firstOrCreate(['name' => 'manage-users']);

        // Writer Permissions: CRUD pada berita sendiri
        $writerRole->givePermissionTo([
            'create-news',
            'view-own-news', 
            'edit-own-news',
            'delete-own-news'
        ]);

        // Editor Permissions: Edit dan publish/reject semua berita
        $editorRole->givePermissionTo([
            'view-all-news',
            'edit-all-news',
            'publish-news',
            'reject-news'
        ]);

        // Super Admin: Semua permissions
        $permissions = Permission::pluck('id')->all();
        $superAdminRole->syncPermissions($permissions);

        $superAdmin = User::firstOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'Admin',
            'password' => bcrypt('admin123')
        ]);
        $superAdmin->assignRole($superAdminRole);

        // Create Editor User
        $editor = User::firstOrCreate([
            'email' => 'editor@gmail.com',
        ], [
            'name' => 'Editor',
            'password' => bcrypt('editor123')
        ]);
        $editor->assignRole($editorRole);

        // Create Writer User
        $writer = User::firstOrCreate([
            'email' => 'writer@gmail.com',
        ], [
            'name' => 'Writer',
            'password' => bcrypt('writer123')
        ]);
        $writer->assignRole($writerRole);

        // Create additional Writer Users
        $writer2 = User::firstOrCreate([
            'email' => 'writer2@gmail.com',
        ], [
            'name' => 'John Writer',
            'password' => bcrypt('writer123')
        ]);
        $writer2->assignRole($writerRole);

        $writer3 = User::firstOrCreate([
            'email' => 'writer3@gmail.com',
        ], [
            'name' => 'Jane Writer',
            'password' => bcrypt('writer123')
        ]);
        $writer3->assignRole($writerRole);
    }
}
