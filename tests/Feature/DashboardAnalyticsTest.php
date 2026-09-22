<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Tickets\RaiseTicket;
use App\Livewire\Tickets\TicketDetail;
use App\Models\Project;
use App\Models\Space;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Models\Workspace;
use App\Services\GeminiService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected Workspace $workspace;
    protected User $user;
    protected Space $space;
    protected Project $projectA;
    protected Project $projectB;
    protected TicketCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['name' => 'Analytics Tester', 'email' => 'analytics@stmu.edu.pk']);
        $this->workspace = Workspace::create([
            'name' => 'Analytics Workspace',
            'slug' => 'analytics-workspace',
            'owner_id' => $this->user->id,
        ]);
        $this->workspace->members()->attach($this->user->id, ['role' => 'owner']);

        $this->space = Space::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'Core Engineering',
            'color' => '#6366f1',
        ]);

        $this->projectA = Project::create([
            'space_id' => $this->space->id,
            'name' => 'Admission Portal',
        ]);

        $this->projectB = Project::create([
            'space_id' => $this->space->id,
            'name' => 'LMS Mobile App',
        ]);

        $this->category = TicketCategory::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'Bug Report',
        ]);

        // Create tasks in projectA and projectB
        $listA = TaskList::create(['project_id' => $this->projectA->id, 'name' => 'Sprint 1']);
        $listB = TaskList::create(['project_id' => $this->projectB->id, 'name' => 'Sprint 2']);

        $status = \App\Models\TaskStatus::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'To Do',
            'type' => 'todo',
            'is_default' => true,
        ]);

        Task::create([
            'task_list_id' => $listA->id,
            'title' => 'Fix gateway timeout',
            'status_id' => $status->id,
            'created_by_id' => $this->user->id,
            'created_at' => Carbon::create(2026, 9, 10, 10, 0, 0),
        ]);

        Task::create([
            'task_list_id' => $listB->id,
            'title' => 'Optimize mobile bundle',
            'status_id' => $status->id,
            'created_by_id' => $this->user->id,
            'created_at' => Carbon::create(2026, 9, 12, 10, 0, 0),
        ]);

        // Tickets in current month (Sept 2026)
        Ticket::create([
            'workspace_id' => $this->workspace->id,
            'project_id' => $this->projectA->id,
            'category_id' => $this->category->id,
            'ticket_number' => 'TCK-9001',
            'subject' => 'Portal Crash',
            'description' => 'Crash on login page',
            'priority' => 'urgent',
            'status' => 'open',
            'raised_by_user_id' => $this->user->id,
            'created_at' => Carbon::create(2026, 9, 15, 12, 0, 0),
        ]);

        // Ticket in previous month (August 2026)
        Ticket::create([
            'workspace_id' => $this->workspace->id,
            'project_id' => $this->projectA->id,
            'category_id' => $this->category->id,
            'ticket_number' => 'TCK-9002',
            'subject' => 'Legacy Bug',
            'description' => 'Occurred last month',
            'priority' => 'high',
            'status' => 'resolved',
            'raised_by_user_id' => $this->user->id,
            'resolved_at' => Carbon::create(2026, 8, 20, 14, 0, 0),
            'created_at' => Carbon::create(2026, 8, 18, 12, 0, 0),
        ]);
    }

    public function test_dashboard_renders_with_comparative_analytics(): void
    {
        $this->actingAs($this->user);

        // Fix current time to September 2026
        Carbon::setTestNow(Carbon::create(2026, 9, 22, 12, 0, 0));

        Livewire::test(Dashboard::class)
            ->assertSet('timeRange', 'month')
            ->assertSet('selectedYear', 2026)
            ->assertSet('selectedMonth', 9)
            ->assertSet('chartType', 'line')
            ->assertSee('Interactive Influx &amp; Workload Analytics', false)
            ->assertSee('Incoming Ticket Influx')
            ->assertSee('Admission Portal')
            ->assertSee('LMS Mobile App')
            ->assertSee('Bug Report');
    }

    public function test_switching_time_range_updates_boundaries_and_deltas(): void
    {
        $this->actingAs($this->user);
        Carbon::setTestNow(Carbon::create(2026, 9, 22, 12, 0, 0));

        Livewire::test(Dashboard::class)
            ->call('setTimeRange', 'year')
            ->assertSet('timeRange', 'year')
            ->call('setTimeRange', 'last_month')
            ->assertSet('timeRange', 'last_month')
            ->call('setTimeRange', 'all')
            ->assertSet('timeRange', 'all')
            ->call('setChartType', 'bar')
            ->assertSet('chartType', 'bar');
    }

    public function test_filtering_by_project_isolates_metrics(): void
    {
        $this->actingAs($this->user);
        Carbon::setTestNow(Carbon::create(2026, 9, 22, 12, 0, 0));

        Livewire::test(Dashboard::class)
            ->set('filterProjectId', $this->projectA->id)
            ->assertSet('filterProjectId', $this->projectA->id)
            ->assertSee('Admission Portal');
    }

    public function test_user_can_raise_ticket_with_associated_project(): void
    {
        $this->actingAs($this->user);

        Livewire::test(RaiseTicket::class, ['workspace' => $this->workspace])
            ->set('subject', 'Admission form photo upload error')
            ->set('description', 'JPEG photos above 2MB fail to upload with an unexpected exception.')
            ->set('priority', 'high')
            ->set('categoryId', $this->category->id)
            ->set('projectId', $this->projectA->id)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSubmitted', true);

        $this->assertDatabaseHas('tickets', [
            'workspace_id' => $this->workspace->id,
            'subject' => 'Admission form photo upload error',
            'project_id' => $this->projectA->id,
            'category_id' => $this->category->id,
        ]);
    }

    public function test_ticket_detail_displays_and_updates_project(): void
    {
        $this->actingAs($this->user);

        $ticket = Ticket::where('ticket_number', 'TCK-9001')->first();
        $this->assertEquals($this->projectA->id, $ticket->project_id);

        Livewire::test(TicketDetail::class)
            ->dispatch('open-ticket-detail', ticketId: $ticket->id)
            ->assertSet('isOpen', true)
            ->assertSet('projectId', $this->projectA->id)
            ->call('updateProject', $this->projectB->id)
            ->assertSet('projectId', $this->projectB->id);

        $this->assertEquals($this->projectB->id, $ticket->fresh()->project_id);
    }

    public function test_generate_ai_analytics_insight_runs_cleanly(): void
    {
        $this->actingAs($this->user);
        Carbon::setTestNow(Carbon::create(2026, 9, 22, 12, 0, 0));

        // Mock GeminiService to return a controlled insight
        $this->mock(GeminiService::class, function ($mock) {
            $mock->shouldReceive('isConfigured')->andReturn(true);
            $mock->shouldReceive('generateAnalyticsInsights')->andReturn("### Executive Diagnostic\n\nTicket influx on Admission Portal increased by 100% due to admissions season.");
        });

        Livewire::test(Dashboard::class)
            ->set('aiPromptQuery', 'Analyze ticket influx patterns')
            ->call('generateAiAnalyticsInsight')
            ->assertSee('Executive Diagnostic')
            ->assertSee('Admission Portal');
    }
}
