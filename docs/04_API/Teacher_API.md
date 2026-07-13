# Teacher API

## Purpose

Provide REST endpoints for managing teachers, academic assignments, and workload.

---

# Base Endpoint

/api/v1/teachers

---

# Endpoints

GET /teachers

List teachers.

GET /teachers/{id}

Retrieve a teacher.

POST /teachers

Create a teacher.

PATCH /teachers/{id}

Update teacher information.

DELETE /teachers/{id}

Deactivate a teacher.

---

# Additional Endpoints

GET /teachers/{id}/subjects

Assigned subjects.

GET /teachers/{id}/classes

Assigned classes.

GET /teachers/{id}/timetable

Teaching timetable.

GET /teachers/{id}/attendance

Attendance records.

GET /teachers/{id}/performance

Performance summary.

---

# Filters

department

status

employment_type

qualification

---

# Search

?q=

---

# Sorting

name

employee_number

created_at

---

# Permissions

teacher.view

teacher.create

teacher.update

teacher.delete

teacher.assign

---

# Audit

Every create, update, assignment and deletion operation is logged.