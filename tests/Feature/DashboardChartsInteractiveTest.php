<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Dashboard;
use App\Models\Project;
use App\Models\Space;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\TaskStatus;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardChartsInteractiveTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workspace $workspace;
    protected Project $project;
    protected TicketCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['name' => 'Demo Leader']);
        $this->workspace = Workspace::create([
            'name' => 'MIS Innovation Hub',
            'slug' => 'mis-hub',
            'owner_id' => $this->user->id,
        ]);
        $this->workspace->members()->attach($this->user->id, ['role' => 'admin']);

        $todoStatus = TaskStatus::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'To Do',
            'type' => 'todo',
            'color' => '#64748b',
            'sort_order' => 1,
            'is_default' => true,
        ]);

        $doneStatus = TaskStatus::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'Done',
            'type' => 'done',
            'color' => '#10b981',
            'sort_order' => 2,
        ]);

        $space = Space::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'Core Systems',
            'color' => '#6366f1',
        ]);

        $this->project = Project::create([
            'space_id' => $space->id,
            'name' => 'Student Portal Modernization',
            'color' => '#8b5cf6',
        ]);

        $list = TaskList::create([
            'project_id' => $this->project->id,
            'name' => 'Backlog',
            'sort_order' => 1,
        ]);

        $this->category = TicketCategory::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'Portal Performance',
            'color' => '#6366f1',
        ]);

        // Historical Data: September 2026 (Past)
        for ($i = 1; $i <= 5; $i++) {
            $created = Carbon::create(2026, 9, 10 + $i, 12, 0, 0);
            $ticket = Ticket::create([
                'workspace_id' => $this->workspace->id,
                'project_id' => $this->project->id,
                'category_id' => $this->category->id,
                'raised_by_user_id' => $this->user->id,
                'ticket_number' => "TCK-SEP-{$i}",
                'subject' => "Past Ticket #{$i}",
                'description' => "Past ticket description #{$i}",
                'priority' => 'normal',
                'status' => 'resolved',
                'resolved_at' => $created->copy()->addHours(6),
            ]);
            $ticket->forceFill(['created_at' => $created])->saveQuietly();

            $task = Task::create([
                'task_list_id' => $list->id,
                'status_id' => $doneStatus->id,
                'created_by_id' => $this->user->id,
                'title' => "Past Task #{$i}",
            ]);
            $task->forceFill(['created_at' => $created, 'updated_at' => $created->copy()->addDay()])->saveQuietly();
        }

        // Present Data: October 2026 (Current)
        for ($i = 1; $i <= 4; $i++) {
            $created = Carbon::create(2026, 10, $i * 2, 10, 0, 0);
            $ticket = Ticket::create([
                'workspace_id' => $this->workspace->id,
                'project_id' => $this->project->id,
                'category_id' => $this->category->id,
                'raised_by_user_id' => $this->user->id,
                'ticket_number' => "TCK-OCT-{$i}",
                'subject' => "Present Ticket #{$i}",
                'description' => "Present ticket description #{$i}",
                'priority' => 'urgent',
                'status' => 'open',
            ]);
            $ticket->forceFill(['created_at' => $created])->saveQuietly();

            $task = Task::create([
                'task_list_id' => $list->id,
                'status_id' => $todoStatus->id,
                'created_by_id' => $this->user->id,
                'title' => "Present Task #{$i}",
            ]);
            $task->forceFill(['created_at' => $created])->saveQuietly();
        }
    }

    public function test_dashboard_renders_with_populated_past_and_present_chart_data(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 10, 9, 10, 0, 0));

        Livewire::actingAs($this->user)
            ->test(Dashboard::class, ['workspace' => $this->workspace])
            ->assertSee('Tickets vs Tasks Inflow & Velocity Trend')
            ->assertSee('Tickets by Category Breakdown')
            ->assertSee('Project Workload vs Ticket Influx Comparison')
            ->assertSeeHtml('Tickets') // Center doughnut stat badge
            ->assertViewHas('chartLabels', function ($labels) {
                return count($labels) > 0;
            })
            ->assertViewHas('chartTicketsCreated', function ($data) {
                return array_sum($data) === 4; // 4 tickets in October
            })
            ->assertViewHas('chartCategoryData', function ($data) {
                return array_sum($data) === 4;
            });
    }

    public function test_interactive_time_range_and_filter_switching(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 10, 9, 10, 0, 0));

        Livewire::actingAs($this->user)
            ->test(Dashboard::class, ['workspace' => $this->workspace])
            // Test switching to Last Month (September)
            ->call('setTimeRange', 'last_month')
            ->assertSet('timeRange', 'last_month')
            ->assertViewHas('chartTicketsCreated', function ($data) {
                return array_sum($data) === 5; // 5 tickets in September
            })
            // Test switching to Year-Wise
            ->call('setTimeRange', 'year')
            ->assertSet('timeRange', 'year')
            ->assertViewHas('chartLabels', function ($labels) {
                return in_array('Sep 2026', $labels) && in_array('Oct 2026', $labels);
            })
            // Test toggling between Line and Bar chart styles
            ->call('setChartType', 'bar')
            ->assertSet('chartType', 'bar')
            ->call('setChartType', 'line')
            ->assertSet('chartType', 'line');
    }
}
