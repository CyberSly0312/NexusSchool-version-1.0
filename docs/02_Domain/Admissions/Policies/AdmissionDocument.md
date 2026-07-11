# Admission Document Policy

## Purpose

This policy defines the authorization rules governing Admission Documents.

Admission Documents contain official records submitted by Applicants to support their Admission Applications. These documents require secure handling, verification, and long-term preservation.

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

- Upload Documents.
- View own Documents.
- Replace Documents before submission.

Restrictions

- Cannot delete verified Documents.
- Cannot modify approved Documents.

---

## Parent or Guardian

Permissions

- Upload Documents for dependents.
- View dependent Documents.

Restrictions

- Cannot access unrelated Applicants.

---

## Admission Officer

Permissions

- View Documents.
- Verify Documents.
- Reject invalid Documents.
- Request replacement.

Restrictions

- Cannot permanently delete Documents.

---

## Admission Manager

Permissions

- Approve verified Documents.
- Override verification results.
- Archive Documents.

Restrictions

- Cannot modify uploaded file contents.

---

## Registrar

Permissions

- View approved Documents.
- Access Student Records after Enrollment.

---

## School Administrator

Permissions

- Monitor document compliance.
- Generate document reports.

---

## System Administrator

Permissions

- Full administrative access.
- Configure storage and retention policies.

---

# Authorization Matrix

| Action | Applicant | Parent | Officer | Manager | Registrar | Administrator |
|---------|-----------|----------|-----------|------------|--------------|----------------|
| Upload | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ |
| View Own | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Verify | ❌ | ❌ | ✅ | ✅ | ❌ | ✅ |
| Approve | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| Replace | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ |
| Archive | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| Delete | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

---

# Business Rules

- Verified Documents become read-only.
- Every mandatory Requirement must have a verified Document.
- Archived Documents remain available for audit.

---

# Security Rules

- Documents are encrypted at rest.
- Access is tenant-isolated.
- Every download is logged.

---

# Audit Requirements

Record:

- Upload
- Verification
- Approval
- Replacement
- Archive
- User
- Timestamp

---

# Future Improvements

- OCR Verification.
- AI Fraud Detection.
- Digital Signatures.
- Government Registry Verification.