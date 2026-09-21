<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Space;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\Ticket;
use App\Models\TicketActivityLog;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    // Ordered pool of models for seamless failover, prioritizing fastest verified endpoints
    protected array $fallbackModels = [
        'gemini-flash-lite-latest',
        'gemini-3-flash-preview',
        'gemini-3.8-flash',
        'gemini-3.6-flash',
        'gemini-3.1-flash-lite-preview',
        'gemini-3.7-flash',
        'gemini-flash-latest',
    ];

    public function __construct()
    {
        $this->apiKey = (string) (config('services.gemini.api_key') ?: env('OPENAI_API_KEY') ?: env('GEMINI_API_KEY'));
        $this->model = (string) (config('services.gemini.model') ?: 'gemini-flash-lite-latest');
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

        if ($summary && !str_starts_with($summary, 'Unable to') && !str_starts_with($summary, 'Error')) {
            Cache::put($cacheKey, $summary, now()->addHours(2));
            return $summary;
        }

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
        ->with(['status', 'assignees', 'creator', 'taskList.project.space', 'tags', 'blockedBy.dependsOn']);

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

            $teamMemberIds = TeamMember::whereIn('team_id', $teamIds)->pluck('user_id')->push($currentUser->id)->unique();

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
     * Fetch support tickets strictly filtered by role hierarchy:
     * - 'director': All tickets in workspace
     * - 'lead': Tickets assigned to user, raised by user, assigned to led teams or their team members
     * - 'member': Tickets assigned to user or raised by user
     * - 'requester' / 'guest': ONLY tickets raised by user
     */
    public function getAccessibleTicketsForAi(Workspace $workspace, ?User $currentUser): \Illuminate\Database\Eloquent\Collection
    {
        if (!$currentUser) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        $tier = $currentUser->getAiHierarchyTier($workspace);

        $baseQuery = Ticket::where('workspace_id', $workspace->id)
            ->with(['category', 'raisedBy', 'assignedTo', 'assignedTeam', 'comments.user']);

        // 1. Director: Full ticket overview
        if ($tier === 'director') {
            return $baseQuery->get();
        }

        // 2. Team Lead
        if ($tier === 'lead') {
            $teamIds = $currentUser->teams()
                ->where('teams.workspace_id', $workspace->id)
                ->wherePivot('role', 'lead')
                ->pluck('teams.id');

            $teamMemberIds = TeamMember::whereIn('team_id', $teamIds)->pluck('user_id')->push($currentUser->id)->unique();

            return $baseQuery->where(function ($q) use ($currentUser, $teamIds, $teamMemberIds) {
                $q->where('raised_by_user_id', $currentUser->id)
                  ->orWhere('assigned_to_user_id', $currentUser->id)
                  ->orWhereIn('assigned_team_id', $teamIds)
                  ->orWhereIn('assigned_to_user_id', $teamMemberIds);
            })->get();
        }

        // 3. Member / Staff: Assigned or raised
        return $baseQuery->where(function ($q) use ($currentUser) {
            $q->where('raised_by_user_id', $currentUser->id)
              ->orWhere('assigned_to_user_id', $currentUser->id);
        })->get();
    }

    /**
     * Assemble holistic project, ticket, team, activity, and capacity intelligence.
     * Powers both Gemini prompt synthesis and local deterministic fallback.
     */
    public function assembleWorkspaceIntelligenceContext(Workspace $workspace, ?User $currentUser): array
    {
        $tier = $currentUser ? $currentUser->getAiHierarchyTier($workspace) : 'member';
        $userLabel = $currentUser ? $currentUser->getAiHierarchyLabel($workspace) : 'Anonymous Member';
        $jobTitle = $currentUser?->job_title ?: 'Member';

        // 1. Tasks
        $tasks = $this->getAccessibleTasksForAi($workspace, $currentUser);
        $totalTasks = $tasks->count();
        $doneTasks = $tasks->filter(fn ($t) => $t->isDone());
        $openTasks = $tasks->filter(fn ($t) => !$t->isDone());
        $overdueTasks = $openTasks->filter(fn ($t) => $t->isOverdue());
        $blockedTasks = $tasks->filter(fn ($t) => $t->status?->type === 'blocked' || $t->blockedBy->isNotEmpty());

        // 2. Tickets
        $tickets = $this->getAccessibleTicketsForAi($workspace, $currentUser);
        $totalTickets = $tickets->count();
        $openTickets = $tickets->filter(fn ($t) => !in_array($t->status, ['resolved', 'closed']));
        $reopenedTickets = $tickets->filter(fn ($t) => $t->status === 'reopened');
        $overdueTickets = $tickets->filter(fn ($t) => $t->isOverdue());

        // 3. Workload per team member (Authorized view)
        $teamsWorkload = [];
        if ($tier === 'director' || $tier === 'lead') {
            $members = DB::table('workspace_members')
                ->join('users', 'workspace_members.user_id', '=', 'users.id')
                ->where('workspace_members.workspace_id', $workspace->id)
                ->select('users.id', 'users.name', 'workspace_members.role', 'workspace_members.job_title')
                ->get();

            foreach ($members as $m) {
                $assignedTasksCount = DB::table('task_assignees')
                    ->join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
                    ->join('task_lists', 'tasks.task_list_id', '=', 'task_lists.id')
                    ->join('projects', 'task_lists.project_id', '=', 'projects.id')
                    ->join('spaces', 'projects.space_id', '=', 'spaces.id')
                    ->where('spaces.workspace_id', $workspace->id)
                    ->where('task_assignees.user_id', $m->id)
                    ->count();

                $doneCount = DB::table('task_assignees')
                    ->join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
                    ->join('task_statuses', 'tasks.status_id', '=', 'task_statuses.id')
                    ->join('task_lists', 'tasks.task_list_id', '=', 'task_lists.id')
                    ->join('projects', 'task_lists.project_id', '=', 'projects.id')
                    ->join('spaces', 'projects.space_id', '=', 'spaces.id')
                    ->where('spaces.workspace_id', $workspace->id)
                    ->where('task_assignees.user_id', $m->id)
                    ->where('task_statuses.type', 'done')
                    ->count();

                $assignedTicketsCount = Ticket::where('workspace_id', $workspace->id)
                    ->where('assigned_to_user_id', $m->id)
                    ->count();

                $cap = DB::table('team_members')
                    ->join('teams', 'team_members.team_id', '=', 'teams.id')
                    ->where('teams.workspace_id', $workspace->id)
                    ->where('team_members.user_id', $m->id)
                    ->value('capacity_limit') ?? 5;

                if ($assignedTasksCount > 0 || $assignedTicketsCount > 0 || in_array($m->role, ['owner', 'admin'])) {
                    $teamsWorkload[] = [
                        'user_id' => $m->id,
                        'name' => $m->name,
                        'role' => $m->role,
                        'assigned_tasks' => $assignedTasksCount,
                        'completed_tasks' => $doneCount,
                        'open_tasks' => $assignedTasksCount - $doneCount,
                        'assigned_tickets' => $assignedTicketsCount,
                        'capacity_limit' => $cap,
                        'is_saturated' => ($assignedTasksCount - $doneCount) > $cap,
                    ];
                }
            }
        }

        // 4. Activity Logs (Chronological history)
        $taskActivityQuery = TaskActivity::whereHas('task.taskList.project.space', function ($q) use ($workspace) {
            $q->where('workspace_id', $workspace->id);
        })->with(['task', 'user'])->latest('created_at')->limit(12);

        $ticketActivityQuery = TicketActivityLog::whereHas('ticket', function ($q) use ($workspace) {
            $q->where('workspace_id', $workspace->id);
        })->with(['ticket', 'user'])->latest('created_at')->limit(12);

        $timelineEvents = [];
        foreach ($taskActivityQuery->get() as $ta) {
            $timelineEvents[] = [
                'type' => 'Task',
                'time' => $ta->created_at->toDateTimeString(),
                'relative_time' => $ta->created_at->diffForHumans(),
                'item' => "Task #{$ta->task_id} ({$ta->task?->title})",
                'actor' => $ta->user?->name ?? 'System',
                'action' => $ta->action,
                'details' => $ta->description,
            ];
        }
        foreach ($ticketActivityQuery->get() as $tka) {
            $timelineEvents[] = [
                'type' => 'Ticket',
                'time' => $tka->created_at->toDateTimeString(),
                'relative_time' => $tka->created_at->diffForHumans(),
                'item' => "Ticket #{$tka->ticket?->ticket_number} ({$tka->ticket?->subject})",
                'actor' => $tka->user?->name ?? 'System',
                'action' => $tka->action,
                'details' => ($tka->from_value ? "{$tka->from_value} -> " : "") . $tka->to_value,
            ];
        }
        usort($timelineEvents, fn ($a, $b) => strcmp($b['time'], $a['time']));

        // 5. Recent milestones
        $lastTask = $tasks->sortByDesc('created_at')->first();
        $lastTicket = $tickets->sortByDesc('created_at')->first();

        // 6. Project & Space breakdown
        $projectBreakdown = [];
        $spaces = Space::where('workspace_id', $workspace->id)->with('projects.lists.tasks.status')->get();
        foreach ($spaces as $sp) {
            foreach ($sp->projects as $pr) {
                $prTasks = $pr->lists->flatMap->tasks;
                $prTotal = $prTasks->count();
                $prDone = $prTasks->filter(fn ($t) => $t->status?->type === 'done')->count();
                if ($prTotal > 0) {
                    $projectBreakdown[] = [
                        'space' => $sp->name,
                        'project' => $pr->name,
                        'total' => $prTotal,
                        'done' => $prDone,
                        'open' => $prTotal - $prDone,
                        'rate' => round(($prDone / $prTotal) * 100) . '%',
                    ];
                }
            }
        }

        // 7. Unassigned items
        $unassignedTasks = $tasks->filter(fn ($t) => $t->assignees->isEmpty());
        $unassignedTickets = $tickets->filter(fn ($t) => empty($t->assigned_to_user_id) && empty($t->assigned_team_id));

        return [
            'user_context' => [
                'name' => $currentUser?->name,
                'tier' => $tier,
                'label' => $userLabel,
                'job_title' => $jobTitle,
            ],
            'derived_metrics' => [
                'last_task' => $lastTask ? [
                    'id' => $lastTask->id,
                    'title' => $lastTask->title,
                    'status' => $lastTask->status?->name,
                    'priority' => $lastTask->priority,
                    'creator' => $lastTask->creator?->name,
                    'assignees' => $lastTask->assignees->pluck('name')->implode(', ') ?: 'Unassigned',
                    'created_at' => (string) $lastTask->created_at,
                    'time_ago' => $lastTask->created_at->diffForHumans(),
                ] : null,
                'last_ticket' => $lastTicket ? [
                    'ticket_number' => $lastTicket->ticket_number,
                    'subject' => $lastTicket->subject,
                    'category' => $lastTicket->category?->name,
                    'status' => $lastTicket->status,
                    'priority' => $lastTicket->priority,
                    'raised_by' => $lastTicket->raisedBy?->name,
                    'assigned_to' => $lastTicket->assignedTo?->name ?: 'Unassigned',
                    'created_at' => (string) $lastTicket->created_at,
                    'time_ago' => $lastTicket->created_at->diffForHumans(),
                    'due_by' => (string) $lastTicket->due_by,
                ] : null,
                'tasks_summary' => [
                    'total' => $totalTasks,
                    'done' => $doneTasks->count(),
                    'open' => $openTasks->count(),
                    'overdue' => $overdueTasks->count(),
                    'blocked' => $blockedTasks->count(),
                    'completion_rate' => $totalTasks > 0 ? round(($doneTasks->count() / $totalTasks) * 100) . '%' : '0%',
                    'status_breakdown' => $tasks->groupBy(fn ($t) => $t->status?->name ?? 'To Do')->map(fn ($g) => $g->count())->toArray(),
                ],
                'tickets_summary' => [
                    'total' => $totalTickets,
                    'open' => $openTickets->count(),
                    'reopened' => $reopenedTickets->count(),
                    'overdue' => $overdueTickets->count(),
                    'status_breakdown' => $tickets->groupBy('status')->map(fn ($g) => $g->count())->toArray(),
                ],
                'overdue_tasks_sample' => $overdueTasks->take(8)->map(fn ($t) => [
                    'id' => $t->id,
                    'title' => $t->title,
                    'due_date' => $t->due_date?->toDateString(),
                    'priority' => $t->priority,
                    'assignees' => $t->assignees->pluck('name')->implode(', '),
                ])->values()->toArray(),
                'blocked_tasks_sample' => $blockedTasks->take(8)->map(fn ($t) => [
                    'id' => $t->id,
                    'title' => $t->title,
                    'priority' => $t->priority,
                    'dependencies' => $t->blockedBy->map(fn ($d) => $d->dependsOn->title)->implode(', ') ?: 'Marked Blocked (External API / Key dependency)',
                ])->values()->toArray(),
                'unassigned_items' => [
                    'tasks' => $unassignedTasks->map(fn ($t) => "#{$t->id}: {$t->title}")->values()->toArray(),
                    'tickets' => $unassignedTickets->map(fn ($tk) => "#{$tk->ticket_number}: {$tk->subject}")->values()->toArray(),
                ],
                'project_breakdown' => $projectBreakdown,
            ],
            'teams_workload' => $teamsWorkload,
            'recent_timeline' => array_slice($timelineEvents, 0, 10),
            'tasks' => $tasks->take(60)->map(fn ($t) => [
                'id' => $t->id,
                'title' => $t->title,
                'status' => $t->status?->name,
                'priority' => $t->priority,
                'project' => $t->taskList?->project?->name,
                'space' => $t->taskList?->project?->space?->name,
                'assignees' => $t->assignees->pluck('name')->all(),
                'due_date' => $t->due_date?->toDateString(),
                'is_overdue' => $t->isOverdue(),
                'is_blocked' => $t->status?->type === 'blocked' || $t->blockedBy->isNotEmpty(),
            ])->toArray(),
            'tickets' => $tickets->map(fn ($tk) => [
                'number' => $tk->ticket_number,
                'subject' => $tk->subject,
                'category' => $tk->category?->name,
                'status' => $tk->status,
                'priority' => $tk->priority,
                'raised_by' => $tk->raisedBy?->name,
                'assigned_to' => $tk->assignedTo?->name ?: 'Unassigned',
                'assigned_team' => $tk->assignedTeam?->name ?: 'Unassigned',
                'created_at' => (string) $tk->created_at,
                'due_by' => (string) $tk->due_by,
                'is_overdue' => $tk->isOverdue(),
            ])->toArray(),
        ];
    }

    /**
     * Answer natural language queries about tasks and support tickets in the workspace,
     * acting as an intelligent Project & Ticket Intelligence Agent.
     */
    public function queryTasks(Workspace $workspace, string $query, ?User $currentUser = null): string
    {
        $context = $this->assembleWorkspaceIntelligenceContext($workspace, $currentUser);
        $tier = $context['user_context']['tier'];
        $userLabel = $context['user_context']['label'];
        $jobTitle = $context['user_context']['job_title'];

        $hierarchyInstructions = match ($tier) {
            'director' => <<<INSTR
The requesting user is a DIRECTOR / EXECUTIVE / ADMIN ({$userLabel}).
- Full workspace-wide authority across all projects, support tickets, teams, workloads, and activity logs.
- Provide comprehensive, analytical, multi-dimensional answers connecting tasks, tickets, SLAs, workloads, and timelines.
INSTR,
            'lead' => <<<INSTR
The requesting user is a TEAM LEAD / MANAGER ({$userLabel}).
- Authorized to view tasks, tickets, and workload for themselves and their supervised team members.
- Cannot inspect unrelated executive-only private discussions or unrelated space internals.
- If asked about records outside their team scope, explain the team-level boundary clearly.
INSTR,
            default => <<<INSTR
The requesting user is a MEMBER / CONTRIBUTOR / QA ({$userLabel}).
- STRICT RBAC ENFORCEMENT: Authorized to view ONLY their personal assigned/created tasks and tickets.
- Do NOT expose other colleagues' private task loads, Director records, or whole-workspace management reviews.
- If the user asks for other colleagues' or workspace-wide management overviews, politely decline:
  "🔒 Under the workspace role-based access policy, your role as {$jobTitle} authorizes you to view only your own assigned and created deliverables."
INSTR
        };

        $contextJson = json_encode($context, JSON_PRETTY_PRINT);

        $systemInstruction = <<<SYS
You are the Senior Project & Ticket Intelligence Agent for workspace "{$workspace->name}".
You are an intelligent project intelligence system, not a basic chatbot or keyword retriever.

CORE OPERATIONAL BEHAVIORS:
1. Think Across the Data:
   Connect information seamlessly across: Tickets, Tasks, Comments, Status changes, Assignees, Teams, Projects, Priorities, Dates, Dependencies, Reassignments, Related issues, Activity history, and Role hierarchy.
2. Calculate Derived Information Proactively:
   Derive aging, resolution speed, backlog changes, completion rates, reopen rates, bottleneck patterns, and capacity saturation.
3. Be Proactive:
   When asked broad questions (e.g. "What happened this week?", "Who was assigned what?", "What is blocked?"), don't merely list records. Show assignment distribution, open vs completed, bottlenecks, comparative trends, and actionable takeaways.
4. Distinguish Facts from Interpretation:
   Always clearly distinguish between:
   - **Observed Facts** (timestamps, exact statuses, logs)
   - **Calculated Metrics** (percentages, velocity, SLAs, aging deltas)
   - **Identified Patterns** (recurring blockers, rapid reopens, capacity bottlenecks)
   - **Possible Explanations** (clearly marked hypotheses)
5. Strictly Respect Organizational Role Hierarchy:
   Adhere to the requesting user's authorization tier ({$tier}). Never bypass boundaries or reveal unauthorized management or peer records.
6. Output Formatting:
   Format responses in clean GitHub markdown with bold section titles, concise bullet points, and markdown tables for comparisons.
SYS;

        $prompt = <<<PROMPT
USER QUESTION:
"{$query}"

REQUESTING USER CONTEXT:
- Name: {$currentUser?->name}
- Authorization Tier: {$tier} ({$userLabel})
- Hierarchy Directives:
{$hierarchyInstructions}

GROUND TRUTH WORKSPACE INTELLIGENCE CONTEXT:
{$contextJson}

Provide the most insightful, accurate, and structured answer possible based strictly on the ground truth data.
PROMPT;

        $res = $this->generate($prompt, $systemInstruction);

        if ($res && !str_starts_with($res, 'Unable to') && !str_starts_with($res, 'Error')) {
            return $res;
        }

        // Intelligent local fallback synthesis with rich derived metrics
        return $this->synthesizeLocalQueryAnswer($context, $query, $workspace, $currentUser, $tier);
    }

    /**
     * Local semantic synthesis fallback when upstream AI API is unreachable/overloaded.
     * Generates rich, data-driven analytical summaries strictly observing role hierarchy.
     */
    public function synthesizeLocalQueryAnswer(
        array $context,
        string $query,
        Workspace $workspace,
        ?User $currentUser,
        string $tier
    ): string {
        $qLower = strtolower($query);
        $jobTitle = $context['user_context']['job_title'];
        $derived = $context['derived_metrics'];
        $tasksSummary = $derived['tasks_summary'];
        $ticketsSummary = $derived['tickets_summary'];

        // 1. Role hierarchy privacy refusal for members inquiring about restricted peers/directors
        if ($tier === 'member') {
            $unauthorizedKeywords = [
                'khubaib', 'director', 'khurram', 'lead', 'ceo', 'alex', 'sarah', 'marcus', 'elena',
                'all tasks', 'everyone', 'all project', 'workspace overview', 'whole team', 'entire team'
            ];

            foreach ($unauthorizedKeywords as $kw) {
                if (str_contains($qLower, $kw)) {
                    $myTaskCount = count($context['tasks']);
                    $taskListStr = collect($context['tasks'])->take(5)
                        ->map(fn ($t) => "* **{$t['title']}** — {$t['status']} ({$t['priority']} priority)")
                        ->implode("\n");

                    return <<<ANSWER
### 🔒 Access Restricted by Role Hierarchy

Under the workspace Role-Based Access Control policy, your current role as **{$jobTitle}** authorizes you to view **only your own assigned and created tasks & support tickets**.

You do not have administrative permission to inspect Director, Team Lead, CEO, or other colleagues' internal records.

---
### 📋 Your Authorized Personal Records ({$myTaskCount} items):
{$taskListStr}
ANSWER;
                }
            }
        }

        // 2. Latest ticket inquiry
        if (str_contains($qLower, 'latest ticket') || str_contains($qLower, 'last ticket') || str_contains($qLower, 'recent ticket')) {
            $lt = $derived['last_ticket'];
            if (!$lt) {
                return "### 🎫 Support Ticket Intelligence\nNo support tickets have been opened in this workspace.";
            }

            return <<<ANSWER
### 🎫 Latest Support Ticket Intelligence

* **Ticket Number:** `#{$lt['ticket_number']}`
* **Subject:** {$lt['subject']}
* **Category:** {$lt['category']}
* **Priority:** `{$lt['priority']}`
* **Current Status:** `{$lt['status']}`
* **Raised By:** {$lt['raised_by']}
* **Assigned To:** {$lt['assigned_to']}
* **Created:** {$lt['created_at']} ({$lt['time_ago']})
* **SLA Due By:** {$lt['due_by']}

**Proactive Context:**
* Total tickets in workspace: **{$ticketsSummary['total']}**
* Active ticket backlog: **{$ticketsSummary['open']}**
* Reopened tickets: **{$ticketsSummary['reopened']}**
ANSWER;
        }

        // 3. Latest task inquiry
        if (str_contains($qLower, 'latest task') || str_contains($qLower, 'last task') || str_contains($qLower, 'recent task')) {
            $lt = $derived['last_task'];
            if (!$lt) {
                return "### 📋 Task Intelligence\nNo tasks found in this workspace.";
            }

            return <<<ANSWER
### 📋 Latest Task Intelligence

* **Task ID:** `#{$lt['id']}`
* **Title:** {$lt['title']}
* **Priority:** `{$lt['priority']}`
* **Current Status:** `{$lt['status']}`
* **Created By:** {$lt['creator']}
* **Assignees:** {$lt['assignees']}
* **Created At:** {$lt['created_at']} ({$lt['time_ago']})
ANSWER;
        }

        // 4. Workload, Capacity & Assignment queries
        if (str_contains($qLower, 'workload') || str_contains($qLower, 'assigned') || str_contains($qLower, 'capacity') || str_contains($qLower, 'who was assigned')) {
            if (empty($context['teams_workload'])) {
                $myTasks = collect($context['tasks']);
                return "### 👥 Personal Workload Overview\nYou have **{$myTasks->count()} assigned tasks** (" . $myTasks->where('is_overdue', true)->count() . " overdue).";
            }

            $rows = '';
            foreach ($context['teams_workload'] as $w) {
                $satFlag = $w['is_saturated'] ? '⚠️ **Over Limit**' : '✅ Normal';
                $rows .= "| **{$w['name']}** | {$w['role']} | {$w['assigned_tasks']} | {$w['completed_tasks']} | {$w['open_tasks']} | {$w['assigned_tickets']} | {$w['capacity_limit']} | {$satFlag} |\n";
            }

            return <<<ANSWER
### 👥 Team Workload & Capacity Analysis

| Team Member | Role | Tasks Assigned | Completed | Open Backlog | Tickets | Capacity Limit | Status |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :--- |
{$rows}

**Key Observations:**
* Top assignees hold the majority of active backlog tasks.
* Capacity limits (default 5 items) are enforced to alert leads on potential delivery bottlenecks.
ANSWER;
        }

        // 5. Blocked tasks & dependencies
        if (str_contains($qLower, 'block') || str_contains($qLower, 'impediment') || str_contains($qLower, 'dependenc')) {
            $blocked = $derived['blocked_tasks_sample'];
            if (empty($blocked)) {
                return "### 🛑 Active Blockers & Dependencies\nNo tasks or tickets are currently marked as blocked within your scope.";
            }

            $list = '';
            foreach ($blocked as $b) {
                $list .= "* **Task #{$b['id']}: {$b['title']}** (`{$b['priority']}`)\n  * **Blocker / Dependency:** {$b['dependencies']}\n";
            }

            $count = $tasksSummary['blocked'];
            return <<<ANSWER
### 🛑 Active Blockers & External Dependencies ({$count} Total)

The following items are currently halted awaiting external dependencies or security keys:

{$list}

**Recommended Action:** Coordinate with integration partners or provide test sandbox stubs to unblock dependent enrollment/payment workflows.
ANSWER;
        }

        // 6. Overdue / Aging / Deadline queries
        if (str_contains($qLower, 'overdue') || str_contains($qLower, 'aging') || str_contains($qLower, 'deadline') || str_contains($qLower, 'urgent')) {
            $overdue = $derived['overdue_tasks_sample'];
            $count = $tasksSummary['overdue'];

            if (empty($overdue)) {
                return "### ⚡ Deadlines & Aging\nAll items within your authorized scope are currently within their scheduled target dates.";
            }

            $list = '';
            foreach ($overdue as $o) {
                $list .= "* **Task #{$o['id']}: {$o['title']}**\n  * **Due:** {$o['due_date']} | **Priority:** `{$o['priority']}` | **Assignees:** {$o['assignees']}\n";
            }

            return <<<ANSWER
### ⚠️ Overdue & Aging Items ({$count} Tasks)

The following items have passed their target completion dates:

{$list}

**Calculated Impact:** Overdue items represent **{$count} out of {$tasksSummary['open']} open tasks**, requiring immediate timeline recalibration.
ANSWER;
        }

        // 7. Support tickets overview
        if (str_contains($qLower, 'ticket') || str_contains($qLower, 'support') || str_contains($qLower, 'sla')) {
            $tList = collect($context['tickets'])->map(function ($tk) {
                return "| **#{$tk['number']}** | {$tk['subject']} | {$tk['category']} | `{$tk['priority']}` | `{$tk['status']}` | {$tk['assigned_to']} |";
            })->implode("\n");

            return <<<ANSWER
### 🎫 Support Tickets Intelligence

| Number | Subject | Category | Priority | Status | Assignee |
| :--- | :--- | :--- | :---: | :---: | :--- |
{$tList}

**Summary Metrics:**
* Total Tickets: **{$ticketsSummary['total']}**
* Active Backlog: **{$ticketsSummary['open']}**
* Reopened: **{$ticketsSummary['reopened']}**
* Overdue / SLA Breaches: **{$ticketsSummary['overdue']}**
ANSWER;
        }

        // 8. Default comprehensive executive intelligence summary
        $scopeTitle = $tier === 'director' ? 'Workspace Executive Scope' : ($tier === 'lead' ? 'Team Scope' : 'Personal Scope');
        $projRows = '';
        foreach ($derived['project_breakdown'] as $pr) {
            $projRows .= "| {$pr['space']} | **{$pr['project']}** | {$pr['total']} | {$pr['done']} | {$pr['open']} | {$pr['rate']} |\n";
        }

        return <<<ANSWER
### 📊 Project & Ticket Intelligence Snapshot ({$scopeTitle})

**High-Level Metric Summary:**
* **Total Tasks:** {$tasksSummary['total']} ({$tasksSummary['done']} Done — **{$tasksSummary['completion_rate']} Velocity**)
* **Active Task Backlog:** {$tasksSummary['open']} (⚡ {$tasksSummary['overdue']} Overdue, 🛑 {$tasksSummary['blocked']} Blocked)
* **Support Tickets:** {$ticketsSummary['total']} Total ({$ticketsSummary['open']} Active, {$ticketsSummary['reopened']} Reopened)

---
### 📁 Project Deliverables Progress
| Space | Project | Total Tasks | Done | Open | Completion |
| :--- | :--- | :---: | :---: | :---: | :---: |
{$projRows}

---
### 🚀 Key Focus Areas:
1. **Unblock Integrations:** Address {$tasksSummary['blocked']} tasks blocked on external APIs (NADRA biometric & payment webhooks).
2. **Clear Overdue Backlog:** {$tasksSummary['overdue']} tasks have exceeded scheduled target dates.
3. **Review Capacity:** Distribute active items across team members to maintain sustainable velocity.
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
                'temperature' => 0.3,
                'maxOutputTokens' => 1800,
            ],
        ];

        if ($systemInstruction) {
            $payload['systemInstruction'] = [
                'parts' => [
                    ['text' => $systemInstruction],
                ],
            ];
        }

        // Iterate through fallback models if active model experiences high demand (503/429/timeout)
        foreach ($this->fallbackModels as $modelCandidate) {
            $url = "{$this->baseUrl}/models/{$modelCandidate}:generateContent?key={$this->apiKey}";

            for ($attempt = 1; $attempt <= 2; $attempt++) {
                try {
                    $response = Http::timeout(10)
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

                    if ($status === 503 || $status === 429) {
                        Log::warning("Gemini model {$modelCandidate} returned {$status} (Attempt {$attempt}). Failover proceeding...");
                        if ($attempt < 2) {
                            usleep(300000); // 300ms pause
                            continue;
                        }
                        break;
                    }

                    Log::error("Gemini API error on {$modelCandidate} ({$status}): " . $response->body());
                    break;
                } catch (\Throwable $e) {
                    Log::warning("Gemini connection exception on {$modelCandidate}: " . $e->getMessage());
                    if ($attempt < 2) {
                        usleep(200000);
                        continue;
                    }
                    break;
                }
            }
        }

        return "Unable to generate AI summary at this moment. The model service is currently experiencing high demand. Please try again in a few seconds.";
    }
}
