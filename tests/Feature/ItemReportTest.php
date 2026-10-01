<?php

namespace Tests\Feature;

use App\Models\ItemReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    }
}
