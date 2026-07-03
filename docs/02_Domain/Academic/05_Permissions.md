# Nexus School

# Academic Domain Permissions

---

**Document ID:** NS-DOM-ACA-005

**Version:** 1.0.0

**Status:** Approved

**Owner:** Nexus Technologies

**Author:** Nexus School Engineering Team

**Review Cycle:** Major Releases

---

# Purpose

This document defines the authorization model for the Academic Domain.

Permissions determine which users may perform specific actions within the Academic module while maintaining security, accountability, and tenant isolation.

Every permission is enforced through Laravel Policies, Gates, Roles, and Permissions.

---

# Objectives

The permission system must:

- Protect academic data.
- Enforce Role-Based Access Control (RBAC).
- Support multi-tenant environments.
- Prevent unauthorized modifications.
- Maintain auditability.
- Support future custom roles.

---

# Permission Principles

The Academic Domain follows these principles:

- Least Privilege
- Separation of Duties
- Role-Based Access
- Tenant Isolation
- Audit Logging
- Explicit Authorization

---

# User Roles

Typical roles include:

- Super Administrator
- Tenant Administrator
- Academic Administrator
- Registrar
- Principal
- Vice Principal
- Head of Department
- Teacher
- Student
- Parent
- Auditor

Each institution may create additional custom roles.

---

# Permission Categories

Permissions are grouped by business capability.

## Academic Year

- academic_year.view
- academic_year.create
- academic_year.update
- academic_year.delete
- academic_year.open
- academic_year.close

---

## Academic Terms

- academic_term.view
- academic_term.create
- academic_term.update
- academic_term.delete

---

## Education Levels

- education_level.view
- education_level.create
- education_level.update
- education_level.delete

---

## Departments

- department.view
- department.create
- department.update
- department.delete

---

## Programs

- program.view
- program.create
- program.update
- program.delete

---

## Classes

- class.view
- class.create
- class.update
- class.delete
- class.assign_teacher
- class.assign_students

---

## Sections

- section.view
- section.create
- section.update
- section.delete

---

## Subjects

- subject.view
- subject.create
- subject.update
- subject.delete
- subject.assign_program

---

## Curriculum

- curriculum.view
- curriculum.create
- curriculum.update
- curriculum.publish

---

## Timetable

- timetable.view
- timetable.generate
- timetable.publish
- timetable.update
- timetable.delete

---

# Default Role Matrix

| Permission | Super Admin | Tenant Admin | Academic Admin | Teacher | Student | Parent |
|------------|-------------|--------------|----------------|----------|----------|---------|
| View Academic Year | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Create Academic Year | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Close Academic Year | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Create Class | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Assign Teacher | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| View Timetable | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Publish Timetable | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |

---

# Authorization Rules

Every protected action requires:

- Authenticated user
- Active tenant
- Valid role
- Required permission
- Policy approval

Authorization is denied if any requirement fails.

---

# Multi-Tenant Rules

Users may only access academic resources belonging to their tenant.

Cross-tenant access is strictly prohibited except for Super Administrators operating at the platform level.

---

# Sensitive Operations

The following operations require elevated privileges:

- Opening an Academic Year
- Closing an Academic Year
- Publishing a Curriculum
- Publishing Timetables
- Editing Historical Academic Records
- Manual Override of Academic Settings

These operations are fully audited.

---

# Audit Requirements

Every permission-sensitive operation records:

- User ID
- Tenant ID
- Action
- Resource
- Timestamp
- IP Address
- Device Information

---

# Future Enhancements

Future versions may include:

- Attribute-Based Access Control (ABAC)
- Time-Based Permissions
- Temporary Delegation
- Approval Workflows
- Emergency Access Procedures

---

# Related Documents

- 03_Business_Rules.md
- 07_API.md
- RBAC Documentation
- Authentication Architecture
- Authorization Architecture

---

# Summary

The Academic Domain Permission Model ensures secure, consistent, and auditable access to academic resources while supporting flexible role management across multiple educational institutions.

---

# Conclusion

Permissions are a critical component of Nexus School's security architecture. Every operation within the Academic Domain must be explicitly authorized before execution.

---

**End of Document**