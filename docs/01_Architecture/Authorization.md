# Nexus School

# Authorization Architecture

**Document ID:** NS-ARCH-007

**Version:** 1.0.0

**Owner:** Nexus Technologies

**Status:** Active

---

# Purpose

This document defines the authorization architecture of Nexus School.

While authentication verifies who the user is, authorization determines what the user is allowed to do.

Authorization ensures that every action performed within the platform is explicitly permitted according to business rules, user roles, tenant boundaries, and resource ownership.

---

# Objectives

The authorization system must:

- Protect sensitive data.
- Enforce Role-Based Access Control (RBAC).
- Support granular permissions.
- Respect tenant isolation.
- Prevent privilege escalation.
- Remain extensible.
- Integrate with Laravel Policies.

---

# Authorization Principles

Authorization follows these principles:

- Least Privilege
- Deny by Default
- Explicit Permissions
- Tenant Isolation
- Policy-Based Access Control
- Resource Ownership
- Auditable Decisions

Every request must be evaluated before accessing business resources.

---

# Authorization Flow

```
Authenticated User

↓

Resolve Tenant

↓

Resolve Role(s)

↓

Load Permissions

↓

Evaluate Policy

↓

Grant or Deny Access

↓

Audit Decision
```

---

# Authorization Layers

Authorization is enforced through multiple layers.

## Layer 1 — Authentication

Verify the user's identity.

---

## Layer 2 — Tenant Membership

Verify the user belongs to the active tenant.

---

## Layer 3 — Role Validation

Determine the user's assigned role(s).

---

## Layer 4 — Permission Validation

Check whether the required permission exists.

---

## Layer 5 — Policy Evaluation

Evaluate resource-specific business rules.

---

## Layer 6 — Ownership Validation

Verify ownership where applicable.

Example:

A parent should only access records belonging to their own children.

---

# Roles

Typical platform roles include:

Platform

- Super Administrator
- Platform Support

School

- School Administrator
- Principal
- Vice Principal
- Accountant
- HR Officer
- Librarian
- Receptionist

Academic

- Teacher
- Head of Department
- Class Teacher

Users

- Student
- Parent

Additional custom roles may be created by each school.

---

# Permissions

Permissions represent individual capabilities.

Examples:

Academic

- academic.view
- academic.create
- academic.update
- academic.delete

Students

- student.view
- student.create
- student.update
- student.promote
- student.graduate

Finance

- finance.view
- finance.payment.create
- finance.payment.refund

Reports

- report.generate
- report.export

Administration

- settings.manage
- users.manage
- roles.manage

Permissions should use a consistent naming convention.

---

# Resource Ownership

Certain resources belong to specific users.

Examples:

Teacher

Can edit:

- Their own profile
- Their own attendance
- Their own timetable

Cannot edit:

- Another teacher's profile

Parent

Can view:

- Their children's reports
- Their children's attendance

Cannot view:

- Other students' records

---

# Laravel Policies

Every protected model should have a corresponding Policy.

Examples:

StudentPolicy

TeacherPolicy

FinancePolicy

AttendancePolicy

ReportPolicy

Policies contain business authorization logic.

---

# Gates

Gates may be used for global capabilities.

Examples:

Access Dashboard

Manage Platform

View Analytics

Manage Tenant

---

# Permission Resolution

Permissions are resolved using the following hierarchy:

```
User

↓

Roles

↓

Permissions

↓

Policies

↓

Decision
```

Users may inherit permissions from multiple roles.

---

# Multi-Role Support

A user may have multiple roles.

Example:

Teacher

+

Department Head

+

Examination Officer

Effective permissions are the union of assigned roles.

---

# Tenant Isolation

Authorization must never bypass tenant boundaries.

Example:

Teacher A

School Alpha

↓

Cannot access

↓

School Beta Student

Even if identical roles exist.

---

# Temporary Permissions

Future versions may support:

- Temporary Delegation
- Time-Limited Permissions
- Emergency Access
- Acting Roles

All temporary permissions must be audited.

---

# Authorization Events

The following events should be logged:

- Permission Granted
- Permission Denied
- Role Assigned
- Role Removed
- Permission Changed
- Policy Failure

These events are forwarded to the Audit module.

---

# Administrative Delegation

School Administrators may:

- Create Roles
- Assign Permissions
- Manage Users

They may not perform platform-level operations.

Platform Administrators retain full platform authority.

---

# Permission Naming Standard

Permission names should follow this format:

```
module.action
```

Examples:

student.view

student.create

finance.refund

attendance.export

report.generate

settings.update

This naming convention improves readability and maintainability.

---

# Security Principles

Authorization decisions must be:

- Explicit
- Consistent
- Auditable
- Predictable
- Tenant Aware

Access should never be granted implicitly.

---

# Future Evolution

Future versions may include:

- Attribute-Based Access Control (ABAC)
- Dynamic Permissions
- Context-Aware Authorization
- AI-Assisted Risk Evaluation
- Government Compliance Profiles

---

# Authorization Summary

Authorization protects every business operation by combining tenant isolation, role-based permissions, ownership validation, and policy evaluation.

It forms the second major security boundary after authentication.

---

# Conclusion

A robust authorization architecture ensures that every user accesses only the resources they are permitted to use while preserving security, privacy, and data integrity across the platform.

---

**End of Document**