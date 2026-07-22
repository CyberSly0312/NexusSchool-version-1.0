# NexusSchool ERP — Architecture Audit & Redesign Report

**Date:** 2026-07-20
**Author:** Lead Software Architect
**Status:** Architecture Proposal — No Code Changes Yet

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Current State Analysis](#2-current-state-analysis)
3. [Critical Problems Identified](#3-critical-problems-identified)
4. [New Architecture Design](#4-new-architecture-design)
5. [New ER Diagram](#5-new-er-diagram)
6. [New Database Architecture](#6-new-database-architecture)
7. [New Models Architecture](#7-new-models-architecture)
8. [Relationship Diagram](#8-relationship-diagram)
9. [Migration Dependency Order](#9-migration-dependency-order)
10. [Complete Impact Inventory](#10-complete-impact-inventory)
11. [Risk Assessment](#11-risk-assessment)
12. [Migration Strategy](#12-migration-strategy)
13. [Backward Compatibility](#13-backward-compatibility)
14. [Refactoring Roadmap](#14-refactoring-roadmap)

---

## 1. Executive Summary

### The Core Flaw

The current architecture incorrectly equates **School == Tenant**. In stancl/tenancy, a Tenant gets its own isolated database. The School model (`app/Domains/School/Models/School.php`) extends `Stancl\Tenancy\Database\Models\Tenant`, meaning **every school is treated as a separate tenant**.

This is architecturally wrong because:

- A **customer** (e.g., *Groupe Scolaire Horizon*) is the tenant, not a single school
- A customer may operate a **headquarters**, **multiple branches**, and **campuses across cities**
- All of these belong to the **same customer** under **one subscription** and **one database**
- Students, teachers, and staff should be shared across the organization's schools

### What Must Change

| Current | New |
|---------|-----|
| `School` extends `Tenant` | `Organization` extends `Tenant` |
| School IS the tenant | Organization IS the tenant |
| One database per school | One database per organization |
| `school_id` on every table | `organization_id` (implicit) + `school_id` (explicit) |
| No multi-school support | Full multi-campus architecture |
| Single-user-to-school mapping | Multi-school user assignments |

---

## 2. Current State Analysis

### 2.1 Database Tables

There are **two overlapping tables** for the same concept:

| Table | PK Type | Purpose |
|-------|---------|---------|
| `tenants` | `string('id')->primary()` | Stancl tenancy table (2019 migration) |
| `schools` | `id()` (auto-increment) + `ulid('uuid')` | Separate schools table (2026 migration) |

**Critical Issue:** The `School` model extends `Tenant` but does **not** override `$table`. By stancl convention, it maps to the `tenants` table. The `schools` table is a **redundant, orphaned table** that is never used by the tenancy system. All 221 foreign key references across migrations point to `tenants.id`, not `schools.id`.

### 2.2 Migration Inventory (91 migrations)

| Category | Count | Tables |
|----------|-------|--------|
| Stancl core | 2 | `tenants`, `domains` |
| School (orphaned) | 1 | `schools` |
| Core academic | 13 | `users`, `permissions`, `academic_years`, `academic_terms`, `departments`, `programs`, `grade_levels`, `sections`, `academic_classes`, `classrooms`, `subject_categories`, `subjects`, `subject_coefficients` |
| Students & enrollment | 3 | `guardians`, `students`, `student_enrollments` |
| Assessment | 3 | `timetable_entries`, `assessments`, `assessment_results` |
| Library | 9 | `authors`, `publishers`, `book_categories`, `books`, `book_copies`, `book_loans`, `reservations`, `fines`, `book_author` |
| Transport | 8 | `vehicles`, `drivers`, `routes`, `route_stops`, `trips`, `student_transport_assignments`, `vehicle_maintenances`, `fuel_logs` |
| Communication | 7 | `notifications`, `notification_deliveries`, `notification_templates`, `announcements`, `messages`, `message_recipients`, `communication_channels` |
| Analytics | 5 | `dashboards`, `dashboard_widgets`, `reports`, `kpis`, `analytics_snapshots` |
| API | 5 | `personal_access_tokens`, `api_clients`, `api_tokens`, `webhooks`, `webhook_deliveries`, `api_audit_logs` |
| AI | 6 | `ai_providers`, `ai_conversations`, `ai_messages`, `ai_knowledge_sources`, `ai_prompt_templates`, `ai_usage_logs` |
| Workflow | 7 | `workflows`, `workflow_versions`, `workflow_triggers`, `workflow_executions`, `workflow_tasks`, `workflow_schedules`, `automation_rules` |
| System | 7 | `system_settings`, `audit_logs`, `activity_logs`, `scheduled_tasks`, `backups`, `system_health`, `maintenance_windows`, `notification_channels` |
| Alter migrations | 6 | `add_classroom_id`, `add_school_id_to_grade_levels`, `add_school_id_to_sections`, `add_school_id_to_academic_classes`, `fix_users_school_id_fk`, `fix_subject_categories_display_order` |
| Attendance | 1 | `attendance_records` |
| Plugins | 2 | `plugins`, `plugin_settings` |

### 2.3 Model Inventory (74 models)

- **73 models** in `app/Models/`
- **1 model** in `app/Domains/School/Models/` (School = Tenant)
- **29 bounded contexts** under `app/Domains/`
- **57 Filament Resources**
- **221 `school_id` references** across migrations

### 2.4 ID Generation Patterns (3 inconsistent patterns)

| Pattern | Used By |
|---------|---------|
| `string('id')->primary()` | Tenants/Schools (stancl) |
| `uuid('id')->primary()` | Transport, Communication, AI models |
| `id()` (auto-increment) + `ulid('uuid')` | All other domain models |

### 2.5 School Scoping Patterns (3 inconsistent patterns)

| Pattern | Models |
|---------|--------|
| `belongsTo(School::class)` | User, Student, AcademicClass, Department, GradeLevel, Section, Classroom, Guardian, Subject, AcademicYear, Program, Assessment, AssessmentResult, Book, SystemSetting, ApiClient, Webhook, Workflow |
| `belongsTo(Tenant::class, 'school_id')` | Route, Vehicle, Trip, Notification, Announcement, Message |
| No relationship defined | AcademicTerm, BookCopy, BookLoan, AuditLog |

---

## 3. Critical Problems Identified

### Problem 1: School == Tenant (Architectural)

**Severity: CRITICAL**

The fundamental design error. Every "school" gets its own isolated database. A school group with 5 branches gets 5 separate databases with no shared data. A teacher who moves between branches needs a separate account in each database.

**Impact:** Impossible to implement the required multi-campus architecture without a full redesign.

### Problem 2: No Organization Concept

**Severity: CRITICAL**

There is no `Organization` model anywhere in the codebase. The concept of a "customer" that owns multiple schools does not exist. All business logic assumes one school = one entity.

### Problem 3: Dual Table Problem (tenants + schools)

**Severity: HIGH**

Two tables exist for what should be one concept: `tenants` (stancl, string PK) and `schools` (auto-increment, ULID). The `schools` table is never referenced by foreign keys. The `School` model extends `Tenant` and maps to `tenants` table, making the `schools` table an orphaned artifact.

### Problem 4: No Automatic Data Scoping

**Severity: HIGH**

Services rely on callers to pass `school_id` for filtering. There is:
- No global query scope
- No middleware that auto-scopes queries
- No Filament resource hook that injects school_id

**Result:** Without manual scoping, queries return data across ALL schools in the tenant database. This is a **data leakage vulnerability**.

### Problem 5: Filament Resources Ignore school_id

**Severity: HIGH**

Only `UserResource` includes `school_id` in forms and filters. All other 56 Filament resources have **no school reference** in their forms, table columns, or filters. This means:
- Forms don't set `school_id` on create
- Tables don't filter by school
- Cross-school data is visible to all users

### Problem 6: Missing Foreign Keys and Relationships

**Severity: MEDIUM**

| Table | Issue |
|-------|-------|
| `student_enrollments` | `school_id` column exists but **no FK constraint** |
| `academic_terms` | `school_id` in fillable but **no `school()` relationship** |
| `book_copies` | `school_id` in fillable but **no `school()` relationship** |
| `book_loans` | `school_id` in fillable but **no `school()` relationship** |
| `audit_logs` | `school_id` in fillable but **no `school()` relationship** |
| `system_settings` | FK is nullable with **no constraint** |

### Problem 7: SubjectService Display Order Bug

**Severity: MEDIUM**

`SubjectService.validateUniqueDisplayOrder()` checks uniqueness **globally** across all schools, not per-school. Two schools cannot have subjects with the same display_order, which is incorrect.

### Problem 8: No Multi-School Permission Model

**Severity: MEDIUM**

- Spatie Permission `teams` is disabled
- No mechanism for a user to access multiple schools
- Policies use `$user->school_id === $model->school_id` which only works for single-school users
- A `super_admin` bypass exists but doesn't solve multi-campus staff access

### Problem 9: Empty Tenant Migrations Directory

**Severity: HIGH**

`database/migrations/tenant/` exists but is **empty**. All 91 migrations are in the central `database/migrations/` directory. With stancl tenancy configured for database isolation, tenant-scoped tables (students, classes, etc.) should be in the `tenant/` directory. This means either:
- Database isolation is not actually working (everything in one database), OR
- The tenant database creation process is broken

### Problem 10: Inconsistent Relationship Targets

**Severity: LOW**

Models reference the school relationship using three different patterns:
- `School::class` (19 models)
- `Tenant::class` with explicit FK (6 models)
- No relationship at all (4 models)

While functionally equivalent (School extends Tenant), this violates DRY and makes refactoring fragile.

---

## 4. New Architecture Design

### 4.1 Core Principle

```
Organization (Tenant) = The Customer
    └── Schools = Physical Campuses (business entities inside the tenant)
```

One Organization = One subscription = One tenant database = One or more Schools.

### 4.2 New Entity Hierarchy

```
CENTRAL DATABASE
└── organizations (Tenant)

TENANT DATABASE (per organization)
├── schools (Physical campuses)
│   └── school_settings
├── academic_years (Org-wide)
│   └── academic_terms
├── programs (Org-wide, optional school override)
├── departments (Per school)
├── grade_levels (Per school)
│   └── sections
├── classrooms (Per school)
├── subjects (Per school)
│   ├── subject_categories
│   └── subject_coefficients
├── academic_classes (Per school + year)
├── users (Org-level or school-level)
│   └── user_school (Multi-campus pivot)
├── students (Owned by org, assigned to school)
├── guardians (Per school)
├── student_enrollments (Per school + year)
├── timetable_entries (Per school)
├── attendance_records (Per school)
├── assessments (Per school)
│   └── assessment_results
├── Library domain (Per school)
│   ├── authors, publishers, book_categories
│   ├── books, book_copies
│   ├── book_loans, reservations, fines
├── Transport domain (Per school)
│   ├── vehicles, drivers
│   ├── routes, route_stops
│   ├── trips, student_transport_assignments
│   ├── vehicle_maintenances, fuel_logs
├── Communication domain
│   ├── notifications, notification_deliveries, notification_templates
│   ├── announcements (org-wide or per school)
│   ├── messages, message_recipients
│   ├── communication_channels
├── Analytics domain (Org-wide)
│   ├── dashboards, dashboard_widgets
│   ├── reports, kpis, analytics_snapshots
├── API domain (Org-wide)
│   ├── api_clients, api_tokens
│   ├── webhooks, webhook_deliveries
│   ├── api_audit_logs
├── AI domain (Org-wide)
│   ├── ai_providers, ai_conversations, ai_messages
│   ├── ai_knowledge_sources, ai_prompt_templates
│   ├── ai_usage_logs
├── Workflow domain (Org-wide)
│   ├── workflows, workflow_versions, workflow_triggers
│   ├── workflow_executions, workflow_tasks, workflow_schedules
│   ├── automation_rules
├── System domain (Org-wide)
│   ├── system_settings, audit_logs, activity_logs
│   ├── scheduled_tasks, backups, system_health
│   ├── maintenance_windows, notification_channels
└── Plugin domain (Org-wide)
    ├── plugins, plugin_settings
```

### 4.3 School Scope Classification

Each table gets classified by its scope:

| Scope | school_id | Description |
|-------|-----------|-------------|
| **Organization** | `NULL` (not present) | Data shared across all schools (academic years, programs, settings, API, AI, Workflow) |
| **School** | `NOT NULL` | Data specific to one campus (classes, students, library, transport) |
| **Hybrid** | `NULLABLE` | Data that can be org-wide or school-specific (announcements, notifications, departments) |

### 4.4 User-School Assignment Model

```
User belongs to Organization (always)
User belongs to 0 or more Schools (via school_id + user_school pivot)
```

| User Type | school_id | user_school entries | Access |
|-----------|-----------|---------------------|--------|
| Super Admin | NULL | None | All schools in org |
| Head Office Staff | NULL | None | All schools in org |
| Branch Principal | school_id = X | Optional additional | One school (or more) |
| Teacher | school_id = X | Optional additional | One school (or more) |
| Parent | NULL | None | Their children's schools only |

### 4.5 Student School Assignment

Students belong to **one school** at a time. Transfers between schools create new enrollment records.

```
Student
├── organization_id (implicit from tenant)
├── school_id (current school)
└── student_enrollments[]
    └── Each enrollment has: school_id, academic_year_id, academic_class_id
```

---

## 5. New ER Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                     CENTRAL DATABASE                            │
│                                                                 │
│  ┌──────────────────────┐      ┌──────────────────────────┐    │
│  │     organizations    │      │    organization_domains   │    │
│  │──────────────────────│      │──────────────────────────│    │
│  │ id (string PK)       │──┐   │ id                       │    │
│  │ uuid (ULID)          │  │   │ organization_id ─────────│──┐ │
│  │ name                 │  │   │ domain                   │  │ │
│  │ slug (unique)        │  │   │ created_at / updated_at  │  │ │
│  │ database (unique)    │  │   └──────────────────────────┘  │ │
│  │ email                │  │                                  │ │
│  │ phone                │  │                                  │ │
│  │ address, city, state │  │                                  │ │
│  │ country, postal_code │  │                                  │ │
│  │ timezone             │  └──────────────────────────────────┘ │
│  │ currency, locale     │                                       │
│  │ logo_url             │                                       │
│  │ registration_number  │                                       │
│  │ subscription_plan    │                                       │
│  │ subscription_status  │                                       │
│  │ subscription_expires │                                       │
│  │ max_schools          │                                       │
│  │ max_users            │                                       │
│  │ max_students          │                                       │
│  │ settings (JSON)      │                                       │
│  │ metadata (JSON)      │                                       │
│  │ status               │                                       │
│  │ created_at/updated_at│                                       │
│  │ deleted_at           │                                       │
│  └──────────────────────┘                                       │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘

                          │ (tenant isolation)
                          ▼

┌─────────────────────────────────────────────────────────────────┐
│                    TENANT DATABASE                               │
│                   (per organization)                              │
│                                                                  │
│  ╔══════════════════════════════════════════════════════════╗   │
│  ║  SCHOOL HIERARCHY                                       ║   │
│  ║                                                         ║   │
│  ║  ┌──────────────────────┐                               ║   │
│  ║  │      schools         │                               ║   │
│  ║  │──────────────────────│                               ║   │
│  ║  │ id (ULID PK)        │◄──┐                            ║   │
│  ║  │ name                 │   │  self-referencing          ║   │
│  ║  │ code (unique/org)    │   │  (parent_school_id)       ║   │
│  ║  │ type (enum)          │   │                            ║   │
│  ║  │ is_head_office       │───┘                            ║   │
│  ║  │ parent_school_id ────│──┐                             ║   │
│  ║  │ address              │  │                             ║   │
│  ║  │ city, country        │  │                             ║   │
│  ║  │ phone, email         │  │                             ║   │
│  ║  │ status               │  │                             ║   │
│  ║  │ settings (JSON)      │  │                             ║   │
│  ║  │ created_at/updated_at│  │                             ║   │
│  ║  │ deleted_at           │  │                             ║   │
│  ║  └──────────────────────┘  │                             ║   │
│  ╚══════════════════════════════════════════════════════════╝   │
│                                                                  │
│  ╔══════════════════════════════════════════════════════════╗   │
│  ║  ORGANIZATION-SCOPED ENTITIES                            ║   │
│  ║  (school_id = NULL / not present)                        ║   │
│  ║                                                         ║   │
│  ║  academic_years ──┬── academic_terms                    ║   │
│  ║  programs         │                                     ║   │
│  ║  system_settings  │                                     ║   │
│  ║  audit_logs       │                                     ║   │
│  ║  activity_logs    │                                     ║   │
│  ║  backups          │                                     ║   │
│  ║  scheduled_tasks  │                                     ║   │
│  ║  system_health    │                                     ║   │
│  ║  plugins ── plugin_settings                              ║   │
│  ║  ai_providers ── ai_conversations ── ai_messages         ║   │
│  ║  ai_knowledge_sources, ai_prompt_templates               ║   │
│  ║  ai_usage_logs                                           ║   │
│  ║  api_clients ── api_tokens ── api_audit_logs             ║   │
│  ║  webhooks ── webhook_deliveries                          ║   │
│  ║  workflows ── workflow_versions/triggers/executions      ║   │
│  ║  workflow_tasks, workflow_schedules, automation_rules    ║   │
│  ║  dashboards ── dashboard_widgets                         ║   │
│  ║  reports, kpis, analytics_snapshots                      ║   │
│  ╚══════════════════════════════════════════════════════════╝   │
│                                                                  │
│  ╔══════════════════════════════════════════════════════════╗   │
│  ║  SCHOOL-SCOPED ENTITIES                                  ║   │
│  ║  (school_id = NOT NULL, FK → schools.id)                 ║   │
│  ║                                                         ║   │
│  ║  departments ─────────── grade_levels ── sections        ║   │
│  ║  classrooms              subject_categories              ║   │
│  ║  subjects ──────────── subject_coefficients              ║   │
│  ║  academic_classes (links year + program + grade + section)║   │
│  ║  students ── guardians                                   ║   │
│  ║  student_enrollments                                     ║   │
│  ║  timetable_entries                                       ║   │
│  ║  attendance_records                                      ║   │
│  ║  assessments ── assessment_results                       ║   │
│  ║                                                         ║   │
│  ║  LIBRARY (per school):                                   ║   │
│  ║  authors, publishers, book_categories                    ║   │
│  ║  books ── book_copies ── book_loans                      ║   │
│  ║  reservations, fines, book_author (pivot)                ║   │
│  ║                                                         ║   │
│  ║  TRANSPORT (per school):                                 ║   │
│  ║  vehicles, drivers                                       ║   │
│  ║  routes ── route_stops ── trips                          ║   │
│  ║  student_transport_assignments                           ║   │
│  ║  vehicle_maintenances, fuel_logs                         ║   │
│  ╚══════════════════════════════════════════════════════════╝   │
│                                                                  │
│  ╔══════════════════════════════════════════════════════════╗   │
│  ║  USER & AUTH (hybrid scoped)                             ║   │
│  ║                                                         ║   │
│  ║  users (school_id nullable)                              ║   │
│  ║  user_school (pivot: multi-campus assignments)           ║   │
│  ║  permission_tables (spatie: roles, permissions, etc.)    ║   │
│  ║  personal_access_tokens                                  ║   │
│  ║                                                         ║   │
│  ║  COMMUNICATION (hybrid scoped):                          ║   │
│  ║  notifications, notification_deliveries                  ║   │
│  ║  notification_templates, notification_channels           ║   │
│  ║  announcements (school_id nullable)                      ║   │
│  ║  messages, message_recipients, communication_channels    ║   │
│  ╚══════════════════════════════════════════════════════════╝   │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 6. New Database Architecture

### 6.1 Central Database Tables

#### `organizations` (replaces `tenants` as the Stancl Tenant table)

```sql
CREATE TABLE organizations (
    id VARCHAR(255) PRIMARY KEY,           -- Stancl string PK
    uuid CHAR(26) UNIQUE NOT NULL,         -- ULID
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    database VARCHAR(255) UNIQUE NOT NULL, -- Stancl DB name
    domain VARCHAR(255) UNIQUE NULL,       -- Primary domain

    -- Contact
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    address TEXT NULL,
    city VARCHAR(100) NULL,
    state VARCHAR(100) NULL,
    country VARCHAR(100) NULL,
    postal_code VARCHAR(20) NULL,

    -- Configuration
    timezone VARCHAR(50) DEFAULT 'UTC',
    currency VARCHAR(10) DEFAULT 'USD',
    locale VARCHAR(10) DEFAULT 'fr',       -- Default French for AF schools
    logo_url VARCHAR(255) NULL,

    -- Legal
    registration_number VARCHAR(100) UNIQUE NULL,
    organization_type VARCHAR(50) DEFAULT 'school_group', -- school_group, university, ministry

    -- Subscription
    subscription_plan VARCHAR(50) NULL,
    subscription_status VARCHAR(20) DEFAULT 'pending',
    subscription_expires_at TIMESTAMP NULL,
    max_schools INT DEFAULT 1,
    max_users INT DEFAULT 10,
    max_students INT DEFAULT 500,

    -- Status
    status VARCHAR(20) DEFAULT 'pending',  -- pending, active, inactive, suspended

    -- Flexible
    settings JSON NULL,
    metadata JSON NULL,

    -- Timestamps
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    INDEX idx_organizations_status (status)
);
```

#### `organization_domains` (Stancl domain identification)

```sql
CREATE TABLE organization_domains (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain VARCHAR(255) UNIQUE NOT NULL,
    organization_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (organization_id) REFERENCES organizations(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);
```

### 6.2 Tenant Database Tables (per organization)

#### `schools` (Physical campus — NOT a tenant)

```sql
CREATE TABLE schools (
    id CHAR(26) PRIMARY KEY,              -- ULID
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL,            -- Unique within organization

    -- Type & Hierarchy
    type ENUM('head_office','branch','campus','annex') NOT NULL DEFAULT 'branch',
    is_head_office BOOLEAN DEFAULT FALSE,
    parent_school_id CHAR(26) NULL,       -- Self-referencing FK

    -- Contact
    address TEXT NULL,
    city VARCHAR(100) NULL,
    country VARCHAR(100) NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(255) NULL,

    -- Status
    status ENUM('active','inactive','suspended') DEFAULT 'active',

    -- Flexible
    settings JSON NULL,

    -- Timestamps
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    UNIQUE KEY uk_schools_code_per_org (code),  -- Enforced at app level
    FOREIGN KEY (parent_school_id) REFERENCES schools(id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    INDEX idx_schools_status (status),
    INDEX idx_schools_type (type)
);
```

#### `users`

```sql
CREATE TABLE users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(26) UNIQUE NOT NULL,

    -- Scope: organization-level (school_id NULL) or school-level
    school_id CHAR(26) NULL,              -- FK → schools.id, NULL = org-level

    -- Profile
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,           -- Unique within tenant DB
    phone VARCHAR(50) NULL,
    avatar VARCHAR(255) NULL,
    password VARCHAR(255) NOT NULL,

    -- Status
    status ENUM('active','inactive','pending','suspended') DEFAULT 'pending',

    -- Auth
    last_login_at TIMESTAMP NULL,
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,

    -- Timestamps
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (school_id) REFERENCES schools(id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    INDEX idx_users_status (status),
    INDEX idx_users_school_id (school_id)
);
```

#### `user_school` (Multi-campus pivot)

```sql
CREATE TABLE user_school (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    school_id CHAR(26) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (school_id) REFERENCES schools(id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    UNIQUE KEY uk_user_school (user_id, school_id)
);
```

#### `students`

```sql
CREATE TABLE students (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(26) UNIQUE NOT NULL,

    -- Scope
    school_id CHAR(26) NOT NULL,          -- FK → schools.id (current school)

    -- Identity
    student_number VARCHAR(50) NOT NULL,   -- Unique within school
    admission_number VARCHAR(50) NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NULL,
    gender ENUM('male','female','other') NULL,

    -- Links
    user_id BIGINT UNSIGNED NULL,          -- FK → users.id (optional login)

    -- Medical
    medical_info JSON NULL,

    -- Status
    status ENUM('active','inactive','graduated','suspended','withdrawn','archived')
        DEFAULT 'active',

    -- Timestamps
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (school_id) REFERENCES schools(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    INDEX idx_students_school_id (school_id),
    INDEX idx_students_status (status)
);
```

#### `academic_years` (Organization-wide)

```sql
CREATE TABLE academic_years (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(26) UNIQUE NOT NULL,

    -- NO school_id — org-wide

    name VARCHAR(100) NOT NULL,           -- e.g., "2026-2027"
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_current BOOLEAN DEFAULT FALSE,
    status ENUM('active','archived') DEFAULT 'active',

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    INDEX idx_academic_years_current (is_current)
);
```

#### `programs` (Organization-wide)

```sql
CREATE TABLE programs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(26) UNIQUE NOT NULL,

    -- NO school_id — org-wide
    department_id BIGINT UNSIGNED NULL,    -- Optional org-level department
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL,
    level ENUM('nursery','primary','junior_secondary','senior_secondary','tertiary','vocational') NOT NULL,
    description TEXT NULL,
    status ENUM('active','archived') DEFAULT 'active',
    display_order INT DEFAULT 0,

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (department_id) REFERENCES departments(id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    INDEX idx_programs_level (level)
);
```

#### `departments` (Per school)

```sql
CREATE TABLE departments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(26) UNIQUE NOT NULL,

    school_id CHAR(26) NOT NULL,          -- FK → schools.id
    head_id BIGINT UNSIGNED NULL,          -- FK → users.id
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL,
    description TEXT NULL,
    display_order INT DEFAULT 0,
    status ENUM('active','archived') DEFAULT 'active',

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (school_id) REFERENCES schools(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (head_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    UNIQUE KEY uk_departments_name_per_school (school_id, name),
    INDEX idx_departments_status (status)
);
```

#### `grade_levels` (Per school)

```sql
CREATE TABLE grade_levels (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(26) UNIQUE NOT NULL,

    school_id CHAR(26) NOT NULL,          -- FK → schools.id
    program_id BIGINT UNSIGNED NOT NULL,   -- FK → programs.id
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL,
    level_order INT NOT NULL DEFAULT 0,
    educational_cycle ENUM('nursery','primary','junior_secondary','senior_secondary','higher_education','vocational') NOT NULL,
    status ENUM('active','archived') DEFAULT 'active',

    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (school_id) REFERENCES schools(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (program_id) REFERENCES programs(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    UNIQUE KEY uk_grade_levels_code_per_school (school_id, code),
    INDEX idx_grade_levels_status (status)
);
```

#### Other school-scoped tables follow the same pattern:

Each gets `school_id CHAR(26) NOT NULL` with `FOREIGN KEY (school_id) REFERENCES schools(id) ON UPDATE CASCADE ON DELETE RESTRICT`.

Full list of school-scoped tables:
- `sections` (FK → grade_levels)
- `classrooms`
- `subject_categories`
- `subjects`
- `subject_coefficients`
- `academic_classes` (links academic_year + program + grade_level + section + classroom)
- `guardians`
- `student_enrollments`
- `timetable_entries`
- `attendance_records`
- `assessments`
- `assessment_results`
- All library tables (authors, publishers, book_categories, books, book_copies, book_loans, reservations, fines, book_author)
- All transport tables (vehicles, drivers, routes, route_stops, trips, student_transport_assignments, vehicle_maintenances, fuel_logs)

---

## 7. New Models Architecture

### 7.1 New/Modified Models

| Model | Location | Change Type | Key Changes |
|-------|----------|-------------|-------------|
| **Organization** | `app/Domains/Organization/Models/Organization.php` | **NEW** | Extends `Tenant`, implements `TenantWithDatabase` |
| **School** | `app/Domains/School/Models/School.php` | **REWRITE** | Regular Eloquent model, belongs to Organization (implicit via tenant) |
| **User** | `app/Models/User.php` | **REWRITE** | Remove `school_id` nullable + add `schools()` ManyToMany + `organization()` |
| **Student** | `app/Models/Student.php` | **REWRITE** | `school_id` → FK to `schools.id`, remove tenant reference |
| **AcademicYear** | `app/Models/AcademicYear.php` | **REWRITE** | Remove `school_id` (org-wide) |
| **AcademicTerm** | `app/Models/AcademicTerm.php` | **REWRITE** | Remove `school_id` (org-wide) |
| **Program** | `app/Models/Program.php` | **REWRITE** | Remove `school_id` (org-wide) |
| All 60+ other models | `app/Models/*.php` | **REWRITE** | Change `school_id` FK target from `tenants` to `schools` |

### 7.2 New Organization Model

```php
// app/Domains/Organization/Models/Organization.php

class Organization extends Tenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'database', 'domain', 'email', 'phone',
        'address', 'city', 'state', 'country', 'postal_code',
        'timezone', 'currency', 'locale', 'logo_url',
        'registration_number', 'organization_type',
        'subscription_plan', 'subscription_status', 'subscription_expires_at',
        'max_schools', 'max_users', 'max_students',
        'status', 'settings', 'metadata',
    ];

    // Relationships
    public function schools(): HasMany { /* → School::class */ }
    public function users(): HasMany { /* → User::class */ }
    public function academicYears(): HasMany { /* → AcademicYear::class */ }
    public function programs(): HasMany { /* → Program::class */ }
    public function systemSettings(): HasMany { /* → SystemSetting::class */ }
    public function domains(): HasMany { /* → Domain::class */ }
    // ... analytics, API, AI, workflow relationships
}
```

### 7.3 Rewritten School Model

```php
// app/Domains/School/Models/School.php

class School extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'name', 'code', 'type', 'is_head_office', 'parent_school_id',
        'address', 'city', 'country', 'phone', 'email',
        'status', 'settings',
    ];

    // Relationships
    public function organization(): BelongsTo {
        return $this->belongsTo(Organization::class);
        // Note: In tenant DB, this is always the current tenant
        // Could also return app(Tenant::class) for convenience
    }

    public function parent(): BelongsTo {
        return $this->belongsTo(School::class, 'parent_school_id');
    }

    public function children(): HasMany {
        return $this->hasMany(School::class, 'parent_school_id');
    }

    public function users(): BelongsToMany {
        return $this->belongsToMany(User::class, 'user_school')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function departments(): HasMany { /* ... */ }
    public function gradeLevels(): HasMany { /* ... */ }
    public function sections(): HasMany { /* ... */ }
    public function classrooms(): HasMany { /* ... */ }
    public function subjects(): HasMany { /* ... */ }
    public function academicClasses(): HasMany { /* ... */ }
    public function students(): HasMany { /* ... */ }
    public function guardians(): HasMany { /* ... */ }
    public function enrollments(): HasMany { /* → StudentEnrollment */ }
    public function assessments(): HasMany { /* ... */ }
    // Library, Transport relationships...
}
```

### 7.4 Rewritten User Model

```php
// app/Models/User.php

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, HasRoles, SoftDeletes, Notifiable;

    protected $fillable = [
        'school_id', 'first_name', 'last_name', 'email',
        'phone', 'avatar', 'password', 'status',
    ];

    // school_id is NULLABLE — NULL = org-level user
    public function school(): BelongsTo {
        return $this->belongsTo(School::class);
    }

    // Multi-campus: additional school assignments
    public function schools(): BelongsToMany {
        return $this->belongsToMany(School::class, 'user_school')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    // All schools this user can access (primary + additional)
    public function accessibleSchools(): Collection
    {
        if (is_null($this->school_id)) {
            // Org-level: access ALL schools
            return School::all();
        }
        // School-level: primary + additional
        return $this->schools->merge([$this->school]);
    }

    public function isOrgLevel(): bool
    {
        return is_null($this->school_id);
    }
}
```

### 7.5 Domain Scoping Service (NEW)

```php
// app/Domains/Shared/Services/TenantScopeService.php

class TenantScopeService
{
    /**
     * Get the current user's accessible school IDs.
     * Returns null for org-level (no filtering needed).
     */
    public function getUserSchoolIds(User $user): ?array
    {
        if ($user->isOrgLevel()) {
            return null; // No filtering — sees all
        }

        return $user->accessibleSchools()
            ->pluck('id')
            ->toArray();
    }

    /**
     * Scope a query to the user's accessible schools.
     */
    public function scopeToUserSchools(Builder $query, User $user): Builder
    {
        $schoolIds = $this->getUserSchoolIds($user);

        if ($schoolIds === null) {
            return $query; // No scope needed
        }

        return $query->whereIn('school_id', $schoolIds);
    }
}
```

---

## 8. Relationship Diagram

```
Organization (Tenant)
│
├── hasMany ── School
│               ├── belongsTo ── School (parent, self-ref)
│               ├── hasMany ── Department
│               │               └── belongsTo ── User (head)
│               ├── hasMany ── GradeLevel
│               │               ├── belongsTo ── School
│               │               └── belongsTo ── Program
│               │                   └── hasMany ── GradeLevel
│               ├── hasMany ── Section
│               │               └── belongsTo ── GradeLevel
│               ├── hasMany ── Classroom
│               ├── hasMany ── Subject
│               │               ├── belongsTo ── SubjectCategory
│               │               ├── belongsTo ── Department
│               │               └── belongsTo ── Program
│               ├── hasMany ── AcademicClass
│               │               ├── belongsTo ── AcademicYear
│               │               ├── belongsTo ── Program
│               │               ├── belongsTo ── GradeLevel
│               │               ├── belongsTo ── Section
│               │               ├── belongsTo ── Classroom
│               │               └── belongsTo ── User (homeroom teacher)
│               ├── hasMany ── Student
│               │               ├── belongsTo ── School
│               │               └── belongsTo ── User (optional login)
│               ├── belongsToMany ── User (via user_school)
│               ├── hasMany ── Guardian
│               ├── hasMany ── StudentEnrollment
│               │               ├── belongsTo ── Student
│               │               ├── belongsTo ── AcademicYear
│               │               └── belongsTo ── AcademicClass
│               ├── hasMany ── Assessment
│               │               └── hasMany ── AssessmentResult
│               │                           └── belongsTo ── Student
│               ├── hasMany ── Library entities (Books, Authors, etc.)
│               └── hasMany ── Transport entities (Vehicles, Routes, etc.)
│
├── hasMany ── AcademicYear (org-wide)
│               └── hasMany ── AcademicTerm
│
├── hasMany ── Program (org-wide)
│
├── hasMany ── User
│               ├── belongsTo ── School (nullable)
│               ├── belongsToMany ── School (via user_school)
│               ├── hasOne ── Student (optional)
│               └── hasOne ── Guardian (optional)
│
├── hasMany ── AiProvider, ApiClient, Workflow, Dashboard, Report...
│
└── hasMany ── SystemSetting, AuditLog, Backup...
```

---

## 9. Migration Dependency Order

### Phase 1: Central Database (Organization as Tenant)

| Order | Migration | Table | Notes |
|-------|-----------|-------|-------|
| 1 | `create_organizations_table` | `organizations` | Stancl tenant table (replaces tenants) |
| 2 | `create_organization_domains_table` | `organization_domains` | Stancl domain identification |

### Phase 2: Tenant Database — Foundation

| Order | Migration | Table | Notes |
|-------|-----------|-------|-------|
| 3 | `create_schools_table` | `schools` | ULID PK, self-referencing parent_school_id |
| 4 | `create_users_table` | `users` | school_id nullable → schools.id |
| 5 | `create_user_school_table` | `user_school` | Multi-campus pivot |
| 6 | `create_permission_tables` | spatie tables | roles, permissions, model_has_roles, etc. |
| 7 | `create_personal_access_tokens_table` | `personal_access_tokens` | Sanctum |

### Phase 3: Tenant Database — Academic Structure

| Order | Migration | Table | Depends On |
|-------|-----------|-------|------------|
| 8 | `create_academic_years_table` | `academic_years` | — (org-wide) |
| 9 | `create_academic_terms_table` | `academic_terms` | academic_years |
| 10 | `create_programs_table` | `programs` | — (org-wide) |
| 11 | `create_departments_table` | `departments` | schools, users |
| 12 | `create_grade_levels_table` | `grade_levels` | schools, programs |
| 13 | `create_sections_table` | `sections` | grade_levels |
| 14 | `create_classrooms_table` | `classrooms` | schools |
| 15 | `create_subject_categories_table` | `subject_categories` | schools |
| 16 | `create_subjects_table` | `subjects` | schools, departments, programs, subject_categories |
| 17 | `create_subject_coefficients_table` | `subject_coefficients` | subjects, academic_years |
| 18 | `create_academic_classes_table` | `academic_classes` | schools, academic_years, programs, grade_levels, sections, classrooms, users |

### Phase 4: Tenant Database — Students & Enrollment

| Order | Migration | Table | Depends On |
|-------|-----------|-------|------------|
| 19 | `create_guardians_table` | `guardians` | schools |
| 20 | `create_students_table` | `students` | schools, users |
| 21 | `create_student_enrollments_table` | `student_enrollments` | students, academic_years, academic_classes, schools |
| 22 | `create_timetable_entries_table` | `timetable_entries` | schools, academic_classes, subjects, classrooms, users |
| 23 | `create_attendance_records_table` | `attendance_records` | students, academic_classes, schools |
| 24 | `create_assessments_table` | `assessments` | schools, academic_classes, academic_terms, subjects |
| 25 | `create_assessment_results_table` | `assessment_results` | assessments, students, schools |

### Phase 5: Tenant Database — Library

| Order | Migration | Table | Depends On |
|-------|-----------|-------|------------|
| 26 | `create_authors_table` | `authors` | schools |
| 27 | `create_publishers_table` | `publishers` | schools |
| 28 | `create_book_categories_table` | `book_categories` | schools |
| 29 | `create_books_table` | `books` | schools, publishers, book_categories |
| 30 | `create_book_copies_table` | `book_copies` | books, schools |
| 31 | `create_book_loans_table` | `book_loans` | book_copies, students, schools |
| 32 | `create_reservations_table` | `reservations` | books, students, schools |
| 33 | `create_fines_table` | `fines` | book_loans, students, schools |
| 34 | `create_book_author_table` | `book_author` | books, authors |

### Phase 6: Tenant Database — Transport

| Order | Migration | Table | Depends On |
|-------|-----------|-------|------------|
| 35 | `create_vehicles_table` | `vehicles` | schools |
| 36 | `create_drivers_table` | `drivers` | schools |
| 37 | `create_routes_table` | `routes` | schools |
| 38 | `create_route_stops_table` | `route_stops` | routes |
| 39 | `create_trips_table` | `trips` | routes, vehicles, drivers, schools |
| 40 | `create_student_transport_assignments_table` | `student_transport_assignments` | students, routes, schools |
| 41 | `create_vehicle_maintenances_table` | `vehicle_maintenances` | vehicles |
| 42 | `create_fuel_logs_table` | `fuel_logs` | vehicles, drivers |

### Phase 7: Tenant Database — Communication

| Order | Migration | Table | Depends On |
|-------|-----------|-------|------------|
| 43 | `create_notification_templates_table` | `notification_templates` | — |
| 44 | `create_notification_channels_table` | `notification_channels` | — |
| 45 | `create_notifications_table` | `notifications` | notification_templates, users |
| 46 | `create_notification_deliveries_table` | `notification_deliveries` | notifications |
| 47 | `create_announcements_table` | `announcements` | users (nullable school_id) |
| 48 | `create_messages_table` | `messages` | users |
| 49 | `create_message_recipients_table` | `message_recipients` | messages, users |
| 50 | `create_communication_channels_table` | `communication_channels` | — |

### Phase 8: Tenant Database — Analytics, API, AI, Workflow

| Order | Migration | Table |
|-------|-----------|-------|
| 51 | `create_dashboards_table` | dashboards |
| 52 | `create_dashboard_widgets_table` | dashboard_widgets |
| 53 | `create_reports_table` | reports |
| 54 | `create_kpis_table` | kpis |
| 55 | `create_analytics_snapshots_table` | analytics_snapshots |
| 56 | `create_api_clients_table` | api_clients |
| 57 | `create_api_tokens_table` | api_tokens |
| 58 | `create_webhooks_table` | webhooks |
| 59 | `create_webhook_deliveries_table` | webhook_deliveries |
| 60 | `create_api_audit_logs_table` | api_audit_logs |
| 61-66 | AI tables | ai_* |
| 67-73 | Workflow tables | workflow_*, automation_rules |

### Phase 9: Tenant Database — System & Plugins

| Order | Migration | Table |
|-------|-----------|-------|
| 74 | `create_system_settings_table` | system_settings |
| 75 | `create_audit_logs_table` | audit_logs |
| 76 | `create_activity_logs_table` | activity_logs |
| 77 | `create_scheduled_tasks_table` | scheduled_tasks |
| 78 | `create_backups_table` | backups |
| 79 | `create_system_health_table` | system_health |
| 80 | `create_maintenance_windows_table` | maintenance_windows |
| 81-82 | Plugin tables | plugins, plugin_settings |

---

## 10. Complete Impact Inventory

### 10.1 Migrations to Rewrite (ALL 91)

Every migration must be rewritten. The changes fall into categories:

**A. Central Database Migrations (2 new)**

| # | Migration | Action |
|---|-----------|--------|
| 1 | `create_organizations_table` | NEW — replaces `create_tenants_table` |
| 2 | `create_organization_domains_table` | NEW — replaces `create_domains_table` |

**B. Tenant Migrations — Move to `database/migrations/tenant/` (89 migrations)**

All existing tenant-scoped migrations must be moved from `database/migrations/` to `database/migrations/tenant/` AND rewritten to fix foreign keys.

**C. Specific Migration Changes:**

| Migration | Change Required |
|-----------|----------------|
| `create_users_table` | `school_id` FK → `schools.id` (was → `tenants`) |
| `create_students_table` | `school_id` FK → `schools.id` (was → `tenants`) |
| `create_academic_years_table` | **Remove `school_id`** (org-wide) |
| `create_academic_terms_table` | **Remove `school_id`** (org-wide) |
| `create_programs_table` | **Remove `school_id`** (org-wide) |
| `create_departments_table` | `school_id` FK → `schools.id` |
| `create_grade_levels_table` | `school_id` FK → `schools.id` |
| `create_sections_table` | `school_id` FK → `schools.id` |
| `create_academic_classes_table` | `school_id` FK → `schools.id` |
| `create_classrooms_table` | `school_id` FK → `schools.id` |
| `create_subjects_table` | `school_id` FK → `schools.id` |
| `create_subject_categories_table` | `school_id` FK → `schools.id` |
| `create_subject_coefficients_table` | `school_id` FK → `schools.id` |
| `create_guardians_table` | `school_id` FK → `schools.id` |
| `create_student_enrollments_table` | `school_id` FK → `schools.id` (ADD FK constraint!) |
| `create_timetable_entries_table` | `school_id` FK → `schools.id` |
| `create_assessments_table` | `school_id` FK → `schools.id` |
| `create_assessment_results_table` | `school_id` FK → `schools.id` |
| `create_attendance_records_table` | `school_id` FK → `schools.id` |
| All library migrations | `school_id` FK → `schools.id` |
| All transport migrations | `school_id` FK → `schools.id` |
| All communication migrations | `school_id` FK → `schools.id` (or remove for org-wide) |
| All analytics migrations | **Remove `school_id`** (org-wide) |
| All API migrations | **Remove `school_id`** (org-wide) |
| All AI migrations | **Remove `school_id`** (org-wide) |
| All workflow migrations | **Remove `school_id`** (org-wide) |
| `system_settings` | **Remove `school_id`** (org-wide) |
| `audit_logs` | **Remove `school_id`** (org-wide) |
| `activity_logs` | **Remove `school_id`** (org-wide) |
| All other system migrations | **Remove `school_id`** (org-wide) |
| `plugins`, `plugin_settings` | **Remove `school_id`** (org-wide) |
| All 6 `add_school_id` alter migrations | **Delete** (rebuilt from scratch) |
| `fix_users_school_id_fk` | **Delete** (rebuilt from scratch) |
| `fix_subject_categories_display_order` | **Delete** (rebuilt from scratch) |

**D. New Migrations to Create:**

| Migration | Table |
|-----------|-------|
| `create_user_school_table` | `user_school` (multi-campus pivot) |
| `create_school_settings_table` | `school_settings` (optional, per-school config) |

### 10.2 Models to Rewrite (74 models)

| Model | File | Changes |
|-------|------|---------|
| **Organization** | `app/Domains/Organization/Models/Organization.php` | **NEW** — extends Tenant |
| **School** | `app/Domains/School/Models/School.php` | **REWRITE** — regular Model, add relationships to departments, grade_levels, etc. |
| **User** | `app/Models/User.php` | Add `schools()` M2M, `accessibleSchools()`, `isOrgLevel()`, `organization()` |
| **Student** | `app/Models/Student.php` | Fix FK target |
| **AcademicYear** | `app/Models/AcademicYear.php` | Remove `school_id`, add `organization()` |
| **AcademicTerm** | `app/Models/AcademicTerm.php` | Remove `school_id`, add `school()` relationship |
| **Program** | `app/Models/Program.php` | Remove `school_id`, add `organization()` |
| **Department** | `app/Models/Department.php` | Fix FK target |
| **GradeLevel** | `app/Models/GradeLevel.php` | Fix FK target |
| **Section** | `app/Models/Section.php` | Fix FK target |
| **Classroom** | `app/Models/Classroom.php` | Fix FK target |
| **AcademicClass** | `app/Models/AcademicClass.php` | Fix FK target |
| **Subject** | `app/Models/Subject.php` | Fix FK target |
| **SubjectCategory** | `app/Models/SubjectCategory.php` | Fix FK target |
| **SubjectCoefficient** | `app/Models/SubjectCoefficient.php` | Fix FK target |
| **Guardian** | `app/Models/Guardian.php` | Fix FK target |
| **StudentEnrollment** | `app/Models/StudentEnrollment.php` | Fix FK target, add FK constraint |
| **Assessment** | `app/Models/Assessment.php` | Fix FK target |
| **AssessmentResult** | `app/Models/AssessmentResult.php` | Fix FK target |
| All library models (8) | `app/Models/Book*.php`, `Author.php`, etc. | Fix FK target, add missing relationships |
| All transport models (8) | `app/Models/Vehicle.php`, `Route.php`, etc. | Fix relationship pattern |
| All communication models (7) | `app/Models/Notification.php`, etc. | Fix relationship, decide org vs school scope |
| All analytics models (5) | `app/Models/Dashboard.php`, etc. | Remove `school_id` (org-wide) |
| All API models (5) | `app/Models/ApiClient.php`, etc. | Remove `school_id` (org-wide) |
| All AI models (6) | `app/Models/Ai*.php` | Remove `school_id` (org-wide) |
| All workflow models (7) | `app/Models/Workflow*.php` | Remove `school_id` (org-wide) |
| All system models (8) | `app/Models/System*.php`, `AuditLog.php`, etc. | Remove `school_id` (org-wide) |
| Plugin models (2) | `app/Models/Plugin*.php` | Remove `school_id` (org-wide) |

### 10.3 Services to Rewrite (all 29 domain services)

| Domain | Service | Changes |
|--------|---------|---------|
| **Organization** | `OrganizationService` | **NEW** — tenant provisioning, subscription management |
| **School** | `SchoolService` | **REWRITE** — CRUD within org, no longer tenant-related |
| **User** | `UserService` | Add multi-school assignment logic |
| **Student** | `StudentService` | Fix school scoping |
| **AcademicYear** | `AcademicYearService` | Remove school scoping (org-wide) |
| **AcademicTerm** | `AcademicTermService` | Remove school scoping (org-wide) |
| **Program** | `ProgramService` | Remove school scoping (org-wide) |
| **Department** | `DepartmentService` | Fix school scoping |
| **GradeLevel** | `GradeLevelService` | Fix school scoping |
| **Section** | `SectionService` | Fix school scoping |
| **AcademicClass** | `AcademicClassService` | Fix school scoping |
| **Subject** | `SubjectService` | Fix school scoping + fix `validateUniqueDisplayOrder` bug |
| **Classroom** | `ClassroomService` | Fix school scoping |
| **StudentEnrollment** | `StudentEnrollmentService` | Fix school scoping |
| **Guardian** | `GuardianService` | Fix school scoping |
| **Gradebook** | `GradebookService` | Fix school scoping |
| **Library** (3) | `LibraryService`, `CirculationService`, `FineService` | Fix school scoping |
| **Transport** (3) | `TransportService`, `FleetService`, `MaintenanceService` | Fix school scoping |
| **Communication** (4) | `NotificationService`, `MessagingService`, etc. | Decide org vs school scope |
| **Analytics** (4) | `AnalyticsService`, `DashboardService`, `KpiService`, `ReportingService` | Remove school scoping (org-wide) |
| **AI** (5) | `AiService`, `ConversationService`, etc. | Remove school scoping (org-wide) |
| **API** (4) | `ApiClientService`, `ApiTokenService`, `WebhookService`, `OAuthService` | Remove school scoping (org-wide) |
| **Workflow** (5) | `WorkflowService`, `ExecutionService`, etc. | Remove school scoping (org-wide) |
| **System** (7) | `AuditService`, `BackupService`, etc. | Remove school scoping (org-wide) |
| **Plugin** (17) | `PluginManager`, etc. | Remove school scoping (org-wide) |
| **Role** | `RoleService` | May need school-aware role assignment |
| **Permission** | `PermissionService` | May need school-aware permission checking |
| **Shared** | `TenantScopeService` | **NEW** — centralized school scoping logic |

### 10.4 Policies to Rewrite (all 29 domain policies)

Every policy currently uses `$user->school_id === $model->school_id`. This must be replaced with:

```php
// NEW pattern for school-scoped entities:
public function view(User $user, School $school): bool
{
    return $user->isOrgLevel()
        || $user->accessibleSchools()->contains('id', $school->id);
}

// NEW pattern for entities with school_id:
public function viewAny(User $user): bool
{
    return $user->hasPermissionTo('view_any_student');
    // Scoping handled by query scope, not policy
}

public function view(User $user, Student $student): bool
{
    return $user->isOrgLevel()
        || $user->accessibleSchools()->contains('id', $student->school_id);
}
```

| Policy | File | Changes |
|--------|------|---------|
| **SchoolPolicy** | `SchoolPolicy.php` | Rewrite for org scope |
| **StudentPolicy** | `StudentPolicy.php` | Use `accessibleSchools()` |
| **UserPolicy** | `UserPolicy.php` | Use `accessibleSchools()` |
| **AcademicClassPolicy** | `AcademicClassPolicy.php` | Use `accessibleSchools()` |
| **DepartmentPolicy** | `DepartmentPolicy.php` | Use `accessibleSchools()` |
| **GradeLevelPolicy** | `GradeLevelPolicy.php` | Use `accessibleSchools()` |
| **SubjectPolicy** | `SubjectPolicy.php` | Use `accessibleSchools()` |
| **StudentEnrollmentPolicy** | `StudentEnrollmentPolicy.php` | Use `accessibleSchools()` |
| **AcademicYearPolicy** | `AcademicYearPolicy.php` | Remove school check (org-wide) |
| **AcademicTermPolicy** | `AcademicTermPolicy.php` | Remove school check (org-wide) |
| **ProgramPolicy** | `ProgramPolicy.php` | Remove school check (org-wide) |
| All library policies (8) | Various | Use `accessibleSchools()` |
| All transport policies (8) | Various | Use `accessibleSchools()` |
| All communication policies (7) | Various | Mixed — some org, some school |
| All analytics policies (5) | Various | Remove school check (org-wide) |
| All other policies (10+) | Various | Remove school check (org-wide) |

### 10.5 Filament Resources to Rewrite (all 57 resources)

| Resource | Changes |
|----------|---------|
| **SchoolResource** | **NEW/MAJOR REWRITE** — CRUD for schools within org |
| **UserResource** | Add multi-school assignment form, school filter update |
| **StudentResource** | Add school filter, scope queries |
| **AcademicClassResource** | Add school selector, scope by school |
| **DepartmentResource** | Add school selector, scope by school |
| **GradeLevelResource** | Add school selector, scope by school |
| **SectionResource** | Add school selector, scope by school |
| **ClassroomResource** | Add school selector, scope by school |
| **SubjectResource** | Add school selector, scope by school |
| **StudentEnrollmentResource** | Add school selector, scope by school |
| **GuardianResource** | Add school selector, scope by school |
| **AssessmentResultResource** | Add school selector, scope by school |
| All library resources (9) | Add school selector, scope by school |
| All transport resources (8) | Add school selector, scope by school |
| All communication resources (7) | Decide org vs school scope |
| All analytics resources (5) | Remove school scope (org-wide) |
| All AI resources (6) | Remove school scope (org-wide) |
| All API resources (5) | Remove school scope (org-wide) |
| All workflow resources (3) | Remove school scope (org-wide) |
| All system resources (5) | Remove school scope (org-wide) |
| AcademicYearResource | Remove school scope (org-wide) |
| ProgramResource | Remove school scope (org-wide) |

### 10.6 DTOs to Rewrite (all domain DTOs)

| DTO | Changes |
|-----|---------|
| `SchoolData` | Add `type`, `isHeadOffice`, `parentSchoolId`, remove tenant fields |
| `OrganizationData` | **NEW** — organization creation/update data |
| `UserData` | Update `schoolId` semantics (nullable = org-level) |
| `StudentData` | Ensure `schoolId` is required |
| `AcademicYearData` | **Remove** `schoolId` (org-wide) |
| `AcademicTermData` | **Remove** `schoolId` (org-wide) |
| `ProgramData` | **Remove** `schoolId` (org-wide) |
| `DepartmentData` | Ensure `schoolId` is required |
| All other DTOs | Verify schoolId semantics |

### 10.7 Enums to Modify

| Enum | Changes |
|------|---------|
| `SchoolType` | **REPLACE** with: `HeadOffice`, `Branch`, `Campus`, `Annex` |
| `SchoolStatus` | Keep: `Active`, `Inactive`, `Suspended` |
| `OrganizationType` | **NEW**: `SchoolGroup`, `University`, `Ministry`, `Independent` |
| `OrganizationStatus` | **NEW**: `Pending`, `Active`, `Inactive`, `Suspended` |

### 10.8 Events & Listeners

All domain events currently carry `school_id` implicitly. They must be reviewed to:
- Ensure org-wide events don't carry `school_id`
- Ensure school-scoped events carry the correct `school_id` → `schools.id`

| Domain | Events | Listeners |
|--------|--------|-----------|
| School | Created, Suspended, Updated | 0 |
| Student | Created, Activated, Updated, Archived | 4 |
| AcademicYear | 9 events | (implied) |
| AcademicTerm | 5 events | (implied) |
| GradeLevel | 3 events | 3 |
| Program | 3 events | 3 |
| Subject | 4 events | 4 |
| ... all others | Multiple | Multiple |

### 10.9 Rules to Rewrite

All validation rules that reference `school_id` need updating. Key ones:

| Domain | Rule | Changes |
|--------|------|---------|
| AcademicClass | `AcademicClassRules` | Fix school scoping |
| Subject | `SubjectRules` | Fix school scoping |
| Student | `StudentRules` | Fix school scoping |
| ... all others | Various | Fix school scoping |

### 10.10 Console Commands to Review

| Command | Changes |
|---------|---------|
| `SystemBackupCommand` | Org-scoped |
| `SystemCleanupCommand` | Org-scoped |
| `SystemHealthCheckCommand` | Org-scoped |
| `SystemMaintenanceCommand` | Org-scoped |
| `ScheduleReports` | Org-scoped |
| All others (10+) | Review for school vs org scope |

### 10.11 Providers to Rewrite

| Provider | Changes |
|----------|---------|
| `TenancyServiceProvider` | **MAJOR REWRITE** — provision Organization (not School) |
| `AdminPanelProvider` | Add school-switcher, tenant middleware |
| `EventServiceProvider` | Update event bindings |
| `AppServiceProvider` | Register new services |

### 10.12 Config Files to Update

| Config | Changes |
|--------|---------|
| `tenancy.php` | Change `tenant_model` → `Organization::class` |
| `permission.php` | Consider enabling `teams` for school-level permissions |
| `auth.php` | May need multi-guard setup |

---

## 11. Risk Assessment

### 11.1 Risk Matrix

| Risk | Probability | Impact | Severity | Mitigation |
|------|-------------|--------|----------|------------|
| Data loss during migration | HIGH | CRITICAL | **CRITICAL** | Full backup before each phase; rollback plan |
| Breaking stancl tenancy provisioning | MEDIUM | CRITICAL | **HIGH** | Test Organization provisioning in isolation first |
| FK constraint violations | HIGH | HIGH | **HIGH** | Migrate data before adding constraints |
| Filament resources break | HIGH | MEDIUM | **HIGH** | Test every resource after changes |
| Permission system breaks | MEDIUM | HIGH | **HIGH** | Migrate roles/permissions with school awareness |
| Service logic breaks | HIGH | MEDIUM | **HIGH** | Comprehensive test suite before refactoring |
| ID type mismatches | MEDIUM | HIGH | **HIGH** | Standardize on ULID for all models |
| Cross-school data leakage | HIGH | CRITICAL | **CRITICAL** | Implement global scope before anything else |
| Performance regression (multi-school queries) | MEDIUM | MEDIUM | **MEDIUM** | Index optimization, query caching |
| Plugin system breaks | LOW | MEDIUM | **LOW** | Plugins use org-wide scope (no change) |
| Tenant database creation fails | LOW | HIGH | **MEDIUM** | Test with MySQL database manager |
| API backward compatibility | MEDIUM | MEDIUM | **MEDIUM** | API versioning during transition |

### 11.2 Blocking Dependencies

1. **Organization model must be created first** — everything depends on it
2. **School model rewrite must precede all FK changes** — all models reference School
3. **Global query scope must be implemented before any data migration** — prevents data leakage
4. **User model changes must come before policy updates** — policies depend on user access methods

---

## 12. Migration Strategy

### Strategy: Big Bang with Phased Rollout

Given the scope of changes (every file in the codebase is affected), an incremental migration is not feasible. The recommended approach is a **clean rewrite with data migration**.

### Phase 0: Preparation (1-2 days)
1. Create comprehensive test suite for current behavior
2. Document current data volumes and relationships
3. Create full database backup
4. Create `Organization` model and test in isolation
5. Set up `database/migrations/tenant/` directory properly

### Phase 1: Foundation (2-3 days)
1. Create `organizations` table in central database
2. Create `organization_domains` table
3. Migrate existing `tenants` data → `organizations`
4. Move ALL tenant-scoped migrations to `database/migrations/tenant/`
5. Create `schools` table in tenant database
6. Create `user_school` pivot table
7. Rewrite `tenancy.php` config
8. Rewrite `TenancyServiceProvider`

### Phase 2: Core Models (3-4 days)
1. Rewrite Organization model (extends Tenant)
2. Rewrite School model (regular Eloquent)
3. Rewrite User model (add multi-school support)
4. Implement `TenantScopeService`
5. Implement global school query scope
6. Update all 60+ Eloquent models with correct FKs

### Phase 3: Business Logic (5-7 days)
1. Rewrite all 29 domain services
2. Rewrite all 29 domain policies
3. Fix all validation rules
4. Update all DTOs
5. Update all enums
6. Fix SubjectService display_order bug
7. Update events and listeners

### Phase 4: UI Layer (5-7 days)
1. Rewrite SchoolResource
2. Update all 57 Filament resources
3. Add school-switcher to admin panel
4. Update all Filament pages and widgets
5. Test every CRUD operation

### Phase 5: Infrastructure (2-3 days)
1. Update all console commands
2. Update API routes and controllers
3. Update Jobs
4. Update Providers
5. Update config files

### Phase 6: Data Migration (1-2 days)
1. Migrate existing tenants → organizations
2. Migrate existing school data to proper school records
3. Fix all FK references in data
4. Validate data integrity

### Phase 7: Testing & Validation (3-4 days)
1. Run full test suite
2. Manual QA for every feature
3. Performance testing with multi-school data
4. Security audit for data isolation
5. Load testing

**Total estimated effort: 22-31 days**

---

## 13. Backward Compatibility

### 13.1 API Breaking Changes

The following API changes are **unavoidable and breaking**:

| Endpoint | Change | Migration |
|----------|--------|-----------|
| `POST /api/schools` | Creates a school within org (not a tenant) | New behavior |
| `GET /api/schools` | Returns schools in current org | New behavior |
| All `school_id` in API payloads | Now references `schools.id` (not `tenants.id`) | Client update required |
| Tenant identification | By Organization (not School) | Domain/subdomain change |

### 13.2 Backward Compatibility Measures

1. **API Versioning:** Introduce `/api/v2/` prefix for changed endpoints
2. **Data Mapping Table:** Maintain a `school_id → organization_id` mapping during transition
3. **Deprecation Notices:** Return `X-Deprecation-Warning` headers for old patterns
4. **Dual-write Period:** Temporarily write to both old and new structures during migration

### 13.3 What Cannot Be Preserved

- Old `school_id` values in external systems (they referenced tenants, not schools)
- Direct database queries from external tools (FK structure changes)
- Cached data (cache keys change)

---

## 14. Refactoring Roadmap

### Sprint 1: Foundation (Week 1)
- [ ] Create Organization domain (model, service, policy, DTO, enum)
- [ ] Create `organizations` and `organization_domains` migrations
- [ ] Configure stancl tenancy for Organization
- [ ] Test Organization tenant provisioning
- [ ] Create `schools` tenant migration
- [ ] Create `user_school` tenant migration

### Sprint 2: Core Models (Week 2)
- [ ] Rewrite School model
- [ ] Rewrite User model with multi-school support
- [ ] Implement TenantScopeService
- [ ] Implement global school scope middleware
- [ ] Update all model FKs
- [ ] Update all model relationships

### Sprint 3: Academic Domain (Week 3)
- [ ] Remove `school_id` from AcademicYear, AcademicTerm, Program
- [ ] Update Department, GradeLevel, Section, AcademicClass, Classroom
- [ ] Update Subject, SubjectCategory, SubjectCoefficient
- [ ] Update Student, Guardian, StudentEnrollment
- [ ] Update Assessment, AssessmentResult
- [ ] Update all related services, policies, DTOs, rules

### Sprint 4: Library & Transport (Week 4)
- [ ] Update all library models (8) and their services/policies
- [ ] Update all transport models (8) and their services/policies
- [ ] Update timetable and attendance

### Sprint 5: Infrastructure Domains (Week 5)
- [ ] Remove `school_id` from Analytics, API, AI, Workflow models
- [ ] Update Communication models (decide org vs school scope)
- [ ] Update System models (org-wide)
- [ ] Update Plugin models (org-wide)

### Sprint 6: UI Layer (Week 6)
- [ ] Create new SchoolResource (multi-campus management)
- [ ] Add school-switcher to AdminPanelProvider
- [ ] Update all 57 Filament resources
- [ ] Update forms, tables, filters, actions

### Sprint 7: Integration & Testing (Week 7)
- [ ] Update all providers
- [ ] Update all console commands
- [ ] Update Jobs
- [ ] Update routes
- [ ] Full test suite
- [ ] Performance testing

### Sprint 8: Data Migration & Launch (Week 8)
- [ ] Write data migration scripts
- [ ] Execute data migration
- [ ] Validate data integrity
- [ ] Security audit
- [ ] Documentation update
- [ ] Deployment

---

## Appendix A: Table Scope Classification (Complete)

| Table | Scope | school_id | FK Target | Notes |
|-------|-------|-----------|-----------|-------|
| `organizations` | CENTRAL | — | — | Stancl tenant |
| `organization_domains` | CENTRAL | — | — | Stancl domain |
| `schools` | ORG (tenant DB) | — (IS the school) | — | ULID PK |
| `users` | HYBRID | nullable | schools.id | NULL = org-level |
| `user_school` | PIVOT | — | users.id, schools.id | Multi-campus |
| `academic_years` | ORG | REMOVED | — | Org-wide |
| `academic_terms` | ORG | REMOVED | — | Org-wide |
| `programs` | ORG | REMOVED | — | Org-wide |
| `departments` | SCHOOL | required | schools.id | Per campus |
| `grade_levels` | SCHOOL | required | schools.id | Per campus |
| `sections` | SCHOOL | required | schools.id | Per campus |
| `classrooms` | SCHOOL | required | schools.id | Per campus |
| `subjects` | SCHOOL | required | schools.id | Per campus |
| `subject_categories` | SCHOOL | required | schools.id | Per campus |
| `subject_coefficients` | SCHOOL | required | schools.id | Per campus |
| `academic_classes` | SCHOOL | required | schools.id | Per campus |
| `students` | SCHOOL | required | schools.id | Per campus |
| `guardians` | SCHOOL | required | schools.id | Per campus |
| `student_enrollments` | SCHOOL | required | schools.id | Per campus |
| `timetable_entries` | SCHOOL | required | schools.id | Per campus |
| `attendance_records` | SCHOOL | required | schools.id | Per campus |
| `assessments` | SCHOOL | required | schools.id | Per campus |
| `assessment_results` | SCHOOL | required | schools.id | Per campus |
| `authors` | SCHOOL | required | schools.id | Per campus |
| `publishers` | SCHOOL | required | schools.id | Per campus |
| `book_categories` | SCHOOL | required | schools.id | Per campus |
| `books` | SCHOOL | required | schools.id | Per campus |
| `book_copies` | SCHOOL | required | schools.id | Per campus |
| `book_loans` | SCHOOL | required | schools.id | Per campus |
| `reservations` | SCHOOL | required | schools.id | Per campus |
| `fines` | SCHOOL | required | schools.id | Per campus |
| `vehicles` | SCHOOL | required | schools.id | Per campus |
| `drivers` | SCHOOL | required | schools.id | Per campus |
| `routes` | SCHOOL | required | schools.id | Per campus |
| `route_stops` | SCHOOL | required | — (route_id) | Via route |
| `trips` | SCHOOL | required | schools.id | Per campus |
| `student_transport_assignments` | SCHOOL | required | schools.id | Per campus |
| `vehicle_maintenances` | SCHOOL | required | — (vehicle_id) | Via vehicle |
| `fuel_logs` | SCHOOL | required | — (vehicle_id) | Via vehicle |
| `notifications` | HYBRID | nullable | schools.id | NULL = org-wide |
| `notification_deliveries` | HYBRID | nullable | — (notification_id) | Via notification |
| `notification_templates` | ORG | REMOVED | — | Org-wide |
| `notification_channels` | ORG | REMOVED | — | Org-wide |
| `announcements` | HYBRID | nullable | schools.id | NULL = org-wide |
| `messages` | HYBRID | nullable | schools.id | NULL = org-wide |
| `message_recipients` | HYBRID | nullable | — (message_id) | Via message |
| `communication_channels` | ORG | REMOVED | — | Org-wide |
| `dashboards` | ORG | REMOVED | — | Org-wide |
| `dashboard_widgets` | ORG | REMOVED | — | Org-wide |
| `reports` | ORG | REMOVED | — | Org-wide |
| `kpis` | ORG | REMOVED | — | Org-wide |
| `analytics_snapshots` | ORG | REMOVED | — | Org-wide |
| `api_clients` | ORG | REMOVED | — | Org-wide |
| `api_tokens` | ORG | REMOVED | — | Org-wide |
| `webhooks` | ORG | REMOVED | — | Org-wide |
| `webhook_deliveries` | ORG | REMOVED | — | Org-wide |
| `api_audit_logs` | ORG | REMOVED | — | Org-wide |
| `ai_providers` | ORG | REMOVED | — | Org-wide |
| `ai_conversations` | ORG | REMOVED | — | Org-wide |
| `ai_messages` | ORG | REMOVED | — | Org-wide |
| `ai_knowledge_sources` | ORG | REMOVED | — | Org-wide |
| `ai_prompt_templates` | ORG | REMOVED | — | Org-wide |
| `ai_usage_logs` | ORG | REMOVED | — | Org-wide |
| `workflows` | ORG | REMOVED | — | Org-wide |
| `workflow_versions` | ORG | REMOVED | — | Org-wide |
| `workflow_triggers` | ORG | REMOVED | — | Org-wide |
| `workflow_executions` | ORG | REMOVED | — | Org-wide |
| `workflow_tasks` | ORG | REMOVED | — | Org-wide |
| `workflow_schedules` | ORG | REMOVED | — | Org-wide |
| `automation_rules` | ORG | REMOVED | — | Org-wide |
| `system_settings` | ORG | REMOVED | — | Org-wide |
| `audit_logs` | ORG | REMOVED | — | Org-wide |
| `activity_logs` | ORG | REMOVED | — | Org-wide |
| `scheduled_tasks` | ORG | REMOVED | — | Org-wide |
| `backups` | ORG | REMOVED | — | Org-wide |
| `system_health` | ORG | REMOVED | — | Org-wide |
| `maintenance_windows` | ORG | REMOVED | — | Org-wide |
| `plugins` | ORG | REMOVED | — | Org-wide |
| `plugin_settings` | ORG | REMOVED | — | Org-wide |
| `personal_access_tokens` | ORG | REMOVED | — | Org-wide |

---

## Appendix B: Scalability Analysis

This architecture supports:

| Scenario | Support |
|----------|---------|
| 1 school (simple) | One org, one school, school_id = same for all users |
| 10 schools (school group) | One org, 10 schools, org-level admin sees all |
| 100 schools (large group) | One org, 100 schools, hierarchical school structure |
| University (multi-campus) | One org, campuses as schools, departments per campus |
| Educational group (holding) | One org per entity, central dashboard for cross-org reporting |
| International campuses | One org, schools in different countries, different currencies/timezones |

**Database sizing per organization:**
- 10 schools × 500 students × 10 years = 50,000 enrollment records (manageable)
- 100 schools × 2,000 students = 200,000 students (requires indexing optimization)
- Library + Transport data scales linearly with schools

**Performance considerations:**
- School-scoped queries are naturally bounded by the tenant database
- Org-wide queries (academic years, programs) are small datasets
- Recommended: composite indexes on `(school_id, status)` for all school-scoped tables
- Recommended: cache org-wide reference data (programs, academic years)

---

*End of Architecture Report*
