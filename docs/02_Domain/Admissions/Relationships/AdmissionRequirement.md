# Admission Requirement Relationships

## Purpose

This document defines the relationships between the Admission Requirement entity and other entities within the Admissions domain.

Admission Requirements define the eligibility conditions that Applicants must satisfy before they can be admitted into the institution.

They serve as configurable business rules for the admission process.

---

# Relationship Overview

Admission Requirements are referenced throughout the admission process to validate applicant eligibility.

Each Requirement may be mandatory or optional depending on the Admission Cycle, Academic Program, Academic Level, or institutional policy.

---

# Entity Relationships

## Admission Cycle

Relationship

Admission Requirement belongs to one Admission Cycle.

Cardinality

Many Requirements

↓

One Admission Cycle

---

## Academic Program

Relationship

Admission Requirement may belong to one Academic Program.

Cardinality

Many Requirements

↓

One Academic Program

Example

Computer Science

Business Studies

General Science

Arts

---

## Academic Level

Relationship

Admission Requirement may belong to one Academic Level.

Cardinality

Many Requirements

↓

One Academic Level

Example

Primary

Junior Secondary

Senior Secondary

---

## Admission Application

Relationship

Admission Applications must satisfy multiple Admission Requirements.

Cardinality

Many-to-Many

Example

Application

↓

Requirement 1 ✓

Requirement 2 ✓

Requirement 3 ✗

---

## Admission Document

Relationship

One Requirement may require multiple supporting documents.

Cardinality

One-to-Many

Example

Requirement

↓

Birth Certificate

Medical Certificate

Transcript

---

## Admission Checklist

Relationship

Admission Requirements generate Checklist Items.

Cardinality

One-to-Many

Flow

Requirement

↓

Checklist Item

↓

Verification

---

## Admission Decision

Relationship

Requirement validation contributes to the Admission Decision.

Cardinality

Many Requirements

↓

One Decision

---

## Admission Offer

Relationship

Only Applications satisfying mandatory Requirements may receive an Admission Offer.

Cardinality

Indirect Relationship

---

## Student

Relationship

Only Applicants satisfying mandatory Requirements become Students.

Cardinality

Indirect Relationship

---

# Relationship Diagram

Admission Cycle

↓

Admission Requirement

├── Academic Program

├── Academic Level

├── Admission Applications

├── Admission Documents

├── Admission Checklist

└── Admission Decision

↓

Admission Offer

↓

Student

---

# Cascade Rules

Deleting a Requirement is prohibited while it is referenced by active Applications.

Archiving a Requirement preserves historical validation records.

Modifying a Requirement does not affect completed Admission Cycles.

---

# Integrity Rules

Every Requirement belongs to one Admission Cycle.

Mandatory Requirements must be completed before approval.

Requirement validation must be completed before Admission Decisions are published.

Archived Requirements remain available for audit purposes.

---

# Related Models

- AdmissionRequirement
- AdmissionCycle
- AdmissionApplication
- AdmissionDocument
- AdmissionChecklist
- AdmissionDecision
- AdmissionOffer
- AcademicProgram
- AcademicLevel
- Student

---

# Future Improvements

- Country-specific Requirement Templates.
- Ministry Requirement Libraries.
- Automatic Requirement Assignment.
- AI Eligibility Validation.
- Dynamic Conditional Requirements.
- Rule Engine Integration.