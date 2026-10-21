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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ModuleSeparationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workspace $workspace;
    protected Space $space;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['name' => 'Module Admin']);
        $this->workspace = Workspace::create([
            'name' => 'Separation Test Hub',
            'slug' => 'sep-hub',
            'owner_id' => $this->user->id,
        ]);
        $this->workspace->members()->attach($this->user->id, ['role' => 'admin']);

        TaskStatus::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'To Do',
            'type' => 'todo',
            'is_default' => true,
        ]);

        $this->space = Space::create([
            'workspace_id' => $this->workspace->id,
            'name' => 'Dev Space',
            'color' => '#6366f1',
        ]);

        $this->project = Project::create([
            'space_id' => $this->space->id,
            'name' => 'Core Engine',
            'color' => '#8b5cf6',
        ]);
    }

    public function test_dashboard_renders_tasks_module_when_explicitly_selected(): void
    {
        Livewire::actingAs($this->user)
            ->withQueryParams(['module' => 'tasks'])
            ->test(Dashboard::class, ['workspace' => $this->workspace])
            ->assertSet('module', 'tasks')
            ->assertSee('Task &amp; Sprint Dashboard', false)
            ->assertSee('Sprint Delivery Velocity &amp; Burnup Curves', false)
            ->assertSee('Sprint &amp; Task Execution Operations', false)
            ->assertSee('Spaces &amp; Projects Delivery Matrix', false)
            ->assertDontSee('Interactive Influx &amp; Workload Analytics', false);
    }

    public function test_dashboard_renders_helpdesk_module_when_explicitly_selected(): void
    {
        Livewire::actingAs($this->user)
            ->withQueryParams(['module' => 'helpdesk'])
            ->test(Dashboard::class, ['workspace' => $this->workspace])
            ->assertSet('module', 'helpdesk')
            ->assertSee('Helpdesk &amp; Service Desk', false)
            ->assertSee('IT Helpdesk &amp; SLA Service Desk', false)
            ->assertSee('Interactive Influx &amp; Workload Analytics', false)
            ->assertSee('Tickets vs Tasks Inflow &amp; Velocity Trend', false)
            ->assertDontSee('Sprint Delivery Velocity &amp; Burnup Curves', false);
    }

    public function test_set_module_method_toggles_mode_and_persists_in_session(): void
    {
        Livewire::actingAs($this->user)
            ->test(Dashboard::class, ['workspace' => $this->workspace])
            ->call('setModule', 'tasks')
            ->assertSet('module', 'tasks')
            ->assertSessionHas('active_module', 'tasks')
            ->call('setModule', 'helpdesk')
            ->assertSet('module', 'helpdesk')
            ->assertSessionHas('active_module', 'helpdesk');
    }

    public function test_switch_module_route_persists_session_and_redirects(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('module.switch', ['module' => 'tasks']));
        $response->assertRedirect(route('dashboard', ['module' => 'tasks']));
        $this->assertEquals('tasks', session('active_module'));

        $response = $this->get(route('module.switch', ['module' => 'helpdesk']));
        $response->assertRedirect(route('dashboard', ['module' => 'helpdesk']));
        $this->assertEquals('helpdesk', session('active_module'));
    }

    public function test_requester_user_is_redirected_to_requester_portal_and_cannot_access_tasks(): void
    {
        $requester = User::factory()->create();
        $this->workspace->members()->attach($requester->id, ['role' => 'requester']);

        $response = $this->actingAs($requester)->get(route('dashboard', ['module' => 'tasks']));
        $response->assertRedirect(route('workspace.tickets.my', ['workspace' => $this->workspace->slug]));
    }
}
