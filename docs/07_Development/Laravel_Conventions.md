# Laravel Conventions

## Purpose

Define the Laravel development conventions adopted across the Nexus School platform.

---

# Laravel Version

Laravel 12.x

PHP 8.4+

Filament 4

PostgreSQL

---

# Directory Structure

app/

├── Domain/

├── DTOs/

├── Enums/

├── Events/

├── Exceptions/

├── Http/

├── Jobs/

├── Listeners/

├── Models/

├── Notifications/

├── Observers/

├── Policies/

├── Providers/

├── Services/

└── Support/

---

# Controllers

Controllers must remain thin.

Responsibilities:

- Receive request
- Validate request
- Call Service
- Return Response

Business logic must never be placed inside controllers.

---

# Validation

Always use Form Requests.

Example

StoreStudentRequest

UpdateStudentRequest

---

# Business Logic

Business rules belong inside Services.

Never inside:

- Controllers
- Models
- Filament Resources
- Livewire Components

---

# Models

Models represent data.

Avoid large models.

Prefer relationships and scopes.

---

# Services

Each module exposes dedicated services.

Examples

StudentService

FinanceService

AttendanceService

EnrollmentService

---

# Policies

Every protected resource must have a Policy.

Authorization never belongs in controllers.

---

# Events

Use Events whenever an action may trigger additional processes.

Examples

StudentCreated

InvoicePaid

SubscriptionActivated

AttendanceRecorded

---

# Queues

Long-running operations must be queued.

Examples

- Email
- Notifications
- PDF generation
- Imports
- Exports
- Backup

---

# Notifications

Always use Laravel Notifications.

Support:

- Email
- Database
- SMS
- Push Notifications

---

# Configuration

Never hardcode values.

Use:

config()

or

.env

---

# Logging

Use Laravel Log channels.

Avoid:

dd()

dump()

var_dump()

in production.

---

# Database

- ULID primary keys
- Soft Deletes where applicable
- Explicit foreign keys
- Indexed relationships

---

# Testing

Every business service should have automated tests.

---

# API

Use API Resources.

Never return Eloquent models directly.

---

# Error Handling

Use Exceptions.

Avoid returning raw error messages.

---

# Performance

Use eager loading.

Avoid N+1 queries.

Use queues for expensive tasks.

Cache frequently accessed data.

---

# Code Quality

Use:

Laravel Pint

PHPStan

Rector (optional)

PHPUnit / Pest