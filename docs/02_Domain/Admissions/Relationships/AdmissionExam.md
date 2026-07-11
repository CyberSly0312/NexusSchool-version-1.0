# Admission Exam Relationships

## Purpose

This document defines the relationships between the Admission Exam entity and other entities within the Admissions domain and related domains.

Admission Exams provide standardized assessments used to evaluate Applicants before an Admission Decision is made.

Examinations may consist of one or multiple assessment sections and contribute significantly to admission rankings.

---

# Relationship Overview

Admission Exams evaluate Applicants according to institutional admission policies.

Each Examination belongs to one Admission Cycle and one Admission Application while contributing to the overall Admission Decision.

---

# Entity Relationships

## Admission Application

Relationship

Admission Exam belongs to one Admission Application.

Cardinality

Many Exams

↓

One Admission Application

---

## Applicant

Relationship

Admission Exam evaluates one Applicant.

Cardinality

Many Exams

↓

One Applicant

---

## Admission Cycle

Relationship

Admission Exam belongs to one Admission Cycle.

Cardinality

Many Exams

↓

One Admission Cycle

---

## Exam Template

Relationship

Admission Exam uses one Examination Template.

Cardinality

Many Exams

↓

One Exam Template

Example

Science Entrance Exam

Business Admission Test

Language Placement Test

---

## Exam Section

Relationship

One Admission Exam contains multiple Sections.

Cardinality

One-to-Many

Example

Mathematics

English

Science

Logical Reasoning

Essay

---

## Examiner

Relationship

Admission Exams are supervised by one or more Examiners.

Cardinality

Many-to-Many

---

## Exam Result

Relationship

Each Admission Exam generates one Examination Result.

Cardinality

One-to-One

---

## Admission Decision

Relationship

Exam Results contribute to one Admission Decision.

Cardinality

Many Exams

↓

One Admission Decision

---

## Admission Checklist

Relationship

Completed Exams satisfy Checklist Items.

Cardinality

One-to-Many

---

## Ranking

Relationship

Exam Results contribute to Admission Rankings.

Cardinality

Many Results

↓

One Ranking

---

## Calendar

Relationship

Exam schedules are synchronized with the institutional Calendar.

Cardinality

Many Exams

↓

One Calendar

---

## Notification System

Relationship

Exam scheduling and publication generate Notifications.

Cardinality

One-to-Many

---

# Relationship Diagram

Admission Application

↓

Admission Exam

├── Applicant

├── Admission Cycle

├── Exam Template

├── Exam Sections

├── Examiners

├── Exam Result

├── Admission Decision

├── Admission Checklist

├── Ranking

├── Calendar

└── Notifications

---

# Cascade Rules

Deleting an Admission Exam is prohibited after completion.

Published Results become immutable.

Exam Results cannot exist without an Admission Exam.

Archived Exams remain available for reporting.

---

# Integrity Rules

Every Admission Exam belongs to one Admission Application.

Every Admission Exam belongs to one Admission Cycle.

Every completed Exam generates one Result.

Every published Result contributes to one Admission Decision.

Historical Exam Results remain permanently accessible.

---

# Related Models

- AdmissionExam
- AdmissionApplication
- Applicant
- AdmissionCycle
- AdmissionDecision
- AdmissionChecklist
- ExamTemplate
- ExamResult
- Calendar
- Notification

---

# Future Improvements

- Computer-Based Testing (CBT).
- Online Proctoring.
- AI Exam Monitoring.
- Adaptive Testing.
- Random Question Generation.
- Automatic Result Publication.
- National Examination Integration.