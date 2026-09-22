# 👥 STMU MIS Task Manager — Personas, Use Cases & Sample Accounts Matrix

This document provides a comprehensive catalog of all user personas, operational use cases, workflows, and seeded demonstration accounts within the **STMU MIS Task & Service Desk Platform**.

---

## 1. Complete Seeded Demonstration Accounts Matrix

All accounts are pre-seeded in the database via `php artisan db:seed`.
**Global Default Password for All Seeded Accounts**: `password`

| Name | Email | System Role | Department / Team | Permitted Sections | Primary Demo Scenarios |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Alex Rivera** *(Legacy Demo)* | `alex@example.com` | **Workspace Owner** | Executive Management | Full System Access | Complete platform oversight, workspace settings, role assignments. |
| **Muhammad Ali** | `muhammad.ali@stmu.edu.pk` | **Workspace Owner** | MIS Directorate | Full System Access | Multi-period analytics, project creation, Gemini AI executive briefing. |
| **Khubaib Ur Rehman** | `khubaib@stmu.edu.pk` | **Admin / Team Lead** | Core MIS & Infrastructure | Workspace, Tasks, AI, Teams, Helpdesk | Team capacity load balancing, assigning tickets to engineers, approving access requests. |
| **Sarah Jenkins** | `sarah@example.com` | **Staff Member** | Core Engineering | Workspace, Tasks, Helpdesk | Working on admission portal tasks, resolving tickets with resolution summaries. |
| **Hamza Tariq** | `hamza@stmu.edu.pk` | **Staff Member** | Full-Stack Engineering | Workspace, Tasks, Helpdesk | Raising urgent biometric defect tickets, sprint velocity tracking. |
| **Khurram Ahmed** | `khurram@stmu.edu.pk` | **Staff Member** | MIS & Systems Operations | Workspace, Tasks, Helpdesk | Resolving high-priority VPN and database deadlocks, updating ticket statuses. |
| **Ubaid ur Rehman** | `ubaid@stmu.edu.pk` | **Staff Member** | Quality Assurance (QA) | Workspace, Tasks, Helpdesk | Conducting stress tests, reporting admission form bugs, logging test checklists. |
| **Elena Rostova** | `elena@example.com` | **Staff Member** | UI/UX Product Design | Workspace, Tasks, Helpdesk | Designing mobile interfaces, resolving dark mode contrast issues. |
| **Ali Altaf** | `ali.altaf@stmu.edu.pk` | **Staff Member** | Product Design & Media | Workspace, Tasks, Helpdesk | Reporting mobile drawer bugs, tracking UI improvements. |
| **Sara Khan** | `requester@example.com` | **Requester (Ticket-Only)** | Faculty / Academic Department | **My Tickets & Raise Ticket Only** | Submitting issue tickets tagged to projects, tracking live SLA countdowns. |
| **Faisal Kamran** | `faisal.kamran@partner.stmu.edu.pk` | **Guest** | External ISO Auditor | View-Only Access | Inspecting security audit trails and ISO 27001 compliance logs. |

---

## 2. User Personas & Detailed Use Cases

---

### 🏛️ Persona 1: Executive Director & Workspace Owner
- **Accounts**: `muhammad.ali@stmu.edu.pk` or `alex@example.com`
- **Role**: `owner`
- **Objectives**: Real-time cross-departmental oversight, velocity trends, SLA compliance rates, and strategic resource allocation.

#### Key Use Case Workflows:
1. **Interactive Multi-Period Trend Analysis**:
   - Access `/dashboard`.
   - Toggle between **Month-Wise**, **Last Month**, **Year-Wise**, and **12 Months** to inspect velocity trends.
   - Use the **Year** and **Month** selectors to compare performance (e.g. September 2026 vs August 2026).
   - Switch between **Line Curve** and **Bar Columns** to visualize task throughput against incoming defect rates.
2. **Project Influx & Workload Matrix Evaluation**:
   - Review the **Project Workload vs Ticket Influx Comparison** chart.
   - Identify if the **Student Admission Portal** has a disproportionate ticket surge relative to available engineering velocity.
   - Filter by a specific project using the **Project Scope** dropdown to drill down into localized trends.
3. **AI Trend Diagnostics & Strategic Forecasting**:
   - Click **"Diagnose Trends with AI"** to generate an automated executive telemetry analysis using Google Gemini.
   - Ask custom inquiries in the prompt bar: *"Which project is at the highest risk of missing SLA targets this month?"*
   - Review AI-generated strategic recommendations for leadership.
4. **Platform Risk Radar**:
   - Check the **Platform Risk Radar** KPI card showing total blocked tasks, breached SLAs, and overdue items.

---

### 📋 Persona 2: Project Manager & Scrum Master
- **Accounts**: `khubaib@stmu.edu.pk`
- **Role**: `admin`
- **Objectives**: Sprint planning, backlog prioritization, Kanban execution, and team capacity balancing.

#### Key Use Case Workflows:
1. **Multi-View Sprint Management**:
   - Navigate to **Task Board** (`/workspace/{slug}/tasks`).
   - Switch between **Kanban Board**, **List View**, **Calendar Grid**, and **Gantt Timeline**.
   - Drag and drop task cards across status columns (`To Do`, `In Progress`, `In Review`, `Done`).
2. **Task Detail Deep-Dive**:
   - Click any task card to open the slide-over drawer.
   - Track progress bars computed automatically from interactive checklist items.
   - Log work duration using the built-in live time tracking stopwatch or manual time entry.
   - Review audit history in the **Activity Stream** tab.
3. **Daily Agile Standup Synthesis**:
   - Click **"My Standup"** on the dashboard to have Google Gemini synthesize a 3-part daily update (*Accomplished*, *Working on Today*, *Blockers*).

---

### 💻 Persona 3: Software Engineer & Technical Lead
- **Accounts**: `sarah@example.com` or `hamza@stmu.edu.pk`
- **Role**: `member`
- **Objectives**: Delivering code, resolving bugs, collaborating with QA, and maintaining SLA targets.

#### Key Use Case Workflows:
1. **Focused Personal Delivery ("My Tasks")**:
   - Navigate to `/my-tasks` to access an urgency-sorted view: *Overdue*, *Due Today*, *Upcoming*, and *Completed*.
2. **Working with Tickets & Linking to Projects**:
   - Open ticket `TCK-1001` or `TCK-1004`.
   - Update priority or reassign to appropriate projects directly in the slide-over metadata properties.
   - Post internal notes (🔒 Staff Only) hidden from customers, or public replies visible to requesters.
   - Mark ticket as **Resolved** and provide a mandatory **Resolution Summary** explaining technical remediation.

---

### 🎧 Persona 4: Helpdesk Lead & Support Specialist
- **Accounts**: `khurram@stmu.edu.pk`
- **Role**: `member` (MIS Lead)
- **Objectives**: Ticket triage, SLA compliance, capacity balancing, and customer issue resolution.

#### Key Use Case Workflows:
1. **Triage Queue & SLA Countdown**:
   - Access **Triage Queue** (`/workspace/{slug}/tickets/queue`).
   - Filter tickets by status (*Open*, *Assigned*, *In Progress*, *Resolved*, *Closed*), priority, or project.
   - Monitor real-time SLA due targets with live countdowns and overdue alert pulses.
2. **Intelligent Load Balancing & Capacity Management**:
   - Access **Team Capacity** (`/workspace/{slug}/tickets/capacity`).
   - Review each engineer's current active ticket load against their predefined capacity limit.
   - When assigning a ticket in the detail drawer, observe the automated **Best Candidate Suggestion** based on least active load.

---

### 🎓 Persona 5: Requester (Faculty, Student & External Client)
- **Accounts**: `requester@example.com`
- **Role**: `requester` (Ticket-Only)
- **Objectives**: Submitting bug reports or service inquiries, tracking resolution progress, and reopening tickets if dissatisfied.

#### Key Use Case Workflows:
1. **Restricted Ticket Portal Access**:
   - Log in as `requester@example.com` / `password`.
   - Notice immediate landing on `/workspace/{slug}/tickets/my`.
   - Notice that internal delivery areas (**Dashboard**, **Task Board**, **AI Assistant**, **Teams**, **Spaces**) are completely absent from the navigation bar.
2. **Raising a Ticket with Project Tagging**:
   - Click **"Raise a Support Ticket"**.
   - Select the related project (e.g. *Student Admission Portal* or *LMS Mobile App*).
   - Choose issue category (*Bug Report*, *Feature Request*, *IT & Infrastructure*, *General Support*).
   - Select priority to view associated SLA resolution commitments (*Urgent = 4h*, *High = 24h*, *Normal = 3d*, *Low = 5d*).
   - Attach files and submit. An automated `TCK-XXXX` ticket number is assigned immediately.
3. **Tracking & Reopening**:
   - Open any submitted ticket in the slide-over drawer to read status updates and staff replies.
   - If a ticket was marked as **Resolved**, the requester can click **"Reopen Ticket"** within policy parameters if the issue recurs.

---

### 🔒 Persona 6: Prospective User & System Access Requester
- **Scenario**: Unauthenticated or external user requesting organizational access.
- **Objectives**: Self-service workspace onboarding with administrative approval.

#### Key Use Case Workflows:
1. **Public Request Submission**:
   - Visit `/access-request`.
   - Submit formal request with institutional email, full name, department, and business justification.
2. **Administrative Review & Provisioning**:
   - Admin (`khubaib@stmu.edu.pk` or `muhammad.ali@stmu.edu.pk`) accesses **Access Requests** (`/workspace/{slug}/access-requests`).
   - Review pending submissions (e.g., *Dr. Ayesha Tariq*, *Zainab Malik*).
   - Click **Approve** to automatically provision a user account with chosen role (*Member*, *Admin*, *Requester*), or click **Reject** with a recorded rejection reason.

---

## 3. End-to-End Demonstration Walkthrough Scripts

### Scenario A: Testing the Interactive Analytics Hub (Director View)
1. Log in as `muhammad.ali@stmu.edu.pk` (`password`).
2. On `/dashboard`, locate the **Interactive Influx & Workload Analytics** toolbar.
3. Click **"Last Month"** &rarr; Notice all KPI cards, deltas, and Chart.js graphs instantly recalculate to August 2026.
4. Click **"Month-Wise"** &rarr; Notice numbers compare September 2026 with August 2026 with (+/- %) badges.
5. In the **Project Scope** dropdown, choose **"Student Admission Portal"** &rarr; Notice metrics isolate specifically to the admissions portal.
6. Click the **Bar Columns** toggle &rarr; Notice the Velocity trend smoothly transitions into a grouped columnar view.
7. Click **"Diagnose Trends with AI"** &rarr; Notice Google Gemini generates a structured multi-point diagnostic report.

### Scenario B: Raising a Ticket as a Faculty Requester
1. Log in as `requester@example.com` (`password`).
2. Verify that only **Support Tickets** (**My Tickets** and **Raise Ticket**) is visible in the sidebar.
3. Click **"Raise Ticket"**.
4. Set:
   - **Related Project**: *Student Admission Portal*
   - **Category**: *Bug Report*
   - **Priority**: *Urgent (4 hrs)*
   - **Subject**: *Payment gateway timeout on fee submission*
5. Click **"Submit Support Ticket"** &rarr; Observe success screen with newly generated ticket number.
6. Click **"View in My Tickets"** &rarr; Click the new ticket to inspect the live SLA target countdown.

### Scenario C: Triage, Reassignment & Resolution (Helpdesk Specialist View)
1. Log in as `khurram@stmu.edu.pk` (`password`).
2. Navigate to **Triage Queue** (`/workspace/stmu-mis/tickets/queue`).
3. Click the newly raised ticket from Scenario B.
4. In the drawer metadata row:
   - Notice the **Related Project** badge shows *Student Admission Portal*.
   - Reassign the ticket to **Core Engineering** &rarr; Notice assignee suggestions list engineers by least load.
   - Change status to **In Progress**.
5. Change status to **Resolved** &rarr; A modal prompts for **Resolution Summary**.
6. Type: *"Refactored payment gateway callback timeout threshold and cleared redis cache."* and click **Confirm Resolution**.
7. Log back in as `requester@example.com` &rarr; Open the ticket &rarr; Verify the resolution summary is displayed in an emerald banner, and the **Reopen Ticket** button is available.
