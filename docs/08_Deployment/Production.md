# Production Environment

## Purpose

Define the standards, requirements, and operational practices for the Nexus School production environment.

---

# Objectives

- High availability
- Security
- Scalability
- Reliability
- Business continuity

---

# Infrastructure

Production consists of:

- Reverse Proxy (Nginx)
- Laravel Application
- Queue Workers
- Scheduler
- PostgreSQL
- Redis
- Object Storage
- Monitoring Services

---

# Server Requirements

Recommended:

- Ubuntu LTS
- Docker Engine
- Docker Compose
- PostgreSQL 16+
- Redis 7+
- Nginx
- PHP 8.4

---

# Security

Production must enforce:

- HTTPS
- TLS 1.3
- Firewall
- Fail2Ban
- Automatic Security Updates
- Database Encryption
- Secret Management

---

# Environment Variables

Sensitive values must be stored in:

.env

Never commit secrets to Git.

---

# Filament Assets

Filament serves precompiled JS/CSS from `public/`. Those files must match the
installed Filament packages. After every `composer install` or `composer update`,
republish them before serving traffic:

```bash
php artisan filament:assets
php artisan optimize:clear
```

Never deploy a Filament version change without this step. Stale published
assets can stop Alpine from booting on every panel page, which breaks the
password reveal toggle and can make the login form report
"The password field is required." even though the password was entered.

Asset URLs are versioned with the package version. When republishing the same
version, purge any browser or CDN cache (or hard-refresh) so clients do not
keep serving the previous copy of the file.

---

# Scaling

Support:

- Vertical Scaling
- Horizontal Scaling
- Read Replicas
- Load Balancing

---

# Maintenance

Regularly perform:

- Dependency updates
- Security patches
- Database optimization
- Log cleanup
- Backup verification

---

# Availability Target

Target uptime

99.9%

---

# Disaster Recovery

Production must support:

- Backup restoration
- Infrastructure replacement
- Database recovery
- Service failover