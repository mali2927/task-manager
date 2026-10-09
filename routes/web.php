<?php

use App\Livewire\AccessRequests\AccessRequestManager;
use App\Livewire\AccessRequests\RequestAccess;
use App\Livewire\Ai\AiAssistant;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Tasks\MyTasks;
use App\Livewire\Tasks\TaskManager;
use App\Livewire\Tickets\CapacityDashboard;
use App\Livewire\Tickets\MyTickets as MyTicketsList;
use App\Livewire\Tickets\RaiseTicket;
use App\Livewire\Tickets\TicketCategoryManager;
use App\Livewire\Tickets\TicketQueue;
use App\Livewire\Workspace\ActivityLogs;
use App\Livewire\Workspace\TeamManager;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Public Access Request Flow (No open registration)
Route::get('/request-access', RequestAccess::class)->name('access-requests.create');
Route::get('/register', fn () => redirect()->route('access-requests.create'))->name('register');
Route::post('/register', fn () => abort(403, 'Public registration is disabled. Please submit an access request.'))->name('register.store');

Route::middleware(['auth', 'verified'])->group(function () {
    // 1. Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // 2. Personal My Tasks
    Route::get('/my-tasks', MyTasks::class)->name('my-tasks');

    // 3. Workspace Task Manager (supports view parameter: board, list, calendar, gantt)
    Route::get('/workspace/{workspace:slug}/tasks/{view?}', TaskManager::class)->name('workspace.tasks');

    // 4. Teams & Workspace People
    Route::get('/workspace/{workspace:slug}/teams', TeamManager::class)->name('workspace.teams');

    // 5. AI Assistant
    Route::get('/workspace/{workspace:slug}/ai', AiAssistant::class)->name('workspace.ai');

    // 6. Access Requests Management (Admin only)
    Route::get('/workspace/{workspace:slug}/access-requests', AccessRequestManager::class)->name('workspace.access-requests');

    // 7. Support Ticketing System
    Route::get('/workspace/{workspace:slug}/tickets/raise', RaiseTicket::class)->name('workspace.tickets.raise');
    Route::get('/workspace/{workspace:slug}/tickets/my', MyTicketsList::class)->name('workspace.tickets.my');
    Route::get('/workspace/{workspace:slug}/tickets/queue', TicketQueue::class)->name('workspace.tickets.queue');
    Route::get('/workspace/{workspace:slug}/tickets/capacity', CapacityDashboard::class)->name('workspace.tickets.capacity');
    Route::get('/workspace/{workspace:slug}/tickets/categories', TicketCategoryManager::class)->name('workspace.tickets.categories');

    // 8. Workspace Audit & Activity Logs (ISO 27001 Compliance)
    Route::get('/workspace/{workspace:slug}/activity-logs', ActivityLogs::class)->name('workspace.activity-logs');

    // 9. CSV Export of Workspace Audit Logs
    Route::get('/workspace/{workspace:slug}/activity-logs/export', function (Workspace $workspace) {
        abort_if(Auth::user()->isWorkspaceRequester($workspace), 403, 'Unauthorized.');

        $ticketLogs = \App\Models\TicketActivityLog::whereHas('ticket', fn ($q) => $q->where('workspace_id', $workspace->id))
            ->with(['ticket', 'user'])
            ->latest('created_at')
            ->get()
            ->map(fn ($l) => [
                'type' => 'Ticket',
                'id' => $l->id,
                'reference' => $l->ticket?->ticket_number ?? 'TCK-???',
                'title' => $l->ticket?->subject ?? 'Deleted Ticket',
                'actor' => $l->user?->name ?? 'System',
                'actor_email' => $l->user?->email ?? 'N/A',
                'action' => $l->action,
                'description' => $l->description,
                'from_value' => $l->from_value,
                'to_value' => $l->to_value,
                'created_at' => $l->created_at->toDateTimeString(),
            ]);

        $taskLogs = \App\Models\TaskActivity::whereHas('task.taskList.project.space', fn ($q) => $q->where('workspace_id', $workspace->id))
            ->with(['task', 'user'])
            ->latest('created_at')
            ->get()
            ->map(fn ($l) => [
                'type' => 'Task',
                'id' => $l->id,
                'reference' => "Task #{$l->task_id}",
                'title' => $l->task?->title ?? 'Deleted Task',
                'actor' => $l->user?->name ?? 'System',
                'actor_email' => $l->user?->email ?? 'N/A',
                'action' => $l->action,
                'description' => $l->description,
                'from_value' => $l->old_value,
                'to_value' => $l->new_value,
                'created_at' => $l->created_at->toDateTimeString(),
            ]);

        $allLogs = $ticketLogs->concat($taskLogs)->sortByDesc('created_at')->values();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"audit-logs-{$workspace->slug}-" . date('Y-m-d') . ".csv\"",
        ];

        $callback = function () use ($allLogs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Type', 'Event ID', 'Reference', 'Record Title', 'Actor Name', 'Actor Email', 'Action Identifier', 'Log Description', 'Old Value', 'New Value', 'Timestamp UTC']);

            foreach ($allLogs as $log) {
                fputcsv($file, [
                    $log['type'],
                    $log['id'],
                    $log['reference'],
                    $log['title'],
                    $log['actor'],
                    $log['actor_email'],
                    $log['action'],
                    $log['description'],
                    $log['from_value'],
                    $log['to_value'],
                    $log['created_at'],
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    })->name('workspace.activity-logs.export');

    // 10. CSV Export of Workspace Tasks
    Route::get('/workspace/{workspace:slug}/export', function (Workspace $workspace) {
        abort_if(Auth::user()->isWorkspaceRequester($workspace), 403, 'Unauthorized. Requesters cannot export workspace internal tasks.');

        $tasks = Task::whereHas('taskList.project.space', fn ($q) => $q->where('workspace_id', $workspace->id))
            ->with(['status', 'assignees', 'taskList.project.space', 'checklists'])
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"tasks-{$workspace->slug}-" . date('Y-m-d') . ".csv\"",
        ];

        $callback = function () use ($tasks) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Space', 'Project', 'List', 'Status', 'Priority', 'Start Date', 'Due Date', 'Estimated Hours', 'Actual Hours', 'Assignees', 'Checklists Total', 'Checklists Done', 'Created At']);

            foreach ($tasks as $t) {
                fputcsv($file, [
                    $t->id,
                    $t->title,
                    $t->taskList?->project?->space?->name,
                    $t->taskList?->project?->name,
                    $t->taskList?->name,
                    $t->status?->name,
                    $t->priority,
                    $t->start_date?->toDateString(),
                    $t->due_date?->toDateString(),
                    $t->estimated_hours,
                    $t->actual_hours,
                    $t->assignees->pluck('name')->implode(', '),
                    $t->checklists->count(),
                    $t->checklists->where('is_completed', true)->count(),
                    $t->created_at->toDateTimeString(),
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    })->name('workspace.export');

    // 9. CSV Export of Workspace Tickets
    Route::get('/workspace/{workspace:slug}/tickets/export', function (Workspace $workspace) {
        abort_if(Auth::user()->isWorkspaceRequester($workspace), 403, 'Unauthorized.');

        $tickets = Ticket::where('workspace_id', $workspace->id)
            ->with(['category', 'raisedBy', 'assignedTeam', 'assignedTo'])
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"tickets-{$workspace->slug}-" . date('Y-m-d') . ".csv\"",
        ];

        $callback = function () use ($tickets) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Ticket Number', 'Subject', 'Category', 'Priority', 'Status', 'Raised By', 'Assigned Team', 'Assigned To', 'Due By', 'Resolved At', 'Created At']);

            foreach ($tickets as $t) {
                fputcsv($file, [
                    $t->ticket_number,
                    $t->subject,
                    $t->category?->name ?? 'General',
                    $t->priority,
                    $t->status,
                    $t->raisedBy?->name,
                    $t->assignedTeam?->name ?? 'Unassigned',
                    $t->assignedTo?->name ?? 'Unassigned',
                    $t->due_by?->toDateTimeString(),
                    $t->resolved_at?->toDateTimeString(),
                    $t->created_at->toDateTimeString(),
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    })->name('workspace.tickets.export');
});

require __DIR__.'/settings.php';
