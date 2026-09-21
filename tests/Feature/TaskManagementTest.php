<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Space;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\TaskComment;
use App\Models\TaskList;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task_with_checklists_and_comments(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::create(['name' => 'Demo Workspace', 'owner_id' => $user->id]);
        $space = Space::create(['workspace_id' => $workspace->id, 'name' => 'General']);
        $project = Project::create(['space_id' => $space->id, 'name' => 'Product v1']);
        $list = TaskList::create(['project_id' => $project->id, 'name' => 'Sprint 1']);
        $status = TaskStatus::create([
            'workspace_id' => $workspace->id,
            'name' => 'In Progress',
            'type' => 'in_progress',
        ]);

        $task = Task::create([
            'task_list_id' => $list->id,
            'created_by_id' => $user->id,
            'title' => 'Write end-to-end tests',
            'description' => 'Cover all core user journeys.',
            'status_id' => $status->id,
            'priority' => 'high',
        ]);

        $task->assignees()->attach($user->id);

        $chk1 = TaskChecklist::create(['task_id' => $task->id, 'title' => 'Test auth', 'is_completed' => true]);
        $chk2 = TaskChecklist::create(['task_id' => $task->id, 'title' => 'Test task CRUD', 'is_completed' => false]);

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'content' => 'Auth tests are already passing.',
        ]);

        $this->assertDatabaseHas('tasks', ['title' => 'Write end-to-end tests', 'priority' => 'high']);
        $this->assertEquals(2, $task->checklists()->count());
        $this->assertEquals(50, $task->checklistStats()['percent']);
        $this->assertEquals(1, $task->comments()->count());
        $this->assertTrue($task->assignees->contains('id', $user->id));
    }

    public function test_task_status_can_be_updated_and_detects_done(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::create(['name' => 'Demo Workspace', 'owner_id' => $user->id]);
        $space = Space::create(['workspace_id' => $workspace->id, 'name' => 'General']);
        $project = Project::create(['space_id' => $space->id, 'name' => 'Product v1']);
        $list = TaskList::create(['project_id' => $project->id, 'name' => 'Sprint 1']);
        
        $todo = TaskStatus::create(['workspace_id' => $workspace->id, 'name' => 'To Do', 'type' => 'todo']);
        $done = TaskStatus::create(['workspace_id' => $workspace->id, 'name' => 'Done', 'type' => 'done']);

        $task = Task::create([
            'task_list_id' => $list->id,
            'created_by_id' => $user->id,
            'title' => 'Sample Task',
            'status_id' => $todo->id,
        ]);

        $this->assertFalse($task->isDone());

        $task->update(['status_id' => $done->id]);
        $task->refresh();

        $this->assertTrue($task->isDone());
    }
}
