# Admission Exam Model

## Purpose

The Admission Exam represents an examination or assessment conducted during the admission process to evaluate an Applicant's academic knowledge, aptitude, language proficiency, or other competencies required for admission.

Admission Exams support objective candidate evaluation and contribute to the final Admission Decision.

---

# Business Objectives

The Admission Exam enables the institution to:

- Conduct entrance examinations.
- Evaluate academic readiness.
- Standardize applicant assessments.
- Rank applicants objectively.
- Support merit-based admissions.
- Produce examination statistics.

---

# Responsibilities

The Admission Exam is responsible for:

- Scheduling examinations.
- Registering applicants.
- Recording examination results.
- Calculating examination scores.
- Supporting admission rankings.
- Providing examination history.

---

# Attributes

## Identification

- ID
- UUID
- Exam Number

---

## Application Information

- Admission Application
- Applicant
- Admission Cycle

---

## Examination Information

- Exam Name
- Exam Type
- Academic Program
- Academic Level

Examples

Exam Types

- Entrance Examination
- Placement Test
- Language Assessment
- Aptitude Test
- Scholarship Examination

---

## Scheduling

- Exam Date
- Start Time
- End Time
- Duration

---

## Location

- Campus
- Building
- Room
- Online Examination Link

---

## Assessment

- Maximum Score
- Passing Score
- Obtained Score
- Percentage
- Grade
- Rank

---

## Result

- Result Status

Examples

- Passed
- Failed
- Pending
- Absent
- Cancelled

---

## Administration

- Examiner
- Supervisor
- Approved By

---

## Status

- Scheduled
- In Progress
- Completed
- Published
- Archived

---

## Audit Information

- Created At
- Updated At
- Published At
- Archived At

---

# Relationships

The Admission Exam belongs to:

- Admission Application
- Applicant
- Admission Cycle

The Admission Exam may have:

- Exam Sections
- Exam Results
- Attachments
- Audit Logs

The Admission Exam contributes to:

- Admission Decision

---

# Lifecycle

Scheduled

↓

In Progress

↓

Completed

↓

Published

↓

Archived

Alternative Paths

Scheduled

↓

Cancelled

↓

Archived

---

# Business Rules

- Every Exam belongs to one Admission Application.
- Scores cannot exceed the Maximum Score.
- Passing Score must be lower than or equal to the Maximum Score.
- Published Results cannot be modified.
- Absent Applicants receive no score.

---

# Constraints

- Exam Date is mandatory.
- Maximum Score is mandatory.
- Passing Score is mandatory.
- Obtained Score cannot be negative.

---

# Security

Applicants may:

- View examination schedule.
- View published results.

Examiners may:

- Record scores.
- Update results before publication.

Admission Administrators may:

- Schedule examinations.
- Publish results.
- Archive examinations.

---

# Audit Requirements

The system shall record:

- Examination scheduling.
- Attendance.
- Score changes.
- Result publication.
- Administrative actions.

---

# Notifications

Applicants should receive notifications when:

- Examination scheduled.
- Examination reminder.
- Results published.

Admission Officers should receive notifications when:

- Scores pending.
- Results ready for publication.

---

# Reporting

Reports include:

- Examination Attendance Rate.
- Pass Rate.
- Average Score.
- Highest Score.
- Lowest Score.
- Ranking by Program.
- Ranking by Admission Cycle.

---

# Integration

The Admission Exam integrates with:

- Admissions
- Academic
- Reporting
- Communication
- Notification System
- Calendar

---

# Future Improvements

- Computer-Based Testing (CBT).
- AI-assisted proctoring.
- Automatic grading.
- Question bank integration.
- Randomized examinations.
- Remote online examinations.
- Adaptive testing.