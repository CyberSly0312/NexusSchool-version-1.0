# Nexus School

# Role-Based Access Control (RBAC)

**Document ID:** NS-ARCH-008

**Version:** 1.0.0

**Owner:** Nexus Technologies

**Status:** Active

---

# Purpose

This document defines the Role-Based Access Control (RBAC) model of Nexus School.

RBAC controls what authenticated users are allowed to do by assigning permissions through roles.

The RBAC system is tenant-aware, extensible, auditable, and designed for enterprise-scale educational institutions.

---

# Objectives

The RBAC system must:

- Simplify permission management.
- Support multiple user roles.
- Allow custom school roles.
- Prevent privilege escalation.
- Integrate with Laravel Policies.
- Support future enterprise features.

---

# RBAC Components

The RBAC model consists of five primary components.

```
User

↓

Role

↓

Permission

↓

Policy

↓

Protected Resource
```

---

# Users

Users represent authenticated identities.

A user may belong to one or more tenants.

A user may have one or multiple roles.

Examples:

- Teacher
- Student
- Parent
- Accountant
- Principal

---

# Roles

Roles represent job functions.

Examples:

Platform Roles

- Super Administrator
- Support Engineer

School Roles

- School Administrator
- Principal
- Vice Principal
- Accountant
- HR Manager
- Librarian
- Receptionist

Academic Roles

- Teacher
- Head Teacher
- Department Head
- Examination Officer

Student Roles

- Student
- Class Representative

Parent Roles

- Parent
- Guardian

---

# Permissions

Permissions define individual actions.

Examples:

Student Module

- student.view
- student.create
- student.update
- student.delete
- student.promote

Teacher Module

- teacher.view
- teacher.assign_subject
- teacher.manage_attendance

Finance Module

- finance.view
- finance.payment.create
- finance.payment.refund

Reports

- report.view
- report.export
- report.print

---

# Permission Groups

Permissions are grouped by module.

Example

Academic

```
academic.view
academic.create
academic.update
academic.delete
```

Finance

```
finance.view
finance.payment.create
finance.payment.update
finance.payment.refund
```

Grouping improves administration.

---

# Role Assignment

A user may receive:

- Direct Role
- Multiple Roles
- Temporary Role (future)

Example

Teacher

+

Examination Officer

+

Club Supervisor

---

# Effective Permissions

Permissions are calculated as:

```
All Assigned Roles

↓

Merge Permissions

↓

Remove Revoked Permissions

↓

Evaluate Policies

↓

Grant Access
```

---

# Custom Roles

Every school may create custom roles.

Example:

- ICT Coordinator
- Guidance Counselor
- Sports Director
- Laboratory Manager

Platform updates must never overwrite custom roles.

---

# Default Roles

Each new tenant automatically receives predefined roles.

Minimum roles include:

- Administrator
- Principal
- Teacher
- Student
- Parent
- Accountant

Schools may modify or extend these roles.

---

# Permission Inheritance

Permissions are inherited through assigned roles.

Example

Principal

inherits

Teacher permissions

+

Administrative permissions

+

Reporting permissions

Inheritance reduces duplication.

---

# Permission Overrides

Future versions may support:

- Explicit Allow
- Explicit Deny

Explicit Deny always takes precedence.

---

# Ownership Rules

Some permissions require ownership validation.

Example

Teacher

Can edit:

Own attendance

Own timetable

Own profile

Cannot edit:

Another teacher's profile

---

# Tenant Awareness

Roles exist inside tenant boundaries.

Example

Teacher

School A

≠

Teacher

School B

Even if the role names are identical.

---

# Super Administrator

Platform administrators bypass school-level RBAC.

Capabilities include:

- Tenant Management
- Billing
- Global Monitoring
- System Configuration

Platform administrators never become members of tenant organizations.

---

# Role Management

School Administrators may:

- Create Roles
- Update Roles
- Delete Roles
- Assign Permissions
- Assign Roles

Subject to platform restrictions.

---

# Permission Naming Convention

Permissions follow:

```
module.action
```

Examples

```
student.view

student.create

teacher.assign

finance.payment.create

attendance.export
```

---

# Audit Requirements

Every RBAC change generates an audit event.

Examples:

- Role Created
- Role Deleted
- Permission Assigned
- Permission Removed
- User Role Changed

---

# Future Capabilities

The RBAC architecture supports future features such as:

- Role Templates
- Permission Profiles
- Time-Based Permissions
- Delegated Administration
- Dynamic Permissions
- ABAC Integration

---

# Security Principles

RBAC follows:

- Least Privilege
- Deny by Default
- Explicit Assignment
- Tenant Isolation
- Auditability
- Predictability

---

# Database Model

Primary entities include:

- users
- roles
- permissions
- role_user
- permission_role
- permission_user (optional)
- tenants

Relationships

```
User

↓

Role

↓

Permission

↓

Policy

↓

Protected Resource
```

---

# Summary

RBAC provides a flexible and secure authorization model capable of supporting educational institutions of all sizes while remaining easy to manage and extend.

---

# Conclusion

The RBAC model ensures that permissions are assigned consistently, enforced securely, and audited continuously, forming the foundation of access control within Nexus School.

---

**End of Document**
