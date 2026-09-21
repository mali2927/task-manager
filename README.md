# Team Task Manager (ClickUp-Style)

A full-featured, professional task management web application built with **Laravel 13**, **Livewire 4**, **Flux UI**, **Tailwind CSS v4**, and **Google Gemini AI** (`gemini-3.6-flash`).

---

## 🌟 Features Overview

### 1. Hierarchy & Organization
- **Workspaces:** Multi-tenant workspace support with custom slugs, branding, and members.
- **Teams:** Group members into cross-functional teams with assigned leads.
- **Spaces:** Department or domain-level spaces (e.g. *Engineering*, *Product Design*, *Growth & Marketing*).
- **Projects & Lists:** Nested folders and lists for sprint backlogs, release cycles, and feature tracks.
- **Roles & Permissions:** Owner, Admin, Member, and Guest (view-only) role enforcement.

### 2. Multi-View Task Management
- **Kanban Board View:** Drag-and-drop / column status updates, priority color badges, checklist progress indicators, and instant card creation.
- **Table / List View:** Sortable and filterable task rows grouped by status with inline dropdown modifiers.
- **Calendar View:** Monthly visual grid mapping task deadlines with color-coded status chips.
- **Gantt & Timeline View:** Visual duration spans representing start dates, due dates, and task completion.
- **My Tasks View:** Personal focused dashboard grouped into *Overdue*, *Due Today*, *Upcoming*, and *Completed*.

### 3. Rich Task Details (Slide-over Drawer)
- **Status & Priority Picker:** Urgent, High, Normal, and Low with visual indicators.
- **Interactive Checklists / Todos:** Add, complete, and delete checklist items with instant progress bar calculation.
- **Nested Subtasks:** Subtasks with their own statuses and assignees.
- **Time Tracking:** Live start/stop timer widget and manual time entry logs with duration formatting (e.g., `3h 30m`).
- **File Attachments:** Drag-and-drop upload with image preview, size formatting, and deletion.
- **Threaded Comments:** Discussion threads with author avatars, relative timestamps, and nested replies.
- **Full Activity Audit Trail:** Automated logs of status changes, assignments, and time logs.

### 4. Dashboards & Analytics
- **Personal KPI Summary:** Open, Overdue (with urgent alerts), Due Today, and Due This Week.
- **Team Throughput & Velocity:** Completion percentage and status distribution breakdown.
- **Workload Capacity View:** Active task count per member with visual capacity bars.
- **Overdue Task Report:** Instant alerts for bottlenecked tasks.
- **Workspace Activity Stream:** Live audit feed across the organization.
- **CSV Data Export:** One-click CSV export of workspace tasks and sprint metrics.

### 5. Google Gemini AI Suite (`gemini-3.6-flash`)
- **Task Summary Generator:** Analyzes task descriptions, checklist progress, and discussion comments to produce a 3-part briefing (Objective & Status, Blockers & Risks, Next Steps).
- **Personal Daily Briefing:** Synthesizes overdue and upcoming tasks by urgency into an executive morning digest.
- **Manager Team Progress Report:** Aggregates delivery health, velocity, and risks across spaces.
- **Agile Standup Generator:** Auto-generates "Accomplished", "Working on Today", and "Blockers" for daily standups.
- **Natural Language Task Query Assistant:** Conversational search grounded strictly in database records (e.g. *"What tasks are blocked?"*, *"What is unassigned in Engineering?"*).
- **Smart Attribute Suggestions:** Recommends priority and tags based on title and description.

---

## 🚀 Quickstart & Installation

### 1. Prerequisites
- PHP 8.3+ with `pdo_mysql`
- MySQL 8.0+
- Node.js 18+ & npm
- Composer 2+

### 2. Clone & Setup
```bash
# Clone the repository
git clone <repo-url> task-manager
cd task-manager

# Install dependencies
composer install
npm install
```

### 3. Environment Configuration
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database and Gemini API key in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task-manager
DB_USERNAME=root
DB_PASSWORD=your_password

# Google Gemini API
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-3.6-flash
GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1beta
```

### 4. Run Migrations & Demo Seed Data
```bash
# Run database migrations
php artisan migrate

# Seed rich enterprise demo data (Acme Innovation Labs)
php artisan db:seed
```

### 5. Build Assets & Start Local Server
```bash
# Build frontend assets (Tailwind CSS v4 & Flux)
npm run build

# Start Laravel development server
php artisan serve
```

Visit `http://localhost:8000` in your browser.

---

## 👥 Demo User Accounts

All demo users are pre-seeded with the password: `password`

### MIS Team Accounts
| Name | Email | Role | Title |
|------|-------|------|-------|
| **Khubaib Ahmed** | `khubaib@example.com` | Admin | Director MIS |
| **Khurram Ahmed** | `khurram@example.com` | Admin | Team Lead |
| **Hamza Shoulat** | `hamza@example.com` | Member | Software Engineer |
| **Muhammad Ali Altaf** | `ali.altaf@example.com` | Member | Software Engineer |
| **Muhammad Ali** | `muhammad.ali@example.com` | Member | Software Engineer |
| **Ubaid ur Rehman** | `ubaid@example.com` | Member | QA Engineer |

### Core Platform Accounts
| Name | Email | Role | Title |
|------|-------|------|-------|
| **Alex Rivers** | `test@example.com` | Owner | Founder & Lead Architect |
| **Sarah Chen** | `sarah.chen@acme.io` | Admin | Senior Frontend Engineer |
| **Marcus Vance** | `marcus.vance@acme.io` | Member | Cloud & DevOps Architect |
| **Elena Rostova** | `elena.rostova@acme.io` | Member | Lead Product Designer |

---

### 🏢 MIS Space & Projects Structure

1. **Admission Project**
   - *Applicant Portal & Registration*
   - *Document Verification & Merit Lists*
   - *Fee Challan & Bank Integration*
2. **ORIC (Office of Research, Innovation & Commercialization)**
   - *Research Grants & Proposals*
   - *Patents & Commercialization*
3. **LMS (Learning Management System)**
   - *Course Enrollment & Attendance*
   - *Quiz & Assignment Submissions*
   - *Gradebook & Transcripts*

---

## 🧪 Running Tests

The test suite covers models, relationships, task CRUD, status transitions, Livewire components, and Gemini AI services with mock responses.

```bash
# Execute PHPUnit / Feature test suite
php artisan test
```

---

## 📁 Key Architectural Files

- **AI Service:** [`app/Services/GeminiService.php`](file:///home/muhammad-ali/task-manager/app/Services/GeminiService.php)
- **Livewire Components:**
  - Task Manager: [`app/Livewire/Tasks/TaskManager.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Tasks/TaskManager.php)
  - Task Detail Drawer: [`app/Livewire/Tasks/TaskDetailModal.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Tasks/TaskDetailModal.php)
  - Dashboard: [`app/Livewire/Dashboard/Dashboard.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Dashboard/Dashboard.php)
  - My Tasks: [`app/Livewire/Tasks/MyTasks.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Tasks/MyTasks.php)
  - Teams & Members: [`app/Livewire/Workspace/TeamManager.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Workspace/TeamManager.php)
  - AI Assistant: [`app/Livewire/Ai/AiAssistant.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Ai/AiAssistant.php)
  - Notification Bell: [`app/Livewire/Notifications/NotificationBell.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Notifications/NotificationBell.php)
- **Hierarchy Migrations:**
  - [`database/migrations/2026_09_18_064001_create_task_manager_hierarchy_tables.php`](file:///home/muhammad-ali/task-manager/database/migrations/2026_09_18_064001_create_task_manager_hierarchy_tables.php)
  - [`database/migrations/2026_09_18_064002_create_tasks_and_activity_tables.php`](file:///home/muhammad-ali/task-manager/database/migrations/2026_09_18_064002_create_tasks_and_activity_tables.php)
- **Seeder:** [`database/seeders/DatabaseSeeder.php`](file:///home/muhammad-ali/task-manager/database/seeders/DatabaseSeeder.php)
