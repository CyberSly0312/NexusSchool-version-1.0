# Enrollment Business Policies

## Purpose

This document defines the business rules governing Student Enrollments.

An Enrollment represents the official academic registration of a Student during a specific Academic Year.

It is the central entity connecting Students to all academic and administrative processes.

---

# Creation Policy

## ENR-001

Only authorized users may create an Enrollment.

Allowed Roles

- Super Admin
- School Administrator
- Registrar

---

## ENR-002

Every Enrollment must reference:

- one Student
- one Academic Year
- one Program
- one Level
- one Classroom

Series and Class Section are optional.

---

## ENR-003

Only Active Academic Years may accept new Enrollments.

---

## ENR-004

Students cannot have more than one active Enrollment in the same Academic Year.

Unique Constraint

(student_id, academic_year_id)

---

## ENR-005

Students may have multiple Enrollments throughout their academic history.

Example

2025-2026

6A

↓

2026-2027

5A

↓

2027-2028

4A

---

# Validation Policy

## ENR-006

The selected Classroom must belong to the selected Level.

---

## ENR-007

If a Series is selected, it must belong to the selected Level.

---

## ENR-008

If a Class Section is selected, it must belong to the selected Classroom.

---

## ENR-009

The Classroom Capacity must not be exceeded.

---

## ENR-010

The Student must satisfy all admission requirements before Enrollment.

Examples

- Required documents submitted
- Previous academic record validated
- Admission approved

---

# Modification Policy

## ENR-011

Changing the assigned Classroom is permitted only before academic activities begin.

---

## ENR-012

Changing the Level is prohibited once grades or attendance records exist.

---

## ENR-013

Changing the Academic Year is prohibited after Enrollment creation.

---

## ENR-014

Every transfer between Classrooms must be recorded in the Student History.

---

# Academic Policy

## ENR-015

Only Active Enrollments may receive:

- Attendance
- Grades
- Examinations
- Report Cards

---

## ENR-016

Archived Enrollments become read-only.

---

## ENR-017

Promotion to the next Level creates a new Enrollment.

Historical Enrollments are never modified.

---

## ENR-018

Repeating a class also creates a new Enrollment.

The previous Enrollment remains unchanged.

---

# Financial Policy

## ENR-019

Enrollment may require payment validation before becoming Active.

The payment rules depend on the school's financial policy.

---

## ENR-020

Outstanding balances do not modify historical academic records.

---

# Archive Policy

## ENR-021

Archived Enrollments remain available for:

- Report Cards
- Academic History
- Statistics
- Ministry Reports

---

# Deletion Policy

## ENR-022

Deleting an Enrollment is prohibited if it is referenced by:

- Attendance
- Grades
- Payments
- Report Cards
- Library Loans
- Disciplinary Records

Instead:

Archive the Enrollment.

---

## ENR-023

Historical Enrollments cannot be deleted.

---

# Security Policy

## ENR-024

Every Enrollment modification must be recorded in the Audit Log.

Captured Information

- User
- Timestamp
- Previous Values
- New Values
- IP Address

---

# Future Policies

- Online enrollment
- Bulk enrollment
- Automatic promotion
- Transfer workflow
- Multi-campus enrollment
- Ministry synchronization