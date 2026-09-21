<?php

namespace Database\Seeders;

use App\Models\AppNotification;
use App\Models\Project;
use App\Models\Space;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\TaskChecklist;
use App\Models\TaskComment;
use App\Models\TaskList;
use App\Models\TaskStatus;
use App\Models\TaskTimeEntry;
use App\Models\Team;
use App\Models\AccessRequest;
use App\Models\Ticket;
use App\Models\TicketActivityLog;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use App\Models\TicketComment;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles & Permissions in DB Tables
        $permissions = [
            'workspace.invite-members',
            'workspace.remove-members',
            'workspace.update-roles',
            'workspace.manage-teams',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $ownerRole = Role::firstOrCreate(['name' => 'Owner', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $memberRole = Role::firstOrCreate(['name' => 'Member', 'guard_name' => 'web']);
        $guestRole = Role::firstOrCreate(['name' => 'Guest', 'guard_name' => 'web']);

        $ownerRole->syncPermissions($permissions);
        $adminRole->syncPermissions($permissions);
        $memberRole->syncPermissions([]);
        $guestRole->syncPermissions([]);

        // 2. Demo Users (Original + MIS Team)
        $owner = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Alex Rivers',
                'password' => Hash::make('password'),
                'job_title' => 'Founder & Lead Architect',
                'timezone' => 'America/New_York',
                'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
            ]
        );
        $owner->assignRole('Owner');

        // MIS Team Members requested by user:
        // - Khubaib Ahmed: Director
        $khubaib = User::updateOrCreate(
            ['email' => 'khubaib@example.com'],
            [
                'name' => 'Khubaib Ahmed',
                'password' => Hash::make('password'),
                'job_title' => 'Director MIS',
                'timezone' => 'Asia/Karachi',
                'avatar_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&auto=format&fit=crop&q=80',
            ]
        );
        $khubaib->assignRole('Admin');

        // - Khurram Ahmed: Team Lead
        $khurram = User::updateOrCreate(
            ['email' => 'khurram@example.com'],
            [
                'name' => 'Khurram Ahmed',
                'password' => Hash::make('password'),
                'job_title' => 'Team Lead',
                'timezone' => 'Asia/Karachi',
                'avatar_url' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=150&auto=format&fit=crop&q=80',
            ]
        );
        $khurram->assignRole('Admin');

        // - Hamza Shoulat: Software Engineer
        $hamza = User::updateOrCreate(
            ['email' => 'hamza@example.com'],
            [
                'name' => 'Hamza Shoulat',
                'password' => Hash::make('password'),
                'job_title' => 'Software Engineer',
                'timezone' => 'Asia/Karachi',
                'avatar_url' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=150&auto=format&fit=crop&q=80',
            ]
        );
        $hamza->assignRole('Member');

        // - Muhammad Ali Altaf: Software Engineer
        $aliAltaf = User::updateOrCreate(
            ['email' => 'ali.altaf@example.com'],
            [
                'name' => 'Muhammad Ali Altaf',
                'password' => Hash::make('password'),
                'job_title' => 'Software Engineer',
                'timezone' => 'Asia/Karachi',
                'avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
            ]
        );
        $aliAltaf->assignRole('Member');

        // - Muhammad Ali: Software Engineer
        $muhammadAli = User::updateOrCreate(
            ['email' => 'muhammad.ali@example.com'],
            [
                'name' => 'Muhammad Ali',
                'password' => Hash::make('password'),
                'job_title' => 'Software Engineer',
                'timezone' => 'Asia/Karachi',
                'avatar_url' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=150&auto=format&fit=crop&q=80',
            ]
        );
        $muhammadAli->assignRole('Member');

        // - Ubaid ur Rehman: QA
        $ubaid = User::updateOrCreate(
            ['email' => 'ubaid@example.com'],
            [
                'name' => 'Ubaid ur Rehman',
                'password' => Hash::make('password'),
                'job_title' => 'QA Engineer',
                'timezone' => 'Asia/Karachi',
                'avatar_url' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=150&auto=format&fit=crop&q=80',
            ]
        );
        $ubaid->assignRole('Member');

        // Other demo users
        $sarah = User::updateOrCreate(
            ['email' => 'sarah.chen@acme.io'],
            [
                'name' => 'Sarah Chen',
                'password' => Hash::make('password'),
                'job_title' => 'Senior Frontend Engineer',
                'timezone' => 'America/Los_Angeles',
                'avatar_url' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150&auto=format&fit=crop&q=80',
            ]
        );
        $sarah->assignRole('Admin');

        $marcus = User::updateOrCreate(
            ['email' => 'marcus.vance@acme.io'],
            [
                'name' => 'Marcus Vance',
                'password' => Hash::make('password'),
                'job_title' => 'Cloud & DevOps Architect',
                'timezone' => 'Europe/London',
                'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
            ]
        );
        $marcus->assignRole('Member');

        $elena = User::updateOrCreate(
            ['email' => 'elena.rostova@acme.io'],
            [
                'name' => 'Elena Rostova',
                'password' => Hash::make('password'),
                'job_title' => 'Lead UX/UI Designer',
                'timezone' => 'Europe/Berlin',
                'avatar_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&auto=format&fit=crop&q=80',
            ]
        );
        $elena->assignRole('Member');

        // 3. Demo Workspace
        $workspace = Workspace::updateOrCreate(
            ['slug' => 'acme-labs'],
            [
                'name' => 'Acme Innovation Labs',
                'description' => 'Cross-functional engineering, product design, and growth organization.',
                'owner_id' => $owner->id,
            ]
        );

        // Attach all members to workspace
        $workspace->members()->sync([
            $owner->id => ['role' => 'owner', 'job_title' => 'Founder & Lead Architect', 'timezone' => 'America/New_York'],
            $khubaib->id => ['role' => 'admin', 'job_title' => 'Director MIS', 'timezone' => 'Asia/Karachi'],
            $khurram->id => ['role' => 'admin', 'job_title' => 'Team Lead', 'timezone' => 'Asia/Karachi'],
            $hamza->id => ['role' => 'member', 'job_title' => 'Software Engineer', 'timezone' => 'Asia/Karachi'],
            $aliAltaf->id => ['role' => 'member', 'job_title' => 'Software Engineer', 'timezone' => 'Asia/Karachi'],
            $muhammadAli->id => ['role' => 'member', 'job_title' => 'Software Engineer', 'timezone' => 'Asia/Karachi'],
            $ubaid->id => ['role' => 'member', 'job_title' => 'QA Engineer', 'timezone' => 'Asia/Karachi'],
            $sarah->id => ['role' => 'admin', 'job_title' => 'Senior Frontend Engineer', 'timezone' => 'America/Los_Angeles'],
            $marcus->id => ['role' => 'member', 'job_title' => 'Cloud & DevOps Architect', 'timezone' => 'Europe/London'],
            $elena->id => ['role' => 'member', 'job_title' => 'Lead UX/UI Designer', 'timezone' => 'Europe/Berlin'],
        ]);

        // 4. Teams (including requested MIS Team)
        $teamMis = Team::firstOrCreate(
            ['workspace_id' => $workspace->id, 'name' => 'MIS'],
            [
                'description' => 'Management Information Systems — Admissions, ORIC Research Hub, LMS & Digital Portals.',
                'color' => '#0284c7',
                'icon' => 'computer-desktop',
            ]
        );
        $teamMis->members()->syncWithoutDetaching([
            $khubaib->id => ['role' => 'lead', 'capacity_limit' => 6],
            $khurram->id => ['role' => 'lead', 'capacity_limit' => 5],
            $hamza->id => ['role' => 'member', 'capacity_limit' => 4],
            $aliAltaf->id => ['role' => 'member', 'capacity_limit' => 5],
            $muhammadAli->id => ['role' => 'member', 'capacity_limit' => 5],
            $ubaid->id => ['role' => 'member', 'capacity_limit' => 4],
        ]);

        $teamEng = Team::firstOrCreate(
            ['workspace_id' => $workspace->id, 'name' => 'Core Platform Engineering'],
            ['description' => 'Responsible for distributed infrastructure, API services, and core database scale.', 'color' => '#6366f1', 'icon' => 'code-bracket']
        );
        $teamEng->members()->syncWithoutDetaching([
            $owner->id => ['role' => 'lead', 'capacity_limit' => 6],
            $sarah->id => ['role' => 'member', 'capacity_limit' => 5],
            $marcus->id => ['role' => 'member', 'capacity_limit' => 4],
        ]);

        $teamDesign = Team::firstOrCreate(
            ['workspace_id' => $workspace->id, 'name' => 'Product Design & UI/UX'],
            ['description' => 'User research, interface prototyping, design system tokens, and usability testing.', 'color' => '#ec4899', 'icon' => 'swatch']
        );
        $teamDesign->members()->syncWithoutDetaching([
            $elena->id => ['role' => 'lead', 'capacity_limit' => 5],
            $sarah->id => ['role' => 'member', 'capacity_limit' => 4],
        ]);

        // 5. Default Task Statuses
        $statuses = [
            ['name' => 'To Do', 'color' => '#94a3b8', 'type' => 'todo', 'sort_order' => 1, 'is_default' => true],
            ['name' => 'In Progress', 'color' => '#3b82f6', 'type' => 'in_progress', 'sort_order' => 2, 'is_default' => false],
            ['name' => 'In Review', 'color' => '#a855f7', 'type' => 'review', 'sort_order' => 3, 'is_default' => false],
            ['name' => 'Blocked', 'color' => '#ef4444', 'type' => 'blocked', 'sort_order' => 4, 'is_default' => false],
            ['name' => 'Done', 'color' => '#10b981', 'type' => 'done', 'sort_order' => 5, 'is_default' => false],
        ];

        $statusModels = [];
        foreach ($statuses as $st) {
            $statusModels[$st['name']] = TaskStatus::firstOrCreate(
                ['workspace_id' => $workspace->id, 'name' => $st['name'], 'space_id' => null],
                $st
            );
        }

        // 6. Tags
        $tagNames = [
            'Admission' => '#0284c7',
            'ORIC' => '#8b5cf6',
            'LMS' => '#10b981',
            'QA' => '#f59e0b',
            'Bug' => '#ef4444',
            'Feature' => '#3b82f6',
            'Performance' => '#eab308',
            'Security' => '#dc2626',
            'Backend' => '#6366f1',
            'Frontend' => '#06b6d4',
            'Urgent' => '#e11d48',
        ];
        $tagModels = [];
        foreach ($tagNames as $name => $color) {
            $tagModels[$name] = Tag::firstOrCreate(
                ['workspace_id' => $workspace->id, 'name' => $name],
                ['color' => $color]
            );
        }

        // 7. Spaces (including dedicated MIS Space)
        $spaceMis = Space::firstOrCreate(
            ['workspace_id' => $workspace->id, 'name' => 'MIS'],
            [
                'description' => 'Management Information Systems: Admission Portal, ORIC Research Hub, and LMS.',
                'icon' => 'computer-desktop',
                'color' => '#0284c7',
                'sort_order' => 1,
            ]
        );

        $spaceEngineering = Space::firstOrCreate(
            ['workspace_id' => $workspace->id, 'name' => 'Engineering'],
            [
                'description' => 'Architecture, APIs, DevOps, and fullstack implementation.',
                'icon' => 'code-bracket',
                'color' => '#6366f1',
                'sort_order' => 2,
            ]
        );

        $spaceDesign = Space::firstOrCreate(
            ['workspace_id' => $workspace->id, 'name' => 'Product Design'],
            [
                'description' => 'Figma components, design tokens, and user journeys.',
                'icon' => 'paint-brush',
                'color' => '#ec4899',
                'sort_order' => 3,
            ]
        );

        // ==========================================
        // 8. MIS PROJECTS (Admission, ORIC, LMS)
        // ==========================================

        // --- Project 1: Admission Project ---
        $projAdmission = Project::firstOrCreate(
            ['space_id' => $spaceMis->id, 'name' => 'Admission Project'],
            [
                'description' => 'Online Student Admissions, Application Form Wizard, Document Verification, and Merit Calculation.',
                'color' => '#0284c7',
                'icon' => 'academic-cap',
            ]
        );

        $listApplicant = TaskList::firstOrCreate(
            ['project_id' => $projAdmission->id, 'name' => 'Applicant Portal & Registration'],
            ['description' => 'Applicant onboarding, CNIC authentication, and program choice selection.', 'color' => '#0284c7']
        );

        $listVerification = TaskList::firstOrCreate(
            ['project_id' => $projAdmission->id, 'name' => 'Document Verification & Merit Lists'],
            ['description' => 'Scrutiny of matric/inter transcripts, IBCC equivalence, and merit ranking.', 'color' => '#0ea5e9']
        );

        $listChallan = TaskList::firstOrCreate(
            ['project_id' => $projAdmission->id, 'name' => 'Fee Challan & Bank Integration'],
            ['description' => 'Integration with 1Link, Kuickpay, and branch fee voucher reconciliation.', 'color' => '#38bdf8']
        );

        // --- Project 2: ORIC ---
        $projOric = Project::firstOrCreate(
            ['space_id' => $spaceMis->id, 'name' => 'ORIC'],
            [
                'description' => 'Office of Research, Innovation & Commercialization: Grant Submissions, Patents, and HEC Publications.',
                'color' => '#8b5cf6',
                'icon' => 'light-bulb',
            ]
        );

        $listGrants = TaskList::firstOrCreate(
            ['project_id' => $projOric->id, 'name' => 'Research Grants & Proposals'],
            ['description' => 'Faculty NRPU grant applications, ethics committee approvals, and funding status.', 'color' => '#8b5cf6']
        );

        $listPatents = TaskList::firstOrCreate(
            ['project_id' => $projOric->id, 'name' => 'Patents & Commercialization'],
            ['description' => 'Intellectual property filings, prototype licensing, and industry linkages.', 'color' => '#a855f7']
        );

        // --- Project 3: LMS ---
        $projLms = Project::firstOrCreate(
            ['space_id' => $spaceMis->id, 'name' => 'LMS'],
            [
                'description' => 'Learning Management System: Course Enrollment, Attendance, Online Quizzes, Gradebook, and Transcripts.',
                'color' => '#10b981',
                'icon' => 'book-open',
            ]
        );

        $listEnrollment = TaskList::firstOrCreate(
            ['project_id' => $projLms->id, 'name' => 'Course Enrollment & Attendance'],
            ['description' => 'Semester registration, prerequisites check, and QR attendance tracking.', 'color' => '#10b981']
        );

        $listQuizzes = TaskList::firstOrCreate(
            ['project_id' => $projLms->id, 'name' => 'Quiz & Assignment Submissions'],
            ['description' => 'Anti-cheating exam engine, timed quizzes, and file submission workflows.', 'color' => '#059669']
        );

        $listGrades = TaskList::firstOrCreate(
            ['project_id' => $projLms->id, 'name' => 'Gradebook & Transcripts'],
            ['description' => 'Relative grading bell-curve, GPA calculations, and digital transcripts.', 'color' => '#047857']
        );

        // ==========================================
        // 9. TASKS IN MIS PROJECTS (Assigned to Team Members)
        // ==========================================

        // --- ADMISSION SYSTEM TASKS ---
        // Task A1: Muhammad Ali
        $tA1 = Task::create([
            'task_list_id' => $listChallan->id,
            'created_by_id' => $khurram->id,
            'title' => 'Implement 1Link & Kuickpay fee challan reconciliation webhook',
            'description' => "### Objective\nConnect the admission portal to 1Link Bill Payment System via Kuickpay API for instantaneous verification of admission processing fees.\n\n### Technical Requirements\n- Validate 1Link 18-digit consumer numbers\n- Verify HMAC SHA-256 webhook signatures\n- Update applicant payment status atomically\n- Send SMS alert via gateway upon successful verification",
            'status_id' => $statusModels['In Progress']->id,
            'priority' => 'urgent',
            'start_date' => now()->subDays(3),
            'due_date' => now()->addDays(2),
            'estimated_hours' => 16.0,
            'actual_hours' => 11.5,
            'sort_order' => 1,
        ]);
        $tA1->assignees()->attach([$muhammadAli->id, $khurram->id]);
        $tA1->tags()->attach([$tagModels['Admission']->id, $tagModels['Backend']->id, $tagModels['Urgent']->id]);
        TaskChecklist::create(['task_id' => $tA1->id, 'title' => 'Sandbox IPN payload verification', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tA1->id, 'title' => 'Transaction timeout and idempotency handling', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tA1->id, 'title' => 'Generate downloadable PDF fee receipt with QR code', 'is_completed' => false]);
        TaskChecklist::create(['task_id' => $tA1->id, 'title' => 'QA sign-off with Ubaid', 'is_completed' => false]);
        TaskComment::create(['task_id' => $tA1->id, 'user_id' => $ubaid->id, 'content' => 'Tested edge cases with duplicate transaction IDs in staging. Returning 409 Conflict as expected. Ready for receipt generator testing.']);
        TaskComment::create(['task_id' => $tA1->id, 'user_id' => $muhammadAli->id, 'content' => 'Thanks Ubaid! Working on the PDF receipt generation now with barcode and QR verification.']);
        TaskTimeEntry::create(['task_id' => $tA1->id, 'user_id' => $muhammadAli->id, 'description' => 'Webhook architecture and HMAC validation', 'duration_minutes' => 380, 'started_at' => now()->subDays(2)]);
        TaskActivity::log($tA1, $khurram, 'created', 'Task assigned to Muhammad Ali');

        // Task A2: Muhammad Ali Altaf
        $tA2 = Task::create([
            'task_list_id' => $listVerification->id,
            'created_by_id' => $khubaib->id,
            'title' => 'Automated Matric & Inter equivalence calculation formula engine',
            'description' => "Build dynamic equivalence calculation engine supporting FBISE, BISE Lahore, Rawalpindi, and Cambridge O/A Levels equivalence tables.",
            'status_id' => $statusModels['In Review']->id,
            'priority' => 'high',
            'start_date' => now()->subDays(4),
            'due_date' => now()->addDay(),
            'estimated_hours' => 14.0,
            'actual_hours' => 13.0,
            'sort_order' => 2,
        ]);
        $tA2->assignees()->attach([$aliAltaf->id, $ubaid->id]);
        $tA2->tags()->attach([$tagModels['Admission']->id, $tagModels['QA']->id, $tagModels['Backend']->id]);
        TaskChecklist::create(['task_id' => $tA2->id, 'title' => 'Implement IBCC letter grade conversion matrix', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tA2->id, 'title' => 'Formula for 70% Inter + 30% Entry Test aggregate', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tA2->id, 'title' => 'Handle foreign board equivalence certificates upload', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tA2->id, 'title' => 'QA stress testing on boundary test cases', 'is_completed' => false]);
        TaskComment::create(['task_id' => $tA2->id, 'user_id' => $aliAltaf->id, 'content' => 'Calculation engine is complete and deployed to staging. @Ubaid ur Rehman please verify with borderline percentage test cases.']);
        TaskComment::create(['task_id' => $tA2->id, 'user_id' => $ubaid->id, 'content' => 'On it. Verified 20 sample records; working on edge cases with pre-medical students applying for computing programs.']);

        // Task A3: Hamza Shoulat
        $tA3 = Task::create([
            'task_list_id' => $listApplicant->id,
            'created_by_id' => $khurram->id,
            'title' => 'Responsive mobile applicant registration wizard with CNIC verification',
            'description' => 'Multi-step applicant registration wizard with real-time CNIC format formatting (XXXXX-XXXXXXX-X), OTP phone verification, and program priority reordering.',
            'status_id' => $statusModels['Done']->id,
            'priority' => 'high',
            'start_date' => now()->subDays(6),
            'due_date' => now()->subDays(1),
            'estimated_hours' => 18.0,
            'actual_hours' => 17.5,
            'sort_order' => 3,
        ]);
        $tA3->assignees()->attach([$hamza->id]);
        $tA3->tags()->attach([$tagModels['Admission']->id, $tagModels['Frontend']->id]);
        TaskChecklist::create(['task_id' => $tA3->id, 'title' => 'Step 1: Account setup & CNIC check', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tA3->id, 'title' => 'Step 2: Educational history form', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tA3->id, 'title' => 'Step 3: Program preferences reordering', 'is_completed' => true]);
        TaskComment::create(['task_id' => $tA3->id, 'user_id' => $khubaib->id, 'content' => 'Tested on mobile. Clean design and intuitive stepper navigation. Great work Hamza.']);

        // Task A4: Ubaid ur Rehman
        $tA4 = Task::create([
            'task_list_id' => $listApplicant->id,
            'created_by_id' => $khurram->id,
            'title' => 'End-to-End QA automation suite for Fall 2026 admission intake',
            'description' => 'Run comprehensive regression, security sanitization, and concurrent form submission tests across all degree programs before public launch.',
            'status_id' => $statusModels['In Progress']->id,
            'priority' => 'urgent',
            'start_date' => now()->subDays(2),
            'due_date' => now()->addDays(3),
            'estimated_hours' => 20.0,
            'actual_hours' => 9.0,
            'sort_order' => 4,
        ]);
        $tA4->assignees()->attach([$ubaid->id]);
        $tA4->tags()->attach([$tagModels['Admission']->id, $tagModels['QA']->id, $tagModels['Security']->id]);
        TaskChecklist::create(['task_id' => $tA4->id, 'title' => 'Automated Playwright smoke test scripts', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tA4->id, 'title' => 'SQL injection & XSS tests on essay input fields', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tA4->id, 'title' => 'Load testing 2,000 concurrent applicants submitting files', 'is_completed' => false]);
        TaskChecklist::create(['task_id' => $tA4->id, 'title' => 'Sign-off report for Director Khubaib Ahmed', 'is_completed' => false]);

        // Task A5: Blocked Task
        $tA5 = Task::create([
            'task_list_id' => $listApplicant->id,
            'created_by_id' => $khubaib->id,
            'title' => 'Integrate NADRA Verisys biometric verification API',
            'description' => 'Direct biometric/family tree validation via NADRA Verisys web service to prevent identity impersonation during admission entry tests.',
            'status_id' => $statusModels['Blocked']->id,
            'priority' => 'high',
            'start_date' => now()->subDays(5),
            'due_date' => now()->subDay(), // Overdue!
            'estimated_hours' => 12.0,
            'actual_hours' => 3.0,
            'sort_order' => 5,
        ]);
        $tA5->assignees()->attach([$muhammadAli->id]);
        $tA5->tags()->attach([$tagModels['Admission']->id, $tagModels['Security']->id]);
        TaskComment::create(['task_id' => $tA5->id, 'user_id' => $muhammadAli->id, 'content' => 'Waiting for the static IP whitelisting confirmation from NADRA data center. Blocked until IP is cleared.']);
        TaskComment::create(['task_id' => $tA5->id, 'user_id' => $khubaib->id, 'content' => 'Sent formal request letter to NADRA regional office today. Expecting clearance by Wednesday.']);

        // --- ORIC PROJECT TASKS ---
        // Task O1: Muhammad Ali Altaf
        $tO1 = Task::create([
            'task_list_id' => $listGrants->id,
            'created_by_id' => $khubaib->id,
            'title' => 'Build National Research Program for Universities (NRPU) proposal review workflow',
            'description' => 'Multi-tier review workflow: Faculty PI submission -> Department Chairperson Review -> Dean of Research -> ORIC Director Final Approval.',
            'status_id' => $statusModels['In Progress']->id,
            'priority' => 'high',
            'start_date' => now()->subDays(3),
            'due_date' => now()->addDays(4),
            'estimated_hours' => 16.0,
            'actual_hours' => 8.0,
            'sort_order' => 1,
        ]);
        $tO1->assignees()->attach([$aliAltaf->id, $khubaib->id]);
        $tO1->tags()->attach([$tagModels['ORIC']->id, $tagModels['Feature']->id]);
        TaskChecklist::create(['task_id' => $tO1->id, 'title' => 'Create dynamic budget itemization table with auto-sum', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tO1->id, 'title' => 'Email notification triggers on status transitions', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tO1->id, 'title' => 'Dean electronic signature approval stamp', 'is_completed' => false]);

        // Task O2: Hamza Shoulat
        $tO2 = Task::create([
            'task_list_id' => $listPatents->id,
            'created_by_id' => $khurram->id,
            'title' => 'Intellectual Property & Patent Disclosure tracking portal',
            'description' => 'Secure portal for faculty inventors to disclose patent drafts, upload CAD blueprints, and track IPO Pakistan examination stages.',
            'status_id' => $statusModels['To Do']->id,
            'priority' => 'normal',
            'start_date' => now()->addDay(),
            'due_date' => now()->addDays(8),
            'estimated_hours' => 14.0,
            'actual_hours' => 0.0,
            'sort_order' => 2,
        ]);
        $tO2->assignees()->attach([$hamza->id]);
        $tO2->tags()->attach([$tagModels['ORIC']->id, $tagModels['Frontend']->id]);

        // Task O3: Ubaid ur Rehman
        $tO3 = Task::create([
            'task_list_id' => $listPatents->id,
            'created_by_id' => $khurram->id,
            'title' => 'QA Security audit on proprietary patent documentation vault',
            'description' => 'Verify strict cryptographic encryption at rest (AES-256) and granular role isolation so unapproved reviewers cannot access IP drafts.',
            'status_id' => $statusModels['Done']->id,
            'priority' => 'urgent',
            'start_date' => now()->subDays(5),
            'due_date' => now()->subDays(2),
            'estimated_hours' => 10.0,
            'actual_hours' => 9.5,
            'sort_order' => 3,
        ]);
        $tO3->assignees()->attach([$ubaid->id]);
        $tO3->tags()->attach([$tagModels['ORIC']->id, $tagModels['Security']->id, $tagModels['QA']->id]);
        TaskChecklist::create(['task_id' => $tO3->id, 'title' => 'Check file access permissions on direct S3 URLs', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tO3->id, 'title' => 'Verify audit log captures all document download events', 'is_completed' => true]);
        TaskComment::create(['task_id' => $tO3->id, 'user_id' => $ubaid->id, 'content' => 'Vault audit passed. Temporary signed URLs expire in 5 minutes and all access is logged to audit trail.']);

        // --- LMS PROJECT TASKS ---
        // Task L1: Hamza Shoulat
        $tL1 = Task::create([
            'task_list_id' => $listEnrollment->id,
            'created_by_id' => $khurram->id,
            'title' => 'Interactive QR code and Geofenced classroom attendance tracker',
            'description' => 'Dynamic rotating QR code on faculty screen rotating every 15 seconds. Student app validates GPS coordinates against lecture hall geofence to prevent proxy attendance.',
            'status_id' => $statusModels['In Progress']->id,
            'priority' => 'urgent',
            'start_date' => now()->subDays(3),
            'due_date' => now()->addDays(2),
            'estimated_hours' => 20.0,
            'actual_hours' => 14.0,
            'sort_order' => 1,
        ]);
        $tL1->assignees()->attach([$hamza->id, $ubaid->id]);
        $tL1->tags()->attach([$tagModels['LMS']->id, $tagModels['Feature']->id, $tagModels['Urgent']->id]);
        TaskChecklist::create(['task_id' => $tL1->id, 'title' => 'WebSocket dynamic QR code refresher (15s TTL)', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tL1->id, 'title' => 'Haversine formula for geofence validation (within 50m radius)', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tL1->id, 'title' => 'Prevent multiple scans from same physical device IMEI/UUID', 'is_completed' => false]);
        TaskChecklist::create(['task_id' => $tL1->id, 'title' => 'Faculty manual override attendance sheet', 'is_completed' => false]);
        TaskComment::create(['task_id' => $tL1->id, 'user_id' => $hamza->id, 'content' => 'Testing device binding token now. QR refresh is working cleanly over WebSockets.']);
        TaskComment::create(['task_id' => $tL1->id, 'user_id' => $ubaid->id, 'content' => 'Simulated fake GPS spoofing on Android; device mock location flag is being detected properly!']);

        // Task L2: Muhammad Ali
        $tL2 = Task::create([
            'task_list_id' => $listQuizzes->id,
            'created_by_id' => $khurram->id,
            'title' => 'Online Quiz Engine with browser tab-switch detection & auto-submit',
            'description' => "Real-time quiz taking module with question shuffling, individual timer per student, and anti-cheating tab switch counter.\n\n### Rules\n- 1st tab switch: Warning modal\n- 2nd tab switch: Final warning\n- 3rd tab switch: Automatic quiz submission with penalty flag",
            'status_id' => $statusModels['In Review']->id,
            'priority' => 'high',
            'start_date' => now()->subDays(4),
            'due_date' => now(), // Due today!
            'estimated_hours' => 16.0,
            'actual_hours' => 15.0,
            'sort_order' => 2,
        ]);
        $tL2->assignees()->attach([$muhammadAli->id, $khurram->id]);
        $tL2->tags()->attach([$tagModels['LMS']->id, $tagModels['Backend']->id, $tagModels['Frontend']->id]);
        TaskChecklist::create(['task_id' => $tL2->id, 'title' => 'Page Visibility API event listener', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tL2->id, 'title' => 'Shuffle MCQ options order per student', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tL2->id, 'title' => 'Auto-save answers every 10 seconds to localStorage and DB', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tL2->id, 'title' => 'Review PR with Team Lead Khurram', 'is_completed' => false]);
        TaskComment::create(['task_id' => $tL2->id, 'user_id' => $khurram->id, 'content' => 'Reviewed the PR. Logic for auto-save during network disconnect is solid. Ready for final merge after Ubaid checks edge cases.']);

        // Task L3: Muhammad Ali Altaf
        $tL3 = Task::create([
            'task_list_id' => $listGrades->id,
            'created_by_id' => $khubaib->id,
            'title' => 'Semester GPA / CGPA calculation engine and transcript generator',
            'description' => 'Relative grading bell-curve distribution, credit-hour weighted grade point calculation, probation alert trigger (CGPA < 2.0), and official transcript PDF generation with QR verification.',
            'status_id' => $statusModels['In Progress']->id,
            'priority' => 'urgent',
            'start_date' => now()->subDays(2),
            'due_date' => now()->addDays(1),
            'estimated_hours' => 18.0,
            'actual_hours' => 10.0,
            'sort_order' => 3,
        ]);
        $tL3->assignees()->attach([$aliAltaf->id]);
        $tL3->tags()->attach([$tagModels['LMS']->id, $tagModels['Backend']->id, $tagModels['Urgent']->id]);
        TaskChecklist::create(['task_id' => $tL3->id, 'title' => 'Implement 4-point GPA scaling formula', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tL3->id, 'title' => 'Relative grading mean & standard deviation calculation', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tL3->id, 'title' => 'Generate digital verifiable transcript PDF', 'is_completed' => false]);
        TaskTimeEntry::create(['task_id' => $tL3->id, 'user_id' => $aliAltaf->id, 'description' => 'Formula implementation and statistical bell curve testing', 'duration_minutes' => 360]);

        // Task L4: Ubaid ur Rehman
        $tL4 = Task::create([
            'task_list_id' => $listQuizzes->id,
            'created_by_id' => $khurram->id,
            'title' => 'Conduct concurrent stress test on LMS assignment submission gateway',
            'description' => 'Simulate 5,000 concurrent students submitting 20MB laboratory assignment ZIP archives during the final 5 minutes before deadline.',
            'status_id' => $statusModels['Blocked']->id,
            'priority' => 'high',
            'start_date' => now()->subDays(2),
            'due_date' => now()->subDay(), // Overdue!
            'estimated_hours' => 12.0,
            'actual_hours' => 4.0,
            'sort_order' => 4,
        ]);
        $tL4->assignees()->attach([$ubaid->id, $muhammadAli->id]);
        $tL4->tags()->attach([$tagModels['LMS']->id, $tagModels['QA']->id, $tagModels['Performance']->id]);
        TaskChecklist::create(['task_id' => $tL4->id, 'title' => 'Write k6 distributed load testing script', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tL4->id, 'title' => 'Await staging environment maintenance window approval', 'is_completed' => false]);
        TaskComment::create(['task_id' => $tL4->id, 'user_id' => $ubaid->id, 'content' => 'Waiting for Director approval on off-peak maintenance window so we do not disrupt active summer semester students.']);
        TaskComment::create(['task_id' => $tL4->id, 'user_id' => $khubaib->id, 'content' => 'Approved for tonight 11:30 PM. Server team will monitor CPU and memory thresholds alongside you.']);

        // Task L5: Khurram Ahmed & Khubaib Ahmed
        $tL5 = Task::create([
            'task_list_id' => $listEnrollment->id,
            'created_by_id' => $khubaib->id,
            'title' => 'Course prerequisite check & prerequisite tree validator',
            'description' => 'Graph algorithm to validate student academic records against course prerequisite dependencies (e.g. Data Structures requires Object Oriented Programming with >= D grade).',
            'status_id' => $statusModels['Done']->id,
            'priority' => 'normal',
            'start_date' => now()->subDays(7),
            'due_date' => now()->subDays(3),
            'estimated_hours' => 14.0,
            'actual_hours' => 12.0,
            'sort_order' => 5,
        ]);
        $tL5->assignees()->attach([$khurram->id]);
        $tL5->tags()->attach([$tagModels['LMS']->id, $tagModels['Backend']->id]);
        TaskChecklist::create(['task_id' => $tL5->id, 'title' => 'Recursive prerequisite dependency tree resolver', 'is_completed' => true]);
        TaskChecklist::create(['task_id' => $tL5->id, 'title' => 'Departmental prerequisite waiver override by advisor', 'is_completed' => true]);

        // Task L6: Hamza Shoulat
        $tL6 = Task::create([
            'task_list_id' => $listQuizzes->id,
            'created_by_id' => $khurram->id,
            'title' => 'Discussion Forum with markdown rendering and code syntax highlighting',
            'description' => 'Course-specific community discussion forum with syntax highlighting for C++, Java, and Python code snippets, threaded replies, and instructor badge verification.',
            'status_id' => $statusModels['Done']->id,
            'priority' => 'normal',
            'start_date' => now()->subDays(8),
            'due_date' => now()->subDays(4),
            'estimated_hours' => 16.0,
            'actual_hours' => 15.0,
            'sort_order' => 6,
        ]);
        $tL6->assignees()->attach([$hamza->id]);
        $tL6->tags()->attach([$tagModels['LMS']->id, $tagModels['Frontend']->id]);

        // ==========================================
        // 10. NOTIFICATIONS FOR MIS TEAM
        // ==========================================
        AppNotification::create([
            'user_id' => $muhammadAli->id,
            'workspace_id' => $workspace->id,
            'type' => 'assigned',
            'title' => 'Assigned to Payment Integration',
            'message' => 'Khurram Ahmed assigned you to "Implement 1Link & Kuickpay fee challan reconciliation webhook".',
            'data' => ['task_id' => $tA1->id],
        ]);

        AppNotification::create([
            'user_id' => $aliAltaf->id,
            'workspace_id' => $workspace->id,
            'type' => 'due_soon',
            'title' => 'Task Due Tomorrow',
            'message' => '"Semester GPA / CGPA calculation engine" is due tomorrow.',
            'data' => ['task_id' => $tL3->id],
        ]);

        AppNotification::create([
            'user_id' => $hamza->id,
            'workspace_id' => $workspace->id,
            'type' => 'status_changed',
            'title' => 'Attendance Tracker Progress',
            'message' => 'Ubaid ur Rehman completed testing on dynamic QR attendance verification.',
            'data' => ['task_id' => $tL1->id],
        ]);

        AppNotification::create([
            'user_id' => $khubaib->id,
            'workspace_id' => $workspace->id,
            'type' => 'status_changed',
            'title' => 'Blocked Task Alert',
            'message' => 'NADRA Verisys verification is currently Blocked pending IP whitelisting.',
            'data' => ['task_id' => $tA5->id],
        ]);

        AppNotification::create([
            'user_id' => $ubaid->id,
            'workspace_id' => $workspace->id,
            'type' => 'assigned',
            'title' => 'New QA Review Request',
            'message' => 'Muhammad Ali Altaf requested QA testing on "Automated Matric & Inter equivalence calculation".',
            'data' => ['task_id' => $tA2->id],
        ]);

        // ==========================================
        // 11. SEED INITIAL GEMINI AI CHAT HISTORY
        // ==========================================
        $convo1 = \App\Models\AiConversation::create([
            'workspace_id' => $workspace->id,
            'user_id' => $owner->id,
            'title' => 'Admission Project Status & Assignments',
        ]);
        \App\Models\AiMessage::create([
            'ai_conversation_id' => $convo1->id,
            'role' => 'user',
            'content' => 'Who is working on the Admission Project and what are their tasks?',
        ]);
        \App\Models\AiMessage::create([
            'ai_conversation_id' => $convo1->id,
            'role' => 'assistant',
            'content' => "### 🎓 Admission Project Task Assignments\nHere are the team members currently assigned to the **Admission Project**:\n\n* **Hamza Shoulat**\n  * **Task:** Responsive mobile applicant registration wizard with CNIC verification\n  * **Status:** Done\n\n* **Muhammad Ali**\n  * **Task:** Implement 1Link & Kuickpay fee challan reconciliation webhook (In Progress, Urgent)\n  * **Task:** Integrate NADRA Verisys biometric verification API (Blocked, High)\n\n* **Muhammad Ali Altaf**\n  * **Task:** Automated Matric & Inter equivalence calculation formula engine (In Review, High)\n\n* **Ubaid ur Rehman**\n  * **Task:** End-to-End QA automation suite for Fall 2026 admission intake (In Progress, Urgent)\n  * **Task:** Automated Matric & Inter equivalence calculation formula engine (In Review)\n\n* **Khurram Ahmed**\n  * **Task:** Implement 1Link & Kuickpay fee challan reconciliation webhook (In Progress)",
        ]);

        $convo2 = \App\Models\AiConversation::create([
            'workspace_id' => $workspace->id,
            'user_id' => $owner->id,
            'title' => 'Workspace Active Blockers Review',
        ]);
        \App\Models\AiMessage::create([
            'ai_conversation_id' => $convo2->id,
            'role' => 'user',
            'content' => 'What tasks are currently blocked across the workspace and why?',
        ]);
        \App\Models\AiMessage::create([
            'ai_conversation_id' => $convo2->id,
            'role' => 'assistant',
            'content' => "### 🛑 Active Blocked Tasks Review\nAcross the workspace, **3 tasks** are currently in a blocked state:\n\n1. **Integrate NADRA Verisys biometric verification API**\n   * **Space / Project:** MIS / Admission Project\n   * **Assignee:** Muhammad Ali\n   * **Reason:** Blocked pending static IP whitelisting approval from NADRA data center.\n\n2. **Conduct concurrent stress test on LMS assignment submission gateway**\n   * **Space / Project:** MIS / LMS\n   * **Assignees:** Ubaid ur Rehman, Muhammad Ali\n   * **Reason:** Awaiting scheduled late-night staging maintenance window approval to avoid student impact.\n\n3. **Configure automated SSL certificate renewal via Cloudflare API**\n   * **Space / Project:** Engineering / Cloud Migration 3.0\n   * **Assignee:** Marcus Vance\n   * **Reason:** Awaiting scoped DNS edit API credentials from security team.",
        ]);

        $convo3 = \App\Models\AiConversation::create([
            'workspace_id' => $workspace->id,
            'user_id' => $owner->id,
            'title' => 'Ubaid QA Workload Analysis',
        ]);
        \App\Models\AiMessage::create([
            'ai_conversation_id' => $convo3->id,
            'role' => 'user',
            'content' => "Show me Ubaid ur Rehman's QA tasks and their status",
        ]);
        \App\Models\AiMessage::create([
            'ai_conversation_id' => $convo3->id,
            'role' => 'assistant',
            'content' => "### 🧪 Ubaid ur Rehman — QA Tasks & Status\n**Ubaid ur Rehman** is currently assigned to **5 tasks** across Admissions, ORIC, and LMS:\n\n* **In Progress (2):**\n  * *End-to-End QA automation suite for Fall 2026 admission intake* (Urgent, Admission Project)\n  * *Interactive QR code and Geofenced classroom attendance tracker* (Urgent, LMS)\n\n* **In Review (1):**\n  * *Automated Matric & Inter equivalence calculation formula engine* (High, Admission Project)\n\n* **Blocked (1):**\n  * *Conduct concurrent stress test on LMS assignment submission gateway* (High, LMS)\n\n* **Done (1):**\n  * *QA Security audit on proprietary patent documentation vault* (Urgent, ORIC)",
        ]);

        // ==========================================
        // 12. SEED TICKET CATEGORIES & DEFAULT ROUTING
        // ==========================================
        $catBug = TicketCategory::firstOrCreate(
            ['workspace_id' => $workspace->id, 'name' => 'Bug Report'],
            [
                'description' => 'System errors, 500 exceptions, and functional regressions.',
                'default_team_id' => $teamEng->id,
            ]
        );

        $catIt = TicketCategory::firstOrCreate(
            ['workspace_id' => $workspace->id, 'name' => 'IT & Infrastructure'],
            [
                'description' => 'VPN, server access, workstation setup, and hardware provisioning.',
                'default_team_id' => $teamMis->id,
            ]
        );

        $catUi = TicketCategory::firstOrCreate(
            ['workspace_id' => $workspace->id, 'name' => 'UI/UX & Feature Request'],
            [
                'description' => 'Design improvements, dashboard enhancements, and user journey optimization.',
                'default_team_id' => $teamDesign->id,
            ]
        );

        $catGeneral = TicketCategory::firstOrCreate(
            ['workspace_id' => $workspace->id, 'name' => 'General Support'],
            [
                'description' => 'Account inquiries, billing questions, and general guidance.',
                'default_team_id' => null,
            ]
        );

        // ==========================================
        // 13. SEED SAMPLE TICKETS & AUDIT TRAIL
        // ==========================================
        // Ticket 1: Urgent Overdue Open Bug
        $tck1 = Ticket::firstOrCreate(
            ['workspace_id' => $workspace->id, 'ticket_number' => 'TCK-1001'],
            [
                'subject' => 'Critical: Biometric verification service failure during peak student intake',
                'description' => "Students submitting their admission applications are receiving timeout errors when authenticating via NADRA Verisys. The API gateway returns HTTP 504 Gateway Timeout intermittently.\n\nImmediate triage required to prevent drop-off in active admission intake.",
                'category_id' => $catBug->id,
                'priority' => 'urgent',
                'status' => 'open',
                'raised_by_user_id' => $hamza->id,
                'assigned_team_id' => $teamEng->id,
                'assigned_to_user_id' => null,
                'due_by' => now()->subHours(2), // Overdue for demonstration!
                'created_at' => now()->subHours(6),
            ]
        );
        TicketActivityLog::log($tck1, $hamza, 'created', null, 'Ticket created with urgent priority');

        // Ticket 2: High Priority In Progress Ticket
        $tck2 = Ticket::firstOrCreate(
            ['workspace_id' => $workspace->id, 'ticket_number' => 'TCK-1002'],
            [
                'subject' => 'VPN Gateway certificate expiration affecting remote developers',
                'description' => "The primary WireGuard SSL certificate for the engineering subnet will expire within 48 hours. Team members working remotely are unable to connect to the staging cluster.",
                'category_id' => $catIt->id,
                'priority' => 'high',
                'status' => 'in_progress',
                'raised_by_user_id' => $ubaid->id,
                'assigned_team_id' => $teamMis->id,
                'assigned_to_user_id' => $khurram->id,
                'due_by' => now()->addHours(18),
                'created_at' => now()->subHours(6),
            ]
        );
        TicketActivityLog::log($tck2, $ubaid, 'created', null, 'Ticket created with high priority');
        TicketActivityLog::log($tck2, $khubaib, 'assigned_team', null, 'MIS');
        TicketActivityLog::log($tck2, $khubaib, 'assigned_user', null, 'Khurram Ahmed');
        TicketActivityLog::log($tck2, $khurram, 'status_changed', 'assigned', 'in_progress');

        TicketComment::create([
            'ticket_id' => $tck2->id,
            'user_id' => $khurram->id,
            'body' => 'I have generated the CSR and submitted it to our institutional CA. Expecting updated cert files within 2 hours.',
            'is_internal_note' => false,
            'created_at' => now()->subHours(3),
        ]);

        TicketComment::create([
            'ticket_id' => $tck2->id,
            'user_id' => $khubaib->id,
            'body' => 'Internal Note: Ensure the failover backup profile on server 10.0.1.50 is tested before applying to production.',
            'is_internal_note' => true,
            'created_at' => now()->subHours(2),
        ]);

        // Ticket 3: Normal Priority Assigned Ticket
        $tck3 = Ticket::firstOrCreate(
            ['workspace_id' => $workspace->id, 'ticket_number' => 'TCK-1003'],
            [
                'subject' => 'Mobile navigation drawer closes unexpectedly on iOS Safari',
                'description' => 'When tapping on the filter drawer in the student admissions review screen on Safari iOS 17.5, the drawer flickers and dismisses without applying chosen filter values.',
                'category_id' => $catUi->id,
                'priority' => 'normal',
                'status' => 'assigned',
                'raised_by_user_id' => $aliAltaf->id,
                'assigned_team_id' => $teamDesign->id,
                'assigned_to_user_id' => $elena->id,
                'due_by' => now()->addDays(2),
                'created_at' => now()->subDay(),
            ]
        );
        TicketActivityLog::log($tck3, $aliAltaf, 'created', null, 'Ticket created');
        TicketActivityLog::log($tck3, $owner, 'assigned_team', null, 'Product Design & UI/UX');
        TicketActivityLog::log($tck3, $owner, 'assigned_user', null, 'Elena Rostova');

        // Ticket 4: Resolved Ticket with Summary
        $tck4 = Ticket::firstOrCreate(
            ['workspace_id' => $workspace->id, 'ticket_number' => 'TCK-1004'],
            [
                'subject' => 'Student grade report PDF export character encoding glitch in Urdu names',
                'description' => 'Transcripts exported as PDF displayed broken Arabic/Nastaliq glyphs for student names written in Urdu.',
                'category_id' => $catBug->id,
                'priority' => 'high',
                'status' => 'resolved',
                'raised_by_user_id' => $muhammadAli->id,
                'assigned_team_id' => $teamEng->id,
                'assigned_to_user_id' => $sarah->id,
                'due_by' => now()->subHours(10),
                'resolution_summary' => 'Integrated Noto Sans Arabic variable TrueType font into the DomPDF rendering pipeline and configured UTF-8 font-family fallbacks in CSS.',
                'resolved_at' => now()->subHours(4),
                'created_at' => now()->subDays(2),
            ]
        );
        TicketActivityLog::log($tck4, $muhammadAli, 'created', null, 'Ticket created');
        TicketActivityLog::log($tck4, $owner, 'assigned_user', null, 'Sarah Jenkins');
        TicketActivityLog::log($tck4, $sarah, 'status_changed', 'in_progress', 'resolved');

        // Ticket 5: General Support Ticket
        $tck5 = Ticket::firstOrCreate(
            ['workspace_id' => $workspace->id, 'ticket_number' => 'TCK-1005'],
            [
                'subject' => 'Request for secondary monitor and ergonomic keyboard setup',
                'description' => 'Need an additional 27-inch 4K monitor and mechanical keyboard for the new workstation setup in MIS Room 302.',
                'category_id' => $catGeneral->id,
                'priority' => 'low',
                'status' => 'open',
                'raised_by_user_id' => $ubaid->id,
                'due_by' => now()->addDays(4),
                'created_at' => now()->subHours(2),
            ]
        );
        TicketActivityLog::log($tck5, $ubaid, 'created', null, 'Ticket created');

        // ==========================================
        // 14. SEED SAMPLE ACCESS REQUESTS
        // ==========================================
        AccessRequest::firstOrCreate(
            ['email' => 'ayesha.tariq@stmu.edu.pk'],
            [
                'name' => 'Dr. Ayesha Tariq',
                'workspace_id' => $workspace->id,
                'department' => 'Department of Pathology & Diagnostics',
                'reason' => 'Leading the pathology lab digitization module. Require access to collaborate on clinical trial task lists and track lab software bug reports.',
                'status' => 'pending',
                'assigned_role' => 'member',
            ]
        );

        AccessRequest::firstOrCreate(
            ['email' => 'zainab.malik@stmu.edu.pk'],
            [
                'name' => 'Zainab Malik',
                'workspace_id' => $workspace->id,
                'department' => 'Examination & Accreditation Cell',
                'reason' => 'Need access to inspect semester grade audit workflows and review QA checklist items before official transcripts release.',
                'status' => 'pending',
                'assigned_role' => 'member',
            ]
        );

        AccessRequest::firstOrCreate(
            ['email' => 'faisal.kamran@partner.stmu.edu.pk'],
            [
                'name' => 'Faisal Kamran',
                'workspace_id' => $workspace->id,
                'department' => 'External Systems Auditor',
                'reason' => 'External security compliance auditor for ISO 27001 accreditation.',
                'status' => 'approved',
                'assigned_role' => 'guest',
                'reviewed_by_id' => $owner->id,
                'reviewed_at' => now()->subDays(1),
            ]
        );

        AccessRequest::firstOrCreate(
            ['email' => 'spammer@randomdomain.xyz'],
            [
                'name' => 'John Anonymous',
                'workspace_id' => $workspace->id,
                'department' => 'Unknown',
                'reason' => 'Looking around.',
                'status' => 'rejected',
                'rejection_reason' => 'Non-institutional email address provided. Please register using your verified STMU faculty or staff credentials.',
                'reviewed_by_id' => $owner->id,
                'reviewed_at' => now()->subDays(2),
            ]
        );
    }
}
