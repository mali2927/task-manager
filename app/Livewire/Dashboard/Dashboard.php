<?php

namespace App\Livewire\Dashboard;

use App\Models\Space;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\Workspace;
use App\Services\GeminiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public ?Workspace $workspace = null;
    public ?int $selectedSpaceId = null;

    // AI summary states
    public ?string $teamAiSummary = null;
    public ?string $myStandupReport = null;
    public bool $isGeneratingTeamSummary = false;
    public bool $isGeneratingStandup = false;

    public function mount(?Workspace $workspace = null): void
    {
        $user = Auth::user();
        if ($workspace && $workspace->id) {
            $this->workspace = $workspace;
        } else {
            $this->workspace = $user?->workspaces()->first() ?? Workspace::first();
            if (!$this->workspace && $user) {
                $this->workspace = Workspace::create([
                    'name' => $user->name . "'s Workspace",
                    'owner_id' => $user->id,
                ]);
                $this->workspace->members()->attach($user->id, ['role' => 'owner']);
                $this->workspace->taskStatuses()->create([
                    'name' => 'To Do', 'color' => '#94a3b8', 'type' => 'todo', 'sort_order' => 1, 'is_default' => true,
                ]);
                $this->workspace->taskStatuses()->create([
                    'name' => 'Done', 'color' => '#10b981', 'type' => 'done', 'sort_order' => 2,
                ]);
            }
        }
    }

    public function generateTeamAiSummary(GeminiService $gemini): void
    {
        $this->isGeneratingTeamSummary = true;
        try {
            $space = $this->selectedSpaceId ? Space::find($this->selectedSpaceId) : null;
            $this->teamAiSummary = $gemini->generateTeamSummary($this->workspace, $space);
        } catch (\Throwable $e) {
            $this->teamAiSummary = "Error generating team summary: " . $e->getMessage();
        } finally {
            $this->isGeneratingTeamSummary = false;
        }
    }

    public function generateStandup(GeminiService $gemini): void
    {
        $this->isGeneratingStandup = true;
        try {
            $this->myStandupReport = $gemini->generateStandupReport(Auth::user(), $this->workspace);
        } catch (\Throwable $e) {
            $this->myStandupReport = "Error generating standup report: " . $e->getMessage();
        } finally {
            $this->isGeneratingStandup = false;
        }
    }

    public function exportTasksCsv()
    {
        return redirect()->route('workspace.export', ['workspace' => $this->workspace->slug]);
    }

    public function render()
    {
        $user = Auth::user();

        // Tasks in workspace query
        $allTasksQuery = Task::whereHas('taskList.project.space', function ($q) {
            $q->where('workspace_id', $this->workspace->id);
            if ($this->selectedSpaceId) {
                $q->where('spaces.id', $this->selectedSpaceId);
            }
        })->with(['status', 'assignees', 'taskList.project.space']);

        $tasks = $allTasksQuery->get();

        // 1. User Personal KPI stats
        $myTasks = $tasks->filter(fn ($t) => $t->assignees->contains('id', $user->id));
        $myOpen = $myTasks->filter(fn ($t) => !$t->isDone());
        $myOverdue = $myOpen->filter(fn ($t) => $t->isOverdue());
        $myDueToday = $myOpen->filter(fn ($t) => $t->isDueToday());
        $myDueThisWeek = $myOpen->filter(function ($t) {
            if (!$t->due_date) return false;
            return $t->due_date->between(now()->startOfWeek(), now()->endOfWeek()) && !$t->isDone();
        });

        // 2. Team Summary metrics
        $totalWorkspaceTasks = $tasks->count();
        $completedWorkspaceTasks = $tasks->filter(fn ($t) => $t->isDone())->count();
        $completionRate = $totalWorkspaceTasks > 0 ? (int) round(($completedWorkspaceTasks / $totalWorkspaceTasks) * 100) : 0;
        $totalOverdue = $tasks->filter(fn ($t) => $t->isOverdue())->count();
        $totalBlocked = $tasks->filter(fn ($t) => $t->status?->type === 'blocked')->count();

        // Status breakdown
        $statusCounts = $tasks->groupBy(fn ($t) => $t->status?->name ?? 'Other')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'color' => $group->first()->status?->color ?? '#94a3b8',
                'type' => $group->first()->status?->type ?? 'todo',
            ]);

        // 3. Workload View (Tasks per assignee)
        $members = $this->workspace->members()->withCount(['assignedTasks' => function ($q) {
            $q->whereHas('taskList.project.space', fn ($sq) => $sq->where('workspace_id', $this->workspace->id))
              ->whereNull('parent_id')
              ->whereHas('status', fn ($sq) => $sq->where('type', '!=', 'done'));
        }])->get();

        // 4. Overdue Tasks list
        $overdueList = $tasks->filter(fn ($t) => $t->isOverdue())->take(6);

        // 5. Recent Activity Feed
        $recentActivities = TaskActivity::whereHas('task.taskList.project.space', function ($q) {
            $q->where('workspace_id', $this->workspace->id);
        })->with(['user', 'task'])->latest()->take(10)->get();

        $spaces = $this->workspace->spaces;

        return view('livewire.dashboard.dashboard', [
            'myOpen' => $myOpen->count(),
            'myOverdue' => $myOverdue->count(),
            'myDueToday' => $myDueToday->count(),
            'myDueThisWeek' => $myDueThisWeek->count(),
            'totalTasks' => $totalWorkspaceTasks,
            'completedTasks' => $completedWorkspaceTasks,
            'completionRate' => $completionRate,
            'totalOverdue' => $totalOverdue,
            'totalBlocked' => $totalBlocked,
            'statusCounts' => $statusCounts,
            'members' => $members,
            'overdueList' => $overdueList,
            'recentActivities' => $recentActivities,
            'spaces' => $spaces,
        ]);
    }
}
