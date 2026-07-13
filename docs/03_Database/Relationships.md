# Database Relationships

## Purpose

Define relationship standards between entities.

---

## One-to-One

Used when an entity owns exactly one related entity.

Examples

- Student → Student Profile
- User → User Preference

---

## One-to-Many

Most common relationship.

Examples

- Program → Subjects
- Department → Teachers
- Class → Students

---

## Many-to-Many

Implemented using pivot tables.

Examples

- Teachers ↔ Subjects
- Students ↔ Clubs
- Roles ↔ Permissions

---

## Polymorphic Relationships

Used where multiple entities share common resources.

Examples

- Attachments
- Comments
- Notifications

---

## Cascade Rules

Preferred

- Restrict Delete
- Cascade Update

Avoid automatic cascade delete for critical business data.

---

## Relationship Guidelines

- Explicit foreign keys.
- Indexed foreign keys.
- No circular dependencies.