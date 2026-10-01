<?php

namespace Database\Seeders;

use App\Models\Claim;
use App\Models\ItemReport;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeder ini membuat data demo secukupnya supaya lo bisa langsung
     * coba semua fitur tanpa input manual satu-satu.
     */
    public function run(): void
    {
        // Akun admin default untuk login pertama kali.
        $admin = User::factory()->admin()->create([
            'name' => 'Admin SCANIC TRACE',
            'email' => 'admin@scanictrace.local',
            'password' => bcrypt('password'),
        ]);

        $staff = User::factory()->staff()->create([
            'name' => 'Staf Tata Usaha',
            'email' => 'staff@scanictrace.local',
            'password' => bcrypt('password'),
        ]);

        $student1 = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@scanictrace.local',
            'password' => bcrypt('password'),
        ]);

        $student2 = User::factory()->create([
            'name' => 'Sari Wulandari',
            'email' => 'sari@scanictrace.local',
            'password' => bcrypt('password'),
        ]);

        // Beberapa laporan tambahan acak.
        User::factory(5)->create();
        ItemReport::factory(10)->create();

        // Laporan spesifik + alur klaim contoh.
        $lostPhone = ItemReport::factory()->create([
            'user_id' => $student1->id,
            'type' => 'lost',
            'title' => 'Handphone Samsung warna hitam',
            'description' => 'Hilang di kantin saat jam istirahat kedua. Ada stiker logo game di casing belakang.',
            'category' => 'Elektronik',
            'location' => 'Kantin',
        ]);

        // Sari mengajukan klaim atas HP yang hilang milik Budi (contoh alur klaim).
        Claim::create([
            'item_report_id' => $lostPhone->id,
            'claimant_id' => $student2->id,
            'proof_details' => 'Saya menemukan HP tersebut di bawah meja kantin, ada stiker logo game seperti yang disebutkan.',
            'status' => Claim::STATUS_PENDING,
        ]);

        $this->command->info('Seeding selesai. Login demo:');
        $this->command->info('Admin  : admin@scanictrace.local / password');
        $this->command->info('Staf   : staff@scanictrace.local / password');
        $this->command->info('Siswa  : budi@scanictrace.local / password');
    }
}
