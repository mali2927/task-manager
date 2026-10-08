<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Tasks\MyTasks;
use App\Livewire\Tasks\TaskManager;
use App\Models\Project;
use App\Models\Space;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskManagerLivewireTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_manager_renders_board_view(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::create(['name' => 'Demo', 'owner_id' => $user->id]);
        $workspace->members()->attach($user->id, ['role' => 'owner']);
        $space = Space::create(['workspace_id' => $workspace->id, 'name' => 'Engineering']);
        $project = Project::create(['space_id' => $space->id, 'name' => 'Web App']);
        $list = TaskList::create(['project_id' => $project->id, 'name' => 'Backlog']);
        $status = TaskStatus::create(['workspace_id' => $workspace->id, 'name' => 'To Do', 'type' => 'todo', 'is_default' => true]);

        $task = Task::create([
            'task_list_id' => $list->id,
            'created_by_id' => $user->id,
            'title' => 'Build Dashboard Metrics',
            'status_id' => $status->id,
            'priority' => 'urgent',
        ]);

        $this->actingAs($user);

        Livewire::test(TaskManager::class, ['workspace' => $workspace])
            ->assertSet('activeView', 'board')
            ->assertSee('Build Dashboard Metrics')
            ->call('setView', 'list')
            ->assertSet('activeView', 'list')
            ->assertSee('Build Dashboard Metrics');
    }

    public function test_dashboard_and_my_tasks_render_successfully(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::create(['name' => 'Demo', 'owner_id' => $user->id]);
        $workspace->members()->attach($user->id, ['role' => 'owner']);

        $this->actingAs($user);

        Livewire::test(Dashboard::class, ['workspace' => $workspace])
            ->assertSee('Overview')
            ->assertSee('My Pending Tasks');

        Livewire::test(MyTasks::class, ['workspace' => $workspace])
            ->assertSee('My Assigned Tasks');
    }
}
