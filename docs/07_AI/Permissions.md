# AI Permissions

## Purpose

Define authorization rules governing AI interactions.

---

# Principle

The AI never has more permissions than the authenticated user.

---

# Allowed Operations

The assistant may:

- Search information
- Summarize records
- Explain reports
- Draft documents
- Recommend actions

---

# Restricted Operations

The assistant cannot:

- Delete records
- Change permissions
- Approve payments
- Publish examination results
- Modify subscriptions

Without explicit user confirmation and authorization.

---

# Tenant Isolation

AI requests remain isolated within the active tenant.

---

# Audit

Every AI interaction records:

- User
- Timestamp
- Prompt
- Response Summary
- Actions Suggested

---

# Security

Sensitive prompts are protected and logged.

Prompt injection attempts should be detected where possible.