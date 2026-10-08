# 🚀 STMU MIS Task & Service Desk Platform (ClickUp & Jira-Style)

A high-performance, enterprise-grade task management and helpdesk ticketing web application built with **Laravel 12/13**, **Livewire 3/4**, **Flux UI**, **Tailwind CSS v4**, **Chart.js**, and **Google Gemini AI** (`gemini-flash-lite-latest` with resilient multi-model failover).

---

## 🌟 Features Overview

### 1. Hierarchy & Multi-Tenant Organization
- **Workspaces:** Multi-tenant workspace isolation with custom branding, members, and slug routing (`/workspace/{slug}`).
- **Teams & Squads:** Group members into cross-functional delivery units with assigned leads and capacity limits.
- **Spaces:** Department-level domains (e.g., *Admissions*, *LMS*, *ORIC*, *Clinical Systems*).
- **Projects & Lists:** Nested projects and task lists for sprint tracking, release cycles, and helpdesk ticket linking.
- **Multi-Tier RBAC:** Strict separation of privileges across **Owner**, **Admin**, **Member**, and **Requester** roles.

### 2. Multi-View Task Management
- **Kanban Board:** Drag-and-drop / column status transitions, priority badges, checklist progress chips, and instant card creation.
- **Table / List View:** Grouped task rows with inline status, priority, and assignee dropdown modifiers.
- **Calendar Grid:** Visual deadline tracking across monthly calendars.
- **Gantt & Timeline:** Duration spans tracking start dates, due dates, milestones, and completion.
- **My Tasks View:** Personal delivery queue segmented into *Overdue*, *Due Today*, *Upcoming*, and *Completed*.

### 3. Helpdesk Ticketing & SLA Engine with Project Association
- **Project-Linked Tickets:** Helpdesk tickets are directly linked to software projects (`project_id`), bridging user issues with sprint backlogs.
- **Live SLA Timers:** Dynamic resolution countdowns with urgency thresholds (*Urgent: 4h*, *High: 24h*, *Normal: 72h*, *Low: 120h*).
- **Agent Capacity Balancing:** [`TicketCapacityService`](file:///home/muhammad-ali/task-manager/app/Services/TicketCapacityService.php) evaluates active agent workloads and recommends the least-loaded engineer.
- **Triage Queue:** Interactive filterable queue for leads and admins to triage, assign, and prioritize incoming requests.
- **Resolution Summary:** Mandatory technical explanation required before marking tickets as `Resolved`.
- **Requester Portal:** Zero-trust, isolated portal for faculty and students (`/workspace/{slug}/tickets/my`) with self-service ticket reopening.

### 4. Interactive Analytics & Cross-Period Comparison Hub
- **Multi-Dataset Velocity Curves:** Playable Chart.js line and column graphs comparing *Tickets Influx*, *Tickets Resolved*, *Tasks Created*, and *Tasks Completed*.
- **Project Workload Matrix:** Grouped bar charts visualizing defect load vs sprint velocity per project.
- **Category Doughnut:** Visual breakdown of issue taxonomy (*Bug Reports*, *IT Infrastructure*, *Feature Requests*, *Access Requests*).
- **Cross-Period Filtering:** Real-time toggling between *Month-Wise* (day-by-day), *Last Month*, *Year-Wise*, and *Trailing 12-Months*.
- **Automated Delta Math:** Computes net change and percentage deltas ($\Delta\% = \frac{\text{Current} - \text{Previous}}{\text{Previous}} \times 100$) for every KPI.
- **Platform Risk Radar:** Real-time visibility into blocked tasks, breached SLAs, and aging items.

### 5. Google Gemini AI Suite & Zero-Leakage RBAC
- **Multi-Model Failover:** High-availability pool (`gemini-flash-lite-latest` ➔ `gemini-3-flash-preview` ➔ `gemini-3.8-flash` ➔ `gemini-3.6-flash` ➔ `gemini-3.7-flash` ➔ `gemini-flash-latest`) with automatic retries on HTTP 429/503.
- **Deterministic Local Intelligence:** Offline-resilient synthesis engine calculating derived metrics (aging, velocity, bottlenecks, SLA tracking) without external API dependencies.
- **5-Layer Zero-Leakage Security Model:** Physical database context isolation prevents confidential director records from ever entering prompts sent by members.
- **Task Summary Generator:** 3-part Markdown briefings (Objective & Status, Blockers & Risks, Next Steps).
- **Personal Daily Digest:** Urgency-weighted morning briefing for individual team members.
- **Executive Team Summary:** High-level throughput, velocity, and blocker reports for Directors and Leads.
- **AI Trend Diagnostics:** Automated telemetry diagnosis on dashboard trends with natural language query support.
- **Agile Standup Generator:** Automatically generates "Accomplished", "Working on Today", and "Blockers" for daily standups.

---

## 👥 Seeded Demonstration Accounts Matrix

All accounts are pre-seeded via `php artisan db:seed`.  
**Global Password for All Accounts**: `password`

| Name | Email | System Role | Department / Team | Access Level | Primary Demo Scenario |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Muhammad Ali** | `muhammad.ali@stmu.edu.pk` | **Workspace Owner** | MIS Directorate | Full System Access | Multi-period analytics, Gemini executive briefings, system configuration. |
| **Khubaib Ur Rehman** | `khubaib@stmu.edu.pk` | **Admin / Team Lead** | Core MIS & Infrastructure | Workspace, Tasks, AI, Teams, Helpdesk | Team capacity load balancing, assigning tickets, triage queue management. |
| **Sarah Jenkins** | `sarah@example.com` | **Staff Member** | Core Engineering | Workspace, Tasks, Helpdesk | Resolving admission portal bugs, drafting resolution summaries. |
| **Hamza Tariq** | `hamza@stmu.edu.pk` | **Staff Member** | Full-Stack Engineering | Workspace, Tasks, Helpdesk | Working on biometric defect tasks, sprint delivery tracking. |
| **Khurram Ahmed** | `khurram@stmu.edu.pk` | **Staff Member** | MIS & Systems Operations | Workspace, Tasks, Helpdesk | Resolving server deadlocks, SLA monitoring, capacity tracking. |
| **Ubaid ur Rehman** | `ubaid@stmu.edu.pk` | **Staff Member** | Quality Assurance (QA) | Workspace, Tasks, Helpdesk | Logging admission form test checklists, reporting defects. |
| **Sara Khan** | `requester@example.com` | **Requester** | Faculty / Academic Client | **Tickets Portal Only** | Submitting issue tickets tagged to projects, tracking live SLA countdowns. |
| **Faisal Kamran** | `faisal.kamran@partner.stmu.edu.pk` | **Guest** | External ISO Auditor | View-Only Access | Auditing system logs and compliance trails. |

---

## 🚀 Quickstart & Local Installation

### 1. Prerequisites
- **PHP**: `8.2` or `8.3` (with `pdo_mysql`, `curl`, `mbstring`, `fileinfo`)
- **MySQL Server**: `8.0+`
- **Node.js**: `18.x` or `20.x LTS` & `npm`
- **Composer**: `2.5+`

### 2. Clone & Setup
```bash
# Clone the repository
git clone <repo-url> task-manager
cd task-manager

# Install PHP dependencies
composer install

# Install Frontend dependencies
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Configure your MySQL database and Google Gemini API credentials in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task-manager
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

# Google Gemini AI Integration
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-flash-lite-latest
GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1beta
```

### 4. Database Setup & Seeding
```bash
# Run migrations
php artisan migrate

# Seed rich multi-department university MIS scenario
php artisan db:seed

# Symlink public storage for attachments & avatars
php artisan storage:link
```

### 5. Build Assets & Start Development Server
```bash
# Terminal 1: Vite Hot Module Reloader
npm run dev

# Terminal 2: Laravel Server
php artisan serve
```
Visit `http://localhost:8000` in your browser.

---

## 🧪 Running the Test Suite

```bash
# Execute entire test suite
php artisan test

# Test AI service multi-model failover & zero-leakage role hierarchy
php artisan test --filter=GeminiServiceTest

# Test requester portal role isolation & navigation guards
php artisan test --filter=RequesterPortalTest

# Test interactive dashboard analytics & Chart.js delta math
php artisan test --filter=DashboardAnalyticsTest

# Test ticketing SLAs, project associations & capacity routing
php artisan test --filter=TicketingTest
```

---

## 📁 Key Architectural Files

- **AI Subsystem:**
  - AI Service & Failover Pool: [`app/Services/GeminiService.php`](file:///home/muhammad-ali/task-manager/app/Services/GeminiService.php)
  - Interactive AI Assistant: [`app/Livewire/Ai/AiAssistant.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Ai/AiAssistant.php)
- **Helpdesk & Ticketing Subsystem:**
  - Capacity & Load Balancer: [`app/Services/TicketCapacityService.php`](file:///home/muhammad-ali/task-manager/app/Services/TicketCapacityService.php)
  - Triage Queue: [`app/Livewire/Tickets/TicketQueue.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Tickets/TicketQueue.php)
  - Ticket Detail Modal: [`app/Livewire/Tickets/TicketDetail.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Tickets/TicketDetail.php)
  - Ticket Creation: [`app/Livewire/Tickets/RaiseTicket.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Tickets/RaiseTicket.php)
  - Requester & Personal Portal: [`app/Livewire/Tickets/MyTickets.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Tickets/MyTickets.php)
- **Analytics & Task Management:**
  - Interactive Analytics Hub: [`app/Livewire/Dashboard/Dashboard.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Dashboard/Dashboard.php)
  - Task Manager (Kanban, List, Calendar, Gantt): [`app/Livewire/Tasks/TaskManager.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Tasks/TaskManager.php)
  - Task Detail Drawer: [`app/Livewire/Tasks/TaskDetailModal.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Tasks/TaskDetailModal.php)
  - Personal Workload: [`app/Livewire/Tasks/MyTasks.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Tasks/MyTasks.php)
- **Security & Authorization:**
  - User Model & AI Hierarchy Tiers: [`app/Models/User.php`](file:///home/muhammad-ali/task-manager/app/Models/User.php)
