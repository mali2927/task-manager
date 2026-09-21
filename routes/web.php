<?php

use App\Livewire\Ai\AiAssistant;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Tasks\MyTasks;
use App\Livewire\Tasks\TaskManager;
use App\Livewire\Workspace\TeamManager;
use App\Models\Task;
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

    // 6. CSV Export of Workspace Tasks
    Route::get('/workspace/{workspace:slug}/export', function (Workspace $workspace) {
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
});

require __DIR__.'/settings.php';
