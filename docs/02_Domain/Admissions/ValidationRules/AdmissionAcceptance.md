# Admission Acceptance Validation Rules

## Purpose

Defines validation rules for Admission Acceptances.

---

## Required Fields

- Admission Offer
- Applicant
- Acceptance Date

---

## Validation Rules

- Offer must be Published.
- Offer must not be Expired.
- Applicant must match the Offer.
- Acceptance Date must be within the validity period.

---

## Business Constraints

- One Acceptance per Offer.
- Completed Acceptances become immutable.
- Acceptance initiates Enrollment.

---

## Error Messages

- Offer expired.
- Offer already accepted.
- Invalid Applicant.
- Duplicate Acceptance.