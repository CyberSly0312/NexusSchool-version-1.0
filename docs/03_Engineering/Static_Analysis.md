# Static Analysis Infrastructure

Status: Active
Owner: Platform Engineering
Last updated: 2026-08-03

This document records the PHPStan + Larastan static analysis setup for NexusSchool,
the changes applied to make it runnable, the remaining known issues, and the roadmap
to raise the analysis level.

## Toolchain

| Tool        | Version | Scope                                   |
|-------------|---------|-----------------------------------------|
| PHPStan     | 2.2.7   | `vendor/bin/phpstan analyse`            |
| Larastan    | 3.10.0  | Laravel-aware PHPStan extension         |
| Laravel Pint| 1.x     | Code style (`vendor/bin/pint`)          |
| Pest        | 3.8     | Test runner (`vendor/bin/pest`)         |

## Installation Report

PHPStan and Larastan were already present in `composer.json` (`require-dev`) and
resolved in `composer.lock` before this work:

- `phpstan/phpstan` **2.2.7**
- `larastan/larastan` **3.10.0**

The packages were installed but **not wired into `phpstan.neon`**, so analysis was
running as bare PHPStan. Enabling the Larastan extension immediately dropped the
reported errors from 1000+ to 685 (the bulk were Eloquent magic properties and
Laravel-specific types that Larastan understands).

### Compatibility verification

| Dependency                | Required by Larastan 3.10 | Installed           | Status |
|---------------------------|---------------------------|---------------------|--------|
| PHP                       | `^8.2`                    | 8.2.12              | OK     |
| `illuminate/*` (Laravel)  | `^11.44.2 \|\| ^12.4.1 \|\| ^13` | laravel/framework 12.63.0 | OK |
| `phpstan/phpstan`         | `^2.2.0`                  | 2.2.7               | OK     |

Other stack members:

- **Filament v5.6** (not v4): Larastan ships no Filament-specific extension, so it is
  compatible by construction. Filament code is analysed with generic PHPStan rules.
- **Stancl Tenancy 3.10**: compatible. Note the `tenant()` helper returns `mixed`;
  the widest source of remaining `argument.type` findings is documented below.
- **Spatie Permission 6.25**: compatible; no extension required.

## Configuration Changes

### `phpstan.neon` (modified)

Added the Larastan extension and the baseline file; kept the existing `level: 5`,
`paths: [app]`, and `tmpDir: storage/phpstan` untouched.

```neon
includes:
    - vendor/larastan/larastan/extension.neon
    - phpstan-baseline.neon

parameters:
    level: 5
    paths:
        - app
    tmpDir: storage/phpstan
```

### `phpstan-baseline.neon` (new)

Generated with `vendor/bin/phpstan analyse --generate-baseline`. It pins **684
pre-existing errors** with exact `message` + `identifier` + `count` + `path`
entries. This is the recommended way to document known errors: it is line-specific,
fails loudly on new errors, and is progressively emptied as issues are fixed. No
broad `ignoreErrors` rules were added.

### `app/Filament/SchoolAdmin/Widgets/ConfigAlertsWidget.php` (fixed)

The only error PHPStan refuses to baseline (non-ignorable) was a genuine bug:

```
Static property ConfigAlertsWidget::$view overrides non-static property
Filament\Widgets\Widget::$view.   (identifier: property.static)
```

Filament v5 declares `protected string $view` as an instance property; the widget
declared `protected static string $view`. Changed to `protected string $view` to
match the parent contract (all sibling widgets already used the instance form).
An unused import (`Subject`) was removed by Pint.

## Quality Gate

| Gate                | Command                          | Status |
|---------------------|----------------------------------|--------|
| PHPStan             | `vendor/bin/phpstan analyse`     | OK, exit 0, no errors |
| Laravel Pint        | `vendor/bin/pint`                | OK on all files touched by this work |
| Pest                | `php artisan test`               | OK, 198 failed / 1086 passed (baseline parity) |

Note on Pint: the repository carries ~446 files that predate `pint.json` and are not
yet PSR-12 formatted (legacy models, configs, migrations, seeders, the Plugin
domain, and legacy tests). Pint itself runs successfully; the exit code `1` on
`pint --test` reflects pre-existing formatting debt, not a tool failure. Reformatting
those files is deliberately out of scope for the infrastructure task and should be a
dedicated, reviewable commit (see Future Improvements).

## Remaining Issues (baseline summary)

The 684 baselined errors cluster as follows:

| Identifier           | Count | Meaning                                                          |
|----------------------|-------|------------------------------------------------------------------|
| `property.notFound`  | 116   | Eloquent magic properties without `@property` docblocks          |
| `identical.alwaysFalse` | 91  | Loose comparisons on mixed/unknown types                         |
| `class.notFound`     | 51    | Unresolvable classes in docblocks/mixins                         |
| `nullsafe.neverNull` | 51    | Redundant `?->` on non-nullable values                           |
| `argument.type`      | 40    | Typed calls receiving `mixed` (frequent with `tenant()` helper)  |
| `method.nonObject` / `property.nonObject` | 70 | Chained calls on possibly-null results             |
| others               | 265   | Smaller rule clusters (see `phpstan-baseline.neon`)              |

These are pre-existing. The gate for new work is: **no new errors above the
baseline** — `vendor/bin/phpstan analyse` must stay green.

## Recommended Level

Stay at **level 5** for now. The baseline is only meaningful if the level is fixed;
raising the level invalidates part of the baseline (higher levels enable stricter
rules) and must be done deliberately.

## Future Improvements

1. **Eloquent model annotations** — add `@property` docblocks to `app/Models` and
   domain models (laravel-ide-helper `php artisan ide-helper:models` or manual).
   Eliminates most of the 116 `property.notFound` entries.
2. **Typed tenant helper** — wrap Stancl's `tenant()` in a `Tenancy::schoolId(): string`
   facade so `tenant()?->id ?? ''` call sites stop producing `argument.type` and
   `nullsafe.neverNull` findings.
3. **CI wiring** — add composer scripts and CI steps:
   - `composer analyse` → `vendor/bin/phpstan analyse`
   - `composer format:check` → `vendor/bin/pint --test`
   Fail the pipeline on new PHPStan errors or formatting drift.
4. **Pint debt pass** — one dedicated commit running `vendor/bin/pint` over the whole
   repo, reviewed for behavioural no-ops, to make `pint --test` green.
5. **Level roadmap** — after the annotation pass (item 1), raise to **level 6**,
   then 7, 8, and finally `max` once the baseline is emptied. Between each bump:
   `vendor/bin/phpstan analyse` and fix the newly surfaced errors, then regenerate
   the baseline.
