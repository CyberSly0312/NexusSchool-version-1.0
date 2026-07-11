# Admission Application Policy

## Purpose

This policy defines the authorization rules governing the Admission Application entity.

It specifies who can create, view, update, review, approve, reject, archive, and manage Admission Applications throughout their lifecycle.

The objective is to ensure secure, auditable, and role-based access to the admissions process.

---

# Scope

This policy applies to:

- Applicants
- Parents or Guardians
- Admission Officers
- Admission Managers
- Registrars
- School Administrators
- System Administrators

---

# Roles

## Applicant

Permissions

- Create own Admission Application
- View own Applications
- Edit Draft Applications
- Submit Applications
- Upload Documents
- Withdraw Draft Applications

Restrictions

- Cannot approve Applications.
- Cannot modify Submitted Applications.
- Cannot view Applications of other Applicants.

---

## Parent or Guardian

Permissions

- Create Applications on behalf of dependent Applicants.
- View dependent Applications.
- Upload supporting Documents.
- Track Application Status.

Restrictions

- Cannot approve Applications.
- Cannot access unrelated Applicants.

---

## Admission Officer

Permissions

- View all Applications.
- Verify submitted Applications.
- Request additional information.
- Validate uploaded Documents.
- Schedule Interviews.
- Schedule Admission Exams.
- Recommend Admission Decisions.

Restrictions

- Cannot modify Applicant personal information after submission.
- Cannot approve final Admission Decisions.

---

## Admission Manager

Permissions

- Review all Applications.
- Approve or reject Applications.
- Assign Admission Officers.
- Override recommendations.
- Publish Admission Decisions.

Restrictions

- Cannot delete historical Applications.

---

## Registrar

Permissions

- View approved Applications.
- Prepare Student records.
- Initiate Enrollment.
- Archive completed Applications.

Restrictions

- Cannot modify Admission Decisions.

---

## School Administrator

Permissions

- Manage Admission Cycles.
- View Admission statistics.
- Configure admission settings.
- Monitor institutional performance.

Restrictions

- Cannot modify historical audit records.

---

## System Administrator

Permissions

- Full system access.
- Configure permissions.
- Recover archived Applications.
- Manage integrations.

Restrictions

- All administrative actions must be logged.

---

# Authorization Matrix

| Action | Applicant | Parent | Officer | Manager | Registrar | Administrator |
|----------|-----------|----------|-----------|-------------|---------------|----------------|
| Create | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ |
| View Own | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| View All | ❌ | ❌ | ✅ | ✅ | ✅ | ✅ |
| Edit Draft | ✅ | ✅ | ❌ | ✅ | ❌ | ✅ |
| Submit | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ |
| Verify | ❌ | ❌ | ✅ | ✅ | ❌ | ✅ |
| Approve | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| Reject | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| Archive | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| Delete | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

---

# Ownership Rules

Applicants may access only their own Applications.

Parents may access only Applications belonging to their dependents.

Admission Officers may access Applications assigned to them or within their institution.

Managers and Administrators may access institutional Applications according to tenant boundaries.

---

# Validation Rules

Applications may only be submitted if:

- Mandatory information is complete.
- Required Documents are uploaded.
- Admission Cycle is open.
- Applicant meets eligibility requirements.

---

# Security Rules

Personal information is protected by role-based access control.

Sensitive applicant information must never be exposed to unauthorized users.

Every access attempt must be logged.

Cross-tenant access is strictly prohibited.

---

# Audit Requirements

The system shall record:

- Application creation.
- Status changes.
- Document uploads.
- Verification actions.
- Approval decisions.
- User identity.
- Timestamp.
- IP Address (optional).
- Device information (optional).

---

# Exception Handling

Emergency administrative overrides require:

- Manager authorization.
- Audit logging.
- Justification.
- Notification to System Administrators.

---

# Future Improvements

- Attribute-Based Access Control (ABAC).
- Temporary delegated permissions.
- AI anomaly detection for unauthorized access.
- Multi-factor approval workflow.
- Risk-based authorization.