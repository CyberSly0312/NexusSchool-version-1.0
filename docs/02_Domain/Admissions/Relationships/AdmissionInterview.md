# Admission Interview Relationships

## Purpose

This document defines the relationships between the Admission Interview entity and other entities within the Admissions domain and related domains.

The Admission Interview serves as an evaluation stage where Applicants are assessed by one or more Admission Officers before an Admission Decision is made.

Interview outcomes contribute to the overall admission evaluation process.

---

# Relationship Overview

Admission Interviews are linked to Admission Applications and provide qualitative assessments that support Admission Decisions.

Interviews may be conducted physically or virtually and may involve multiple interviewers.

---

# Entity Relationships

## Admission Application

Relationship

Admission Interview belongs to one Admission Application.

Cardinality

Many Interviews

↓

One Admission Application

---

## Applicant

Relationship

Admission Interview evaluates one Applicant.

Cardinality

Many Interviews

↓

One Applicant

---

## Admission Cycle

Relationship

Admission Interview belongs to one Admission Cycle.

Cardinality

Many Interviews

↓

One Admission Cycle

---

## Admission Officer

Relationship

Admission Interviews are conducted by one or more Admission Officers.

Cardinality

Many-to-Many

Example

Interview

↓

Chairperson

Academic Officer

Department Representative

Observer

---

## Interview Panel

Relationship

One Interview may have multiple Panel Members.

Cardinality

One-to-Many

---

## Evaluation Criteria

Relationship

Each Interview contains multiple Evaluation Criteria.

Cardinality

One-to-Many

Example

Communication

Leadership

Academic Motivation

Critical Thinking

Behavior

Confidence

---

## Interview Score

Relationship

Each Evaluation Criterion generates one or more Scores.

Cardinality

One-to-Many

---

## Admission Decision

Relationship

Interview Results contribute to the Admission Decision.

Cardinality

Many Interviews

↓

One Admission Decision

---

## Admission Checklist

Relationship

Completed Interviews satisfy Checklist Items.

Cardinality

One-to-Many

---

## Calendar

Relationship

Interview schedules are synchronized with the institutional Calendar.

Cardinality

Many Interviews

↓

One Calendar

---

## Notification System

Relationship

Interview scheduling generates Notifications.

Cardinality

One-to-Many

---

# Relationship Diagram

Admission Application

↓

Admission Interview

├── Applicant

├── Admission Cycle

├── Admission Officers

├── Interview Panel

├── Evaluation Criteria

├── Interview Scores

├── Admission Decision

├── Admission Checklist

├── Calendar

└── Notifications

---

# Cascade Rules

Deleting an Interview is prohibited after completion.

Completed Interviews become read-only.

Interview Scores cannot exist without an Interview.

Removing an Admission Application preserves historical Interview records.

---

# Integrity Rules

Every Interview belongs to one Admission Application.

Every Interview evaluates one Applicant.

Completed Interviews must contain at least one Evaluation.

Every Interview contributes to one Admission Decision.

Archived Interviews remain available for reporting.

---

# Related Models

- AdmissionInterview
- AdmissionApplication
- Applicant
- AdmissionCycle
- AdmissionDecision
- AdmissionChecklist
- AdmissionOfficer
- Calendar
- Notification

---

# Future Improvements

- AI Interview Analysis.
- Automatic Interview Scheduling.
- Video Interview Recording.
- Speech-to-Text Transcription.
- Online Interview Platform.
- Interview Performance Analytics.
- Digital Evaluation Forms.