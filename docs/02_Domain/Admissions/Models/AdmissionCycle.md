# Admission Cycle Model

## Purpose

The Admission Cycle represents an official admission campaign during which Applicants can submit applications for enrollment into the institution.

It defines the admission calendar, application periods, eligibility rules, available academic programs, and admission capacity for a specific academic year.

Every Admission Application must belong to exactly one Admission Cycle.

---

# Business Objectives

The Admission Cycle enables the institution to:

- Organize admission campaigns.
- Define application periods.
- Manage admission calendars.
- Control application availability.
- Assign available Programs and Levels.
- Monitor admission capacity.
- Produce admission statistics.
- Support multi-campus admissions.

---

# Responsibilities

The Admission Cycle is responsible for:

- Opening admissions.
- Closing admissions.
- Defining application deadlines.
- Managing admission capacity.
- Controlling admission status.
- Coordinating admission activities.
- Triggering notifications.
- Providing reporting information.

---

# Attributes

## Identification

- ID
- UUID
- Cycle Code
- Cycle Name

Examples

- 2026 Admissions
- 2027 Intake
- Mid-Year Admission

---

## Academic Information

- Academic Year
- Campus
- Program
- Available Levels
- Available Series

---

## Schedule

- Opening Date
- Closing Date
- Document Deadline
- Interview Start Date
- Interview End Date
- Examination Date
- Decision Publication Date
- Acceptance Deadline

---

## Capacity

- Maximum Applications
- Maximum Accepted Students
- Reserved Seats
- Available Seats

---

## Status

- Draft
- Scheduled
- Open
- Closed
- Completed
- Archived

---

## Administration

- Admission Coordinator
- Created By
- Approved By

---

## Audit Information

- Created At
- Updated At
- Published At
- Closed At
- Archived At

---

# Relationships

The Admission Cycle belongs to:

- Academic Year
- Campus

The Admission Cycle has many:

- Admission Applications
- Admission Requirements
- Admission Decisions
- Interviews
- Entrance Exams

---

# Lifecycle

Draft

↓

Scheduled

↓

Open

↓

Closed

↓

Completed

↓

Archived

---

# Business Rules

- Only one Admission Cycle may be Open for the same Academic Year and Campus.
- Applications are accepted only during the Open period.
- Closing Date must be later than Opening Date.
- Capacity cannot be exceeded.
- Admission Decisions can only be published after the review process.
- Completed Admission Cycles become read-only.

---

# Constraints

- Cycle Code must be unique.
- Opening Date is mandatory.
- Closing Date is mandatory.
- Academic Year is mandatory.
- Campus is mandatory.

---

# Security

Only authorized Admission Administrators may:

- Create Admission Cycles.
- Modify schedules.
- Open admissions.
- Close admissions.
- Archive cycles.

Admission Officers may:

- View active cycles.
- Process applications.

Applicants may:

- View only published Admission Cycles.
- Apply only during Open periods.

---

# Audit Requirements

The system shall record:

- Cycle creation.
- Schedule modifications.
- Opening and closing history.
- Capacity changes.
- Administrative approvals.
- Status transitions.

---

# Notifications

The system should notify:

Applicants

- Admission opens.
- Admission closing reminder.
- Decision publication.
- Acceptance deadline.

Admission Officers

- Admission opening.
- Capacity reached.
- Pending applications.
- Admission closing.

---

# Reporting

Reports include:

- Applications by Cycle.
- Applications by Program.
- Acceptance Rate.
- Rejection Rate.
- Capacity Utilization.
- Processing Time.
- Enrollment Conversion Rate.

---

# Integration

The Admission Cycle integrates with:

- Academic
- Admissions
- Students
- Finance
- Communication
- Reporting
- Notification System
- Calendar

---

# Future Improvements

- Multiple admission waves.
- Rolling admissions.
- AI capacity prediction.
- Automatic deadline reminders.
- Ministry synchronization.
- Multi-campus coordination.
- Public admission portal.