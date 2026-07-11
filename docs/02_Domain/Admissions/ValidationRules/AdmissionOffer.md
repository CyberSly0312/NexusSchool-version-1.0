# Admission Offer Validation Rules

## Purpose

Defines validation rules for Admission Offers.

---

## Required Fields

- Admission Decision
- Offer Number
- Publication Date
- Expiration Date

---

## Validation Rules

- Decision must be Approved.
- Offer Number must be unique.
- Expiration Date must be later than Publication Date.
- Program Capacity must be available.

---

## Business Constraints

- One Offer per Approved Decision.
- Accepted Offers cannot be modified.
- Expired Offers cannot be accepted.

---

## Error Messages

- Invalid Offer Number.
- Offer already exists.
- Offer expired.
- Program capacity exceeded.