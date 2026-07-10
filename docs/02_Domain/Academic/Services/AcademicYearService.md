# Academic Module
# Services
## AcademicYearService

Version : 1.0

---

# Objectif

AcademicYearService centralise toute la logique métier liée aux années académiques.

Aucun Controller, Filament Resource ou API ne doit manipuler directement le modèle AcademicYear.

Toutes les opérations passent par ce service.

---

# Responsabilités

Le service est responsable de :

- création d'une année académique
- modification
- activation
- fermeture
- archivage
- suppression
- validation
- vérification des conflits de dates
- contrôle des chevauchements
- gestion de l'année active

---

# Dépendances

AcademicYearService utilise :

- AcademicYear Repository
- Validation Rules
- Audit Log Service
- Event Dispatcher

---

# Méthodes publiques

## create()

Créer une nouvelle année académique.

Entrée

```

CreateAcademicYearDTO

```

Sortie

```

AcademicYear

```

---

## update()

Mettre à jour une année académique.

Entrée

```

AcademicYear

UpdateAcademicYearDTO

```

Sortie

```

AcademicYear

```

---

## activate()

Définit une année académique comme année active.

Entrée

```

AcademicYear

```

Sortie

```

bool

```

---

## close()

Ferme une année académique.

Toutes les opérations académiques deviennent interdites.

---

## archive()

Archive une année.

Elle devient uniquement consultable.

---

## delete()

Supprime une année.

Uniquement si aucune donnée académique n'est présente.

---

## validateDates()

Vérifie

- StartDate
- EndDate

---

## detectOverlap()

Recherche les chevauchements.

Retour

```

true

false

```

---

## hasActiveAcademicYear()

Retourne

```

true

false

```

---

## getCurrentAcademicYear()

Retourne

```

AcademicYear|null

```

---

## getNextAcademicYear()

Retourne

l'année suivante

si elle existe.

---

## getPreviousAcademicYear()

Retourne

l'année précédente.

---

## canDelete()

Retour

```

bool

```

---

## canActivate()

Retour

```

bool

```

---

## canClose()

Retour

```

bool

```

---

# Workflow

Création

↓

Validation

↓

Détection des conflits

↓

Sauvegarde

↓

Audit Log

↓

Event

↓

Notification

---

# Validation métier

Le service doit vérifier :

✓ format du nom

✓ dates

✓ chevauchements

✓ unicité

✓ année active

✓ école

---

# Exceptions

AcademicYearAlreadyExistsException

---

AcademicYearOverlapException

---

AcademicYearHasDependenciesException

---

AcademicYearAlreadyActiveException

---

AcademicYearClosedException

---

AcademicYearValidationException

---

# Audit Log

Toutes les opérations doivent être historisées.

Exemples

Create Academic Year

Update Academic Year

Activate Academic Year

Close Academic Year

Archive Academic Year

Delete Academic Year

---

# Événements déclenchés

AcademicYearCreated

AcademicYearUpdated

AcademicYearActivated

AcademicYearClosed

AcademicYearArchived

AcademicYearDeleted

---

# Transactions

Les opérations suivantes doivent être atomiques :

- create()

- activate()

- close()

- delete()

En cas d'erreur

Rollback complet.

---

# Sécurité

Le service doit vérifier :

- Tenant courant
- Permissions
- Intégrité des données

Aucune opération cross-tenant n'est autorisée.

---

# Performance

Optimisations recommandées

- Cache de l'année active

- Lazy Loading

- Index sur

    school_id

    status

    start_date

    end_date

---

# Tests unitaires

✓ Création

✓ Modification

✓ Activation

✓ Fermeture

✓ Archivage

✓ Suppression

✓ Détection des conflits

✓ Détection du chevauchement

✓ Rollback

✓ Audit Log

✓ Events

✓ Validation multi-tenant

---

# Diagramme simplifié

```

Controller

↓

AcademicYearService

↓

Validation

↓

Repository

↓

Database

↓

Events

↓

Audit Log

```

---

# Résumé

AcademicYearService est l'unique point d'entrée de la logique métier des années académiques.

Il garantit :

- l'intégrité des données ;
- l'unicité de l'année académique active ;
- l'absence de chevauchements ;
- la traçabilité via les journaux d'audit ;
- la sécurité multi-tenant ;
- la cohérence transactionnelle de toutes les opérations.