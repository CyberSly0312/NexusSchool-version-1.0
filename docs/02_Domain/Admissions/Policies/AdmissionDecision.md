# Admission Decision Policy

## Purpose

This policy defines the authorization rules governing Admission Decisions.

Admission Decisions represent the official institutional outcome of an Admission Application after all evaluations have been completed.

---

# Scope

This policy applies to:

- Admission Officers
- Admission Managers
- Registrars
- School Administrators
- System Administrators

---

# Roles

## Admission Officer

Permissions

- Review Applications.
- Recommend Decisions.
- View Decision History.

Restrictions

- Cannot approve final Decisions.

---

## Admission Manager

Permissions

- Approve Decisions.
- Reject Applications.
- Waitlist Applicants.
- Publish Decisions.
- Reopen Decisions.

Restrictions

- Cannot delete historical Decisions.

---

## Registrar

Permissions

- View Approved Decisions.
- Prepare Student Records.
- Initiate Enrollment.

Restrictions

- Cannot modify Decisions.

---

## School Administrator

Permissions

- View Decision Statistics.
- Monitor Admission Performance.

---

## System Administrator

Permissions

- Full administrative access.
- Configure Decision Workflows.

---

# Authorization Matrix

| Action | Officer | Manager | Registrar | Administrator |
|----------|----------|----------|------------|----------------|
| View | ✅ | ✅ | ✅ | ✅ |
| Recommend | ✅ | ✅ | ❌ | ✅ |
| Approve | ❌ | ✅ | ❌ | ✅ |
| Reject | ❌ | ✅ | ❌ | ✅ |
| Publish | ❌ | ✅ | ❌ | ✅ |
| Archive | ❌ | ✅ | ❌ | ✅ |
| Delete | ❌ | ❌ | ❌ | ❌ |

---

# Business Rules

- Every Decision requires a completed Application.
- Mandatory Requirements must be satisfied.
- Interview and Examination results must be available when applicable.
- Published Decisions become immutable.
- Every accepted Decision generates one Admission Offer.

---

# Security Rules

- Decision publication is restricted to authorized Managers.
- Decision changes require audit logging.
- Cross-tenant Decision access is prohibited.

---

# Audit Requirements

Record:

- Recommendation
- Approval
- Publication
- Reopening
- Archive
- User
- Timestamp

---

# Future Improvements

- Multi-level Approval Workflow.
- AI Decision Recommendation.
- Digital Approval Signatures.
- Ministry Approval Integration.