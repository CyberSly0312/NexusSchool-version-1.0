# DEC-001: Database-per-Tenant Multi-tenancy

**Status:** Accepted  
**Date:** 2026-07-01  
**Decision Makers:** Nexus Technologies Engineering  

## Context

Nexus School ERP is a SaaS platform serving multiple schools. Each school must operate independently with full data isolation. We needed to choose a multi-tenancy strategy that balances:

- **Data isolation** — Schools must never see each other's data.
- **Operational simplicity** — Deployment and maintenance should remain manageable.
- **Scalability** — The architecture must support growth from tens to thousands of tenants.
- **Cost** — Infrastructure costs must stay reasonable for African market pricing.

The three primary approaches considered:

| Approach | Isolation | Complexity | Cost |
|----------|-----------|------------|------|
| Shared database, shared tables | Low | Low | Low |
| Shared database, separate tables | Medium | Medium | Low |
| Separate database per tenant | High | Medium | Medium |

## Decision

We adopt **database-per-tenant** isolation using [stancl/tenancy](https://github.com/stancl/tenancy) for Laravel.

Each tenant (school) gets its own MySQL database. The central `tenants` database stores only tenant registry data (id, domain, database name). Application data never crosses tenant boundaries.

### Implementation Details

- Tenant identification: subdomain or domain-based resolution.
- Database provisioning: automatic on tenant creation via `Tenants::create()`.
- Migrations: run centrally and automatically applied to all tenant databases.
- Central models (`Tenant`) live in the `tenants` connection.
- All application models are scoped to the tenant database by default.
- `school_id` on tenant-scoped models provides an additional logical isolation layer.

## Consequences

### Positive
- **Strong isolation** — Physical database separation prevents accidental data leaks.
- **Per-tenant backups** — Individual schools can be backed up or restored independently.
- **Per-tenant maintenance** — One tenant's issues don't affect others.
- **Regulatory compliance** — Meets data residency requirements for schools in different regions.
- **Simple recovery** — Restore a single tenant without affecting others.

### Negative
- **Operational overhead** — Schema changes must be applied across all tenant databases.
- **Connection management** — More database connections than a shared-table approach.
- **Migration coordination** — Failed migrations on one tenant must be handled gracefully.
- **Cost at scale** — More databases means higher hosting costs than shared-table approaches.

### Mitigations
- Use `stancl/tenancy` built-in migration commands to apply schema changes to all tenants.
- Use connection pooling (ProxySQL or similar) at scale.
- Implement automated tenant migration health checks in CI/CD.
- Monitor database count and plan infrastructure scaling proactively.
