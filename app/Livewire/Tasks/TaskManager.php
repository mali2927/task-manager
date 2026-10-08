<?php

namespace App\Livewire\Tasks;

use App\Models\Project;
use App\Models\Space;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\TaskList;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\Workspace;
use App\Services\GeminiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class TaskManager extends Component
{
    public Workspace $workspace;

    #[Url(as: 'view')]
    public string $activeView = 'board'; // 'list', 'board', 'calendar', 'gantt'

    #[Url(as: 'space')]
    public ?int $selectedSpaceId = null;

    #[Url(as: 'project')]
    public ?int $selectedProjectId = null;

    #[Url(as: 'list')]
    public ?int $selectedListId = null;

    #[Url(as: 'search')]
    public string $searchQuery = '';

    #[Url(as: 'priority')]
    public string $filterPriority = '';

    #[Url(as: 'assignee')]
    public string $filterAssignee = '';

    // Quick Task Modal
    public bool $showCreateModal = false;
    public string $newTaskTitle = '';
    public string $newTaskDescription = '';
    public ?int $newTaskListId = null;
    public ?int $newTaskStatusId = null;
    public string $newTaskPriority = 'normal';
    public ?string $newTaskDueDate = null;
    public array $newTaskAssigneeIds = [];

    // AI Suggestions in Create modal
    public bool $isSuggesting = false;
    public ?string $aiSuggestedPriority = null;

    // Calendar
    public string $currentMonth;

    public function mount(?string $view = null): void
    {
        if (isset($this->workspace) && Auth::user()?->isWorkspaceRequester($this->workspace)) {
            $this->redirect(route('workspace.tickets.my', ['workspace' => $this->workspace->slug]), navigate: true);
            return;
        }

        if ($view && in_array($view, ['list', 'board', 'calendar', 'gantt'])) {
            $this->activeView = $view;
        }

        $this->currentMonth = now()->format('Y-m');
    }

    public function setView(string $view): void
    {
        if (in_array($view, ['list', 'board', 'calendar', 'gantt'])) {
            $this->activeView = $view;
        }
    }

    public function selectSpace(?int $spaceId): void
    {
        $this->selectedSpaceId = $spaceId;
        $this->selectedProjectId = null;
        $this->selectedListId = null;
    }

    public function selectProject(?int $projectId): void
    {
        $this->selectedProjectId = $projectId;
        $this->selectedListId = null;
    }

    public function selectList(?int $listId): void
    {
        $this->selectedListId = $listId;
    }

    public function resetFilters(): void
    {
        $this->searchQuery = '';
        $this->filterPriority = '';
        $this->filterAssignee = '';
        $this->selectedSpaceId = null;
        $this->selectedProjectId = null;
        $this->selectedListId = null;
    }

    public function openCreateModal(?int $statusId = null, ?int $listId = null): void
    {
        $this->resetCreateForm();

        // Pick default list
        if ($listId) {
            $this->newTaskListId = $listId;
        } else {
            $firstList = TaskList::whereHas('project.space', fn ($q) => $q->where('workspace_id', $this->workspace->id))->first();
            $this->newTaskListId = $firstList?->id;
        }

        // Pick default status
        if ($statusId) {
            $this->newTaskStatusId = $statusId;
        } else {
            $defStatus = $this->workspace->taskStatuses()->where('is_default', true)->first()
                ?? $this->workspace->taskStatuses()->first();
            $this->newTaskStatusId = $defStatus?->id;
        }

        $this->showCreateModal = true;
    }

    public function resetCreateForm(): void
    {
        $this->newTaskTitle = '';
        $this->newTaskDescription = '';
        $this->newTaskPriority = 'normal';
        $this->newTaskDueDate = null;
        $this->newTaskAssigneeIds = [];
        $this->aiSuggestedPriority = null;
    }

    public function askAiForSuggestions(GeminiService $gemini): void
    {
        if (empty(trim($this->newTaskTitle))) return;

        $this->isSuggesting = true;
        try {
            $res = $gemini->suggestAttributes($this->newTaskTitle, $this->newTaskDescription);
            if (!empty($res['priority'])) {
                $this->newTaskPriority = $res['priority'];
                $this->aiSuggestedPriority = $res['priority'];
            }
        } catch (\Throwable $e) {
            // silent fallback
        } finally {
            $this->isSuggesting = false;
        }
    }

    public function createTask(): void
    {
        $this->validate([
            'newTaskTitle' => 'required|string|max:255',
            'newTaskListId' => 'required|exists:task_lists,id',
            'newTaskStatusId' => 'required|exists:task_statuses,id',
        ]);

        $task = Task::create([
            'task_list_id' => $this->newTaskListId,
            'created_by_id' => Auth::id(),
            'title' => trim($this->newTaskTitle),
            'description' => trim($this->newTaskDescription) ?: null,
            'status_id' => $this->newTaskStatusId,
            'priority' => $this->newTaskPriority,
            'due_date' => $this->newTaskDueDate ?: null,
            'sort_order' => Task::where('task_list_id', $this->newTaskListId)->count() + 1,
        ]);

        if (!empty($this->newTaskAssigneeIds)) {
            $task->assignees()->sync($this->newTaskAssigneeIds);
        }

        TaskActivity::log($task, Auth::user(), 'created', "Task '{$task->title}' created");

        $this->showCreateModal = false;
        $this->resetCreateForm();
        $this->dispatch('task-created');
    }

    public function updateTaskStatus(int $taskId, int $newStatusId): void
    {
        $task = Task::find($taskId);
        if ($task) {
            $oldStatus = $task->status?->name;
            $task->update(['status_id' => $newStatusId]);
            $newStatus = TaskStatus::find($newStatusId)?->name;
            TaskActivity::log($task, Auth::user(), 'status_updated', "Moved to '{$newStatus}'", $oldStatus, $newStatus);
        }
    }

    #[On('task-updated')]
    #[On('task-created')]
    public function refreshTasks(): void
    {
        // re-renders automatically
    }

    public function nextMonth(): void
    {
        $this->currentMonth = Carbon::parse($this->currentMonth . '-01')->addMonth()->format('Y-m');
    }

    public function prevMonth(): void
    {
        $this->currentMonth = Carbon::parse($this->currentMonth . '-01')->subMonth()->format('Y-m');
    }

    public function render()
    {
        // Base task query
        $query = Task::whereHas('taskList.project.space', function ($q) {
            $q->where('workspace_id', $this->workspace->id);
        })
        ->whereNull('parent_id') // Top-level tasks
        ->with([
            'status',
            'assignees',
            'checklists',
            'tags',
            'taskList.project.space',
        ]);

        // Space / Project / List filters
        if ($this->selectedListId) {
            $query->where('task_list_id', $this->selectedListId);
        } elseif ($this->selectedProjectId) {
            $query->whereHas('taskList', fn ($q) => $q->where('project_id', $this->selectedProjectId));
        } elseif ($this->selectedSpaceId) {
            $query->whereHas('taskList.project', fn ($q) => $q->where('space_id', $this->selectedSpaceId));
        }

        // Search query
        if (!empty(trim($this->searchQuery))) {
            $search = '%' . trim($this->searchQuery) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        // Priority filter
        if (!empty($this->filterPriority)) {
            $query->where('priority', $this->filterPriority);
        }

        // Assignee filter
        if ($this->filterAssignee === 'unassigned') {
            $query->doesntHave('assignees');
        } elseif (!empty($this->filterAssignee)) {
            $query->whereHas('assignees', fn ($q) => $q->where('users.id', (int) $this->filterAssignee));
        }

        $tasks = $query->orderBy('sort_order')->get();

        // Statuses
        $statuses = $this->workspace->taskStatuses()->orderBy('sort_order')->get();

        // Spaces tree
        $spaces = $this->workspace->spaces()->with(['projects.lists'])->get();

        // Workspace members
        $members = $this->workspace->members;

        // Calendar generation if view is calendar
        $calendarDays = [];
        if ($this->activeView === 'calendar') {
            $startOfMonth = Carbon::parse($this->currentMonth . '-01')->startOfMonth();
            $endOfMonth = Carbon::parse($this->currentMonth . '-01')->endOfMonth();
            $startGrid = $startOfMonth->copy()->startOfWeek();
            $endGrid = $endOfMonth->copy()->endOfWeek();

            $current = $startGrid->copy();
            while ($current->lte($endGrid)) {
                $dateStr = $current->format('Y-m-d');
                $dayTasks = $tasks->filter(fn ($t) => $t->due_date?->format('Y-m-d') === $dateStr);

                $calendarDays[] = [
                    'date' => $current->copy(),
                    'date_str' => $dateStr,
                    'is_current_month' => $current->month === $startOfMonth->month,
                    'is_today' => $current->isToday(),
                    'tasks' => $dayTasks,
                ];
                $current->addDay();
            }
        }

        return view('livewire.tasks.task-manager', [
            'tasks' => $tasks,
            'statuses' => $statuses,
            'spaces' => $spaces,
            'members' => $members,
            'calendarDays' => $calendarDays,
        ]);
    }
}
