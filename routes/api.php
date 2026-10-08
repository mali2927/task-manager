<?php

use App\Models\Space;
use App\Models\Task;
use App\Models\Workspace;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// Internal AI endpoints (authenticated via web session or token)
Route::middleware(['web', 'auth'])->prefix('ai')->group(function () {
    Route::post('/task/{task}/summarize', function (Task $task, GeminiService $gemini) {
        $summary = $gemini->summarizeTask($task);
        return response()->json(['summary' => $summary]);
    });

    Route::post('/workspace/{workspace}/team-summary', function (Workspace $workspace, Request $request, GeminiService $gemini) {
        $spaceId = $request->input('space_id');
        $space = $spaceId ? Space::find($spaceId) : null;
        $summary = $gemini->generateTeamSummary($workspace, $space);
        return response()->json(['summary' => $summary]);
    });

    Route::post('/workspace/{workspace}/standup', function (Workspace $workspace, Request $request, GeminiService $gemini) {
        $user = $request->user();
        $summary = $gemini->generateStandupReport($user, $workspace);
        return response()->json(['summary' => $summary]);
    });

    Route::post('/workspace/{workspace}/query', function (Workspace $workspace, Request $request, GeminiService $gemini) {
        $query = $request->validate(['query' => 'required|string|max:500'])['query'];
        $result = $gemini->queryTasks($workspace, $query, $request->user());
        return response()->json(['answer' => $result]);
    });

    Route::post('/suggest', function (Request $request, GeminiService $gemini) {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
        ]);
        $suggestions = $gemini->suggestAttributes($data['title'], $data['description'] ?? null);
        return response()->json($suggestions);
    });
});
