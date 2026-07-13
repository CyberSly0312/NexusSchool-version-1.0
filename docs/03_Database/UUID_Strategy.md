# UUID Strategy

## Purpose

Define the identifier strategy across the entire platform.

---

## Standard

Laravel ULID will be used for all primary keys.

---

## Advantages

- Globally unique
- Sortable
- Distributed-system friendly
- Better security than auto-increment IDs

---

## Exceptions

Reference tables may use integer IDs when appropriate.

---

## Foreign Keys

Foreign keys always match the primary key type.

---

## Public APIs

Expose ULIDs instead of sequential IDs.