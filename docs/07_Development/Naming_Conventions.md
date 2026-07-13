# Naming Conventions

## Purpose

Provide consistent naming conventions throughout the Nexus School codebase.

---

# Classes

PascalCase

Examples

Student

AcademicYear

FinanceService

AttendanceController

---

# Interfaces

Suffix:

Interface

Example

PaymentGatewayInterface

---

# Traits

Suffix:

Trait

Example

HasAuditTrailTrait

---

# Enums

Suffix:

Enum

Examples

GenderEnum

AttendanceStatusEnum

PaymentStatusEnum

---

# Services

Suffix:

Service

Examples

StudentService

BillingService

EnrollmentService

---

# Controllers

Suffix:

Controller

Examples

StudentController

FinanceController

---

# Requests

Suffix:

Request

Examples

StoreStudentRequest

UpdateTeacherRequest

---

# Policies

Suffix:

Policy

Examples

StudentPolicy

InvoicePolicy

---

# Events

Past tense.

Examples

StudentCreated

InvoicePaid

AttendanceRecorded

---

# Listeners

Describe the action.

Examples

SendWelcomeEmail

GenerateInvoice

NotifyParents

---

# Jobs

Describe the task.

Examples

ExportStudentsJob

GenerateReportJob

BackupDatabaseJob

---

# Notifications

Suffix:

Notification

Examples

PaymentReceivedNotification

WelcomeNotification

---

# Migrations

Examples

create_students_table

add_phone_to_students_table

create_fee_structures_table

---

# Database Tables

Plural

snake_case

Examples

students

academic_years

fee_structures

---

# Database Columns

snake_case

Examples

student_number

created_at

updated_at

deleted_at

---

# Foreign Keys

Singular + _id

Examples

student_id

teacher_id

tenant_id

---

# Pivot Tables

Alphabetical order

Examples

role_permission

student_subject

teacher_subject

---

# Route Names

Dot notation

Examples

students.index

students.store

finance.invoices.index

attendance.record

---

# Route URLs

Plural

Examples

/students

/teachers

/invoices

---

# API Endpoints

Plural

/api/v1/students

/api/v1/teachers

/api/v1/payments

---

# Configuration Files

snake_case

Examples

billing.php

attendance.php

finance.php

---

# Environment Variables

UPPER_SNAKE_CASE

Examples

APP_NAME

QUEUE_CONNECTION

MAIL_HOST

DB_CONNECTION

TENANT_MODE

---

# Constants

UPPER_SNAKE_CASE

Examples

MAX_LOGIN_ATTEMPTS

DEFAULT_CURRENCY

DEFAULT_LANGUAGE