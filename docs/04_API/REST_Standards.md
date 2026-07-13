# REST Standards

## Purpose

Define REST conventions used across the Nexus School platform.

---

## Resource Naming

Resources use plural nouns.

Correct

/students

/teachers

/classes

Incorrect

/getStudents

/createStudent

---

## HTTP Methods

GET

Retrieve resources.

POST

Create resources.

PUT

Replace resources.

PATCH

Partial update.

DELETE

Remove resources.

---

## Status Codes

200 OK

201 Created

204 No Content

400 Bad Request

401 Unauthorized

403 Forbidden

404 Not Found

409 Conflict

422 Validation Error

500 Internal Server Error

---

## URL Structure

/api/v1/students

/api/v1/teachers

/api/v1/classes

---

## Nested Resources

Example

/students/{student}/attendance

/classes/{class}/students

---

## Idempotency

GET

Safe

PUT

Idempotent

DELETE

Idempotent

POST

Non-idempotent

---

## Validation

Validation errors always return HTTP 422.

---

## Rate Limiting

API requests are throttled to prevent abuse.

---

## Best Practices

- Stateless requests
- Predictable responses
- Resource-oriented endpoints
- Consistent naming