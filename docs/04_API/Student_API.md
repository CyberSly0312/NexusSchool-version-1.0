# Student API

## Purpose

Manage student lifecycle, enrollment information, academic records, and student services.

---

# Base Endpoint

/api/v1/students

---

# Endpoints

GET /students

List students.

GET /students/{id}

Retrieve student profile.

POST /students

Create student.

PATCH /students/{id}

Update student.

DELETE /students/{id}

Archive student.

---

# Additional Endpoints

GET /students/{id}/parents

Parent information.

GET /students/{id}/attendance

Attendance history.

GET /students/{id}/grades

Academic results.

GET /students/{id}/finance

Financial account.

GET /students/{id}/documents

Uploaded documents.

GET /students/{id}/timeline

Student activity timeline.

GET /students/export

Export student list.

POST /students/import

Bulk student import.

---

# Filters

academic_year

class

program

status

gender

nationality

admission_date

---

# Search

?q=

student_number

name

email

phone

---

# Sorting

name

student_number

created_at

admission_date

---

# Permissions

student.view

student.create

student.update

student.delete

student.export

student.import

---

# Business Rules

Student Number must be unique.

Students cannot have multiple active enrollments in the same Academic Period.

Archived students remain available through audit history.

---

# Security

Tenant isolation

Role-based authorization

Audit logging

HTTPS only

---

# Audit

Every modification to a student profile is recorded with:

- User
- Timestamp
- Previous values
- New values
- IP Address