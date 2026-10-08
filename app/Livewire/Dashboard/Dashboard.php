<?php

namespace App\Livewire\Dashboard;

use App\Models\Project;
use App\Models\Space;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\Ticket;
use App\Models\TicketActivityLog;
use App\Models\TicketCategory;
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
    public string $activeTab = 'overview'; // overview, analytics, tasks, tickets
    public string $activityFilter = 'all'; // all, tasks, tickets

    // Interactive Time Filters & Comparison
    public string $timeRange = 'month'; // 'month', 'last_month', 'year', 'all'
    public int $selectedYear = 2026;
    public int $selectedMonth = 9;
    public string $chartType = 'line'; // 'line' or 'bar'
    public ?int $filterProjectId = null;

    // AI summary & analytics states
    public ?string $teamAiSummary = null;
    public ?string $myStandupReport = null;
    public bool $isGeneratingTeamSummary = false;
    public bool $isGeneratingStandup = false;

    // AI Deep Analytics & Trend Advisor
    public string $aiAnalyticsPrompt = '';
    public string $aiPromptQuery = '';
    public ?string $aiAnalyticsInsight = null;
    public bool $isAnalyzingTrends = false;

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

        if ($user && $this->workspace && $user->isWorkspaceRequester($this->workspace)) {
            $this->redirect(route('workspace.tickets.my', ['workspace' => $this->workspace->slug]), navigate: true);
            return;
        }

        $this->selectedYear = (int) now()->format('Y');
        $this->selectedMonth = (int) now()->format('n');
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['overview', 'analytics', 'tasks', 'tickets'])) {
            $this->activeTab = $tab;
        }
    }

    public function setActivityFilter(string $filter): void
    {
        if (in_array($filter, ['all', 'tasks', 'tickets'])) {
            $this->activityFilter = $filter;
        }
    }

    public function setTimeRange(string $range): void
    {
        if (in_array($range, ['month', 'last_month', 'year', 'all'])) {
            $this->timeRange = $range;
        }
    }

    public function setChartType(string $type): void
    {
        if (in_array($type, ['line', 'bar'])) {
            $this->chartType = $type;
        }
    }

    public function setFilterProject(?int $projectId): void
    {
        $this->filterProjectId = $projectId;
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

    public function generateAiAnalyticsInsight(GeminiService $gemini, ?string $quickQuery = null): void
    {
        $this->isAnalyzingTrends = true;
        try {
            $query = $quickQuery ?: (trim($this->aiPromptQuery ?: $this->aiAnalyticsPrompt) ?: null);
            $analyticsPayload = $this->buildAnalyticsPayloadForAi();
            $this->aiAnalyticsInsight = $gemini->generateAnalyticsInsights($this->workspace, $analyticsPayload, $query, Auth::user());
        } catch (\Throwable $e) {
            $this->aiAnalyticsInsight = "Error generating trend analytics: " . $e->getMessage();
        } finally {
            $this->isAnalyzingTrends = false;
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

    protected function getTimeBoundaries(): array
    {
        $now = now();
        $year = $this->selectedYear ?: (int) $now->format('Y');
        $month = $this->selectedMonth ?: (int) $now->format('n');

        return match ($this->timeRange) {
            'last_month' => [
                'current_start' => Carbon::create($year, $month, 1)->subMonth()->startOfMonth(),
                'current_end' => Carbon::create($year, $month, 1)->subMonth()->endOfMonth(),
                'prev_start' => Carbon::create($year, $month, 1)->subMonths(2)->startOfMonth(),
                'prev_end' => Carbon::create($year, $month, 1)->subMonths(2)->endOfMonth(),
                'label' => Carbon::create($year, $month, 1)->subMonth()->format('F Y'),
                'prev_label' => Carbon::create($year, $month, 1)->subMonths(2)->format('F Y'),
                'granularity' => 'day',
            ],
            'year' => [
                'current_start' => Carbon::create($year, 1, 1)->startOfYear(),
                'current_end' => Carbon::create($year, 12, 31)->endOfYear(),
                'prev_start' => Carbon::create($year - 1, 1, 1)->startOfYear(),
                'prev_end' => Carbon::create($year - 1, 12, 31)->endOfYear(),
                'label' => "Year {$year}",
                'prev_label' => "Year " . ($year - 1),
                'granularity' => 'month',
            ],
            'all' => [
                'current_start' => $now->copy()->subMonths(11)->startOfMonth(),
                'current_end' => $now->copy()->endOfMonth(),
                'prev_start' => $now->copy()->subMonths(23)->startOfMonth(),
                'prev_end' => $now->copy()->subMonths(12)->endOfMonth(),
                'label' => 'Trailing 12 Months',
                'prev_label' => 'Prior 12 Months',
                'granularity' => 'month',
            ],
            default => [ // 'month'
                'current_start' => Carbon::create($year, $month, 1)->startOfMonth(),
                'current_end' => Carbon::create($year, $month, 1)->endOfMonth(),
                'prev_start' => Carbon::create($year, $month, 1)->subMonth()->startOfMonth(),
                'prev_end' => Carbon::create($year, $month, 1)->subMonth()->endOfMonth(),
                'label' => Carbon::create($year, $month, 1)->format('F Y'),
                'prev_label' => Carbon::create($year, $month, 1)->subMonth()->format('F Y'),
                'granularity' => 'day',
            ],
        };
    }

    protected function computeDelta(int $current, int $previous): array
    {
        if ($previous === 0) {
            $pct = $current > 0 ? 100 : 0;
        } else {
            $pct = (int) round((($current - $previous) / $previous) * 100);
        }

        return [
            'current' => $current,
            'previous' => $previous,
            'delta' => $current - $previous,
            'pct' => $pct,
            'direction' => $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'flat'),
        ];
    }

    protected function buildAnalyticsPayloadForAi(): array
    {
        $boundaries = $this->getTimeBoundaries();
        $wsId = $this->workspace->id;

        $cStart = $boundaries['current_start'];
        $cEnd = $boundaries['current_end'];
        $pStart = $boundaries['prev_start'];
        $pEnd = $boundaries['prev_end'];

        $ticketsCurrent = Ticket::where('workspace_id', $wsId)->whereBetween('created_at', [$cStart, $cEnd])->count();
        $ticketsPrev = Ticket::where('workspace_id', $wsId)->whereBetween('created_at', [$pStart, $pEnd])->count();
        $ticketsResolvedCur = Ticket::where('workspace_id', $wsId)->whereBetween('resolved_at', [$cStart, $cEnd])->count();
        $ticketsResolvedPrev = Ticket::where('workspace_id', $wsId)->whereBetween('resolved_at', [$pStart, $pEnd])->count();

        $tasksCurrent = Task::whereHas('taskList.project.space', fn ($q) => $q->where('workspace_id', $wsId))->whereBetween('created_at', [$cStart, $cEnd])->count();
        $tasksPrev = Task::whereHas('taskList.project.space', fn ($q) => $q->where('workspace_id', $wsId))->whereBetween('created_at', [$pStart, $pEnd])->count();

        $projects = Project::whereHas('space', fn ($q) => $q->where('workspace_id', $wsId))->withCount([
            'tasks',
            'tickets as tickets_current' => fn ($q) => $q->whereBetween('created_at', [$cStart, $cEnd]),
            'tickets as tickets_prev' => fn ($q) => $q->whereBetween('created_at', [$pStart, $pEnd]),
        ])->get()->map(fn ($p) => [
            'project_name' => $p->name,
            'total_tasks' => $p->tasks_count,
            'tickets_current_period' => $p->tickets_current,
            'tickets_prev_period' => $p->tickets_prev,
        ])->toArray();

        $categories = TicketCategory::where('workspace_id', $wsId)->withCount([
            'tickets as current_period' => fn ($q) => $q->whereBetween('created_at', [$cStart, $cEnd]),
            'tickets as prev_period' => fn ($q) => $q->whereBetween('created_at', [$pStart, $pEnd]),
        ])->get()->map(fn ($c) => [
            'category_name' => $c->name,
            'current_period_count' => $c->current_period,
            'prev_period_count' => $c->prev_period,
        ])->toArray();

        return [
            'timeframe_label' => "{$boundaries['label']} (compared to {$boundaries['prev_label']})",
            'ticket_metrics' => [
                'created_current' => $ticketsCurrent,
                'created_previous' => $ticketsPrev,
                'resolved_current' => $ticketsResolvedCur,
                'resolved_previous' => $ticketsResolvedPrev,
            ],
            'task_metrics' => [
                'created_current' => $tasksCurrent,
                'created_previous' => $tasksPrev,
            ],
            'project_breakdown' => $projects,
            'category_breakdown' => $categories,
        ];
    }

    public function render()
    {
        $user = Auth::user();
        $wsId = $this->workspace->id;

        // 1. Dynamic Greeting
        $hour = (int) now()->format('H');
        $greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };

        // 2. Time Window & Comparisons
        $boundaries = $this->getTimeBoundaries();
        $cStart = $boundaries['current_start'];
        $cEnd = $boundaries['current_end'];
        $pStart = $boundaries['prev_start'];
        $pEnd = $boundaries['prev_end'];

        // Base Queries for Tasks & Tickets
        $allTasksQuery = Task::whereHas('taskList.project.space', function ($q) use ($wsId) {
            $q->where('workspace_id', $wsId);
            if ($this->selectedSpaceId) {
                $q->where('spaces.id', $this->selectedSpaceId);
            }
            if ($this->filterProjectId) {
                $q->where('projects.id', $this->filterProjectId);
            }
        })->with(['status', 'assignees', 'taskList.project.space']);

        $tasks = $allTasksQuery->get();

        $allTicketsQuery = Ticket::where('workspace_id', $wsId)
            ->when($this->filterProjectId, fn ($q) => $q->where('project_id', $this->filterProjectId))
            ->with(['category', 'project', 'raisedBy', 'assignedTo']);

        $workspaceTickets = $allTicketsQuery->get();

        // 3. Month-over-Month / Year-over-Year Comparative Deltas
        $ticketsCreatedCur = $workspaceTickets->filter(fn ($t) => $t->created_at >= $cStart && $t->created_at <= $cEnd)->count();
        $ticketsCreatedPrev = $workspaceTickets->filter(fn ($t) => $t->created_at >= $pStart && $t->created_at <= $pEnd)->count();
        $ticketCreationDelta = $this->computeDelta($ticketsCreatedCur, $ticketsCreatedPrev);

        $ticketsResolvedCur = $workspaceTickets->filter(fn ($t) => $t->resolved_at && $t->resolved_at >= $cStart && $t->resolved_at <= $cEnd)->count();
        $ticketsResolvedPrev = $workspaceTickets->filter(fn ($t) => $t->resolved_at && $t->resolved_at >= $pStart && $t->resolved_at <= $pEnd)->count();
        $ticketResolutionDelta = $this->computeDelta($ticketsResolvedCur, $ticketsResolvedPrev);

        $tasksCreatedCur = $tasks->filter(fn ($t) => $t->created_at >= $cStart && $t->created_at <= $cEnd)->count();
        $tasksCreatedPrev = $tasks->filter(fn ($t) => $t->created_at >= $pStart && $t->created_at <= $pEnd)->count();
        $taskCreationDelta = $this->computeDelta($tasksCreatedCur, $tasksCreatedPrev);

        $tasksCompletedCur = $tasks->filter(fn ($t) => $t->isDone() && $t->updated_at >= $cStart && $t->updated_at <= $cEnd)->count();
        $tasksCompletedPrev = $tasks->filter(fn ($t) => $t->isDone() && $t->updated_at >= $pStart && $t->updated_at <= $pEnd)->count();
        $taskCompletionDelta = $this->computeDelta($tasksCompletedCur, $tasksCompletedPrev);

        // 4. Time Series Graph Generator for Chart.js
        $chartLabels = [];
        $chartTicketsCreated = [];
        $chartTicketsResolved = [];
        $chartTasksCreated = [];
        $chartTasksCompleted = [];

        if ($boundaries['granularity'] === 'day') {
            // Group by days in current window
            $periodDays = $cStart->diffInDays($cEnd) + 1;
            // Cap at actual days in month or today
            $step = $periodDays > 20 ? 2 : 1;

            for ($d = $cStart->copy(); $d <= $cEnd; $d->addDays($step)) {
                $dayStart = $d->copy()->startOfDay();
                $dayEnd = $d->copy()->addDays($step - 1)->endOfDay();
                if ($dayEnd > $cEnd) $dayEnd = $cEnd->copy();

                $chartLabels[] = $d->format('M j');
                $chartTicketsCreated[] = $workspaceTickets->filter(fn ($t) => $t->created_at >= $dayStart && $t->created_at <= $dayEnd)->count();
                $chartTicketsResolved[] = $workspaceTickets->filter(fn ($t) => $t->resolved_at && $t->resolved_at >= $dayStart && $t->resolved_at <= $dayEnd)->count();
                $chartTasksCreated[] = $tasks->filter(fn ($t) => $t->created_at >= $dayStart && $t->created_at <= $dayEnd)->count();
                $chartTasksCompleted[] = $tasks->filter(fn ($t) => $t->isDone() && $t->updated_at >= $dayStart && $t->updated_at <= $dayEnd)->count();
            }
        } else {
            // Group by months in year or 12 months
            $mStart = $cStart->copy()->startOfMonth();
            for ($m = $mStart; $m <= $cEnd; $m->addMonth()) {
                $monthStart = $m->copy()->startOfMonth();
                $monthEnd = $m->copy()->endOfMonth();

                $chartLabels[] = $m->format('M Y');
                $chartTicketsCreated[] = $workspaceTickets->filter(fn ($t) => $t->created_at >= $monthStart && $t->created_at <= $monthEnd)->count();
                $chartTicketsResolved[] = $workspaceTickets->filter(fn ($t) => $t->resolved_at && $t->resolved_at >= $monthStart && $t->resolved_at <= $monthEnd)->count();
                $chartTasksCreated[] = $tasks->filter(fn ($t) => $t->created_at >= $monthStart && $t->created_at <= $monthEnd)->count();
                $chartTasksCompleted[] = $tasks->filter(fn ($t) => $t->isDone() && $t->updated_at >= $monthStart && $t->updated_at <= $monthEnd)->count();
            }
        }

        // 5. Project Breakdown: How Many Tasks Are Here & How Many Tickets Coming on Which Project
        $projectsQuery = Project::whereHas('space', fn ($q) => $q->where('workspace_id', $wsId))
            ->when($this->selectedSpaceId, fn ($q) => $q->where('space_id', $this->selectedSpaceId))
            ->with(['space', 'tasks.status', 'tickets']);

        $projectsMatrix = $projectsQuery->get()->map(function ($p) use ($cStart, $cEnd, $pStart, $pEnd) {
            $pTasks = $p->tasks;
            $totalTasks = $pTasks->count();
            $doneTasks = $pTasks->filter(fn ($t) => $t->isDone())->count();
            $inProgressTasks = $pTasks->filter(fn ($t) => !$t->isDone() && $t->status?->type === 'in_progress')->count();
            $overdueTasks = $pTasks->filter(fn ($t) => $t->isOverdue())->count();
            $taskPct = $totalTasks > 0 ? (int) round(($doneTasks / $totalTasks) * 100) : 0;

            $pTickets = $p->tickets;
            $ticketsTotal = $pTickets->count();
            $ticketsCurrent = $pTickets->filter(fn ($t) => $t->created_at >= $cStart && $t->created_at <= $cEnd)->count();
            $ticketsPrevious = $pTickets->filter(fn ($t) => $t->created_at >= $pStart && $t->created_at <= $pEnd)->count();
            $ticketsOpen = $pTickets->whereIn('status', ['open', 'assigned', 'in_progress'])->count();
            $ticketsResolved = $pTickets->whereIn('status', ['resolved', 'closed'])->count();

            $ratio = $totalTasks > 0 ? round($ticketsCurrent / $totalTasks, 2) : $ticketsCurrent;

            $healthStatus = match (true) {
                $ticketsOpen >= 5 || $overdueTasks >= 3 => 'High Load',
                $ticketsCurrent > $ticketsPrevious && $ticketsCurrent >= 3 => 'Surging Influx',
                $taskPct >= 70 => 'Healthy Velocity',
                default => 'Normal',
            };

            $healthColor = match ($healthStatus) {
                'High Load' => 'rose',
                'Surging Influx' => 'amber',
                'Healthy Velocity' => 'emerald',
                default => 'blue',
            };

            $ticketDelta = $ticketsCurrent - $ticketsPrevious;

            return [
                'id' => $p->id,
                'project_id' => $p->id,
                'name' => $p->name,
                'project_name' => $p->name,
                'space_name' => $p->space?->name ?? 'Space',
                'space_color' => $p->space?->color ?? '#6366f1',
                'color' => $p->color ?: '#3b82f6',
                'icon' => $p->icon ?: 'folder',
                'total_tasks' => $totalTasks,
                'done_tasks' => $doneTasks,
                'completed_tasks' => $doneTasks,
                'in_progress_tasks' => $inProgressTasks,
                'overdue_tasks' => $overdueTasks,
                'task_completion_pct' => $taskPct,
                'tickets_total' => $ticketsTotal,
                'tickets_current' => $ticketsCurrent,
                'tickets_count_current' => $ticketsCurrent,
                'tickets_previous' => $ticketsPrevious,
                'tickets_count_previous' => $ticketsPrevious,
                'ticket_delta' => $ticketDelta,
                'tickets_open' => $ticketsOpen,
                'open_tickets' => $ticketsOpen,
                'tickets_resolved' => $ticketsResolved,
                'ticket_task_ratio' => $ratio,
                'health_status' => $healthStatus,
                'health_color' => $healthColor,
            ];
        })->sortByDesc('tickets_current')->values();

        // Project Workload Chart Data
        $chartProjectNames = $projectsMatrix->pluck('name')->take(8)->toArray();
        $chartProjectTasks = $projectsMatrix->pluck('total_tasks')->take(8)->toArray();
        $chartProjectTickets = $projectsMatrix->pluck('tickets_current')->take(8)->toArray();

        // 6. Category / Issue Type Comparison (This Month vs Previous Month)
        $categoriesMatrix = TicketCategory::where('workspace_id', $wsId)->get()->map(function ($c) use ($workspaceTickets, $cStart, $cEnd, $pStart, $pEnd) {
            $catTickets = $workspaceTickets->where('category_id', $c->id);
            $curCount = $catTickets->filter(fn ($t) => $t->created_at >= $cStart && $t->created_at <= $cEnd)->count();
            $prevCount = $catTickets->filter(fn ($t) => $t->created_at >= $pStart && $t->created_at <= $pEnd)->count();
            $delta = $this->computeDelta($curCount, $prevCount);

            return [
                'id' => $c->id,
                'category_id' => $c->id,
                'name' => $c->name,
                'description' => $c->description,
                'current' => $curCount,
                'previous' => $prevCount,
                'delta' => $delta['delta'],
                'pct' => $delta['pct'],
                'direction' => $delta['direction'],
                'delta_data' => $delta,
            ];
        })->sortByDesc('current')->values();

        $chartCategoryLabels = $categoriesMatrix->pluck('name')->toArray();
        $chartCategoryData = $categoriesMatrix->pluck('current')->toArray();

        // 7. General KPIs & Personal Items
        $myTasks = $user ? $tasks->filter(fn ($t) => $t->assignees->contains('id', $user->id)) : collect();
        $myOpen = $myTasks->filter(fn ($t) => !$t->isDone());
        $myOverdue = $myOpen->filter(fn ($t) => $t->isOverdue());
        $myDueToday = $myOpen->filter(fn ($t) => $t->isDueToday());
        $myDueThisWeek = $myOpen->filter(function ($t) {
            if (!$t->due_date) return false;
            return $t->due_date->between(now()->startOfWeek(), now()->endOfWeek()) && !$t->isDone();
        });

        $totalWorkspaceTasks = $tasks->count();
        $completedWorkspaceTasks = $tasks->filter(fn ($t) => $t->isDone())->count();
        $completionRate = $totalWorkspaceTasks > 0 ? (int) round(($completedWorkspaceTasks / $totalWorkspaceTasks) * 100) : 0;
        $totalOverdue = $tasks->filter(fn ($t) => $t->isOverdue())->count();
        $totalBlocked = $tasks->filter(fn ($t) => $t->status?->type === 'blocked')->count();

        $statusCounts = $tasks->groupBy(fn ($t) => $t->status?->name ?? 'Other')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'color' => $group->first()->status?->color ?? '#94a3b8',
                'type' => $group->first()->status?->type ?? 'todo',
            ]);

        $criticalTasks = $tasks->filter(fn ($t) => ($t->isOverdue() || $t->status?->type === 'blocked') && !$t->isDone())->take(6);

        // Support Tickets Overview
        $ticketsOpen = $workspaceTickets->where('status', 'open')->count();
        $ticketsInProgress = $workspaceTickets->where('status', 'in_progress')->count();
        $ticketsWaitingClient = $workspaceTickets->where('status', 'waiting_on_client')->count();
        $ticketsResolvedCount = $workspaceTickets->whereIn('status', ['resolved', 'closed'])->count();
        $ticketsOverdue = $workspaceTickets->filter(fn ($t) => $t->isOverdue())->count();
        $ticketsAssignedToMe = $user ? $workspaceTickets->where('assigned_to_user_id', $user->id)
            ->filter(fn ($t) => !in_array($t->status, ['resolved', 'closed']))
            ->count() : 0;

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

        $urgentTickets = $workspaceTickets->filter(function ($t) {
            return !in_array($t->status, ['resolved', 'closed']) && ($t->priority === 'urgent' || $t->isOverdue());
        })->take(6);

        // Spaces Progress
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

        // Workload & Capacity Heatmap
        $members = $this->workspace->members()->withCount([
            'assignedTasks' => function ($q) use ($wsId) {
                $q->whereHas('taskList.project.space', fn ($sq) => $sq->where('workspace_id', $wsId))
                  ->whereNull('parent_id')
                  ->whereHas('status', fn ($sq) => $sq->where('type', '!=', 'done'));
            },
            'assignedTickets' => function ($q) use ($wsId) {
                $q->where('workspace_id', $wsId)
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

        // Unified Live Activity Stream
        $taskActivities = collect();
        if (in_array($this->activityFilter, ['all', 'tasks'])) {
            $taskActivities = TaskActivity::whereHas('task.taskList.project.space', function ($q) use ($wsId) {
                $q->where('workspace_id', $wsId);
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
            $ticketActivities = TicketActivityLog::whereHas('ticket', function ($q) use ($wsId) {
                $q->where('workspace_id', $wsId);
            })->with(['user', 'ticket'])->latest()->take(12)->get()->map(fn ($a) => [
                'type' => 'ticket',
                'id' => $a->id,
                'title' => $a->ticket ? ($a->ticket->ticket_number . ' - ' . $a->ticket->subject) : 'Ticket',
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

        $totalAttentionItems = $totalOverdue + $ticketsOverdue + $totalBlocked;

        // All workspace projects for filter dropdown
        $workspaceProjects = Project::whereHas('space', fn ($q) => $q->where('workspace_id', $wsId))->orderBy('name')->get();

        return view('livewire.dashboard.dashboard', [
            'greeting' => $greeting,
            'timeBoundaries' => $boundaries,
            'ticketCreationDelta' => $ticketCreationDelta,
            'ticketResolutionDelta' => $ticketResolutionDelta,
            'taskCreationDelta' => $taskCreationDelta,
            'taskCompletionDelta' => $taskCompletionDelta,
            'chartLabels' => $chartLabels,
            'chartTicketsCreated' => $chartTicketsCreated,
            'chartTicketsResolved' => $chartTicketsResolved,
            'chartTasksCreated' => $chartTasksCreated,
            'chartTasksCompleted' => $chartTasksCompleted,
            'chartProjectNames' => $chartProjectNames,
            'chartProjectTasks' => $chartProjectTasks,
            'chartProjectTickets' => $chartProjectTickets,
            'chartCategoryLabels' => $chartCategoryLabels,
            'chartCategoryData' => $chartCategoryData,
            'projectsMatrix' => $projectsMatrix,
            'categoriesMatrix' => $categoriesMatrix,
            'workspaceProjects' => $workspaceProjects,
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
