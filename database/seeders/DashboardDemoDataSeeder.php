<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Space;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\TaskList;
use App\Models\TaskStatus;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketActivityLog;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\User;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $workspaces = Workspace::all();
        $users = User::all();
        $adminUser = User::where('email', 'test@example.com')->first() ?? $users->first();
        $khubaib = User::where('email', 'khubaib@example.com')->first() ?? $users->first();
        $khurram = User::where('email', 'khurram@example.com')->first() ?? $users->first();
        $aliAltaf = User::where('email', 'ali.altaf@example.com')->first() ?? $users->first();
        $hamza = User::where('email', 'hamza@example.com')->first() ?? $users->first();
        $ubaid = User::where('email', 'ubaid@example.com')->first() ?? $users->first();
        $requester = User::where('email', 'requester@example.com')->first() ?? $users->last();

        foreach ($workspaces as $workspace) {
            $this->seedWorkspaceData($workspace, $users, [
                'admin' => $adminUser,
                'khubaib' => $khubaib,
                'khurram' => $khurram,
                'aliAltaf' => $aliAltaf,
                'hamza' => $hamza,
                'ubaid' => $ubaid,
                'requester' => $requester,
            ]);
        }
    }

    protected function seedWorkspaceData(Workspace $workspace, $users, array $keyUsers): void
    {
        // 1. Ensure Categories
        $categoryNames = [
            'Student Portal & LMS' => 'Issues related to student dashboard, LMS courses, assignments, and lectures.',
            'ERP & Fee Billing' => 'Fee challans, 1Link reconciliations, voucher generation, and finance locks.',
            'IT Infrastructure & Server' => 'Data center servers, cloud hosting, backups, and network outages.',
            'Network & Campus WiFi' => 'Campus Wi-Fi APs, VPN tunneling, student dorm connectivity.',
            'Exam & Grading Records' => 'GPA calculation anomalies, grade roster locks, transcripts.',
            'Software Bug Reports' => 'Application exceptions, mobile app crashes, UI/UX regressions.',
            'Feature Requests' => 'Requested platform enhancements and departmental automation.',
            'Hardware & Peripherals' => 'Classroom multimedia projectors, biometric terminals, printers.',
        ];

        $categories = [];
        foreach ($categoryNames as $name => $desc) {
            $categories[] = TicketCategory::firstOrCreate(
                ['workspace_id' => $workspace->id, 'name' => $name],
                ['description' => $desc]
            );
        }

        // 2. Ensure Teams
        $teams = Team::where('workspace_id', $workspace->id)->get();
        if ($teams->isEmpty()) {
            $team1 = Team::create(['workspace_id' => $workspace->id, 'name' => 'MIS Support']);
            $team2 = Team::create(['workspace_id' => $workspace->id, 'name' => 'Core Infrastructure']);
            $teams = collect([$team1, $team2]);
        }

        // 3. Ensure Statuses
        $statuses = TaskStatus::where('workspace_id', $workspace->id)->get();
        $todoStatus = $statuses->firstWhere('type', 'todo') ?? $statuses->first();
        $inProgressStatus = $statuses->firstWhere('type', 'in_progress') ?? $statuses->first();
        $blockedStatus = $statuses->firstWhere('type', 'blocked') ?? $statuses->first();
        $doneStatus = $statuses->firstWhere('type', 'done') ?? $statuses->last();

        // 4. Ensure Projects & TaskLists
        $projects = Project::whereHas('space', fn ($q) => $q->where('workspace_id', $workspace->id))->get();
        if ($projects->isEmpty()) {
            $space = Space::firstOrCreate(
                ['workspace_id' => $workspace->id, 'name' => 'Core Systems'],
                ['color' => '#6366f1', 'icon' => 'server']
            );
            $p = Project::create([
                'space_id' => $space->id,
                'name' => 'MIS Modernization',
                'description' => 'Digital transformation & automation project',
            ]);
            $list = TaskList::create(['project_id' => $p->id, 'name' => 'Sprint Backlog', 'sort_order' => 1]);
            $projects = collect([$p]);
        }

        $allTaskLists = TaskList::whereIn('project_id', $projects->pluck('id'))->get();
        if ($allTaskLists->isEmpty()) {
            $list = TaskList::create(['project_id' => $projects->first()->id, 'name' => 'Main List', 'sort_order' => 1]);
            $allTaskLists = collect([$list]);
        }

        // 5. Seed Past & Present Tickets (July, August, September, October 2026)
        $ticketCounter = Ticket::where('workspace_id', $workspace->id)->count() + 100;
        
        $ticketTemplates = [
            ['Student unable to view Fall 2026 enrolled courses on mobile app', 'Student reports that newly registered courses are missing from mobile course list after semester registration.', 'Student Portal & LMS', 'high'],
            ['1Link fee payment instant webhook acknowledgement delayed', 'Bank payment webhook did not clear fee receipt within 15 minutes of student payment.', 'ERP & Fee Billing', 'urgent'],
            ['Exam Marks Roster submission timeout for Pathology Dept', 'Faculty encountering 504 Gateway Timeout while submitting 120 student final marks.', 'Exam & Grading Records', 'urgent'],
            ['Geofenced classroom attendance QR code scan failure in Hall C', 'Students in Hall C reporting GPS geofence distance exceeded by 20 meters.', 'Student Portal & LMS', 'high'],
            ['Faculty VPN tunnel handshake timing out on macOS Sonoma', 'Faculty members cannot access internal research file servers via IPSec VPN.', 'Network & Campus WiFi', 'medium'],
            ['Admission portal CNIC duplicate error on re-application', 'Applicant with revised FSc marks cannot resubmit admission form due to CNIC constraint.', 'Software Bug Reports', 'high'],
            ['Database read replica replication lag exceeding 120 seconds', 'MySQL read replica CPU spiked to 95% causing delayed reporting queries.', 'IT Infrastructure & Server', 'urgent'],
            ['Dark mode contrast ratio failure on student grade transcript', 'Letter grades D and F have insufficient contrast against dark zinc card background.', 'Software Bug Reports', 'low'],
            ['Library biometric gate terminal 2 unresponsive to RFID cards', 'Physical access terminal at Central Library entrance is not registering student smart cards.', 'Hardware & Peripherals', 'medium'],
            ['Automated GPA / CGPA rounding disparity on probation alerts', 'Academic probation logic evaluated 1.999 GPA as passing instead of triggering advisory warning.', 'Exam & Grading Records', 'urgent'],
            ['Bulk SMS gateway quota exhausted during emergency closure alert', 'Campus notification service halted due to third-party SMS vendor credit limit.', 'IT Infrastructure & Server', 'high'],
            ['Add PDF export support for semester syllabus outline', 'Faculty requested one-click PDF syllabus export with university header and QR verification.', 'Feature Requests', 'low'],
            ['Kuickpay reconciliation discrepancy for 45 student fee challans', 'End-of-day settlement batch had 45 fee payments not matched automatically in ledger.', 'ERP & Fee Billing', 'high'],
            ['Biometric attendance sync delay between medical college and MIS', 'Evening clinical attendance from hospital wards takes 3 hours to reflect in main DB.', 'Student Portal & LMS', 'medium'],
            ['Hostel Wi-Fi access point firmware upgrade schedule', 'Scheduled off-peak firmware patch for 24 Ubiquiti access points in Girls Hostel block.', 'Network & Campus WiFi', 'medium'],
            ['NADRA Verisys biometric API SSL certificate expiry notice', 'Third-party NADRA certificate expiring in 7 days, needs renewal to prevent admission downtime.', 'IT Infrastructure & Server', 'urgent'],
            ['Request for research lab high-performance GPU cluster access', 'PhD student requests SSH access to A100 GPU workstation for bioinformatics deep learning.', 'Feature Requests', 'medium'],
            ['Faculty leave application approval chain routing error', 'Leave applications for Dental faculty skipped Head of Department and went directly to Dean.', 'Software Bug Reports', 'medium'],
        ];

        // Seed Historical Distribution:
        // July 2026: 14 tickets
        $this->generateTicketBatch($workspace, $ticketTemplates, $categories, $projects, $teams, $keyUsers, $ticketCounter, [
            'year' => 2026, 'month' => 7, 'days' => [2, 5, 8, 12, 15, 18, 22, 25, 28, 30], 'count' => 14, 'resolution_ratio' => 0.95
        ]);

        // August 2026: 22 tickets
        $this->generateTicketBatch($workspace, $ticketTemplates, $categories, $projects, $teams, $keyUsers, $ticketCounter, [
            'year' => 2026, 'month' => 8, 'days' => [1, 3, 6, 9, 12, 14, 17, 20, 23, 26, 29, 31], 'count' => 22, 'resolution_ratio' => 0.90
        ]);

        // September 2026: 32 tickets
        $this->generateTicketBatch($workspace, $ticketTemplates, $categories, $projects, $teams, $keyUsers, $ticketCounter, [
            'year' => 2026, 'month' => 9, 'days' => [2, 4, 7, 10, 13, 16, 18, 21, 24, 27, 29, 30], 'count' => 32, 'resolution_ratio' => 0.85
        ]);

        // October 2026 (Present Month: October 1 - 9): 24 tickets
        $this->generateTicketBatch($workspace, $ticketTemplates, $categories, $projects, $teams, $keyUsers, $ticketCounter, [
            'year' => 2026, 'month' => 10, 'days' => [1, 2, 3, 4, 5, 6, 7, 8, 9], 'count' => 24, 'resolution_ratio' => 0.45
        ]);

        // 6. Seed Past & Present Tasks (July, August, September, October 2026)
        $taskTemplates = [
            ['Deploy Dockerized Redis cluster for session cache', 'Configure multi-node Redis cluster with sentinel failover for MIS student portal sessions.', 'high', 16],
            ['Implement automated matric & intermediate equivalence calculation', 'Build grade conversion matrix for Cambridge O/A levels and American high school diplomas.', 'urgent', 20],
            ['Conduct stress test on LMS quiz gateway for 3,500 concurrent students', 'Run distributed k6 load testing scripts simulating 50-question quiz submissions.', 'high', 12],
            ['Build Principal Investigator research grant review workflow', 'Multi-tier approval routing: Faculty PI -> Chairperson -> Dean -> ORIC Director.', 'normal', 14],
            ['Interactive dynamic QR code classroom attendance engine', 'WebSocket rotating QR code refreshed every 15s with GPS geofence validation.', 'urgent', 24],
            ['Automate Kuickpay 1Link end-of-day bank settlement reconciliation', 'CRON job processing nightly bank settlement CSV files and generating balance reconciliations.', 'high', 18],
            ['Upgrade frontend design system to Iris & Obsidian palette', 'Harmonize sidebar, dashboard cards, modal slide-overs with Tailwind CSS v4 design tokens.', 'normal', 10],
            ['Migrate database backups to immutable AWS S3 Glacier storage', 'Enforce WORM (Write Once Read Many) policy on daily university database snapshots.', 'urgent', 8],
            ['Audit role-based permissions on student medical record vault', 'Verify HIPAA / ISO 27001 compliance on digital patient histories in teaching hospital.', 'high', 15],
            ['Integrate WhatsApp Business API for instant student fee receipt alerts', 'Automated template notifications with fee receipt PDF download links.', 'normal', 12],
            ['Optimize slow SQL queries on semester GPA aggregation engine', 'Add composite indexes on enrollments table and partition semester transcripts by academic year.', 'high', 10],
            ['Conduct cybersecurity vulnerability scan on public admissions portal', 'Run OWASP ZAP automated scanner and remediate CSRF/XSS findings prior to admissions intake.', 'urgent', 14],
            ['Build faculty annual appraisal KPI scoring calculator', 'Automate research citation index, teaching evaluations, and committee scores.', 'normal', 16],
            ['Setup Prometheus & Grafana real-time server telemetry dashboards', 'Server CPU, memory, database connection pool, and queue workers monitoring.', 'high', 12],
            ['Configure TLS 1.3 and HSTS strict security headers across all domains', 'Enforce A+ rating on SSL Labs for stmu.edu.pk and mis.stmu.edu.pk portals.', 'normal', 6],
        ];

        // July Tasks: 16 tasks
        $this->generateTaskBatch($workspace, $taskTemplates, $allTaskLists, $statuses, $keyUsers, [
            'year' => 2026, 'month' => 7, 'count' => 16, 'completion_ratio' => 0.95
        ]);

        // August Tasks: 24 tasks
        $this->generateTaskBatch($workspace, $taskTemplates, $allTaskLists, $statuses, $keyUsers, [
            'year' => 2026, 'month' => 8, 'count' => 24, 'completion_ratio' => 0.90
        ]);

        // September Tasks: 35 tasks
        $this->generateTaskBatch($workspace, $taskTemplates, $allTaskLists, $statuses, $keyUsers, [
            'year' => 2026, 'month' => 9, 'count' => 35, 'completion_ratio' => 0.85
        ]);

        // October Tasks (Present Month: October 1 - 9): 28 tasks
        $this->generateTaskBatch($workspace, $taskTemplates, $allTaskLists, $statuses, $keyUsers, [
            'year' => 2026, 'month' => 10, 'count' => 28, 'completion_ratio' => 0.50
        ]);
    }

    protected function generateTicketBatch(Workspace $workspace, array $templates, array $categories, $projects, $teams, array $keyUsers, int &$counter, array $config): void
    {
        $year = $config['year'];
        $month = $config['month'];
        $days = $config['days'];
        $count = $config['count'];
        $resRatio = $config['resolution_ratio'];

        $agents = [
            $keyUsers['admin'],
            $keyUsers['khubaib'],
            $keyUsers['khurram'],
            $keyUsers['aliAltaf'],
            $keyUsers['hamza'],
            $keyUsers['ubaid'],
        ];

        for ($i = 0; $i < $count; $i++) {
            $counter++;
            $tpl = $templates[$i % count($templates)];
            $day = $days[$i % count($days)];
            $hour = rand(8, 17);
            $minute = rand(0, 59);

            $createdAt = Carbon::create($year, $month, $day, $hour, $minute, 0);
            $isResolved = ($i / $count) < $resRatio;

            // Find matching category or random
            $cat = collect($categories)->first(fn ($c) => $c->name === $tpl[2]) ?? $categories[array_rand($categories)];
            $proj = $projects->random();
            $team = $teams->random();
            $agent = $agents[array_rand($agents)];

            $priority = $tpl[3];
            $status = $isResolved ? 'resolved' : (rand(0, 1) ? 'in_progress' : 'open');
            $resolvedAt = null;
            $resolutionSummary = null;

            if ($isResolved) {
                $resolveDays = rand(1, 3);
                $resolvedAt = (clone $createdAt)->addDays($resolveDays)->addHours(rand(1, 5));
                if ($month === 10 && $resolvedAt > Carbon::create(2026, 10, 9, 10, 0, 0)) {
                    $resolvedAt = Carbon::create(2026, 10, 9, 8, rand(10, 50), 0);
                }
                $resolutionSummary = "Issue investigated and resolved. Fix verified in staging environment and deployed to production.";
            }

            // Calculate realistic SLA due date
            $slaHours = match ($priority) {
                'urgent' => 4,
                'high' => 24,
                'medium' => 48,
                default => 72,
            };
            $dueBy = (clone $createdAt)->addHours($slaHours);

            $ticketNumber = 'TCK-W' . $workspace->id . '-' . rand(1000, 9999) . '-' . $counter;
            $ticket = Ticket::create([
                'workspace_id' => $workspace->id,
                'ticket_number' => $ticketNumber,
                'subject' => $tpl[0] . ($count > 18 ? " [Ref #{$counter}]" : ''),
                'description' => $tpl[1],
                'category_id' => $cat->id,
                'project_id' => $proj->id,
                'priority' => $priority,
                'status' => $status,
                'raised_by_user_id' => $keyUsers['requester']->id ?? $keyUsers['admin']->id,
                'assigned_team_id' => $team->id,
                'assigned_to_user_id' => $status !== 'open' ? $agent->id : null,
                'due_by' => $dueBy,
                'resolution_summary' => $resolutionSummary,
                'resolved_at' => $resolvedAt,
                'created_at' => $createdAt,
                'updated_at' => $resolvedAt ?? $createdAt,
            ]);

            // Add realistic activity logs for October tickets
            if ($month === 10) {
                TicketActivityLog::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $keyUsers['requester']->id ?? $keyUsers['admin']->id,
                    'action' => 'created',
                    'to_value' => "Ticket {$ticket->ticket_number} created",
                    'created_at' => $createdAt,
                ]);

                if ($status === 'in_progress' || $isResolved) {
                    TicketActivityLog::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $agent->id,
                        'action' => 'assigned_user',
                        'to_value' => $agent->name,
                        'created_at' => (clone $createdAt)->addMinutes(15),
                    ]);
                }

                if ($isResolved) {
                    TicketActivityLog::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $agent->id,
                        'action' => 'status_changed',
                        'from_value' => 'in_progress',
                        'to_value' => 'resolved',
                        'created_at' => $resolvedAt,
                    ]);
                }
            }
        }
    }

    protected function generateTaskBatch(Workspace $workspace, array $templates, $taskLists, $statuses, array $keyUsers, array $config): void
    {
        $year = $config['year'];
        $month = $config['month'];
        $count = $config['count'];
        $compRatio = $config['completion_ratio'];

        $todoStatus = $statuses->firstWhere('type', 'todo') ?? $statuses->first();
        $inProgStatus = $statuses->firstWhere('type', 'in_progress') ?? $statuses->first();
        $blockedStatus = $statuses->firstWhere('type', 'blocked') ?? $statuses->first();
        $doneStatus = $statuses->firstWhere('type', 'done') ?? $statuses->last();

        $assignableUsers = [
            $keyUsers['admin'],
            $keyUsers['khubaib'],
            $keyUsers['khurram'],
            $keyUsers['aliAltaf'],
            $keyUsers['hamza'],
            $keyUsers['ubaid'],
        ];

        $maxDays = $month === 10 ? 9 : 28;

        for ($i = 0; $i < $count; $i++) {
            $tpl = $templates[$i % count($templates)];
            $day = rand(1, $maxDays);
            $createdAt = Carbon::create($year, $month, $day, rand(8, 17), rand(0, 59), 0);
            $isDone = ($i / $count) < $compRatio;

            $status = $isDone ? $doneStatus : ($i % 4 === 0 ? $blockedStatus : ($i % 2 === 0 ? $inProgStatus : $todoStatus));
            $dueDate = (clone $createdAt)->addDays(rand(2, 7));

            $list = $taskLists->random();
            $assignee = $assignableUsers[$i % count($assignableUsers)];

            $task = Task::create([
                'task_list_id' => $list->id,
                'created_by_id' => $keyUsers['khubaib']->id ?? $keyUsers['admin']->id,
                'title' => $tpl[0] . ($count > 15 ? " (v{$year}.{$month}.{$i})" : ''),
                'description' => $tpl[1],
                'status_id' => $status->id,
                'priority' => $tpl[2],
                'start_date' => $createdAt,
                'due_date' => $dueDate,
                'estimated_hours' => $tpl[3],
                'actual_hours' => $isDone ? $tpl[3] : round($tpl[3] * 0.4, 1),
                'sort_order' => $i + 1,
                'is_archived' => false,
                'created_at' => $createdAt,
                'updated_at' => $isDone ? (clone $createdAt)->addDays(rand(1, 3)) : $createdAt,
            ]);

            // Assign user
            $task->assignees()->sync([$assignee->id]);

            // Add activity log for recent October tasks
            if ($month === 10) {
                TaskActivity::create([
                    'task_id' => $task->id,
                    'user_id' => $assignee->id,
                    'action' => $isDone ? 'status_updated' : 'created',
                    'description' => $isDone ? "Completed task \"{$task->title}\"" : "Created task \"{$task->title}\"",
                    'old_value' => $isDone ? 'In Progress' : null,
                    'new_value' => $isDone ? 'Done' : 'To Do',
                    'created_at' => $isDone ? $task->updated_at : $createdAt,
                    'updated_at' => $isDone ? $task->updated_at : $createdAt,
                ]);
            }
        }
    }
}
