# Finance API

## Purpose

Manage financial operations including invoices, payments, fee structures, scholarships, and refunds.

---

# Base Endpoint

/api/v1/finance

---

# Resources

/invoices

/payments

/fee-structures

/scholarships

/refunds

---

# Endpoints

GET

List resources.

POST

Create resource.

PATCH

Update resource.

DELETE

Archive resource when permitted.

---

# Additional Endpoints

GET /finance/dashboard

Financial dashboard.

GET /finance/reports

Financial reports.

GET /finance/statistics

Financial statistics.

POST /finance/payments/verify

Verify payment.

POST /finance/payments/webhook

Payment gateway callback.

GET /finance/outstanding-balances

Outstanding balances.

---

# Filters

student

academic_year

payment_method

status

date

---

# Permissions

finance.view

finance.create

finance.update

finance.approve

finance.refund

finance.export

---

# Business Rules

Invoices are immutable after payment.

Payments cannot be deleted.

Refunds require approval workflow.

All financial operations are auditable.

---

# Security

HTTPS only.

Signed payment callbacks.

Tenant isolation.

Audit logging.

---

# Integrations

Mobile Money

Stripe

Paystack

Flutterwave

Bank APIs