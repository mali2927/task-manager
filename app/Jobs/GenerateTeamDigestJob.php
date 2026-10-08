<?php

namespace App\Jobs;

use App\Models\Space;
use App\Models\Workspace;
use App\Services\GeminiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateTeamDigestJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Workspace $workspace, public ?Space $space = null)
    {
    }

    public function handle(GeminiService $gemini): void
    {
        try {
            $summary = $gemini->generateTeamSummary($this->workspace, $this->space);
            Log::info("Asynchronous team summary generated for workspace {$this->workspace->id}");
        } catch (\Throwable $e) {
            Log::error("Failed to generate team digest for workspace {$this->workspace->id}: " . $e->getMessage());
        }
    }
}
