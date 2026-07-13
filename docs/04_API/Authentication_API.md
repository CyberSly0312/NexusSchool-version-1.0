# Authentication API

## Purpose

Provide secure authentication and authorization services for all Nexus School clients.

---

# Base Endpoint

/api/v1/auth

---

# Authentication Methods

- Email & Password
- Username & Password (optional)
- Mobile Number (optional)
- Single Sign-On (future)
- Multi-Factor Authentication (optional)

---

# Endpoints

POST /login

Authenticate user.

POST /logout

Terminate current session.

POST /refresh

Refresh authentication token.

POST /forgot-password

Request password reset.

POST /reset-password

Reset password.

POST /change-password

Change authenticated user's password.

GET /me

Retrieve authenticated user profile.

GET /permissions

Retrieve user permissions.

GET /roles

Retrieve assigned roles.

---

# Authentication

Laravel Sanctum

Bearer Token

HTTPS Required

---

# Security

- Password hashing (Argon2id)
- Rate limiting
- Token expiration
- Session management
- Device tracking
- Account lockout after configurable failed attempts
- Optional Multi-Factor Authentication

---

# Authorization

Access is enforced using:

- Roles
- Permissions
- Policies
- Tenant isolation

---

# Response Codes

200 OK

201 Created

401 Unauthorized

403 Forbidden

422 Validation Error

429 Too Many Requests

---

# Audit

The following actions are logged:

- Login
- Logout
- Password changes
- Failed login attempts
- Password reset
- Token revocation