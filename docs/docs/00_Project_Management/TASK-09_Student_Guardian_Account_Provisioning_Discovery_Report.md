# TASK-09 — Student & Guardian Account Provisioning Integration — Discovery Report

| Field | Value |
|---|---|
| Status | **DRAFT DISCOVERY — DOCUMENTATION ONLY — NO IMPLEMENTATION, NO GOVERNANCE APPROVAL** |
| Date | 2026-11-24 |
| Scope | Read-only discovery of enrollment, Student/Guardian/User relations, account provisioning, invitations, tenant isolation, and RolesRelationManager |
| Application code modified by this task | **None** |
| Migrations / tests modified by this task | **None** |
| Only file created by this task | `docs/docs/00_Project_Management/TASK-09_Student_Guardian_Account_Provisioning_Discovery_Report.md` |
| Related document | `docs/docs/00_Project_Management/Account_Provisioning_Foundation_Specification.md` (DRAFT, not approved) |

---

## 1. État actuel vérifié

- **Contrôle Git initial** : `git status --porcelain=v1 -uall` n’a retourné aucune ligne. L’arbre de travail était propre avant toute investigation.
- Aucun fichier applicatif, migration, test, configuration ou document existant n’a été modifié. Aucune opération Git de staging, commit, push, reset, checkout, restore, clean ou stash n’a été effectuée.
- Le socle d’invitation existe déjà : `user_invitations` stocke un ULID, `user_id`, `school_id`, un `purpose`, un `token_hash` SHA-256 unique, `expires_at`, `consumed_at`, `revoked_at` et `invited_by_user_id`. Le jeton en clair n’est pas persisté (`app/Models/UserInvitation.php:21-41`, `123-162`, `205-226` ; `database/migrations/2026_11_24_020539_create_user_invitations_table.php:12-28`).
- L’expiration est de **48 heures** (`app/Models/UserInvitation.php:26`, `137`). Une nouvelle invitation révoque les invitations actives précédentes pour le même triplet `user_id + school_id + purpose` (`app/Models/UserInvitation.php:117-159`).
- `AccountInvitationActivationService` valide le mot de passe, verrouille les lignes dans une transaction, vérifie l’école hôte, le but de l’invitation, l’état `Pending`, la correspondance utilisateur/école, puis écrit le mot de passe haché et consomme l’invitation (`app/Domains/User/Services/AccountInvitationActivationService.php:24-29`, `39-108`, `137-199`).
- `AccountProvisioningService` **crée** un nouvel utilisateur, lui attribue un rôle autorisé, génère un mot de passe aléatoire inconnu de l’utilisateur, puis émet une invitation. Il ne réutilise pas un compte existant (`app/Domains/User/Services/AccountProvisioningService.php:44-76`).
- Les rôles actuellement autorisés côté service sont :
  - School Admin : `teacher`, `secretary`, `accountant` (`AccountProvisioningService.php:29-33`, `192-213`) ;
  - plateforme : `school_admin` uniquement (`AccountProvisioningService.php:40-42`, `174-190`).
  - `student` et `parent` possèdent bien des purposes d’invitation, mais ne sont pas attribuables par ce service dans son état actuel (`AccountProvisioningService.php:216-223` ; `UserInvitation.php:21-24`).
- Aucune création ni aucun rattachement de compte utilisateur n’existe dans le parcours canonique d’inscription, ni dans les parcours de création de Student ou Guardian.
- La livraison d’invitation n’est pas implémentée comme e-mail ou SMS. Dans le parcours School Admin, le lien est affiché dans une notification flash Filament à transmettre manuellement de manière sécurisée ; le texte indique explicitement qu’aucun e-mail ni SMS n’a été envoyé (`app/Filament/Resources/UserResource/Pages/CreateUser.php:240-258`). Aucun fournisseur e-mail/SMS n’est donc présumé configuré.
- Deux parcours d’inscription sont enregistrés dans le même panneau : le parcours legacy `StudentEnrollmentResource` et le parcours canonique `EnrollmentResource` (`app/Providers/Filament/SchoolAdminPanelProvider.php:69-93`).
- Deux piles Eloquent coexistent sur les mêmes tables physiques `students`, `guardians` et `student_guardians` : les modèles `App\Models\*` et les modèles DDD `App\Domains\{Student,Guardian}\Infrastructure\Models\*`.
- Les événements canoniques d’inscription, de création d’élève et de création de tuteur ont des loggers d’audit qui écrivent notamment `audit_logs` et `enrollment_history`. Aucune notification métier n’est émise par ces parcours. Les listeners legacy mappés dans `app/Providers/EventServiceProvider.php` apparaissent inertes car ce provider n’est pas enregistré dans `bootstrap/providers.php:8-39`.
- Le document `Account_Provisioning_Foundation_Specification.md` propose une architecture V1 et des règles, mais son statut est **DRAFT — NOT APPROVED FOR IMPLEMENTATION**. Il ne constitue donc pas une décision métier validée.

## 2. Parcours d’inscription existant

### 2.1 Point d’entrée canonique

Le point d’entrée métier canonique est :

`CreateEnrollment` (Filament) → `EnrollmentService::enroll()` → `CreateEnrollmentAction` → règles métier → création de `Enrollment` → `EnrollmentCreated` → audit/historique.

Preuves principales :

- `app/Domains/Enrollment/Presentation/Filament/Resources/EnrollmentResource/Pages/CreateEnrollment.php:19-28` dérive `school_id` du contexte locataire/utilisateur, construit `CreateEnrollmentData` et appelle `EnrollmentService::enroll()`.
- `app/Domains/Enrollment/Application/Services/EnrollmentService.php:25-47` délègue la création à `CreateEnrollmentAction`.
- `app/Domains/Enrollment/Application/DTOs/CreateEnrollmentData.php:12-67` contient les données scolaires : élève, type, année académique, programme, niveau, classe, section, numéro, date, notes et statut. Il ne contient aucun champ de compte utilisateur, mot de passe, invitation ou responsable légal.
- `app/Domains/Enrollment/Application/Actions/CreateEnrollmentAction.php:30-47`, `83-102` valide les références et l’appartenance au tenant, crée uniquement l’inscription et dispatche `EnrollmentCreated`.
- `app/Domains/Enrollment/EnrollmentServiceProvider.php:66-76` enregistre les listeners canoniques ; `app/Domains/Enrollment/Infrastructure/Listeners/EnrollmentAuditLogger.php:27-31`, `98-132` écrit `AuditLog` et `EnrollmentHistory`.

### 2.2 Parcours legacy parallèle

`app/Filament/Resources/StudentEnrollmentResource.php` et ses pages `List/Create/EditStudentEnrollments` utilisent une ressource Filament standard : la page `CreateStudentEnrollment` n’appelle aucun service métier et crée directement un `StudentEnrollment` via `CreateRecord` (`app/Filament/Resources/StudentEnrollmentResource/Pages/CreateStudentEnrollment.php:10-13`).

Ce parcours :

- écrit dans `student_enrollments`, et non dans la table canonique `enrollments` ;
- ne expose pas `school_id` dans son formulaire (`app/Filament/Resources/StudentEnrollmentResource.php:43-143`) ;
- possède un `StudentEnrollmentService` distinct qui ne semble pas être le point d’entrée runtime ; ses références sont principalement ses tests et sa déclaration de classe ;
- est toujours enregistré dans le panneau School Admin au même titre que la ressource canonique.

### 2.3 Où introduire une orchestration future

Sans implémenter quoi que ce soit, le point d’intégration le plus cohérent serait la couche Application, autour de l’orchestration de `EnrollmentService::enroll()`, et non :

- dans la page Filament, qui doit rester une surface de saisie et d’affichage ;
- dans un observer ou un listener global, qui rendrait l’ordre et la transaction implicites ;
- dans `CreateEnrollmentAction`, dont la responsabilité actuelle est la création de l’agrégat `Enrollment` ;
- dans la ressource legacy, qui devrait d’abord être alignée, retirée ou redirigée vers le service canonique.

Une orchestration dédiée pourrait coordonner explicitement : création/acquisition du dossier élève, création de l’inscription, recherche/réutilisation du Guardian, création optionnelle du compte élève, création ou réutilisation du compte parent, liaison `student_guardians`, invitation et audit. Chaque responsabilité resterait dans son service existant.

## 3. Relations entre Student, Guardian et User

### 3.1 Utilisateur

`App\Models\User` contient `school_id`, `first_name`, `last_name`, `email`, `phone`, `avatar`, `password` haché, `status`, `last_login_at`, `uuid` et soft deletes (`app/Models/User.php:34-67`). Les statuts sont `Active`, `Inactive`, `Pending` et `Suspended`. L’e-mail est unique globalement, y compris pour les comptes supprimés selon le comportement vérifié dans `AccountProvisioningService` (`AccountProvisioningService.php:137-154`).

`User` possède des relations `guardian()` et `student()` de type `HasOne` (`app/Models/User.php:97-105`), ainsi que les rôles Spatie via `HasRoles`. L’accès aux panneaux est centralisé dans `PanelAccessRegistry` : utilisateur actif, rôle mappé et établissement actif sont requis ; les rôles `student` et `parent` sont mappés respectivement aux panneaux `student` et `parent` (`app/Services/PanelAccessRegistry.php:63-105`).

### 3.2 Élève et tuteur

Les tables `students` et `guardians` possèdent un `school_id` et un `user_id` nullable. L’identité Guardian est donc liée à un établissement dans la V1 : numéro, téléphone et e-mail sont uniques par établissement lorsqu’ils sont présents (`database/migrations/2026_07_15_000015_create_guardians_table.php:51-58`).

Les identifiants disponibles pour une recherche parent sont :

- identifiant interne Guardian ;
- `user_id` lorsque le tuteur possède déjà un compte ;
- e-mail ;
- téléphone ;
- nom et prénom.

Aucune logique de correspondance floue, de vérification de téléphone, de désambiguïsation ou de fusion n’a été trouvée. Les contrôles existants sont des comparaisons exactes de chaînes et des contrôles d’unicité.

### 3.3 Table `student_guardians`

La table n’est pas un simple pivot technique :

- `guardian_id` est nullable ;
- des champs intégrés `name`, `phone`, `email` et `relationship` permettent de représenter un tuteur sans fiche Guardian ;
- des indicateurs métier existent : primary, emergency contact, authorized pickup, financial responsible, notification recipient, medical contact, plages de récupération et historique de relation ;
- unicité sur `(school_id, guardian_id, student_id, deleted_at)` (`database/migrations/2026_08_01_000013_create_student_guardians_table.php:12-33` ; `database/migrations/2026_08_03_000002_upgrade_student_guardians_table_for_relationships.php:17-41`) ;
- une table d’historique dédiée existe (`database/migrations/2026_08_03_000003_create_student_guardian_relationship_history_table.php`).

Deux modèles différents exploitent la même table :

- `StudentGuardian` pour les lignes intégrées issues de `GuardianReference`, avec `guardian_id = NULL` ;
- `StudentGuardianRelationship` pour les relations de premier niveau avec `student()` et `guardian()`.

`GuardianReference` contient des informations de contact intégrées mais aucun identifiant de Guardian ni de User (`app/Domains/Student/Domain/ValueObjects/GuardianReference.php:14-31`, `79-105`). Les lignes intégrées sont donc invisibles pour les relations Guardian-domain basées sur `guardian_id`. `CreateStudentAction::syncGuardians()` et `UpdateStudentAction::replaceGuardians()` écrivent ces lignes intégrées ; `UpdateStudentAction` les remplace même lorsque `replaceGuardians = true` (`app/Domains/Student/Application/Actions/UpdateStudentAction.php:98-128` ; `app/Domains/Student/Application/DTOs/UpdateStudentData.php:90-119`).

### 3.4 Conséquence pour le provisionnement

Le schéma permet techniquement :

- un élève sans compte ;
- un tuteur sans compte ;
- un tuteur avec compte lié à plusieurs élèves via plusieurs lignes `student_guardians` ;
- des responsables intégrés sans identité Guardian durable.

Mais le parcours actuel d’inscription ne crée ni ne relie aucun compte. Pour réutiliser un compte parent de façon fiable, il faut s’appuyer sur `guardians.user_id` et/ou une relation `StudentGuardianRelationship` avec `guardian_id`, et non sur les seules lignes `GuardianReference` intégrées.

## 4. Scénarios de réutilisation des comptes parents

### 4.1 Comportement actuel confirmé

- `GuardianService::create()` crée uniquement un Guardian et dispatche `GuardianCreated` (`app/Domains/Guardian/Services/GuardianService.php:26-48`).
- `CreateGuardianAction` effectue une détection de doublon exacte et par établissement pour numéro, téléphone et e-mail (`app/Domains/Guardian/Application/Actions/CreateGuardianAction.php:80-122`).
- La vérification d’un `user_id` existant dans `CreateGuardianAction` est globale, sans filtre `forSchool()`, contrairement aux autres contrôles liés au Guardian.
- `GuardianService::create()` ignore le paramètre `$schoolId` qui lui est transmis (`app/Domains/Guardian/Application/Services/GuardianService.php:37-42`), tandis que `App\Domains\Guardian\DTOs\GuardianData::fromArray()` lit `school_id` alors que le formulaire Filament ne le fournit pas (`app/Domains/Guardian/Presentation/Filament/Resources/GuardianResource/Pages/CreateGuardian.php:16-21`).
- `LinkGuardianAction` résout le Guardian dans l’établissement courant, empêche le doublon de la paire et copie les informations de contact dans la relation (`app/Domains/Guardian/Application/Actions/LinkGuardianAction.php:22-58`, `70-123`).
- Aucune recherche de compte parent n’existe dans `CreateEnrollmentData`, `CreateEnrollmentAction` ou `EnrollmentService`.

### 4.2 Cas de réutilisation à étudier

| Situation | État vérifié | Traitement architecturally sûr à étudier |
|---|---|---|
| Guardian existant dans le même établissement, déjà lié à un User `parent` actif | Modèle possible ; aucun usage automatique dans l’inscription | Réutiliser le compte et la fiche Guardian, rattacher l’élève, auditer, sans renvoyer de mot de passe |
| Guardian existant dans le même établissement, sans compte | Cas possible car `guardians.user_id` est nullable | Décision métier requise avant création d’un compte ou simple rattachement de la fiche |
| User existant avec rôle `parent` dans le même établissement, sans Guardian lié | Possible ; aucune règle de correspondance fiable trouvée | Exiger corroboration ou validation humaine ; ne pas fusionner implicitement |
| Correspondance par e-mail exact dans le même établissement | Contrôle d’unicité Guardian disponible | Peut être un signal fort, mais la politique exacte reste à valider |
| Correspondance par téléphone seul | Téléphone non vérifié ; normalisation limitée à un trim dans les value objects observés | Ne jamais associer automatiquement ; signaler pour vérification humaine |
| E-mail appartenant à un User d’un autre établissement | `users.email` est globalement unique | Bloquer la création et traiter comme conflit inter-établissements ; aucune identité globale n’est à introduire en V1 |
| Correspondance ambiguë | Aucune file de revue ni UI de désambiguïsation trouvée | Arrêter la liaison automatique et créer un élément de revue administrative |

### 4.3 Plusieurs enfants pour un même parent

Le schéma `student_guardians` autorise plusieurs lignes pour un même `(school_id, guardian_id)`. Un compte parent peut donc être relié à plusieurs élèves du même établissement sans créer un second compte, à condition que :

- la fiche Guardian soit la même dans le tenant courant ;
- le même `user_id` soit réutilisé ;
- les indicateurs de relation propres à chaque enfant soient préservés ;
- l’unicité de la paire Guardian-élève soit respectée.

Les lignes intégrées via `GuardianReference` ne permettent pas d’atteindre cette garantie, car elles n’identifient ni le Guardian ni le User.

## 5. Scénarios de création des comptes élèves

### 5.1 Séparation confirmée

La création du dossier scolaire, la création du compte utilisateur et l’activation sont déjà conceptuellement séparées dans les modèles et dans la spécification :

- `students.user_id` est nullable ;
- `CreateEnrollmentData` ne contient aucun champ de compte ;
- `StudentService` crée uniquement le Student et dispatche `StudentCreated` ;
- `CreateStudentAction` gère les références guardian intégrées, pas des comptes ;
- `UserInvitation::PURPOSE_STUDENT_INITIAL` existe et est accepté par le service d’activation ;
- `AccountProvisioningService` n’autorise toutefois pas encore le rôle `student`.

### 5.2 Scénarios à couvrir

1. **Inscription sans compte élève** : comportement actuellement possible et seul comportement réellement implémenté. Le dossier élève et l’inscription existent sans `users` ni `students.user_id`.
2. **Création de compte élève demandée explicitement** : nécessite une option dans la commande d’orchestration, une règle d’éligibilité, un rôle `student`, la création du User, la liaison de `students.user_id` et une invitation `student_initial`.
3. **Compte élève déjà présent** : ne pas recréer de User ; vérifier l’appartenance au même établissement et la compatibilité du rôle avant liaison.
4. **Invitation étudiante** : utiliser le mécanisme existant, sans mot de passe transmis en clair. Le canal de remise du lien et l’éventuel accompagnement par un parent ou un administrateur ne sont pas encore décidés.
5. **Minors et coordonnées** : la spécification recommande une activation encadrée si l’élève n’a pas d’e-mail propre, mais cette recommandation n’est pas une décision métier approuvée.

Aucun scénario ne doit rendre la création du dossier scolaire dépendante de la réussite de la création d’un compte ou de la livraison d’une invitation.

## 6. Risques de doublons et d’association incorrecte

### 6.1 Risques confirmés

| Risque | Preuve / état |
|---|---|
| Deux parcours d’inscription non consolidés | Les deux ressources sont enregistrées dans `SchoolAdminPanelProvider.php:69-93` ; une orchestration future pourrait n’être appliquée qu’à l’un des deux |
| Aucune création/réutilisation de compte dans l’inscription | `CreateEnrollmentData.php:12-67` et `CreateEnrollmentAction.php:30-47` ne contiennent aucun champ ni appel lié à User |
| Doubles piles Eloquent sur les mêmes tables | Les modèles `App\Models\*` et DDD écrivent les mêmes tables avec des enums et relations différents |
| Lignes `student_guardians` sans identifiant durable | `guardian_id` nullable et `GuardianReference` sans ID (`GuardianReference.php:14-31`) |
| Écrasement possible des références guardian intégrées | `UpdateStudentAction::replaceGuardians()` remplace les lignes quand `replaceGuardians = true` |
| Détection de doublon strictement exacte | `CreateGuardianAction.php:80-122` ; aucune normalisation robuste ni correspondance floue trouvée |
| Aucune gestion d’ambiguïté | Aucune file de revue, UI de désambiguïsation ou règle de conflit fonctionnelle trouvée |
| Risque d’association sur téléphone non vérifié | Le téléphone est un identifiant disponible, mais aucun mécanisme de vérification n’existe |
| E-mail globalement unique | `AccountProvisioningService.php:137-154` bloque tout nouvel utilisateur avec un e-mail existant, même supprimé ; les cas inter-établissements sont des conflits, pas des fusions |
| Incohérence de scoping Guardian | `GuardianService::create()` ignore `$schoolId` ; `CreateGuardianAction` vérifie `user_id` globalement |
| Divergence d’allowlist de rôles | Le service interdit `student`/`parent` à la création, alors que `RolesRelationManager` les propose à l’association |
| Lien d’invitation exposé dans l’interface admin | `CreateUser.php:240-258` affiche le jeton en clair dans une notification Filament ; aucun e-mail/SMS n’est envoyé automatiquement |
| Purposes student/parent non encore provisionnés | Les constantes existent, mais `AccountProvisioningService` ne les autorise pas dans ses allowlists |
| Listeners legacy probablement inertes | `EventServiceProvider` n’est pas enregistré dans `bootstrap/providers.php` |

### 6.2 Risques hypothétiques

- Un `role_id` arbitraire pourrait être attaché via `RolesRelationManager` si la validation Filament du Select ne contraint pas côté serveur la valeur envoyée. Le callback d’association ne réapplique pas l’allowlist ; ce scénario nécessite une vérification runtime dédiée.
- Une correspondance parent could être incorrecte si plusieurs fiches du même établissement partagent des informations de contact partiellement identiques et qu’une règle future privilégie un seul champ.
- Une race condition pourrait créer deux User ou deux Guardian concurrents si les contrôles restent uniquement applicatifs sans contrainte d’unicité adaptée.
- Une modification de rôle, statut ou établissement pourrait laisser des sessions, tokens ou caches Spatie obsolètes ; aucun mécanisme de révocation complet n’a été vérifié dans ce périmètre.
- Le jeton affiché dans l’interface pourrait être copié, journalisé ou conservé dans l’historique navigateur si les procédures administratives ne sont pas strictement définies.
- L’introduction future d’une identité parent globale contournerait l’isolation V1 ; ce rapport ne propose pas un tel mécanisme.

## 7. Analyse de l’isolation tenant

### 7.1 Contrôles existants

- `User`, `Student`, `Guardian`, `Enrollment` et `UserInvitation` portent un `school_id`.
- `UserInvitation::issue()` dérive `school_id` de l’utilisateur et exige une valeur non vide (`app/Models/UserInvitation.php:123-162`).
- `AccountInvitationActivationService` vérifie l’école résolue par hôte, l’école de l’invitation et l’école de l’utilisateur dans la même transaction que la consommation (`AccountInvitationActivationService.php:53-105`, `172-199`).
- Le contrôleur d’activation est public mais lié à l’hôte courant ; il refuse une invitation d’une autre école et n’authentifie pas l’utilisateur (`app/Domains/User/Presentation/Http/AccountInvitationActivationController.php:15-79`, `routes/tenant.php:60-72`).
- `CreateUser.php:55-74`, `189-229` exige un School Admin actif dont l’établissement correspond au tenant courant, puis construit la commande de provisioning à partir du tenant, pas d’un `school_id` libre.
- `AccountProvisioningService::resolveAndValidateSchool()` refuse tout `school_id` vide, toute école inexistante/inactive et tout School Admin agissant hors de son établissement (`AccountProvisioningService.php:89-135`).
- `CreateEnrollmentAction.php:83-102` valide les références scolaires par rapport au `schoolId` du DTO.
- `ScopedByTenant.php:11-25` filtre par `school_id` et renvoie zéro ligne si aucun tenant n’est initialisé.
- `UserResource::getEloquentQuery()` applique un filtre `school_id` dans le panneau School Admin et un `whereRaw('1 = 0')` en l’absence de tenant (`app/Filament/Resources/UserResource.php:236-252`).
- `PanelAccessRegistry` exige un utilisateur actif et une école active pour tous les panneaux non super-admin (`app/Services/PanelAccessRegistry.php:78-105`).

### 7.2 Lacunes à traiter

- `HasSchoolId` assigne automatiquement `school_id` seulement à la création et ne fournit pas de global scope Eloquent (`app/Traits/HasSchoolId.php:12-24`). Une requête oubliant `forSchool()` peut donc traverser les tenants.
- La ressource legacy `StudentEnrollmentResource` ne porte pas `school_id` dans son formulaire alors que `StudentEnrollmentRules` l’exige.
- `GuardianService::create()` ignore le `schoolId` reçu et `CreateGuardianAction` vérifie `user_id` sans `forSchool()`.
- Les deux piles de modèles rendent l’isolation dépendante du modèle utilisé par chaque action.
- `users.email` étant globalement unique, la réutilisation inter-établissements est un conflit et non une correspondance autorisée.
- Aucune liaison inter-établissements n’est à introduire : la V1 doit conserver une identité parent liée à un seul établissement.

Toute recherche, création, réutilisation ou liaison de parent/élève devra donc être explicitement filtrée par le tenant courant et refuser les identifiants provenant d’un autre `school_id`.

## 8. Audit de RolesRelationManager

Fichier audité : `app/Filament/Resources/UserResource/RelationManagers/RolesRelationManager.php`.

### 8.1 Rôles attribuables dans le panneau School Admin

La liste locale `$schoolAdminAllowedRoles` contient :

- `school_admin`
- `director`
- `teacher`
- `student`
- `parent`
- `accountant`
- `secretary`

`super_admin` n’y figure pas (`RolesRelationManager.php:24-32`).

Actions présentes :

- `CreateAction` est masquée dans le panneau School Admin (`RolesRelationManager.php:83-90`) ;
- `AssociateAction` reste visible et ses options sont filtrées par la liste ci-dessus (`RolesRelationManager.php:91-100`) ;
- `DissociateAction` et `DissociateBulkAction` sont visibles sans condition propre au rôle (`RolesRelationManager.php:106-113`) ;
- `canView(Model $record)` retourne toujours `true` (`RolesRelationManager.php:39-42`).

Le test `tests/Feature/Filament/SchoolAdminStaffProvisioningTest.php:501-534` confirme que la gestion des rôles reste exposée sur la page d’édition et que `school_admin` fait partie des options visibles.

### 8.2 Vérifications côté serveur

- Le formulaire et le Select d’association filtrent les rôles par la liste locale pour le panneau School Admin.
- Aucun `authorize()`, `can()`, `hasPermissionTo()` ou appel à `AccountProvisioningService` n’existe dans le relation manager.
- Le callback `associateRelatedRecordUsing()` reçoit `role_id`, exécute `Role::findOrFail($data['role_id'])` puis `attach()` sans revérifier l’allowlist ni l’autorisation (`RolesRelationManager.php:101-104`).
- Aucun événement d’audit ni journal spécifique n’est émis par ce relation manager.
- La ressource `UserResource` est enregistrée dans le panneau School Admin (`SchoolAdminPanelProvider.php:66`) et son query est filtrée par tenant (`UserResource.php:236-252`). La politique `App\Policies\UserPolicy` autorise la mise à jour d’un utilisateur par un School Admin du même établissement (`app/Policies/UserPolicy.php:38-49`), mais cela ne constitue pas une vérification fine du rôle attaché.

### 8.3 Risques confirmés

- **Divergence avec Task 08** : la création de compte interdit `director`, `school_admin`, `student` et `parent` (`SchoolAdminStaffProvisioningTest.php:309-342`), alors que le relation manager permet de les associer à un utilisateur existant.
- **Contournement indirect confirmé au niveau des règles** : une action de création refusée par le service peut être obtenue par association sur un compte existant.
- **`canView()` toujours vrai** : aucune autorisation explicite n’est portée par le relation manager lui-même.
- **Allowlist non réappliquée côté callback** : le filtre d’options n’est pas une garantie serveur dans le code d’association.
- **Cohérence insuffisante avec les responsabilités School Admin** : la spécification provisioning indique qu’un School Admin ne doit pas créer `school_admin`, ni attribuer des rôles non approuvés ; le relation manager ne suit pas cette matrice.
- **Aucune auditabilité dédiée** : ni attribution ni retrait de rôle n’est journalisé par ce composant.

### 8.4 Risques hypothétiques

- Si Filament ne valide pas strictement l’option du Select côté serveur, un `role_id` non autorisé pourrait être attaché. Le code applicatif ne l’empêcherait pas ; ce point nécessite un test de sécurité runtime avant toute conclusion d’exploitabilité.
- Si le contexte d’un enregistrement parent n’est pas correctement restreint, un rôle pourrait être attaché à un utilisateur d’un autre tenant. Le query de la ressource réduit ce risque, mais le relation manager n’ajoute pas de garde-fou propre.
- L’attribution de `school_admin` ou `director` pourrait élever les privilèges d’un utilisateur et, en l’absence de révocation de sessions/cache, prolonger des accès déjà ouverts.
- Le retrait massif de rôles pourrait verrouiller accidentellement un utilisateur si aucune règle de dernier administrateur n’existe ; aucune telle règle n’a été trouvée dans ce périmètre.

Aucune règle n’a été modifiée pendant cette revue.

## 9. Décisions métier encore nécessaires

Les dix points suivants nécessitent une validation métier/gouvernance explicite. Le code ou la spécification non approuvée ne permet pas d’y répondre de manière autoritative.

1. **Le compte élève doit-il être facultatif ou activé par défaut ?**  
   La spécification recommande une activation facultative et désactivée par défaut, mais son statut est DRAFT et aucun paramètre exécutable n’a été trouvé.

2. **Dans quels cas un parent doit-il recevoir une invitation ?**  
   Le mécanisme d’invitation existe, mais aucun déclencheur parent n’est implémenté dans l’inscription.

3. **Comment traiter un parent existant sans compte utilisateur ?**  
   `guardians.user_id` est nullable et aucune règle approuvée ne décide entre rattachement simple, création de compte et revue humaine.

4. **Comment traiter un parent existant ayant déjà un compte actif ?**  
   Le modèle permet le lien, mais aucune règle d’inscription ne vérifie encore la compatibilité d’identité, de rôle et de tenant.

5. **Comment traiter un compte parent Pending avec une invitation encore valide ?**  
   Le statut `Pending` et la table d’invitation existent, mais la sémantique de réutilisation, relance ou rotation n’est pas validée.

6. **Comment traiter un compte parent dont l’invitation a expiré ?**  
   L’expiration à 48 heures est technique ; la politique de réémission et de notification reste à décider.

7. **Comment gérer plusieurs responsables légaux pour un même élève ?**  
   Le schéma permet plusieurs relations, mais les règles de primary, emergency, pickup, financement et notification doivent être confirmées pour le provisionnement.

8. **Comment gérer un parent ayant plusieurs enfants dans le même établissement ?**  
   Le schéma le permet via `student_guardians`, mais la règle de réutilisation d’un seul compte et les cas de conflit restent à valider.

9. **Quel processus appliquer lorsqu’une correspondance parent est ambiguë ?**  
   Aucune file de revue, UI ou rôle responsable de la résolution n’existe actuellement.

10. **Quelles autorisations doivent être accordées au School Admin pour gérer les comptes et les rôles ?**  
    La création est limitée par Task 08, mais `RolesRelationManager` expose une liste plus large ; la matrice définitive doit être arbitrée.

## 10. Architecture proposée

### 10.1 Principes directeurs

- Conserver DDD/Clean Architecture : Presentation → Application → Domain → Infrastructure.
- Garder `EnrollmentService` comme point d’entrée canonique de l’inscription scolaire.
- Ne pas déplacer la logique métier dans les pages Filament ni dans des observers implicites.
- Ne pas dupliquer la création d’utilisateur, l’émission d’invitation ou la détection de doublon.
- Conserver l’identité parent liée à l’établissement en V1.
- Rendre toute donnée de tenant dérivée du contexte serveur, jamais d’un champ libre du formulaire.
- Distinguer strictement dossier scolaire, compte utilisateur et activation.

### 10.2 Couches et responsabilités

**Presentation**

- La page d’inscription collecte uniquement les choix métier explicites : création optionnelle d’un compte élève, informations parent, canal préférentiel si disponible.
- Elle transmet une commande à l’Application et affiche soit le résultat, soit les conflits nécessitant une revue.
- Aucun role ID, school ID de confiance, mot de passe ou jeton n’est accepté depuis cette couche.

**Application**

- Un orchestrateur d’inscription coordonne les services existants :
  - `EnrollmentService::enroll()` pour l’inscription canonique ;
  - services Student/Guardian pour les dossiers et relations ;
  - `AccountProvisioningService` pour la création de User et l’invitation ;
  - une règle de correspondance Guardian centralisée pour la recherche et la réutilisation.
- L’orchestrateur ne réimplémente ni la création d’utilisateur ni la validation de rôle.
- Chaque étape reçoit un `schoolId` issu du tenant courant.

**Domain**

- Règles typées : `StudentAccountEligibilityRule`, `GuardianMatchPolicy`, `ParentReusePolicy`, `TenantOwnershipRule`.
- Exceptions typées : `AmbiguousGuardianMatchException`, `CrossTenantAccessException`, `DuplicateAccountException`, `InvalidRoleException`, `InvitationUnavailableException`, `DeliveryChannelUnavailableException`.
- Les purposes existants `student_initial` et `guardian_initial` sont conservés et étendus seulement par décision explicite.

**Infrastructure**

- Dépôts avec filtres obligatoires `forSchool($schoolId)`.
- Abstraction de notification pour remettre le lien d’activation, sans supposer e-mail ou SMS configuré.
- Écriture des audits et événements via l’infrastructure existante, sans journaliser le jeton en clair.

### 10.3 DTO proposés

- **Commande d’orchestration** : tenant courant, données d’inscription, données élève, candidats parent, indicateur de création de compte élève, indicateur de création/réutilisation de compte parent, clé d’idempotence et canal souhaité.
- **Résultat** : inscription créée, élève créé/réutilisé, liens Guardian créés, comptes créés/réutilisés, invitations émises, conflits et éléments de revue.
- **Résultat de correspondance parent** : statut `exact`, `possible`, `ambiguous`, `conflict`, `none`, avec les identifiants et champs ayant motivé la décision, mais sans données secrets.

### 10.4 Transactions et atomicité

- Création du dossier élève, inscription, liens `student_guardians`, compte utilisateur et invitation doivent être dans une même transaction applicative lorsque l’option de provisionnement est activée.
- La remise externe du lien reste hors de la transaction ; son statut est enregistré séparément afin qu’un échec de livraison ne fasse pas disparaître l’inscription ou le compte.
- Une invitation échouée, expirée ou non livrée ne doit pas être confondue avec une absence de dossier scolaire.
- Les vérifications de correspondance devraient être accompagnées de contraintes d’unicité adaptées pour éviter les courses concurrentes.

### 10.5 Réutilisation sans duplication

- Une seule règle de correspondance parent doit servir l’inscription, la gestion Guardian et toute future action administrative.
- Une seule méthode de création de compte doit rester autorisée : `AccountProvisioningService` ou un service central qui l’appelle.
- Les lignes `GuardianReference` intégrées ne doivent pas être utilisées comme base d’un compte parent ; elles restent des snapshots de contact.
- La pile modèle canonique doit être choisie explicitement pour les nouveaux flux afin d’éviter les écritures divergentes.
- La ressource legacy doit être alignée, retirée ou redirigée avant de brancher une orchestration, sinon le provisionnement restera contournable.

### 10.6 Invitations et droits

- Réutiliser le jeton haché, l’expiration 48 h, la révocation de l’invitation précédente et la consommation unique existants.
- Distinguer les purposes `staff_initial`, `school_admin_initial`, `student_initial` et `guardian_initial`.
- Ne jamais stocker ni journaliser le mot de passe ou le jeton en clair.
- L’activation reste liée à l’hôte/tenant courant.
- `PanelAccessRegistry` reste l’autorité pour l’accès aux panneaux `student` et `parent`.

### 10.7 Événements et audit

Émettre des événements d’application distincts pour : inscription créée, compte élève provisionné, correspondance parent acceptée, correspondance ambiguë signalée, compte parent réutilisé, invitation émise, invitation livrée/échouée et compte activé. Chaque audit doit enregistrer acteur, tenant, cible, décision et raison, sans jeton ni mot de passe.

## 11. Découpage recommandé des prochains sprints

Les sprints ci-dessous sont une proposition de planification, pas une autorisation d’implémentation.

### Sprint 0 — Arbitrage métier et consolidated baseline

- Valider les dix questions de la section 9.
- Choisir la pile modèle canonique Student/Guardian et décider du sort de la ressource legacy.
- Confirmer la matrice des rôles School Admin et la politique de compte élève.
- Aucune modification de code tant que ces décisions ne sont pas prises.

### Sprint 1 — Socle de correspondance et d’isolation

- Centraliser les requêtes Guardian/User avec `forSchool()` obligatoire.
- Formaliser les règles exact / possible / ambiguous / conflict.
- Ajouter une file ou un mécanisme de revue humaine pour les correspondances ambiguës.
- Couvrir les cas téléphone seul, e-mail exact, identifiant interne, conflit inter-établissement et plusieurs enfants.

### Sprint 2 — Extension contrôlée du provisionnement

- Étendre le service central pour les scénarios `student` et `parent` après validation de la matrice.
- Introduire une abstraction de notification d’invitation, sans fournisseur imposé.
- Définir la rotation, la réémission et le suivi de livraison des invitations.
- Ajouter les exceptions typées et les événements d’audit correspondants.

### Sprint 3 — Intégration au parcours d’inscription canonique

- Brancher l’orchestration sur le parcours `EnrollmentResource` uniquement.
- Gérer inscription sans compte, compte élève optionnel, parent existant, nouveau parent et plusieurs enfants.
- Garantir l’atomicité transactionnelle et l’absence de mot de passe en clair.
- Ne toucher à la ressource legacy que selon la décision du Sprint 0.

### Sprint 4 — Durcissement RolesRelationManager

- Alignement total de l’allowlist avec Task 08 et la matrice validée.
- Vérification serveur dans le callback d’association, pas uniquement dans les options du formulaire.
- Contrôle explicite du tenant et de l’utilisateur cible.
- Audit des attributions et retraits, plus tests de tentative d’injection de rôle.

### Sprint 5 — Validation, régression et préparation au cutover

- Tests de bout en bout pour les scénarios A à D.
- Tests de non-régression sur les inscriptions existantes sans compte.
- Tests multi-tenant incluant tentative de réutilisation depuis un autre établissement.
- Tests de non-exposition du jeton et de non-livraison implicite par e-mail/SMS.
- Décision finale de retrait ou d’alignement de la voie legacy avant toute production.

## 12. Liste exacte des fichiers consultés et du rapport créé

### 12.1 Fichiers consultés

- `app/Domains/AcademicCommunication/Domain/Contracts/GuardianContract.php`
- `app/Domains/AcademicCommunication/Domain/References/GuardianReference.php`
- `app/Domains/AcademicCommunication/Domain/References/StudentReference.php`
- `app/Domains/AcademicCommunication/Infrastructure/Contracts/GuardianAdapter.php`
- `app/Domains/AcademicCommunication/Infrastructure/Contracts/StudentIdentityAdapter.php`
- `app/Domains/Enrollment/Application/Actions/CreateEnrollmentAction.php`
- `app/Domains/Enrollment/Application/DTOs/CreateEnrollmentData.php`
- `app/Domains/Enrollment/Application/Services/EnrollmentAcademicResolver.php`
- `app/Domains/Enrollment/Application/Services/EnrollmentNumberGenerator.php`
- `app/Domains/Enrollment/Application/Services/EnrollmentService.php`
- `app/Domains/Enrollment/Domain/Events/EnrollmentCreated.php`
- `app/Domains/Enrollment/Domain/Events/EnrollmentRestored.php`
- `app/Domains/Enrollment/Domain/Events/EnrollmentSuspended.php`
- `app/Domains/Enrollment/Domain/Events/EnrollmentUpdated.php`
- `app/Domains/Enrollment/Domain/Events/StudentGraduated.php`
- `app/Domains/Enrollment/Domain/Events/StudentPromoted.php`
- `app/Domains/Enrollment/Domain/Events/StudentRepeated.php`
- `app/Domains/Enrollment/Domain/Events/StudentTransferred.php`
- `app/Domains/Enrollment/Domain/Events/StudentWithdrawn.php`
- `app/Domains/Enrollment/Domain/Rules/ActiveAcademicYearRule.php`
- `app/Domains/Enrollment/Domain/Rules/UniqueActiveEnrollmentRule.php`
- `app/Domains/Enrollment/EnrollmentServiceProvider.php`
- `app/Domains/Enrollment/Infrastructure/Listeners/EnrollmentAuditLogger.php`
- `app/Domains/Enrollment/Infrastructure/Models/Enrollment.php`
- `app/Domains/Enrollment/Infrastructure/Models/EnrollmentHistory.php`
- `app/Domains/Enrollment/Infrastructure/Observers/EnrollmentEntityObserver.php`
- `app/Domains/Enrollment/Presentation/Filament/Resources/BulkEnrollmentResource/Pages/CreateBulkEnrollment.php`
- `app/Domains/Enrollment/Presentation/Filament/Resources/EnrollmentResource.php`
- `app/Domains/Enrollment/Presentation/Filament/Resources/EnrollmentResource/Pages/CreateEnrollment.php`
- `app/Domains/Enrollment/Presentation/Filament/Resources/EnrollmentResource/Pages/EditEnrollment.php`
- `app/Domains/Enrollment/Presentation/Filament/Resources/EnrollmentResource/Pages/ListEnrollments.php`
- `app/Domains/Guardian/Application/Actions/CreateGuardianAction.php`
- `app/Domains/Guardian/Application/Actions/UpdateGuardianAction.php`
- `app/Domains/Guardian/Application/Actions/LinkGuardianAction.php`
- `app/Domains/Guardian/Application/Actions/UnlinkGuardianAction.php`
- `app/Domains/Guardian/Application/Actions/AssignPrimaryGuardianAction.php`
- `app/Domains/Guardian/Application/Actions/AssignEmergencyContactAction.php`
- `app/Domains/Guardian/Application/Actions/AssignPickupAuthorizationAction.php`
- `app/Domains/Guardian/Application/DTOs/GuardianData.php`
- `app/Domains/Guardian/Application/DTOs/RelationshipData.php`
- `app/Domains/Guardian/Application/Services/GuardianService.php`
- `app/Domains/Guardian/Application/Services/GuardianNumberGenerator.php`
- `app/Domains/Guardian/Domain/Enums/GuardianStatus.php`
- `app/Domains/Guardian/Domain/Events/GuardianCreated.php`
- `app/Domains/Guardian/Domain/Exceptions/DuplicateGuardianException.php`
- `app/Domains/Guardian/Domain/Exceptions/DuplicateRelationshipException.php`
- `app/Domains/Guardian/Domain/Rules/GuardianRules.php`
- `app/Domains/Guardian/Domain/Traits/HandlesGuardianAuthorization.php`
- `app/Domains/Guardian/Domain/ValueObjects/EmailAddress.php`
- `app/Domains/Guardian/Domain/ValueObjects/PhoneNumber.php`
- `app/Domains/Guardian/DTOs/GuardianData.php`
- `app/Domains/Guardian/Enums/GuardianStatus.php`
- `app/Domains/Guardian/GuardianServiceProvider.php`
- `app/Domains/Guardian/Infrastructure/Listeners/GuardianAuditLogger.php`
- `app/Domains/Guardian/Infrastructure/Models/Guardian.php`
- `app/Domains/Guardian/Infrastructure/Models/RelationshipType.php`
- `app/Domains/Guardian/Infrastructure/Models/StudentGuardianRelationship.php`
- `app/Domains/Guardian/Infrastructure/Models/StudentGuardianRelationshipHistory.php`
- `app/Domains/Guardian/Infrastructure/Policies/GuardianPolicy.php`
- `app/Domains/Guardian/Presentation/Filament/Resources/AuthorizedPickupResource.php`
- `app/Domains/Guardian/Presentation/Filament/Resources/EmergencyContactsResource.php`
- `app/Domains/Guardian/Presentation/Filament/Resources/GuardianResource.php`
- `app/Domains/Guardian/Presentation/Filament/Resources/GuardianResource/Pages/CreateGuardian.php`
- `app/Domains/Guardian/Presentation/Filament/Resources/GuardianResource/Pages/EditGuardian.php`
- `app/Domains/Guardian/Presentation/Filament/Resources/GuardianResource/Pages/ListGuardians.php`
- `app/Domains/Guardian/Presentation/Filament/Resources/GuardianResource/RelationManagers/RelationshipsRelationManager.php`
- `app/Domains/Guardian/Presentation/Filament/Resources/GuardianResource/RelationManagers/StudentsRelationManager.php`
- `app/Domains/Guardian/Presentation/Filament/Resources/StudentGuardianRelationshipResource.php`
- `app/Domains/Guardian/Presentation/Filament/Resources/StudentGuardianRelationshipResource/Pages/EditStudentGuardianRelationship.php`
- `app/Domains/Guardian/Rules/GuardianRules.php`
- `app/Domains/Guardian/Services/GuardianService.php`
- `app/Domains/School/Models/School.php`
- `app/Domains/Student/Application/Actions/CreateStudentAction.php`
- `app/Domains/Student/Application/Actions/UpdateStudentAction.php`
- `app/Domains/Student/Application/DTOs/CreateStudentData.php`
- `app/Domains/Student/Application/DTOs/UpdateStudentData.php`
- `app/Domains/Student/Application/Services/StudentService.php`
- `app/Domains/Student/Domain/Events/StudentCreated.php`
- `app/Domains/Student/Domain/Exceptions/DuplicateStudentException.php`
- `app/Domains/Student/Domain/ValueObjects/GuardianReference.php`
- `app/Domains/Student/DTOs/StudentData.php`
- `app/Domains/Student/Enums/StudentStatus.php`
- `app/Domains/Student/Infrastructure/Listeners/StudentAuditLogger.php`
- `app/Domains/Student/Infrastructure/Models/Student.php`
- `app/Domains/Student/Infrastructure/Models/StudentGuardian.php`
- `app/Domains/Student/Presentation/Filament/Resources/StudentResource.php`
- `app/Domains/Student/Presentation/Filament/Resources/StudentResource/Pages/CreateStudent.php`
- `app/Domains/Student/Presentation/Filament/Resources/StudentResource/Pages/EditStudent.php`
- `app/Domains/Student/Presentation/Filament/Resources/StudentResource/Pages/ListStudents.php`
- `app/Domains/Student/Presentation/Filament/Resources/StudentResource/RelationManagers/GuardiansRelationManager.php`
- `app/Domains/Student/Rules/StudentRules.php`
- `app/Domains/Student/Services/StudentService.php`
- `app/Domains/StudentEnrollment/DTOs/StudentEnrollmentData.php`
- `app/Domains/StudentEnrollment/Enums/EnrollmentStatus.php`
- `app/Domains/StudentEnrollment/Enums/EnrollmentType.php`
- `app/Domains/StudentEnrollment/Events/EnrollmentActivated.php`
- `app/Domains/StudentEnrollment/Events/EnrollmentCompleted.php`
- `app/Domains/StudentEnrollment/Events/EnrollmentSuspended.php`
- `app/Domains/StudentEnrollment/Events/EnrollmentWithdrawn.php`
- `app/Domains/StudentEnrollment/Events/StudentEnrolled.php`
- `app/Domains/StudentEnrollment/Listeners/LogEnrollmentActivated.php`
- `app/Domains/StudentEnrollment/Listeners/LogEnrollmentCompleted.php`
- `app/Domains/StudentEnrollment/Listeners/LogEnrollmentSuspended.php`
- `app/Domains/StudentEnrollment/Listeners/LogEnrollmentWithdrawn.php`
- `app/Domains/StudentEnrollment/Listeners/LogStudentEnrolled.php`
- `app/Domains/StudentEnrollment/Policies/StudentEnrollmentPolicy.php`
- `app/Domains/StudentEnrollment/Rules/StudentEnrollmentRules.php`
- `app/Domains/StudentEnrollment/Services/StudentEnrollmentService.php`
- `app/Domains/User/DTOs/AccountProvisioningCommand.php`
- `app/Domains/User/DTOs/AccountProvisioningContext.php`
- `app/Domains/User/DTOs/AccountProvisioningResult.php`
- `app/Domains/User/DTOs/UserInvitationIssue.php`
- `app/Domains/User/Enums/UserStatus.php`
- `app/Domains/User/Exceptions/AccountInvitationActivationException.php`
- `app/Domains/User/Exceptions/AccountProvisioningException.php`
- `app/Domains/User/Policies/UserPolicy.php`
- `app/Domains/User/Presentation/Http/AccountInvitationActivationController.php`
- `app/Domains/User/Services/AccountInvitationActivationService.php`
- `app/Domains/User/Services/AccountProvisioningService.php`
- `app/Domains/User/UserServiceProvider.php`
- `app/Filament/Resources/CentralUserResource.php`
- `app/Filament/Resources/CentralUserResource/Pages/CreateCentralUser.php`
- `app/Filament/Resources/CentralUserResource/Pages/EditCentralUser.php`
- `app/Filament/Resources/CentralUserResource/Pages/ListCentralUsers.php`
- `app/Filament/Resources/StudentEnrollmentResource.php`
- `app/Filament/Resources/StudentEnrollmentResource/Pages/CreateStudentEnrollment.php`
- `app/Filament/Resources/StudentEnrollmentResource/Pages/EditStudentEnrollment.php`
- `app/Filament/Resources/StudentEnrollmentResource/Pages/ListStudentEnrollments.php`
- `app/Filament/Resources/UserResource.php`
- `app/Filament/Resources/UserResource/Pages/CreateUser.php`
- `app/Filament/Resources/UserResource/Pages/EditUser.php`
- `app/Filament/Resources/UserResource/Pages/ListUsers.php`
- `app/Filament/Resources/UserResource/RelationManagers/RolesRelationManager.php`
- `app/Models/Guardian.php`
- `app/Models/Role.php`
- `app/Models/Student.php`
- `app/Models/StudentEnrollment.php`
- `app/Models/User.php`
- `app/Models/UserInvitation.php`
- `app/Policies/TenantPolicy.php`
- `app/Policies/UserPolicy.php`
- `app/Providers/AppServiceProvider.php`
- `app/Providers/EventServiceProvider.php`
- `app/Providers/Filament/SchoolAdminPanelProvider.php`
- `app/Services/PanelAccessRegistry.php`
- `app/Traits/HasSchoolId.php`
- `app/Traits/ScopedByTenant.php`
- `bootstrap/providers.php`
- `config/nexus-guardian.php`
- `config/tenancy.php`
- `database/factories/Guardian/GuardianFactory.php`
- `database/factories/Guardian/StudentGuardianRelationshipFactory.php`
- `database/migrations/0001_01_01_000000_create_users_table.php`
- `database/migrations/2026_07_14_000002_create_permission_tables.php`
- `database/migrations/2026_07_15_000015_create_guardians_table.php`
- `database/migrations/2026_07_15_000016_create_students_table.php`
- `database/migrations/2026_07_15_000017_create_student_enrollments_table.php`
- `database/migrations/2026_07_17_000070_create_audit_logs_table.php`
- `database/migrations/2026_08_01_000012_add_student_domain_columns_to_students_table.php`
- `database/migrations/2026_08_01_000013_create_student_guardians_table.php`
- `database/migrations/2026_08_01_000014_create_enrollments_table.php`
- `database/migrations/2026_08_03_000001_create_relationship_types_table.php`
- `database/migrations/2026_08_03_000002_upgrade_student_guardians_table_for_relationships.php`
- `database/migrations/2026_08_03_000003_create_student_guardian_relationship_history_table.php`
- `database/migrations/2026_11_24_020539_create_user_invitations_table.php`
- `database/seeders/Guardian/RelationshipTypeSeeder.php`
- `docs/DOCUMENTATION_GOVERNANCE.md`
- `docs/DOCUMENTATION_INDEX.md`
- `docs/docs/00_Project_Management/ADR-025_School_Gateway_Implementation_Report.md`
- `docs/docs/00_Project_Management/Account_Provisioning_Foundation_Specification.md`
- `docs/docs/00_Project_Management/PROJECT_STATE.md`
- `docs/06_ADR/ADR-004-EnrollmentLifecycle.md`
- `routes/tenant.php`
- `tests/Arch/GuardianDomainArchitectureTest.php`
- `tests/Feature/Filament/SchoolAdminStaffProvisioningTest.php`
- `tests/Unit/Domains/Guardian/Actions/CreateGuardianActionTest.php`
- `tests/Unit/Domains/Guardian/Actions/RelationshipActionsTest.php`
- `tests/Unit/Domains/Guardian/GuardianServiceTest.php`
- `tests/Unit/Domains/User/AccountInvitationActivationServiceTest.php`
- `tests/Unit/Domains/User/AccountProvisioningServiceTest.php`

### 12.2 Seul rapport créé

- `docs/docs/00_Project_Management/TASK-09_Student_Guardian_Account_Provisioning_Discovery_Report.md`

### 12.3 Contrôle Git final

- Avant création du rapport : `git status --porcelain=v1 -uall` → aucune ligne.
- Après création du rapport : `git status --porcelain=v1 -uall` retourne exactement une ligne : `?? docs/docs/00_Project_Management/TASK-09_Student_Guardian_Account_Provisioning_Discovery_Report.md`.
- Comparaison : seule la ligne correspondant au rapport créé diffère de l’état initial ; aucun autre fichier suivi ou non suivi n’a été ajouté ou modifié.
- Aucun staging, commit, push, reset, checkout, restore, clean ou stash n’a été effectué.
