# Admission Offer Relationships

## Purpose

This document defines the relationships between the Admission Offer entity and other entities within the Admissions domain and related domains.

The Admission Offer represents the official admission proposal issued to an Applicant following an approved Admission Decision.

It serves as the contractual bridge between institutional approval and applicant acceptance.

---

# Relationship Overview

Admission Offers are generated only after successful Admission Decisions.

Each Offer remains valid until it is accepted, declined, expired, or revoked.

Accepted Offers initiate Student creation and Enrollment.

---

# Entity Relationships

## Admission Decision

Relationship

Admission Offer belongs to one Admission Decision.

Cardinality

One-to-One

Example

Admission Decision

↓

Admission Offer

---

## Admission Application

Relationship

Admission Offer references one Admission Application.

Cardinality

One-to-One

---

## Applicant

Relationship

Admission Offer is issued to one Applicant.

Cardinality

Many Offers

↓

One Applicant

---

## Admission Cycle

Relationship

Admission Offer belongs to one Admission Cycle.

Cardinality

Many Offers

↓

One Admission Cycle

---

## Academic Program

Relationship

Admission Offer specifies one Academic Program.

Cardinality

Many Offers

↓

One Academic Program

---

## Academic Level

Relationship

Admission Offer specifies one Academic Level.

Cardinality

Many Offers

↓

One Academic Level

---

## Campus

Relationship

Admission Offer is issued for one Campus.

Cardinality

Many Offers

↓

One Campus

---

## Admission Acceptance

Relationship

Admission Offer may generate one Admission Acceptance.

Cardinality

One-to-One

Flow

Admission Offer

↓

Admission Acceptance

---

## Student

Relationship

Accepted Offers generate one Student.

Cardinality

Indirect Relationship

---

## Enrollment

Relationship

Accepted Offers initiate one Enrollment.

Cardinality

Indirect Relationship

---

## Finance

Relationship

Admission Offers may generate Registration Fees and Initial Billing.

Cardinality

One-to-Many

Example

Admission Offer

↓

Registration Fee

Admission Deposit

Scholarship Adjustment

---

## Scholarship

Relationship

Admission Offers may include Scholarship Awards.

Cardinality

One-to-One

---

## Notification System

Relationship

Offer publication generates Notifications.

Cardinality

One-to-Many

---

## Reporting

Relationship

Admission Offers contribute to Admission Statistics.

Cardinality

Many Offers

↓

Reporting Module

---

# Relationship Diagram

Admission Decision

↓

Admission Offer

├── Admission Application

├── Applicant

├── Admission Cycle

├── Academic Program

├── Academic Level

├── Campus

├── Admission Acceptance

│      ↓

│ Student

│      ↓

│ Enrollment

├── Finance

├── Scholarship

├── Notifications

└── Reporting

---

# Cascade Rules

Deleting an Admission Offer is prohibited after publication.

Accepted Offers become immutable.

Expired Offers remain available for reporting.

Revoked Offers cannot be accepted.

---

# Integrity Rules

Every Admission Offer belongs to one Admission Decision.

Only approved Admission Decisions may generate Offers.

Every Offer references one Applicant.

Accepted Offers create one Admission Acceptance.

Enrollment cannot begin without an accepted Offer.

---

# Related Models

- AdmissionOffer
- AdmissionDecision
- AdmissionApplication
- Applicant
- AdmissionCycle
- AdmissionAcceptance
- Student
- Enrollment
- Finance
- Scholarship

---

# Future Improvements

- Digital Offer Letters.
- Electronic Signature.
- Parent Co-Acceptance.
- Online Admission Portal.
- Automated Fee Estimation.
- Scholarship Recommendation Integration.
- Multi-language Offer Templates.