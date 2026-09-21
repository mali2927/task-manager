<?php

namespace App\Livewire\Tasks;

use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\TaskAttachment;
use App\Models\TaskChecklist;
use App\Models\TaskComment;
use App\Models\TaskStatus;
use App\Models\TaskTimeEntry;
use App\Models\User;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class TaskDetailModal extends Component
{
    use WithFileUploads;

    public ?int $taskId = null;
    public ?Task $task = null;
    public bool $isOpen = false;

    // Editable fields
    public string $title = '';
    public string $description = '';
    public ?int $statusId = null;
    public string $priority = 'normal';
    public ?string $startDate = null;
    public ?string $dueDate = null;
    public ?float $estimatedHours = null;

    // Subtasks & Checklists
    public string $newChecklistTitle = '';
    public string $newSubtaskTitle = '';

    // Comments
    public string $newCommentContent = '';
    public ?int $replyToCommentId = null;

    // Attachments
    public $uploadedFile;

    // Time tracking
    public bool $isTimerRunning = false;
    public ?int $activeTimerEntryId = null;
    public ?string $manualTimeMinutes = null;
    public ?string $manualTimeDesc = null;

    // AI
    public ?string $aiSummary = null;
    public bool $isGeneratingAi = false;

    #[On('open-task-detail')]
    public function openTask(int $taskId): void
    {
        $this->taskId = $taskId;
        $this->loadTask();
        $this->isOpen = true;
        $this->aiSummary = null;
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->taskId = null;
        $this->task = null;
        $this->dispatch('task-updated');
    }

    public function loadTask(): void
    {
        if (!$this->taskId) return;

        $this->task = Task::with([
            'taskList.project.space.workspace.taskStatuses',
            'status',
            'assignees',
            'checklists',
            'subtasks.status',
            'comments.user',
            'comments.replies.user',
            'attachments.user',
            'tags',
            'timeEntries.user',
            'activities.user',
            'blockedBy.dependsOn',
        ])->find($this->taskId);

        if ($this->task) {
            $this->title = $this->task->title;
            $this->description = $this->task->description ?? '';
            $this->statusId = $this->task->status_id;
            $this->priority = $this->task->priority;
            $this->startDate = $this->task->start_date?->format('Y-m-d');
            $this->dueDate = $this->task->due_date?->format('Y-m-d');
            $this->estimatedHours = $this->task->estimated_hours;

            // Check running timer
            $runningEntry = TaskTimeEntry::where('task_id', $this->task->id)
                ->where('user_id', Auth::id())
                ->whereNull('stopped_at')
                ->first();

            $this->isTimerRunning = (bool) $runningEntry;
            $this->activeTimerEntryId = $runningEntry?->id;
        }
    }

    public function updateTitle(): void
    {
        if (!$this->task || empty(trim($this->title))) return;

        $old = $this->task->title;
        $this->task->update(['title' => $this->title]);
        TaskActivity::log($this->task, Auth::user(), 'title_updated', "Renamed task from '{$old}' to '{$this->title}'", $old, $this->title);
        $this->dispatch('task-updated');
    }

    public function updateDescription(): void
    {
        if (!$this->task) return;

        $this->task->update(['description' => $this->description]);
        TaskActivity::log($this->task, Auth::user(), 'description_updated', 'Updated task description');
        $this->dispatch('task-updated');
    }

    public function updateStatus(int $newStatusId): void
    {
        if (!$this->task) return;

        $oldStatus = $this->task->status?->name;
        $this->task->update(['status_id' => $newStatusId]);
        $this->statusId = $newStatusId;
        $newStatus = TaskStatus::find($newStatusId)?->name;

        TaskActivity::log($this->task, Auth::user(), 'status_updated', "Changed status to '{$newStatus}'", $oldStatus, $newStatus);
        $this->loadTask();
        $this->dispatch('task-updated');
    }

    public function updatePriority(string $newPriority): void
    {
        if (!$this->task) return;

        $old = $this->task->priority;
        $this->task->update(['priority' => $newPriority]);
        $this->priority = $newPriority;

        TaskActivity::log($this->task, Auth::user(), 'priority_updated', "Updated priority from {$old} to {$newPriority}", $old, $newPriority);
        $this->loadTask();
        $this->dispatch('task-updated');
    }

    public function updateDates(): void
    {
        if (!$this->task) return;

        $this->task->update([
            'start_date' => $this->startDate ?: null,
            'due_date' => $this->dueDate ?: null,
        ]);

        TaskActivity::log($this->task, Auth::user(), 'dates_updated', 'Updated task dates');
        $this->loadTask();
        $this->dispatch('task-updated');
    }

    public function toggleAssignee(int $userId): void
    {
        if (!$this->task) return;

        $user = User::find($userId);
        if (!$user) return;

        if ($this->task->assignees()->where('users.id', $userId)->exists()) {
            $this->task->assignees()->detach($userId);
            TaskActivity::log($this->task, Auth::user(), 'unassigned', "Removed {$user->name} from assignees");
        } else {
            $this->task->assignees()->attach($userId);
            TaskActivity::log($this->task, Auth::user(), 'assigned', "Assigned {$user->name} to task");
        }

        $this->loadTask();
        $this->dispatch('task-updated');
    }

    public function addChecklist(): void
    {
        if (!$this->task || empty(trim($this->newChecklistTitle))) return;

        TaskChecklist::create([
            'task_id' => $this->task->id,
            'title' => trim($this->newChecklistTitle),
            'is_completed' => false,
            'sort_order' => $this->task->checklists()->count() + 1,
        ]);

        $this->newChecklistTitle = '';
        TaskActivity::log($this->task, Auth::user(), 'checklist_added', 'Added checklist item');
        $this->loadTask();
        $this->dispatch('task-updated');
    }

    public function toggleChecklist(int $checklistId): void
    {
        $item = TaskChecklist::find($checklistId);
        if ($item && $item->task_id === $this->task?->id) {
            $item->update(['is_completed' => !$item->is_completed]);
            $this->loadTask();
            $this->dispatch('task-updated');
        }
    }

    public function deleteChecklist(int $checklistId): void
    {
        $item = TaskChecklist::find($checklistId);
        if ($item && $item->task_id === $this->task?->id) {
            $item->delete();
            $this->loadTask();
            $this->dispatch('task-updated');
        }
    }

    public function addSubtask(): void
    {
        if (!$this->task || empty(trim($this->newSubtaskTitle))) return;

        $defaultStatus = $this->task->taskList?->project?->space?->workspace?->taskStatuses()->where('is_default', true)->first()
            ?? $this->task->status;

        Task::create([
            'task_list_id' => $this->task->task_list_id,
            'parent_id' => $this->task->id,
            'created_by_id' => Auth::id(),
            'title' => trim($this->newSubtaskTitle),
            'status_id' => $defaultStatus->id,
            'priority' => 'normal',
        ]);

        $this->newSubtaskTitle = '';
        TaskActivity::log($this->task, Auth::user(), 'subtask_created', 'Added nested subtask');
        $this->loadTask();
        $this->dispatch('task-updated');
    }

    public function addComment(): void
    {
        if (!$this->task || empty(trim($this->newCommentContent))) return;

        TaskComment::create([
            'task_id' => $this->task->id,
            'user_id' => Auth::id(),
            'parent_id' => $this->replyToCommentId,
            'content' => trim($this->newCommentContent),
        ]);

        $this->newCommentContent = '';
        $this->replyToCommentId = null;
        TaskActivity::log($this->task, Auth::user(), 'comment_added', 'Added comment');
        $this->loadTask();
    }

    public function replyTo(int $commentId): void
    {
        $this->replyToCommentId = $commentId;
    }

    public function cancelReply(): void
    {
        $this->replyToCommentId = null;
    }

    public function uploadAttachment(): void
    {
        $this->validate([
            'uploadedFile' => 'required|file|max:10240', // 10MB
        ]);

        if (!$this->task || !$this->uploadedFile) return;

        $path = $this->uploadedFile->store('attachments', 'public');

        TaskAttachment::create([
            'task_id' => $this->task->id,
            'user_id' => Auth::id(),
            'file_name' => $this->uploadedFile->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $this->uploadedFile->getSize(),
            'mime_type' => $this->uploadedFile->getMimeType(),
        ]);

        $this->uploadedFile = null;
        TaskActivity::log($this->task, Auth::user(), 'attachment_added', 'Uploaded file attachment');
        $this->loadTask();
    }

    public function deleteAttachment(int $attachmentId): void
    {
        $attachment = TaskAttachment::find($attachmentId);
        if ($attachment && $attachment->task_id === $this->task?->id) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
            $this->loadTask();
        }
    }

    public function toggleTimer(): void
    {
        if (!$this->task) return;

        if ($this->isTimerRunning) {
            // Stop timer
            $entry = TaskTimeEntry::find($this->activeTimerEntryId);
            if ($entry) {
                $now = now();
                $diffMinutes = max(1, $entry->started_at->diffInMinutes($now));
                $entry->update([
                    'stopped_at' => $now,
                    'duration_minutes' => $diffMinutes,
                ]);

                $this->task->increment('actual_hours', round($diffMinutes / 60, 2));
            }

            $this->isTimerRunning = false;
            $this->activeTimerEntryId = null;
            TaskActivity::log($this->task, Auth::user(), 'time_logged', "Logged {$diffMinutes} minutes with timer");
        } else {
            // Start timer
            $entry = TaskTimeEntry::create([
                'task_id' => $this->task->id,
                'user_id' => Auth::id(),
                'started_at' => now(),
                'duration_minutes' => 0,
            ]);

            $this->isTimerRunning = true;
            $this->activeTimerEntryId = $entry->id;
        }

        $this->loadTask();
        $this->dispatch('task-updated');
    }

    public function logManualTime(): void
    {
        $minutes = (int) $this->manualTimeMinutes;
        if (!$this->task || $minutes <= 0) return;

        TaskTimeEntry::create([
            'task_id' => $this->task->id,
            'user_id' => Auth::id(),
            'description' => $this->manualTimeDesc ?: 'Manual work log',
            'duration_minutes' => $minutes,
            'started_at' => now()->subMinutes($minutes),
            'stopped_at' => now(),
        ]);

        $this->task->increment('actual_hours', round($minutes / 60, 2));

        $this->manualTimeMinutes = null;
        $this->manualTimeDesc = null;

        TaskActivity::log($this->task, Auth::user(), 'time_logged', "Manually logged {$minutes} minutes");
        $this->loadTask();
        $this->dispatch('task-updated');
    }

    public function generateAiSummary(GeminiService $gemini): void
    {
        if (!$this->task) return;

        $this->isGeneratingAi = true;
        try {
            $this->aiSummary = $gemini->summarizeTask($this->task, \Illuminate\Support\Facades\Auth::user());
        } catch (\Throwable $e) {
            $this->aiSummary = "Error generating AI summary: " . $e->getMessage();
        } finally {
            $this->isGeneratingAi = false;
        }
    }

    public function deleteTask(): void
    {
        if (!$this->task) return;

        $this->task->delete();
        $this->close();
    }

    public function render()
    {
        $allStatuses = $this->task?->taskList?->project?->space?->workspace?->taskStatuses ?? collect();
        $workspaceUsers = $this->task?->taskList?->project?->space?->workspace?->members ?? collect();

        return view('livewire.tasks.task-detail-modal', [
            'allStatuses' => $allStatuses,
            'workspaceUsers' => $workspaceUsers,
        ]);
    }
}
