<?php

namespace Database\Seeders;

use App\Models\Claim;
use App\Models\Handover;
use App\Models\ItemReport;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $admin = User::firstOrCreate(
                ['email' => 'admin@scanictrace.local'],
                ['name' => 'Admin SCANIC TRACE', 'password' => 'password', 'role' => User::ROLE_ADMIN],
            );
            $staff = User::firstOrCreate(
                ['email' => 'staff@scanictrace.local'],
                ['name' => 'Staf Tata Usaha', 'password' => 'password', 'role' => User::ROLE_STAFF],
            );
            $budi = User::firstOrCreate(
                ['email' => 'budi@scanictrace.local'],
                ['name' => 'Budi Santoso', 'password' => 'password', 'role' => User::ROLE_TEACHER],
            );
            $sari = User::firstOrCreate(
                ['email' => 'sari@scanictrace.local'],
                ['name' => 'Sari Wulandari', 'password' => 'password', 'role' => User::ROLE_STUDENT],
            );

            $budi->update(['name' => 'Budi Santoso', 'role' => User::ROLE_TEACHER]);
            $sari->update(['name' => 'Sari Wulandari', 'role' => User::ROLE_STUDENT]);

            $legacyFactoryTitles = [
                'ipsa et magni',
                'nihil nobis molestias',
                'repudiandae molestiae nulla',
                'qui aut et',
                'fuga magnam cupiditate',
                'illum ducimus aut',
                'eos in voluptatum',
                'laborum in qui',
                'et enim rem',
                'doloribus inventore dolores',
                'Handphone Samsung warna hitam',
            ];

            ItemReport::withTrashed()
                ->whereIn('title', $legacyFactoryTitles)
                ->forceDelete();

            ItemReport::query()
                ->where('title', 'handphone')
                ->where('category', 'electronic')
                ->where('location', 'masjid')
                ->update([
                    'title' => 'HP ditemukan dekat gerobak pangsit',
                    'type' => ItemReport::TYPE_FOUND,
                    'description' => 'Saya menemukan HP ini di dekat gerobak pangsit di area sekolah. Pemilik bisa menjelaskan merek dan ciri khususnya kepada petugas.',
                    'category' => 'Elektronik',
                    'location' => 'Kantin',
                    'photo_path' => null,
                ]);

            ItemReport::query()->whereNotNull('photo_path')->get()->each(function (ItemReport $report): void {
                if (! $report->photo_url) {
                    $report->update(['photo_path' => null]);
                }
            });

            $lostReport = ItemReport::updateOrCreate(
                ['title' => 'HP ROG Phone 8 Black'],
                [
                    'user_id' => $sari->id,
                    'type' => ItemReport::TYPE_LOST,
                    'description' => 'HP ROG Phone 8 Black hilang di area Kantin setelah jam istirahat kedua. HP memakai casing transparan dengan retak tipis di sudut kanan bawah.',
                    'category' => 'Elektronik',
                    'location' => 'Kantin',
                    'incident_date' => now()->subDay()->toDateString(),
                    'photo_path' => null,
                    'status' => ItemReport::STATUS_OPEN,
                ],
            );

            $walletReport = ItemReport::updateOrCreate(
                ['title' => 'Dompet Kulit Cokelat'],
                [
                    'user_id' => $staff->id,
                    'type' => ItemReport::TYPE_FOUND,
                    'description' => 'Dompet kulit cokelat ditemukan di dekat meja Ruang Guru. Pemilik dapat menjelaskan isi dan ciri khususnya kepada admin.',
                    'category' => 'Dompet',
                    'location' => 'Ruang Guru',
                    'incident_date' => now()->subDay()->toDateString(),
                    'photo_path' => null,
                    'status' => ItemReport::STATUS_OPEN,
                ],
            );

            Claim::updateOrCreate(
                ['item_report_id' => $walletReport->id, 'claimant_id' => $budi->id],
                [
                    'claimant_full_name' => 'Budi Santoso',
                    'claimant_class_position' => 'Guru',
                    'proof_details' => 'Di dalam dompet ada Kartu Identitas Guru dan STNK. Bagian dalam dompet juga memiliki kantong kecil beritsleting di sebelah kiri.',
                    'status' => Claim::STATUS_PENDING,
                    'reviewed_by' => null,
                    'review_note' => null,
                    'reviewed_at' => null,
                ],
            );

            $keyReport = ItemReport::updateOrCreate(
                ['title' => 'Kunci Motor Honda Vario'],
                [
                    'user_id' => $staff->id,
                    'type' => ItemReport::TYPE_FOUND,
                    'description' => 'Kunci motor Honda Vario ditemukan di halaman sekolah. Ada gantungan kunci karakter anime berwarna merah.',
                    'category' => 'Elektronik',
                    'location' => 'Halaman Sekolah',
                    'incident_date' => now()->subDays(2)->toDateString(),
                    'photo_path' => null,
                    'status' => ItemReport::STATUS_RETURNED,
                ],
            );

            $keyClaim = Claim::updateOrCreate(
                ['item_report_id' => $keyReport->id, 'claimant_id' => $sari->id],
                [
                    'claimant_full_name' => 'Sari Wulandari',
                    'claimant_class_position' => 'X PPLG 1',
                    'proof_details' => 'Kunci tersebut milik saya. Gantungannya karakter anime berwarna merah, dan kunci motornya memiliki label kecil Honda.',
                    'status' => Claim::STATUS_APPROVED,
                    'reviewed_by' => $admin->id,
                    'review_note' => 'Ciri kunci dan gantungan sesuai dengan keterangan pemilik.',
                    'reviewed_at' => now()->subDay(),
                ],
            );

            Handover::updateOrCreate(
                ['claim_id' => $keyClaim->id],
                [
                    'handed_over_by' => $staff->id,
                    'received_by' => $sari->id,
                    'notes' => 'Kunci sudah diterima oleh pemilik di ruang Tata Usaha.',
                    'handed_over_at' => now()->subDay(),
                ],
            );

            $this->command?->info('Data demo SCANIC TRACE berhasil disiapkan.');
            $this->command?->info('Akun demo: admin@scanictrace.local, staff@scanictrace.local, budi@scanictrace.local, sari@scanictrace.local (password awal: password).');
        });
    }
}
