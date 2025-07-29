<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class WriterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role Writer sudah ada
        $writerRole = Role::firstOrCreate(['name' => 'Writer']);
        
        // Data penulis yang beragam
        $writers = [
            [
                'name' => 'Ahmad Fauzi Rahman',
                'email' => 'ahmad.fauzi@portalnews.com',
                'specialization' => 'Teknologi & Inovasi',
                'location' => 'Jakarta'
            ],
            [
                'name' => 'Sari Dewi Lestari',
                'email' => 'sari.dewi@portalnews.com',
                'specialization' => 'Ekonomi & Bisnis',
                'location' => 'Surabaya'
            ],
            [
                'name' => 'Muhammad Rizki Pratama',
                'email' => 'rizki.pratama@portalnews.com',
                'specialization' => 'Olahraga',
                'location' => 'Bandung'
            ],
            [
                'name' => 'Dr. Indira Sari Putri',
                'email' => 'indira.putri@portalnews.com',
                'specialization' => 'Kesehatan & Kedokteran',
                'location' => 'Medan'
            ],
            [
                'name' => 'Prof. Budi Santoso',
                'email' => 'budi.santoso@portalnews.com',
                'specialization' => 'Pendidikan & Akademik',
                'location' => 'Yogyakarta'
            ],
            [
                'name' => 'Andi Kurniawan',
                'email' => 'andi.kurniawan@portalnews.com',
                'specialization' => 'Politik & Pemerintahan',
                'location' => 'Makassar'
            ],
            [
                'name' => 'Maya Sinta Wulandari',
                'email' => 'maya.wulandari@portalnews.com',
                'specialization' => 'Teknologi Digital',
                'location' => 'Semarang'
            ],
            [
                'name' => 'Arief Budiman',
                'email' => 'arief.budiman@portalnews.com',
                'specialization' => 'Ekonomi Regional',
                'location' => 'Palembang'
            ],
            [
                'name' => 'Dr. Fitri Handayani',
                'email' => 'fitri.handayani@portalnews.com',
                'specialization' => 'Penelitian & Sains',
                'location' => 'Denpasar'
            ],
            [
                'name' => 'Hendra Wijaya',
                'email' => 'hendra.wijaya@portalnews.com',
                'specialization' => 'Olahraga Prestasi',
                'location' => 'Balikpapan'
            ],
            [
                'name' => 'Ratna Sari Dewi',
                'email' => 'ratna.dewi@portalnews.com',
                'specialization' => 'Kesehatan Masyarakat',
                'location' => 'Manado'
            ],
            [
                'name' => 'Agus Setiawan',
                'email' => 'agus.setiawan@portalnews.com',
                'specialization' => 'Startup & Fintech',
                'location' => 'Padang'
            ],
            [
                'name' => 'Nina Safitri',
                'email' => 'nina.safitri@portalnews.com',
                'specialization' => 'Pendidikan Digital',
                'location' => 'Malang'
            ],
            [
                'name' => 'Dedi Kurniadi',
                'email' => 'dedi.kurniadi@portalnews.com',
                'specialization' => 'Politik & Kebijakan Publik',
                'location' => 'Samarinda'
            ],
            [
                'name' => 'Lilis Suryani',
                'email' => 'lilis.suryani@portalnews.com',
                'specialization' => 'Investasi & Pasar Modal',
                'location' => 'Banjarmasin'
            ],
            [
                'name' => 'Fajar Nugroho',
                'email' => 'fajar.nugroho@portalnews.com',
                'specialization' => 'Teknologi AI & Robotik',
                'location' => 'Pontianak'
            ],
            [
                'name' => 'Dewi Kartika Sari',
                'email' => 'dewi.kartika@portalnews.com',
                'specialization' => 'Kesehatan Digital',
                'location' => 'Jambi'
            ],
            [
                'name' => 'Bambang Prasetyo',
                'email' => 'bambang.prasetyo@portalnews.com',
                'specialization' => 'Olahraga Tradisional',
                'location' => 'Pekanbaru'
            ],
            [
                'name' => 'Ani Purwanti',
                'email' => 'ani.purwanti@portalnews.com',
                'specialization' => 'UMKM & Kewirausahaan',
                'location' => 'Lampung'
            ],
            [
                'name' => 'Rudi Hermawan',
                'email' => 'rudi.hermawan@portalnews.com',
                'specialization' => 'Energi & Lingkungan',
                'location' => 'Mataram'
            ],
        ];

        foreach ($writers as $writerData) {
            // Cek apakah user sudah ada
            $existingUser = User::where('email', $writerData['email'])->first();
            
            if (!$existingUser) {
                $user = User::create([
                    'name' => $writerData['name'],
                    'email' => $writerData['email'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password123'), // Default password untuk semua writer
                ]);
                
                // Assign role Writer
                $user->assignRole($writerRole);
                
                $this->command->info("Writer berhasil dibuat: {$writerData['name']} - {$writerData['specialization']}");
            } else {
                // Pastikan user existing memiliki role Writer
                if (!$existingUser->hasRole('Writer')) {
                    $existingUser->assignRole($writerRole);
                }
                $this->command->info("Writer sudah ada: {$writerData['name']}");
            }
        }

        $this->command->info('Writer seeder berhasil dijalankan!');
        $this->command->info('Total writers: ' . User::role('Writer')->count());
    }
}
