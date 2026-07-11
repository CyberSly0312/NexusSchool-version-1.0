# Admission Document State Machine

## States

- Uploaded
- PendingVerification
- Approved
- Rejected
- Archived

---

## Initial State

Uploaded

---

## Final State

Archived

---

## Allowed Transitions

Uploaded
→ PendingVerification

PendingVerification
→ Approved
→ Rejected

Rejected
→ Uploaded

Approved
→ Archived

Rejected
→ Archived

---

## Invalid Transitions

Approved → Uploaded

Archived → PendingVerification

---

## Business Rules

- Approved Documents cannot be modified.
- Rejected Documents may be replaced.

---

## Events

- DocumentUploaded
- DocumentVerified
- DocumentRejected
- DocumentArchived