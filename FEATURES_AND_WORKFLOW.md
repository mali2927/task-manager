# 📘 STMU MIS Task & Helpdesk Service Platform: Features & Workflow Specification

**Version:** 2.5 (Enterprise Edition)  
**Target Environment:** Shifa Tameer-e-Millat University (STMU) MIS Directorate  
**Architecture:** Laravel 12/13, Livewire 3/4, Alpine.js, Tailwind CSS v4, Flux UI, Chart.js 4, Google Gemini AI, WebAuthn Passkeys  
**Documentation Date:** October 2026  

---

## 📑 Table of Contents
1. [Executive Summary & System Vision](#1-executive-summary--system-vision)
2. [Hierarchical Organization & Multi-Tenancy Architecture](#2-hierarchical-organization--multi-tenancy-architecture)
3. [End-to-End Core Workflows](#3-end-to-end-core-workflows)
   - [Workflow 1: Task Lifecycle & Agile Sprint Execution](#workflow-1-task-lifecycle--agile-sprint-execution)
   - [Workflow 2: Helpdesk Ticketing, Smart Triage & SLA Resolution](#workflow-2-helpdesk-ticketing-smart-triage--sla-resolution)
   - [Workflow 3: Interactive Dashboard & Influx Analytics Hub](#workflow-3-interactive-dashboard--influx-analytics-hub)
   - [Workflow 4: Google Gemini AI Operations & Standup Synthesis](#workflow-4-google-gemini-ai-operations--standup-synthesis)
   - [Workflow 5: Passkeys (WebAuthn), RBAC & ISO 27001 Audit Telemetry](#workflow-5-passkeys-webauthn-rbac--iso-27001-audit-telemetry)
4. [Module Feature Matrix](#4-module-feature-matrix)
5. [Database Model & Relationship Map](#5-database-model--relationship-map)
6. [User Roles & Security Personas Guide](#6-user-roles--security-personas-guide)
7. [API & Export Services](#7-api--export-services)

---

## 1. Executive Summary & System Vision

The **STMU MIS Task & Helpdesk Service Platform** is an enterprise-grade, unified operational operating system modeled after the industry-leading ergonomics of **ClickUp** and **Jira Service Management**. Designed specifically for high-velocity university operations, medical complex coordination, and IT management, it seamlessly bridges **Agile task management** with **customer-facing service desk ticketing**.

### Key Architectural Pillars
- **Unified Ticket-to-Sprint Bridge:** Unlike traditional decoupled helpdesk tools, every support ticket links directly to a software engineering project (`project_id`), enabling immediate triage from bug report to backlog task.
- **Dynamic SLA Timers & Capacity Balancing:** Enforces strict compliance with automatic resolution countdowns and workload-aware assignment algorithms that prevent engineer burnout.
- **Interactive Time-Travel Analytics:** High-performance bundled Chart.js graphs allowing instant month-over-month, year-wise, and trailing 12-month trend comparisons.
- **Resilient AI Operations:** Google Gemini multi-model pool failover with deterministic local fallbacks for automated standups, executive digests, and natural language trend analysis.
- **ISO 27001 Compliance & Biometrics:** Cryptographic FIDO2/WebAuthn Passkey sign-ins, two-factor authentication, and immutable chronological audit trails capturing every system mutation.

---

## 2. Hierarchical Organization & Multi-Tenancy Architecture

The platform organizes work across five distinct hierarchical tiers, providing both deep departmental isolation and cross-functional visibility:

```
[ University Platform ]
         │
         ├──► [ Workspace: MIS Innovation Hub ] (Multi-Tenant Isolation)
         │           │
         │           ├──► [ Space: Core Systems ] (Departmental Domain)
         │           │           │
         │           │           └──► [ Project: Student Portal Modernization ]
         │           │                       │
         │           │                       ├──► [ Task List: Sprint 26 Backlog ]
         │           │                       │           │
         │           │                       │           ├──► Task 1: Refactor GPA Engine
         │           │                       │           └──► Task 2: Fix Voucher Sync
         │           │                       │
         │           │                       └──► [ Linked Tickets ] (TCK-MIS-1049, TCK-MIS-1052)
         │           │
         │           ├──► [ Space: Hospital & Clinical IT ]
         │           │           └──► [ Project: Biometric Ward Terminals ]
         │           │
         │           └──► [ Teams & Squads ] (e.g., Core DevOps, ERP Squad)
         │                       │
         │                       └──► Engineers (Max Capacity Limits & Workload Tracking)
```

### Hierarchy Breakdown
1. **Workspaces (`workspaces` table):** Top-level multi-tenant boundary. Enforces team membership, custom task statuses, ticket categories, and independent URL routing (`/workspace/{slug}`).
2. **Spaces (`spaces` table):** Major institutional divisions (e.g., *Core Systems*, *Admissions & Registrar*, *Hospital IT*, *ORIC Research*). Each Space has custom branding, icons, and themes.
3. **Projects (`projects` table):** Deliverable software systems or initiatives. Projects own Task Lists and are directly linked to customer-reported Support Tickets.
4. **Task Lists (`task_lists` table):** Kanban columns or sprint stages (e.g., *Backlog*, *In Development*, *QA Verification*, *Deployed*).
5. **Tasks (`tasks` table):** Atom of execution. Supports checklists, start/due dates, priority tags, markdown notes, file attachments, and subtasks.
6. **Support Tickets (`tickets` table):** External or internal service requests tagged by Category, Project, SLA, and Assigned Engineer.

---

## 3. End-to-End Core Workflows

### Workflow 1: Task Lifecycle & Agile Sprint Execution

```
[ Create Task ]
       │
       ▼
[ Assignee & Priority ] ──► [ Set Start & Due Date ] ──► [ Add Checklists / Subtasks ]
       │
       ▼
[ Kanban Status Transition ] (To Do ➔ In Progress ➔ QA Review ➔ Done)
       │
       ▼
[ Automatic Time Tracking & Audit Log Entry Recorded ]
```

1. **Task Inception:**
   - Tasks are created via global shortcuts, the project board, or escalated directly from incoming helpdesk tickets.
   - Title, rich Markdown description, priority (*Low*, *Normal*, *High*, *Urgent*), due dates, and assignee(s) are designated.
2. **Multi-View Execution:**
   - **Kanban Board:** Drag-and-drop cards between status columns. Visual progress chips reflect completed checklist items.
   - **Table View:** Inline editing of assignees, priorities, and deadlines without opening modal dialogs.
   - **Calendar View:** Month and week scheduling views highlighting deadline density.
   - **Gantt / Timeline View:** Milestone tracking displaying task duration bars and sequential dependencies.
3. **Personal Queue (`My Tasks`):**
   - Individual engineers view their workload grouped into *Overdue*, *Due Today*, *Upcoming This Week*, and *Completed*.
4. **Lifecycle Audit:**
   - Every status move, date change, and comment writes a permanent record to `task_activities`.

---

### Workflow 2: Helpdesk Ticketing, Smart Triage & SLA Resolution

```
[ Client Submits Ticket ]
       │
       ▼
[ Calculate Dynamic SLA Due Date ] (Urgent: 4h | High: 24h | Normal: 72h | Low: 120h)
       │
       ▼
[ Smart Capacity Assignment ] ──► Evaluates Least-Loaded Engineer in Assigned Team
       │
       ▼
[ Engineer Investigates & Transitions to 'In Progress' ]
       │
       ▼
[ Mandatory Resolution Summary Entered ] ──► Fix Verified & Deployed
       │
       ▼
[ Status: Resolved ] ──► Client Notified
       │
       ▼
[ Requester Portal ] ──► [ Client Rates 1-5 Stars ] OR [ Self-Service Reopens Ticket ]
```

1. **Ticket Creation:**
   - Requester (student, faculty, clinical staff) submits issue specifying Category (*Student Portal*, *ERP Billing*, *WiFi*, *Hardware*), Project, Urgency, and Attachments.
   - Unique tracking code generated (e.g., `TCK-W1-4092-49`).
2. **SLA Clock Initialization:**
   - Dynamic SLA target computed immediately:
     - **Urgent:** 4 Hours SLA
     - **High:** 24 Hours SLA
     - **Normal:** 72 Hours (3 Days) SLA
     - **Low:** 120 Hours (5 Days) SLA
3. **Smart Triage & Load Balancing:**
   - `TicketCapacityService` inspects the target Team. It checks each agent's active ticket count against their defined `capacity_limit` and flags overload warnings if an engineer is at $\ge 100\%$ load.
4. **Resolution Gate:**
   - Marking a ticket `Resolved` requires entering a **Resolution Summary**. Tickets cannot be closed without documenting root cause and fix verification.
5. **Customer Feedback & Reopening:**
   - Requesters view their tickets at `/workspace/{slug}/tickets/my`. If unsatisfied, they can submit a reopening reason within 7 days, which transitions the ticket back to `open` and notifies the engineer.

---

### Workflow 3: Interactive Dashboard & Influx Analytics Hub

```
[ Select Time Range ] (Month-Wise | Last Month | Year-Wise | 12 Months)
       │
       ├──► [ Influx & Velocity Curve ] ──► Tickets Created vs Resolved vs Tasks Completed
       │
       ├──► [ Issue Category Doughnut ] ──► Slices with 12px Hover Offset & Center Counter
       │
       ├──► [ Project Workload Matrix ] ──► Defect Volume vs Active Sprint Task Load
       │
       └──► [ Automated MoM Math ] ──► Computes Net Change & Percentage Deltas (Δ%)
```

1. **Time-Travel Granularity:**
   - **Month-Wise:** Day-by-day stepped curves (1st to 31st) for current month.
   - **Last Month:** Instantly shifts calculations to previous month.
   - **Year-Wise:** 12-month distribution for selected calendar year (2024–2027).
   - **12 Months:** Rolling 1-year trailing telemetry.
2. **Animated Visual Canvases:**
   - Local bundled Chart.js renders with 1000ms Quartic easing animations, glowing linear gradients under Influx/Resolution curves, and hover pop-out effects.
3. **MoM Trend Calculations:**
   - Automated delta engine compares current window to previous equivalent period:
     $$\Delta\% = \left( \frac{\text{Current} - \text{Previous}}{\text{Previous}} \right) \times 100$$
4. **Platform Risk Radar:**
   - Real-time KPI cards alerting on Overdue Tasks, Breached SLAs, and Blocked blockers.

---

### Workflow 4: Google Gemini AI Operations & Standup Synthesis

```
[ Livewire Trigger ] ──► [ Zero-Leakage Context Assembly ]
                               │
                               ▼
               [ Multi-Model Failover Engine ]
               ├─ 1. gemini-flash-lite-latest (Primary)
               ├─ 2. gemini-3-flash-preview   (Secondary)
               ├─ 3. gemini-3.8-flash         (Tertiary)
               └─ 4. Deterministic Local Math (Offline Fallback)
                               │
                               ▼
     [ Formatted Executive Briefing / Agile Standup / Trend Insights ]
```

1. **Executive Team Briefing:** Synthesizes institutional throughput, critical defects, and sprint velocity for directors.
2. **Personal Daily Standup:** Summarizes an engineer's past 24 hours, active in-progress items, and blocker alerts.
3. **AI Trend Diagnostics:** Analyzes historical surges and answers natural language questions (e.g., *"Why did student portal tickets spike this week?"*).
4. **Zero-Leakage Security Model:** The user's role and workspace boundary strictly constrain prompt context before dispatching to the Gemini API, preventing data exfiltration.

---

### Workflow 5: Passkeys (WebAuthn), RBAC & ISO 27001 Audit Telemetry

```
[ User Browser / Device ]
       │
       ├──► [ FIDO2 WebAuthn Passkey (Fingerprint / FaceID / YubiKey) ] ──► Instant Zero-Password Auth
       │
       └──► [ Standard Login + 2FA TOTP Code ]
                     │
                     ▼
       [ Role-Based Access Control Evaluation ]
       ├─ Owner / Admin: Full Configuration, Triage, AI, Telemetry
       ├─ Member: Tasks, Sprint Boards, Assigned Tickets
       └─ Requester: Restricted to Personal Ticket Portal
                     │
                     ▼
       [ Immutable Audit Trail Logged to DB & CSV Export ]
```

1. **Biometric Passkey Registration:** Users register TouchID, Windows Hello, or hardware keys for cryptographic passwordless login.
2. **Role Boundaries:**
   - Requesters are strictly confined to `/workspace/{slug}/tickets/my` and cannot inspect internal task boards or other users' tickets.
3. **ISO 27001 Audit Trail:** Every update, deletion, assignment, and status transition is recorded chronologically in `ticket_activity_logs` and `task_activities`, with CSV export capabilities.

---

## 4. Module Feature Matrix

| Module Name | UI Endpoint | Key Capabilities | Underlying Services / Models |
| :--- | :--- | :--- | :--- |
| **Interactive Dashboard** | `/workspace/{slug}` | Multi-dataset velocity curve, category doughnut, workload matrix, time-travel pills, MoM delta KPIs, risk radar. | `Dashboard.php`, Chart.js, `Ticket`, `Task`, `Project` |
| **Kanban Task Board** | `/workspace/{slug}/tasks` | Drag-and-drop columns, priority chips, checklists, due dates, task details modal, custom status workflows. | `TaskManager.php`, `Task`, `TaskStatus`, `TaskList` |
| **My Personal Tasks** | `/workspace/{slug}/tasks/my` | Focus queue segmented into Overdue, Today, This Week, and Done. | `MyTasks.php`, `Task` |
| **Service Desk Queue** | `/workspace/{slug}/tickets/queue` | Incoming ticket triage, SLA countdown badges, priority filters, project association, engineer assignment. | `TicketQueue.php`, `Ticket`, `TicketCategory` |
| **Smart Agent Capacity**| `/workspace/{slug}/tickets/capacity` | Workload heatmaps, capacity thresholds (e.g. 5 tickets max), rebalancing engine. | `TicketCapacityService.php`, `Team`, `User` |
| **Requester Portal** | `/workspace/{slug}/tickets/my` | Zero-trust isolated self-service portal, ticket submission, status tracking, rating feedback, reopening. | `MyTickets.php`, `Ticket` |
| **Gemini AI Operations** | Top Navbar / Dashboard | Executive digest, daily standups, trend diagnostics, multi-model failover pool. | `GeminiService.php`, Google Gemini API |
| **Audit Logs Telemetry** | `/workspace/{slug}/activity-logs` | Chronological immutable trail of all events, filters by user/type, ISO compliance, CSV export. | `ActivityLogs.php`, `TicketActivityLog`, `TaskActivity` |
| **Team Management** | `/workspace/{slug}/teams` | Squad definitions, team leads, department spaces, capacity allocations. | `TeamManager.php`, `Team`, `Space` |
| **Passkeys & Security** | `/settings/security` | FIDO2 WebAuthn biometric keys, 2FA recovery codes, session security. | `PasskeyController.php`, `laravel/passkeys` |

---

## 5. Database Model & Relationship Map

### Core Tables & Foreign Keys

```
workspaces
 ├── id, name, slug, owner_id
 └── hasMany: spaces, teams, taskStatuses, ticketCategories, tickets

spaces
 ├── id, workspace_id, name, color, icon
 └── hasMany: projects

projects
 ├── id, space_id, name, color, icon
 ├── hasMany: taskLists, tasks, tickets
 └── belongsTo: space

task_lists
 ├── id, project_id, name, sort_order
 └── hasMany: tasks

tasks
 ├── id, task_list_id, status_id, created_by_id, title, description, priority, due_date
 ├── belongsTo: taskList, status, creator
 └── hasMany: taskActivities, subtasks, checklists, taskAssignees

tickets
 ├── id, workspace_id, project_id, category_id, raised_by_user_id, assigned_to_user_id, assigned_team_id
 ├── ticket_number, subject, description, priority, status, due_by, resolved_at, resolution_summary
 ├── belongsTo: workspace, project, category, requester, assignee, team
 └── hasMany: ticketActivityLogs, ticketComments

teams
 ├── id, workspace_id, lead_user_id, name, capacity_limit
 └── belongsToMany: users (team_user pivot)
```

---

## 6. User Roles & Security Personas Guide

| Persona | System Role | Primary Responsibilities | Authorized Views |
| :--- | :--- | :--- | :--- |
| **Executive Director** | `Owner` | Institutional oversight, strategic delivery velocity, cross-period KPI tracking, AI executive digests. | Full platform access, all workspaces, system telemetry. |
| **MIS Lead / Scrum Master** | `Admin` | Sprint backlog planning, triage queue management, assigning tickets, monitoring agent capacity limits. | Workspace configuration, task boards, triage queue, capacity manager. |
| **Software Engineer** | `Member` | Executing sprint tasks, updating checklists, resolving defect tickets, entering mandatory resolution summaries. | Task boards, sprint lists, personal tasks, assigned ticket queue. |
| **Faculty / Student Client**| `Requester` | Submitting support tickets, tracking SLA resolution timers, rating completed service, reopening issues. | **Restricted exclusively to `/workspace/{slug}/tickets/my`**. |
| **ISO Compliance Auditor** | `Guest` | Auditing system event logs, verifying SLA compliance, exporting chronological audit trails. | Read-only access to Audit & Activity Trails (`/activity-logs`). |

---

## 7. API & Export Services

### Export Endpoints
- **Tasks CSV Export:** `GET /workspace/{slug}/tasks/export` — Exports complete task backlogs with assignees, statuses, priorities, and timestamps.
- **Tickets CSV Export:** `GET /workspace/{slug}/tickets/export` — Exports helpdesk tickets with SLA metrics, categories, resolution times, and ratings.
- **Audit Logs CSV Export:** `GET /workspace/{slug}/activity-logs/export` — Exports raw chronological telemetry records formatted for compliance audits.

---

*Authored by STMU MIS Directorate & Advanced Engineering Team. Built with Laravel, Livewire, and Google Gemini AI.*
