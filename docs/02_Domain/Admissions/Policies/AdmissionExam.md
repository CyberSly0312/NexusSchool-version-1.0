# Admission Exam Policy

## Purpose

This policy defines the authorization rules governing Admission Examinations.

Admission Exams are formal assessments used to evaluate Applicants during the admission process. This policy ensures secure scheduling, administration, grading, and publication of examination results.

---

# Scope

This policy applies to:

- Admission Officers
- Examiners
- Admission Managers
- School Administrators
- System Administrators

---

# Roles

## Admission Officer

Permissions

- Schedule Admission Exams.
- Register Applicants.
- View Exam Results.

Restrictions

- Cannot modify published Results.

---

## Examiner

Permissions

- Conduct Exams.
- Grade Exams.
- Submit Scores.
- Add Evaluation Comments.

Restrictions

- Cannot publish Results.

---

## Admission Manager

Permissions

- Assign Examiners.
- Approve Results.
- Publish Results.
- Reschedule Exams.

Restrictions

- Cannot modify finalized Scores.

---

## School Administrator

Permissions

- Monitor Examination Performance.
- Access Examination Reports.

---

## System Administrator

Permissions

- Full administrative access.
- Configure Examination Templates.

---

# Authorization Matrix

| Action | Officer | Examiner | Manager | Administrator |
|----------|----------|-----------|-----------|----------------|
| View | ✅ | ✅ | ✅ | ✅ |
| Schedule | ✅ | ❌ | ✅ | ✅ |
| Conduct | ❌ | ✅ | ✅ | ✅ |
| Grade | ❌ | ✅ | ✅ | ✅ |
| Publish Results | ❌ | ❌ | ✅ | ✅ |
| Archive | ❌ | ❌ | ✅ | ✅ |
| Delete | ❌ | ❌ | ❌ | ❌ |

---

# Business Rules

- Exams must belong to an active Admission Cycle.
- Every completed Exam must have at least one Result.
- Published Results become read-only.
- Regrading requires Manager approval.

---

# Security Rules

- Only assigned Examiners may grade Exams.
- Results remain confidential until publication.
- Every grading action is logged.

---

# Audit Requirements

Record:

- Scheduling
- Attendance
- Grading
- Result Publication
- Approval
- User
- Timestamp

---

# Future Improvements

- Computer-Based Testing (CBT).
- AI Proctoring.
- Automatic Grading.
- Question Bank Integration.