<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data users untuk seeding
        $users = [
            // Super Admin
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@gmail.com',
                'password' => 'superadmin123',
                'role' => 'Super Admin'
            ],
            
            // Editors
            [
                'name' => 'Editor Utama',
                'email' => 'editor@gmail.com',
                'password' => 'editor123',
                'role' => 'Editor'
            ],
            [
                'name' => 'Sarah Editor',
                'email' => 'sarah.editor@gmail.com',
                'password' => 'editor123',
                'role' => 'Editor'
            ],
            
            // Writers
            [
                'name' => 'Writer Berita',
                'email' => 'writer@gmail.com',
                'password' => 'writer123',
                'role' => 'Writer'
            ],
            [
                'name' => 'Ahmad Journalist',
                'email' => 'ahmad.writer@gmail.com',
                'password' => 'writer123',
                'role' => 'Writer'
            ],
            [
                'name' => 'Siti Reporter',
                'email' => 'siti.writer@gmail.com',
                'password' => 'writer123',
                'role' => 'Writer'
            ],
            [
                'name' => 'Budi News Writer',
                'email' => 'budi.writer@gmail.com',
                'password' => 'writer123',
                'role' => 'Writer'
            ],
            [
                'name' => 'Dewi Journalist',
                'email' => 'dewi.writer@gmail.com',
                'password' => 'writer123',
                'role' => 'Writer'
            ],
            [
                'name' => 'Rizki Reporter',
                'email' => 'rizki.writer@gmail.com',
                'password' => 'writer123',
                'role' => 'Writer'
            ]
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email'],
            ], [
                'name' => $userData['name'],
                'password' => Hash::make($userData['password']),
            ]);

            // Assign role
            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                $user->assignRole($role);
                $this->command->info("Created user: {$userData['name']} with role: {$userData['role']}");
            }
        }

        $this->command->info('Users seeder completed successfully!');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('==================');
        $this->command->info('Super Admin: superadmin@gmail.com / superadmin123');
        $this->command->info('Editor: editor@gmail.com / editor123');
        $this->command->info('Writer: writer@gmail.com / writer123');
        $this->command->info('');
        $this->command->info('Additional accounts created with same password pattern.');
    }
}
