<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Tasks\MyTasks;
use App\Livewire\Tasks\TaskManager;
use App\Livewire\Tickets\MyTickets;
use App\Livewire\Tickets\RaiseTicket;
use App\Livewire\Tickets\TicketDetail;
use App\Livewire\Workspace\TeamManager;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RequesterPortalTest extends TestCase
{
    use RefreshDatabase;

    protected Workspace $workspace;
    protected User $owner;
    protected User $admin;
    protected User $member;
    protected User $requester;
    protected TicketCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Owner', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Member', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Guest', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Requester', 'guard_name' => 'web']);

        $this->owner = User::factory()->create(['name' => 'Workspace Owner', 'email' => 'owner@test.com']);
        $this->owner->assignRole('Owner');

        $this->workspace = Workspace::create([
            'name' => 'STMU MIS Portal',
            'slug' => 'stmu-mis',
            'owner_id' => $this->owner->id,
        ]);
        $this->workspace->members()->attach($this->owner->id, ['role' => 'owner']);

        $this->admin = User::factory()->create(['name' => 'Admin User', 'email' => 'admin@test.com']);
        $this->admin->assignRole('Admin');
        $this->workspace->members()->attach($this->admin->id, ['role' => 'admin']);

        $this->member = User::factory()->create(['name' => 'Developer Member', 'email' => 'dev@test.com']);
        $this->member->assignRole('Member');
        $this->workspace->members()->attach($this->member->id, ['role' => 'member']);

        $this->requester = User::factory()->create(['name' => 'Support Requester', 'email' => 'requester@test.com']);
        $this->requester->assignRole('Requester');
        $this->workspace->members()->attach($this->requester->id, ['role' => 'requester']);

        $this->category = TicketCategory::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'Portal Technical Issue',
            'description' => 'Issues submitting forms or accessing portal services',
        ]);
    }

    public function test_user_is_identified_as_workspace_requester(): void
    {
        $this->assertTrue($this->requester->isWorkspaceRequester($this->workspace));
        $this->assertTrue($this->requester->isTicketOnlyUser($this->workspace));

        $this->assertFalse($this->owner->isWorkspaceRequester($this->workspace));
        $this->assertFalse($this->admin->isWorkspaceRequester($this->workspace));
        $this->assertFalse($this->member->isWorkspaceRequester($this->workspace));

        // Legacy guest role is also recognized as requester
        $legacyGuest = User::factory()->create(['name' => 'Legacy Guest', 'email' => 'guest@test.com']);
        $this->workspace->members()->attach($legacyGuest->id, ['role' => 'guest']);
        $this->assertTrue($legacyGuest->isWorkspaceRequester($this->workspace));
    }

    public function test_requester_visiting_dashboard_is_redirected_to_my_tickets(): void
    {
        Livewire::actingAs($this->requester)
            ->test(Dashboard::class, ['workspace' => $this->workspace])
            ->assertRedirect(route('workspace.tickets.my', ['workspace' => $this->workspace->slug]));
    }

    public function test_requester_accessing_internal_sections_is_redirected(): void
    {
        // TaskManager
        Livewire::actingAs($this->requester)
            ->test(TaskManager::class, ['workspace' => $this->workspace])
            ->assertRedirect(route('workspace.tickets.my', ['workspace' => $this->workspace->slug]));

        // MyTasks
        Livewire::actingAs($this->requester)
            ->test(MyTasks::class, ['workspace' => $this->workspace])
            ->assertRedirect(route('workspace.tickets.my', ['workspace' => $this->workspace->slug]));

        // TeamManager
        Livewire::actingAs($this->requester)
            ->test(TeamManager::class, ['workspace' => $this->workspace])
            ->assertRedirect(route('workspace.tickets.my', ['workspace' => $this->workspace->slug]));

        // Export tasks CSV is forbidden
        $response = $this->actingAs($this->requester)->get(route('workspace.export', ['workspace' => $this->workspace->slug]));
        $response->assertForbidden();
    }

    public function test_requester_sees_only_ticket_navigation_in_sidebar(): void
    {
        $response = $this->actingAs($this->requester)->get(route('workspace.tickets.my', ['workspace' => $this->workspace->slug]));
        $response->assertOk();

        // Should see ticket links
        $response->assertSee('My Tickets');
        $response->assertSee('Raise Ticket');
        $response->assertSee('Requester');

        // Should NOT see internal workspace sections in navigation
        $response->assertDontSee('Task Board');
        $response->assertDontSee('Teams &amp; People');
        $response->assertDontSee('Triage Queue');
        $response->assertDontSee('Team Capacity');
        $response->assertDontSee('Categories &amp; Routing');
        $response->assertDontSee('Access Requests');
        $response->assertDontSee('Ask Gemini or search...');
    }

    public function test_my_tickets_locks_view_mode_to_raised_for_requester(): void
    {
        // Requester has viewMode strictly as 'raised'
        Livewire::actingAs($this->requester)
            ->test(MyTickets::class, ['workspace' => $this->workspace])
            ->assertSet('viewMode', 'raised')
            ->assertDontSee('Assigned to Me');

        // Admin has access to viewMode toggle tabs
        Livewire::actingAs($this->admin)
            ->test(MyTickets::class, ['workspace' => $this->workspace])
            ->assertSee('Assigned to Me')
            ->assertSee('Raised by Me');
    }

    public function test_requester_can_raise_ticket_and_view_its_status(): void
    {
        // 1. Requester raises a ticket
        Livewire::actingAs($this->requester)
            ->test(RaiseTicket::class, ['workspace' => $this->workspace])
            ->set('subject', 'Faculty LMS course upload error')
            ->set('description', 'PDF syllabus cannot be attached to the lecture outline module due to 500 error.')
            ->set('categoryId', $this->category->id)
            ->set('priority', 'high')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSubmitted', true);

        $this->assertDatabaseHas('tickets', [
            'workspace_id' => $this->workspace->id,
            'subject' => 'Faculty LMS course upload error',
            'raised_by_user_id' => $this->requester->id,
            'status' => 'open',
            'priority' => 'high',
        ]);

        $ticket = Ticket::where('raised_by_user_id', $this->requester->id)->first();
        $this->assertNotNull($ticket);

        // 2. Requester sees it in MyTickets
        Livewire::actingAs($this->requester)
            ->test(MyTickets::class, ['workspace' => $this->workspace])
            ->assertSee($ticket->ticket_number)
            ->assertSee('Faculty LMS course upload error');

        // 3. Requester can open TicketDetail and view status and timeline
        Livewire::actingAs($this->requester)
            ->test(TicketDetail::class)
            ->dispatch('open-ticket-detail', ticketId: $ticket->id)
            ->assertSet('isOpen', true)
            ->assertSet('status', 'open')
            ->assertSee('Faculty LMS course upload error');
    }

    public function test_admin_can_update_member_role_to_requester_in_team_manager(): void
    {
        Livewire::actingAs($this->admin)
            ->test(TeamManager::class, ['workspace' => $this->workspace])
            ->call('updateMemberRole', $this->member->id, 'requester');

        $this->assertEquals('requester', $this->member->roleInWorkspace($this->workspace));
        $this->assertTrue($this->member->fresh()->isWorkspaceRequester($this->workspace));
    }
}
