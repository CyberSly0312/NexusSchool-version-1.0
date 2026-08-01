# Nexus Engineering Handbook v1.0

---

# 1. Purpose

The Nexus Engineering Handbook defines the official engineering practices for the Nexus School ERP.

Its objective is to ensure that every contributor produces software that is:

* consistent;
* maintainable;
* testable;
* scalable;
* secure;
* aligned with the project architecture.

This handbook applies to every source file committed to the repository.

---

# 2. Engineering Philosophy

Every engineer working on Nexus School shall remember one principle:

> **We are building a long-term ERP platform, not a short-term Laravel application.**

Every implementation must prioritize long-term maintainability over short-term speed.

---

# 3. Engineering Values

All development must follow these values.

## Simplicity

Choose the simplest solution that correctly solves the business problem.

---

## Readability

Code is read far more often than it is written.

Optimize for readability.

---

## Explicitness

Magic behavior should be avoided whenever it reduces clarity.

---

## Consistency

Consistency is preferred over personal coding style.

---

## Business Orientation

Business rules belong to the Domain.

Never inside the UI.

---

# 4. Development Workflow

Every task follows this sequence.

```text
Issue

↓

Business Analysis

↓

Documentation

↓

Architecture Review

↓

Development

↓

Testing

↓

Review

↓

Merge

↓

Release
```

---

# 5. Domain Structure

Every Domain follows the same organization.

```text
Domain/

Actions/

DTOs/

Enums/

Events/

Exceptions/

Jobs/

Listeners/

Models/

Policies/

Queries/

Resources/

Rules/

Services/

ValueObjects/

Tests/
```

Additional folders may exist only when justified.

---

# 6. Responsibilities

## Controller

Responsible for:

* request validation;
* authorization;
* orchestration.

Controllers never contain business rules.

---

## Service

Responsible for business logic.

Services coordinate operations across models.

---

## Model

Responsible for persistence and relationships.

Models shall never become business service classes.

---

## Policy

Responsible only for authorization.

Policies never execute business actions.

---

## Resource

Responsible only for presentation.

Filament Resources never become service layers.

---

# 7. Naming Rules

Names must describe business meaning.

Bad:

```php
doStuff()
```

Good:

```php
PromoteStudentAction
```

Bad:

```php
handle()
```

Good:

```php
GenerateReportCard()
```

Business vocabulary always wins.

---

# 8. Business Rules

Every business rule must satisfy:

* documented;
* testable;
* deterministic;
* domain owned.

Undocumented business rules are prohibited.

---

# 9. Dependencies

Dependencies flow downward.

```text
UI

↓

Application

↓

Domain

↓

Infrastructure
```

Reverse dependencies are forbidden.

---

# 10. Events

Events describe completed business actions.

Examples:

StudentEnrolled

TeacherAssigned

PaymentReceived

AcademicYearOpened

Events should describe facts.

Not commands.

---

# 11. Services

Services should expose meaningful operations.

Good:

CreateEnrollment()

TransferStudent()

GraduateStudent()

ArchiveAcademicYear()

Avoid service methods that simply wrap Eloquent CRUD.

---

# 12. DTO Usage

DTOs should be used whenever data crosses architectural boundaries.

Especially:

* Services

* Jobs

* APIs

* External integrations

DTOs improve consistency and testability.

---

# 13. Validation

Validation belongs at the application's entry points.

Prefer:

Request Objects

Validation Rules

Custom Rules

Business validation remains inside Services.

---

# 14. Errors

Business exceptions should be explicit.

Never throw generic exceptions for business scenarios.

Example:

StudentAlreadyGraduatedException

instead of

Exception()

---

# 15. Testing Strategy

Every business feature requires:

Unit Tests

Feature Tests

Authorization Tests

Tenant Isolation Tests

Regression Tests

Critical domains require integration tests.

---

# 16. Performance

Optimize only after measuring.

Premature optimization is discouraged.

However:

Avoid N+1 queries.

Use eager loading.

Cache expensive operations.

Queue long-running processes.

---

# 17. Security

Every feature must answer:

Who may execute it?

Which tenant owns the data?

What should be audited?

Could sensitive information leak?

Security is everyone's responsibility.

---

# 18. Documentation Rule

Every significant feature updates:

Documentation

Tests

Release Notes (when applicable)

Code without documentation is considered incomplete.

---

# 19. Code Review Checklist

Before approval verify:

✓ Architecture respected

✓ Domain respected

✓ Authorization implemented

✓ Validation complete

✓ Tests passing

✓ Naming correct

✓ Documentation updated

✓ No duplicated logic

✓ Multi-tenant safe

✓ Performance acceptable

---

# 20. Engineering Principle

Develop software that another engineer can understand six months later without asking its original author.

Maintainability is a feature.

---

# Final Statement

The Nexus Engineering Handbook is the authoritative engineering reference for the Nexus School ERP.

Every contributor, whether human or AI-assisted, shall follow these practices to preserve architectural integrity, code quality, and long-term maintainability.