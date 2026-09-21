<?php

namespace Tests\Feature;

use App\Livewire\Tickets\MyTickets;
use App\Livewire\Tickets\RaiseTicket;
use App\Livewire\Tickets\TicketDetail;
use App\Livewire\Tickets\TicketQueue;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\User;
use App\Models\Workspace;
use App\Services\TicketCapacityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TicketingTest extends TestCase
{
    use RefreshDatabase;

    protected Workspace $workspace;
    protected User $admin;
    protected User $member1;
    protected User $member2;
    protected User $guest;
    protected Team $team;
    protected TicketCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['name' => 'Admin Alex', 'email' => 'alex@example.com']);
        $this->workspace = Workspace::create([
            'name' => 'Support Workspace',
            'slug' => 'support-workspace',
            'owner_id' => $this->admin->id,
        ]);
        $this->workspace->members()->attach($this->admin->id, ['role' => 'owner']);

        $this->member1 = User::factory()->create(['name' => 'Engineer Hamza', 'email' => 'hamza@example.com']);
        $this->member2 = User::factory()->create(['name' => 'Engineer Ali', 'email' => 'ali@example.com']);
        $this->guest = User::factory()->create(['name' => 'External Guest', 'email' => 'guest@example.com']);

        $this->workspace->members()->attach($this->member1->id, ['role' => 'member']);
        $this->workspace->members()->attach($this->member2->id, ['role' => 'member']);
        $this->workspace->members()->attach($this->guest->id, ['role' => 'guest']);

        $this->team = Team::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'DevOps & Infrastructure',
        ]);

        $this->team->members()->attach($this->member1->id, ['role' => 'member', 'capacity_limit' => 5]);
        $this->team->members()->attach($this->member2->id, ['role' => 'member', 'capacity_limit' => 5]);

        $this->category = TicketCategory::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'Infrastructure Bug',
            'default_team_id' => $this->team->id,
        ]);
    }

    public function test_user_can_raise_ticket_with_auto_generated_number_and_sla(): void
    {
        $this->actingAs($this->member1);

        Livewire::test(RaiseTicket::class, ['workspace' => $this->workspace])
            ->set('subject', 'Server out of disk space')
            ->set('description', 'The /var log partition on staging server 01 is 99% full.')
            ->set('priority', 'urgent')
            ->set('categoryId', $this->category->id)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSubmitted', true);

        $this->assertDatabaseHas('tickets', [
            'workspace_id' => $this->workspace->id,
            'subject' => 'Server out of disk space',
            'priority' => 'urgent',
            'status' => 'open',
            'raised_by_user_id' => $this->member1->id,
        ]);

        $ticket = Ticket::where('subject', 'Server out of disk space')->first();
        $this->assertNotNull($ticket->ticket_number);
        $this->assertStringStartsWith('TCK-', $ticket->ticket_number);
        $this->assertNotNull($ticket->due_by);
        // Urgent SLA is 4 hours
        $this->assertTrue($ticket->due_by->diffInHours(now()) <= 4);
    }

    public function test_capacity_service_suggests_least_loaded_team_member(): void
    {
        // Give member1 3 active tickets and member2 1 active ticket
        for ($i = 0; $i < 3; $i++) {
            Ticket::create([
                'workspace_id' => $this->workspace->id,
                'ticket_number' => "TCK-200{$i}",
                'subject' => "Task {$i}",
                'description' => "Test description {$i}",
                'priority' => 'normal',
                'status' => 'in_progress',
                'raised_by_user_id' => $this->admin->id,
                'assigned_team_id' => $this->team->id,
                'assigned_to_user_id' => $this->member1->id,
            ]);
        }

        Ticket::create([
            'workspace_id' => $this->workspace->id,
            'ticket_number' => 'TCK-2004',
            'subject' => 'Single task for member 2',
            'description' => 'Test description',
            'priority' => 'normal',
            'status' => 'assigned',
            'raised_by_user_id' => $this->admin->id,
            'assigned_team_id' => $this->team->id,
            'assigned_to_user_id' => $this->member2->id,
        ]);

        $service = app(TicketCapacityService::class);
        $suggested = $service->suggestBestAssignee($this->team);

        // member2 should be suggested because member2 has 1/5 load vs member1's 3/5 load
        $this->assertEquals($this->member2->id, $suggested->id);
    }

    public function test_status_transitions_and_resolution_summary_requirement(): void
    {
        $ticket = Ticket::create([
            'workspace_id' => $this->workspace->id,
            'ticket_number' => 'TCK-3001',
            'subject' => 'Resolve workflow test',
            'description' => 'Testing resolution lifecycle',
            'priority' => 'normal',
            'status' => 'in_progress',
            'raised_by_user_id' => $this->member1->id,
            'assigned_team_id' => $this->team->id,
            'assigned_to_user_id' => $this->member2->id,
        ]);

        $this->actingAs($this->member2);

        // Transition to resolved requires resolutionSummary
        Livewire::test(TicketDetail::class)
            ->call('openTicket', $ticket->id)
            ->call('updateStatus', 'resolved')
            ->assertSet('showResolveModal', true)
            ->set('resolutionSummary', 'Fixed by applying patch 2.4.1 to nginx config')
            ->call('confirmResolution')
            ->assertSet('showResolveModal', false);

        $ticket->refresh();
        $this->assertEquals('resolved', $ticket->status);
        $this->assertNotNull($ticket->resolved_at);
        $this->assertEquals('Fixed by applying patch 2.4.1 to nginx config', $ticket->resolution_summary);
    }

    public function test_requester_can_reopen_ticket_within_7_days(): void
    {
        $ticket = Ticket::create([
            'workspace_id' => $this->workspace->id,
            'ticket_number' => 'TCK-4001',
            'subject' => 'Bug not fixed yet',
            'description' => 'Reopen testing',
            'priority' => 'normal',
            'status' => 'resolved',
            'raised_by_user_id' => $this->member1->id,
            'assigned_team_id' => $this->team->id,
            'assigned_to_user_id' => $this->member2->id,
            'resolved_at' => now()->subDays(2),
            'resolution_summary' => 'Initial fix attempted',
        ]);

        $this->actingAs($this->member1);

        Livewire::test(TicketDetail::class)
            ->call('openTicket', $ticket->id)
            ->call('reopenTicket');

        $ticket->refresh();
        $this->assertEquals('reopened', $ticket->status);
        $this->assertNull($ticket->resolved_at);
    }

    public function test_internal_notes_are_hidden_from_guest_requester(): void
    {
        $ticket = Ticket::create([
            'workspace_id' => $this->workspace->id,
            'ticket_number' => 'TCK-5001',
            'subject' => 'Guest ticket',
            'description' => 'Guest submission description',
            'priority' => 'normal',
            'status' => 'in_progress',
            'raised_by_user_id' => $this->guest->id,
            'assigned_team_id' => $this->team->id,
            'assigned_to_user_id' => $this->member1->id,
        ]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $this->member1->id,
            'body' => 'Public response visible to everyone',
            'is_internal_note' => false,
        ]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $this->member1->id,
            'body' => 'Secret confidential internal note for staff only',
            'is_internal_note' => true,
        ]);

        // Guest views ticket
        $this->actingAs($this->guest);

        Livewire::test(TicketDetail::class)
            ->call('openTicket', $ticket->id)
            ->assertSee('Public response visible to everyone')
            ->assertDontSee('Secret confidential internal note for staff only');
    }

    public function test_guest_cannot_view_other_users_tickets(): void
    {
        $otherTicket = Ticket::create([
            'workspace_id' => $this->workspace->id,
            'ticket_number' => 'TCK-6001',
            'subject' => 'Internal sensitive ticket',
            'description' => 'Not for guest eyes',
            'priority' => 'high',
            'status' => 'open',
            'raised_by_user_id' => $this->member1->id,
        ]);

        $this->actingAs($this->guest);

        // Guest attempts to open another user's ticket -> 403
        Livewire::test(TicketDetail::class)
            ->call('openTicket', $otherTicket->id)
            ->assertStatus(403);
    }

    public function test_non_admin_cannot_access_triage_queue(): void
    {
        $this->actingAs($this->member1);

        $response = $this->get(route('workspace.tickets.queue', ['workspace' => $this->workspace->slug]));
        $response->assertStatus(403);
    }
}
