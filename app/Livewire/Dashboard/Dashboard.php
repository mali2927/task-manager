<?php

namespace App\Livewire\Dashboard;

use App\Models\Space;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\Ticket;
use App\Models\TicketActivityLog;
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
    public string $activeTab = 'overview'; // overview, tasks, tickets
    public string $activityFilter = 'all'; // all, tasks, tickets

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

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['overview', 'tasks', 'tickets'])) {
            $this->activeTab = $tab;
        }
    }

    public function setActivityFilter(string $filter): void
    {
        if (in_array($filter, ['all', 'tasks', 'tickets'])) {
            $this->activityFilter = $filter;
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

    public function exportTicketsCsv()
    {
        return redirect()->route('workspace.tickets.export', ['workspace' => $this->workspace->slug]);
    }

    public function render()
    {
        $user = Auth::user();

        // 1. Dynamic Greeting
        $hour = (int) now()->format('H');
        $greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };

        // 2. Tasks in workspace query
        $allTasksQuery = Task::whereHas('taskList.project.space', function ($q) {
            $q->where('workspace_id', $this->workspace->id);
            if ($this->selectedSpaceId) {
                $q->where('spaces.id', $this->selectedSpaceId);
            }
        })->with(['status', 'assignees', 'taskList.project.space']);

        $tasks = $allTasksQuery->get();

        // Personal Task KPIs
        $myTasks = $tasks->filter(fn ($t) => $t->assignees->contains('id', $user->id));
        $myOpen = $myTasks->filter(fn ($t) => !$t->isDone());
        $myOverdue = $myOpen->filter(fn ($t) => $t->isOverdue());
        $myDueToday = $myOpen->filter(fn ($t) => $t->isDueToday());
        $myDueThisWeek = $myOpen->filter(function ($t) {
            if (!$t->due_date) return false;
            return $t->due_date->between(now()->startOfWeek(), now()->endOfWeek()) && !$t->isDone();
        });

        // Team / Sprint Task Delivery Metrics
        $totalWorkspaceTasks = $tasks->count();
        $completedWorkspaceTasks = $tasks->filter(fn ($t) => $t->isDone())->count();
        $completionRate = $totalWorkspaceTasks > 0 ? (int) round(($completedWorkspaceTasks / $totalWorkspaceTasks) * 100) : 0;
        $totalOverdue = $tasks->filter(fn ($t) => $t->isOverdue())->count();
        $totalBlocked = $tasks->filter(fn ($t) => $t->status?->type === 'blocked')->count();

        // Task Status Breakdown
        $statusCounts = $tasks->groupBy(fn ($t) => $t->status?->name ?? 'Other')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'color' => $group->first()->status?->color ?? '#94a3b8',
                'type' => $group->first()->status?->type ?? 'todo',
            ]);

        // Overdue & Blocked Critical Tasks (for Attention Center)
        $criticalTasks = $tasks->filter(fn ($t) => ($t->isOverdue() || $t->status?->type === 'blocked') && !$t->isDone())->take(6);

        // 3. Support Tickets Metrics
        $workspaceTickets = Ticket::where('workspace_id', $this->workspace->id)->with(['category', 'raisedBy', 'assignedTo'])->get();
        $ticketsOpen = $workspaceTickets->where('status', 'open')->count();
        $ticketsInProgress = $workspaceTickets->where('status', 'in_progress')->count();
        $ticketsWaitingClient = $workspaceTickets->where('status', 'waiting_on_client')->count();
        $ticketsResolvedCount = $workspaceTickets->whereIn('status', ['resolved', 'closed'])->count();
        $ticketsOverdue = $workspaceTickets->filter(fn ($t) => $t->isOverdue())->count();
        $ticketsAssignedToMe = $workspaceTickets->where('assigned_to_user_id', $user->id)
            ->filter(fn ($t) => !in_array($t->status, ['resolved', 'closed']))
            ->count();

        $totalTicketsCount = $workspaceTickets->count();
        $slaComplianceRate = $totalTicketsCount > 0 
            ? (int) round((($totalTicketsCount - $ticketsOverdue) / $totalTicketsCount) * 100) 
            : 100;

        $resolvedTickets = $workspaceTickets->filter(fn ($t) => $t->resolved_at !== null);
        $avgResolutionTime = 'N/A';
        if ($resolvedTickets->count() > 0) {
            $totalMinutes = $resolvedTickets->sum(fn ($t) => $t->created_at->diffInMinutes($t->resolved_at));
            $avgMinutes = $totalMinutes / $resolvedTickets->count();
            if ($avgMinutes < 60) {
                $avgResolutionTime = round($avgMinutes) . 'm';
            } elseif ($avgMinutes < 1440) {
                $avgResolutionTime = round($avgMinutes / 60, 1) . 'h';
            } else {
                $avgResolutionTime = round($avgMinutes / 1440, 1) . 'd';
            }
        }

        // Ticket Priority Breakdown
        $ticketPriorityCounts = [
            'urgent' => [
                'count' => $workspaceTickets->where('priority', 'urgent')->whereNotIn('status', ['resolved', 'closed'])->count(),
                'color' => '#f43f5e',
                'label' => 'Urgent (4h SLA)',
            ],
            'high' => [
                'count' => $workspaceTickets->where('priority', 'high')->whereNotIn('status', ['resolved', 'closed'])->count(),
                'color' => '#f97316',
                'label' => 'High (24h SLA)',
            ],
            'normal' => [
                'count' => $workspaceTickets->where('priority', 'normal')->whereNotIn('status', ['resolved', 'closed'])->count(),
                'color' => '#3b82f6',
                'label' => 'Normal (3d SLA)',
            ],
            'low' => [
                'count' => $workspaceTickets->where('priority', 'low')->whereNotIn('status', ['resolved', 'closed'])->count(),
                'color' => '#10b981',
                'label' => 'Low (5d SLA)',
            ],
        ];

        // Urgent / Overdue Tickets (for Attention Center)
        $urgentTickets = $workspaceTickets->filter(function ($t) {
            return !in_array($t->status, ['resolved', 'closed']) && ($t->priority === 'urgent' || $t->isOverdue());
        })->take(6);

        // 4. Spaces & Projects Health Matrix
        $spacesProgress = $this->workspace->spaces()->with(['projects.lists.tasks.status'])->get()->map(function ($s) {
            $spaceTasks = $s->projects->flatMap->lists->flatMap->tasks;
            $tot = $spaceTasks->count();
            $don = $spaceTasks->filter(fn ($t) => $t->isDone())->count();
            $pct = $tot > 0 ? (int) round(($don / $tot) * 100) : 0;
            return [
                'id' => $s->id,
                'name' => $s->name,
                'color' => $s->color ?: '#6366f1',
                'icon' => $s->icon ?: 'folder',
                'total_tasks' => $tot,
                'done_tasks' => $don,
                'done' => $don,
                'pct' => $pct,
                'completion_pct' => $pct,
                'projects_count' => $s->projects->count(),
            ];
        });

        // 5. Workload & Capacity Heatmap (Combined Tasks + Support Tickets)
        $members = $this->workspace->members()->withCount([
            'assignedTasks' => function ($q) {
                $q->whereHas('taskList.project.space', fn ($sq) => $sq->where('workspace_id', $this->workspace->id))
                  ->whereNull('parent_id')
                  ->whereHas('status', fn ($sq) => $sq->where('type', '!=', 'done'));
            },
            'assignedTickets' => function ($q) {
                $q->where('workspace_id', $this->workspace->id)
                  ->whereNotIn('status', ['resolved', 'closed']);
            }
        ])->get()->map(function ($m) {
            $activeTasks = $m->assigned_tasks_count;
            $activeTickets = $m->assigned_tickets_count;
            $capacityLimit = (int) ($m->pivot->capacity_limit ?: 5);
            $ticketLoadPct = $capacityLimit > 0 ? (int) round(($activeTickets / $capacityLimit) * 100) : 0;
            
            $statusLabel = match (true) {
                $activeTickets > $capacityLimit => 'Overloaded',
                $activeTickets === $capacityLimit => 'At Capacity',
                $ticketLoadPct >= 80 => 'Near Capacity',
                default => 'Available',
            };

            $statusColor = match ($statusLabel) {
                'Overloaded' => 'rose',
                'At Capacity' => 'amber',
                'Near Capacity' => 'yellow',
                default => 'emerald',
            };

            return [
                'id' => $m->id,
                'name' => $m->name,
                'email' => $m->email,
                'avatar' => $m->avatar(),
                'job_title' => $m->pivot->job_title ?? $m->job_title ?? 'Team Member',
                'active_tasks' => $activeTasks,
                'active_tickets' => $activeTickets,
                'capacity_limit' => $capacityLimit,
                'ticket_load_pct' => $ticketLoadPct,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
            ];
        })->sortByDesc('active_tickets')->values();

        // 6. Unified Live Activity Stream
        $taskActivities = collect();
        if (in_array($this->activityFilter, ['all', 'tasks'])) {
            $taskActivities = TaskActivity::whereHas('task.taskList.project.space', function ($q) {
                $q->where('workspace_id', $this->workspace->id);
            })->with(['user', 'task'])->latest()->take(12)->get()->map(fn ($a) => [
                'type' => 'task',
                'id' => $a->id,
                'title' => $a->task?->title ?? 'Task',
                'link_id' => $a->task_id,
                'user' => $a->user,
                'description' => $a->description,
                'action' => $a->action,
                'created_at' => $a->created_at,
            ]);
        }

        $ticketActivities = collect();
        if (in_array($this->activityFilter, ['all', 'tickets'])) {
            $ticketActivities = TicketActivityLog::whereHas('ticket', function ($q) {
                $q->where('workspace_id', $this->workspace->id);
            })->with(['user', 'ticket'])->latest()->take(12)->get()->map(fn ($a) => [
                'type' => 'ticket',
                'id' => $a->id,
                'title' => $a->ticket ? ($a->ticket->ticket_number . ' - ' . $a->ticket->title) : 'Ticket',
                'link_id' => $a->ticket_id,
                'user' => $a->user,
                'description' => $a->description,
                'action' => $a->action,
                'created_at' => $a->created_at,
            ]);
        }

        $recentActivities = $taskActivities->concat($ticketActivities)
            ->sortByDesc('created_at')
            ->take(12)
            ->values();

        // Total attention items
        $totalAttentionItems = $totalOverdue + $ticketsOverdue + $totalBlocked;

        return view('livewire.dashboard.dashboard', [
            'greeting' => $greeting,
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
            'criticalTasks' => $criticalTasks,
            'recentActivities' => $recentActivities,
            'spaces' => $this->workspace->spaces,
            'spacesProgress' => $spacesProgress,
            'ticketsOpen' => $ticketsOpen,
            'ticketsInProgress' => $ticketsInProgress,
            'ticketsWaitingClient' => $ticketsWaitingClient,
            'ticketsResolvedCount' => $ticketsResolvedCount,
            'ticketsOverdue' => $ticketsOverdue,
            'ticketsAssignedToMe' => $ticketsAssignedToMe,
            'totalTicketsCount' => $totalTicketsCount,
            'slaComplianceRate' => $slaComplianceRate,
            'avgResolutionTime' => $avgResolutionTime,
            'ticketPriorityCounts' => $ticketPriorityCounts,
            'urgentTickets' => $urgentTickets,
            'totalAttentionItems' => $totalAttentionItems,
        ]);
    }
}
