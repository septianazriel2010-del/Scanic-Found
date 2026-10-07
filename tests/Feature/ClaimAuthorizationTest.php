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

    public function test_claim_identity_fields_are_required_and_current_role_is_visible_to_admin(): void
    {
        $reporter = User::factory()->create();
        $claimant = User::factory()->create(['name' => 'Budi Santoso']);
        $admin = User::factory()->admin()->create();
        $report = ItemReport::factory()->create([
            'user_id' => $reporter->id,
            'type' => ItemReport::TYPE_FOUND,
        ]);

        $this->actingAs($claimant)
            ->from(route('claims.create', $report))
            ->post(route('claims.store', $report), [
                'claimant_full_name' => '',
                'claimant_class_position' => '',
                'proof_details' => '',
            ])
            ->assertSessionHasErrors(['claimant_full_name', 'claimant_class_position', 'proof_details']);

        $this->post(route('claims.store', $report), [
            'claimant_full_name' => 'Budi Santoso',
            'claimant_class_position' => 'XI PPLG 2',
            'proof_details' => 'Ada goresan kecil di sisi kiri dan gantungan kunci biru.',
        ])->assertRedirect(route('claims.index'));

        $claim = Claim::query()->firstOrFail();
        $this->assertSame('Budi Santoso', $claim->claimant_full_name);
        $this->assertSame('XI PPLG 2', $claim->claimant_class_position);
        $this->assertSame(ItemReport::STATUS_OPEN, $report->fresh()->status);

        $this->post(route('logout'));
        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('items.index'));

        $this->get(route('admin.dashboard'))->assertOk();
        $this->patch(route('admin.users.update-role', $claimant), [
            'role' => User::ROLE_TEACHER,
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $claimant->id,
            'role' => User::ROLE_TEACHER,
        ]);

        $this->get(route('admin.claims.index'))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('Teacher');

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Teacher')
            ->assertSee('XI PPLG 2');
    }

    public function test_admin_approval_updates_claim_and_handover_updates_report(): void
    {
        $admin = User::factory()->admin()->create();
        $reporter = User::factory()->create();
        $claimant = User::factory()->teacher()->create();
        $otherClaimant = User::factory()->create();
        $report = ItemReport::factory()->create([
            'user_id' => $reporter->id,
            'type' => ItemReport::TYPE_FOUND,
            'status' => ItemReport::STATUS_OPEN,
        ]);
        $claim = Claim::create([
            'item_report_id' => $report->id,
            'claimant_id' => $claimant->id,
            'claimant_full_name' => $claimant->name,
            'claimant_class_position' => 'Guru Matematika',
            'proof_details' => 'Ada goresan kecil dan stiker biru pada sisi barang.',
            'status' => Claim::STATUS_PENDING,
        ]);
        $otherClaim = Claim::create([
            'item_report_id' => $report->id,
            'claimant_id' => $otherClaimant->id,
            'proof_details' => 'Deskripsi berbeda yang cukup panjang untuk pengajuan klaim.',
            'status' => Claim::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.claims.update-status', $claim), [
                'status' => 'approved',
                'review_note' => 'Ciri barang sesuai.',
            ])
            ->assertRedirect(route('admin.claims.show', $claim));

        $this->assertDatabaseHas('claims', ['id' => $claim->id, 'status' => Claim::STATUS_APPROVED]);
        $this->assertDatabaseHas('claims', ['id' => $otherClaim->id, 'status' => Claim::STATUS_REJECTED]);
        $this->assertDatabaseHas('item_reports', ['id' => $report->id, 'status' => ItemReport::STATUS_CLAIMED]);

        $this->post(route('admin.claims.handover', $claim), [
            'handed_over_at' => now()->format('Y-m-d H:i:s'),
            'notes' => 'Barang diterima langsung.',
        ])->assertRedirect(route('admin.claims.show', $claim));

        $this->assertDatabaseHas('item_reports', ['id' => $report->id, 'status' => ItemReport::STATUS_RETURNED]);
        $this->assertDatabaseHas('handovers', ['claim_id' => $claim->id, 'received_by' => $claimant->id]);

        $secondReport = ItemReport::factory()->create(['user_id' => $reporter->id, 'type' => ItemReport::TYPE_FOUND]);
        $rejectedClaim = Claim::create([
            'item_report_id' => $secondReport->id,
            'claimant_id' => $otherClaimant->id,
            'proof_details' => 'Klaim untuk barang kedua dengan ciri yang dapat diverifikasi.',
            'status' => Claim::STATUS_PENDING,
        ]);

        $this->patch(route('admin.claims.update-status', $rejectedClaim), [
            'status' => 'rejected',
            'review_note' => 'Ciri barang tidak sesuai.',
        ])->assertRedirect(route('admin.claims.show', $rejectedClaim));

        $this->assertDatabaseHas('claims', ['id' => $rejectedClaim->id, 'status' => Claim::STATUS_REJECTED]);
        $this->assertDatabaseHas('item_reports', ['id' => $secondReport->id, 'status' => ItemReport::STATUS_OPEN]);
    }
}
