<?php

namespace Tests\Feature;

use App\Livewire\Notifications\Dropdown;
use App\Livewire\Notifications\Index;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_only_own_notifications(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();

        Notification::factory()->count(3)->create(['user_id' => $me->id, 'is_read' => false]);
        Notification::factory()->count(2)->create(['user_id' => $other->id]);

        $this->actingAs($me);

        Livewire::test(Index::class)
            ->assertViewHas('notifications', fn ($p) => $p->total() === 3)
            ->assertViewHas('unreadCount', 3);
    }

    public function test_mark_all_read(): void
    {
        $me = User::factory()->create();
        Notification::factory()->count(4)->create(['user_id' => $me->id, 'is_read' => false]);

        $this->actingAs($me);

        Livewire::test(Index::class)->call('markAllRead');

        $this->assertEquals(0, Notification::where('user_id', $me->id)->where('is_read', false)->count());
    }

    public function test_dropdown_shows_unread_count(): void
    {
        $me = User::factory()->create();
        Notification::factory()->count(2)->unread()->create(['user_id' => $me->id]);
        Notification::factory()->count(3)->read()->create(['user_id' => $me->id]);

        $this->actingAs($me);

        Livewire::test(Dropdown::class)->assertSet('unread', 2);
    }
}
