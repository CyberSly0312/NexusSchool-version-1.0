# Billing

## Purpose

Manage invoicing, payments, financial records, and subscription billing.

---

# Billing Components

- Subscription Plans
- Invoices
- Payments
- Taxes
- Discounts
- Coupons
- Refunds

---

# Supported Payment Methods

- Mobile Money
- Credit Card
- Bank Transfer
- Cash (optional)

---

# Supported Payment Providers

- Paystack
- Flutterwave
- Stripe
- Mobile Money Gateways

---

# Invoice Lifecycle

Draft

↓

Issued

↓

Paid

↓

Cancelled

↓

Refunded

---

# Invoice Information

- Invoice Number
- Tenant
- Subscription
- Amount
- Currency
- Due Date
- Payment Status

---

# Payment Verification

Payments are verified automatically using webhook notifications.

---

# Automatic Activation

Successful payment immediately:

- Activates the subscription
- Creates or renews the tenant
- Generates the receipt
- Sends confirmation email

No manual intervention is required.

---

# Refunds

Refunds require administrator approval.

Every refund is audited.

---

# Currency

The platform supports multiple currencies.

Examples

- XOF
- GHS
- NGN
- USD
- EUR

---

# Financial Reports

- Revenue
- Outstanding Payments
- Monthly Recurring Revenue (MRR)
- Annual Recurring Revenue (ARR)
- Subscription Growth
- Churn Rate

---

# Security

- HTTPS
- Webhook Signature Verification
- Audit Logs
- PCI-Aware Integration

---

# Future Enhancements

- Promotional Codes
- Installment Payments
- Automatic Tax Calculation
- Regional Payment Providers