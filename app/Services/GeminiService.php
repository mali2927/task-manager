<?php

namespace App\Services;

use App\Models\Space;
use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    // Ordered pool of models for seamless failover if Google experiences high demand (503)
    protected array $fallbackModels = [
        'gemini-3.6-flash',
        'gemini-3.8-flash',
        'gemini-3.7-flash',
        'gemini-3-flash-preview',
        'gemini-3.1-flash-lite-preview',
        'gemini-flash-lite-latest',
        'gemini-flash-latest',
    ];

    public function __construct()
    {
        $this->apiKey = (string) (config('services.gemini.api_key') ?: env('OPENAI_API_KEY') ?: env('GEMINI_API_KEY'));
        $this->model = (string) (config('services.gemini.model') ?: 'gemini-3.6-flash');
        $this->baseUrl = (string) (config('services.gemini.base_url') ?: 'https://generativelanguage.googleapis.com/v1beta');

        // Put primary model first
        $this->fallbackModels = array_unique(array_merge([$this->model], $this->fallbackModels));
    }

    /**
     * Generate a concise status & blockers summary for a specific task.
     * Enforces role hierarchy if a requesting user is provided.
     */
    public function summarizeTask(Task $task, ?User $requestingUser = null): string
    {
        $task->loadMissing('taskList.project.space.workspace');
        $workspace = $task->taskList?->project?->space?->workspace;

        if ($requestingUser && $workspace && !$requestingUser->canAccessTaskInHierarchy($task, $workspace)) {
            $jobTitle = $requestingUser->job_title ?: 'Member';
            return "🔒 **Access Restricted by Role Hierarchy**\n\nYour current role as **{$jobTitle}** does not authorize you to view or summarize this task. Under organization privacy policy, this task is restricted to Director, Team Lead, or designated assignees.";
        }

        $cacheKey = "task_ai_summary_{$task->id}_{$task->updated_at->timestamp}";

        // Check if valid cached summary exists
        $cached = Cache::get($cacheKey);
        if ($cached && !str_starts_with($cached, 'Unable to') && !str_starts_with($cached, 'Error') && !str_starts_with($cached, '🔒')) {
            return $cached;
        }

        $task->loadMissing([
            'taskList.project.space',
            'status',
            'assignees',
            'checklists',
            'comments.user',
            'activities' => fn ($q) => $q->latest()->limit(5),
            'blockedBy.dependsOn',
        ]);

        $checklists = $task->checklists->map(fn ($c) => ($c->is_completed ? '[x] ' : '[ ] ') . $c->title)->implode("\n");
        $comments = $task->comments->take(4)->map(fn ($c) => "- {$c->user->name}: {$c->content}")->implode("\n");
        $activities = $task->activities->map(fn ($a) => "- {$a->description} (" . $a->created_at->diffForHumans() . ")")->implode("\n");
        $blockedBy = $task->blockedBy->map(fn ($d) => $d->dependsOn->title)->implode(', ');

        $prompt = <<<PROMPT
You are an expert AI project assistant. Summarize the following task accurately and concisely.

TASK DETAILS:
- Title: {$task->title}
- Space: {$task->taskList?->project?->space?->name}
- Project / List: {$task->taskList?->project?->name} / {$task->taskList?->name}
- Current Status: {$task->status?->name} ({$task->status?->type})
- Priority: {$task->priority}
- Assignees: {$task->assignees->pluck('name')->implode(', ')}
- Start Date: {$task->start_date?->toDateString()}
- Due Date: {$task->due_date?->toDateString()}
- Description: {$task->description}
- Blocked By: {$blockedBy}

CHECKLIST ITEMS:
{$checklists}

RECENT COMMENTS:
{$comments}

RECENT ACTIVITIES:
{$activities}

Please provide a structured 3-part markdown summary:
1. **Objective & Status**: 1-2 sentence overview of what this task is and where it stands.
2. **Current Blockers & Risks**: Any impediments, dependencies, or missing pieces (or specify "None identified").
3. **Next Steps**: Recommended immediate actions for the assignees.
PROMPT;

        $summary = $this->generate($prompt, 'You are a professional project management assistant who writes clear, concise, and structured summaries.');

        // If Gemini succeeds, cache it for 2 hours
        if ($summary && !str_starts_with($summary, 'Unable to') && !str_starts_with($summary, 'Error')) {
            Cache::put($cacheKey, $summary, now()->addHours(2));
            return $summary;
        }

        // Graceful intelligent synthesis fallback if all external AI endpoints are unreachable
        return $this->synthesizeLocalTaskSummary($task, $blockedBy);
    }

    /**
     * Intelligent local fallback summary when upstream AI API is unreachable.
     */
    protected function synthesizeLocalTaskSummary(Task $task, string $blockedBy): string
    {
        $chkStats = $task->checklistStats();
        $assigneesStr = $task->assignees->pluck('name')->implode(', ') ?: 'Unassigned';
        $dueStr = $task->due_date ? $task->due_date->format('M j, Y') : 'No due date';
        $statusName = $task->status?->name ?? 'Open';

        $blockersText = !empty($blockedBy)
            ? "* Blocked by dependency: **{$blockedBy}**"
            : ($task->status?->type === 'blocked'
                ? "* Marked as **Blocked**. Review recent comments for impediment details."
                : ($task->isOverdue() ? "* ⚠️ Task is **Overdue** (Due {$dueStr})." : "* None identified."));

        $progressText = $chkStats['total'] > 0
            ? "Checklist progress is at **{$chkStats['completed']}/{$chkStats['total']}** ({$chkStats['percent']}%)."
            : "No checklist milestones registered.";

        return <<<SUMMARY
### 1. Objective & Status
This **{$task->priority}** priority task is currently **{$statusName}** and assigned to **{$assigneesStr}**. {$progressText}

### 2. Current Blockers & Risks
{$blockersText}

### 3. Next Steps
* Complete remaining checklist items and verify acceptance criteria.
* Coordinate with **{$assigneesStr}** to finalize review before **{$dueStr}**.
SUMMARY;
    }

    /**
     * Generate a natural-language pending task digest for a specific user.
     */
    public function generateUserDigest(User $user, Workspace $workspace): string
    {
        $tasks = Task::whereHas('taskList.project.space', function ($q) use ($workspace) {
            $q->where('workspace_id', $workspace->id);
        })
        ->whereHas('assignees', fn ($q) => $q->where('users.id', $user->id))
        ->with(['status', 'taskList.project.space'])
        ->get();

        $openTasks = $tasks->filter(fn ($t) => !$t->isDone());
        $overdue = $openTasks->filter(fn ($t) => $t->isOverdue());
        $dueToday = $openTasks->filter(fn ($t) => $t->isDueToday());
        $upcoming = $openTasks->filter(fn ($t) => !$t->isOverdue() && !$t->isDueToday());

        $overdueList = $overdue->map(fn ($t) => "- [{$t->priority}] {$t->title} (Due: {$t->due_date?->toDateString()}, Status: {$t->status?->name})")->implode("\n");
        $todayList = $dueToday->map(fn ($t) => "- [{$t->priority}] {$t->title} (Status: {$t->status?->name})")->implode("\n");
        $upcomingList = $upcoming->take(8)->map(fn ($t) => "- [{$t->priority}] {$t->title} (Due: {$t->due_date?->toDateString()})")->implode("\n");

        $prompt = <<<PROMPT
You are an executive productivity assistant. Write a personal, encouraging, and actionable daily task digest for {$user->name}.

WORKSPACE: {$workspace->name}
TOTAL OPEN TASKS: {$openTasks->count()}
OVERDUE COUNT: {$overdue->count()}
DUE TODAY COUNT: {$dueToday->count()}

OVERDUE TASKS:
{$overdueList}

TASKS DUE TODAY:
{$todayList}

UPCOMING TASKS:
{$upcomingList}

Instructions:
- Write a friendly greeting to {$user->name}.
- Highlight what requires urgent attention first (overdue or high priority due today).
- Give an executive summary of today's focus.
- Keep it concise, motivational, and formatted in clean markdown with bullet points.
PROMPT;

        $res = $this->generate($prompt);

        if ($res && !str_starts_with($res, 'Unable to')) {
            return $res;
        }

        // Local fallback digest
        return "### ☀️ Daily Briefing for {$user->name}\n\nYou have **{$openTasks->count()} open tasks** in {$workspace->name}.\n- **Overdue:** {$overdue->count()} task(s)\n- **Due Today:** {$dueToday->count()} task(s)\n- **Upcoming:** {$upcoming->count()} task(s)\n\n*Prioritize your overdue and high-priority items first to maintain sprint velocity.*";
    }

    /**
     * Aggregated team/space summary for managers.
     * Restricted to Director and Team Lead roles.
     */
    public function generateTeamSummary(Workspace $workspace, ?Space $space = null, ?User $requestingUser = null): string
    {
        if ($requestingUser) {
            $tier = $requestingUser->getAiHierarchyTier($workspace);
            if ($tier === 'member') {
                $jobTitle = $requestingUser->job_title ?: 'Member';
                return "🔒 **Access Restricted by Role Hierarchy**\n\nGenerating workspace-wide or team executive summaries is restricted to Team Leads and Directors. As a **{$jobTitle}**, you can review your personal workload and daily briefing under **My Tasks**.";
            }
        }

        $query = Task::whereHas('taskList.project.space', function ($q) use ($workspace, $space) {
            $q->where('workspace_id', $workspace->id);
            if ($space) {
                $q->where('spaces.id', $space->id);
            }
        })->with(['status', 'assignees', 'taskList.project.space']);

        $allTasks = $query->get();
        $total = $allTasks->count();
        $doneCount = $allTasks->filter(fn ($t) => $t->isDone())->count();
        $rate = $total > 0 ? round(($doneCount / $total) * 100) : 0;
        $overdueCount = $allTasks->filter(fn ($t) => $t->isOverdue())->count();
        $blockedTasks = $allTasks->filter(fn ($t) => $t->status?->type === 'blocked' || $t->blockedBy()->exists());

        $statusCounts = $allTasks->groupBy(fn ($t) => $t->status?->name ?? 'Unassigned')
            ->map(fn ($g) => $g->count())->toJson();

        $blockedList = $blockedTasks->take(5)->map(fn ($t) => "- {$t->title} (Assigned to: " . $t->assignees->pluck('name')->implode(', ') . ")")->implode("\n");

        $scopeName = $space ? "Space: {$space->name}" : "Workspace: {$workspace->name}";

        $prompt = <<<PROMPT
You are a senior technical program manager. Provide an executive summary of progress, velocity, and risks for the team.

SCOPE: {$scopeName}
TOTAL TASKS: {$total}
COMPLETED: {$doneCount} ({$rate}%)
OVERDUE: {$overdueCount}
STATUS DISTRIBUTION: {$statusCounts}

KEY BLOCKED OR BOTTLENECKED TASKS:
{$blockedList}

Generate a manager-friendly briefing:
1. **Executive Snapshot**: Overall delivery health (On Track, At Risk, Needs Attention) and overall throughput.
2. **Key Progress & Velocity**: Accomplishments and sprint momentum.
3. **Bottlenecks & Blockers**: Attention needed for overdue or blocked items.
4. **Actionable Recommendations**: 2-3 specific recommendations for team leads.
Keep the tone professional, direct, and structured in markdown.
PROMPT;

        $res = $this->generate($prompt);

        if ($res && !str_starts_with($res, 'Unable to')) {
            return $res;
        }

        return "### 📊 Executive Team Summary - {$scopeName}\n\n- **Overall Throughput:** {$doneCount}/{$total} tasks completed ({$rate}%)\n- **Overdue Items:** {$overdueCount}\n- **Active Blockers:** {$blockedTasks->count()}\n\n*Recommendation:* Conduct an impedance sync on blocked tasks to unblock downstream dependencies.";
    }

    /**
     * Generate daily/weekly standup report for a user or team.
     * Enforces that members can only generate standup reports for themselves.
     */
    public function generateStandupReport(User $user, Workspace $workspace, ?User $requestingUser = null): string
    {
        if ($requestingUser && $requestingUser->id !== $user->id) {
            $reqTier = $requestingUser->getAiHierarchyTier($workspace);
            if ($reqTier === 'member') {
                $jobTitle = $requestingUser->job_title ?: 'Member';
                return "🔒 **Access Restricted by Role Hierarchy**\n\nAs a **{$jobTitle}**, you can only generate standup reports for yourself. You do not have permission to view or generate standup reports for other team members or Directors.";
            }
        }

        $tasks = Task::whereHas('taskList.project.space', function ($q) use ($workspace) {
            $q->where('workspace_id', $workspace->id);
        })
        ->whereHas('assignees', fn ($q) => $q->where('users.id', $user->id))
        ->with(['status', 'activities'])
        ->get();

        $completed = $tasks->filter(fn ($t) => $t->isDone());
        $inProgress = $tasks->filter(fn ($t) => $t->status?->type === 'in_progress');
        $blocked = $tasks->filter(fn ($t) => $t->status?->type === 'blocked' || $t->isOverdue());

        $doneStr = $completed->take(5)->map(fn ($t) => "- [DONE] {$t->title}")->implode("\n") ?: "- No completed tasks logged in the current cycle";
        $inProgStr = $inProgress->take(5)->map(fn ($t) => "- [IN PROGRESS] {$t->title}")->implode("\n") ?: "- No tasks currently marked in progress";
        $blockedStr = $blocked->take(5)->map(fn ($t) => "- [BLOCKER] {$t->title} ({$t->status?->name})")->implode("\n") ?: "- None";

        $prompt = <<<PROMPT
Format a clean, professional Agile Daily Standup report for {$user->name} in workspace "{$workspace->name}".

RECENTLY COMPLETED:
{$doneStr}

CURRENTLY IN PROGRESS:
{$inProgStr}

BLOCKERS / ATTENTION NEEDED:
{$blockedStr}

Format strictly as:
### 🚀 Daily Standup - {$user->name}
**1. What I accomplished:**
(Bullets with brief context)

**2. What I am working on today:**
(Bullets with expected milestones)

**3. Blockers & Help Needed:**
(Bullets or "None")
PROMPT;

        $res = $this->generate($prompt);

        if ($res && !str_starts_with($res, 'Unable to')) {
            return $res;
        }

        return "### 🚀 Daily Standup - {$user->name}\n\n**1. What I accomplished:**\n{$doneStr}\n\n**2. What I am working on today:**\n{$inProgStr}\n\n**3. Blockers & Help Needed:**\n{$blockedStr}";
    }

    /**
     * Fetch tasks strictly filtered by the user's role hierarchy level in the workspace:
     * - 'director': Full workspace access
     * - 'lead': Tasks assigned to, created by, or watched by the lead and members of their led teams
     * - 'member': STRICTLY tasks where user is assignee, creator, or watcher
     */
    public function getAccessibleTasksForAi(Workspace $workspace, ?User $currentUser): \Illuminate\Database\Eloquent\Collection
    {
        if (!$currentUser) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        $tier = $currentUser->getAiHierarchyTier($workspace);

        $baseQuery = Task::whereHas('taskList.project.space', function ($q) use ($workspace) {
            $q->where('workspace_id', $workspace->id);
        })
        ->with(['status', 'assignees', 'creator', 'taskList.project.space', 'tags']);

        // 1. Director / Executive / Admin: Complete workspace visibility
        if ($tier === 'director') {
            return $baseQuery->get();
        }

        // 2. Team Lead: Visibility of own tasks and team members' tasks within led teams
        if ($tier === 'lead') {
            $teamIds = $currentUser->teams()
                ->where('teams.workspace_id', $workspace->id)
                ->wherePivot('role', 'lead')
                ->pluck('teams.id');

            $teamMemberIds = \App\Models\TeamMember::whereIn('team_id', $teamIds)->pluck('user_id')->push($currentUser->id)->unique();

            return $baseQuery->where(function ($q) use ($currentUser, $teamMemberIds) {
                $q->whereHas('assignees', fn ($sq) => $sq->whereIn('users.id', $teamMemberIds))
                  ->orWhere('created_by_id', $currentUser->id)
                  ->orWhereHas('watchers', fn ($sq) => $sq->where('users.id', $currentUser->id));
            })->get();
        }

        // 3. Member / Software Engineer / QA: STRICTLY own tasks only
        return $baseQuery->where(function ($q) use ($currentUser) {
            $q->whereHas('assignees', fn ($sq) => $sq->where('users.id', $currentUser->id))
              ->orWhere('created_by_id', $currentUser->id)
              ->orWhereHas('watchers', fn ($sq) => $sq->where('users.id', $currentUser->id));
        })->get();
    }

    /**
     * Answer natural language queries about tasks in the workspace, strictly enforcing role hierarchy.
     */
    public function queryTasks(Workspace $workspace, string $query, ?User $currentUser = null): string
    {
        $tier = $currentUser ? $currentUser->getAiHierarchyTier($workspace) : 'member';
        $userLabel = $currentUser ? $currentUser->getAiHierarchyLabel($workspace) : 'Anonymous Member';
        $jobTitle = $currentUser?->job_title ?: 'Member';

        // 1. Fetch accessible tasks strictly filtered by role hierarchy
        $tasks = $this->getAccessibleTasksForAi($workspace, $currentUser);

        $structuredTasks = $tasks->take(60)->map(function ($t) {
            return [
                'id' => $t->id,
                'title' => $t->title,
                'status' => $t->status?->name,
                'status_type' => $t->status?->type,
                'priority' => $t->priority,
                'assignees' => $t->assignees->pluck('name')->all(),
                'creator' => $t->creator?->name,
                'space' => $t->taskList?->project?->space?->name,
                'project' => $t->taskList?->project?->name,
                'due_date' => $t->due_date?->toDateString(),
                'is_overdue' => $t->isOverdue(),
                'tags' => $t->tags->pluck('name')->all(),
            ];
        })->toJson();

        $hierarchyInstructions = match ($tier) {
            'director' => <<<INSTR
The requesting user is a DIRECTOR / EXECUTIVE / ADMIN ({$userLabel}).
- They have UNRESTRICTED, WORKSPACE-WIDE authorization to view all projects, spaces, teams, members, tasks, and blockers.
- Answer all queries fully with complete visibility into the workspace data provided.
INSTR,
            'lead' => <<<INSTR
The requesting user is a TEAM LEAD / MANAGER ({$userLabel}).
- They have authorization to view tasks for themselves and their supervised team members.
- They CANNOT view unrelated teams or confidential higher-level executive (Director/CEO) tasks that do not involve their team.
- If asked about higher-level or other unrelated teams' records, inform them that those records belong to other spaces/executives and are outside their team lead scope.
INSTR,
            default => <<<INSTR
The requesting user is a LOWER-HIERARCHY MEMBER / ENGINEER / QA ({$userLabel}).
- STRICT ACCESS ENFORCEMENT: This user is authorized to view ONLY their own personal records (tasks assigned to them, created by them, or watched by them).
- The ground truth data provided above contains ONLY tasks this user has permission to see.
- THEY CANNOT SEE DIRECTOR, TEAM LEAD, CEO, OR OTHER COLLEAGUES' RECORDS.
- If the user asks about their own tasks, deadlines, checklists, or personal workload: answer helpfully using the provided data.
- CRITICAL PRIVACY RULE: If the user asks to see 'all tasks', 'workspace overview', 'Director Khubaib tasks', 'Team Lead Khurram tasks', 'CEO tasks', or any other member's tasks, you MUST DECLINE politely and firmly:
  "🔒 Under the workspace role-based access hierarchy, your access level ({$jobTitle}) authorizes you to view only your own assigned, created, and watched tasks. You do not have permission to view Director, Team Lead, CEO, or other team members' records."
INSTR
        };

        $prompt = <<<PROMPT
You are an intelligent task manager assistant with strict Role-Based Access Control (RBAC).

REQUESTING USER CONTEXT:
- Name: {$currentUser?->name}
- Role Hierarchy Tier: {$tier} ({$userLabel})
- Guidelines:
{$hierarchyInstructions}

USER QUESTION:
"{$query}"

AUTHORIZED GROUND-TRUTH TASK DATA:
{$structuredTasks}

INSTRUCTIONS:
- Adhere strictly to the Role Hierarchy Guidelines above. Never bypass privacy boundaries.
- Answer based ONLY on the authorized ground-truth tasks provided.
- If the user is asking for records outside their authorization tier, explain the role hierarchy limitation clearly and politely.
- Format responses in clean GitHub markdown with bold titles and bullet points.
PROMPT;

        $res = $this->generate($prompt, 'You are a professional project management AI assistant that strictly enforces role-based access control and organizational hierarchy.');

        if ($res && !str_starts_with($res, 'Unable to') && !str_starts_with($res, 'Error')) {
            return $res;
        }

        // Intelligent local fallback that ALSO enforces role hierarchy
        return $this->synthesizeLocalQueryAnswer($tasks, $query, $workspace, $currentUser, $tier);
    }

    /**
     * Local semantic synthesis fallback when upstream AI API is unreachable/overloaded (503).
     * Strictly enforces the role hierarchy policy.
     */
    public function synthesizeLocalQueryAnswer(
        \Illuminate\Database\Eloquent\Collection $tasks,
        string $query,
        Workspace $workspace,
        ?User $currentUser,
        string $tier
    ): string {
        $qLower = strtolower($query);
        $jobTitle = $currentUser?->job_title ?: 'Member';

        // 1. Member tier privacy check
        if ($tier === 'member') {
            $unauthorizedKeywords = [
                'khubaib', 'director', 'khurram', 'lead', 'ceo', 'alex', 'sarah', 'marcus', 'elena',
                'all tasks', 'everyone', 'all project', 'workspace overview', 'whole team', 'entire team'
            ];

            foreach ($unauthorizedKeywords as $kw) {
                if (str_contains($qLower, $kw)) {
                    $myTaskCount = $tasks->count();
                    $taskListStr = $tasks->take(5)->map(fn ($t) => "* **{$t->title}** — {$t->status?->name} ({$t->priority} priority)")->implode("\n");

                    return <<<ANSWER
### 🔒 Access Restricted by Role Hierarchy

Under the workspace Role-Based Access Control policy, your role as **{$jobTitle}** authorizes you to view **only your own assigned, created, and watched tasks**.

You do not have administrative permission to view Director, Team Lead, CEO, or other colleagues' task records.

---
### 📋 Your Authorized Personal Tasks ({$myTaskCount}):
{$taskListStr}
ANSWER;
                }
            }
        }

        // 2. Team Lead tier check
        if ($tier === 'lead') {
            $executiveKeywords = ['ceo', 'alex rivers', 'founder', 'board', 'director'];
            foreach ($executiveKeywords as $kw) {
                if (str_contains($qLower, $kw)) {
                    return "### 🔒 Scope Notice\nAs a **Team Lead**, you have access to your team's assigned projects and members. Higher-level executive / CEO confidential records are outside team scope.";
                }
            }
        }

        // 3. Blocked tasks
        if (str_contains($qLower, 'block') || str_contains($qLower, 'impediment')) {
            $blocked = $tasks->filter(fn ($t) => $t->status?->type === 'blocked' || $t->blockedBy()->exists());
            if ($blocked->isEmpty()) {
                return "### 🛑 Active Blocked Tasks\nNo blocked tasks found within your authorized scope.";
            }
            $list = $blocked->map(fn ($t) => "* **{$t->title}**\n  * Status: {$t->status?->name}\n  * Priority: {$t->priority}\n  * Space: {$t->taskList?->project?->space?->name}")->implode("\n\n");
            return "### 🛑 Active Blocked Tasks (Authorized Scope)\nHere are the blocked tasks within your access level:\n\n{$list}";
        }

        // 4. Urgent / Overdue tasks
        if (str_contains($qLower, 'urgent') || str_contains($qLower, 'overdue') || str_contains($qLower, 'deadline')) {
            $urgent = $tasks->filter(fn ($t) => $t->priority === 'urgent' || $t->isOverdue());
            if ($urgent->isEmpty()) {
                return "### ⚡ Urgent Tasks\nNo urgent or overdue tasks found within your authorized scope.";
            }
            $list = $urgent->map(fn ($t) => "* **{$t->title}**\n  * Due: " . ($t->due_date ? $t->due_date->format('M d, Y') : 'No due date') . "\n  * Status: {$t->status?->name}\n  * Priority: {$t->priority}")->implode("\n\n");
            return "### ⚡ Urgent & High Priority Items (Authorized Scope)\n\n{$list}";
        }

        // 5. Default overview for authorized tasks
        $total = $tasks->count();
        $inProg = $tasks->filter(fn ($t) => $t->status?->type === 'in_progress')->count();
        $done = $tasks->filter(fn ($t) => $t->isDone())->count();

        $items = $tasks->take(6)->map(fn ($t) => "* **{$t->title}** — {$t->status?->name} ({$t->priority}) [{$t->taskList?->project?->name}]")->implode("\n");

        $scopeTitle = $tier === 'director' ? 'Full Workspace' : ($tier === 'lead' ? 'Team Scope' : 'Personal Scope');

        return <<<ANSWER
### 📋 Tasks Overview ({$scopeTitle})
You have access to **{$total} tasks** ({$done} Done, {$inProg} In Progress):

{$items}
ANSWER;
    }

    /**
     * Smart suggestions for priority and tags based on title/description.
     */
    public function suggestAttributes(string $title, ?string $description = null): array
    {
        $prompt = <<<PROMPT
Analyze this task title and description, and suggest the most suitable priority and 2-3 relevant tags:
Title: {$title}
Description: {$description}

Allowed priorities: "urgent", "high", "normal", "low".
Common tags: Bug, Feature, UI/UX, Backend, Frontend, DevOps, Documentation, Security, Performance, Refactor.

Return ONLY a valid JSON object in this exact format:
{
  "priority": "high",
  "tags": ["Bug", "Frontend"],
  "estimated_hours": 4
}
PROMPT;

        try {
            $response = $this->generate($prompt);
            $clean = trim($response);
            $clean = preg_replace('/^```json/i', '', $clean);
            $clean = preg_replace('/```$/', '', $clean);
            $parsed = json_decode(trim($clean), true);

            if (is_array($parsed) && isset($parsed['priority'])) {
                return $parsed;
            }
        } catch (\Throwable $e) {
            Log::warning("Gemini suggestAttributes fallback: " . $e->getMessage());
        }

        return [
            'priority' => 'normal',
            'tags' => ['Task'],
            'estimated_hours' => 2,
        ];
    }

    /**
     * Robust API call with multi-model failover and retries.
     */
    public function generate(string $prompt, ?string $systemInstruction = null): string
    {
        if (empty($this->apiKey)) {
            return "Gemini API key is not configured. Please set `GEMINI_API_KEY` in your `.env` file to enable AI features.";
        }

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 1200,
            ],
        ];

        if ($systemInstruction) {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemInstruction],
                ],
            ];
        }

        // Iterate through fallback models if the active model is experiencing high demand (503/429)
        foreach ($this->fallbackModels as $modelCandidate) {
            $url = "{$this->baseUrl}/models/{$modelCandidate}:generateContent?key={$this->apiKey}";

            for ($attempt = 1; $attempt <= 2; $attempt++) {
                try {
                    $response = Http::timeout(12)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post($url, $payload);

                    if ($response->successful()) {
                        $data = $response->json();
                        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                        if (!empty($text)) {
                            return trim($text);
                        }
                    }

                    $status = $response->status();

                    // If temporary capacity/rate issue, backoff and retry or try next model
                    if ($status === 503 || $status === 429) {
                        Log::warning("Gemini model {$modelCandidate} returned {$status} (Attempt {$attempt}). Failover proceeding...");
                        if ($attempt < 2) {
                            usleep(400000); // 400ms pause
                            continue;
                        }
                        break; // Try next model candidate
                    }

                    Log::error("Gemini API error on {$modelCandidate} ({$status}): " . $response->body());
                    break;
                } catch (\Throwable $e) {
                    Log::warning("Gemini connection exception on {$modelCandidate}: " . $e->getMessage());
                    if ($attempt < 2) {
                        usleep(300000);
                        continue;
                    }
                    break;
                }
            }
        }

        return "Unable to generate AI summary at this moment. The model service is currently experiencing high demand. Please try again in a few seconds.";
    }
}
