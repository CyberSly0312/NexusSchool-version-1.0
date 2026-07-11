# Admission Offer State Machine

## Purpose

Defines the lifecycle of an Admission Offer.

---

## States

- Draft
- Generated
- Published
- Accepted
- Declined
- Expired
- Revoked
- Archived

---

## Initial State

Draft

---

## Final States

- Accepted
- Declined
- Expired
- Revoked
- Archived

---

## Allowed Transitions

Draft
→ Generated

Generated
→ Published

Published
→ Accepted
→ Declined
→ Expired
→ Revoked

Accepted
→ Archived

Declined
→ Archived

Expired
→ Archived

Revoked
→ Archived

---

## Invalid Transitions

Accepted → Published

Expired → Accepted

Revoked → Accepted

Archived → Any State

---

## Business Rules

- Only Published Offers may be accepted.
- Expired Offers become invalid.
- Revoked Offers cannot be restored.

---

## Events

- OfferGenerated
- OfferPublished
- OfferAccepted
- OfferDeclined
- OfferExpired
- OfferRevoked
- OfferArchived