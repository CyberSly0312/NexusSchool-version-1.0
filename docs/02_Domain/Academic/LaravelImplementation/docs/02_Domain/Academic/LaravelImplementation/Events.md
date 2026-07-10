# Academic Module
# Laravel Implementation
## Events

Version : 1.0

---

# Objectif

Ce document décrit l'implémentation des événements (Events) du module Academic.

Les Events permettent de notifier les autres composants du système lorsqu'une opération métier importante est réalisée.

Ils favorisent une architecture découplée et orientée événements (Event-Driven Architecture).

---

# Organisation

Les événements sont regroupés dans :

```
app/
└── Domain/
    └── Academic/
        └── Events/
```

Exemple :

```
AcademicYearCreated.php

AcademicYearActivated.php

AcademicYearClosed.php

AcademicTermCreated.php

ProgramCreated.php

ClassroomCreated.php

SubjectCreated.php

EnrollmentCreated.php

StudentPromoted.php
```

---

# Responsabilités

Un Event représente un fait métier déjà accompli.

Exemples :

- une année académique a été créée ;
- une inscription a été validée ;
- un élève a été promu ;
- une matière a été archivée.

Les Events ne contiennent aucune logique métier.

---

# Déclenchement

Les Events sont déclenchés uniquement par les Services.

Exemple :

```php
EnrollmentCreated::dispatch($enrollment);
```

ou

```php
event(new EnrollmentCreated($enrollment));
```

Les Controllers et les Resources Filament ne déclenchent jamais directement les Events.

---

# Contenu d'un Event

Un Event transporte uniquement les informations nécessaires.

Exemple :

```php
class EnrollmentCreated
{
    public function __construct(
        public Enrollment $enrollment,
    ) {}
}
```

Éviter de transmettre des données inutiles.

---

# Listeners

Chaque Event peut être associé à plusieurs Listeners.

Exemple :

```
EnrollmentCreated

↓

GenerateStudentNumberListener

↓

NotifyParentsListener

↓

CreateFinanceAccountListener

↓

WriteAuditLogListener
```

Les Listeners sont indépendants les uns des autres.

---

# Événements synchrones

Les événements critiques peuvent être exécutés immédiatement.

Exemple :

- création d'une année académique ;
- activation d'une période.

---

# Événements asynchrones

Les traitements longs doivent être placés dans la file d'attente.

Exemples :

- envoi d'e-mails ;
- génération de rapports ;
- notifications ;
- synchronisation avec des systèmes externes.

Utiliser :

```php
ShouldQueue
```

---

# Multi-tenant

Tous les traitements déclenchés par un Event doivent conserver le contexte de l'établissement.

Les Listeners doivent toujours utiliser le :

```
school_id
```

correspondant.

---

# Gestion des erreurs

Une erreur dans un Listener ne doit pas compromettre la transaction métier déjà validée.

Les erreurs doivent être :

- journalisées ;
- surveillées ;
- éventuellement relancées via la queue.

---

# Tests

Chaque Event doit être testé pour vérifier :

- son déclenchement ;
- les données transportées ;
- les Listeners associés ;
- les traitements asynchrones.

Utiliser :

```php
Event::fake();

Event::assertDispatched();
```

---

# Bonnes pratiques

- Un Event représente un fait passé.
- Nommer les Events au passé.
- Ne jamais modifier les données dans un Event.
- Garder les Events simples.
- Utiliser des Listeners pour les traitements secondaires.

---

# Résumé

Les Events permettent au module Academic de communiquer avec les autres modules de Nexus School de manière découplée.

Ils améliorent la maintenabilité, la réutilisabilité et l'évolutivité de l'application en séparant les traitements métier des traitements secondaires.