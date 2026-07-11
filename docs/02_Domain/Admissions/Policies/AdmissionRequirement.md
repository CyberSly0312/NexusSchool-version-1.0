# Admission Requirement Policy

## Purpose

This policy defines the authorization rules governing Admission Requirements.

Admission Requirements determine the eligibility criteria Applicants must satisfy before admission.

---

# Scope

This policy applies to:

- Admission Officers
- Admission Managers
- Academic Managers
- School Administrators
- System Administrators

---

# Roles

## Admission Officer

Permissions

- View Requirements.
- Verify Requirement completion.

Restrictions

- Cannot modify Requirements.

---

## Admission Manager

Permissions

- Create Requirements.
- Update Requirements.
- Activate Requirements.
- Deactivate Requirements.

Restrictions

- Cannot delete historical Requirements.

---

## Academic Manager

Permissions

- Recommend academic Requirements.
- Review curriculum-related Requirements.

Restrictions

- Cannot publish Requirements.

---

## School Administrator

Permissions

- Approve institutional Requirements.
- Archive obsolete Requirements.

---

## System Administrator

Permissions

- Full access.
- Configure Requirement templates.

---

# Authorization Matrix

| Action | Officer | Manager | Academic Manager | Administrator |
|---------|----------|----------|------------------|----------------|
| View | ✅ | ✅ | ✅ | ✅ |
| Create | ❌ | ✅ | ❌ | ✅ |
| Update | ❌ | ✅ | ❌ | ✅ |
| Verify | ✅ | ✅ | ✅ | ✅ |
| Activate | ❌ | ✅ | ❌ | ✅ |
| Archive | ❌ | ✅ | ❌ | ✅ |
| Delete | ❌ | ❌ | ❌ | ❌ |

---

# Business Rules

- Mandatory Requirements cannot be removed from active Admission Cycles.
- Requirement changes affect only future Applications.
- Archived Requirements remain available for audit.

---

# Security Rules

- Requirement modifications require appropriate authorization.
- All changes are logged.

---

# Audit Requirements

Record:

- Creation
- Modification
- Activation
- Archive
- User
- Timestamp

---

# Future Improvements

- Rule Engine Integration.
- Country-specific Requirement Templates.
- AI Eligibility Validation.