<?php

namespace App\Livewire\Workspace;

use App\Models\TaskActivity;
use App\Models\TicketActivityLog;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ActivityLogs extends Component
{
    use WithPagination;

    public Workspace $workspace;

    #[Url(as: 'type')]
    public string $typeFilter = 'all'; // all, tickets, tasks

    #[Url(as: 'action')]
    public string $actionFilter = 'all'; // all, status, assign, priority, comment, attachment, time, csat, convert

    #[Url(as: 'user')]
    public ?int $selectedUserId = null;

    #[Url(as: 'period')]
    public string $dateRange = 'all'; // today, 7days, 30days, all

    #[Url(as: 'search')]
    public string $search = '';

    public ?int $inspectLogId = null;
    public ?string $inspectLogType = null;
    public bool $showInspectModal = false;
    public ?array $inspectData = null;

    public function mount(Workspace $workspace): void
    {
        $this->workspace = $workspace;

        $user = Auth::user();
        if ($user && $user->isWorkspaceRequester($this->workspace)) {
            $this->redirect(route('workspace.tickets.my', ['workspace' => $this->workspace->slug]), navigate: true);
            return;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActionFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedUserId(): void
    {
        $this->resetPage();
    }

    public function updatingDateRange(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->typeFilter = 'all';
        $this->actionFilter = 'all';
        $this->selectedUserId = null;
        $this->dateRange = 'all';
        $this->search = '';
        $this->resetPage();
    }

    public function inspectLog(string $type, int $id): void
    {
        $this->inspectLogType = $type;
        $this->inspectLogId = $id;

        if ($type === 'ticket') {
            $log = TicketActivityLog::with(['ticket.workspace', 'user'])->find($id);
            if ($log) {
                $this->inspectData = [
                    'type' => 'Ticket Activity',
                    'id' => $log->id,
                    'reference' => $log->ticket?->ticket_number ?? 'N/A',
                    'title' => $log->ticket?->subject ?? 'Deleted Ticket',
                    'actor' => $log->user?->name ?? 'System',
                    'actor_email' => $log->user?->email ?? 'system@automated.internal',
                    'action' => $log->action,
                    'description' => $log->description,
                    'from_value' => $log->from_value,
                    'to_value' => $log->to_value,
                    'timestamp' => $log->created_at->format('Y-m-d H:i:s T'),
                    'relative_time' => $log->created_at->diffForHumans(),
                    'ticket_status' => $log->ticket?->status,
                    'ticket_priority' => $log->ticket?->priority,
                    'ticket_id' => $log->ticket_id,
                ];
                $this->showInspectModal = true;
            }
        } else {
            $log = TaskActivity::with(['task.taskList.project.space', 'user'])->find($id);
            if ($log) {
                $this->inspectData = [
                    'type' => 'Task Activity',
                    'id' => $log->id,
                    'reference' => "Task #{$log->task_id}",
                    'title' => $log->task?->title ?? 'Deleted Task',
                    'actor' => $log->user?->name ?? 'System',
                    'actor_email' => $log->user?->email ?? 'system@automated.internal',
                    'action' => $log->action,
                    'description' => $log->description,
                    'from_value' => $log->old_value,
                    'to_value' => $log->new_value,
                    'timestamp' => $log->created_at->format('Y-m-d H:i:s T'),
                    'relative_time' => $log->created_at->diffForHumans(),
                    'task_status' => $log->task?->status?->name,
                    'task_priority' => $log->task?->priority,
                    'task_id' => $log->task_id,
                ];
                $this->showInspectModal = true;
            }
        }
    }

    public function closeInspectModal(): void
    {
        $this->showInspectModal = false;
        $this->inspectData = null;
        $this->inspectLogId = null;
        $this->inspectLogType = null;
    }

    public function render()
    {
        $wsId = $this->workspace->id;

        // Date Boundary
        $startDate = match ($this->dateRange) {
            'today' => now()->startOfDay(),
            '7days' => now()->subDays(7)->startOfDay(),
            '30days' => now()->subDays(30)->startOfDay(),
            default => null,
        };

        // Query Ticket Activities
        $ticketLogs = collect();
        if (in_array($this->typeFilter, ['all', 'tickets'])) {
            $tQuery = TicketActivityLog::whereHas('ticket', fn ($q) => $q->where('workspace_id', $wsId))
                ->with(['ticket.category', 'user']);

            if ($startDate) {
                $tQuery->where('created_at', '>=', $startDate);
            }

            if ($this->selectedUserId) {
                $tQuery->where('user_id', $this->selectedUserId);
            }

            // Action Filter mapping
            if ($this->actionFilter !== 'all') {
                $tQuery->where(function ($q) {
                    match ($this->actionFilter) {
                        'status' => $q->whereIn('action', ['status_changed', 'reopened', 'resolved', 'closed']),
                        'assign' => $q->whereIn('action', ['assigned_team', 'assigned_user']),
                        'priority' => $q->where('action', 'priority_changed'),
                        'comment' => $q->where('action', 'commented'),
                        'attachment' => $q->where('action', 'attachment_added'),
                        'convert' => $q->whereIn('action', ['converted_to_task', 'linked_to_task', 'unlinked_from_task', 'created_from_task']),
                        'csat' => $q->where('action', 'csat_rated'),
                        default => null,
                    };
                });
            }

            if (!empty(trim($this->search))) {
                $s = '%' . trim($this->search) . '%';
                $tQuery->where(function ($q) use ($s) {
                    $q->where('action', 'like', $s)
                      ->orWhere('to_value', 'like', $s)
                      ->orWhere('from_value', 'like', $s)
                      ->orWhereHas('ticket', fn ($t) => $t->where('ticket_number', 'like', $s)->orWhere('subject', 'like', $s))
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $s)->orWhere('email', 'like', $s));
                });
            }

            $ticketLogs = $tQuery->latest('created_at')->take(200)->get()->map(fn ($log) => [
                'type' => 'ticket',
                'id' => $log->id,
                'entity_id' => $log->ticket_id,
                'reference' => $log->ticket?->ticket_number ?? 'TCK-???',
                'title' => $log->ticket?->subject ?? 'Deleted Ticket',
                'action' => $log->action,
                'description' => $log->description,
                'from_value' => $log->from_value,
                'to_value' => $log->to_value,
                'user' => $log->user,
                'created_at' => $log->created_at,
                'badge_color' => match ($log->action) {
                    'resolved', 'csat_rated' => 'emerald',
                    'reopened', 'priority_changed' => 'amber',
                    'assigned_team', 'assigned_user' => 'sky',
                    'converted_to_task', 'linked_to_task', 'unlinked_from_task', 'created_from_task' => 'purple',
                    'created' => 'indigo',
                    default => 'zinc',
                },
            ]);
        }

        // Query Task Activities
        $taskLogs = collect();
        if (in_array($this->typeFilter, ['all', 'tasks'])) {
            $taskQuery = TaskActivity::whereHas('task.taskList.project.space', fn ($q) => $q->where('workspace_id', $wsId))
                ->with(['task.taskList.project', 'user']);

            if ($startDate) {
                $taskQuery->where('created_at', '>=', $startDate);
            }

            if ($this->selectedUserId) {
                $taskQuery->where('user_id', $this->selectedUserId);
            }

            if ($this->actionFilter !== 'all') {
                $taskQuery->where(function ($q) {
                    match ($this->actionFilter) {
                        'status' => $q->where('action', 'status_updated'),
                        'assign' => $q->whereIn('action', ['assigned', 'unassigned']),
                        'priority' => $q->where('action', 'priority_updated'),
                        'comment' => $q->where('action', 'comment_added'),
                        'attachment' => $q->where('action', 'attachment_added'),
                        'time' => $q->where('action', 'time_logged'),
                        'convert' => $q->whereIn('action', ['converted_from_ticket', 'ticket_linked', 'ticket_unlinked', 'ticket_created']),
                        default => null,
                    };
                });
            }

            if (!empty(trim($this->search))) {
                $s = '%' . trim($this->search) . '%';
                $taskQuery->where(function ($q) use ($s) {
                    $q->where('action', 'like', $s)
                      ->orWhere('description', 'like', $s)
                      ->orWhere('old_value', 'like', $s)
                      ->orWhere('new_value', 'like', $s)
                      ->orWhereHas('task', fn ($t) => $t->where('title', 'like', $s))
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $s)->orWhere('email', 'like', $s));
                });
            }

            $taskLogs = $taskQuery->latest('created_at')->take(200)->get()->map(fn ($log) => [
                'type' => 'task',
                'id' => $log->id,
                'entity_id' => $log->task_id,
                'reference' => "Task #{$log->task_id}",
                'title' => $log->task?->title ?? 'Deleted Task',
                'action' => $log->action,
                'description' => $log->description,
                'from_value' => $log->old_value,
                'to_value' => $log->new_value,
                'user' => $log->user,
                'created_at' => $log->created_at,
                'badge_color' => match ($log->action) {
                    'status_updated' => 'blue',
                    'assigned', 'unassigned' => 'sky',
                    'time_logged' => 'emerald',
                    'priority_updated' => 'amber',
                    'converted_from_ticket', 'ticket_linked', 'ticket_unlinked', 'ticket_created' => 'purple',
                    'created' => 'indigo',
                    default => 'zinc',
                },
            ]);
        }

        // Merge, sort, and paginate
        $merged = $ticketLogs->concat($taskLogs)->sortByDesc('created_at')->values();

        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $merged->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedLogs = new LengthAwarePaginator($currentItems, $merged->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        // Global Workspace Audit KPI metrics
        $totalTicketActivities = TicketActivityLog::whereHas('ticket', fn ($q) => $q->where('workspace_id', $wsId))->count();
        $totalTaskActivities = TaskActivity::whereHas('task.taskList.project.space', fn ($q) => $q->where('workspace_id', $wsId))->count();
        $todayActivities = TicketActivityLog::whereHas('ticket', fn ($q) => $q->where('workspace_id', $wsId))->whereDate('created_at', today())->count()
            + TaskActivity::whereHas('task.taskList.project.space', fn ($q) => $q->where('workspace_id', $wsId))->whereDate('created_at', today())->count();

        // Workspace Users list for filter dropdown
        $workspaceUsers = $this->workspace->members()->orderBy('name')->get();

        return view('livewire.workspace.activity-logs', [
            'logs' => $paginatedLogs,
            'totalTicketActivities' => $totalTicketActivities,
            'totalTaskActivities' => $totalTaskActivities,
            'totalActivities' => $totalTicketActivities + $totalTaskActivities,
            'todayActivities' => $todayActivities,
            'workspaceUsers' => $workspaceUsers,
        ]);
    }
}
