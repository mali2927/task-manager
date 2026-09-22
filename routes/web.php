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

    // 8. CSV Export of Workspace Tasks
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
