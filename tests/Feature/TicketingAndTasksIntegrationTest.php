<?php

namespace Tests\Feature;

use App\Livewire\Tasks\TaskDetailModal;
use App\Livewire\Tickets\TicketDetail;
use App\Livewire\Workspace\ActivityLogs;
use App\Models\Project;
use App\Models\Space;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\TaskStatus;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TicketingAndTasksIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected Workspace $workspace;
    protected User $admin;
    protected User $staff;
    protected User $requester;
    protected Space $space;
    protected Project $project;
    protected TaskList $taskList;
    protected TaskStatus $todoStatus;
    protected Team $team;
    protected TicketCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['name' => 'Admin Alex', 'email' => 'admin@example.com']);
        $this->staff = User::factory()->create(['name' => 'Staff Sarah', 'email' => 'staff@example.com']);
        $this->requester = User::factory()->create(['name' => 'Requester Rachel', 'email' => 'requester@example.com']);

        $this->workspace = Workspace::create([
            'name' => 'MIS Workspace',
            'slug' => 'mis-workspace',
            'owner_id' => $this->admin->id,
        ]);

        $this->workspace->members()->attach($this->admin->id, ['role' => 'owner']);
        $this->workspace->members()->attach($this->staff->id, ['role' => 'member']);
        $this->workspace->members()->attach($this->requester->id, ['role' => 'requester']);

        $this->todoStatus = TaskStatus::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'To Do',
            'color' => '#6366f1',
            'type' => 'todo',
            'is_default' => true,
            'sort_order' => 1,
        ]);

        $this->space = Space::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'Core Development',
        ]);

        $this->project = Project::create([
            'space_id' => $this->space->id,
            'name' => 'Student Portal',
        ]);

        $this->taskList = TaskList::create([
            'project_id' => $this->project->id,
            'name' => 'Sprint 1',
        ]);

        $this->team = Team::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'DevOps',
        ]);
        $this->team->members()->attach($this->staff->id, ['role' => 'member']);

        $this->category = TicketCategory::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'System Glitch',
            'default_team_id' => $this->team->id,
        ]);
    }

    public function test_staff_can_convert_ticket_to_task(): void
    {
        $this->actingAs($this->staff);

        $ticket = Ticket::create([
            'workspace_id' => $this->workspace->id,
            'ticket_number' => 'TCK-2001',
            'subject' => 'Database connection timeout on portal',
            'description' => 'Users unable to query transcript grades after 9 PM.',
            'category_id' => $this->category->id,
            'project_id' => $this->project->id,
            'priority' => 'high',
            'status' => 'open',
            'raised_by_user_id' => $this->requester->id,
            'assigned_team_id' => $this->team->id,
            'due_by' => now()->addHours(8),
        ]);

        Livewire::test(TicketDetail::class)
            ->call('openTicket', $ticket->id)
            ->call('openConvertToTaskModal')
            ->assertSet('showConvertToTaskModal', true)
            ->set('targetTaskListId', $this->taskList->id)
            ->set('targetTaskTitle', 'Fix database connection timeout')
            ->set('targetTaskPriority', 'urgent')
            ->call('convertToTask')
            ->assertHasNoErrors()
            ->assertSet('showConvertToTaskModal', false);

        $ticket->refresh();
        $this->assertNotNull($ticket->task_id);

        $task = Task::find($ticket->task_id);
        $this->assertNotNull($task);
        $this->assertEquals('Fix database connection timeout', $task->title);
        $this->assertEquals('urgent', $task->priority);
        $this->assertEquals($ticket->id, $task->ticket_id);

        $this->assertDatabaseHas('ticket_activity_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'converted_to_task',
        ]);

        $this->assertDatabaseHas('task_activities', [
            'task_id' => $task->id,
            'action' => 'converted_from_ticket',
        ]);
    }

    public function test_requester_can_submit_csat_rating_on_resolved_ticket(): void
    {
        $this->actingAs($this->requester);

        $ticket = Ticket::create([
            'workspace_id' => $this->workspace->id,
            'ticket_number' => 'TCK-2002',
            'subject' => 'Password reset SMS not received',
            'description' => 'SMS gateway timeout during login.',
            'category_id' => $this->category->id,
            'priority' => 'normal',
            'status' => 'resolved',
            'resolved_at' => now(),
            'raised_by_user_id' => $this->requester->id,
            'assigned_to_user_id' => $this->staff->id,
            'due_by' => now()->addHours(24),
        ]);

        Livewire::test(TicketDetail::class)
            ->call('openTicket', $ticket->id)
            ->set('csatFeedback', 'Quick turnaround, resolved within an hour!')
            ->call('submitCsatRating', 5)
            ->assertHasNoErrors();

        $ticket->refresh();
        $this->assertEquals(5, $ticket->rating);
        $this->assertEquals('Quick turnaround, resolved within an hour!', $ticket->rating_feedback);
        $this->assertTrue($ticket->hasRating());

        $this->assertDatabaseHas('ticket_activity_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'csat_rated',
        ]);
    }

    public function test_staff_can_apply_canned_response_in_ticket_detail(): void
    {
        $this->actingAs($this->staff);

        $ticket = Ticket::create([
            'workspace_id' => $this->workspace->id,
            'ticket_number' => 'TCK-2003',
            'subject' => 'Login error code 503',
            'description' => 'Server returns 503 during course registration.',
            'category_id' => $this->category->id,
            'priority' => 'normal',
            'status' => 'open',
            'raised_by_user_id' => $this->requester->id,
            'assigned_team_id' => $this->team->id,
        ]);

        $component = Livewire::test(TicketDetail::class)
            ->call('openTicket', $ticket->id)
            ->call('applyCannedResponse', 'investigating');

        $this->assertStringContainsString('actively investigating', $component->get('newCommentBody'));
    }

    public function test_staff_can_link_existing_ticket_to_task_in_modal(): void
    {
        $this->actingAs($this->staff);

        $task = Task::create([
            'task_list_id' => $this->taskList->id,
            'created_by_id' => $this->staff->id,
            'title' => 'Optimize grade calculation query',
            'status_id' => $this->todoStatus->id,
            'priority' => 'normal',
        ]);

        $ticket = Ticket::create([
            'workspace_id' => $this->workspace->id,
            'ticket_number' => 'TCK-2004',
            'subject' => 'Transcript slow loading issue',
            'description' => 'Grades page loading takes over 15 seconds.',
            'category_id' => $this->category->id,
            'priority' => 'high',
            'status' => 'open',
            'raised_by_user_id' => $this->requester->id,
        ]);

        Livewire::test(TaskDetailModal::class)
            ->call('openTask', $task->id)
            ->call('openLinkTicketModal')
            ->assertSet('showLinkTicketModal', true)
            ->set('selectedTicketIdToLink', $ticket->id)
            ->call('linkTicket')
            ->assertHasNoErrors()
            ->assertSet('showLinkTicketModal', false);

        $task->refresh();
        $ticket->refresh();

        $this->assertEquals($ticket->id, $task->ticket_id);
        $this->assertEquals($task->id, $ticket->task_id);

        $this->assertDatabaseHas('task_activities', [
            'task_id' => $task->id,
            'action' => 'ticket_linked',
        ]);

        $this->assertDatabaseHas('ticket_activity_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'linked_to_task',
        ]);
    }

    public function test_staff_can_unlink_ticket_from_task(): void
    {
        $this->actingAs($this->staff);

        $task = Task::create([
            'task_list_id' => $this->taskList->id,
            'created_by_id' => $this->staff->id,
            'title' => 'Investigate cache invalidation',
            'status_id' => $this->todoStatus->id,
            'priority' => 'normal',
        ]);

        $ticket = Ticket::create([
            'workspace_id' => $this->workspace->id,
            'ticket_number' => 'TCK-2005',
            'subject' => 'Outdated profile photo displaying',
            'description' => 'Image CDN cache not flushing on upload.',
            'category_id' => $this->category->id,
            'priority' => 'low',
            'status' => 'open',
            'raised_by_user_id' => $this->requester->id,
            'task_id' => $task->id,
        ]);

        $task->update(['ticket_id' => $ticket->id]);

        Livewire::test(TaskDetailModal::class)
            ->call('openTask', $task->id)
            ->call('unlinkTicket')
            ->assertHasNoErrors();

        $task->refresh();
        $ticket->refresh();

        $this->assertNull($task->ticket_id);
        $this->assertNull($ticket->task_id);

        $this->assertDatabaseHas('task_activities', [
            'task_id' => $task->id,
            'action' => 'ticket_unlinked',
        ]);

        $this->assertDatabaseHas('ticket_activity_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'unlinked_from_task',
        ]);
    }

    public function test_staff_can_create_ticket_from_task(): void
    {
        $this->actingAs($this->staff);

        $task = Task::create([
            'task_list_id' => $this->taskList->id,
            'created_by_id' => $this->staff->id,
            'title' => 'Security patch for API authentication header',
            'description' => 'JWT secret rotation required urgently.',
            'status_id' => $this->todoStatus->id,
            'priority' => 'urgent',
        ]);

        Livewire::test(TaskDetailModal::class)
            ->call('openTask', $task->id)
            ->call('openCreateTicketModal')
            ->assertSet('showCreateTicketModal', true)
            ->set('createTicketSubject', 'Security incident: API header vulnerability')
            ->set('createTicketCategoryId', $this->category->id)
            ->set('createTicketPriority', 'urgent')
            ->call('createTicketFromTask')
            ->assertHasNoErrors()
            ->assertSet('showCreateTicketModal', false);

        $task->refresh();
        $this->assertNotNull($task->ticket_id);

        $ticket = Ticket::find($task->ticket_id);
        $this->assertNotNull($ticket);
        $this->assertEquals('Security incident: API header vulnerability', $ticket->subject);
        $this->assertEquals('urgent', $ticket->priority);
        $this->assertEquals($task->id, $ticket->task_id);
        $this->assertStringStartsWith('TCK-', $ticket->ticket_number);

        $this->assertDatabaseHas('task_activities', [
            'task_id' => $task->id,
            'action' => 'ticket_created',
        ]);

        $this->assertDatabaseHas('ticket_activity_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'created_from_task',
        ]);
    }

    public function test_workspace_activity_logs_and_csv_export(): void
    {
        $this->actingAs($this->staff);

        // View activity logs component
        Livewire::test(ActivityLogs::class, ['workspace' => $this->workspace])
            ->assertOk()
            ->set('typeFilter', 'all')
            ->set('actionFilter', 'all')
            ->assertHasNoErrors();

        // Member can export CSV
        $response = $this->get(route('workspace.activity-logs.export', ['workspace' => $this->workspace->slug]));
        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));

        // Requester cannot export CSV
        $this->actingAs($this->requester);
        $forbiddenResponse = $this->get(route('workspace.activity-logs.export', ['workspace' => $this->workspace->slug]));
        $forbiddenResponse->assertForbidden();
    }
}
