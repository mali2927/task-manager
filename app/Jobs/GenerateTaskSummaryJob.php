<?php

namespace App\Jobs;

use App\Models\Task;
use App\Services\GeminiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateTaskSummaryJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Task $task)
    {
    }

    public function handle(GeminiService $gemini): void
    {
        try {
            $summary = $gemini->summarizeTask($this->task);
            Log::info("Asynchronous summary generated for task {$this->task->id}");
        } catch (\Throwable $e) {
            Log::error("Failed to generate async summary for task {$this->task->id}: " . $e->getMessage());
        }
    }
}
