# ESP-002 — Repository Hardening Report

**Status:** Complete
**Branch:** main (working tree, uncommitted)
**Date:** 2026-08-10
**Owner:** Backend Architect / Engineering Governance
**Related ADR:** none (minor contract addition, no architecture change — see Boundaries & Extension Strategy)

---

# Summary

ESP-002 is a **repository hardening effort**, not a feature sprint. Its goal was
to eliminate a class of PHPStan findings where the authorization traits accepted
a generic `Illuminate\Database\Eloquent\Model` while reading `$model->school_id`
as a magic property. Because the parameter type was the base `Model` class,
PHPStan could not statically verify that a `school_id` property existed, which
forced those reads into the error baseline.

The fix replaces the implicit, convention-based ownership with an **explicit
contract**: `app/Contracts/SchoolOwned` exposes `getSchoolId(): string`, the
shared `HasSchoolId` trait implements it, and the six tenant-scoped aggregates
that carry a non-nullable `school_id` column now implement the contract. The two
authorization traits that performed the cross-school check
(`HandlesAttendanceAuthorization`, `HandlesClassCouncilAuthorization`) are now
typed against `SchoolOwned` and read the school via `$model->getSchoolId()`
instead of the magic property. Authorization logic is **byte-identical** —
`super_admin` bypass, cross-school denial, never-delete rules and the
central-school context are all preserved verbatim.

A secondary remediation repaired UTF-8 mojibake in **11 factory files** so the
codebase no longer carries corrupted French characters in test data.

**Quality gate at hand-off:**

- **PHPStan (level 5, Larastan):** exit 0 — "No errors". The pinned
  `phpstan-baseline.neon` was **not rewritten** and gained **no new entries**;
  the targeted findings were resolved by the contract, not by baseline expansion.
- **Pest:** full suite **1819 passed / 4281 assertions / 0 failed / 0 skipped**,
  verified twice (one transient database connection failure, not an assertion
  failure). Targeted authorization / tenant-isolation suites:
  **20 passed / 52 assertions**.
- **Pint:** all touched files formatted and clean.

---

# Objectives

- Remove the implicit `school_id` magic-property reads from the authorization
  traits so PHPStan can verify tenant ownership statically.
- Prefer an **explicit contract over baseline suppression**: the requirement is
  that every model passed to the authorization traits statically guarantees a
  school id.
- Keep the authorization **semantics byte-identical** — this is a
  type-safety change only, never a permission change.
- Scope the change to the two traits and the six aggregates that read
  `school_id` on the subjects they authorize; do **not** migrate the whole
  repository.
- Leave `phpstan-baseline.neon` unchanged; the hardening must not hide findings.
- Land the effort green under the three quality gates (PHPStan, Pest, Pint).

---

# Delivered Scope

## Contract

- **`app/Contracts/SchoolOwned.php`** (new) — `getSchoolId(): string`. The
  explicit representation of tenant ownership for authorization subjects.

## Shared trait

- **`app/Traits/HasSchoolId.php`** —
  - `@property string $school_id` PHPDoc so the trait documents the column it
    reads;
  - the `creating` observer callback is now typed `self $model`;
  - new `getSchoolId(): string` implementation of the contract (the pre-existing
    `&& tenant()` guard in the observer is preserved).

## Aggregates implementing `SchoolOwned`

All six carry a **non-nullable** `school_id` column (`string(36)` ULID), so the
contract is truthful for every row:

| Domain      | Model                                                               |
|-------------|---------------------------------------------------------------------|
| Attendance  | `AttendanceAlert`, `AttendanceCorrection`, `AttendanceRecord`,       |
|             | `AttendanceSession`, `AttendanceSummary`                              |
| ClassCouncil| `CouncilSession`                                                     |

## Authorization traits (typed against the contract)

- **`HandlesAttendanceAuthorization`** — subject typed as `SchoolOwned`,
  school read via `getSchoolId()`.
- **`HandlesClassCouncilAuthorization`** — subject typed as `SchoolOwned`,
  school read via `getSchoolId()`.

Authorization behavior is unchanged:

- `super_admin` platform bypass preserved;
- cross-school subjects denied;
- delete / restore / force-delete always denied;
- central-school context handling preserved.

## UTF-8 remediation

- **11 factory files** under `database/factories/` had corrupted French
  characters (mojibake); all were repaired to proper UTF-8. No unrelated changes
  were introduced in those files.

---

# Architectural Decision

## Why a contract instead of the alternatives

The root cause was **implicit ownership**: the traits received a generic
`Model` and assumed a `school_id` column existed. The viable fixes were
compared:

| Option                                    | Verdict                                                                              |
|-------------------------------------------|--------------------------------------------------------------------------------------|
| Suppress the PHPStan error                | Rejected — hides the same latent defect in every future model with the same shape.   |
| Extend the error baseline                 | Rejected — grows the baseline instead of removing findings.                          |
| Put `school_id` on the base `Model`       | Rejected — every model, including non-tenant ones, would lie about ownership.        |
| Blind `getAttribute('school_id')` cast    | Rejected — keeps ownership implicit and unverifiable.                                |
| **Explicit `SchoolOwned` contract**       | **Adopted** — the traits demand a static guarantee; only truly tenant-scoped models  |
|                                            | implement it; PHPStan verifies it at every call site.                                 |

The contract is **additive**: models that implement it gain a documented,
statically-verifiable ownership promise, and the authorization traits no longer
depend on an undeclared magic property.

---

# Static Analysis

- PHPStan level 5 with the Larastan extension, run against the current working
  tree: **exit 0 — "No errors"**.
- `phpstan-baseline.neon` was **not rewritten** and **no new entries** were
  added: the previously-pinned magic-property findings were resolved by the
  contract, not suppressed.
- Pint reports all touched files clean.

---

# Test Coverage & Verification

- **Full suite (Pest):** **1819 passed / 4281 assertions / 0 failed /
  0 skipped** — run twice. The first run hit a transient database connection
  failure (`Connection.php:584/827`), not an assertion failure; the second,
  clean run confirms full green parity.
- **Targeted authorization / tenant-isolation suites:** **20 passed /
  52 assertions** across the Attendance and ClassCouncil policy and isolation
  tests.
- **Security scenarios verified:**
  - same-school subject → allowed;
  - cross-school subject → denied;
  - `super_admin` → bypass preserved;
  - delete / restore / force-delete → always denied;
  - central-school context → preserved.

---

# Boundaries & Extension Strategy

- **Scope boundary:** ESP-002 touches only the contract, the shared trait, the
  six aggregates, the two authorization traits and the 11 remediated factories.
  The working tree carries ~291 files changed against `HEAD` from **prior
  uncommitted work that is explicitly outside this effort**; no attempt was made
  to review, revert or clean them.
- **No commit was created** — the repository convention is to commit at session
  close under explicit instruction; this report records the working-tree state.
- **ADR not required:** the `SchoolOwned` contract is an additive implementation
  detail of the existing multi-tenancy design, not a change to the architecture.
  It is recorded here and in the governance documents instead.
- **Future extension:** any new tenant-scoped aggregate that is an
  authorization subject shall implement `SchoolOwned`; the remaining
  authorization traits may adopt the contract as they are touched by future
  work — this sprint deliberately did not migrate the whole repository.

---

# Known Issues / Next Steps

- The repository still carries its pre-existing full-suite legacy items (legacy
  Filament panel tests, legacy enum labels, historical test harness) — **out of
  scope** for ESP-002, tracked separately in the governance documents.
- The broader repository still reads `school_id` through magic properties in
  places not touched by this effort; the contract pattern is now available for
  future hardening but was not applied repository-wide by design.
- The ~291 pre-existing uncommitted files remain uncommitted; committing /
  reviewing that backlog is a separate decision, not part of ESP-002.

---

# Related Documents

- [MASTER_ROADMAP](./MASTER_ROADMAP.md)
- [CURRENT_POSITION](./CURRENT_POSITION.md)
- [PROJECT_STATE](./PROJECT_STATE.md)
- [CHANGELOG](./CHANGELOG.md)
- [SESSION_HANDOFF](../../SESSION_HANDOFF.md)
- [Project Journal — 2026-08-10](../99_Project_Journal/2026/08-August/2026-08-10.md)
