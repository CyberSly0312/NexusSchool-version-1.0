# Architecture Decision Records

This directory contains Architecture Decision Records (ADRs) for Nexus School ERP.

## What is an ADR?

An ADR is a short document that captures a significant architectural decision along with its context and consequences. Each record describes a choice that affects the structure, behavior, or quality of the system.

## Why ADRs?

- They provide a permanent, searchable history of why the system is built the way it is.
- They help new contributors understand the reasoning behind architectural choices.
- They force explicit consideration of tradeoffs before implementation.

## Format

Each ADR follows this structure:

| Field | Description |
|-------|-------------|
| **Title** | Short name for the decision (e.g., "Use Database-per-Tenant Multi-tenancy") |
| **Status** | Proposed, Accepted, Deprecated, or Superseded |
| **Context** | What situation necessitated this decision? |
| **Decision** | What was decided? |
| **Consequences** | What are the positive and negative outcomes? |

## Naming Convention

Files are named `DEC-{number}-{short-title}.md` in sequence order.

## Current Decisions

| ADR | Title | Status |
|-----|-------|--------|
| [DEC-001](DEC-001-Shared-Database-Multi-Tenant.md) | Database-per-Tenant Multi-tenancy | Accepted |
| [DEC-002](DEC-002-Domain-Driven-Architecture.md) | Domain-Driven Architecture | Accepted |
| [DEC-003](DEC-003-Laravel-Modular-Structure.md) | Laravel Modular Structure | Accepted |
| [DEC-004](DEC-004-UUID-Primary-Keys.md) | ULID Primary Keys | Accepted |
| [DEC-005](DEC-005-Event-Driven-Architecture.md) | Event-Driven Architecture | Accepted |
| [DEC-006](DEC-006-Laravel-Policies-RBAC.md) | Laravel Policies + RBAC | Accepted |
| [DEC-007](DEC-007-Queue-First-Architecture.md) | Queue-First Architecture | Accepted |
| [DEC-008](DEC-008-Repository-Service-Pattern.md) | Repository + Service Pattern | Accepted |
| [DEC-009](DEC-009-Storage-Abstraction.md) | Storage Abstraction | Accepted |
| [DEC-010](DEC-010-API-Versioning.md) | API Versioning | Accepted |
| [DEC-011](DEC-011-Shared-Authentication.md) | Shared Authentication | Accepted |
| [DEC-012](DEC-012-Soft-Deletes-Strategy.md) | Soft Deletes Strategy | Accepted |
| [DEC-013](DEC-013-Use-FilamentPHP.md) | Use FilamentPHP | Accepted |
| [DEC-014](DEC-014-Audit-Everything.md) | Audit Everything | Accepted |
| [DEC-015](DEC-015-Tenant-Isolation.md) | Tenant Isolation | Accepted |
| [DEC-016](DEC-016-Laravel-Events.md) | Laravel Events | Accepted |
| [DEC-017](DEC-017-Caching-Strategy.md) | Caching Strategy | Accepted |
| [DEC-018](DEC-018-Testing-Strategy.md) | Testing Strategy | Accepted |
| [DEC-019](DEC-019-Security-First.md) | Security First | Accepted |
| [DEC-020](DEC-020-Future-Microservices.md) | Future Microservices | Proposed |
