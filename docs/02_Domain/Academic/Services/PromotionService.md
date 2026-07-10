# Academic Module
# Services
## PromotionService

Version : 1.0

---

# Objectif

PromotionService centralise toute la logique métier liée au passage des élèves d'une année académique à la suivante.

Il est responsable de la promotion, du redoublement, de la réorientation et de la création des nouvelles inscriptions académiques.

Toutes les opérations de fin d'année doivent passer par ce service.

---

# Responsabilités

Le service est responsable de :

- promotion d'un élève
- promotion de plusieurs élèves
- promotion d'une classe entière
- redoublement
- changement de série
- changement de programme
- création des nouvelles inscriptions
- clôture de l'ancienne inscription
- conservation de l'historique académique
- validation des critères de promotion

---

# Dépendances

PromotionService utilise :

- EnrollmentService
- AcademicYearService
- ClassroomService
- GradeService
- ReportCardService
- Audit Log Service
- Event Dispatcher

---

# Méthodes publiques

## promoteStudent()

Promouvoir un élève.

Entrée

```
Enrollment

TargetClassroom
```

Retour

```
Enrollment
```

---

## promoteClassroom()

Promouvoir tous les élèves d'une classe.

Retour

```
Collection<Enrollment>
```

---

## promoteLevel()

Promouvoir tous les élèves d'un niveau.

---

## repeatStudent()

Créer un redoublement.

---

## transferSeries()

Changer la série d'un élève.

Exemple

Première C

↓

Première D

---

## transferProgram()

Changer le programme.

Exemple

Général

↓

Technique

---

## validatePromotion()

Vérifie

si un élève est promouvable.

Retour

```
bool
```

---

## calculateTargetLevel()

Détermine automatiquement

le niveau suivant.

---

## createNextEnrollment()

Créer la nouvelle inscription.

---

## closeCurrentEnrollment()

Clôturer l'inscription actuelle.

---

## rollbackPromotion()

Annuler complètement

une promotion.

---

# Workflow

Validation

↓

Calcul des résultats

↓

Validation des critères

↓

Détermination du niveau suivant

↓

Création de la nouvelle Enrollment

↓

Clôture de l'ancienne Enrollment

↓

Audit Log

↓

Events

↓

Notifications

---

# Validation métier

Le service vérifie :

✓ année académique clôturée

✓ bulletins publiés

✓ moyennes calculées

✓ décisions disponibles

✓ classe de destination valide

✓ capacité disponible

✓ cohérence académique

---

# Décisions possibles

Promoted

Passage

---

Repeated

Redoublement

---

Transferred

Réorientation

---

Graduated

Diplômé

---

Withdrawn

Sortie du système

---

# Gestion des redoublements

Le service :

- conserve l'historique
- crée une nouvelle inscription
- référence l'ancienne inscription
- conserve les anciens bulletins

---

# Gestion des diplômés

Lorsque l'élève termine

le dernier niveau

le service :

- clôture définitivement l'inscription
- marque le statut Graduated
- empêche toute promotion supplémentaire

---

# Exceptions

PromotionNotAllowedException

---

PromotionValidationException

---

ClassroomCapacityExceededException

---

GraduationCompletedException

---

AcademicYearNotClosedException

---

PromotionRollbackException

---

# Audit Log

Toutes les opérations doivent être historisées.

Exemples

Promote Student

Promote Classroom

Repeat Student

Transfer Series

Transfer Program

Graduate Student

Rollback Promotion

---

# Événements déclenchés

StudentPromoted

StudentRepeated

StudentGraduated

StudentTransferred

PromotionCompleted

PromotionRolledBack

---

# Transactions

Les opérations suivantes doivent être atomiques :

- promoteStudent()

- promoteClassroom()

- repeatStudent()

- transferSeries()

- createNextEnrollment()

- rollbackPromotion()

Rollback complet

en cas d'erreur.

---

# Sécurité

Le service vérifie :

- Tenant courant

- Permissions

- École

- Année académique

- Cohérence des données

Toutes les promotions sont limitées à une seule école.

---

# Performance

Optimisations recommandées

- Traitement par lots (Batch Processing)

- Queue Jobs pour les promotions massives

- Transactions optimisées

- Index sur

    enrollment_id

    academic_year_id

    classroom_id

    promotion_status

---

# Intégration avec les autres modules

PromotionService met à jour :

- Enrollment
- Gradebook
- Report Cards
- Attendance
- Finance
- Library
- Transport
- Hostel

Les autres modules doivent être informés via des événements.

---

# Tests unitaires

✓ Promotion individuelle

✓ Promotion de masse

✓ Redoublement

✓ Réorientation

✓ Diplôme

✓ Validation métier

✓ Rollback

✓ Audit Log

✓ Events

✓ Multi-tenant

---

# Diagramme simplifié

```
Controller
      │
      ▼
PromotionService
      │
      ├── Validation
      ├── Grade Verification
      ├── Decision Engine
      ├── Target Classroom
      ├── Enrollment Creation
      ▼
Repository
      ▼
Database
      ▼
Events
      ▼
Audit Log
```

---

# Résumé

PromotionService est l'orchestrateur de la transition entre deux années académiques.

Il garantit :

- une promotion cohérente et sécurisée des élèves ;
- la conservation intégrale de l'historique scolaire ;
- la création des nouvelles inscriptions académiques ;
- le traitement des redoublements, réorientations et diplômés ;
- une exécution transactionnelle avec possibilité de rollback ;
- une communication fiable avec les autres modules via les événements.