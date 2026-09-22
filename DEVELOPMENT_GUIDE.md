# 🛠️ STMU MIS Task Manager — Comprehensive Development Guide

This guide provides everything required to set up, develop, test, debug, and deploy the **STMU MIS Task & Service Desk Platform**.

---

## 1. Technology Stack & Prerequisites

| Layer | Technology | Version | Purpose |
| :--- | :--- | :--- | :--- |
| **Backend Framework** | Laravel | `12.x / 13.x` | Core application framework, Eloquent ORM, Routing, Auth |
| **Reactivity Layer** | Livewire | `v3 / v4` | Component-driven reactive UI without writing heavy Vue/React |
| **Component UI Library** | Flux UI (Livewire) | `v2.x` | Enterprise layout, navigation, inputs, drawers, dropdowns |
| **Styling & Design System** | Tailwind CSS | `v4.x` | Vanilla CSS tokens, modern glassmorphism, dark/light theme |
| **Interactive Graphs** | Chart.js | `v4.4+` | Playable velocity curves, project workload bars, category doughnuts |
| **Artificial Intelligence** | Google Gemini API | `REST API` | Executive diagnostics, daily standups, team summaries, natural language queries |
| **Database** | MySQL | `8.0+` | Relational storage, foreign keys, transaction safety |
| **Language Runtime** | PHP | `^8.2 / 8.3+` | Strict typing, typed properties, match expressions |
| **Frontend Tooling** | Vite | `v5.x / v6.x` | Asset bundling, hot module replacement (HMR) |
| **Task Queue & Mailer** | Redis / Database / Sync | N/A | Asynchronous email dispatch, SLA timers, notifications |

### System Prerequisites
Ensure the following tools are installed on your Linux / macOS / Windows (WSL2) system:
- **PHP**: `8.2` or `8.3` with extensions: `pdo_mysql`, `curl`, `mbstring`, `xml`, `bcmath`, `fileinfo`, `zip`.
- **Composer**: `v2.5+`
- **Node.js**: `v18.x` or `v20.x LTS` & `npm`
- **MySQL Server**: `8.0+` running on port `3306`

---

## 2. Step-by-Step Local Setup

### Step 1: Clone Repository & Install Dependencies
```bash
git clone <repository_url> task-manager
cd task-manager

# Install PHP packages
composer install

# Install Frontend packages
npm install
```

### Step 2: Environment Configuration
Copy the `.env.example` file:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your local MySQL database and Google Gemini API credentials in `.env`:
```env
APP_NAME="STMU MIS Task Manager"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task-manager
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

# Testing Database (used for php artisan test)
# Ensure database 'task-manager_testing' exists in MySQL:
# CREATE DATABASE `task-manager_testing`;

# Google Gemini AI Integration
GEMINI_API_KEY=your_gemini_api_key_here
GEMINI_MODEL=gemini-flash-lite-latest
GEMINI_BASE_URL=https://generativelanguage.googleapis.com/v1beta

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=sync
```

> [!TIP]
> **Getting a Free Gemini API Key**: Visit [Google AI Studio](https://aistudio.google.com/) and create an API key. The application includes a multi-model fallback pool (`gemini-flash-lite-latest`, `gemini-3-flash-preview`, `gemini-3.8-flash`, etc.) that automatically handles API rate limits.

### Step 3: Run Database Migrations & Seed Demo Data
```bash
# Create databases in MySQL if they do not exist
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS \`task-manager\`; CREATE DATABASE IF NOT EXISTS \`task-manager_testing\`;"

# Execute all migrations
php artisan migrate

# Seed rich multi-department university MIS demo data
php artisan db:seed
```

### Step 4: Storage Symlink
Symlink the public storage disk so ticket attachments and user avatars are accessible:
```bash
php artisan storage:link
```

### Step 5: Start Development Servers
Run the Laravel application and Vite hot-reloader:

**Terminal 1 (Backend Server):**
```bash
php artisan serve
# Application is live at: http://127.0.0.1:8000
```

**Terminal 2 (Vite Frontend Compiler):**
```bash
npm run dev
```

---

## 3. Architecture & Directory Blueprint

```
task-manager/
├── app/
│   ├── Livewire/                         # Full-page and slide-over reactive components
│   │   ├── AccessRequests/               # Public access request & Admin triage review
│   │   ├── Ai/                           # Conversational AI Assistant & database querying
│   │   ├── Dashboard/                    # Interactive Analytics Hub & Executive Overview
│   │   ├── Tasks/                        # Kanban Board, List View, Calendar, Gantt, Modals
│   │   ├── Tickets/                      # Triage Queue, Raise Ticket, My Tickets, Detail Drawer
│   │   └── Workspace/                    # Team Manager, Member capacity & roles
│   ├── Models/                           # Eloquent models with relations and policies
│   │   ├── AccessRequest.php             # Self-service workspace onboarding
│   │   ├── Project.php                   # Project belongsTo Space, hasMany Tasks & Tickets
│   │   ├── Space.php                     # Functional departments (Admissions, IT, Clinical)
│   │   ├── Task.php                      # Core delivery unit (status, assignees, checklists)
│   │   ├── Team.php                      # Cross-functional squads (MIS, DevOps, UI/UX)
│   │   ├── Ticket.php                    # Helpdesk service ticket (SLA countdown, priority)
│   │   ├── TicketActivityLog.php         # Immutable audit trail
│   │   ├── TicketCategory.php            # Issue taxonomy (Bug, Feature, IT, UI, Access)
│   │   ├── User.php                      # Multi-tenant RBAC (Owner, Admin, Member, Requester)
│   │   └── Workspace.php                 # Multi-tenant boundary
│   └── Services/
│       ├── GeminiService.php             # Google Gemini AI SDK with multi-model failover
│       └── TicketCapacityService.php     # Load balancer & least-loaded agent routing
├── database/
│   ├── migrations/                       # Incremental schema evolution
│   └── seeders/
│       └── DatabaseSeeder.php            # Comprehensive STMU MIS scenario & demo seeders
├── resources/
│   ├── css/app.css                       # Tailwind CSS v4 theme design tokens
│   └── views/
│       ├── layouts/app/                  # Base layout, sidebar & brand header
│       └── livewire/                     # Blade templates matching Livewire components
└── tests/
    └── Feature/                          # PHPUnit / Pest integration test suites
        ├── DashboardAnalyticsTest.php    # Interactive charts, delta math, time filtering
        ├── RequesterPortalTest.php       # Role isolation & route guards
        ├── TicketingTest.php             # SLA targets, routing & capacity balancing
        └── GeminiServiceTest.php         # Multi-model AI failover & fallback synthesis
```

---

## 4. Key Functional Subsystems

### A. Interactive Analytics & Cross-Period Comparison Engine
- **Component**: [`app/Livewire/Dashboard/Dashboard.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Dashboard/Dashboard.php)
- **View**: [`resources/views/livewire/dashboard/dashboard.blade.php`](file:///home/muhammad-ali/task-manager/resources/views/livewire/dashboard/dashboard.blade.php)
- **Granularities Supported**:
  - `month` (Day-by-day velocity curve for chosen Month & Year).
  - `last_month` (Comparison against previous month).
  - `year` (Month-by-month trajectory across the chosen calendar year).
  - `all` (Trailing 12-Month organizational throughput).
- **Delta Calculations**:
  - Automatically computes net change and percentage delta:
    $$\Delta\% = \frac{\text{Current} - \text{Previous}}{\text{Previous}} \times 100$$
- **Playable Chart.js Integration**:
  - **Velocity Curve**: Multi-dataset interactive line/bar chart displaying *Tickets Influx*, *Tickets Resolved*, *Tasks Created*, and *Tasks Completed*.
  - **Project Influx Matrix**: Side-by-side grouped bar chart illustrating task volume vs ticket volume per project.
  - **Category Breakdown**: Doughnut chart illustrating ticket issues (Bugs, Hardware, Access, Features).
- **Gemini Trend Diagnostics**:
  - Generates executive analysis and accepts free-form natural language inquiries (e.g., *"Why did tickets spike on Admission Portal?"*).

### B. Helpdesk Ticketing & SLA Engine
- **Component**: [`app/Livewire/Tickets/TicketQueue.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Tickets/TicketQueue.php)
- **SLA Commitments**:
  - **Urgent**: 4 Hours.
  - **High**: 24 Hours (1 Business Day).
  - **Normal**: 72 Hours (3 Days).
  - **Low**: 120 Hours (5 Days).
- **Capacity Management**:
  - [`TicketCapacityService.php`](file:///home/muhammad-ali/task-manager/app/Services/TicketCapacityService.php) calculates current ticket load vs maximum capacity per agent and suggests the least-loaded engineer.

### C. Requester Portal (Isolated Support View)
- **Component**: [`app/Livewire/Tickets/MyTickets.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Tickets/MyTickets.php) & [`RaiseTicket.php`](file:///home/muhammad-ali/task-manager/app/Livewire/Tickets/RaiseTicket.php)
- **Role Isolation**:
  - Requesters are strictly confined to `/workspace/{slug}/tickets/my` and `/raise`.
  - Attempts to access `/dashboard`, `/tasks`, `/ai`, or `/teams` automatically redirect to `/tickets/my`.
  - Internal features (Task Board, AI Assistant, Sprints, Member Lists) are removed from the DOM and navigation tree.

---

## 5. Testing & Quality Assurance

Run the test suite using PHPUnit:
```bash
# Run all feature tests
php artisan test

# Run interactive dashboard analytics tests
php artisan test --filter=DashboardAnalyticsTest

# Run requester portal isolation tests
php artisan test --filter=RequesterPortalTest

# Run ticketing & SLA tests
php artisan test --filter=TicketingTest

# Run AI assistant tests
php artisan test --filter=GeminiServiceTest
```

### Common Testing Gotchas & Tips:
1. **MySQL Metadata Locks**: If running tests in background terminals, ensure no dangling transactions remain open:
   ```bash
   mysql -u root -p -e "SHOW FULL PROCESSLIST;"
   ```
2. **Database Testing Isolation**: In `phpunit.xml`, `DB_DATABASE` is configured to `task-manager_testing`. Ensure this database exists before running tests.

---

## 6. Production Deployment Checklist

1. **Optimize Autoloader & Caches**:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
2. **Build Production Assets**:
   ```bash
   npm run build
   ```
3. **Queue Worker (Supervisor)**:
   Configure Supervisor to run `php artisan queue:work --sleep=3 --tries=3` for background notifications and email dispatches.
4. **Cron Scheduler**:
   Add the Laravel scheduler to `/etc/crontab`:
   ```crontab
   * * * * * cd /path-to-task-manager && php artisan schedule:run >> /dev/null 2>&1
   ```
