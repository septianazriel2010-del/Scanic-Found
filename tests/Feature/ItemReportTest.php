<?php

namespace Tests\Feature;

use App\Models\ItemReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_item_report_list(): void
    {
        ItemReport::factory(3)->create();

        $this->get(route('items.index'))->assertOk();
    }

    public function test_guest_cannot_create_item_report(): void
    {
        $this->get(route('items.create'))->assertRedirect(route('login'));
    }

    public function test_report_form_rejects_category_or_location_outside_select_options(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from(route('items.create'))->post(route('items.store'), [
            'type' => 'lost',
            'title' => 'Kunci motor',
            'description' => 'Kunci motor dengan gantungan merah.',
            'category' => 'Kategori typo',
            'location' => 'Lokasi typo',
            'incident_date' => now()->toDateString(),
        ])->assertSessionHasErrors(['category', 'location']);
    }

    public function test_only_owner_can_update_their_report(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $report = ItemReport::factory()->create(['user_id' => $owner->id]);

        // Owner boleh masuk ke halaman edit.
        $this->actingAs($owner)
            ->get(route('items.edit', $report))
            ->assertOk();

        // User lain TIDAK boleh (mencegah IDOR).
        $this->actingAs($otherUser)
            ->get(route('items.edit', $report))
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->delete(route('items.destroy', $report))
            ->assertForbidden();

        $this->actingAs($owner)
            ->delete(route('items.destroy', $report))
            ->assertRedirect(route('items.index'));

        $this->assertSoftDeleted($report);
    }

    public function test_admin_can_edit_own_and_delete_any_reports_while_keeps_report_actions(): void
    {
        $admin = User::factory()->admin()->create();
        $reporter = User::factory()->create();
        $foundReport = ItemReport::factory()->create([
            'user_id' => $reporter->id,
            'type' => ItemReport::TYPE_FOUND,
            'status' => ItemReport::STATUS_OPEN,
        ]);
        $lostReport = ItemReport::factory()->create([
            'user_id' => $reporter->id,
            'type' => ItemReport::TYPE_LOST,
            'status' => ItemReport::STATUS_OPEN,
        ]);
        $adminOwnedReport = ItemReport::factory()->create([
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('items.edit', $adminOwnedReport))
            ->assertOk();
        $this->get(route('items.show', $adminOwnedReport))
            ->assertOk()
            ->assertSee('Edit Laporan');

        $this->actingAs($admin)
            ->get(route('items.edit', $foundReport))
            ->assertForbidden();

        $this->get(route('items.show', $foundReport))
            ->assertOk()
            ->assertDontSee('Edit Laporan')
            ->assertSee('Hapus Laporan')
            ->assertSee('Ajukan Klaim');

        $this->get(route('items.show', $lostReport))
            ->assertOk()
            ->assertSee('Laporkan Barang Ditemukan');

        $this->delete(route('items.destroy', $foundReport))
            ->assertRedirect(route('items.index'));

        $this->assertSoftDeleted($foundReport);

        $this->delete(route('items.destroy', $adminOwnedReport))
            ->assertRedirect(route('items.index'));

        $this->assertSoftDeleted($adminOwnedReport);
    }

    public function test_admin_can_reopen_closed_report(): void
    {
        $admin = User::factory()->admin()->create();
        $otherUser = User::factory()->create();
        $report = ItemReport::factory()->create([
            'status' => ItemReport::STATUS_CLOSED,
        ]);

        $this->actingAs($otherUser)
            ->patch(route('admin.reports.reopen', $report))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('Buka Kembali');

        $this->patch(route('admin.reports.reopen', $report))
            ->assertRedirect();

        $this->assertDatabaseHas('item_reports', [
            'id' => $report->id,
            'status' => ItemReport::STATUS_OPEN,
        ]);
    }

    public function test_uploaded_photo_is_saved_and_rendered_on_report_page(): void
    {
        $disk = config('filesystems.default');
        Storage::fake($disk);
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('items.store'), [
            'type' => 'lost',
            'title' => 'Dompet hitam',
            'description' => 'Dompet kecil dengan kartu pelajar.',
            'category' => 'Dompet',
            'location' => 'Kantin',
            'incident_date' => now()->toDateString(),
            'photo' => UploadedFile::fake()->create('dompet.jpg', 100, 'image/jpeg'),
        ])->assertRedirect();

        $report = ItemReport::latest()->firstOrFail();
        Storage::disk($disk)->assertExists($report->photo_path);

        $this->get(route('items.show', $report))
            ->assertOk()
            ->assertSee($report->photo_url);

        $this->get(route('items.index'))
            ->assertOk()
            ->assertSee('src="'.$report->photo_url.'"', false)
            ->assertDontSee('>'.$report->photo_path.'<', false);
    }

    public function test_photo_url_uses_local_file_or_shared_supabase_public_url(): void
    {
        Storage::fake('public');
        config([
            'filesystems.default' => 'public',
            'filesystems.disks.supabase.url' => 'https://project.supabase.co/storage/v1/object/public/reports',
        ]);

        Storage::disk('public')->put('item-reports/local.jpg', 'local image');
        $localReport = ItemReport::factory()->create(['photo_path' => 'item-reports/local.jpg']);

        $this->assertStringEndsWith('/storage/item-reports/local.jpg', $localReport->photo_url);

        $sharedReport = ItemReport::factory()->create(['photo_path' => 'item-reports/shared.jpg']);

        $this->assertSame(
            'https://project.supabase.co/storage/v1/object/public/reports/item-reports/shared.jpg',
            $sharedReport->photo_url,
        );
    }

    public function test_pending_claim_is_visible_and_hides_regular_claim_action(): void
    {
        $reporter = User::factory()->create();
        $claimant = User::factory()->create();
        $report = ItemReport::factory()->create([
            'user_id' => $reporter->id,
            'type' => ItemReport::TYPE_FOUND,
        ]);

        $report->claims()->create([
            'claimant_id' => $claimant->id,
            'proof_details' => 'Ada ciri khusus yang bisa diverifikasi.',
            'status' => \App\Models\Claim::STATUS_PENDING,
        ]);

        $this->get(route('items.show', $report))
            ->assertOk()
            ->assertSee('Menunggu Verifikasi')
            ->assertSee('Klaim telah diajukan dan sedang menunggu verifikasi admin.')
            ->assertDontSee('Ajukan Klaim');
    }
}
