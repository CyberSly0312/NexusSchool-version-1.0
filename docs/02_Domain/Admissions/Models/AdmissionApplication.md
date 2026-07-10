# Admission Application Model

## Purpose

The Admission Application represents the official request submitted by an Applicant seeking admission to the institution for a specific Admission Cycle.

It serves as the central entity of the Admissions domain, coordinating all admission-related activities, including document verification, eligibility assessment, interviews, entrance examinations, admission decisions, offer management, and student creation.

An Admission Application remains the primary reference throughout the admission process until it is either rejected, withdrawn, or converted into an official Student Enrollment.

---

# Business Objectives

The Admission Application enables the institution to:

- Receive and manage admission requests.
- Organize applicant information.
- Track admission progress.
- Validate admission requirements.
- Manage supporting documents.
- Schedule interviews.
- Schedule entrance examinations.
- Record admission decisions.
- Generate admission offers.
- Convert successful applicants into Students.
- Maintain a complete admission history.

---

# Responsibilities

The Admission Application is responsible for:

- Registering applicant information.
- Linking the Applicant to an Admission Cycle.
- Managing admission status.
- Coordinating required documents.
- Coordinating admission evaluations.
- Recording administrative decisions.
- Triggering Student creation.
- Triggering Enrollment creation.
- Maintaining audit history.

---

# Attributes

## Identification

- ID
- UUID
- Application Number

---

## Applicant Information

- Applicant
- Admission Cycle
- Academic Year

---

## Academic Information

- Requested Program
- Requested Level
- Requested Series
- Requested Classroom (optional)

---

## Application Details

- Application Date
- Submission Date
- Status
- Priority
- Source

Examples

- Online
- Walk-in
- Referral
- Scholarship
- Government Placement

---

## Processing Information

- Assigned Admission Officer
- Reviewer
- Decision Date
- Decision Reason

---

## Audit Information

- Created At
- Updated At
- Submitted At
- Approved At
- Archived At

---

# Relationships

The Admission Application belongs to:

- Applicant
- Admission Cycle

The Admission Application has many:

- Admission Documents
- Interviews
- Entrance Exams
- Decisions
- Notes
- Audit Logs

The Admission Application may create:

- Student
- Enrollment

---

# Lifecycle

Draft

↓

Submitted

↓

Under Review

↓

Interview

↓

Entrance Examination

↓

Decision

↓

Offer

↓

Accepted

↓

Student Created

↓

Enrollment Created

↓

Archived

---

# Business Rules

- Every Application must belong to one Admission Cycle.
- Every Application must have one Applicant.
- One Applicant may submit multiple Applications across different Admission Cycles.
- Application Numbers must be unique.
- Applications cannot be modified after approval unless authorized.
- Required documents must be validated before approval.
- Student creation is allowed only after acceptance.
- Enrollment creation requires Student creation.

---

# Constraints

- One active Application per Admission Cycle.
- Duplicate Applications are prohibited.
- Required fields cannot be empty.
- Archived Applications are read-only.

---

# Security

Only authorized Admission Officers may:

- Review Applications.
- Modify Decisions.
- Approve Applications.
- Reject Applications.
- Archive Applications.

Applicants may only:

- View their own Applications.
- Upload required documents.
- Accept or decline Admission Offers.

---

# Audit Requirements

The system shall record:

- Submission history.
- Status changes.
- Reviewer actions.
- Decision history.
- Document updates.
- Offer acceptance.
- Student creation.
- Enrollment creation.

---

# Notifications

The system should notify Applicants when:

- Application submitted.
- Documents missing.
- Interview scheduled.
- Examination scheduled.
- Decision published.
- Offer available.
- Enrollment completed.

Admission Officers should receive notifications when:

- New Application submitted.
- Documents pending verification.
- Interview pending.
- Decision overdue.

---

# Reporting

Reports include:

- Applications by Admission Cycle.
- Applications by Program.
- Applications by Level.
- Applications by Status.
- Acceptance Rate.
- Rejection Rate.
- Conversion Rate.
- Average Processing Time.

---

# Integration

The Admission Application integrates with:

- Admissions
- Academic
- Students
- Finance
- Communication
- Documents
- Identity Management
- Reporting
- Notification System

---

# Future Improvements

- Online self-service applications.
- AI-assisted eligibility verification.
- Automatic document validation.
- OCR integration.
- Digital signatures.
- Scholarship evaluation.
- Multi-campus admissions.
- Ministry admission synchronization.
- Parent portal integration.