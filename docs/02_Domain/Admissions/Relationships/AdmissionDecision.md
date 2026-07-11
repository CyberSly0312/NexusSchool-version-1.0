# Admission Decision Relationships

## Purpose

This document defines the relationships between the Admission Decision entity and all related entities within the Admissions domain.

The Admission Decision represents the official institutional outcome of an Admission Application after all evaluations have been completed.

It consolidates interview results, examination scores, document verification, eligibility validation, and institutional policies before producing the final admission outcome.

---

# Relationship Overview

The Admission Decision acts as the final evaluation point of the Admissions process.

Every accepted Applicant proceeds toward Admission Offer generation, while rejected or waitlisted Applicants remain within the historical admission records.

---

# Entity Relationships

## Admission Application

Relationship

Admission Decision belongs to one Admission Application.

Cardinality

One-to-One

Example

Application

↓

Admission Decision

---

## Applicant

Relationship

Admission Decision concerns one Applicant.

Cardinality

Many Decisions

↓

One Applicant

---

## Admission Cycle

Relationship

Admission Decision belongs to one Admission Cycle.

Cardinality

Many Decisions

↓

One Admission Cycle

---

## Admission Interview

Relationship

Admission Decision considers Interview Results.

Cardinality

Many Interviews

↓

One Admission Decision

---

## Admission Exam

Relationship

Admission Decision considers Examination Results.

Cardinality

Many Exams

↓

One Admission Decision

---

## Admission Requirement

Relationship

Admission Decision validates Requirement Compliance.

Cardinality

Many Requirements

↓

One Admission Decision

---

## Admission Document

Relationship

Verified Documents support the Admission Decision.

Cardinality

Many Documents

↓

One Admission Decision

---

## Admission Offer

Relationship

Approved Decisions generate one Admission Offer.

Cardinality

One-to-One

Flow

Admission Decision

↓

Admission Offer

---

## Admission Acceptance

Relationship

Accepted Offers generate one Admission Acceptance.

Cardinality

Indirect Relationship

---

## Student

Relationship

Accepted Decisions create one Student.

Cardinality

One-to-One

Flow

Decision

↓

Student

---

## Enrollment

Relationship

Accepted Decisions ultimately generate one Enrollment.

Cardinality

One-to-One

Flow

Decision

↓

Student

↓

Enrollment

---

## Notification System

Relationship

Decision publication generates Notifications.

Cardinality

One-to-Many

---

## Reporting

Relationship

Admission Decisions contribute to Admission Analytics and Reports.

Cardinality

Many Decisions

↓

Reporting Module

---

# Relationship Diagram

Admission Application

↓

Admission Decision

├── Applicant

├── Admission Cycle

├── Admission Interview

├── Admission Exam

├── Admission Requirement

├── Admission Document

├── Admission Offer

│      ↓

│ Admission Acceptance

│      ↓

│ Student

│      ↓

│ Enrollment

├── Notifications

└── Reporting

---

# Cascade Rules

Deleting an Admission Decision is prohibited after publication.

Approved Decisions become immutable.

Revoking an Admission Decision automatically invalidates pending Admission Offers.

Historical Decisions remain permanently available for auditing.

---

# Integrity Rules

Every Admission Decision belongs to one Admission Application.

Only one active Admission Decision may exist per Admission Application.

Accepted Decisions generate one Admission Offer.

Rejected Decisions cannot generate Admission Offers.

Every Student created from Admissions references one Admission Decision.

---

# Related Models

- AdmissionDecision
- AdmissionApplication
- AdmissionCycle
- AdmissionInterview
- AdmissionExam
- AdmissionRequirement
- AdmissionDocument
- AdmissionOffer
- AdmissionAcceptance
- Student
- Enrollment

---

# Future Improvements

- AI Decision Recommendations.
- Multi-level Approval Workflow.
- Decision Appeals.
- Scholarship Recommendation Engine.
- Automated Eligibility Scoring.
- Ministry Decision Synchronization.