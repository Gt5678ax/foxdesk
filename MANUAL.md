# FoxDesk — Complete User Manual

> **Version 0.3.57** | Last Updated: 2026-03-02
> Self-hosted PHP helpdesk & time-tracking platform

---

## Table of Contents

1. [Introduction & Product Overview](#1-introduction--product-overview)
2. [System Requirements & Architecture](#2-system-requirements--architecture)
3. [Installation](#3-installation)
4. [First Login & Initial Setup Wizard](#4-first-login--initial-setup-wizard)
5. [Dashboard](#5-dashboard)
6. [Tickets — Full Lifecycle](#6-tickets--full-lifecycle)
7. [Customer Portal (End-User View)](#7-customer-portal-end-user-view)
8. [Time Tracking](#8-time-tracking)
9. [Recurring Tasks](#9-recurring-tasks)
10. [Users, Roles & Permissions](#10-users-roles--permissions)
11. [Organizations & Clients](#11-organizations--clients)
12. [Notifications & Email](#12-notifications--email)
13. [Email-to-Ticket (IMAP Ingest)](#13-email-to-ticket-imap-ingest)
14. [Reports & Analytics](#14-reports--analytics)
15. [Public Sharing — Tickets & Reports](#15-public-sharing--tickets--reports)
16. [Agent / External API](#16-agent--external-api)
17. [Admin Settings — Complete Reference](#17-admin-settings--complete-reference)
18. [System Update & Maintenance](#18-system-update--maintenance)
19. [Keyboard Shortcuts & Command Palette](#19-keyboard-shortcuts--command-palette)
20. [Localization & Multi-Language Support](#20-localization--multi-language-support)
21. [Docker Deployment](#21-docker-deployment)
22. [Security & Hardening](#22-security--hardening)
23. [Troubleshooting & FAQ](#23-troubleshooting--faq)
24. [Glossary](#24-glossary)
25. [Changelog (Recent)](#25-changelog-recent)
26. [Potentially Overlooked Features](#26-potentially-overlooked-features)

---

## 1. Introduction & Product Overview

### What is FoxDesk?

FoxDesk is a self-hosted, open-source helpdesk and service-desk platform written in PHP 8+. It is designed for small-to-medium teams that need a streamlined ticket management system, integrated time tracking, client billing reports, and email-based ticket ingestion — all without monthly SaaS fees or vendor lock-in.

### Core Philosophy

- **Self-hosted first** — Your data stays on your server, whether that is shared hosting with cPanel, a VPS, or a Docker container.
- **Zero dependencies** — No Composer, no npm build step. Upload the files, open the web wizard, done — just like WordPress.
- **Agent-first UX** — The interface prioritizes the support agent workflow: fast ticket resolution, one-click time tracking, inline editing, keyboard shortcuts, and a command palette.
- **Multi-language** — Built-in support for English, Czech (čeština), German (Deutsch), Spanish (español), and Italian (italiano). Each user can pick their own language.
- **Extensible via API** — A full REST API with Bearer-token authentication allows AI agents, automation scripts, and external systems to interact with FoxDesk programmatically.

### Feature Summary

| Area | Capabilities |
|------|-------------|
| **Tickets** | Create, assign, prioritize, tag, comment (public + internal), rich-text editor, file attachments, due dates, archive/restore, bulk actions, ticket export (Markdown), public share links |
| **Time Tracking** | Per-ticket stopwatch with pause/resume, manual time entry, billable/non-billable flags, cost rates, per-organization billing rates |
| **Recurring Tasks** | Automatic ticket creation on daily/weekly/monthly/yearly schedule, optional email notifications |
| **Reports** | Summary, detailed, weekly, worklog tabs; filter by organization/agent/tags/date-range; client-facing PDF-style report builder with public share links |
| **Email** | SMTP outbound (notifications), IMAP inbound (email-to-ticket), customizable email templates per language |
| **Users & Roles** | Three roles (Admin, Agent, User), per-user language, avatar, notification preferences, user impersonation, AI agent accounts |
| **Organizations** | Client grouping, logo, billing rate, ICO (company ID), multi-organization membership |
| **API** | Bearer-token REST API for agents: create/list/update tickets, add comments, log time, list statuses/priorities/users |
| **Admin** | Customizable statuses, priorities, ticket types (with icons & colors), email templates, system settings, debug logs, security logs, update system |
| **Sharing** | Public ticket share links (with expiry & revoke), public report share links |
| **UI** | Dark/light theme, responsive (mobile-friendly), drag-and-drop dashboard widgets, Tailwind CSS, Alpine.js interactivity |

---

## 2. System Requirements & Architecture

### Server Requirements

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| PHP | 8.0 | 8.1+ |
| MySQL | 5.7 | 8.0+ |
| MariaDB | 10.2 | 10.6+ |
| Disk Space | 50 MB (app) | 200 MB+ (with uploads & backups) |
| RAM | 128 MB | 256 MB+ |

### Required PHP Extensions

| Extension | Required? | Purpose |
|-----------|-----------|---------|
| `pdo_mysql` | **Yes** | Database connection |
| `mbstring` | **Yes** | Multi-byte string handling (UTF-8) |
| `json` | **Yes** | API & data processing |
| `openssl` | **Yes** | HTTPS, token generation, password hashing |
| `fileinfo` | Recommended | MIME type detection for uploads |
| `imap` | Optional | Email-to-ticket ingest |
| `zip` | Recommended | Backup & update system |

### Apache Modules

| Module | Required? | Purpose |
|--------|-----------|---------|
| `mod_rewrite` | **Yes** | Authorization header passthrough for API |

### Technology Stack

- **Backend:** PHP 8+ (no framework, custom MVC-like router)
- **Database:** MySQL 5.7+ / MariaDB 10.2+ with InnoDB, `utf8mb4_unicode_ci` collation
- **Frontend:** Tailwind CSS (pre-built `tailwind.min.css`), Alpine.js for interactivity, vanilla JavaScript
- **Rich-text Editor:** Quill.js (for ticket descriptions and comments)
- **Date Picker:** Flatpickr
- **Icons:** Font Awesome (inline SVG helper)

### File Structure Overview

```
foxdesk/
├── index.php              # Main entry point & router
├── config.php             # Database & app configuration (generated by installer)
├── config.example.php     # Template for config.php
├── install.php            # Web installer (delete after setup!)
├── upgrade.php            # Database migration script
├── image.php              # Secure image proxy for uploaded files
├── download.php           # Secure file download handler
├── theme.css              # Custom theme styles
├── tailwind.min.css       # Pre-compiled Tailwind CSS
├── version.json           # Current version metadata
├── assets/
│   ├── css/               # Additional stylesheets
│   └── js/                # JavaScript (app-header.js, app-footer.js, shortcuts.js)
├── backups/               # Auto-created update backups
├── bin/                   # CLI/Cron scripts
│   ├── ingest-emails.php          # IMAP email processor
│   ├── process-recurring-tasks.php # Recurring task scheduler
│   ├── run-maintenance.php         # Cleanup & maintenance
│   ├── allowed-senders.php         # Manage allowed email senders
│   ├── populate-tags.php           # Tag migration utility
│   └── seed-demo.php              # Demo data seeder
├── includes/
│   ├── schema.sql         # Fresh install database schema
│   ├── database.php       # PDO database abstraction layer
│   ├── functions.php      # Core helper functions
│   ├── auth.php           # Authentication & session management
│   ├── api.php            # API entry point
│   ├── api/               # Modular API handlers
│   │   ├── router.php
│   │   ├── ticket-handler.php
│   │   ├── user-handler.php
│   │   ├── upload-handler.php
│   │   ├── reorder-handler.php
│   │   ├── smtp-handler.php
│   │   ├── agent-handler.php    # External/AI agent API
│   │   └── update-api.php
│   ├── mailer.php                  # SMTP email sender
│   ├── ticket-*.php               # Ticket CRUD, access, queries, time, import, share
│   ├── user-functions.php
│   ├── settings-functions.php
│   ├── report-functions.php
│   ├── recurring-task-functions.php
│   ├── email-functions.php
│   ├── email-ingest-functions.php
│   ├── update-functions.php
│   ├── update-check-functions.php
│   ├── upload-functions.php
│   ├── security-helpers.php
│   ├── translations.php
│   ├── lang/                       # Language files
│   │   ├── en.php
│   │   ├── cs.php
│   │   ├── de.php
│   │   ├── es.php
│   │   └── it.php
│   ├── header.php                  # HTML <head>, nav, sidebar
│   ├── footer.php
│   ├── icons.php                   # SVG icon helper
│   └── components/                 # Reusable UI components
├── pages/
│   ├── dashboard.php
│   ├── tickets.php
│   ├── ticket-detail.php
│   ├── ticket-export.php
│   ├── new-ticket.php
│   ├── profile.php
│   ├── user-profile.php
│   ├── login.php
│   ├── forgot-password.php
│   ├── reset-password.php
│   ├── ticket-share.php           # Public ticket share page
│   ├── report-share.php           # Public report share page
│   ├── report-public.php          # Public report viewer
│   └── admin/
│       ├── settings.php
│       ├── statuses.php
│       ├── priorities.php
│       ├── ticket-types.php
│       ├── organizations.php
│       ├── clients.php
│       ├── users.php
│       ├── reports.php
│       ├── reports-list.php
│       ├── report-builder.php
│       ├── recurring-tasks.php
│       └── agent-connect.php
└── uploads/               # User-uploaded files (writable directory)
```

---

## 3. Installation

FoxDesk uses a **WordPress-style web installation wizard**. There is no need to manually create or edit `config.php` — the wizard does everything for you.

### Method A: Shared Hosting (FTP / cPanel) — Recommended

#### Step 1 — Upload Files

1. Download the latest FoxDesk release archive (e.g. `foxdesk-0.3.42.zip`).
2. Upload all files to your web root (`public_html/`) via FTP or the cPanel File Manager.
3. Ensure hidden files like `.htaccess` are uploaded (enable "Show Hidden Files" in File Manager).

#### Step 2 — Create a MySQL Database

Before running the wizard, you need a MySQL/MariaDB database and a user with privileges. This is the only preparation step.

**Via cPanel:**

1. Open **MySQL Databases** in cPanel.
2. Create a new database (e.g. `helpdesk`).
3. Create a new user with a strong password.
4. Add the user to the database with **All Privileges**.
5. Note the full database name (on cPanel it is usually `cpaneluser_dbname`).

**Via phpMyAdmin or MySQL CLI (VPS):**

```sql
CREATE DATABASE helpdesk_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'helpdesk_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON helpdesk_db.* TO 'helpdesk_user'@'localhost';
FLUSH PRIVILEGES;
```

> **Note:** The wizard can also auto-create the database if the provided MySQL user has CREATE privileges. In that case, just have the username and password ready.

#### Step 3 — Run the Installation Wizard

Open `https://your-domain.tld/install.php` in your browser. The wizard guides you through three steps:

<!-- Screenshot: install-wizard.png -->

**Step 1 of 3: Database**

Enter your database connection details:

| Field | Description | Typical value |
|-------|-------------|---------------|
| **Database host** | Server address | `localhost` (shared hosting) or `127.0.0.1` (VPS) |
| **Port** | MySQL port | `3306` (default) |
| **Database name** | The database you created | `helpdesk_db` |
| **Database user** | MySQL username | `helpdesk_user` |
| **Password** | MySQL password | Your chosen password |

Click **Connect and continue**. The wizard tests the connection, creates the database if needed, and creates all tables automatically.

**Step 2 of 3: App Setup**

| Field | Description |
|-------|-------------|
| **Application name** | The display name for your helpdesk (e.g. "Acme Support"). Shown in the header and emails. |
| **Admin email** | Your admin login email address. |
| **First name / Last name** | Your admin account name. |
| **Password** | Admin password (minimum 6 characters). |
| **Confirm password** | Repeat the password. |

Click **Finish installation**. The wizard:
- Creates the admin user account.
- Seeds default statuses (New, Testing, Waiting for customer, In progress, Done, Cancelled).
- Seeds default priorities (Low, Medium, High, Urgent).
- Seeds default ticket types (General, Quote request, Inquiry, Bug report).
- Seeds default email & IMAP settings.
- Seeds default email templates.
- **Auto-generates `config.php`** with your database credentials, a random 64-char `SECRET_KEY`, and the detected `APP_URL`.
- Creates the `uploads/` directory.

**Step 3 of 3: Complete!**

A green checkmark confirms the installation is finished. Click **Go to app** to log in.

#### Step 4 — Post-Install Security

> **IMPORTANT:** Delete `install.php` from the server immediately after installation. Leaving it accessible allows anyone to re-run the installer.

```bash
rm install.php
```

That's it — **no manual `config.php` editing, no command-line tools, no Composer**. Just upload, open the wizard, fill in the form, done.

> **Tip:** If you ever need to tweak the generated `config.php` later (e.g. to change the timezone, add IMAP settings, or adjust upload limits), the file is in the web root and can be edited with any text editor. A reference template is available in `config.example.php`.

### Method B: VPS / Dedicated Server

Follow steps 1–6 above, plus configure your web server:

**Apache** (`.htaccess` is included in the distribution):

```apache
<VirtualHost *:443>
    ServerName helpdesk.example.com
    DocumentRoot /var/www/helpdesk
    <Directory /var/www/helpdesk>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx** (manual configuration required):

```nginx
server {
    listen 443 ssl;
    server_name helpdesk.example.com;
    root /var/www/helpdesk;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_param HTTP_AUTHORIZATION $http_authorization;
    }

    location ~ ^/(backups|bin|includes)/ {
        deny all;
    }

    location ~ ^/uploads/.*\.php$ {
        deny all;
    }
}
```

### Method C: Docker

See [Section 21 — Docker Deployment](#21-docker-deployment) for full details.

---

## 4. First Login & Initial Setup Wizard

### Logging In

Navigate to your FoxDesk URL. You will see a clean login page. Enter the admin email and password you created during installation.

<!-- Screenshot: login-page.png -->

### Recommended First Steps After Login

1. **Settings → General** — Set your app name, default language, currency symbol, time format, and billing rounding.
2. **Settings → Email** — Configure SMTP so notification emails can be sent. Use the "Test SMTP" button to verify.
3. **Settings → Statuses** — Review and customize the ticket workflow states (e.g. Open, In Progress, Waiting, Resolved, Closed). Mark which statuses are "closed" states.
4. **Settings → Priorities** — Customize priority levels (e.g. Low, Normal, High, Urgent) with colors and icons.
5. **Settings → Ticket Types** — Define ticket categories (e.g. Bug, Feature Request, Support Question) with icons and colors.
6. **Settings → Organizations** — Create your first client organization(s).
7. **Settings → Users** — Add agent accounts for your support team, and user accounts for clients.
8. **Settings → Email Templates** — Review and customize the notification email templates for each supported language.

> **Tip:** FoxDesk seeds sensible defaults for statuses, priorities, and ticket types during installation. You can use them as-is or customize them to match your workflow.

---

## 5. Dashboard

The dashboard is the home screen after login. Its content adapts based on the user's role.

### Dashboard Widgets

The dashboard uses a **3-column CSS grid layout** on desktop (2-column on tablet, single column on mobile). Each widget is a draggable card.

**Widgets available to Agents & Admins:**

| Widget | Description |
|--------|-------------|
| **My Open Tickets** | Count of tickets assigned to you that are not in a "closed" status. Click to navigate to the filtered tickets list. |
| **Unassigned Tickets** | Count of tickets with no assignee. Highlights tickets that need attention. |
| **Overdue Tickets** | Tickets where `due_date` has passed and the ticket is still open. |
| **Due Soon** | Tickets with a due date within the next 48 hours. |
| **Recent Activity** | Stream of the latest ticket events: new tickets, status changes, comments, assignments. |
| **My Active Timer** | Shows if you have a running time-tracking timer, with a link to the ticket. |
| **Tickets by Status** | Visual breakdown of all tickets grouped by status (bar/list). |
| **Tickets by Priority** | Breakdown by priority level. |
| **Recent Tickets** | List of the 10 most recently updated tickets. |
| **My Time** | Shows your logged time for Today / This week / This month with progress bars. Times are displayed in human-readable format (e.g. "1h 15min", "42 min"). |
| **Team** | Table of all agents with their logged time (Today, This week, This month). Includes a totals row. |
| **Team Time** | Aggregate team time for Today / This week / This month with progress bars. |
| **Organization Summary** | (Admin only) Quick overview of tickets per organization. |

**Widgets available to Users (Customers):**

| Widget | Description |
|--------|-------------|
| **My Tickets** | Count of tickets the user created. |
| **Open Tickets** | Count of the user's tickets that are still open. |
| **Recent Tickets** | List of the user's most recently updated tickets. |

### Drag-and-Drop Widget Reordering

Agents and admins can rearrange dashboard widgets by dragging them:

1. Hover over a widget — the cursor changes to a grab icon.
2. Click and drag the widget to a new position.
3. Drop it on the desired location — widgets rearrange automatically.
4. The layout is saved per-user via the API (`save-dashboard-layout`) and persists across sessions.

> **Tip:** On mobile devices, drag-and-drop is disabled for usability. The default layout is used.

### Tag Filtering on Dashboard

You can filter the entire dashboard by tag(s) using the URL parameter `?tags=tagname`. For example:

```
https://helpdesk.example.com/?page=dashboard&tags=VIP,urgent
```

This filters all dashboard widgets to show only tickets with the specified tag(s).

---

## 6. Tickets — Full Lifecycle

Tickets are the core of FoxDesk. They represent support requests, tasks, bug reports, or any trackable work item.

### 6.1 Ticket List View

Navigate to **Tickets** in the sidebar.

<!-- Screenshot: tickets-list.png -->

**Features of the ticket list:**

- **Search:** Full-text search across ticket titles and descriptions. Type in the search box and press Enter or click the search icon.
- **Filters:** Filter by status, priority, ticket type, assignee, organization, and tags. Filters are applied via dropdowns above the list.
- **Sorting:** Click column headers to sort by title, status, priority, created date, updated date, due date, or assignee.
- **Pagination:** Configurable page size. Navigate between pages at the bottom.
- **Bulk Actions:** Select multiple tickets via checkboxes, then use the bulk action bar to change status, priority, assignee, tags, organization, or move tickets to the archive.

### 6.2 Creating a New Ticket

Navigate to **New Ticket** in the sidebar or click the "+" button.

<!-- Screenshot: new-ticket.png -->

**Fields:**

| Field | Required | Description |
|-------|----------|-------------|
| **Title** | Yes | Short summary of the issue (max 255 chars). |
| **Description** | No | Detailed description. Supports rich text (bold, italic, lists, links, code blocks) via the Quill editor. |
| **Ticket Type** | No | Category (e.g. Bug, Feature Request). Defaults to the system default type. |
| **Priority** | No | Severity level. Defaults to the system default priority. |
| **Status** | Auto | Automatically set to the default status (usually "Open"). |
| **Organization** | No | Link the ticket to a client organization. For users, this is auto-set based on their organization membership. |
| **Assignee** | No | Assign to a specific agent. Leave empty for unassigned. |
| **Due Date** | No | Optional deadline. Uses a date picker (Flatpickr). |
| **Tags** | No | Comma-separated tags for categorization (e.g. `VIP, billing, server-02`). |
| **Attachments** | No | Upload files (drag-and-drop or click to browse). Max file size is configured in `config.php` (default: 10 MB). |
| **Time (min)** | No | (Agents only) Manual time input in minutes. Enter the number of minutes spent and a time entry will be created automatically with the ticket. If both the timer and manual time are provided, manual time takes priority. |

> **Tip:** When a user (customer) creates a ticket, they see a simplified form without the assignee field. The organization is automatically set based on their account.

### 6.3 Ticket Detail View

Click on any ticket in the list to open its detail page.

<!-- Screenshot: ticket-detail.png -->

The ticket detail page is the primary workspace for resolving tickets. It consists of:

#### Header Bar

- **Ticket ID & Hash** — e.g. `#42` with a unique hash for public sharing.
- **Title** — Editable inline (click to edit for agents/admins).
- **Status Badge** — Color-coded current status. Click to change.
- **Priority Badge** — Color-coded current priority.
- **Quick Actions** — Buttons for common operations (described below).

#### Ticket Properties Sidebar

On the right side (desktop) or below the description (mobile):

| Property | Editable? | Notes |
|----------|-----------|-------|
| Status | Yes (dropdown) | Changes are logged in the activity timeline. |
| Priority | Yes (dropdown) | — |
| Ticket Type | Yes (dropdown) | — |
| Assignee | Yes (dropdown) | Search for agents by name. |
| Organization | Yes (dropdown) | — |
| Due Date | Yes (date picker) | Shows visual indicators for overdue/due-soon. |
| Tags | Yes (inline editor) | Add/remove tags with autocomplete. |
| Created | Read-only | Timestamp + "by [user name]" |
| Updated | Read-only | Last modification timestamp. |
| Source | Read-only | How the ticket was created: `web`, `email`, `api`, `recurring`, `import`. |

#### Description

The full ticket description rendered as rich HTML. If the description was entered as plain text, it is displayed with preserved line breaks.

#### Comments / Discussion Thread

Below the description is the comment thread. Comments appear in chronological order.

**Adding a Comment:**

1. Type in the rich-text comment box at the bottom.
2. Optionally toggle **Internal Note** (checkbox). Internal notes are visible only to agents and admins, not to the ticket creator or other users.
3. Optionally attach files.
4. Click **Add Comment** (or press `Ctrl+Enter`).

**Editing a Comment:**

- Agents can edit their own comments. Click the pencil icon on a comment to enter edit mode.
- Admins can edit any comment.

**Deleting a Comment:**

- Click the trash icon. A confirmation dialog appears.
- Deleted comments are permanently removed along with their attachments.

#### Activity Timeline

The activity timeline is interleaved with comments and shows all ticket events:

- Status changes (with old → new values)
- Priority changes
- Assignee changes
- Ticket type changes
- Organization changes
- Tag additions/removals
- Time entries (start/stop/manual)
- Archiving/restoring

Each activity entry shows the user who made the change, the timestamp, and the details.

#### Attachments

Files attached to the ticket or its comments are listed in a dedicated section:

- **Image thumbnails** — Images (JPEG, PNG, GIF, WebP) show inline thumbnails. Click to open a **lightbox preview** with full-size image.
- **Non-image files** — Show a file icon with the filename. Click to download.
- **Attachment metadata** — Original filename, file size, upload date, uploaded by.

> **Tip:** Uploaded files are stored in the `uploads/` directory with randomized filenames for security. The original filename is preserved in the database.

### 6.4 Quick Status Change

On the ticket detail page, you can change the ticket status with a single click:

1. Click the current status badge in the header.
2. A dropdown shows all available statuses with their colors.
3. Select the new status — it is applied immediately via AJAX.
4. The page updates without a full reload.

### 6.5 Ticket Assignment

**Manual Assignment:**

- In the ticket detail sidebar, click the Assignee dropdown.
- Search for an agent by name.
- Select the agent to assign the ticket.

**Self-assignment:**

- Agents can assign tickets to themselves using the same dropdown.

**Unassign:**

- Select the empty/none option in the assignee dropdown.

### 6.6 Tags

Tags are flexible labels you can attach to tickets. They are stored as a comma-separated string in the `tags` column.

**Adding Tags:**

1. On the ticket detail page, find the Tags field in the sidebar.
2. Type a new tag and press Enter, or select from autocomplete suggestions.
3. Tags are saved via AJAX immediately.

**Tag Autocomplete:**

The tag input provides autocomplete suggestions based on tags already used in other tickets. This promotes consistency.

**Filtering by Tags:**

- On the tickets list page, use the tag filter dropdown.
- On the dashboard, append `?tags=tagname` to the URL.
- In reports, filter by tags in the filter bar.

### 6.7 Due Dates

Set a due date on a ticket to establish a deadline:

1. In the ticket detail sidebar, click the Due Date field.
2. Use the Flatpickr date picker to select a date.
3. The due date is saved immediately.

**Visual indicators:**

- **Overdue** (past due date, ticket still open) — Shown with a red badge/icon.
- **Due soon** (within 48 hours) — Shown with an orange/yellow badge.
- **Due date notifications** — If email notifications are enabled, the system sends notifications for overdue and due-soon tickets.

### 6.8 Ticket Archive

FoxDesk supports soft-archiving of tickets:

**Archiving a Ticket:**

1. On the ticket detail page, click the **Archive** button.
2. Alternatively, use bulk actions on the ticket list: select tickets → click "Archive".
3. Archived tickets are removed from the main ticket list but not deleted.

**Viewing the Archive:**

- Navigate to **Tickets → Archive** (visible to admins only).
- The archive view shows all archived tickets.

**Restoring from Archive:**

- Open an archived ticket and click **Restore**.
- The ticket returns to the main ticket list.

**Permanent Deletion:**

- In the archive view, select tickets and click **Delete permanently**.
- This removes the ticket, all comments, attachments (files are deleted from disk), activity logs, and time entries.
- **This action cannot be undone.**

### 6.9 Ticket Export

Tickets can be exported as Markdown files:

1. On the ticket detail page, click the **Export** button.
2. This generates a `.md` file containing the ticket title, description, all comments, and metadata.
3. The file is downloaded to your browser.

### 6.10 Bulk Actions

On the tickets list page, agents and admins can perform bulk operations:

1. Select multiple tickets using the checkboxes.
2. The bulk action bar appears at the top.
3. Available actions:
   - **Change Status** — Set all selected tickets to a chosen status.
   - **Change Priority** — Set all selected tickets to a chosen priority.
   - **Change Assignee** — Assign all selected tickets to an agent (or unassign).
   - **Change Organization** — Move tickets to a different organization (or clear).
   - **Change Tags** — Add tags, remove tags, or replace all tags on selected tickets.
   - **Archive** — Move selected tickets to the archive.
4. Click **Apply** to execute the bulk action.

> **Tip:** In the archive view, the bulk action is **Delete permanently** instead of archive.

### 6.11 Ticket Source Tracking

Every ticket records its source:

| Source | Description |
|--------|-------------|
| `web` | Created via the web interface (default). |
| `email` | Created from an inbound email (IMAP ingest). |
| `api` | Created via the Agent/External API. |
| `recurring` | Auto-created by a recurring task schedule. |
| `import` | Created via the Markdown ticket import feature. |

The source is displayed in the ticket detail sidebar and can be used for filtering.

---

## 7. Customer Portal (End-User View)

Users with the **User** role have a simplified view of FoxDesk designed for end-customers.

### What Users See

- **Dashboard** — Shows their own ticket counts (total, open) and recent tickets.
- **Tickets** — Lists only tickets the user created, plus tickets explicitly shared with them via ticket access grants.
- **New Ticket** — Simplified creation form (no assignee field, organization auto-selected).
- **Ticket Detail** — Can view their own tickets, add comments, upload attachments. Cannot see internal notes.
- **Profile** — Can update their name, language, avatar, password, and notification preferences.

### What Users Cannot See

- Other users' tickets (unless explicitly shared via ticket access).
- Internal notes/comments on tickets.
- Admin settings, reports, organizations management.
- The archive.
- User management.

### Ticket Access Sharing

Admins and agents can grant a user access to a specific ticket they didn't create:

1. On the ticket detail page, use the "Share Access" feature.
2. Search for a user and add them.
3. The user can now see and comment on the ticket from their portal.

This is useful when multiple users from the same organization need to follow a ticket.

---

## 8. Time Tracking

FoxDesk includes a full-featured time tracking system designed for billing and productivity analysis.

### 8.1 Live Timer (Stopwatch)

Each ticket has a timer that agents can start, pause, resume, and stop:

**Starting a Timer:**

1. Open a ticket detail page.
2. Click the **Start Timer** button (play icon) in the time tracking section.
3. The timer begins counting from `00:00:00`.
4. A small timer indicator appears in the navigation bar so you know a timer is running from any page.

**Pausing and Resuming:**

- Click **Pause** to temporarily stop the timer. The elapsed time is preserved.
- Click **Resume** to continue from where you left off.
- Paused time is tracked separately — only active time counts toward the entry.

**Stopping the Timer:**

1. Click **Stop Timer**.
2. A dialog appears where you can:
   - Review the elapsed time.
   - Add a **summary** describing the work done.
   - Mark the entry as **billable** or non-billable.
   - Adjust the billable and cost rates.
3. Click **Save** to create the time entry.

**Discarding a Timer:**

- Click **Discard** to cancel the running timer without saving an entry.

> **Important:** Only one timer can run at a time per user across all tickets. Starting a new timer on a different ticket is not allowed until the current one is stopped or discarded.

### 8.2 Manual Time Entry

Agents can add time entries manually without using the live timer:

**On the ticket detail page:**

1. Click the **pen icon** (✏️) next to the timer controls to open the manual entry form.
2. Fill in:
   - **Date** — When the work was done.
   - **Start / End** — Start and end time. Duration is calculated automatically.
   - **Summary** — Description of the work (optional — time entries can be logged without a comment).
   - **Billable** — Toggle billable/non-billable.
3. Click **Send update** to save the time entry.

> **Note:** Time entries can be added standalone without writing a comment. This is useful for quickly logging time spent on a ticket without needing to explain the work in a separate comment.

**On the new ticket form:**

Agents can also log time directly when creating a new ticket:

1. Next to the timer, there is a **Time (min)** input field.
2. Enter the number of minutes spent.
3. When the ticket is saved, a time entry labeled "Ticket creation" is automatically created.
4. If both the timer and manual time are provided, the manual time takes priority.

### 8.3 Inline Editing of Time Entries

Existing time entries can be edited inline:

- Click on a time entry in the ticket's time tracking section.
- Edit the summary, duration, billable flag, or rates.
- Changes are saved via AJAX.

### 8.4 Deleting Time Entries

- Click the trash icon on a time entry.
- Confirm the deletion in the dialog.

### 8.5 Billable Rates

FoxDesk supports a hierarchical rate system:

| Level | Source | Description |
|-------|--------|-------------|
| Organization | `organizations.billable_rate` | Default billing rate for the client. |
| User | `users.cost_rate` | Internal cost rate per agent (for profitability tracking). |
| Time Entry | `ticket_time_entries.billable_rate` / `.cost_rate` | Per-entry override. |

When a timer is stopped, the rates are auto-populated from the organization and user settings, but can be overridden on each entry.

### 8.6 Long Timer Alert

If a timer has been running for an extended period (configurable), the system can send an email notification alerting the agent and/or admin. This prevents forgotten timers from inflating time records.

---

## 9. Recurring Tasks

Recurring tasks automatically create new tickets on a defined schedule.

### Creating a Recurring Task

Navigate to **Admin → Recurring Tasks** and click **New Recurring Task**.

| Field | Required | Description |
|-------|----------|-------------|
| **Title** | Yes | Title for the auto-created tickets. |
| **Description** | No | Description for the auto-created tickets. |
| **Ticket Type** | No | Category for the created tickets. |
| **Organization** | No | Client organization to assign to. |
| **Assigned User** | No | Agent to assign the created tickets to. |
| **Priority** | No | Priority for the created tickets. |
| **Initial Status** | Yes | Status for newly created tickets (usually "Open"). |
| **Recurrence Type** | Yes | `daily`, `weekly`, `monthly`, or `yearly`. |
| **Recurrence Interval** | Yes | Every N days/weeks/months/years (default: 1). |
| **Day of Week** | Conditional | Required for weekly recurrence (0=Sunday, 1=Monday, etc.). |
| **Day of Month** | Conditional | Required for monthly recurrence (1-31). |
| **Month** | Conditional | Required for yearly recurrence (1-12). |
| **Start Date** | Yes | When the schedule begins. |
| **End Date** | No | When the schedule stops (leave empty for indefinite). |
| **Send Email Notification** | No | Whether to email the assigned user when a ticket is created. |
| **Active** | Yes | Toggle the task on/off without deleting it. |

### How It Works

1. The cron job `bin/process-recurring-tasks.php` runs every hour.
2. It checks all active recurring tasks where `next_run_date <= now`.
3. For each matching task, it creates a new ticket with the configured properties.
4. It calculates and updates `next_run_date` based on the recurrence rules.
5. If email notification is enabled, it sends an email to the assigned agent.

### Cron Setup

Add this to your crontab:

```bash
0 * * * * /usr/bin/php /path/to/foxdesk/bin/process-recurring-tasks.php >> /var/log/foxdesk-recurring.log 2>&1
```

### Managing Recurring Tasks

- **Edit** — Click on a recurring task to modify its settings.
- **Deactivate** — Toggle the Active switch to pause the schedule without deleting.
- **Delete** — Permanently remove the recurring task. Tickets already created are not affected.

> **Tip:** The recurring task list shows the next scheduled run date for each task, making it easy to verify the schedule is correct.

---

## 10. Users, Roles & Permissions

### 10.1 Role Hierarchy

FoxDesk has three built-in roles with increasing privileges:

| Role | Description | Access Level |
|------|-------------|-------------|
| **User** | Customer / end-user. Can create tickets, comment on their own tickets, manage their profile. | Limited — own tickets only. |
| **Agent** | Support staff. Can see all tickets, assign, comment, track time, view reports. | Broad — all tickets + reports. |
| **Admin** | Full access. Can manage settings, users, organizations, templates, system updates. | Full — everything. |

### 10.2 Permission Details

| Capability | User | Agent | Admin |
|------------|------|-------|-------|
| View own tickets | ✅ | ✅ | ✅ |
| View all tickets | ❌ | ✅ | ✅ |
| Create tickets | ✅ | ✅ | ✅ |
| Assign tickets | ❌ | ✅ | ✅ |
| Change status/priority | Own only | ✅ | ✅ |
| Internal comments | ❌ | ✅ | ✅ |
| Time tracking | ❌ | ✅ | ✅ |
| View reports | ❌ | ✅ | ✅ |
| Manage organizations | ❌ | ❌ | ✅ |
| Manage users | ❌ | ❌ | ✅ |
| Admin settings | ❌ | ❌ | ✅ |
| Archive access | ❌ | ❌ | ✅ |
| Impersonate users | ❌ | ❌ | ✅ |
| System updates | ❌ | ❌ | ✅ |

### 10.3 Managing Users

Navigate to **Admin → Users**.

**Creating a User:**

1. Click **New User**.
2. Fill in: Email, First Name, Last Name, Role, Organization (optional), Language.
3. Set an initial password.
4. Click **Create**.

**Editing a User:**

1. Click on a user in the list.
2. Edit any fields: name, email, role, organization, language, cost rate, contact phone, notes.
3. Optionally upload or change their avatar.
4. Click **Save**.

**Deactivating/Archiving a User:**

- Toggle the **Active** switch to deactivate a user. Deactivated users cannot log in.
- Deactivated users' tickets and time entries remain intact.

> **Tip:** FoxDesk uses soft-delete for users (the `deleted_at` column). Archived users can be restored.

### 10.4 User Impersonation

Admins can "log in as" any user to see the application from their perspective:

1. Go to **Admin → Users**.
2. Click the **Impersonate** button (eye icon) next to the user.
3. You are now logged in as that user — the interface shows their permissions and data.
4. A banner at the top indicates you are impersonating someone.
5. Click **Stop Impersonation** (or navigate to `?page=impersonate&stop=1`) to return to your admin account.

**Security:**

- All impersonation events are logged in the security log (start/stop, who impersonated whom).
- Archived/inactive users cannot be impersonated.
- You cannot impersonate yourself.
- If the impersonated user becomes inactive/deleted during the session, the system automatically restores the admin session.

### 10.5 AI Agent Accounts

FoxDesk supports creating special "AI Agent" user accounts:

1. Navigate to **Admin → Users** and switch to the **AI Agents** tab.
2. Click **New AI Agent**.
3. Fill in: Agent name, AI model name, cost rate.
4. The system generates a unique **API token** for the agent.
5. Copy and securely store this token — it is used for API authentication.

AI Agents appear as regular users in ticket assignment and activity logs, but they authenticate via Bearer tokens instead of passwords.

### 10.6 User Profile

Every user can manage their own profile at **Profile** (sidebar).

**Available Settings:**

- First Name, Last Name
- Email (requires current password confirmation to change)
- Language preference (en, cs, de, es, it)
- Avatar (upload an image)
- Contact Phone
- Notes (personal notes visible to admins)
- Password change (requires current password)
- **Notification Preferences:**
  - Email notifications (on/off)
  - In-app notifications (on/off)
  - In-app notification sound (on/off)

---

## 11. Organizations & Clients

Organizations represent your clients — the companies or groups that submit support requests.

### Managing Organizations

Navigate to **Admin → Organizations**.

**Creating an Organization:**

1. Click **New Organization**.
2. Fill in:
   - **Name** — Company name.
   - **ICO** — Company identification number (optional, common in Czech/EU business).
   - **Address** — Company address.
   - **Contact Email** — Primary contact email.
   - **Contact Phone** — Primary phone number.
   - **Notes** — Internal notes about this client.
   - **Logo** — Upload a company logo.
   - **Billable Rate** — Default hourly billing rate for this client (used in time tracking & reports).
   - **Active** — Toggle organization on/off.
3. Click **Create**.

### Organization Members

Each organization can have multiple users associated with it:

- Users can be assigned to an organization during user creation/editing.
- A user can belong to multiple organizations (the ★ star marks their primary organization).
- Organization membership determines which tickets are visible to users and which organization is auto-selected when they create tickets.

**Adding Members:**

1. Open the organization's detail page.
2. Use the **Add Member** search to find and add users.
3. Members are linked via the `users.organization_id` field (primary) or the user-organization association.

**Removing Members:**

1. Click the **Remove** button next to a member.
2. The user is unlinked from the organization but not deleted.

### Clients Page

Navigate to **Admin → Clients** for a dedicated view of all users with the "User" role, grouped by organization. This is an alternative way to manage customer accounts.

---

## 12. Notifications & Email

### 12.1 SMTP Configuration

FoxDesk sends email notifications via SMTP. Configure it in **Admin → Settings → Email**.

| Setting | Description |
|---------|-------------|
| **SMTP Enabled** | Master toggle for outbound email. |
| **SMTP Host** | Mail server hostname (e.g. `smtp.gmail.com`). |
| **SMTP Port** | Port number (587 for TLS, 465 for SSL, 25 for none). |
| **SMTP Encryption** | `tls`, `ssl`, or `none`. |
| **SMTP Username** | Authentication username (usually your email address). |
| **SMTP Password** | Authentication password or app-specific password. |
| **From Email** | The "From" address on outbound emails. |
| **From Name** | The "From" display name. |

**Common SMTP Configurations:**

| Provider | Host | Port | Encryption |
|----------|------|------|------------|
| Gmail / Google Workspace | `smtp.gmail.com` | 587 | TLS |
| Microsoft 365 | `smtp.office365.com` | 587 | TLS |
| Amazon SES | `email-smtp.region.amazonaws.com` | 587 | TLS |
| Mailgun | `smtp.mailgun.org` | 587 | TLS |
| Generic | Your SMTP server | 587/465/25 | TLS/SSL/none |

> **Tip:** Click the **Test SMTP** button after configuration to send a test email to the admin address. This verifies the connection works before relying on it for production notifications.

### 12.2 Email Templates

FoxDesk sends the following notification emails:

| Template Key | Trigger | Description |
|-------------|---------|-------------|
| `new_ticket` | New ticket created | Sent to the assigned agent (if any). |
| `new_comment` | Comment added to a ticket | Sent to the ticket creator and assigned agent. |
| `status_change` | Ticket status changes | Sent to the ticket creator and assigned agent. |
| `ticket_assignment` | Ticket assigned to an agent | Sent to the newly assigned agent. |
| `ticket_confirmation` | User creates a ticket | Confirmation sent back to the ticket creator. |
| `password_reset` | Password reset requested | Sent to the user with a reset link. |
| `recurring_task_assignment` | Recurring task creates a ticket | Sent to the assigned agent if notifications are enabled. |
| `long_timer_alert` | Timer running too long | Alert email sent to the agent and/or admin. |

**Customizing Templates:**

1. Go to **Admin → Settings → Email Templates**.
2. Each template has a **subject** and **body** field.
3. Templates support **per-language** variants — if you have Czech users, create a `cs` variant of each template.
4. Use placeholder variables in templates:
   - `{ticket_title}` — The ticket title.
   - `{ticket_id}` — The ticket ID number.
   - `{ticket_url}` — Direct link to the ticket.
   - `{status}` — Current/new status.
   - `{assignee}` — Name of the assigned agent.
   - `{user_name}` — Name of the relevant user.
   - `{comment}` — Comment text (for new_comment).
   - `{reset_link}` — Password reset URL (for password_reset).
   - `{app_name}` — Application name.

> **Tip:** If a template is missing from the database for a specific language, FoxDesk falls back to built-in defaults in `includes/mailer.php`. The built-in defaults cover English, Czech, German, Spanish, and Italian.

### 12.3 Notification Preferences

Each user can control their notification preferences in their profile:

- **Email Notifications** — Enable/disable all email notifications.
- **In-App Notifications** — Enable/disable browser-based notifications.
- **In-App Sound** — Enable/disable sound when a notification appears.

---

## 13. Email-to-Ticket (IMAP Ingest)

FoxDesk can automatically create tickets from incoming emails via IMAP.

### How It Works

1. The system connects to an IMAP mailbox (e.g. `support@yourcompany.com`).
2. It reads unprocessed emails from the configured folder (default: INBOX).
3. For each email:
   - Validates the sender against the **allowed senders** list.
   - If the sender matches an existing user, creates the ticket under that user.
   - If `IMAP_ALLOW_UNKNOWN_SENDERS` is true, creates a new user account for unknown senders.
   - Extracts the email subject as the ticket title and body as the description.
   - Processes attachments (respecting size limits and denied file extensions).
   - Stores email metadata (headers, message-id, in-reply-to) for thread tracking.
4. Processed emails are moved to the `Processed` folder; failed ones to the `Failed` folder.
5. The process is idempotent — each email is processed only once, tracked by mailbox UID.

### Configuration

#### In `config.php`:

```php
define('IMAP_ENABLED', true);
define('IMAP_HOST', 'imap.example.com');
define('IMAP_PORT', 993);
define('IMAP_ENCRYPTION', 'ssl');      // ssl | tls | none
define('IMAP_VALIDATE_CERT', false);
define('IMAP_USERNAME', 'support@example.com');
define('IMAP_PASSWORD', 'email_password');
define('IMAP_FOLDER', 'INBOX');
define('IMAP_PROCESSED_FOLDER', 'Processed');
define('IMAP_FAILED_FOLDER', 'Failed');
define('IMAP_MAX_EMAILS_PER_RUN', 50);
define('IMAP_MAX_ATTACHMENT_SIZE', 10 * 1024 * 1024); // 10 MB
define('IMAP_DENY_EXTENSIONS', 'php,phtml,php3,php4,php5,phar,exe,bat,cmd,js,vbs,ps1,sh');
define('IMAP_ALLOW_UNKNOWN_SENDERS', false);
```

#### Cron Job:

```bash
*/5 * * * * /usr/bin/php /path/to/foxdesk/bin/ingest-emails.php >> /var/log/foxdesk-email.log 2>&1
```

### Allowed Senders

By default, only whitelisted senders can create tickets via email. Manage allowed senders:

**Via Admin UI:**

Settings for allowed senders may be configured in the admin panel.

**Via CLI:**

```bash
php bin/allowed-senders.php add email user@example.com
php bin/allowed-senders.php add domain example.com
php bin/allowed-senders.php list
php bin/allowed-senders.php remove email user@example.com
```

**Sender Types:**

| Type | Description |
|------|-------------|
| `email` | Allow a specific email address. |
| `domain` | Allow all email addresses from a domain (e.g. `@example.com`). |

### Email Thread Tracking

FoxDesk tracks email threads using the `Message-ID`, `In-Reply-To`, and `References` headers. If an incoming email is a reply to an existing ticket's email thread, the email is added as a comment on that ticket rather than creating a new one.

### Ingest Logs

All email processing is logged in the `email_ingest_logs` table:

| Status | Meaning |
|--------|---------|
| `processed` | Email was successfully converted to a ticket or comment. |
| `skipped` | Email was ignored (e.g. unknown sender, duplicate). |
| `failed` | An error occurred during processing. |

---

## 14. Reports & Analytics

FoxDesk provides comprehensive time-tracking reports for billing and analysis.

### 14.1 Time Reports

Navigate to **Admin → Reports** (accessible to Agents and Admins).

#### Report Tabs

| Tab | Description |
|-----|-------------|
| **Summary** | Aggregated totals: total hours, billable hours, revenue, cost, profit — grouped by agent and/or organization. |
| **Detailed** | Line-by-line list of all time entries with ticket, agent, duration, billable amount, dates. |
| **Weekly** | Week-by-week breakdown showing hours per agent per week. |
| **Worklog** | Detailed work log grouped by date and agent. |
| **Shared** | List of shared report links with their status (active/expired/revoked). |

#### Filters

All report tabs share a common filter bar:

| Filter | Description |
|--------|-------------|
| **Time Range** | Preset ranges: This Week, This Month, Last Month, This Quarter, This Year, Custom. |
| **Custom Date Range** | Start and end dates for custom filtering. |
| **Organizations** | Filter by one or more client organizations. |
| **Agents** | Filter by one or more agents. |
| **Tags** | Filter by ticket tags. |
| **Show Money** | Toggle to show/hide financial columns (useful for non-billing views). |

### 14.2 Report Builder

Navigate to **Admin → Report Builder** to create professional client-facing reports.

**Creating a Report:**

1. Click **New Report**.
2. Configure:
   - **Title** — Report name (e.g. "January 2026 - Acme Corp").
   - **Organization** — The client this report is for.
   - **Date Range** — Period covered by the report.
   - **Language** — Report language (en, cs, de, es, it).
   - **Executive Summary** — Free-text overview written by you.
   - **Show Financials** — Toggle billing columns.
   - **Show Team Attribution** — Show which agent worked on what.
   - **Show Cost Breakdown** — Show internal cost data (usually off for client reports).
   - **Group By** — Group time entries by: none, ticket, agent, date.
   - **Rounding** — Round time to nearest N minutes (e.g. 15 min).
   - **Theme Color** — Customize the report accent color.
   - **Hide Branding** — Remove FoxDesk branding from the report.
3. Click **Generate** to create a snapshot.

**Report Snapshots:**

Each time you generate a report, a snapshot is saved with the calculated KPI data and chart data. You can regenerate snapshots as data changes.

**Sharing Reports:**

Reports can be shared via public links — see [Section 15](#15-public-sharing--tickets--reports).

### 14.3 Report KPIs

Generated reports include these key performance indicators:

- Total time tracked (hours)
- Billable time (hours)
- Non-billable time (hours)
- Total revenue (billable hours × rate)
- Total cost (hours × agent cost rate)
- Profit margin
- Number of tickets worked on
- Number of agents involved
- Average time per ticket

---

## 15. Public Sharing — Tickets & Reports

FoxDesk supports sharing tickets and reports with external parties via secure public links.

### 15.1 Ticket Public Sharing

**Creating a Share Link:**

1. Open a ticket detail page.
2. Click **Share** (or the share icon).
3. A unique public URL is generated.
4. Copy the URL and send it to the recipient.

**Share Link Properties:**

- **Token-based** — Each link contains a 64-character random token.
- **Hashed storage** — The token is stored as a SHA-256 hash in the database. Even if the database is compromised, the original link cannot be reconstructed.
- **Expiry** — Optionally set an expiration date on the share link.
- **Revocable** — You can revoke a share link at any time, immediately invalidating it.
- **Read-only** — External viewers can see the ticket title, description, comments (excluding internal notes), and attachments, but cannot make changes.

**What the Public Viewer Sees:**

- Ticket title and description.
- Public comments (internal notes are hidden).
- Attachments.
- Status and priority.
- A clean, standalone page without navigation or login requirements.

### 15.2 Report Public Sharing

Reports from the Report Builder can also be shared publicly:

1. In the report list, click the **Share** button.
2. A unique public URL is generated for the report.
3. Recipients can view the full report with KPIs, charts, and time breakdowns.
4. Share links can be revoked or set to expire.

---

## 16. Agent / External API

FoxDesk provides a REST API for external integrations, AI agents, and automation scripts.

### 16.1 Authentication

The API uses **Bearer token** authentication:

```
Authorization: Bearer <your-api-token>
```

Tokens are generated when creating AI Agent accounts (see [Section 10.5](#105-ai-agent-accounts)) or can be assigned to regular user accounts.

### 16.2 Base URL

All API endpoints are accessed via:

```
https://your-domain.tld/index.php?page=api&action=<endpoint>
```

### 16.3 Response Format

All responses follow a standard JSON format:

**Success:**
```json
{
    "success": true,
    "data": { ... }
}
```

**Error:**
```json
{
    "success": false,
    "error": "Error message description"
}
```

### 16.4 Available Endpoints

#### Identity

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `agent-me` | Get the authenticated user's profile. |

#### Lookups

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `agent-list-statuses` | List all ticket statuses with IDs, names, colors. |
| GET | `agent-list-priorities` | List all priorities with IDs, names, colors. |
| GET | `agent-list-users` | List all users. Params: `?role=user` (filter by role), `?exclude_ai=1` (exclude AI agents). |

#### Tickets

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `agent-create-ticket` | Create a new ticket. Body: `title`, `description`, `priority_id`, `status_id`, `assignee_id`, `organization_id`, `ticket_type_id`, `due_date`, `tags`. |
| GET | `agent-list-tickets` | List tickets. Supports pagination, status/priority/assignee filters. |
| GET | `agent-get-ticket` | Get a single ticket by ID. Params: `?ticket_id=123`. Returns full ticket with comments and time entries. |
| POST | `agent-add-comment` | Add a comment to a ticket. Body: `ticket_id`, `content`, `is_internal` (0/1). |
| POST | `agent-update-status` | Change a ticket's status. Body: `ticket_id`, `status_id`. |

#### Time Tracking

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `agent-log-time` | Log a manual time entry. Body: `ticket_id`, `duration_minutes`, `summary`, `is_billable` (0/1), `started_at`. |

### 16.5 API Examples

**Create a ticket:**

```bash
curl -X POST "https://helpdesk.example.com/index.php?page=api&action=agent-create-ticket" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "title=Server+disk+full&description=Disk+usage+at+95%25&priority_id=3&assignee_id=2"
```

**List tickets:**

```bash
curl "https://helpdesk.example.com/index.php?page=api&action=agent-list-tickets" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Add a comment:**

```bash
curl -X POST "https://helpdesk.example.com/index.php?page=api&action=agent-add-comment" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d "ticket_id=42&content=Investigating+now&is_internal=1"
```

**Log time:**

```bash
curl -X POST "https://helpdesk.example.com/index.php?page=api&action=agent-log-time" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d "ticket_id=42&duration_minutes=30&summary=Root+cause+analysis&is_billable=1"
```

### 16.6 Internal API Endpoints

These endpoints are used by the FoxDesk frontend (AJAX calls) and require session-based authentication:

| Endpoint | Purpose |
|----------|---------|
| `change-status` | Quick status change on ticket detail. |
| `start-timer` / `pause-timer` / `resume-timer` / `discard-timer` | Live timer control. |
| `delete-time-entry` / `update-time-inline` | Time entry management. |
| `edit-comment` / `delete-comment` | Comment editing. |
| `search_users` | User search (autocomplete). |
| `get_user_tickets` | Tickets for a specific user. |
| `get_active_timer` / `get_active_timers` | Check timer state. |
| `org-add-user` / `org-remove-user` | Organization membership. |
| `get-tags` / `update-tags` | Tag management. |
| `search-tickets` | Command palette search. |
| `save-dashboard-layout` | Dashboard widget order. |
| `upload` | File upload handler. |
| `reorder-statuses` / `reorder-priorities` / `reorder-ticket-types` | Drag-and-drop reordering. |
| `move-status-up` / `move-status-down` / `move-priority-up` / `move-priority-down` | Arrow-key reordering. |
| `test-smtp` | Send a test email via SMTP. |
| `check-for-updates` / `download-remote-update` / `dismiss-update-notice` | System update management. |

---

## 17. Admin Settings — Complete Reference

Navigate to **Admin → Settings**. The settings page is divided into tabs.

### 17.1 General Settings

| Setting | Description | Default |
|---------|-------------|---------|
| **App Name** | Display name shown in the header and emails. | FoxDesk |
| **App Logo** | Custom logo image (upload). Replaces the default FoxDesk logo. | — |
| **Favicon** | Custom favicon for browser tabs. | — |
| **Default Language** | System-wide default language for new users. | `en` |
| **Currency** | Currency symbol used in reports and billing (e.g. `CZK`, `EUR`, `USD`, `£`). | CZK |
| **Time Format** | 12-hour or 24-hour clock display. | 24h |
| **Billing Rounding** | Round time entries to the nearest N minutes in reports (e.g. 15 = quarter-hour rounding). | 15 |

### 17.2 Email Settings

See [Section 12.1 — SMTP Configuration](#121-smtp-configuration) for details.

### 17.3 Email Templates

See [Section 12.2 — Email Templates](#122-email-templates) for details.

### 17.4 Statuses

Ticket statuses define the workflow states:

**Default Statuses (seeded during install):**

| Status | Color | Default? | Closed? |
|--------|-------|----------|---------|
| New | Blue | ✅ | ❌ |
| Testing | Purple | ❌ | ❌ |
| Waiting for customer | Orange | ❌ | ❌ |
| In progress | Cyan | ❌ | ❌ |
| Done | Green | ❌ | ✅ |
| Cancelled | Red | ❌ | ✅ |

**Customization:**

- **Name** — Display name.
- **Color** — Hex color code for visual distinction.
- **Is Default** — Which status is assigned to new tickets (only one can be default).
- **Is Closed** — Whether this status counts as "resolved/closed" (affects dashboard counts, overdue calculations).
- **Sort Order** — Drag-and-drop reordering or up/down arrows.

### 17.5 Priorities

Ticket priorities define severity levels:

**Default Priorities (seeded during install):**

| Priority | Color | Icon |
|----------|-------|------|
| Low | Green | Arrow down |
| Medium | Blue | Minus |
| High | Orange | Arrow up |
| Urgent | Red | Exclamation |

**Customization:** Name, color, icon (Font Awesome), sort order, default flag.

### 17.6 Ticket Types

Ticket types categorize issues:

**Default Types (seeded during install):**

| Type | Icon | Color |
|------|------|-------|
| General | File | Blue |
| Quote request | Coins | Orange |
| Inquiry | Question circle | Purple |
| Bug report | Bug | Red |

**Customization:** Name, icon, color, sort order, active/inactive, default flag.

### 17.7 System Settings

| Setting | Description |
|---------|-------------|
| **Update Check** | Enable/disable automatic checking for new FoxDesk versions. |
| **Manual Update** | Upload a `.zip` update package to apply manually. |
| **Debug Logs** | View the application debug log (stored in `debug_log` table). Filter by channel, level, date range. |
| **Security Logs** | View the security event log (login attempts, impersonation events, rate limit triggers). |
| **API Tokens** | Manage API tokens for agent accounts. |
| **Backup** | The update system automatically creates backups in `backups/` before applying updates. |

---

## 18. System Update & Maintenance

### 18.1 Manual Update Process

1. Download the new version's ZIP file (e.g. `foxdesk-0.3.42.zip`).
2. Navigate to **Admin → Settings → System**.
3. In the **Manual Update** section, click **Choose File** and select the ZIP.
4. Click **Upload & Install**.
5. The system:
   - Validates the ZIP file structure (must contain `version.json` and `files/` directory).
   - Checks the version number is newer than the current version.
   - Creates a backup of all files that will be overwritten (stored in `backups/`).
   - Extracts and overwrites the updated files.
   - Runs `upgrade.php` to apply any database migrations.
   - Shows a success message and redirects.
6. After the update, verify everything works correctly.

### 18.2 Automatic Update Checks

If enabled in settings, FoxDesk periodically checks for new versions:

- A notification banner appears in admin settings when an update is available.
- You can download and install the update directly from the notification.
- You can dismiss the notification for a specific version.

### 18.3 Database Migrations

`upgrade.php` handles incremental database schema changes:

- It is idempotent — running it multiple times is safe.
- It checks which tables/columns exist before making changes.
- It adds new tables, columns, indexes, and default data as needed.
- It runs automatically during the update process and can also be run manually:

```bash
php upgrade.php
```

### 18.4 Maintenance Cron

The maintenance script handles periodic cleanup:

```bash
0 3 * * * /usr/bin/php /path/to/foxdesk/bin/run-maintenance.php >> /var/log/foxdesk-maintenance.log 2>&1
```

Tasks performed:
- Purge old debug log entries.
- Clean up expired session data.
- Remove orphaned temporary files.
- Purge expired rate limit records.

### 18.5 Backups

**Automatic Backups:**
- Created before every system update in the `backups/` directory.
- Named with a timestamp and version identifier.

**Manual Backups:**
- FoxDesk does not have a built-in full-backup feature.
- Use standard tools to back up:
  - **Database:** `mysqldump helpdesk_db > backup.sql`
  - **Files:** `tar czf foxdesk-backup.tar.gz /path/to/foxdesk/`
  - **Uploads:** Back up the `uploads/` directory separately if it contains important files.

---

## 19. Keyboard Shortcuts & Command Palette

### 19.1 Command Palette

Press **Ctrl+K** (or **Cmd+K** on Mac) anywhere in FoxDesk to open the command palette.

The command palette provides instant search across:
- **Tickets** — Search by title, ID, or description.
- **Navigation** — Jump to any page (Dashboard, Tickets, New Ticket, Settings, etc.).

Type your query and press Enter to navigate to the top result, or use arrow keys to select a specific item.

### 19.2 Keyboard Shortcuts

| Shortcut | Action | Context |
|----------|--------|---------|
| `Ctrl+K` / `Cmd+K` | Open command palette | Global |
| `Ctrl+Enter` | Submit comment | Ticket detail (comment box) |
| `N` | New ticket | Tickets list page |
| `Esc` | Close modal / dismiss notification | Global |

> **Tip:** The command palette searches tickets in real-time via the `search-tickets` API endpoint, providing instant results as you type.

---

## 20. Localization & Multi-Language Support

### Supported Languages

| Code | Language | Coverage |
|------|----------|----------|
| `en` | English | Full (reference language) |
| `cs` | Čeština (Czech) | Full |
| `de` | Deutsch (German) | Full |
| `es` | Español (Spanish) | Full |
| `it` | Italiano (Italian) | Full |

### How Localization Works

1. **System default language** is set in **Admin → Settings → General**.
2. **Per-user language** is set in each user's profile. This overrides the system default.
3. The translation function `t('key')` looks up the current user's language file.
4. If a translation is missing for the user's language, the English version is used as a fallback.

### Language Files

Language files are PHP arrays located in `includes/lang/`:

```php
// includes/lang/cs.php
return [
    'Dashboard' => 'Přehled',
    'Tickets' => 'Požadavky',
    'New ticket' => 'Nový požadavek',
    // ... hundreds more translations
];
```

### Adding a New Language

1. Copy `includes/lang/en.php` to `includes/lang/xx.php` (where `xx` is the ISO 639-1 code).
2. Translate all values in the array.
3. Register the language in `includes/translations.php`:
   ```php
   return [
       'en' => require __DIR__ . '/lang/en.php',
       'cs' => require __DIR__ . '/lang/cs.php',
       // Add your language:
       'fr' => require __DIR__ . '/lang/fr.php',
   ];
   ```
4. The language will automatically appear in the user profile language dropdown.

### Email Template Localization

Email templates are stored per-language in the database. When sending a notification, FoxDesk uses the recipient's language to select the correct template. If no template exists for that language, built-in defaults from `includes/mailer.php` are used.

---

## 21. Docker Deployment

FoxDesk includes Docker support for easy deployment and development.

### Docker Compose

The included `docker-compose.yml` defines two services:

```yaml
services:
  app:
    build: .
    ports:
      - "8888:80"
    environment:
      - DB_HOST=db
      - DB_PORT=3306
      - DB_NAME=foxdesk
      - DB_USER=foxdesk
      - DB_PASS=foxdesk123
      - APP_URL=http://localhost:8888
      - ADMIN_EMAIL=admin@foxdesk.local
      - ADMIN_PASS=Admin123!
      - ADMIN_NAME=Admin
      - ADMIN_SURNAME=User
    volumes:
      - uploads:/var/www/html/uploads
      - backups:/var/www/html/backups
    depends_on:
      db:
        condition: service_healthy

  db:
    image: mariadb:10.11
    environment:
      - MARIADB_ROOT_PASSWORD=rootpass
      - MARIADB_DATABASE=foxdesk
      - MARIADB_USER=foxdesk
      - MARIADB_PASSWORD=foxdesk123
    volumes:
      - dbdata:/var/lib/mysql
    healthcheck:
      test: ["CMD", "healthcheck.sh", "--connect", "--innodb_initialized"]
      interval: 5s
      timeout: 5s
      retries: 10

volumes:
  dbdata:
  uploads:
  backups:
```

### Quick Start with Docker

```bash
# Clone the repository
git clone https://github.com/your-repo/foxdesk.git
cd foxdesk

# Start the containers
docker compose up -d

# Access FoxDesk at http://localhost:8888
# Login: admin@foxdesk.local / Admin123!
```

### Docker Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_HOST` | Database hostname | `db` |
| `DB_PORT` | Database port | `3306` |
| `DB_NAME` | Database name | `foxdesk` |
| `DB_USER` | Database username | `foxdesk` |
| `DB_PASS` | Database password | `foxdesk123` |
| `APP_URL` | Application URL | `http://localhost:8888` |
| `ADMIN_EMAIL` | Initial admin email | `admin@foxdesk.local` |
| `ADMIN_PASS` | Initial admin password | `Admin123!` |
| `ADMIN_NAME` | Admin first name | `Admin` |
| `ADMIN_SURNAME` | Admin last name | `User` |

### Docker Setup Process

The `docker-setup.php` script automatically:

1. Generates `config.php` from environment variables.
2. Creates the database schema from `includes/schema.sql`.
3. Creates the admin user account.
4. Seeds default statuses, priorities, and ticket types.
5. Runs `upgrade.php` for any pending migrations.

### Persistent Data

Docker volumes ensure data persists across container restarts:

- `dbdata` — MariaDB data files.
- `uploads` — User-uploaded attachments.
- `backups` — Update backup files.

---

## 22. Security & Hardening

### 22.1 Built-in Security Features

FoxDesk includes several security mechanisms:

**Session Security:**

- Strict session mode (`session.use_strict_mode`).
- HTTP-only cookies (`session.cookie_httponly`).
- SameSite cookie attribute (`Lax`).
- Secure cookie flag when HTTPS is detected.
- Session regeneration on login, impersonation start/stop.

**CSRF Protection:**

- All POST forms include a CSRF token.
- `require_csrf_token()` validates the token on every form submission.

**Password Security:**

- Passwords are hashed using PHP's `password_hash()` (bcrypt).
- Password reset tokens are time-limited.

**Rate Limiting:**

- Login attempts are rate-limited per IP address.
- Tracked in the `rate_limits` table.

**Security Headers:**

- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: SAMEORIGIN`
- `X-XSS-Protection: 1; mode=block`
- `Referrer-Policy: strict-origin-when-cross-origin`

**Input Sanitization:**

- HTML output is escaped via the `e()` function (`htmlspecialchars`).
- Rich text content is sanitized via `safe_html()` (whitelisted tags only, no scripts/events).
- File uploads validate MIME types and reject dangerous extensions.

**SQL Injection Prevention:**

- All database queries use parameterized PDO prepared statements.

**Security Logging:**

- Failed login attempts, rate limit triggers, impersonation events, and other security-relevant events are logged to the `security_log` table.
- View security logs at **Admin → Settings → Security Logs**.

### 22.2 Recommended Hardening Steps

1. **Delete `install.php`** immediately after installation.
2. **Use HTTPS** — Get a free SSL certificate from Let's Encrypt.
3. **Generate a strong SECRET_KEY** — `php -r "echo bin2hex(random_bytes(32));"`
4. **Restrict file permissions:**
   - `config.php` — 644 or 640 (readable by web server, not world-readable).
   - `uploads/` — 755 (writable by web server).
   - `backups/` — 755 (writable by web server).
5. **Block directory listing** — Ensure `Options -Indexes` in `.htaccess`.
6. **Block access to sensitive directories:**
   ```apache
   <FilesMatch "(^\.ht|config\.php|schema\.sql)">
       Require all denied
   </FilesMatch>
   ```
7. **Prevent PHP execution in uploads:**
   ```apache
   # In uploads/.htaccess
   php_flag engine off
   ```
8. **Configure PHP securely:**
   ```ini
   expose_php = Off
   display_errors = Off
   log_errors = On
   upload_max_filesize = 10M
   post_max_size = 12M
   session.cookie_secure = 1
   ```
9. **Keep FoxDesk updated** — Apply security patches when new versions are released.

### 22.3 Image Proxy

FoxDesk serves uploaded images through `image.php` rather than direct file access. This:

- Validates that the requested file exists in the uploads directory.
- Prevents path traversal attacks.
- Sets correct Content-Type headers.
- Avoids exposing the uploads directory structure.

---

## 23. Troubleshooting & FAQ

### Common Issues

**Q: I see a blank page or HTTP 500 error.**

A: Check the PHP error log on your server. Common causes:
- `config.php` is missing or has wrong database credentials.
- PHP version is below 8.0.
- The `pdo_mysql` extension is not enabled.
- Database doesn't exist or user lacks permissions.

**Q: CSS is broken / page looks unstyled.**

A: Verify that `theme.css` and `tailwind.min.css` were uploaded to the web root (same directory as `index.php`). Clear your browser cache with `Ctrl+Shift+R`.

**Q: API returns 401 Unauthorized.**

A: Check that:
- The `.htaccess` file was uploaded and `mod_rewrite` is enabled.
- For Nginx, add `fastcgi_param HTTP_AUTHORIZATION $http_authorization;`.
- Your Bearer token is correct and the associated user account is active.

**Q: File uploads fail.**

A: Check:
- `upload_max_filesize` and `post_max_size` in `php.ini` (must be >= `MAX_UPLOAD_SIZE` in config.php).
- The `uploads/` directory is writable by the web server (755 or 775).
- The `fileinfo` PHP extension is enabled.

**Q: Email notifications are not being sent.**

A: Verify SMTP settings in **Admin → Settings → Email**. Click the **Test SMTP** button to diagnose:
- Wrong SMTP host or port → Connection refused.
- Wrong credentials → Authentication failed.
- Missing TLS/SSL → Connection not secure.
- Firewall blocking outbound SMTP → Contact your hosting provider.

**Q: Recurring tasks are not creating tickets.**

A: Check that:
- The cron job for `bin/process-recurring-tasks.php` is set up and running.
- The recurring task is set to **Active**.
- The `next_run_date` has passed (check the recurring tasks list).
- Test manually: `php bin/process-recurring-tasks.php`

**Q: Email-to-ticket is not working.**

A: Check that:
- `IMAP_ENABLED` is `true` in `config.php`.
- IMAP credentials are correct (test with an email client first).
- The cron job for `bin/ingest-emails.php` is set up and running.
- The sender is in the allowed senders list.
- Check `email_ingest_logs` table for error messages.

**Q: I forgot the admin password.**

A: Use the "Forgot Password" link on the login page (requires working SMTP). Alternatively, update the password directly in the database:

```sql
UPDATE users SET password = '$2y$10$...' WHERE email = 'admin@example.com';
```

Generate a bcrypt hash with: `php -r "echo password_hash('NewPassword123!', PASSWORD_DEFAULT);"`

**Q: The update notification keeps appearing after applying an update.**

A: This was a known bug fixed in v0.3.42. The `apply_update()` function now properly clears the pending update session data before exiting.

**Q: Characters look corrupted (mojibake) in Czech/German/Spanish text.**

A: Ensure your database uses `utf8mb4_unicode_ci` collation. Also ensure `config.php` does not have a BOM (Byte Order Mark) at the beginning of the file. FoxDesk forces UTF-8 output via `ini_set('default_charset', 'UTF-8')`.

---

## 24. Glossary

| Term | Definition |
|------|-----------|
| **Agent** | A support team member with elevated permissions. Can view all tickets, assign, track time, and view reports. |
| **Admin** | A user with full system access, including settings, user management, and system updates. |
| **Assignee** | The agent responsible for resolving a ticket. |
| **Billable** | Time that will be charged to the client. |
| **CSRF Token** | Cross-Site Request Forgery protection token. Included in forms to prevent unauthorized submissions. |
| **Due Date** | The deadline for resolving a ticket. |
| **Hash** | A unique 16-character identifier for each ticket, used in public share URLs. |
| **ICO** | Czech company identification number (IČO). Also used generically for company ID. |
| **IMAP Ingest** | The process of converting incoming emails into tickets automatically. |
| **Impersonation** | An admin temporarily logging in as another user to see the app from their perspective. |
| **Internal Note** | A comment visible only to agents and admins, hidden from regular users. |
| **Organization** | A client company or group that users belong to. |
| **Recurring Task** | A template that automatically creates new tickets on a schedule. |
| **Report Snapshot** | A frozen-in-time calculation of report data (KPIs, charts, time entries). |
| **Share Token** | A random string used to create public access links for tickets and reports. |
| **Slug** | A URL-friendly identifier (e.g. `in-progress` for the "In Progress" status). |
| **Source** | How a ticket was created: web, email, api, recurring, or import. |
| **Stopwatch / Timer** | The live time-tracking feature that counts elapsed time on a ticket. |
| **Tags** | Free-form labels attached to tickets for categorization and filtering. |
| **User** | An end-customer with limited permissions. Can create tickets and view their own tickets. |

---

## 25. Changelog (Recent)

### v0.3.57 (2026-03-02)

- **Fix:** Dashboard time display now shows human-readable format ("1h 15min", "42 min") instead of confusing decimal hours ("0.7h", "1.3h").
- **Removed:** "Create another" checkbox on the new ticket form (was rarely used).
- **New:** Manual time input (minutes) on the new ticket form — agents can log time spent directly when creating a ticket.
- **New:** Standalone time entries on ticket detail — agents can now log time without being required to write a comment.

### v0.3.56 (2026-03-02)

- **Fix:** Attachments and Company fields on one row in new ticket form.
- **Fix:** Tags moved next to On Behalf Of in Advanced grid.
- **Fix:** Update package structure (forward slashes in ZIP).

### v0.3.42 (2026-02-26)

- **Fix:** Update notification now properly clears after applying an update (the "Update ready to install" message no longer persists).
- **Fix:** Encoding corruption fixed across all email templates (Czech, German, Spanish, Italian) in both settings UI defaults and mailer fallbacks.
- **Fix:** Czech language file (`cs.php`) encoding corruption repaired — all Š, ň characters now display correctly.
- **Fix:** Star character (★) in user management fixed.
- **Fix:** Czech regex patterns in ticket import functions corrected.
- **New:** Image proxy (`image.php`) for secure serving of uploaded images.
- **New:** `upload_url()` helper function for consistent image URL generation.
- **New:** Attachment thumbnails with lightbox preview for image files.
- **Audit:** Comprehensive codebase audit identifying and prioritizing 30+ improvement items across security, performance, code quality, and maintainability.

### v0.3.41

- Report builder and public report sharing.
- Recurring task email notifications.
- Dashboard widget drag-and-drop reordering.
- AI Agent API endpoints.
- Email-to-ticket IMAP ingest system.

---

## 26. Potentially Overlooked Features

This section highlights features that are easy to miss or may not be immediately obvious from the standard workflow.

### 26.1 Dashboard Widget Drag-and-Drop

Many users don't realize the dashboard widgets can be rearranged by dragging. Hover over any widget card, grab it, and drop it in a new position. The layout is saved automatically per user.

### 26.2 Command Palette (Ctrl+K)

The command palette provides instant ticket search and navigation from anywhere in the app. Press `Ctrl+K` (or `Cmd+K` on Mac) to activate it. This is significantly faster than navigating through menus.

### 26.3 Internal Notes

Comments can be marked as "Internal" by toggling the checkbox. Internal notes are visible only to agents and admins — the ticket creator (customer) will never see them. This is perfect for team discussions about a ticket without exposing them to the client.

### 26.4 Ticket Hash for Public Sharing

Every ticket has a unique 16-character hash. This enables secure public sharing without exposing sequential ticket IDs. Share links use the hash, not the ID.

### 26.5 Multi-Organization User Membership

A user can belong to multiple organizations. The primary organization (marked with ★) is used as the default, but users can be associated with additional organizations. This is useful for consultants or people who work across multiple client accounts.

### 26.6 Per-User Language

Each user can independently set their language in their profile. This means you can have an admin interface in English while a Czech customer sees everything in Czech — without changing a system-wide setting.

### 26.7 Impersonation with Full Security Audit Trail

Admin user impersonation logs every start/stop event to the security log, including the admin's ID, the impersonated user's ID, and timestamps. If an impersonated user's account becomes inactive during impersonation, the system automatically restores the admin session safely.

### 26.8 Ticket Export as Markdown

From any ticket detail page, you can export the entire ticket (title, description, all comments, metadata) as a Markdown `.md` file. This is useful for archiving, offline reading, or migrating ticket data.

### 26.9 Tag Autocomplete

When typing tags on a ticket, the system suggests existing tags from across all tickets. This promotes tag consistency and prevents duplicates like "server" vs "Server" vs "servers".

### 26.10 Timer Pause/Resume

The live timer supports pause/resume, not just start/stop. Paused seconds are tracked separately, so the final time entry accurately reflects active work time only.

### 26.11 Report Builder Theme Colors

When generating client-facing reports, you can customize the theme color to match the client's brand. The report header and accents will use the chosen color.

### 26.12 Allowed Senders CLI Tool

The `bin/allowed-senders.php` script provides a command-line interface for managing the email-to-ticket whitelist. This is faster than using the admin UI for bulk additions.

### 26.13 Automatic Backup on Update

Every time you apply a system update, FoxDesk automatically creates a backup of all files that will be overwritten. Backups are stored in the `backups/` directory with a timestamped identifier. You can roll back manually if something goes wrong.

### 26.14 Debug Log Viewer

**Admin → Settings → Debug Logs** provides a built-in log viewer with filtering by channel, level, and date range. This shows application-level logs stored in the `debug_log` database table — useful for diagnosing issues without SSH access.

### 26.15 Security Log

**Admin → Settings → Security Logs** shows all security-relevant events: login attempts (successful and failed), rate limit triggers, impersonation events, and API token usage. This is a built-in audit trail.

### 26.16 Bulk Tag Operations

On the ticket list, the bulk action bar supports tag operations: you can add tags, remove specific tags, or replace all tags on multiple tickets at once. The tags mode selector (Add/Remove/Replace) controls the behavior.

### 26.17 Soft-Delete for Users

When you "delete" or "archive" a user, they are soft-deleted (the `deleted_at` column is set). Their tickets, comments, and time entries remain intact. Soft-deleted users cannot log in but can be restored.

### 26.18 Activity Timeline on Ticket Detail

The activity timeline on the ticket detail page is not just comments — it interleaves all events: status changes, priority changes, assignments, tag modifications, timer events. This gives a complete audit trail of everything that happened on a ticket.

### 26.19 Due Date Email Notifications

FoxDesk sends automatic email notifications for overdue tickets and tickets approaching their due date. These notifications are sent in the user's preferred language using localized templates.

### 26.20 Session Security on Impersonation

When an admin starts impersonation, the session ID is regenerated to prevent session fixation. When impersonation ends, the session is regenerated again and fully restored to the admin's original state.

### 26.21 Agent Connect Page

**Admin → Agent Connect** provides setup instructions and configuration for connecting external AI agents to FoxDesk. It shows the API base URL, available endpoints, and how to use the Bearer token authentication.

### 26.22 Ticket Source Filter

Each ticket tracks its source (web, email, api, recurring, import). While there is no dedicated UI filter for source in the ticket list, the information is available on each ticket's detail page and can be queried via the API.

### 26.23 Email Thread Continuity

When using IMAP ingest, FoxDesk tracks email `Message-ID` and `In-Reply-To` headers. If a customer replies to a ticket notification email, the reply is automatically appended as a comment on the original ticket rather than creating a new one.

### 26.24 Demo Seed Script

The `bin/seed-demo.php` script can populate your FoxDesk instance with sample data (tickets, users, organizations, time entries) for testing and demonstration purposes.

### 26.25 Configurable Upload Limits

Upload limits are controlled at two levels:
- **PHP level:** `upload_max_filesize` and `post_max_size` in `php.ini`.
- **Application level:** `MAX_UPLOAD_SIZE` in `config.php` (default: 10 MB).

Both must be set appropriately. The application enforces the lower of the two limits.

---

*This manual covers FoxDesk v0.3.57. For the latest updates and community support, visit the project repository.*
