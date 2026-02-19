# Open Docket — Laravel Application Plan

## Overview

**Open Docket** is a personal complaint/ticket tracking application built with Laravel. Tracks complaints (RTI applications, DND complaints, consumer grievances, etc.) with due dates, reminders, comments, file attachments, tags, and full audit logging.

- **Project slug:** `open-docket`
- **Local dev URL:** `opendocket.test`

Each ticket belongs to a **ticket type** that defines a custom JSON schema for type-specific fields. This makes the system extensible — adding new complaint types requires no code changes.

---

## Tech Stack

- **Framework:** Laravel 12 (latest stable)
- **PHP:** 8.2+
- **Database:** MySQL 8 / Postgres 18
- **Queue:** database driver (switchable to SQS/Redis later)
- **Auth:** Laravel Fortify (backend only, no registration — single user seeded via seeder)
- **Frontend:** Blade + Alpine.js (lightweight interactivity for dynamic forms, dropdowns, modals, filters)
- **CSS:** Tailwind CSS 4 + DaisyUI (latest) — use DaisyUI component classes for buttons, cards, badges, modals, tabs, alerts, etc.

### Key Packages

| Package | Purpose |
|---------|---------|
| `spatie/laravel-medialibrary` | Polymorphic file attachments on Tickets, Comments, Contacts |
| `spatie/laravel-activitylog` | Audit trail / activity logging on all models |
| `sourceopen/tags` | Polymorphic tagging on Tickets (and optionally Contacts) |
| `spatie/laravel-data` | DTOs for ticket type schema validation |
| `laravel/fortify` | Auth backend (login, password reset — registration disabled) |
| `daisyui` (npm, latest) | UI component library on top of Tailwind CSS 4 |

---

## Database Schema

### `users`
Standard Laravel auth table. No modifications needed.

### `contacts`
People/organizations that complaints are filed with or relate to.

```
contacts
├── id (bigint, PK)
├── name (string)
├── designation (string, nullable) — e.g., "Public Information Officer"
├── organization (string, nullable) — e.g., "Ministry of Finance"
├── email (string, nullable)
├── phone (string, nullable)
├── address (text, nullable)
├── type (enum: authority, company, department, individual)
├── notes (text, nullable)
├── created_at, updated_at
└── deleted_at (soft deletes)
```

- Has media (spatie/laravel-medialibrary) — for storing business cards, letterheads, etc.
- Has tags (sourceopen/tags)
- Logs activity (spatie/laravel-activitylog)

### `ticket_types`
Defines categories of complaints and their custom field schemas.

```
ticket_types
├── id (bigint, PK)
├── name (string) — "RTI Application", "DND Complaint", "Consumer Complaint"
├── slug (string, unique)
├── description (text, nullable)
├── default_sla_days (integer, nullable) — auto-set due_date on ticket creation
├── schema_definition (json) — defines custom fields for this type (see below)
├── allowed_statuses (json, nullable) — restrict valid statuses for this type
├── icon (string, nullable) — icon class/name for UI
├── color (string, nullable) — hex color for UI badges
├── is_active (boolean, default true)
├── sort_order (integer, default 0)
├── created_at, updated_at
└── deleted_at (soft deletes)
```

**`schema_definition` example for RTI Application:**
```json
{
  "fields": [
    {
      "key": "pio_name",
      "label": "PIO Name",
      "type": "string",
      "required": true
    },
    {
      "key": "department",
      "label": "Department",
      "type": "string",
      "required": false
    },
    {
      "key": "fee_paid",
      "label": "Fee Paid (₹)",
      "type": "number",
      "default": 10
    },
    {
      "key": "mode_of_filing",
      "label": "Mode of Filing",
      "type": "select",
      "options": ["online_portal", "speed_post", "in_person"],
      "required": true
    },
    {
      "key": "first_appeal_deadline",
      "label": "First Appeal Deadline",
      "type": "date"
    }
  ]
}
```

**Supported field types:** `string`, `text`, `number`, `date`, `datetime`, `boolean`, `select`, `url`

### `tickets`
Core entity — each complaint/application is a ticket.

```
tickets
├── id (bigint, PK)
├── user_id (FK → users)
├── ticket_type_id (FK → ticket_types)
├── reference_number (string, unique) — auto-generated: TKT-2026-0001
├── external_reference (string, nullable) — e.g., RTI registration number, portal complaint ID
├── title (string)
├── description (text, nullable)
├── status (string, default 'draft') — see Status Workflow below
├── priority (enum: low, medium, high, critical — default: medium)
├── filed_with_contact_id (FK → contacts, nullable)
├── filed_date (date, nullable) — when complaint was actually filed
├── due_date (date, nullable) — auto-calculated from ticket_type.default_sla_days if not set
├── closed_date (date, nullable)
├── custom_fields (json, nullable) — validated against ticket_type.schema_definition
├── parent_ticket_id (FK → tickets, nullable) — for appeals, follow-ups, linked tickets
├── created_at, updated_at
└── deleted_at (soft deletes)
```

- Has media (spatie/laravel-medialibrary) — complaint documents, receipts, screenshots
- Has tags (sourceopen/tags)
- Logs activity (spatie/laravel-activitylog)
- Has many comments
- Has many reminders
- Belongs to optional parent ticket (self-referential)
- Has many child tickets

### `comments`
Updates, notes, and responses for a ticket.

```
comments
├── id (bigint, PK)
├── ticket_id (FK → tickets)
├── user_id (FK → users)
├── body (text) — supports markdown
├── type (enum: update, note, response_received, escalation, resolution)
│   ├── update — general progress update
│   ├── note — internal/private note
│   ├── response_received — reply from the authority/company
│   ├── escalation — escalation record
│   └── resolution — resolution details
├── is_internal (boolean, default false) — for future multi-user: hide from shared views
├── created_at, updated_at
└── deleted_at (soft deletes)
```

- Has media (spatie/laravel-medialibrary) — attach response letters, screenshots
- Logs activity (spatie/laravel-activitylog)

### `reminders`
Scheduled reminders tied to tickets.

```
reminders
├── id (bigint, PK)
├── ticket_id (FK → tickets)
├── user_id (FK → users)
├── title (string) — short label: "RTI deadline approaching"
├── remind_at (datetime)
├── type (enum: deadline_approaching, follow_up, custom)
├── notes (text, nullable)
├── is_sent (boolean, default false)
├── sent_at (datetime, nullable)
├── is_recurring (boolean, default false)
├── recurrence_rule (string, nullable) — simplified: "every_7_days", "every_30_days", "every_weekday"
├── recurrence_ends_at (datetime, nullable)
├── created_at, updated_at
└── deleted_at (soft deletes)
```

- Logs activity (spatie/laravel-activitylog)

---

## Status Workflow

Default statuses (can be overridden per ticket_type via `allowed_statuses`):

```
draft → submitted → acknowledged → in_progress → resolved → closed
                                  ↘ escalated → resolved
                  closed → reopened → in_progress
```

| Status | Description |
|--------|-------------|
| `draft` | Ticket created but not yet filed |
| `submitted` | Complaint filed with authority/company |
| `acknowledged` | Acknowledgement received |
| `in_progress` | Being actively worked on / awaiting response |
| `escalated` | Escalated to higher authority (first appeal, senior officer, etc.) |
| `resolved` | Resolution received |
| `closed` | Ticket closed (satisfied or no further action) |
| `reopened` | Previously closed, reopened for follow-up |

Store status as a string (not enum) so new statuses can be added without migration.

**Valid transitions** should be enforced in a `TicketStateMachine` service class.

---

## Reference Number Generation

Auto-generate on ticket creation: `TKT-{YEAR}-{SEQUENTIAL_PADDED}`

Example: `TKT-2026-0001`, `TKT-2026-0002`

Use a `settings` table or config to track the last sequence number per year. Alternatively, derive from the ticket ID with year prefix.

---

## Key Features & Implementation Notes

### 1. Dashboard
- Summary cards: Open tickets, Overdue tickets, Due this week, Recently closed
- Overdue tickets list (sorted by most overdue first)
- Upcoming reminders (next 7 days)
- Recent activity feed (from spatie activity log)

### 2. Ticket CRUD
- Create: Select ticket type → form renders standard fields + dynamic custom fields from `schema_definition`
- Dynamic custom fields rendered via an Alpine.js component (`x-data`) that reads the JSON schema and builds form inputs
- When ticket type is changed in the dropdown, fetch the new type's schema via an AJAX call to a `TicketTypeController@schema` endpoint and re-render custom fields
- Auto-set `due_date` = `filed_date` + `ticket_type.default_sla_days` if due_date not manually set
- Auto-generate `reference_number`
- List view with filters: status, priority, ticket type, tags, overdue, date range — all as GET query params, filtered server-side
- Detail view: ticket info, timeline of comments + activity log merged chronologically

### 3. Comments
- Threaded under ticket detail page
- Markdown support (render with a simple markdown parser)
- File attachments per comment via spatie media library
- Comment type selector (update, note, response_received, escalation, resolution)
- Auto-log status changes when adding escalation/resolution comments

### 4. Reminders
- CRUD under each ticket
- Recurring reminders support
- Scheduled command: `php artisan reminders:send` — runs every 15 minutes via cron
- Notification channels: database notification (in-app) + email (via Laravel notifications)
- Auto-create a "deadline approaching" reminder when due_date is set (3 days before, 1 day before, on due date)
- Dashboard widget for upcoming reminders

### 5. File Attachments (spatie/laravel-medialibrary)
- Configure media collections:
    - `tickets`: collection "documents" — complaint docs, receipts, screenshots
    - `comments`: collection "attachments" — response letters, evidence
    - `contacts`: collection "documents" — business cards, reference docs
- Use conversions for image thumbnails
- Store on local disk (configurable to S3 later)

### 6. Tags (sourceopen/tags)
- Apply tags to tickets and contacts
- Use for categorization: "tax", "telecom", "banking", "government", "urgent"
- Filter tickets by tags on list view
- Tag management page (CRUD)

### 7. Activity Log (spatie/laravel-activitylog)
- Enable on: Ticket, Comment, Reminder, Contact, TicketType
- Log all attribute changes
- Display as a timeline on ticket detail page, merged with comments
- Dashboard: recent activity feed

### 8. Contacts
- CRUD for contacts
- Link contacts to tickets (filed_with_contact_id)
- Contact detail page shows all related tickets
- Reusable across tickets

### 9. Linked/Child Tickets
- A ticket can have a `parent_ticket_id`
- Use case: RTI → First Appeal → Second Appeal (CIC)
- Show linked tickets on parent ticket detail page
- "Create follow-up" button on ticket detail

### 10. Ticket Types Management
- Admin CRUD for ticket types
- JSON schema builder for custom fields (simple form: add field name, type, required, options)
- Seed common types: RTI Application, DND Complaint, Consumer Complaint, Insurance Complaint, Banking Ombudsman, General

---

## Artisan Commands

| Command | Schedule | Purpose |
|---------|----------|---------|
| `reminders:send` | Every 15 min | Check and send due reminders via notifications |
| `tickets:check-overdue` | Daily 9 AM | Mark overdue tickets, create notifications |
| `tickets:auto-remind` | Daily 9 AM | Auto-create deadline reminders for tickets approaching due date |

---

## Routes Structure

```
GET    /dashboard                     — Dashboard
GET    /tickets                       — Ticket list (filterable)
GET    /tickets/create                — Create ticket (step 1: select type)
GET    /tickets/create/{ticketType}   — Create ticket (step 2: fill form)
POST   /tickets                       — Store ticket
GET    /tickets/{ticket}              — Ticket detail (with comments + activity)
GET    /tickets/{ticket}/edit         — Edit ticket
PUT    /tickets/{ticket}              — Update ticket
DELETE /tickets/{ticket}              — Soft delete ticket
POST   /tickets/{ticket}/comments     — Add comment
DELETE /tickets/{ticket}/comments/{comment} — Delete comment
POST   /tickets/{ticket}/reminders    — Add reminder
PUT    /tickets/{ticket}/reminders/{reminder} — Update reminder
DELETE /tickets/{ticket}/reminders/{reminder} — Delete reminder
POST   /tickets/{ticket}/follow-up    — Create child/follow-up ticket

GET    /contacts                      — Contact list
GET    /contacts/create               — Create contact
POST   /contacts                      — Store contact
GET    /contacts/{contact}            — Contact detail (with related tickets)
GET    /contacts/{contact}/edit       — Edit contact
PUT    /contacts/{contact}            — Update contact
DELETE /contacts/{contact}            — Soft delete contact

GET    /ticket-types                  — Ticket type list
GET    /ticket-types/create           — Create ticket type
POST   /ticket-types                  — Store ticket type
GET    /ticket-types/{ticketType}/edit — Edit ticket type
GET    /ticket-types/{ticketType}/schema — JSON endpoint: returns schema_definition for Alpine.js dynamic fields
PUT    /ticket-types/{ticketType}     — Update ticket type
DELETE /ticket-types/{ticketType}     — Soft delete ticket type

GET    /tags                          — Tag management
GET    /activity                      — Global activity log
```

---

## Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── TicketController.php
│   │   ├── CommentController.php
│   │   ├── ReminderController.php
│   │   ├── ContactController.php
│   │   ├── TicketTypeController.php
│   │   └── TagController.php
│   └── Requests/
│       ├── StoreTicketRequest.php
│       ├── UpdateTicketRequest.php
│       ├── StoreCommentRequest.php
│       ├── StoreReminderRequest.php
│       ├── StoreContactRequest.php
│       └── StoreTicketTypeRequest.php
├── Models/
│   ├── User.php
│   ├── Ticket.php
│   ├── TicketType.php
│   ├── Comment.php
│   ├── Reminder.php
│   └── Contact.php
├── Services/
│   ├── TicketStateMachine.php      — Validates status transitions
│   ├── ReferenceNumberGenerator.php — Generates TKT-YYYY-NNNN
│   ├── CustomFieldValidator.php    — Validates custom_fields against schema_definition
│   └── ReminderService.php         — Handles reminder logic, auto-creation
├── Notifications/
│   ├── ReminderDueNotification.php
│   ├── TicketOverdueNotification.php
│   └── TicketStatusChangedNotification.php
├── Console/
│   └── Commands/
│       ├── SendReminders.php
│       ├── CheckOverdueTickets.php
│       └── AutoCreateReminders.php
├── Observers/
│   └── TicketObserver.php          — Auto-set due_date, reference_number, auto-reminders
└── Enums/
    ├── TicketStatus.php            — Backed string enum
    ├── TicketPriority.php          — Backed string enum
    ├── CommentType.php             — Backed string enum
    ├── ContactType.php             — Backed string enum
    └── ReminderType.php            — Backed string enum
```

---

## Seeders

Seed the following ticket types on `db:seed`:

### RTI Application
- SLA: 30 days
- Custom fields: pio_name (string, required), department (string), fee_paid (number, default 10), mode_of_filing (select: online_portal/speed_post/in_person), first_appeal_deadline (date)

### DND Complaint (TRAI)
- SLA: 7 days
- Custom fields: operator_name (string, required), complaint_number (string), phone_number (string, required), type_of_violation (select: promotional_call/promotional_sms/spam)

### Consumer Complaint
- SLA: 45 days
- Custom fields: company_name (string, required), product_or_service (string), amount_involved (number), complaint_forum (select: district/state/national)

### Banking Ombudsman
- SLA: 30 days
- Custom fields: bank_name (string, required), account_type (select: savings/current/loan/credit_card), complaint_category (string), amount_disputed (number)

### Insurance Complaint
- SLA: 15 days
- Custom fields: insurer_name (string, required), policy_number (string), claim_number (string), claim_amount (number)

### General Complaint
- SLA: null
- Custom fields: none (empty schema)

---

## Implementation Order

Follow this order for incremental, testable development:

### Phase 1 — Foundation
1. Fresh Laravel 12 install
2. Install and configure npm packages: Tailwind CSS 4, DaisyUI (latest), Alpine.js
3. Install and configure composer packages: laravel/fortify (disable registration, enable login + password reset only), spatie/laravel-medialibrary, spatie/laravel-activitylog, sourceopen/tags
4. Configure Fortify: publish config, create login Blade view (`resources/views/auth/login.blade.php`) using DaisyUI form components, set `features` array in `config/fortify.php` to only enable login and password reset (no registration)
5. Create a base Blade layout (`resources/views/layouts/app.blade.php`) with DaisyUI navbar, sidebar, and content area — use DaisyUI theme (default or a chosen theme from daisyUI themes)
6. Create all migrations in order: contacts, ticket_types, tickets, comments, reminders
7. Create all Eloquent models with relationships, casts, traits (HasMedia, LogsActivity, HasTags)
8. Create Enums
9. Run migrations and seeders (ticket types + a single admin user with known credentials)

### Phase 2 — Core CRUD
10. TicketType CRUD (admin pages) — needed first so tickets can reference types. Use DaisyUI table, form, and card components.
11. Contact CRUD
12. Ticket CRUD with custom fields — use an Alpine.js component (`x-data`) that reads the ticket type's `schema_definition` JSON (passed via Blade as a JS variable) and dynamically renders form fields. On ticket type change (select dropdown), fetch schema via a simple controller endpoint returning JSON and re-render fields.
13. Reference number generation (TicketObserver)
14. Status workflow (TicketStateMachine service) — status change dropdown using Alpine.js + DaisyUI dropdown
15. Comment CRUD — standard Blade form on ticket detail page, comments listed below. Use Alpine.js for confirm-delete modals (DaisyUI modal component).

### Phase 3 — Reminders & Notifications
16. Reminder CRUD — Blade form on ticket detail page, list with Alpine.js for inline edit/delete
17. Auto-reminder creation on due_date set (TicketObserver)
18. `reminders:send` artisan command
19. `tickets:check-overdue` artisan command
20. Notification classes (database + email)
21. Schedule commands in `routes/console.php`

### Phase 4 — Attachments, Tags, Activity
22. Configure spatie media library collections on Ticket, Comment, Contact models
23. File upload UI on ticket create/edit, comment form, contact form — use Alpine.js for drag-drop preview and multi-file handling
24. Tag management page
25. Tag assignment on ticket create/edit — use Alpine.js powered tag input (comma-separated or DaisyUI badge-style pills)
26. Activity log configuration and display
27. Merged timeline on ticket detail: comments + activity log entries fetched in controller, merged and sorted by created_at, rendered in a Blade partial using DaisyUI timeline/chat bubble components

### Phase 5 — Dashboard & Polish
28. Dashboard with summary stats (DaisyUI stat cards), overdue list, upcoming reminders, recent activity
29. Ticket list filters — Alpine.js component with dropdowns for status, priority, type, tags, date range, overdue toggle. Submits as GET query params for server-side filtering.
30. Linked/child tickets UI
31. Search (full-text search on ticket title, description, reference_number, external_reference)
32. UI polish, responsive design — leverage DaisyUI responsive utilities and drawer component for mobile sidebar
33. Tests: Feature tests for ticket lifecycle, reminder sending, status transitions

---

## Configuration Notes

### spatie/laravel-medialibrary
```php
// In Ticket model:
public function registerMediaCollections(): void
{
    $this->addMediaCollection('documents');
}

// In Comment model:
public function registerMediaCollections(): void
{
    $this->addMediaCollection('attachments');
}
```

### spatie/laravel-activitylog
```php
// In each model:
use LogsActivity;

public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logAll()
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs();
}
```

### sourceopen/tags
Follow package documentation for setup. Apply the `HasTags` trait to `Ticket` and `Contact` models.

### Laravel Fortify Configuration
```php
// config/fortify.php
'features' => [
    Features::updatePasswords(),
    Features::resetPasswords(),
    // Registration is NOT listed — disabled
],

// FortifyServiceProvider — register login view:
Fortify::loginView(fn () => view('auth.login'));
Fortify::resetPasswordView(fn ($request) => view('auth.reset-password', ['request' => $request]));
```

Create a user via seeder only — no registration route:
```php
// database/seeders/UserSeeder.php
User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
]);
```

### Alpine.js Custom Fields Rendering
The ticket create/edit form should include an Alpine.js component for dynamic custom fields:
1. Blade passes the ticket type's `schema_definition` JSON as a JavaScript variable
2. Alpine's `x-data` initializes with this schema and any existing `custom_fields` values (for edit)
3. Uses `x-for` to loop over schema fields and `x-if`/`x-show` to render the correct input type (text, number, select, date, etc.)
4. On ticket type dropdown change, use `fetch()` to call `GET /ticket-types/{id}/schema` which returns the JSON schema, then Alpine re-renders the fields
5. All custom field values are submitted as `custom_fields[key]` and collected server-side into a JSON column
6. Validation happens server-side in `StoreTicketRequest` using the `CustomFieldValidator` service

### DaisyUI Theming
- Use a DaisyUI theme (e.g., `corporate`, `light`, or `nord`) configured in `tailwind.config.js` (or CSS `@plugin` for Tailwind v4)
- Key DaisyUI components to use throughout:
    - `btn`, `btn-primary`, `btn-ghost` — all buttons
    - `card`, `card-body` — content containers
    - `badge` — status and priority indicators
    - `table` — data tables
    - `modal` — confirm dialogs (triggered via Alpine `x-show`)
    - `tabs` — ticket detail page sections
    - `timeline` — activity/comment timeline
    - `stat` — dashboard summary cards
    - `alert` — flash messages
    - `dropdown` — status changers, action menus
    - `drawer` — responsive mobile sidebar navigation
    - `form-control`, `input`, `select`, `textarea` — all form fields

---

## Environment & Deployment Notes

- Use `php artisan schedule:work` for local development
- Queue driver: `database` for simplicity (configure `QUEUE_CONNECTION=database`)
- Run `php artisan queue:work` for processing reminder notifications
- Mail: configure Mailtrap or similar for development, real SMTP for production
- Storage: local disk, symlink with `php artisan storage:link`
