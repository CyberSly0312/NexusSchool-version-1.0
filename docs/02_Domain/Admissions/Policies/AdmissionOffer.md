# Admission Offer Policy

## Purpose

This policy defines the authorization rules governing Admission Offers.

Admission Offers are official institutional documents issued to Applicants following an approved Admission Decision.

---

# Scope

This policy applies to:

- Admission Officers
- Admission Managers
- Registrars
- School Administrators
- System Administrators
- Applicants

---

# Roles

## Applicant

Permissions

- View own Admission Offer.
- Accept Admission Offer.
- Decline Admission Offer.

Restrictions

- Cannot modify Offer contents.

---

## Admission Officer

Permissions

- Generate Admission Offers.
- View Offer Status.

Restrictions

- Cannot publish Offers.

---

## Admission Manager

Permissions

- Approve Offers.
- Publish Offers.
- Revoke Offers.
- Extend Offer Expiration.

Restrictions

- Cannot modify historical Offers.

---

## Registrar

Permissions

- View Accepted Offers.
- Prepare Enrollment.

Restrictions

- Cannot revoke Offers.

---

## School Administrator

Permissions

- View Offer Reports.
- Monitor Admission Conversion.

---

## System Administrator

Permissions

- Full administrative access.
- Configure Offer Templates.

---

# Authorization Matrix

| Action | Applicant | Officer | Manager | Registrar | Administrator |
|----------|-----------|----------|----------|------------|----------------|
| View | ✅ | ✅ | ✅ | ✅ | ✅ |
| Generate | ❌ | ✅ | ✅ | ❌ | ✅ |
| Publish | ❌ | ❌ | ✅ | ❌ | ✅ |
| Accept | ✅ | ❌ | ❌ | ❌ | ❌ |
| Decline | ✅ | ❌ | ❌ | ❌ | ❌ |
| Revoke | ❌ | ❌ | ✅ | ❌ | ✅ |
| Archive | ❌ | ❌ | ✅ | ❌ | ✅ |

---

# Business Rules

- Only approved Decisions generate Offers.
- Expired Offers cannot be accepted.
- Accepted Offers become immutable.
- Revoked Offers become invalid immediately.

---

# Security Rules

- Applicants may access only their own Offers.
- Every Offer publication is audited.

---

# Audit Requirements

Record:

- Generation
- Publication
- Acceptance
- Decline
- Revocation
- User
- Timestamp

---

# Future Improvements

- Electronic Signature.
- QR Verification.
- Digital Offer Portal.
- Multi-language Templates.