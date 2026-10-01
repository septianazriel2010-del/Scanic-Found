<?php

namespace Tests\Feature;

use App\Models\Claim;
use App\Models\ItemReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test paling penting untuk keamanan: user lain tidak boleh melihat
     * detail klaim (termasuk proof_details yang privat) milik user lain.
     */
    public function test_user_cannot_view_other_users_claim(): void
    {
        $reporter = User::factory()->create();
        $claimant = User::factory()->create();
        $strangeUser = User::factory()->create();

        $report = ItemReport::factory()->create(['user_id' => $reporter->id]);

        $claim = Claim::create([
            'item_report_id' => $report->id,
            'claimant_id' => $claimant->id,
            'proof_details' => 'Bukti rahasia milik claimant.',
            'status' => Claim::STATUS_PENDING,
        ]);

        $this->actingAs($strangeUser)
            ->get(route('claims.show', $claim))
            ->assertForbidden();

        $this->actingAs($claimant)
            ->get(route('claims.show', $claim))
            ->assertOk();
    }

    public function test_only_admin_can_review_claims(): void
    {
        $claimant = User::factory()->create();
        $report = ItemReport::factory()->create();

        $claim = Claim::create([
            'item_report_id' => $report->id,
            'claimant_id' => $claimant->id,
            'proof_details' => 'Bukti kepemilikan.',
            'status' => Claim::STATUS_PENDING,
        ]);

        $this->actingAs($claimant)
            ->patch(route('admin.claims.update-status', $claim), ['status' => 'approved'])
            ->assertForbidden();
    }
}
