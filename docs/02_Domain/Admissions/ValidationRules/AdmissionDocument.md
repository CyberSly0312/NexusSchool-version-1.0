# Admission Document Validation Rules

## Purpose

Defines validation rules for Admission Documents.

---

## Required Fields

- Applicant
- Document Type
- File
- Upload Date

---

## Validation Rules

- Supported file formats only.
- Maximum file size respected.
- Document Type must exist.
- File checksum must be valid.
- Duplicate uploads are rejected.

---

## Business Constraints

- Approved Documents cannot be modified.
- Rejected Documents may be replaced.

---

## Error Messages

- Unsupported file type.
- File too large.
- Duplicate document.
- Invalid file.