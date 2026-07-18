# Contributing to Nexus School ERP

Thank you for your interest in contributing. This document outlines the standards and process for contributing to the project.

## Code Style

- Follow **PSR-12** coding standard (enforced by Laravel Pint).
- Always declare `declare(strict_types=1);` at the top of every PHP file.
- Use `composer format` before committing to auto-fix style issues.

## Domain-Driven Design

The project is organized into domain modules under `app/Domains/`. Each domain should be self-contained:

```
app/Domains/Student/
├── Models/
├── Services/
├── Policies/
├── Events/
├── Listeners/
├── Filament/
├── Http/
└── Tests/
```

When adding a new domain or feature:

1. Place code in the correct domain module.
2. Keep domain logic out of controllers — use Services.
3. Use events for cross-domain communication.
4. Never import models from unrelated domains directly.

## Testing

- Every new feature must include tests.
- Run the full suite before submitting: `composer test`.
- Unit tests go in `tests/Unit/{Domain}/`.
- Feature tests go in `tests/Feature/{Domain}/`.
- Target meaningful coverage, not just line count.

## Commit Messages

Use conventional commits:

```
<type>(<scope>): <description>
```

Types: `feat`, `fix`, `refactor`, `test`, `docs`, `chore`, `perf`, `security`

Examples:
- `feat(library): add book checkout workflow`
- `fix(student): correct enrollment date validation`
- `security(api): add tenant isolation to student endpoints`
- `refactor(academic): extract shared grading logic to service`

## Pull Request Process

1. Create a feature branch from `main`.
2. Make your changes following the guidelines above.
3. Ensure all tests pass (`composer test`) and code is formatted (`composer format`).
4. Open a PR with a clear description of what changed and why.
5. Link any related issues.
6. Wait for review approval before merging.

## Questions?

Open a discussion or reach out to the maintainers.
