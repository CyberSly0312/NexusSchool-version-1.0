# Academic Module
# Laravel Implementation
## Testing

Version : 1.0

---

# Objectif

Ce document décrit la stratégie de tests du module Academic.

Les tests garantissent que les fonctionnalités du module restent fiables, sécurisées et stables au fil des évolutions.

Le module Academic adopte une approche orientée qualité avec une couverture de tests automatisés.

---

# Architecture des tests

Les tests sont organisés dans :

```
tests/

├── Unit/

├── Feature/

├── Integration/

└── Pest.php
```

Le projet utilise Pest PHP comme framework principal.

---

# Types de tests

Le module Academic comprend :

- Unit Tests
- Feature Tests
- Integration Tests
- Database Tests

Chaque niveau répond à un objectif précis.

---

# Unit Tests

Les Unit Tests vérifient :

- Services
- DTO
- Enums
- Rules
- Helpers

Ils ne dépendent pas de la base de données lorsque cela est possible.

---

# Feature Tests

Les Feature Tests vérifient :

- Controllers
- API
- Filament Resources
- Policies
- Middleware
- Authentification

Ils simulent le comportement réel de l'application.

---

# Integration Tests

Les Integration Tests valident :

- Services + Repository
- Services + Events
- Services + Queue
- Services + Notifications

Ils vérifient que plusieurs composants fonctionnent correctement ensemble.

---

# Database Tests

Les Database Tests utilisent :

```
RefreshDatabase
```

ou

```
DatabaseTransactions
```

Les données sont recréées automatiquement entre les tests.

---

# Factories

Toutes les entités utilisent leurs Factories.

Exemple :

```php
AcademicYear::factory()->create();

Enrollment::factory()->create();

Student::factory()->create();
```

---

# Seeders

Les Seeders de démonstration peuvent être utilisés pour préparer les scénarios complexes.

Les données critiques restent créées directement dans les tests.

---

# Authentification

Les utilisateurs de test possèdent différents rôles.

Exemple :

```
Super Administrator

Academic Director

Registrar

Teacher

Student

Parent
```

Chaque rôle est testé indépendamment.

---

# Permissions

Les tests vérifient :

- accès autorisé ;
- accès refusé ;
- School Scope ;
- Ownership ;
- Policies.

---

# Events

Les événements sont simulés avec :

```php
Event::fake();
```

Puis vérifiés avec :

```php
Event::assertDispatched();
```

---

# Queues

Les Jobs utilisent :

```php
Bus::fake();

Queue::fake();
```

Les traitements sont validés sans être réellement exécutés.

---

# Notifications

Les notifications utilisent :

```php
Notification::fake();
```

Puis :

```php
Notification::assertSentTo();
```

---

# Tests API

Les API sont testées pour :

- HTTP Status
- Validation
- Authentification
- Permissions
- Pagination
- Format JSON

---

# Couverture

Objectif minimal :

```
80 %
```

Objectif recommandé :

```
90 % et plus
```

Les Services métier critiques doivent viser une couverture proche de 100 %.

---

# Intégration Continue

Les tests sont exécutés automatiquement :

- Pull Requests
- Merge
- Release

Aucun code ne doit être fusionné si les tests échouent.

---

# Bonnes pratiques

- Un test = un comportement.
- Nommer les tests clairement.
- Utiliser les Factories.
- Éviter les dépendances entre tests.
- Tester les cas d'erreur.
- Tester les permissions.

---

# Résumé

Les tests assurent la qualité du module Academic.

Ils permettent de détecter rapidement les régressions et garantissent que chaque évolution respecte les règles métier et techniques définies par Nexus School.