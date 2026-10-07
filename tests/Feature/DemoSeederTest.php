<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\Handover;
use App\Models\ItemReport;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_creates_realistic_idempotent_presentation_scenarios(): void
    {
        $legacyOwner = User::factory()->create();
        $legacyReport = ItemReport::factory()->create([
            'user_id' => $legacyOwner->id,
            'title' => 'ipsa et magni',
        ]);
        $legacyClaim = $legacyReport->claims()->create([
            'claimant_id' => User::factory()->create()->id,
            'proof_details' => 'Klaim demo lama yang harus ikut dibersihkan bersama laporan dummy.',
            'status' => Claim::STATUS_PENDING,
        ]);

        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $lostPhone = ItemReport::where('title', 'HP ROG Phone 8 Black')->firstOrFail();
        $wallet = ItemReport::where('title', 'Dompet Kulit Cokelat')->firstOrFail();
        $returnedKey = ItemReport::where('title', 'Kunci Motor Honda Vario')->firstOrFail();

        $this->assertSame(ItemReport::STATUS_OPEN, $lostPhone->status);
        $this->assertSame(ItemReport::TYPE_LOST, $lostPhone->type);
        $this->assertSame('Kantin', $lostPhone->location);

        $walletClaim = $wallet->claims()->with('claimant')->firstOrFail();
        $this->assertSame(Claim::STATUS_PENDING, $walletClaim->status);
        $this->assertSame('Budi Santoso', $walletClaim->claimant_full_name);
        $this->assertSame('Guru', $walletClaim->claimant_class_position);
        $this->assertSame(User::ROLE_TEACHER, $walletClaim->claimant->role);

        $keyClaim = $returnedKey->claims()->with('handover')->firstOrFail();
        $this->assertSame(ItemReport::STATUS_RETURNED, $returnedKey->status);
        $this->assertSame(Claim::STATUS_APPROVED, $keyClaim->status);
        $this->assertSame('X PPLG 1', $keyClaim->claimant_class_position);
        $this->assertInstanceOf(Handover::class, $keyClaim->handover);

        $this->assertSame(1, ItemReport::where('title', 'HP ROG Phone 8 Black')->count());
        $this->assertSame(1, Claim::where('item_report_id', $wallet->id)->count());
        $this->assertSame(1, Handover::where('claim_id', $keyClaim->id)->count());
        $this->assertDatabaseMissing('item_reports', ['title' => 'ipsa et magni']);
        $this->assertDatabaseMissing('claims', ['id' => $legacyClaim->id]);
        $this->assertDatabaseMissing('item_reports', ['title' => 'Handphone Samsung warna hitam']);
    }
}