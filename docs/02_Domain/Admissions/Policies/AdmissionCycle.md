# Admission Cycle Policy

## Purpose

This policy defines the authorization rules governing Admission Cycles.

Admission Cycles control the admission period during which Applicants may submit Admission Applications.

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

- View Admission Cycles.
- Monitor application statistics.

Restrictions

- Cannot create or modify Admission Cycles.

---

## Admission Manager

Permissions

- Create Admission Cycles.
- Edit Admission Cycles.
- Open Admission Cycles.
- Close Admission Cycles.
- Assign Officers.

Restrictions

- Cannot delete historical Admission Cycles.

---

## Registrar

Permissions

- View Admission Cycles.
- Monitor enrollment preparation.

Restrictions

- Cannot modify Admission Cycles.

---

## School Administrator

Permissions

- Configure institutional Admission Cycles.
- Archive completed Cycles.
- Generate reports.

---

## System Administrator

Permissions

- Full access.
- Recover archived Cycles.
- Configure global settings.

---

# Authorization Matrix

| Action | Officer | Manager | Registrar | Administrator |
|---------|----------|----------|------------|----------------|
| View | ✅ | ✅ | ✅ | ✅ |
| Create | ❌ | ✅ | ❌ | ✅ |
| Update | ❌ | ✅ | ❌ | ✅ |
| Open | ❌ | ✅ | ❌ | ✅ |
| Close | ❌ | ✅ | ❌ | ✅ |
| Archive | ❌ | ✅ | ❌ | ✅ |
| Delete | ❌ | ❌ | ❌ | ❌ |

---

# Business Rules

- Only one Admission Cycle may be active for the same intake.
- Closed Cycles reject new Applications.
- Archived Cycles become read-only.

---

# Security Rules

- Cross-tenant access is prohibited.
- Every administrative action is audited.

---

# Audit Requirements

Record:

- Creation
- Opening
- Closing
- Archive
- User
- Timestamp

---

# Future Improvements

- Multi-campus Admission Cycles.
- Automated Cycle Activation.
- AI Capacity Forecasting.