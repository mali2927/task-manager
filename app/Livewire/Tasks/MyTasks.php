<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\TaskStatus;
use App\Models\Workspace;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MyTasks extends Component
{
    public Workspace $workspace;
    public ?string $aiDigest = null;
    public bool $isGeneratingDigest = false;

    public function mount(?Workspace $workspace = null): void
    {
        if (!$workspace || !$workspace->id) {
            $this->workspace = Auth::user()->workspaces()->first() ?? Workspace::first();
        } else {
            $this->workspace = $workspace;
        }

        if ($this->workspace && Auth::user()?->isWorkspaceRequester($this->workspace)) {
            $this->redirect(route('workspace.tickets.my', ['workspace' => $this->workspace->slug]), navigate: true);
            return;
        }
    }

    public function generateAiDigest(GeminiService $gemini): void
    {
        $this->isGeneratingDigest = true;
        try {
            $this->aiDigest = $gemini->generateUserDigest(Auth::user(), $this->workspace);
        } catch (\Throwable $e) {
            $this->aiDigest = "Error generating digest: " . $e->getMessage();
        } finally {
            $this->isGeneratingDigest = false;
        }
    }

    public function toggleComplete(int $taskId): void
    {
        $task = Task::find($taskId);
        if (!$task) return;

        $doneStatus = $this->workspace->taskStatuses()->where('type', 'done')->first();
        $todoStatus = $this->workspace->taskStatuses()->where('is_default', true)->first()
            ?? $this->workspace->taskStatuses()->first();

        if ($task->isDone()) {
            $task->update(['status_id' => $todoStatus->id]);
            TaskActivity::log($task, Auth::user(), 'status_updated', "Reopened task");
        } else {
            if ($doneStatus) {
                $task->update(['status_id' => $doneStatus->id]);
                TaskActivity::log($task, Auth::user(), 'status_updated', "Marked as completed");
            }
        }
    }

    public function render()
    {
        $user = Auth::user();

        $tasks = Task::whereHas('taskList.project.space', fn ($q) => $q->where('workspace_id', $this->workspace->id))
            ->whereHas('assignees', fn ($q) => $q->where('users.id', $user->id))
            ->whereNull('parent_id')
            ->with(['status', 'taskList.project.space', 'tags', 'checklists'])
            ->get();

        $overdue = $tasks->filter(fn ($t) => $t->isOverdue());
        $dueToday = $tasks->filter(fn ($t) => $t->isDueToday());
        $upcoming = $tasks->filter(fn ($t) => !$t->isDone() && !$t->isOverdue() && !$t->isDueToday());
        $completed = $tasks->filter(fn ($t) => $t->isDone());

        return view('livewire.tasks.my-tasks', [
            'overdue' => $overdue,
            'dueToday' => $dueToday,
            'upcoming' => $upcoming,
            'completed' => $completed,
        ]);
    }
}
