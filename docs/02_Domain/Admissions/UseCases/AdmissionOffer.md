# Admission Offer Use Cases

## Purpose

Defines all business processes related to Admission Offers.

---

# Supported Use Cases

- Generate Offer
- Publish Offer
- Extend Offer Validity
- Revoke Offer
- Archive Offer

---

## Business Rules

- Offers are generated only from Approved Decisions.
- Accepted Offers cannot be revoked.
- Expired Offers cannot be accepted.

---

## Related Services

- AdmissionOfferService

---

## Related Events

- AdmissionOfferGenerated
- AdmissionOfferPublished
- AdmissionOfferAccepted
- AdmissionOfferExpired
- AdmissionOfferRevoked

---

## Related Permissions

- admissions.offer.generate
- admissions.offer.publish
- admissions.offer.revoke