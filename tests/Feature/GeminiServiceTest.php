<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Space;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\Workspace;
use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_gemini_service_summarizes_task_with_mocked_api(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => "### Objective\nComplete testing on task features.\n### Blockers\nNone.\n### Next Steps\nShip to production."]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $user = User::factory()->create();
        $workspace = Workspace::create(['name' => 'Demo Workspace', 'owner_id' => $user->id]);
        $space = Space::create(['workspace_id' => $workspace->id, 'name' => 'Engineering']);
        $project = Project::create(['space_id' => $space->id, 'name' => 'Core']);
        $list = TaskList::create(['project_id' => $project->id, 'name' => 'Sprint']);
        $status = TaskStatus::create(['workspace_id' => $workspace->id, 'name' => 'In Progress', 'type' => 'in_progress']);

        $task = Task::create([
            'task_list_id' => $list->id,
            'created_by_id' => $user->id,
            'title' => 'Implement WebSockets',
            'description' => 'Real-time sync between browsers.',
            'status_id' => $status->id,
        ]);

        $service = new GeminiService();
        $summary = $service->summarizeTask($task);

        $this->assertStringContainsString('Objective', $summary);
        $this->assertStringContainsString('Ship to production', $summary);
    }

    public function test_gemini_service_handles_api_failure_gracefully(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['error' => 'Rate limit exceeded'], 429)
        ]);

        $service = new GeminiService();
        $res = $service->generate('Hello');

        $this->assertStringContainsString('Unable to generate AI summary', $res);
    }

    public function test_gemini_role_hierarchy_restricts_member_to_only_own_tasks(): void
    {
        $director = User::factory()->create(['name' => 'Khubaib Ahmed', 'job_title' => 'Director MIS']);
        $engineer = User::factory()->create(['name' => 'Hamza Shoulat', 'job_title' => 'Software Engineer']);

        $workspace = Workspace::create(['name' => 'MIS Workspace', 'owner_id' => $director->id]);
        $space = Space::create(['workspace_id' => $workspace->id, 'name' => 'Admissions']);
        $project = Project::create(['space_id' => $space->id, 'name' => 'Admission Portal']);
        $list = TaskList::create(['project_id' => $project->id, 'name' => 'Sprint 1']);
        $status = TaskStatus::create(['workspace_id' => $workspace->id, 'name' => 'In Progress', 'type' => 'in_progress']);

        // Director task
        $directorTask = Task::create([
            'task_list_id' => $list->id,
            'created_by_id' => $director->id,
            'title' => 'Executive Strategic Roadmap',
            'status_id' => $status->id,
        ]);
        $directorTask->assignees()->attach($director->id);

        // Engineer task
        $engineerTask = Task::create([
            'task_list_id' => $list->id,
            'created_by_id' => $engineer->id,
            'title' => 'Fix applicant login bug',
            'status_id' => $status->id,
        ]);
        $engineerTask->assignees()->attach($engineer->id);

        Http::fake([
            'generativelanguage.googleapis.com/*' => function ($request) {
                $body = json_decode($request->body(), true);
                $promptText = $body['contents'][0]['parts'][0]['text'] ?? '';
                if (str_contains($promptText, 'Director Khubaib')) {
                    return Http::response([
                        'candidates' => [
                            ['content' => ['parts' => [['text' => '🔒 Under the workspace role-based access hierarchy, your access level (Software Engineer) authorizes you to view only your own assigned, created, and watched tasks. You do not have permission to view Director, Team Lead, CEO, or other team members\' records.']]]]
                        ]
                    ], 200);
                }
                return Http::response([
                    'candidates' => [
                        ['content' => ['parts' => [['text' => "### 📋 Tasks Overview\n* Executive Strategic Roadmap\n* Fix applicant login bug"]]]]
                    ]
                ], 200);
            }
        ]);

        $service = new GeminiService();

        // 1. Check accessible tasks collection directly
        $directorAccessible = $service->getAccessibleTasksForAi($workspace, $director);
        $this->assertEquals(2, $directorAccessible->count());

        $engineerAccessible = $service->getAccessibleTasksForAi($workspace, $engineer);
        $this->assertEquals(1, $engineerAccessible->count());
        $this->assertEquals('Fix applicant login bug', $engineerAccessible->first()->title);

        // 2. Query as Engineer asking for Director task
        $queryResult = $service->queryTasks($workspace, 'What is Director Khubaib working on?', $engineer);
        $this->assertTrue(
            str_contains($queryResult, 'Access Restricted') ||
            str_contains(strtolower($queryResult), 'role-based access') ||
            str_contains(strtolower($queryResult), 'permission to view director')
        );
        $this->assertStringNotContainsString('Executive Strategic Roadmap', $queryResult);

        // 3. Query as Director asking for tasks
        $directorQueryResult = $service->queryTasks($workspace, 'Show all tasks', $director);
        $this->assertTrue(
            str_contains($directorQueryResult, 'Tasks Overview') ||
            str_contains($directorQueryResult, 'Executive Strategic Roadmap') ||
            str_contains($directorQueryResult, 'tasks')
        );
    }

    public function test_gemini_role_hierarchy_blocks_member_from_summarizing_director_task(): void
    {
        $director = User::factory()->create(['name' => 'Alex Rivers', 'job_title' => 'Founder & CEO']);
        $engineer = User::factory()->create(['name' => 'Muhammad Ali', 'job_title' => 'Software Engineer']);

        $workspace = Workspace::create(['name' => 'Test Labs', 'owner_id' => $director->id]);
        $space = Space::create(['workspace_id' => $workspace->id, 'name' => 'Core']);
        $project = Project::create(['space_id' => $space->id, 'name' => 'Platform']);
        $list = TaskList::create(['project_id' => $project->id, 'name' => 'Main']);
        $status = TaskStatus::create(['workspace_id' => $workspace->id, 'name' => 'Open', 'type' => 'todo']);

        $confidentialTask = Task::create([
            'task_list_id' => $list->id,
            'created_by_id' => $director->id,
            'title' => 'Confidential Board Budget Allocations',
            'status_id' => $status->id,
        ]);
        $confidentialTask->assignees()->attach($director->id);

        $service = new GeminiService();

        // Member tries to summarize Director task
        $summary = $service->summarizeTask($confidentialTask, $engineer);
        $this->assertStringContainsString('Access Restricted by Role Hierarchy', $summary);
        $this->assertStringContainsString('does not authorize you to view or summarize this task', $summary);

        // Director can summarize
        $directorSummary = $service->summarizeTask($confidentialTask, $director);
        $this->assertStringNotContainsString('Access Restricted', $directorSummary);
    }

    public function test_gemini_role_hierarchy_blocks_member_from_generating_team_summary(): void
    {
        $director = User::factory()->create(['name' => 'Alex Rivers', 'job_title' => 'CEO']);
        $engineer = User::factory()->create(['name' => 'Ubaid ur Rehman', 'job_title' => 'QA Engineer']);

        $workspace = Workspace::create(['name' => 'Test Labs', 'owner_id' => $director->id]);

        $service = new GeminiService();

        $memberTeamSummary = $service->generateTeamSummary($workspace, null, $engineer);
        $this->assertStringContainsString('Access Restricted by Role Hierarchy', $memberTeamSummary);
        $this->assertStringContainsString('restricted to Team Leads and Directors', $memberTeamSummary);
    }

    public function test_gemini_service_project_intelligence_includes_tickets_and_lifecycle(): void
    {
        $owner = User::factory()->create(['name' => 'Alex Rivers', 'job_title' => 'CEO']);
        $workspace = Workspace::create(['name' => 'Test Labs', 'owner_id' => $owner->id]);

        $category = \App\Models\TicketCategory::create([
            'workspace_id' => $workspace->id,
            'name' => 'Bug Reports',
        ]);

        $ticket = \App\Models\Ticket::create([
            'workspace_id' => $workspace->id,
            'ticket_number' => 'TCK-9999',
            'subject' => 'Payment Gateway Webhook Timeout',
            'description' => 'Detailed webhook failure logs.',
            'category_id' => $category->id,
            'priority' => 'urgent',
            'status' => 'reopened',
            'raised_by_user_id' => $owner->id,
            'assigned_to_user_id' => $owner->id,
            'due_by' => now()->addHours(4),
        ]);

        $service = new GeminiService();
        $context = $service->assembleWorkspaceIntelligenceContext($workspace, $owner);

        $this->assertNotEmpty($context['tickets']);
        $this->assertEquals('TCK-9999', $context['derived_metrics']['last_ticket']['ticket_number']);
        $this->assertEquals('reopened', $context['derived_metrics']['last_ticket']['status']);

        // Test local synthesis fallback
        $answer = $service->synthesizeLocalQueryAnswer($context, 'When was the last ticket opened and what is its status?', $workspace, $owner, 'director');
        $this->assertStringContainsString('TCK-9999', $answer);
        $this->assertStringContainsString('Payment Gateway Webhook Timeout', $answer);
        $this->assertStringContainsString('reopened', $answer);
    }
}

