# Changelog

All notable changes to Nexus School ERP will be documented in this file.

## [1.0.0-RC1] - 2026-07-17

### Added
- 30 domain modules (Academics, Students, Library, Transport, Communication, Analytics, AI Platform, Workflow Engine, System Administration)
- 71 Eloquent models with ULID primary keys and soft deletes
- 57 Filament admin resources with 194 pages
- RESTful API with OAuth token authentication
- Multi-tenant architecture (database-per-tenant)
- Role-based access control (RBAC) via Spatie
- Event-driven architecture with 143 events and 122 listeners
- 838+ unit test methods across 25+ domains
- 124+ feature test methods across 37 resources
- Automated health checks, backups, and maintenance windows
- AI integration platform (OpenAI, Anthropic, Gemini, Azure, Ollama)
- Workflow automation engine
- PDF generation via DomPDF

### Security
- Fixed timing attack vulnerability in OAuth client secret comparison
- Added tenant isolation to all API endpoints
- Added authorization checks to all API controllers
- Enforced token abilities/permissions
- Added rate limiting to OAuth token endpoint
- Added CORS configuration
- Fixed daily rate limit enforcement
- All policies now enforce multi-tenant scoping

### Changed
- Consolidated 10 duplicate Active/Archived status enums into shared enums
- Extracted HasEntityExtraction trait from 16 duplicated listener methods
- Created UniqueValidator trait for common validation patterns
- Added `school_id` to GradeLevel, Section, and AcademicClass models
- Fixed users.school_id foreign key to reference tenants table

### Fixed
- Publisher model status cast corrected from AuthorStatus to PublisherStatus
- BookCategory model status cast corrected from BookStatus to BookCategoryStatus
- subject_categories.display_order unique constraint now per-school
- API routes properly registered in bootstrap/app.php
