# NexusSchool — Architecture des PanelProviders

> Dernière mise à jour : 2026-07-24
> Objectif : Séparation stricte des responsabilités par panel. Chaque Resource appartient à un seul panel. Aucun doublon. Aucune route non intentionnelle.

---

## 1. Vue d'ensemble

```
NexusSchool
├── SuperAdmin Panel (/super-admin)          [CENTRAL — pas de tenancy]
├── SchoolAdmin Panel (/school-admin)        [TENANT — gestion établissement]
├── Director Panel (/director)               [TENANT — pilotage (pages custom uniquement)]
├── Teacher Panel (/teacher)                 [TENANT — stub]
├── Student Panel (/student)                 [TENANT — stub]
├── Parent Panel (/parent)                   [TENANT — stub]
├── Secretary Panel (/secretary)             [TENANT — stub]
└── Accountant Panel (/accountant)           [TENANT — stub]
```

- **9 panels** au total, **8 panels** en production (admin supprimé)
- **Tenancy** : `stancl/tenancy` avec modèle `School` — tous les panels sauf SuperAdmin sont tenant-scoped
- **Auto-discovery** : AUCUN panel n'utilise `discoverResources`, `discoverPages` ou `discoverWidgets`
- Chaque panel enregistre **explicitement** ses Resources, Pages et Widgets

---

## 2. SuperAdmin Panel

**Chemin** : `/super-admin`
**Tenancy** : Central (aucun middleware tenancy)
**Rôle** : Gestion de la plateforme SaaS elle-même — écoles, abonnements, configuration système.

### Pages
| Page | Description |
|------|-------------|
| `SuperAdminDashboard` | Tableau de bord SaaS |

### Widgets
| Widget | Description |
|--------|-------------|
| `StatsOverview` | Statistiques globales |
| `SchoolsChart` | Graphique des écoles |
| `RevenueChart` | Graphique des revenus |
| `SubscriptionStatusChart` | Statut des abonnements |

### Resources (11)

| # | Resource | Navigation Group | Modèle |
|---|----------|-----------------|--------|
| 1 | `SuperAdminSchoolResource` | Plateforme | School |
| 2 | `PlanResource` | Plateforme | Plan |
| 3 | `SubscriptionResource` | Plateforme | Subscription |
| 4 | `DomainResource` | Plateforme | CentralDomain |
| 5 | `CentralUserResource` | Plateforme | User |
| 6 | `AuditLogResource` | Administration systeme | AuditLog |
| 7 | `SystemSettingResource` | Administration systeme | SystemSetting |
| 8 | `BackupResource` | Administration systeme | Backup |
| 9 | `SystemHealthResource` | Administration systeme | SystemHealth |
| 10 | `MaintenanceWindowResource` | Administration systeme | MaintenanceWindow |
| 11 | `NotificationChannelResource` | Administration systeme | NotificationChannel |

### Responsabilités

- Gestion des écoles (CRUD super-admin)
- Gestion des plans et abonnements SaaS
- Gestion des domaines centralisés
- Gestion des utilisateurs centraux (hors tenant)
- Audit log, paramètres système, backups, santé système
- Fenêtres de maintenance, canaux de notification

### Permissions

- Accès réservé aux super-admins uniquement
- Aucun accès aux données tenant (élèves, notes, etc.)
- Middleware d'authentification Filament standard

---

## 3. SchoolAdmin Panel

**Chemin** : `/school-admin`
**Tenancy** : Tenant-scoped (`InitializeTenancyForPanel`)
**Rôle** : Configuration et administration complète d'un établissement scolaire. Le SchoolAdmin configure l'école.

### Pages
| Page | Description |
|------|-------------|
| `SchoolAdminDashboard` | Tableau de bord de l'administration |

### Widgets
| Widget | Description |
|--------|-------------|
| `AccountWidget` | Widget de compte |
| `FilamentInfoWidget` | Widget d'information Filament |

### Resources (24)

| # | Resource | Navigation Group | Modèle | Catégorie |
|---|----------|-----------------|--------|-----------|
| 1 | `StudentResource` | Ecole | Student | Élèves |
| 2 | `GuardianResource` | Ecole | Guardian | Élèves |
| 3 | `StudentEnrollmentResource` | Ecole | StudentEnrollment | Élèves |
| 4 | `AcademicYearResource` | Enseignements | AcademicYear | Académique |
| 5 | `AcademicTermResource` | Enseignements | AcademicTerm | Académique |
| 6 | `AcademicClassResource` | Enseignements | AcademicClass | Académique |
| 7 | `ProgramResource` | Enseignements | Program | Académique |
| 8 | `GradeLevelResource` | Enseignements | GradeLevel | Académique |
| 9 | `SectionResource` | Enseignements | Section | Académique |
| 10 | `ClassroomResource` | Installations | Classroom | Installations |
| 11 | `SubjectResource` | Enseignements | Subject | Académique |
| 12 | `SubjectCategoryResource` | Enseignements | SubjectCategory | Académique |
| 13 | `SubjectCoefficientResource` | Enseignements | SubjectCoefficient | Académique |
| 14 | `AssessmentResultResource` | Carnet de notes | AssessmentResult | Examens |
| 15 | `AttendanceResource` | Presences | AttendanceRecord | Présences |
| 16 | `PaymentResource` | Finances | Payment | Finances |
| 17 | `AnnouncementResource` | Communication | Announcement | Communication |
| 18 | `MessageResource` | Communication | Message | Communication |
| 19 | `IncidentResource` | Securite | Incident | Sécurité |
| 20 | `ReportResource` | Analytiques | Report | Rapports |
| 21 | `UserResource` | Administration | User | Utilisateurs |
| 22 | `RoleResource` | Administration | Role | Utilisateurs |
| 23 | `PermissionResource` | Administration | Permission | Utilisateurs |
| 24 | `DepartmentResource` | Administration | Department | Utilisateurs |

### Responsabilités

- **Élèves** : Inscription, gestion des tuteurs, inscriptions scolaires
- **Académique** : Années scolaires, périodes, classes, programmes, niveaux, sections, matières, coefficients
- **Installations** : Salles de classe
- **Examens** : Résultats d'évaluation, carnet de notes
- **Présences** : Gestion des présences/absences
- **Finances** : Paiements, frais scolaires
- **Communication** : Annonces, messages
- **Sécurité** : Incidents
- **Rapports** : Tableaux de bord, exports
- **Administration** : Utilisateurs, rôles, permissions, départements

### Permissions

- Accès limité au tenant courant
- Gestion des rôles et permissions au niveau école
- Les utilisateurs de ce panel voient uniquement les données de leur établissement

---

## 4. Director Panel

**Chemin** : `/director`
**Tenancy** : Tenant-scoped (`InitializeTenancyForPanel`)
**Rôle** : Pilotage de l'établissement via des pages de visualisation et de configuration. **Aucune Resource enregistrée.**

### Pages (4)

| Page | Description | Navigation Group |
|------|-------------|-----------------|
| `DirectorDashboard` | Tableau de bord de pilotage | (racine) |
| `DocumentCenter` | Centre de génération de documents officiels | Documents |
| `SchoolIdentity` | Identité visuelle de l'établissement | Parametres |
| `SchoolWizard` | Assistant de configuration en 5 étapes | Parametres |

### Widgets (5)

| Widget | Description |
|--------|-------------|
| `DirectorStatsOverview` | Statistiques de pilotage |
| `EnrollmentChart` | Graphique des inscriptions |
| `AttendanceChart` | Graphique des présences |
| `PaymentsChart` | Graphique des paiements |
| `AcademicResultsChart` | Graphique des résultats académiques |

### Resources

**Aucune.** Ce panel est un panneau de visualisation et de configuration. Les données sont gérées via SchoolAdmin.

### Responsabilités

- Visualisation du tableau de bord de pilotage
- Génération de documents officiels (attestations, certificats, bulletins)
- Configuration de l'identité visuelle (logo, devise, couleurs)
- Assistant de configuration initiale de l'école

---

## 5. Panels Stubs (Teacher, Student, Parent, Secretary, Accountant)

Ces 5 panels sont des **coquilles vides** — ils disposent d'un dashboard mais aucune Resource ni Widget.

| Panel | Path | Dashboard | Tenancy |
|-------|------|-----------|---------|
| `teacher` | `/teacher` | `TeacherDashboard` | Tenant-scoped |
| `student` | `/student` | `StudentDashboard` | Tenant-scoped |
| `parent` | `/parent` | `ParentDashboard` | Tenant-scoped |
| `secretary` | `/secretary` | `SecretaryDashboard` | Tenant-scoken |
| `accountant` | `/accountant` | `AccountantDashboard` | Tenant-scoped |

> Ces panels seront développés lors de prochains sprints métier. Leur infrastructure est prête.

---

## 6. Resources Masquées (Backlog Technique)

Ces Resources existent dans le code mais ne sont enregistrées dans **aucun panel**. Elles sont absentes de la navigation et de l'ensemble des routes. Elles sont conservées pour un développement futur.

### Transport (8 Resources)

| Resource | Navigation Group | Modèle |
|----------|-----------------|--------|
| `VehicleResource` | Transport | Vehicle |
| `VehicleMaintenanceResource` | Transport | VehicleMaintenance |
| `TripResource` | Transport | Trip |
| `RouteResource` | Transport | Route |
| `RouteStopResource` | Transport | RouteStop |
| `DriverResource` | Transport | Driver |
| `FuelLogResource` | Transport | FuelLog |
| `StudentTransportAssignmentResource` | Transport | StudentTransportAssignment |

### Bibliothèque (8 Resources)

| Resource | Navigation Group | Modèle |
|----------|-----------------|--------|
| `BookResource` | Bibliotheque | Book |
| `BookCategoryResource` | Bibliotheque | BookCategory |
| `BookCopyResource` | Bibliotheque | BookCopy |
| `BookLoanResource` | Bibliotheque | BookLoan |
| `AuthorResource` | Bibliotheque | Author |
| `PublisherResource` | Bibliotheque | Publisher |
| `ReservationResource` | Bibliotheque | Reservation |
| `FineResource` | Bibliotheque | Fine |

### Intelligence Artificielle (6 Resources)

| Resource | Navigation Group | Modèle |
|----------|-----------------|--------|
| `AiConversationResource` | Plateforme IA | AiConversation |
| `AiMessageResource` | Plateforme IA | AiMessage |
| `AiProviderResource` | Plateforme IA | AiProviderModel |
| `AiUsageLogResource` | Plateforme IA | AiUsageLog |
| `AiPromptTemplateResource` | Plateforme IA | AiPromptTemplate |
| `AiKnowledgeSourceResource` | Plateforme IA | AiKnowledgeSource |

### Moteur de Workflow (3 Resources)

| Resource | Navigation Group | Modèle |
|----------|-----------------|--------|
| `WorkflowResource` | Moteur de workflow | Workflow |
| `WorkflowExecutionResource` | Moteur de workflow | WorkflowExecution |
| `AutomationRuleResource` | Moteur de workflow | AutomationRule |

### Communication Avancée (3 Resources)

| Resource | Navigation Group | Modèle |
|----------|-----------------|--------|
| `CommunicationChannelResource` | Communication | CommunicationChannel |
| `NotificationResource` | Communication | Notification |
| `NotificationTemplateResource` | Communication | NotificationTemplate |

### Analytiques (2 Resources)

| Resource | Navigation Group | Modèle |
|----------|-----------------|--------|
| `KpiResource` | Analytiques | Kpi |
| `DashboardResource` | Analytiques | Dashboard |

### Total masqué : 30 Resources

> **Stratégie** : Ces Resources ne sont supprimées d'aucun fichier. Elles restent dans `app/Filament/Resources/` et seront réactivées dans le panel approprié lors du développement de chaque module.

---

## 7. Stratégie de Séparation des Permissions

### Principe Fondamental

> **Une Resource = Un Panel. Aucun doublon. Les différences d'accès se gèrent par Policies, pas par copies.**

### Règles

1. **Chaque Resource est enregistrée dans un seul panel.** Jamais dans deux panels simultanément.
2. **Les droits d'accès au sein d'un panel** se gèrent via les **Filament Policies** et les **Permissions** (Laravel ACL).
3. **Les différences de visibilité** entre rôles au sein du même panel se gèrent par :
   - `canView()` / `canCreate()` / `canEdit()` / `canDelete()` sur les Resources
   - `canAccess()` sur les Pages
   - Navigation conditionnelle via `$navigationGroup` et `$navigationSort`
4. **Aucune Resource n'est copiée** entre panels. Si deux rôles ont besoin de la même donnée, la Resource est dans un seul panel et l'accès est contrôlé par des Policies.

### Matrice d'accès

| Resource | SuperAdmin | SchoolAdmin | Director | Teacher | Student | Parent | Secretary | Accountant |
|----------|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| SuperAdminSchoolResource | CRUD | — | — | — | — | — | — | — |
| PlanResource | CRUD | — | — | — | — | — | — | — |
| SubscriptionResource | CRUD | — | — | — | — | — | — | — |
| DomainResource | CRUD | — | — | — | — | — | — | — |
| CentralUserResource | CRUD | — | — | — | — | — | — | — |
| AuditLogResource | R | — | — | — | — | — | — | — |
| SystemSettingResource | CRUD | — | — | — | — | — | — | — |
| BackupResource | CRUD | — | — | — | — | — | — | — |
| SystemHealthResource | R | — | — | — | — | — | — | — |
| MaintenanceWindowResource | CRUD | — | — | — | — | — | — | — |
| NotificationChannelResource | CRUD | — | — | — | — | — | — | — |
| StudentResource | — | CRUD | — | R | R* | R* | R | — |
| GuardianResource | — | CRUD | — | R | — | R* | R | — |
| StudentEnrollmentResource | — | CRUD | — | R | — | — | R | — |
| AcademicYearResource | — | CRUD | — | R | — | — | R | — |
| AcademicTermResource | — | CRUD | — | R | — | — | R | — |
| AcademicClassResource | — | CRUD | — | R | — | — | R | — |
| ProgramResource | — | CRUD | — | R | — | — | R | — |
| GradeLevelResource | — | CRUD | — | R | — | — | R | — |
| SectionResource | — | CRUD | — | R | — | — | R | — |
| ClassroomResource | — | CRUD | — | R | — | — | R | — |
| SubjectResource | — | CRUD | — | R | — | — | R | — |
| SubjectCategoryResource | — | CRUD | — | — | — | — | R | — |
| SubjectCoefficientResource | — | CRUD | — | — | — | — | R | — |
| AssessmentResultResource | — | CRUD | — | CRUD | R* | R* | R | — |
| AttendanceResource | — | CRUD | — | CRUD | R* | R* | R | — |
| PaymentResource | — | CRUD | — | — | — | R* | R | CRUD |
| AnnouncementResource | — | CRUD | — | R | R | R | R | R |
| MessageResource | — | CRUD | — | R | R | R | R | R |
| IncidentResource | — | CRUD | — | R | R | R | R | — |
| ReportResource | — | CRUD | — | R | R | R | R | R |
| UserResource | — | CRUD | — | — | — | — | R | — |
| RoleResource | — | CRUD | — | — | — | — | — | — |
| PermissionResource | — | CRUD | — | — | — | — | — | — |
| DepartmentResource | — | CRUD | — | R | — | — | R | — |

**Légende** : CRUD = Create/Read/Update/Delete | R = Read only | R* = Read own data only | — = Pas d'accès

> **Note** : Les accès Teacher, Student, Parent, Secretary, Accountant sur les Resources SchoolAdmin seront implémentés via des **Policies** dédiées, pas via des copies de Resources. Ces panels stub restent vides en attendant le développement de ces accès.

---

## 8. Fichiers Modifiés

| Fichier | Action | Détail |
|---------|--------|--------|
| `app/Providers/Filament/AdminPanelProvider.php` | **SUPPRIMÉ** | Panel non retenu |
| `bootstrap/providers.php` | **MODIFIÉ** | Ligne AdminPanelProvider retirée |
| `app/Providers/Filament/SchoolAdminPanelProvider.php` | **RÉÉCRIT** | 24 Resources explicites, aucun discover |
| `app/Providers/Filament/DirectorPanelProvider.php` | **RÉÉCRIT** | 0 Resources, pages + widgets conservés |
| `app/Providers/Filament/SuperAdminPanelProvider.php` | **INCHANGÉ** | Déjà correct |
| 5 panels stubs | **INCHANGÉS** | Déjà corrects |
| `app/Filament/Resources/DashboardResource.php` | **MASQUÉ** | Non enregistré dans aucun panel |
| `app/Filament/Resources/AssessmentResource/` | **ORPHELIN** | Dossier vide sans fichier PHP |
| `app/Filament/Resources/TimetableEntryResource/` | **ORPHELIN** | Dossier vide sans fichier PHP |

---

## 9. Règles d'Archivecture

1. **Aucun panel ne doit utiliser `discoverResources()`, `discoverPages()` ou `discoverWidgets()`**. Toute inscription est explicite.
2. **Une Resource ne peut apparaître dans un seul panel**. En cas de besoin multi-panel, utiliser des Policies.
3. **Les modules hors MVP** restent dans le code source mais ne sont enregistrés dans aucun panel.
4. **Toute nouvelle Resource** doit être attribuée à un panel existant lors de sa création.
5. **Les panels stubs** (Teacher, Student, Parent, Secretary, Accountant) sont prêts à recevoir des Resources via des enregistrements explicites.
6. **Le panel Director** est un panneau de visualisation. Il ne gère pas de Resources CRUD.
7. **La navigation** est contrôlée par `$navigationGroup` et `$navigationSort` au sein de chaque panel.
8. **Les permissions** sont gérées par des Policies Laravel, jamais par des copies de Resources.
