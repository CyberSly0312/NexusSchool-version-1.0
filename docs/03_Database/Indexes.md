# Database Index Strategy

## Purpose

Optimize query performance while minimizing storage overhead.

---

## Default Indexes

- Primary Keys
- Foreign Keys
- Unique Columns

---

## Frequently Indexed Columns

- tenant_id
- student_number
- employee_number
- email
- created_at
- status

---

## Composite Indexes

Examples

(tenant_id, status)

(academic_year_id, class_id)

(student_id, academic_period_id)

---

## Search Optimization

Use PostgreSQL full-text search where appropriate.

---

## Performance Guidelines

- Avoid unnecessary indexes.
- Monitor slow queries.
- Review execution plans regularly.

---

## Maintenance

Indexes are reviewed during each major release.