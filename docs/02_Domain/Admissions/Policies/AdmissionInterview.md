# Admission Interview Policy

## Purpose

This policy defines the authorization rules governing Admission Interviews.

Admission Interviews evaluate Applicants and contribute to the Admission Decision through structured assessments.

---

# Scope

This policy applies to:

- Admission Officers
- Interview Panel Members
- Admission Managers
- School Administrators
- System Administrators

---

# Roles

## Admission Officer

Permissions

- Schedule Interviews.
- Conduct Interviews.
- Record Evaluations.

Restrictions

- Cannot publish Admission Decisions.

---

## Interview Panel Member

Permissions

- Participate in Interviews.
- Submit Evaluation Scores.
- Add Interview Notes.

Restrictions

- Cannot modify other panel members' evaluations.

---

## Admission Manager

Permissions

- Assign Interview Panels.
- Approve Interview Results.
- Reschedule Interviews.

Restrictions

- Cannot alter finalized evaluations.

---

## School Administrator

Permissions

- View Interview Reports.
- Monitor Interview Performance.

---

## System Administrator

Permissions

- Full administrative access.
- Configure Interview Templates.

---

# Authorization Matrix

| Action | Officer | Panel | Manager | Administrator |
|---------|----------|---------|-----------|----------------|
| View | ✅ | ✅ | ✅ | ✅ |
| Schedule | ✅ | ❌ | ✅ | ✅ |
| Conduct | ✅ | ✅ | ✅ | ✅ |
| Score | ✅ | ✅ | ✅ | ✅ |
| Approve | ❌ | ❌ | ✅ | ✅ |
| Archive | ❌ | ❌ | ✅ | ✅ |
| Delete | ❌ | ❌ | ❌ | ❌ |

---

# Business Rules

- Every completed Interview requires at least one Evaluation.
- Finalized Evaluations become immutable.
- Interviews must belong to an active Admission Cycle.

---

# Security Rules

- Only assigned Interviewers may evaluate Applicants.
- Evaluation results remain confidential until the Admission Decision is published.

---

# Audit Requirements

Record:

- Scheduling
- Attendance
- Evaluation
- Approval
- User
- Timestamp

---

# Future Improvements

- AI Interview Assistant.
- Video Recording.
- Online Interviews.
- Speech-to-Text Evaluation.