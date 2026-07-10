# Admission Application Relationships

## Purpose

This document defines the relationships between the Admission Application entity and all other entities within the Admissions domain and related domains.

The Admission Application serves as the central entity of the Admissions domain, connecting Applicants, Admission Cycles, Documents, Interviews, Examinations, Decisions, Offers, Acceptances, and ultimately the Academic domain.

---

# Relationship Overview

Admission Application is the parent entity for the admission process.

Every admission-related activity originates from an Admission Application.

---

# Entity Relationships

## Applicant

Relationship

Admission Application belongs to one Applicant.

Cardinality

Many Applications

↓

One Applicant

Example

Applicant

↓

Admission Application 2026

Admission Application 2027

---

## Admission Cycle

Relationship

Admission Application belongs to one Admission Cycle.

Cardinality

Many Applications

↓

One Admission Cycle

Example

Admission Cycle 2026

↓

2,500 Applications

---

## Admission Requirement

Relationship

Admission Application must satisfy multiple Admission Requirements.

Cardinality

Many-to-Many

Admission Application

↔

Admission Requirement

Example

Application

↓

Birth Certificate

Medical Certificate

BEPC

Entrance Exam

---

## Admission Document

Relationship

Admission Application has many Admission Documents.

Cardinality

One-to-Many

Admission Application

↓

Birth Certificate

Transcript

Medical Certificate

Photographs

Passport

---

## Admission Interview

Relationship

Admission Application may have multiple Interviews.

Cardinality

One-to-Many

Example

Initial Interview

↓

Academic Interview

↓

Scholarship Interview

---

## Admission Exam

Relationship

Admission Application may have multiple Admission Exams.

Cardinality

One-to-Many

Example

Mathematics

English

Science

IQ Test

---

## Admission Decision

Relationship

Admission Application has one final Admission Decision.

Cardinality

One-to-One

Example

Application

↓

Accepted

Rejected

Waitlisted

Deferred

---

## Admission Offer

Relationship

Admission Decision may generate one Admission Offer.

Cardinality

One-to-One

Flow

Application

↓

Decision

↓

Offer

---

## Admission Acceptance

Relationship

Admission Offer may produce one Acceptance.

Cardinality

One-to-One

Flow

Offer

↓

Acceptance

---

## Admission Checklist

Relationship

Admission Application has one Admission Checklist.

Cardinality

One-to-One

Example

Application

↓

Checklist

↓

Enrollment Ready

---

## Student

Relationship

Accepted Admission Applications create one Student.

Cardinality

One-to-One

Flow

Application

↓

Student

---

## Enrollment

Relationship

Accepted Applications generate one Enrollment.

Cardinality

One-to-One

Flow

Application

↓

Student

↓

Enrollment

---

## Parent

Relationship

Admission Application may register one or more Parents or Guardians.

Cardinality

One-to-Many

Example

Application

↓

Father

Mother

Guardian

---

## User Account

Relationship

Application Acceptance may generate User Accounts.

Cardinality

One-to-Many

Example

Student Account

Parent Account

---

## Academic Year

Relationship

Admission Application belongs to one Academic Year through the Admission Cycle.

Cardinality

Many Applications

↓

One Academic Year

---

## Campus

Relationship

Admission Application belongs to one Campus.

Cardinality

Many Applications

↓

One Campus

---

# Relationship Diagram

Applicant

↓

Admission Application

├── Admission Cycle

├── Admission Requirement

├── Admission Document

├── Admission Interview

├── Admission Exam

├── Admission Decision

│      ↓

│   Admission Offer

│      ↓

│ Admission Acceptance

├── Admission Checklist

│

├── Student

│      ↓

└── Enrollment

---

# Cascade Rules

Deleting an Application is prohibited after submission.

Deleting an Applicant does not remove historical Applications.

Deleting an Admission Cycle is prohibited when Applications exist.

Deleting a Student never deletes the originating Application.

Deleting an Enrollment never deletes the Admission history.

---

# Integrity Rules

Every Application must belong to exactly one Applicant.

Every Application must belong to exactly one Admission Cycle.

Every Accepted Application must create exactly one Student.

Every Student created from Admissions must originate from one Application.

Every Enrollment created through Admissions must reference its originating Application.

---

# Related Models

- AdmissionApplication
- Applicant
- AdmissionCycle
- AdmissionRequirement
- AdmissionDocument
- AdmissionInterview
- AdmissionExam
- AdmissionDecision
- AdmissionOffer
- AdmissionAcceptance
- AdmissionChecklist
- Student
- Enrollment

---

# Future Improvements

- Multi-campus applications.
- Cross-campus transfer applications.
- International applicant support.
- Scholarship application integration.
- Government admission synchronization.
- AI relationship validation.