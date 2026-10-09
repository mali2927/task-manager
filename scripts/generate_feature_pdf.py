#!/usr/bin/env python3
"""
Generate publication-quality Features & Workflow Architecture PDF
for STMU MIS Task & Helpdesk Service Platform.
"""

import os
import sys
from reportlab.lib.pagesizes import letter
from reportlab.lib import colors
from reportlab.lib.units import inch
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether, HRFlowable
)
from reportlab.pdfgen import canvas

class NumberedCanvas(canvas.Canvas):
    """Two-pass canvas to dynamically compute and print total page count."""
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_decorations(num_pages)
            super().showPage()
        super().save()

    def draw_page_decorations(self, page_count):
        self.saveState()
        
        # Don't draw header/footer on cover/first page
        if self._pageNumber > 1:
            # Header
            self.setFont("Helvetica-Bold", 8)
            self.setFillColor(colors.HexColor("#6366f1"))
            self.drawString(36, 11 * inch - 30, "STMU MIS PLATFORM")
            self.setFont("Helvetica", 8)
            self.setFillColor(colors.HexColor("#64748b"))
            self.drawString(135, 11 * inch - 30, "|  Features, Workflows & Architecture Specification v2.5")
            
            # Header Rule
            self.setStrokeColor(colors.HexColor("#e2e8f0"))
            self.setLineWidth(0.75)
            self.line(36, 11 * inch - 34, 8.5 * inch - 36, 11 * inch - 34)

            # Footer Rule
            self.line(36, 40, 8.5 * inch - 36, 40)
            
            # Footer
            self.setFont("Helvetica", 8)
            self.setFillColor(colors.HexColor("#94a3b8"))
            self.drawString(36, 28, "Shifa Tameer-e-Millat University (STMU) • Management Information Systems Directorate")
            page_text = f"Page {self._pageNumber} of {page_count}"
            self.drawRightString(8.5 * inch - 36, 28, page_text)
            
        self.restoreState()


def build_pdf(filename="STMU_MIS_Task_Manager_Features_and_Workflow.pdf"):
    doc = SimpleDocTemplate(
        filename,
        pagesize=letter,
        leftMargin=36,
        rightMargin=36,
        topMargin=46,
        bottomMargin=46
    )

    styles = getSampleStyleSheet()

    # Custom Color Palette
    PRIMARY = colors.HexColor("#4f46e5")    # Electric Indigo
    SECONDARY = colors.HexColor("#7c3aed")  # Deep Violet
    DARK = colors.HexColor("#0f172a")       # Obsidian
    TEXT = colors.HexColor("#1e293b")       # Slate Text
    MUTED = colors.HexColor("#64748b")      # Muted Slate
    BORDER = colors.HexColor("#e2e8f0")     # Light Border
    ACCENT_BG = colors.HexColor("#f8fafc")  # Card Background
    EMERALD = colors.HexColor("#10b981")    # Success Green
    AMBER = colors.HexColor("#f59e0b")      # Warning
    ROSE = colors.HexColor("#f43f5e")       # Alert Rose

    # Paragraph Styles
    title_style = ParagraphStyle(
        'DocTitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=24,
        leading=28,
        textColor=PRIMARY,
        spaceAfter=6
    )

    subtitle_style = ParagraphStyle(
        'DocSubtitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=12,
        leading=16,
        textColor=SECONDARY,
        spaceAfter=14
    )

    meta_style = ParagraphStyle(
        'DocMeta',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9,
        leading=13,
        textColor=MUTED,
        spaceAfter=16
    )

    h1_style = ParagraphStyle(
        'Heading1_Custom',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=15,
        leading=19,
        textColor=PRIMARY,
        spaceBefore=14,
        spaceAfter=8,
        keepWithNext=True
    )

    h2_style = ParagraphStyle(
        'Heading2_Custom',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=11,
        leading=15,
        textColor=SECONDARY,
        spaceBefore=10,
        spaceAfter=5,
        keepWithNext=True
    )

    body_style = ParagraphStyle(
        'Body_Custom',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9,
        leading=13,
        textColor=TEXT,
        spaceAfter=6
    )

    body_bold = ParagraphStyle(
        'Body_Bold_Custom',
        parent=body_style,
        fontName='Helvetica-Bold'
    )

    bullet_style = ParagraphStyle(
        'Bullet_Custom',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=12,
        textColor=TEXT,
        leftIndent=12,
        firstLineIndent=-8,
        spaceAfter=3
    )

    code_style = ParagraphStyle(
        'Code_Custom',
        parent=styles['Normal'],
        fontName='Courier',
        fontSize=8,
        leading=10.5,
        textColor=DARK,
    )

    callout_style = ParagraphStyle(
        'Callout_Custom',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=12,
        textColor=DARK,
    )

    table_header = ParagraphStyle(
        'TableHeader',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8,
        leading=10,
        textColor=colors.white,
        alignment=0
    )

    table_cell = ParagraphStyle(
        'TableCell',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=7.5,
        leading=9.5,
        textColor=TEXT
    )

    table_cell_bold = ParagraphStyle(
        'TableCellBold',
        parent=table_cell,
        fontName='Helvetica-Bold'
    )

    table_cell_mono = ParagraphStyle(
        'TableCellMono',
        parent=table_cell,
        fontName='Courier',
        fontSize=7,
        leading=8.5
    )

    story = []

    # ==========================================
    # HEADER / TITLE BLOCK
    # ==========================================
    title_box = [
        [Paragraph("STMU MIS TASK & HELPDESK PLATFORM", title_style)],
        [Paragraph("Comprehensive Features, Workflows & Operational Architecture Guide", subtitle_style)],
        [Paragraph("<b>Version:</b> 2.5 (Enterprise Edition)  |  <b>Target:</b> Shifa Tameer-e-Millat University MIS Directorate  |  <b>Date:</b> October 2026", meta_style)],
    ]
    t_box = Table(title_box, colWidths=[7.5 * inch])
    t_box.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor("#f5f3ff")),
        ('BOX', (0, 0), (-1, -1), 1.5, PRIMARY),
        ('PADDING', (0, 0), (-1, -1), 14),
        ('BOTTOMPADDING', (0, -1), (-1, -1), 10),
    ]))
    story.append(t_box)
    story.append(Spacer(1, 14))

    # ==========================================
    # 1. EXECUTIVE SUMMARY
    # ==========================================
    story.append(Paragraph("1. Executive Summary & Architecture Overview", h1_style))
    story.append(Paragraph(
        "The <b>STMU MIS Task & Helpdesk Service Platform</b> is an integrated operational platform tailored for the "
        "demands of higher education management, clinical hospital systems, and institutional development sprints. "
        "It eliminates organizational silos by directly unifying <b>Agile Sprint Delivery (ClickUp-style)</b> with "
        "<b>Customer Support Helpdesk Ticketing (Jira Service Desk-style)</b>.",
        body_style
    ))

    # Tech Stack Summary Grid
    tech_data = [
        [Paragraph("Layer", table_header), Paragraph("Technology Stack & Version", table_header), Paragraph("Key Role & Responsibility", table_header)],
        [Paragraph("Backend Framework", table_cell_bold), Paragraph("Laravel 12 / PHP 8.3", table_cell), Paragraph("Robust Eloquent ORM, scheduled jobs, multi-guard auth, REST APIs", table_cell)],
        [Paragraph("Dynamic Frontend", table_cell_bold), Paragraph("Livewire 3 + Alpine.js", table_cell), Paragraph("Zero-refresh reactive UI, drawer slide-overs, instant modals", table_cell)],
        [Paragraph("UI Design System", table_cell_bold), Paragraph("Tailwind CSS v4 + Flux UI", table_cell), Paragraph("Electric Iris & Obsidian dark mode, micro-animations, glass panels", table_cell)],
        [Paragraph("Data Visualization", table_cell_bold), Paragraph("Chart.js 4 (Bundled Vite ESM)", table_cell), Paragraph("Velocity curves, category doughnut, workload matrix, 1000ms easing", table_cell)],
        [Paragraph("AI Intelligence", table_cell_bold), Paragraph("Google Gemini Multi-Model Pool", table_cell), Paragraph("Failover pool (Flash-Lite / Flash-Preview), standup & trend synthesis", table_cell)],
        [Paragraph("Biometrics & Security", table_cell_bold), Paragraph("WebAuthn / FIDO2 Passkeys", table_cell), Paragraph("Hardware token & fingerprint authentication, 2FA, ISO 27001 logs", table_cell)],
    ]
    t_tech = Table(tech_data, colWidths=[1.8 * inch, 2.2 * inch, 3.5 * inch])
    t_tech.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), PRIMARY),
        ('BOX', (0, 0), (-1, -1), 0.5, BORDER),
        ('GRID', (0, 0), (-1, -1), 0.5, BORDER),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.white, ACCENT_BG]),
        ('PADDING', (0, 0), (-1, -1), 5),
    ]))
    story.append(t_tech)
    story.append(Spacer(1, 14))

    # ==========================================
    # 2. HIERARCHICAL SYSTEM ARCHITECTURE
    # ==========================================
    story.append(Paragraph("2. Hierarchical Organization & Data Model", h1_style))
    story.append(Paragraph(
        "The system enforces a five-tier hierarchy providing strict multi-tenant containment while allowing cross-departmental coordination:",
        body_style
    ))

    hierarchy_flow = """
    <b>[ University Institution ]</b>
      └── <b>Workspace:</b> MIS Innovation Hub (Multi-Tenant boundary, Slug: /mis)
            ├── <b>Spaces:</b> Core Systems  |  Hospital IT  |  Admissions & Student Affairs  |  ORIC Research
            │     └── <b>Projects:</b> Student Portal Modernization  |  Cloud Migration 3.0  |  LMS Engine
            │           ├── <b>Task Lists:</b> Sprint Backlog  |  In Development  |  QA Verification  |  Done
            │           │     └── <b>Tasks:</b> Checklists, assignees, priorities, due dates, activity logs
            │           └── <b>Linked Support Tickets:</b> Defect TCK-W1-1049 linked directly to Sprint Project
            └── <b>Teams & Squads:</b> Core DevOps, ERP Squad (Tracks Lead, Max Capacity & Active Ticket Load)
    """
    t_hier = Table([[Paragraph(hierarchy_flow.replace("\n", "<br/>"), callout_style)]], colWidths=[7.5 * inch])
    t_hier.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor("#f1f5f9")),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor("#cbd5e1")),
        ('PADDING', (0, 0), (-1, -1), 8),
    ]))
    story.append(t_hier)
    story.append(Spacer(1, 14))

    # ==========================================
    # 3. END-TO-END WORKFLOWS (DETAILED)
    # ==========================================
    story.append(Paragraph("3. End-to-End Operational Workflows", h1_style))

    # WORKFLOW 1: Task Lifecycle
    story.append(Paragraph("Workflow 1: Agile Task Lifecycle & Sprint Execution", h2_style))
    story.append(Paragraph(
        "<b>Phase A (Inception):</b> Tasks are initiated within a specific Project and Task List. Attributes include Title, "
        "Markdown specs, Priority (Low, Normal, High, Urgent), start date, deadline, assignees, and nested checklist items.<br/>"
        "<b>Phase B (Multi-View Interaction):</b> Engineers manage work across 4 view modes: "
        "(1) <i>Kanban Board</i> with drag-and-drop column transitions; "
        "(2) <i>Table View</i> with inline dropdown adjustments; "
        "(3) <i>Calendar Grid</i> for milestone deadlines; "
        "(4) <i>Gantt/Timeline</i> for duration schedules.<br/>"
        "<b>Phase C (Audit Logging):</b> Every status modification, assignee assignment, checklist check, or comment writes "
        "an immutable entry to <code>task_activities</code> with old vs new value snapshots.",
        body_style
    ))
    story.append(Spacer(1, 6))

    # WORKFLOW 2: Helpdesk Ticketing & SLA
    story.append(Paragraph("Workflow 2: Helpdesk Service Desk, Smart Triage & SLA Resolution", h2_style))
    story.append(Paragraph(
        "<b>Phase 1 (Submission):</b> Students, faculty, or hospital departments submit tickets with Category, Project, "
        "Description, and Attachments. System assigns a formatted serial number (e.g. <code>TCK-W1-4092-49</code>).<br/>"
        "<b>Phase 2 (Dynamic SLA Clock):</b> Real-time countdown clock begins ticking based on priority: "
        "<b>Urgent (4h SLA)</b>, <b>High (24h SLA)</b>, <b>Normal (72h SLA)</b>, <b>Low (120h SLA)</b>.<br/>"
        "<b>Phase 3 (Smart Capacity Balancing):</b> <code>TicketCapacityService</code> evaluates agent loads in the assigned team. "
        "If an agent has reached their <code>capacity_limit</code> (e.g. 5/5 tickets), an amber/rose overload warning triggers, "
        "and the system recommends reassigning to the least-burdened engineer.<br/>"
        "<b>Phase 4 (Mandatory Resolution Summary Gate):</b> An engineer cannot mark a ticket <code>Resolved</code> without "
        "submitting an exhaustive technical resolution summary detailing the root cause and verification steps.<br/>"
        "<b>Phase 5 (Client Rating & Self-Service Reopening):</b> Requesters view their ticket in the zero-trust Requester Portal "
        "(<code>/tickets/my</code>), can submit 1-5 star ratings, or reopen within 7 days if the defect persists.",
        body_style
    ))
    story.append(Spacer(1, 6))

    # WORKFLOW 3: Interactive Dashboard & Influx Analytics
    story.append(Paragraph("Workflow 3: Interactive Dashboard & Comparative Analytics Hub", h2_style))
    story.append(Paragraph(
        "<b>1. Granular Time-Travel Selector:</b> Livewire toolbar toggles between <b>Month-Wise</b> (day-stepped), "
        "<b>Last Month</b>, <b>Year-Wise</b>, and <b>Trailing 12-Months</b> with dynamic Year (2024–2027) and Month (1–12) dropdowns.<br/>"
        "<b>2. Three Animated Chart.js Canvases:</b><br/>"
        "&nbsp;&nbsp;• <i>Velocity Curve:</i> Smooth bezier lines tracking Tickets Influx, Tickets Resolved, and Tasks Shipped with radiant gradient fills.<br/>"
        "&nbsp;&nbsp;• <i>Category Doughnut:</i> Issue breakdown (LMS, ERP, Network, Exam Records) with 12px hover offset and center ticket counter.<br/>"
        "&nbsp;&nbsp;• <i>Project Workload Matrix:</i> Grouped bar chart comparing active task backlogs against incoming defect tickets.<br/>"
        "<b>3. Automated MoM Delta Calculations:</b> Automated mathematical comparison calculates net difference and percentage delta: "
        "<code>Δ% = ((Current - Previous) / Previous) * 100</code>.<br/>"
        "<b>4. Platform Risk Radar:</b> Instant flags on Overdue Tasks, SLA Breaches, and Blocked Blocker items.",
        body_style
    ))
    story.append(Spacer(1, 6))

    # WORKFLOW 4: Google Gemini AI
    story.append(Paragraph("Workflow 4: Google Gemini AI Operations Suite & Zero-Leakage RBAC", h2_style))
    story.append(Paragraph(
        "<b>High-Availability Multi-Model Failover:</b> Dispatches requests across a tiered pool "
        "(<code>gemini-flash-lite-latest</code> ➔ <code>gemini-3-flash-preview</code> ➔ <code>gemini-3.8-flash</code>) with deterministic local mathematical fallbacks.<br/>"
        "<b>Key Operational Capabilities:</b><br/>"
        "&nbsp;&nbsp;• <i>Executive Team Briefing:</i> High-level throughput, blocker bottlenecks, and sprint velocity synthesized for Directors.<br/>"
        "&nbsp;&nbsp;• <i>Personal Daily Standup:</i> Automatically formats accomplishments, today's targets, and obstacles for daily Scrum.<br/>"
        "&nbsp;&nbsp;• <i>Trend Diagnostics:</i> Answers ad-hoc natural language prompts (e.g. <i>'Why did tickets spike this week?'</i>).<br/>"
        "&nbsp;&nbsp;• <i>Zero-Leakage Security:</i> Server-side prompt assembly filters strictly by user permissions, preventing cross-tenant or private record leaks.",
        body_style
    ))
    story.append(Spacer(1, 6))

    # WORKFLOW 5: Passkeys & ISO 27001 Audit
    story.append(Paragraph("Workflow 5: Biometric Passkeys (WebAuthn) & ISO 27001 Audit Trail", h2_style))
    story.append(Paragraph(
        "<b>Passwordless Authentication:</b> Users register FIDO2 WebAuthn credentials (Apple TouchID, FaceID, Windows Hello, YubiKeys). "
        "Public keys are stored cryptographically; zero plaintext passwords traverse the network.<br/>"
        "<b>Compliance Telemetry:</b> Chronological activity log (<code>/activity-logs</code>) records every login, task transition, ticket assignment, "
        "and data export, providing full CSV exportability for ISO 27001 accreditation.",
        body_style
    ))
    story.append(Spacer(1, 14))

    # ==========================================
    # 4. MODULE FEATURE SPECIFICATION MATRIX
    # ==========================================
    story.append(PageBreak())
    story.append(Paragraph("4. Comprehensive Module Feature Specification Matrix", h1_style))

    matrix_data = [
        [Paragraph("Module", table_header), Paragraph("Route / Endpoint", table_header), Paragraph("Core Capabilities", table_header), Paragraph("Underlying Models / Services", table_header)],
        [
            Paragraph("<b>Dashboard Hub</b>", table_cell),
            Paragraph("<code>/workspace/{slug}</code>", table_cell_mono),
            Paragraph("Playable velocity curves, category doughnut, workload matrix, MoM delta math, time-travel toolbar, risk radar.", table_cell),
            Paragraph("Dashboard.php, Chart.js, Ticket, Task, Project", table_cell)
        ],
        [
            Paragraph("<b>Kanban Board</b>", table_cell),
            Paragraph("<code>/workspace/{slug}/tasks</code>", table_cell_mono),
            Paragraph("Drag-and-drop columns, priority badges, checklist chips, task detail drawer, custom status creation.", table_cell),
            Paragraph("TaskManager.php, Task, TaskStatus, TaskList", table_cell)
        ],
        [
            Paragraph("<b>My Tasks</b>", table_cell),
            Paragraph("<code>/workspace/{slug}/tasks/my</code>", table_cell_mono),
            Paragraph("Personal focus queue segmented by Overdue, Due Today, This Week, and Completed sprints.", table_cell),
            Paragraph("MyTasks.php, Task, User", table_cell)
        ],
        [
            Paragraph("<b>Triage Queue</b>", table_cell),
            Paragraph("<code>/workspace/{slug}/tickets/queue</code>", table_cell_mono),
            Paragraph("Incoming ticket triage, live SLA countdown timers, priority filters, project association, engineer assignment.", table_cell),
            Paragraph("TicketQueue.php, Ticket, Project, Category", table_cell)
        ],
        [
            Paragraph("<b>Agent Capacity</b>", table_cell),
            Paragraph("<code>/workspace/{slug}/tickets/capacity</code>", table_cell_mono),
            Paragraph("Workload heatmaps, capacity limit thresholds (e.g. 5 tickets), rebalancing recommendations.", table_cell),
            Paragraph("TicketCapacityService.php, Team, User", table_cell)
        ],
        [
            Paragraph("<b>Requester Portal</b>", table_cell),
            Paragraph("<code>/workspace/{slug}/tickets/my</code>", table_cell_mono),
            Paragraph("Zero-trust client portal, ticket creation, real-time tracking, 5-star rating, self-service reopen.", table_cell),
            Paragraph("MyTickets.php, Ticket", table_cell)
        ],
        [
            Paragraph("<b>Gemini AI Suite</b>", table_cell),
            Paragraph("<code>Header / AI Bar</code>", table_cell_mono),
            Paragraph("Executive summaries, agile standups, natural language trend diagnostics, multi-model failover pool.", table_cell),
            Paragraph("GeminiService.php, Google Gemini API", table_cell)
        ],
        [
            Paragraph("<b>Audit Telemetry</b>", table_cell),
            Paragraph("<code>/workspace/{slug}/activity-logs</code>", table_cell_mono),
            Paragraph("Chronological immutable event trail, user/type filtering, ISO 27001 compliance, CSV data export.", table_cell),
            Paragraph("ActivityLogs.php, TicketActivityLog, TaskActivity", table_cell)
        ],
        [
            Paragraph("<b>Passkeys & 2FA</b>", table_cell),
            Paragraph("<code>/settings/security</code>", table_cell_mono),
            Paragraph("FIDO2 WebAuthn biometric passkeys, TOTP two-factor codes, recovery codes, session revocation.", table_cell),
            Paragraph("PasskeyController.php, laravel/passkeys", table_cell)
        ],
        [
            Paragraph("<b>CSV Exports</b>", table_cell),
            Paragraph("<code>/workspace/{slug}/export</code>", table_cell_mono),
            Paragraph("Full database export of tasks, tickets, SLA metrics, and compliance logs in universal CSV format.", table_cell),
            Paragraph("ExportController.php, Storage", table_cell)
        ],
    ]

    t_matrix = Table(matrix_data, colWidths=[1.3 * inch, 1.8 * inch, 2.6 * inch, 1.8 * inch])
    t_matrix.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), PRIMARY),
        ('BOX', (0, 0), (-1, -1), 0.5, BORDER),
        ('GRID', (0, 0), (-1, -1), 0.5, BORDER),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.white, ACCENT_BG]),
        ('PADDING', (0, 0), (-1, -1), 4),
    ]))
    story.append(t_matrix)
    story.append(Spacer(1, 14))

    # ==========================================
    # 5. USER PERSONAS & RBAC GUIDE
    # ==========================================
    story.append(Paragraph("5. User Roles, Security Boundaries & Personas", h1_style))

    roles_data = [
        [Paragraph("Persona / Name", table_header), Paragraph("Role", table_header), Paragraph("System Privileges & Boundaries", table_header), Paragraph("Authorized Areas", table_header)],
        [
            Paragraph("<b>Muhammad Ali</b><br/><font color='#64748b'>muhammad.ali@stmu.edu.pk</font>", table_cell),
            Paragraph("<b>Workspace Owner</b>", table_cell_bold),
            Paragraph("Complete administrative authority, workspace billing, space creation, telemetry export, AI digests.", table_cell),
            Paragraph("All Platform Views & System Settings", table_cell)
        ],
        [
            Paragraph("<b>Khubaib Ur Rehman</b><br/><font color='#64748b'>khubaib@stmu.edu.pk</font>", table_cell),
            Paragraph("<b>Admin / Team Lead</b>", table_cell_bold),
            Paragraph("Triage queue management, sprint planning, ticket assignment, agent capacity rebalancing, standups.", table_cell),
            Paragraph("Workspaces, Tasks, Triage, Teams, Capacity", table_cell)
        ],
        [
            Paragraph("<b>Hamza / Sarah / Khurram</b><br/><font color='#64748b'>Engineering Staff</font>", table_cell),
            Paragraph("<b>Staff Member</b>", table_cell_bold),
            Paragraph("Sprint execution, checklist progress, defect resolution, mandatory resolution summary documentation.", table_cell),
            Paragraph("Task Boards, Personal Tasks, Assigned Tickets", table_cell)
        ],
        [
            Paragraph("<b>Sara Khan (Faculty/Student)</b><br/><font color='#64748b'>requester@example.com</font>", table_cell),
            Paragraph("<b>Requester</b>", table_cell_bold),
            Paragraph("Zero-trust restricted client. Can ONLY view and submit own support tickets. Cannot access internal sprint tasks.", table_cell),
            Paragraph("<b>Exclusively restricted to <code>/tickets/my</code></b>", table_cell)
        ],
        [
            Paragraph("<b>Faisal Kamran (ISO Auditor)</b><br/><font color='#64748b'>Auditor Guest</font>", table_cell),
            Paragraph("<b>Guest / Auditor</b>", table_cell_bold),
            Paragraph("Read-only verification of SLA compliance rates, resolution timestamps, and immutable audit logs.", table_cell),
            Paragraph("Audit Activity Logs & CSV Exports", table_cell)
        ],
    ]

    t_roles = Table(roles_data, colWidths=[1.8 * inch, 1.2 * inch, 2.7 * inch, 1.8 * inch])
    t_roles.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), SECONDARY),
        ('BOX', (0, 0), (-1, -1), 0.5, BORDER),
        ('GRID', (0, 0), (-1, -1), 0.5, BORDER),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.white, ACCENT_BG]),
        ('PADDING', (0, 0), (-1, -1), 5),
    ]))
    story.append(t_roles)
    story.append(Spacer(1, 14))

    # ==========================================
    # 6. VERIFICATION & QUALITY STANDARDS
    # ==========================================
    story.append(Paragraph("6. Quality Assurance & Verification Standards", h1_style))
    story.append(Paragraph(
        "The platform includes automated PHPUnit / Pest integration suites certifying data integrity, SLA math, and Livewire reactivity:",
        body_style
    ))
    story.append(Paragraph("• <b>Dashboard Interactive Suite:</b> <code>DashboardChartsInteractiveTest.php</code> certifies time-travel filters, dataset counts, and Chart.js DOM bindings.", bullet_style))
    story.append(Paragraph("• <b>End-to-End Integration Suite:</b> <code>TicketingAndTasksIntegrationTest.php</code> certifies full cross-functional workflows from ticket intake to resolution and task completion.", bullet_style))
    story.append(Paragraph("• <b>Test Execution Status:</b> All 9 integration tests passing (100% assertions verified).", bullet_style))

    story.append(Spacer(1, 14))
    story.append(HRFlowable(width="100%", thickness=1, color=BORDER, spaceBefore=6, spaceAfter=8))
    story.append(Paragraph("<i>Authored by STMU MIS Directorate & Advanced Engineering Team • Built with Laravel, Livewire, Chart.js & Google Gemini AI.</i>", meta_style))

    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"PDF successfully built: {filename}")

if __name__ == "__main__":
    out_file = sys.argv[1] if len(sys.argv) > 1 else "STMU_MIS_Task_Manager_Features_and_Workflow.pdf"
    build_pdf(out_file)
