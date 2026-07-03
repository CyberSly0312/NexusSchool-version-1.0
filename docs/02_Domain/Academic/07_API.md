# Nexus School

# Academic Domain API Specification

---

**Document ID:** NS-DOM-ACA-007

**Version:** 1.0.0

**Status:** Approved

**Owner:** Nexus Technologies

**Author:** Nexus School Engineering Team

**Review Cycle:** Major Releases

---

# Purpose

This document defines the REST API endpoints for the Academic Domain.

The API exposes secure, versioned, and tenant-aware endpoints used by the Web Application, Mobile Application, AI Assistant, and third-party integrations.

---

# API Principles

The Academic API follows these principles:

- RESTful
- Stateless
- Versioned
- Secure
- Consistent
- Predictable
- Documented

---

# Base URL

```
/api/v1
```

Future versions:

```
/api/v2
```

---

# Authentication

Protected endpoints require authentication using Laravel Sanctum.

Every authenticated request includes:

- User
- Tenant
- Permissions

---

# Authorization

Every endpoint validates:

- Authentication
- Tenant ownership
- Required permission
- Business rules

Unauthorized requests return:

```
403 Forbidden
```

---

# Content Type

Requests

```
application/json
```

Responses

```
application/json
```

---

# Academic Year Endpoints

| Method | Endpoint | Description |
|---------|----------|-------------|
| GET | /academic-years | List Academic Years |
| GET | /academic-years/{uuid} | View Academic Year |
| POST | /academic-years | Create Academic Year |
| PUT | /academic-years/{uuid} | Update Academic Year |
| DELETE | /academic-years/{uuid} | Delete Academic Year |
| POST | /academic-years/{uuid}/open | Open Academic Year |
| POST | /academic-years/{uuid}/close | Close Academic Year |

---

# Academic Term Endpoints

| Method | Endpoint |
|---------|----------|
| GET | /academic-terms |
| GET | /academic-terms/{uuid} |
| POST | /academic-terms |
| PUT | /academic-terms/{uuid} |
| DELETE | /academic-terms/{uuid} |

---

# Education Level Endpoints

| Method | Endpoint |
|---------|----------|
| GET | /education-levels |
| GET | /education-levels/{uuid} |
| POST | /education-levels |
| PUT | /education-levels/{uuid} |
| DELETE | /education-levels/{uuid} |

---

# Department Endpoints

| Method | Endpoint |
|---------|----------|
| GET | /departments |
| GET | /departments/{uuid} |
| POST | /departments |
| PUT | /departments/{uuid} |
| DELETE | /departments/{uuid} |

---

# Program Endpoints

| Method | Endpoint |
|---------|----------|
| GET | /programs |
| GET | /programs/{uuid} |
| POST | /programs |
| PUT | /programs/{uuid} |
| DELETE | /programs/{uuid} |

---

# Class Endpoints

| Method | Endpoint |
|---------|----------|
| GET | /classes |
| GET | /classes/{uuid} |
| POST | /classes |
| PUT | /classes/{uuid} |
| DELETE | /classes/{uuid} |
| POST | /classes/{uuid}/assign-teacher |
| POST | /classes/{uuid}/assign-subject |

---

# Section Endpoints

| Method | Endpoint |
|---------|----------|
| GET | /sections |
| GET | /sections/{uuid} |
| POST | /sections |
| PUT | /sections/{uuid} |
| DELETE | /sections/{uuid} |

---

# Subject Endpoints

| Method | Endpoint |
|---------|----------|
| GET | /subjects |
| GET | /subjects/{uuid} |
| POST | /subjects |
| PUT | /subjects/{uuid} |
| DELETE | /subjects/{uuid} |

---

# Curriculum Endpoints

| Method | Endpoint |
|---------|----------|
| GET | /curricula |
| GET | /curricula/{uuid} |
| POST | /curricula |
| PUT | /curricula/{uuid} |
| DELETE | /curricula/{uuid} |
| POST | /curricula/{uuid}/publish |

---

# Timetable Endpoints

| Method | Endpoint |
|---------|----------|
| GET | /timetables |
| GET | /timetables/{uuid} |
| POST | /timetables |
| PUT | /timetables/{uuid} |
| DELETE | /timetables/{uuid} |
| POST | /timetables/{uuid}/generate |
| POST | /timetables/{uuid}/publish |

---

# Standard Success Response

```json
{
  "success": true,
  "message": "Operation completed successfully.",
  "data": {},
  "meta": {}
}
```

---

# Standard Error Response

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": []
}
```

---

# Pagination

Collection endpoints support:

```
?page=1

&per_page=25
```

---

# Filtering

Examples:

```
?status=active

?academic_year=uuid

?department=uuid
```

---

# Sorting

```
?sort=name

?sort=-created_at
```

---

# Searching

```
?search=Computer
```

---

# Includes

Relationships may be loaded using:

```
?include=department,subjects
```

---

# Validation Errors

Validation failures return:

```
422 Unprocessable Entity
```

Each response includes:

- Field
- Validation Message

---

# Rate Limiting

Default limits:

Authenticated Users

100 requests/minute

Guests

30 requests/minute

---

# Audit

Mutating requests generate audit records.

Examples:

- Create
- Update
- Delete
- Publish
- Close Academic Year

---

# Versioning

Breaking API changes require a new API version.

Existing versions remain supported during the deprecation period.

---

# Future Enhancements

Future API improvements include:

- GraphQL
- Webhooks
- Bulk Operations
- API Keys
- SDK Support
- AI Endpoints

---

# Related Documents

- 01_Overview.md
- 03_Business_Rules.md
- 05_Permissions.md
- API Architecture

---

# Summary

The Academic Domain API provides a secure and consistent interface for managing academic resources across Nexus School.

---

# Conclusion

The API specification ensures interoperability, maintainability, and scalability while enforcing business rules and tenant isolation.

---

**End of Document**