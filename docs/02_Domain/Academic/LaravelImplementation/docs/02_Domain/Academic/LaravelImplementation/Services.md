# Academic Module
# Laravel Implementation
## Services

Version : 1.0

---

# Objectif

Ce document décrit les conventions d'implémentation des Services du module Academic.

Les Services contiennent toute la logique métier de l'application.

Ils orchestrent les opérations complexes, manipulent les modèles, déclenchent les événements et assurent la cohérence des transactions.

---

# Organisation

Tous les Services sont regroupés dans :

```
app/
└── Domain/
    └── Academic/
        └── Services/
```

Exemple :

```
AcademicYearService.php

AcademicTermService.php

ProgramService.php

ClassroomService.php

SubjectService.php

EnrollmentService.php

PromotionService.php
```

---

# Responsabilités

Les Services sont responsables de :

- appliquer les règles métier ;
- manipuler les modèles ;
- exécuter les transactions ;
- déclencher les événements ;
- appeler les Repositories ;
- retourner des DTOs ou des modèles.

---

# Les Services ne doivent jamais

- afficher des vues ;
- retourner des réponses HTTP ;
- effectuer des validations d'entrée ;
- vérifier les permissions.

Ces responsabilités appartiennent aux autres couches.

---

# Injection de dépendances

Les Services utilisent l'injection de dépendances.

Exemple :

```php
public function __construct(

    EnrollmentRepository $repository,

    EventDispatcher $events

)
{
}
```

---

# Transactions

Toute opération critique doit être exécutée dans une transaction.

Exemple :

```php
DB::transaction(function () {

    // logique métier

});
```

---

# Gestion des événements

Les Services déclenchent les Domain Events.

Exemple :

```php
EnrollmentCreated::dispatch($enrollment);

StudentPromoted::dispatch($student);

AcademicYearClosed::dispatch($year);
```

---

# Gestion des exceptions

Les erreurs métier utilisent des exceptions spécifiques.

Exemple :

```php
AcademicYearAlreadyActiveException

EnrollmentAlreadyExistsException

InvalidPromotionException
```

Les Services ne doivent jamais masquer une erreur métier.

---

# Retour des méthodes

Les Services retournent :

- un Model ;
- un DTO ;
- une Collection ;
- un booléen lorsque cela est approprié.

Ils ne retournent jamais de Response HTTP.

---

# Exemple de responsabilités

AcademicYearService

- créer une année académique ;
- activer une année ;
- clôturer une année ;
- archiver une année.

---

EnrollmentService

- inscrire un élève ;
- suspendre une inscription ;
- annuler une inscription ;
- transférer un élève.

---

PromotionService

- calculer les promotions ;
- promouvoir les élèves ;
- gérer les redoublements ;
- générer les nouvelles inscriptions.

---

SubjectService

- créer une matière ;
- affecter un enseignant ;
- archiver une matière.

---

# Réutilisation

Les Services sont appelés par :

- Controllers ;
- Filament Resources ;
- API ;
- Console Commands ;
- Jobs ;
- Listeners.

Toute logique métier doit être centralisée dans les Services.

---

# Tests

Chaque Service doit être testé pour :

- scénario nominal ;
- cas d'erreur ;
- exceptions ;
- transactions ;
- événements déclenchés.

---

# Bonnes pratiques

- Un Service par domaine métier.
- Une méthode par cas d'utilisation.
- Méthodes courtes et explicites.
- Transactions sur les opérations critiques.
- Exceptions métier dédiées.
- Déclencher les événements uniquement après succès.

---

# Résumé

Les Services constituent le cœur de l'implémentation métier du module Academic.

Ils garantissent une séparation claire des responsabilités et assurent une logique métier cohérente, testable et réutilisable dans l'ensemble de Nexus School.