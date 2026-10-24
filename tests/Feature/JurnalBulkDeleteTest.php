<?php

namespace Tests\Feature;

use App\Models\SelfDevelopmentJournal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JurnalBulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_jurnal_index_shows_selection_toolbar_when_journals_exist(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $j1 = SelfDevelopmentJournal::create([
            'user_id'       => $user->id,
            'activity_date' => '2026-10-01',
            'activity_name' => 'Seminar A',
            'organizer'     => 'Panitia A',
            'speaker'       => 'Narasumber A',
            'description'   => 'Deskripsi seminar A',
            'reflection'    => str_repeat('Refleksi. ', 22),
            'status'        => 'published',
            'published_at'  => now(),
        ]);

        $j2 = SelfDevelopmentJournal::create([
            'user_id'       => $user->id,
            'activity_date' => '2026-10-02',
            'activity_name' => 'Seminar B',
            'organizer'     => 'Panitia B',
            'speaker'       => 'Narasumber B',
            'description'   => 'Deskripsi seminar B',
            'reflection'    => str_repeat('Refleksi. ', 22),
            'status'        => 'published',
            'published_at'  => now(),
        ]);

        $html = $this->actingAs($user)
            ->get(route('jurnal.index'))
            ->assertOk()
            ->getContent();

        // Toolbar pilih semua ada
        $this->assertStringContainsString('id="selectionToolbar"', $html);
        $this->assertStringContainsString('id="checkAll"', $html);
        $this->assertStringContainsString('jurnal-checkbox', $html);
        $this->assertStringContainsString('id="modalKonfirmasi"', $html);
        $this->assertStringContainsString('konfirmasiHapusMassal', $html);
        $this->assertStringContainsString('konfirmasiHapusSatu', $html);
    }

    public function test_bulk_delete_removes_only_own_journals(): void
    {
        $user  = User::factory()->create(['role' => 'user', 'is_active' => true]);
        $other = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $j1 = SelfDevelopmentJournal::create([
            'user_id' => $user->id, 'activity_date' => '2026-10-01',
            'activity_name' => 'A', 'organizer' => 'O', 'speaker' => 'S',
            'description' => 'D', 'reflection' => str_repeat('R ', 110),
            'status' => 'published', 'published_at' => now(),
        ]);
        $j2 = SelfDevelopmentJournal::create([
            'user_id' => $user->id, 'activity_date' => '2026-10-02',
            'activity_name' => 'B', 'organizer' => 'O', 'speaker' => 'S',
            'description' => 'D', 'reflection' => str_repeat('R ', 110),
            'status' => 'published', 'published_at' => now(),
        ]);
        $jOther = SelfDevelopmentJournal::create([
            'user_id' => $other->id, 'activity_date' => '2026-10-03',
            'activity_name' => 'C', 'organizer' => 'O', 'speaker' => 'S',
            'description' => 'D', 'reflection' => str_repeat('R ', 110),
            'status' => 'published', 'published_at' => now(),
        ]);

        // Kirim ids[] termasuk jurnal milik user lain (serangan lintas user)
        $this->actingAs($user)
            ->delete(route('jurnal.destroyBulk'), [
                'ids' => [$j1->id, $j2->id, $jOther->id],
            ])
            ->assertRedirect(route('jurnal.index'));

        // Jurnal milik user sendiri terhapus (soft-deleted)
        $this->assertSoftDeleted('self_development_journals', ['id' => $j1->id]);
        $this->assertSoftDeleted('self_development_journals', ['id' => $j2->id]);

        // Jurnal milik user lain AMAN — tidak terhapus sama sekali
        $this->assertDatabaseHas('self_development_journals', [
            'id' => $jOther->id,
            'deleted_at' => null,
        ]);
    }

    public function test_bulk_delete_with_no_ids_redirects_with_error(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $this->actingAs($user)
            ->delete(route('jurnal.destroyBulk'), ['ids' => []])
            ->assertRedirect(route('jurnal.index'));
    }
}
