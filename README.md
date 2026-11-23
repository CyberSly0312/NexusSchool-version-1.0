# Nexus School ERP


> A modern, scalable, AI-ready School Management ERP built for African educational institutions.

Nexus School is a next-generation multi-tenant SaaS platform that helps schools manage academics, students, finances, transport, library, communication, and more from a single application. Built with Domain-Driven Design, event-driven architecture, and security-first principles.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.4, Laravel 12 |
| Admin Panel | Filament 4, Livewire |
| Frontend | Blade, TailwindCSS, AlpineJS |
| Database | MySQL (database-per-tenant) |
| Cache/Queue | Redis |
| Auth | Laravel Sanctum (OAuth), Spatie RBAC |
| Multi-tenancy | stancl/tenancy |
| PDF | DomPDF, Spatie PDF |
| AI | OpenAI, Anthropic, Gemini, Azure, Ollama |

## Features

**30 domain modules** covering the full school lifecycle:

| Category | Modules |
|----------|---------|
| **Academics** | Academic Year, Term, Class, Section, Grade Level, Subject, Subject Category, Subject Coefficient, Classroom, Program, Gradebook |
| **People** | Student, Guardian, User, Role, Permission |
| **Student Lifecycle** | Student Enrollment |
| **Operations** | Transport, Library, Communication |
| **Intelligence** | Analytics, AI Platform |
| **Automation** | Workflow Engine |
| **System** | School, System Administration, API |

Plus: 71 Eloquent models, 57 Filament resources, 143 events, 122 listeners, 838+ test methods, and a full RESTful API.

## Requirements

- PHP 8.4+
- MySQL 8.0+
- Node.js 18+
- Composer

## Installation

```bash
# Clone the repository
git clone https://github.com/nexus-technologies/nexus-school.git
cd nexus-school

# Install dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Configure database in .env, then migrate
php artisan migrate

# Frontend assets
npm install
npm run build
```

Or use the setup script:

```bash
composer setup
```

## Development

Run the full development stack (server, queue, logs, vite) concurrently:

```bash
composer dev
```

This starts:
- `php artisan serve` — HTTP server
- `php artisan queue:listen` — Queue worker
- `php artisan pail` — Log viewer
- `npm run dev` — Vite asset compiler

## Testing

```bash
composer test
```

Runs PHPUnit with cleared config. The test suite covers 838+ unit tests and 124+ feature tests across all domains.

## Architecture

```
app/
├── Domains/           # 28 domain modules (DDD bounded contexts)
│   ├── AcademicClass/
│   ├── Student/
│   ├── Library/
│   ├── Transport/
│   ├── Communication/
│   ├── Ai/
│   ├── Analytics/
│   ├── Workflow/
│   └── ...
├── Filament/          # Filament admin panel resources
├── Http/              # Controllers, middleware, API layer
├── Infrastructure/    # Cross-cutting concerns, traits
├── Models/            # Shared/base Eloquent models
├── Jobs/              # Queued jobs
├── Console/           # Artisan commands
└── Providers/         # Service providers
```

**Key design decisions:**

- **Domain-Driven Design** — Each domain is a self-contained module with its own models, services, policies, events, and tests.
- **Multi-tenant** — Database-per-tenant isolation via `stancl/tenancy`. Each school operates on its own database.
- **Event-driven** — 143 domain events with 122 listeners for decoupled, extensible workflows.
- **ULID primary keys** — All models use ULIDs for sortability and global uniqueness.
- **Soft deletes** — Every model supports soft deletion for data recovery.
- **RBAC** — Role-based access control via Spatie with per-tenant role management.

For full architecture documentation, see [`docs/`](docs/README.md) and [`architecture/`](architecture/).

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for code style, testing requirements, and PR process.

## License

MIT License.

## Contributors

Built by [Nexus Technologies](https://github.com/nexus-technologies).



