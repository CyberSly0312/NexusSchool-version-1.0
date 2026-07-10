# Academic Module
# Laravel Implementation
## Observers

Version : 1.0

---

# Objectif

Ce document décrit les conventions d'implémentation des Observers du module Academic.

Les Observers permettent d'exécuter automatiquement certaines actions lors des événements du cycle de vie des modèles Eloquent.

Ils centralisent les traitements techniques liés aux modèles tout en maintenant les modèles légers.

---

# Organisation

Les Observers sont regroupés dans :

```
app/
└── Domain/
    └── Academic/
        └── Observers/
```

Exemple :

```
AcademicYearObserver.php

AcademicTermObserver.php

ProgramObserver.php

ClassroomObserver.php

SubjectObserver.php

EnrollmentObserver.php
```

---

# Responsabilités

Les Observers sont responsables de :

- préparer certaines données ;
- enregistrer des informations techniques ;
- déclencher des événements techniques si nécessaire ;
- maintenir la cohérence des modèles.

Ils ne doivent jamais contenir de logique métier complexe.

---

# Événements observés

Un Observer peut implémenter les méthodes suivantes :

```php
creating()

created()

updating()

updated()

saving()

saved()

deleting()

deleted()

restoring()

restored()

forceDeleted()
```

Chaque méthode correspond à un événement du cycle de vie Eloquent.

---

# Enregistrement

Les Observers sont enregistrés dans un Service Provider.

Exemple :

```php
public function boot(): void
{
    AcademicYear::observe(AcademicYearObserver::class);

    AcademicTerm::observe(AcademicTermObserver::class);

    Subject::observe(SubjectObserver::class);

    Enrollment::observe(EnrollmentObserver::class);
}
```

---

# Exemples d'utilisation

## creating()

Attribuer automatiquement :

- UUID
- code interne
- valeurs par défaut

---

## updating()

Mettre à jour automatiquement :

- updated_by
- informations d'audit

---

## deleting()

Empêcher certaines suppressions si nécessaire.

Exemple :

- une année académique contenant encore des inscriptions.

---

## restored()

Réinitialiser certaines données techniques après restauration.

---

# Audit

Les Observers peuvent enregistrer automatiquement :

- utilisateur créateur ;
- utilisateur modificateur ;
- utilisateur ayant supprimé la donnée.

Exemple :

```php
$model->created_by = auth()->id();
```

---

# Déclenchement d'événements

Les Observers peuvent déclencher des événements techniques.

Exemple :

```php
ModelCreated::dispatch($model);
```

Les événements métier restent déclenchés par les Services.

---

# Limites

Les Observers ne doivent pas :

- envoyer des e-mails ;
- appeler des API externes ;
- exécuter des traitements longs ;
- contenir des règles métier.

Ces traitements appartiennent aux Services ou aux Listeners.

---

# Multi-tenant

Les Observers peuvent automatiquement renseigner :

```php
school_id
```

lors de la création d'un enregistrement si nécessaire.

---

# Tests

Chaque Observer doit être testé pour :

- création ;
- modification ;
- suppression ;
- restauration ;
- attribution automatique des données ;
- audit.

---

# Bonnes pratiques

- Un Observer par modèle.
- Aucun traitement métier.
- Utiliser les événements Eloquent uniquement.
- Garder les méthodes courtes.
- Déléguer les traitements complexes.

---

# Résumé

Les Observers centralisent les traitements techniques liés au cycle de vie des modèles Eloquent.

Ils permettent d'automatiser les tâches répétitives tout en gardant les modèles propres et les Services concentrés sur la logique métier.