# Architecture des Panels — NexusSchool

## Vue d'ensemble

NexusSchool utilise **8 panels Filament** pour séparer les responsabilités selon le rôle de chaque utilisateur.

## Panels

| Panel | Path | Rôle | Tenancy | Ressources |
|-------|------|------|---------|------------|
| **super-admin** | `/super-admin` | Administrateur SaaS | Non (central) | 11 |
| **school-admin** | `/school-admin` | Administrateur technique de l'école | Oui (tenant) | 16 + 6 masquées |
| **director** | `/director` | Directeur de l'école | Oui (tenant) | 0 (futur) |
| **teacher** | `/teacher` | Enseignant | Oui (tenant) | 0 (futur) |
| **student** | `/student` | Élève | Oui (tenant) | 0 (futur) |
| **secretary** | `/secretary` | Secrétaire | Oui (tenant) | 0 (futur) |
| **parent** | `/parent` | Parent / Tuteur | Oui (tenant) | 0 (futur) |
| **accountant** | `/accountant` | Comptable | Oui (tenant) | 0 (futur) |

## Isolation des données

### Schéma partagé + discriminateur `school_id`

- Les écoles (tenant) partagent le même schéma ; chaque table tenant porte une
  colonne `school_id` (voir `App\Traits\HasSchoolId` et `App\Traits\ScopedByTenant`)
- La base par tenant (`tenant{id}`) de stancl n'est pas utilisée : les
  bootstrappers de base de données sont désactivés (`config/tenancy.php`)
- Toutes les requêtes Eloquent des modèles tenant sont isolées via `ScopedByTenant`

### Résolution du tenant par le host (Phase 2)

Le host de la requête est **autoritaire** pour les panels école. Le middleware
`InitializeTenancyForPanel` résout l'école depuis la table `domains`
(`domain = host`), en excluant les écoles supprimées, puis n'initialise le
contexte tenant que si l'école est `Active`.

| Situation | Réponse |
|-----------|---------|
| Host dans `central_domains` | `404` |
| Host non enregistré | `404` |
| École supprimée (soft delete) | `404` |
| École non `Active` (pending/inactive/suspended) | `403` |
| Utilisateur authentifié d'une autre école | `403` |
| Invité sur un host école valide | page de connexion |

Il n'y a **aucun repli** sur l'école de l'utilisateur authentifié : le host décide.

### Défense en profondeur

Le trait `ScopedByTenant` ajoute un filtre `school_id` à chaque ressource du school-admin :

```php
use App\Traits\ScopedByTenant;

class StudentResource extends Resource
{
    use ScopedByTenant;
    // ...
}
```

### Sécurité SuperAdmin → SchoolAdmin

- Le panel `super-admin` n'utilise PAS le middleware `InitializeTenancyForPanel`
- `canAccessPanel()` dans le modèle `User` vérifie les rôles par panel
- Les rôles/permissions sont gérés centralement (`$connection = 'mysql'`)
- `RoleResource` et `PermissionResource` ne sont PAS accessibles au school-admin

## Navigation School Admin

```
🏠 Tableau de bord          (ConfigStatusWidget + ConfigAlertsWidget)
🏫 Établissement
   ├── Informations générales  (EstablishmentInfo)
   ├── Campus / Annexes        (CampusResource)
   └── Paramètres PDF          (PDFSettings)
📚 Académique
   ├── Années scolaires        (AcademicYearResource)
   ├── Périodes                (AcademicTermResource)
   ├── Programmes              (ProgramResource)
   ├── Niveaux                 (GradeLevelResource)
   ├── Sections                (SectionResource)
   ├── Classes                 (AcademicClassResource)
   ├── Salles                  (ClassroomResource)
   ├── Catégories de matières  (SubjectCategoryResource)
   ├── Matières                (SubjectResource)
   └── Coefficients            (SubjectCoefficientResource)
👥 Utilisateurs
   ├── Utilisateurs            (UserResource)
   └── Départements            (DepartmentResource)
🎓 Élèves
   ├── Élèves                  (StudentResource)
   ├── Parents / Tuteurs       (GuardianResource)
   └── Inscriptions            (StudentEnrollmentResource)
💰 Configuration financière
   └── Configuration des frais (FeeConfiguration)
⚙ Paramètres
   └── Paramètres PDF          (PDFSettings)
```

## Ressources déplacées (masquées du School Admin)

Ces ressources existent toujours dans le code mais ne sont pas visibles dans la navigation du school-admin. Elles seront exposées dans les futurs panels :

| Ressource | Panel futur | Description |
|-----------|-------------|-------------|
| AssessmentResultResource | Director | Résultats d'évaluation |
| IncidentResource | Director | Incidents de sécurité |
| ReportResource | Director | Rapports et statistiques |
| MessageResource | Secretary | Messages internes |
| AnnouncementResource | Secretary | Annonces |
| PaymentResource | Accountant | Paiements |
