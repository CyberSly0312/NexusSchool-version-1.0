# TASK 10C — Rapport d’implémentation : récupération de compte et cycle de vie des accès administratifs

## 1. Résumé de l’implémentation (Implementation summary)

Task 10C livre un dispositif centralisé de récupération de compte et de gestion du cycle de vie des accès administratifs, bâti sur la découverte Task 10A, l’autorisation des rôles Task 10B, la passerelle scolaire, l’architecture existante de provisionnement/invitation et l’infrastructure d’authentification installée.

Trois parcours sont désormais couverts :

1. **Modification personnelle du mot de passe** : tout utilisateur authentifié modifie uniquement son propre compte, après vérification du mot de passe actuel, avec validation et confirmation, sans jamais exposer le hash stocké.
2. **Réinitialisation d’un School Admin initiée par un Super Admin** : le Super Admin ne voit, ne choisit, ne transporte et ne transmet aucun mot de passe en clair. Il obtient une notification Filament **unique** contenant un lien à usage unique valable 24 heures ; le School Admin définit lui-même son nouveau mot de passe sur le domaine de son établissement.
3. **Départ et remplacement d’un School Admin** : l’ancien compte est suspendu (jamais supprimé), tous ses accès persistants sont révoqués, son historique et ses références sont conservés, et un remplaçant distinct est créé par le service de provisionnement autorisé avec une invitation d’activation séparée.

Aucun fournisseur d’e-mail ni de SMS n’a été inventé : conformément à Task 10A, la remise des liens se fait par notification Filament visible de l’opérateur, avec la mention obligatoire « Aucun e-mail ni SMS n’a été envoyé. ». Le périmètre reste strictement limité aux comptes administratifs ; le provisionnement des élèves et des parents n’a pas été étendu.

## 2. Mécanismes existants vérifiés et réutilisés (Verified existing mechanisms reused)

La version installée a été vérifiée avant toute modification : Laravel `v12.63.0`, Filament `v5.6.8`, Livewire `v4.3.3`, Spatie Permission `6.25.0`, Stancl Tenancy `v3.10.0`, Sanctum `v4.3.2`.

Mécanismes réutilisés, sans duplication :

- **Authentification et hachage** : `Hash`, `Validator`, garde `web` et statut `UserStatus` existants.
- **Sessions en base** : table `sessions`, déjà utilisée par l’application ; aucune nouvelle table de session n’a été introduite.
- **Jetons API** : `ApiTokenService`, `ApiToken` et `OAuthService` existants. `ApiTokenService::revokeAllForUser()` a été ajouté comme point de révocation groupée, tenant-scopé ; la validation des jetons révoqués/inactifs reste assurée par `OAuthService`.
- **Invitations** : `UserInvitation` (jeton aléatoire de 64 caractères hexadécimaux, hash SHA-256 uniquement, `expires_at`, `consumed_at`, `revoked_at`, portée `school_id`), déjà utilisé pour l’activation initiale et la configuration. Le nouveau purpose `school_admin_reset` réutilise exactement ce mécanisme.
- **Provisionnement** : `AccountProvisioningService`, `SchoolAdminProvisioningService` et `SchoolAdminPermissionGrantService` pour la création du remplaçant et l’attribution des permissions school-level.
- **Audit** : `AuditService` et `AuditAction` existants ; aucun second système d’audit n’a été créé.
- **Autorisation des rôles Task 10B** : `App\Domains\Role\Policies\SchoolAdminRolePolicy` (liste autorisée `teacher`, `secretary`, `accountant`) et `UserRoleChangeService` restent l’unique source de vérité pour les mutations de rôles.
- **Passerelle et panneaux** : `SchoolGatewayAuthController` et `InitializeTenancyForPanel` vérifient déjà `isActive()` ; la suspension est donc immédiatement effective sans nouveau middleware.
- **Notifications Filament** : motif « lien à usage unique affiché une seule fois » déjà validé en Task 08/10A pour les liens d’activation.

## 3. Nouvelle architecture et nouveaux services (New architecture and services)

Les règles métier ont été placées dans les couches Domain/Application ; les pages Filament et les contrôleurs ne font que traduire les résultats en interface.

### Domaine `AccountRecovery`

- `DTOs/` : `SchoolAdminResetCommand`, `SchoolAdminResetResult`, `CompletePasswordResetCommand`, `CompletedPasswordResetResult`.
- `Enums/PasswordResetFailure.php` : acteur, établissement, cible, jeton, jeton expiré, jeton révoqué, jeton déjà utilisé, validation, persistance.
- `Exceptions/PasswordResetException.php`.
- `Services/SchoolAdminPasswordResetService.php` : initiation et finalisation de la réinitialisation, dans une transaction.
- `Presentation/Http/SchoolAdminPasswordResetController.php` : pages tenant du lien de réinitialisation.

### Domaine `User`

- `Policies/PasswordPolicy.php` : source unique de validation (`min:8` + confirmation pour le mot de passe personnel ; `min:12` + `PrivilegedPasswordRule` pour les réinitialisations privilégiées) et messages français.
- `Rules/PrivilegedPasswordRule.php` : minuscule, majuscule, chiffre et symbole obligatoires.
- `DTOs/` : `PasswordChangeCommand`, `PasswordChangeResult`, `AccessRevocationCommand`, `AccessRevocationResult`.
- `Enums/` : `PasswordChangeFailure`, `AccessRevocationFailure`, `AccessRevocationTrigger`.
- `Exceptions/` : `PasswordChangeException`, `AccessRevocationException`.
- `Services/UserPasswordChangeService.php` : modification personnelle, acteur = cible par construction (aucun identifiant de cible dans la commande).
- `Services/UserAccessRevocationService.php` : point d’entrée central et transactionnel de toutes les révocations.

### Domaine `School`

- `DTOs/` : `SchoolAdminLifecycleCommand`, `SchoolAdminLifecycleResult`, `SchoolAdminReplacementCommand`, `SchoolAdminReplacementResult`.
- `Enums/SchoolAdminLifecycleFailure.php`, `Exceptions/SchoolAdminLifecycleException.php`.
- `Services/SchoolAdminLifecycleService.php` : suspension, remplacement et protection du dernier administrateur actif.

### Présentation et infrastructure

- `app/Filament/Pages/ChangePassword.php` : page enregistrée dans les huit panneaux ; formulaire sans sélecteur de compte.
- `resources/views/filament/pages/change-password.blade.php`.
- `routes/tenant.php` : routes `school.account.password-reset.show`, `.store` et `.done`, liées au tenant.
- `resources/views/account-recovery/password-reset/{reset,invalid,done}.blade.php` : vues françaises.
- `app/Filament/Resources/SuperAdminSchoolResource/Pages/EditSuperAdminSchool.php` : actions `initiate_school_admin_reset`, `suspend_school_admin`, `replace_school_admin`, toutes gardées côté serveur.
- `AuditAction` : nouveaux cas `PasswordChanged`, `PasswordResetRequested`, `PasswordResetCompleted`, `PasswordResetRejected`, `AccessSuspended`, `SchoolAdminReplaced`, `SensitiveOperationRejected`, avec libellés et couleurs français.
- `UserInvitation` : purpose `PURPOSE_SCHOOL_ADMIN_RESET`, `RESET_EXPIRATION_HOURS = 24`, `findByPlainToken()` par hash.
- `ApiTokenService` : `revokeAllForUser()`, filtré par `user_id` et `school_id`.

## 4. Cycle de vie de la modification et de la réinitialisation du mot de passe (Password change and reset lifecycle)

### Modification personnelle

- `PasswordChangeCommand` contient l’acteur authentifié, le mot de passe actuel, le nouveau mot de passe et sa confirmation, ainsi que l’identifiant de session à conserver.
- Validation : mot de passe actuel obligatoire ; nouveau mot de passe `required|string|min:8|confirmed`, messages français ; le nouveau mot de passe doit être différent de l’actuel.
- Le hash stocké n’est jamais sérialisé dans le résultat, la notification, l’audit ni les exceptions.
- Transaction : verrouillage `lockForUpdate()` de l’utilisateur, écriture de `Hash::make()`, révocation centralisée des autres sessions/tokens, puis audit `password_changed` ou `sensitive_operation_rejected`.
- La page Filament `ChangePassword` est disponible dans les huit panneaux et n’offre aucun moyen de cibler un autre utilisateur.

### Réinitialisation initiée par le Super Admin

1. L’acteur est rechargé et doit être actif, non supprimé et posséder `super_admin` ; l’établissement doit être actif ; la cible doit appartenir à cet établissement, être active et posséder `school_admin`.
2. `UserInvitation::issue()` révoque d’abord toute invitation non consommée du même user/purpose/établissement, puis crée une invitation `school_admin_reset` avec `expires_at = now()+24h` et `token_hash = sha256(jeton)` ; le jeton en clair n’est renvoyé que dans le DTO de résultat.
3. Audit `password_reset_requested` : identifiants et date d’expiration uniquement, jamais le jeton ni le hash du mot de passe.
4. L’action Filament affiche une notification **persistante et unique** : lien à usage unique valable 24 heures, consigne de transmission sécurisée, mention « Aucun e-mail ni SMS n’a été envoyé. » et information sur la révocation des liens précédents.
5. La cible ouvre le lien sur le domaine de son établissement (`/account/password-reset/{token}`), choisit son propre mot de passe (`min:12`, minuscule/majuscule/chiffre/symbole, confirmation), puis est redirigée vers `/account/password-reset-termine`.
6. La finalisation revérifie le purpose, l’expiration, la révocation, la consommation, l’établissement et le rôle, consomme le jeton de façon atomique, écrit le nouveau hash, révoque les accès persistants et audite `password_reset_completed`. Les échecs sont audités en `password_reset_rejected` / `sensitive_operation_rejected`.

## 5. Cycle de vie de suspension et de remplacement d’un School Admin (School Admin suspension and replacement lifecycle)

- **Suspension** : réservée à un Super Admin actif ; l’établissement doit être actif ; la cible doit être un School Admin actif du même établissement. Le dernier administrateur actif ne peut pas être suspendu isolément (`SchoolAdminLifecycleFailure::LastActive`), afin de ne jamais laisser un établissement sans administrateur.
- La suspension passe le statut à `Suspended`, révoque centralement tous les accès, conserve la ligne `users`, le rôle, les audits et les références historiques. Aucune suppression, même douce, n’est effectuée.
- **Remplacement** : le service vérifie la validité du nom/e-mail, refuse toute réutilisation de l’adresse de l’ancien compte (`IdentityTransfer`) et toute adresse déjà existante, puis crée un **nouveau** compte via `AccountProvisioningService` avec le rôle `school_admin`. Le remplaçant est `Pending`, reçoit une invitation d’activation distincte (48 h, même infrastructure à usage unique) et définit son propre mot de passe. L’ancien compte est suspendu et ses accès révoqués dans la même transaction.
- Le remplacement avec un nouveau compte et une invitation non consommée constitue la stratégie sûre documentée pour le cas « dernier administrateur actif » : la vérification d’unicité est donc intentionnellement omise dans ce seul flux, pendant que la suspension autonome reste refusée.
- Les audits `school_admin_replaced`, `access_suspended` et, en cas de refus, `sensitive_operation_rejected` sont écrits via `AuditService`. Les journaux ne contiennent ni adresse e-mail, ni mot de passe, ni jeton.
- Les actions Super Admin correspondantes sont visibles uniquement pour un acteur `super_admin` actif avec un administrateur existant ; l’action de création initiale reste masquée tant qu’un administrateur existe.

## 6. Comportement de révocation des sessions et des jetons (Session and token revocation behavior)

`UserAccessRevocationService::revoke()` est le seul point de révocation. Dans une transaction, il traite selon les indicateurs de `AccessRevocationCommand` :

- lignes de la table `sessions` (avec possibilité de conserver la session courante) ;
- `remember_token` neutralisé par `forceFill(['remember_token' => null])`, sans créer de nouveau secret ;
- jetons API révoqués en masse via `ApiTokenService::revokeAllForUser()`, requête filtrée par `user_id` et `school_id` ;
- jetons Laravel `password_reset_tokens` supprimés pour l’adresse concernée ;
- invitations non consommées révoquées ;
- jetons de configuration `SchoolAdminSetupToken` non utilisés invalidés via `used_at` (aucune colonne `revoked_at` n’existe dans cette table historique) ;
- caches Spatie Permission purgés pour l’utilisateur et le registre global.

Le service vérifie la correspondance `school_id` attendu avant toute révocation et n’expose aucun secret dans son résultat. La passerelle scolaire rejette les comptes inactifs avant l’authentification, le middleware des panneaux rejette tout utilisateur authentifié devenu inactif à chaque requête, et le chemin API rejette les jetons révoqués/inactifs via `OAuthService`.

## 7. Isolation tenant et garanties d’autorisation (Tenant isolation and authorization safeguards)

- Toutes les lectures d’acteur, d’établissement et de cible sont rechargées depuis la base puis filtrées par `school_id` ; une cible d’une autre école est rejetée avant toute mutation.
- Les URL de réinitialisation et d’activation sont construites à partir du domaine canonique de l’établissement ; un jeton valide présenté sur une autre école est refusé, sans modification du mot de passe d’origine.
- Les autorisations sont vérifiées côté serveur dans les services, indépendamment de la visibilité des actions Filament. Un acteur non Super Admin ne peut pas accéder au panneau central, et toute tentative directe de révocation/réinitialisation est refusée et auditée.
- Task 10B reste autoritaire : aucune mutation de rôle n’a été réécrite dans Task 10C ; le remplacement passe par le service de provisionnement existant.
- Le dernier administrateur actif est protégé ; le transfert d’identité par réutilisation d’e-mail est interdit.
- Les champs de mot de passe directs sont masqués/déshydratés pour les cibles `school_admin` dans `CentralUserResource` et `UserResource`, afin qu’aucun opérateur ne puisse fixer leur mot de passe depuis une ressource d’administration.

## 8. Comportement d’audit et garanties de traitement des secrets (Audit behavior and secret-handling guarantees)

- Tous les événements métier passent par `AuditService` ; aucun journal secondaire n’a été ajouté.
- Les actions ajoutées portent des libellés français (`Mot de passe modifié`, `Réinitialisation du mot de passe demandée/terminée/refusée`, `Accès suspendu`, `Administrateur d’établissement remplacé`, `Opération sensible refusée`).
- Les identifiants `auditable_id` et `user_id` sont explicitement convertis en chaînes pour satisfaire le DTO d’audit strict.
- Les journaux ne contiennent aucun hash, aucun mot de passe en clair, aucun jeton de réinitialisation ni URL secrète. Les tests vérifient que la sérialisation JSON des audits ne contient ni le jeton ni le hash.
- Le jeton en clair n’existe que dans le DTO de résultat et dans l’URL de la notification Filament à usage unique ; la base ne stocke que `token_hash` (SHA-256). La file de notifications est vidée après une lecture.
- Les exceptions métier sont françaises et ne contiennent ni secret ni détail technique ; les échecs inattendus sont remplacés par des messages génériques.

## 9. Fichiers exacts créés et modifiés (Exact files created and modified)

### Créés pendant Task 10C

```
app/Domains/AccountRecovery/DTOs/CompletePasswordResetCommand.php
app/Domains/AccountRecovery/DTOs/CompletedPasswordResetResult.php
app/Domains/AccountRecovery/DTOs/SchoolAdminResetCommand.php
app/Domains/AccountRecovery/DTOs/SchoolAdminResetResult.php
app/Domains/AccountRecovery/Enums/PasswordResetFailure.php
app/Domains/AccountRecovery/Exceptions/PasswordResetException.php
app/Domains/AccountRecovery/Presentation/Http/SchoolAdminPasswordResetController.php
app/Domains/AccountRecovery/Services/SchoolAdminPasswordResetService.php
app/Domains/School/DTOs/SchoolAdminLifecycleCommand.php
app/Domains/School/DTOs/SchoolAdminLifecycleResult.php
app/Domains/School/DTOs/SchoolAdminReplacementCommand.php
app/Domains/School/DTOs/SchoolAdminReplacementResult.php
app/Domains/School/Enums/SchoolAdminLifecycleFailure.php
app/Domains/School/Exceptions/SchoolAdminLifecycleException.php
app/Domains/School/Services/SchoolAdminLifecycleService.php
app/Domains/User/DTOs/AccessRevocationCommand.php
app/Domains/User/DTOs/AccessRevocationResult.php
app/Domains/User/DTOs/PasswordChangeCommand.php
app/Domains/User/DTOs/PasswordChangeResult.php
app/Domains/User/Enums/AccessRevocationFailure.php
app/Domains/User/Enums/AccessRevocationTrigger.php
app/Domains/User/Enums/PasswordChangeFailure.php
app/Domains/User/Exceptions/AccessRevocationException.php
app/Domains/User/Exceptions/PasswordChangeException.php
app/Domains/User/Policies/PasswordPolicy.php
app/Domains/User/Rules/PrivilegedPasswordRule.php
app/Domains/User/Services/UserAccessRevocationService.php
app/Domains/User/Services/UserPasswordChangeService.php
app/Filament/Pages/ChangePassword.php
resources/views/account-recovery/password-reset/done.blade.php
resources/views/account-recovery/password-reset/invalid.blade.php
resources/views/account-recovery/password-reset/reset.blade.php
resources/views/filament/pages/change-password.blade.php
tests/Feature/AccountRecovery/PasswordChangeTest.php
tests/Feature/AccountRecovery/SchoolAdminLifecycleTest.php
tests/Feature/AccountRecovery/SchoolAdminPasswordResetTest.php
tests/Feature/AccountRecovery/SuperAdminLifecycleActionsTest.php
docs/docs/00_Project_Management/TASK-10C_Account_Recovery_Access_Lifecycle_Report.md
```

### Modifiés pendant Task 10C

```
app/Domains/Api/Services/ApiTokenService.php
app/Domains/System/Enums/AuditAction.php
app/Filament/Resources/SuperAdminSchoolResource/Pages/EditSuperAdminSchool.php
app/Filament/Resources/UserResource/Pages/EditUser.php
app/Http/Controllers/Auth/SchoolGatewayAuthController.php
app/Http/Middleware/InitializeTenancyForPanel.php
app/Models/UserInvitation.php
app/Providers/Filament/AccountantPanelProvider.php
app/Providers/Filament/DirectorPanelProvider.php
app/Providers/Filament/ParentPanelProvider.php
app/Providers/Filament/SchoolAdminPanelProvider.php
app/Providers/Filament/SecretaryPanelProvider.php
app/Providers/Filament/StudentPanelProvider.php
app/Providers/Filament/SuperAdminPanelProvider.php
app/Providers/Filament/TeacherPanelProvider.php
routes/tenant.php
```

### Modifications Task 10B et rapports antérieurs conservés

Les fichiers `app/Domains/Role/...`, `app/Filament/Resources/CentralUserResource.php`, `app/Filament/Resources/UserResource.php`, `app/Filament/Resources/UserResource/RelationManagers/RolesRelationManager.php`, `app/Domains/User/Services/AccountProvisioningService.php`, `app/Providers/EventServiceProvider.php`, `bootstrap/providers.php`, `tests/Feature/Filament/SchoolAdminStaffProvisioningTest.php` et les rapports Task 09/Task 10A ainsi que `tests/Feature/Filament/SchoolAdminRoleAssignmentSecurityTest.php` étaient déjà présents avant Task 10C et n’ont pas été écrasés.

## 10. Tests exécutés et résultats exacts (Tests executed and exact results)

Tous les tests ont été lancés avec `php artisan test` (jamais avec `vendor/bin/phpunit`, qui échoue sur le helper `makeTenant()`).

| Commande | Résultat exact |
|---|---|
| `php artisan test tests/Feature/AccountRecovery --testdox` | `OK (27 tests, 160 assertions)` |
| `php artisan test tests/Feature/AccountRecovery/SuperAdminLifecycleActionsTest.php --testdox` | `OK (4 tests, 41 assertions)` |
| `php artisan test --filter='(SchoolGatewayAuthenticationTest\|ApiToken\|OAuth\|SchoolAdminProvisioningTest\|SuperAdminInitialSchoolAdminProvisioningTest\|SchoolAdminStaffProvisioningTest\|SchoolAdminRoleAssignmentSecurityTest\|TenantIsolation\|AuditActionTest\|AuditServiceTest\|UserResourceTest)' --testdox` | `OK (227 tests, 1104 assertions)` |
| `php artisan test` | `Tests: 2362 passed (7265 assertions)`, `Duration: 841.01s` |

La suite Task 10C couvre explicitement : modification propre, mot de passe actuel erroné, validation et confirmation, impossibilité de cibler un autre utilisateur, initiation/refus/expiration/usage unique/révocation du jeton précédent/finalisation de la réinitialisation, suspension, rejet d’authentification de l’ancien administrateur, révocation des sessions et jetons, provisionnement du remplaçant, protection du dernier administrateur actif, isolation inter-écoles, audits de succès et de refus, et absence de fuite de mot de passe ou de jeton. Les tests Filament ajoutés vérifient la visibilité des actions, la notification de réinitialisation sans mot de passe, le refus de suspension du dernier administrateur et le remplacement effectif.

## 11. Résultats Pint et PHPStan (Pint and PHPStan results)

- `vendor/bin/pint <fichiers PHP modifiés et créés>` → `{"tool":"pint","result":"passed"}` (une première passe avait corrigé `ordered_imports` dans trois fichiers).
- `vendor/bin/pint --test <fichiers PHP modifiés et créés>` → `{"tool":"pint","result":"passed"}`.
- `vendor/bin/phpstan analyse --no-progress --memory-limit=2G` → `[OK] No errors`.
- `phpstan-baseline.neon` n’a pas été modifié. Le Pint sur l’ensemble du projet reste affecté par des fichiers préexistants sans rapport ; le contrôle pertinent porte sur les fichiers modifiés par Task 10C.

## 12. Comparaison des états Git initial et final (Initial/final Git status comparison)

Commande : `git status --porcelain=v1 -uall`.

État initial (avant tout code Task 10C, c’est-à-dire après Task 10B) :

```
 M app/Domains/Role/Events/RoleAssignedToUser.php
 M app/Domains/Role/Events/RoleRevokedFromUser.php
 M app/Domains/System/Enums/AuditAction.php
 M app/Domains/User/Services/AccountProvisioningService.php
 M app/Filament/Resources/CentralUserResource.php
 M app/Filament/Resources/CentralUserResource/Pages/CreateCentralUser.php
 M app/Filament/Resources/CentralUserResource/Pages/EditCentralUser.php
 M app/Filament/Resources/UserResource.php
 M app/Filament/Resources/UserResource/RelationManagers/RolesRelationManager.php
 M app/Providers/EventServiceProvider.php
 M bootstrap/providers.php
 M tests/Feature/Filament/SchoolAdminStaffProvisioningTest.php
?? app/Domains/Role/DTOs/RoleChangeCommand.php
?? app/Domains/Role/DTOs/RoleChangeContext.php
?? app/Domains/Role/DTOs/RoleChangePlan.php
?? app/Domains/Role/DTOs/RoleChangeResult.php
?? app/Domains/Role/Enums/RoleChangeOperation.php
?? app/Domains/Role/Events/RoleChangeRejected.php
?? app/Domains/Role/Exceptions/RoleChangeException.php
?? app/Domains/Role/Listeners/AuditRoleChange.php
?? app/Domains/Role/Policies/SchoolAdminRolePolicy.php
?? app/Domains/Role/RoleServiceProvider.php
?? app/Domains/Role/Services/UserRoleChangeService.php
?? docs/docs/00_Project_Management/TASK-09_Student_Guardian_Account_Provisioning_Discovery_Report.md
?? docs/docs/00_Project_Management/TASK-10A_Account_Recovery_Administrative_Access_Discovery_Report.md
?? tests/Feature/Filament/SchoolAdminRoleAssignmentSecurityTest.php
```

État final :

```
 M app/Domains/Api/Services/ApiTokenService.php
 M app/Domains/Role/Events/RoleAssignedToUser.php
 M app/Domains/Role/Events/RoleRevokedFromUser.php
 M app/Domains/System/Enums/AuditAction.php
 M app/Domains/User/Services/AccountProvisioningService.php
 M app/Filament/Resources/CentralUserResource.php
 M app/Filament/Resources/CentralUserResource/Pages/CreateCentralUser.php
 M app/Filament/Resources/CentralUserResource/Pages/EditCentralUser.php
 M app/Filament/Resources/SuperAdminSchoolResource/Pages/EditSuperAdminSchool.php
 M app/Filament/Resources/UserResource.php
 M app/Filament/Resources/UserResource/Pages/EditUser.php
 M app/Filament/Resources/UserResource/RelationManagers/RolesRelationManager.php
 M app/Http/Controllers/Auth/SchoolGatewayAuthController.php
 M app/Http/Middleware/InitializeTenancyForPanel.php
 M app/Models/UserInvitation.php
 M app/Providers/EventServiceProvider.php
 M app/Providers/Filament/AccountantPanelProvider.php
 M app/Providers/Filament/DirectorPanelProvider.php
 M app/Providers/Filament/ParentPanelProvider.php
 M app/Providers/Filament/SchoolAdminPanelProvider.php
 M app/Providers/Filament/SecretaryPanelProvider.php
 M app/Providers/Filament/StudentPanelProvider.php
 M app/Providers/Filament/SuperAdminPanelProvider.php
 M app/Providers/Filament/TeacherPanelProvider.php
 M bootstrap/providers.php
 M routes/tenant.php
 M tests/Feature/Filament/SchoolAdminStaffProvisioningTest.php
?? app/Domains/AccountRecovery/DTOs/CompletePasswordResetCommand.php
?? app/Domains/AccountRecovery/DTOs/CompletedPasswordResetResult.php
?? app/Domains/AccountRecovery/DTOs/SchoolAdminResetCommand.php
?? app/Domains/AccountRecovery/DTOs/SchoolAdminResetResult.php
?? app/Domains/AccountRecovery/Enums/PasswordResetFailure.php
?? app/Domains/AccountRecovery/Exceptions/PasswordResetException.php
?? app/Domains/AccountRecovery/Presentation/Http/SchoolAdminPasswordResetController.php
?? app/Domains/AccountRecovery/Services/SchoolAdminPasswordResetService.php
?? app/Domains/Role/DTOs/RoleChangeCommand.php
?? app/Domains/Role/DTOs/RoleChangeContext.php
?? app/Domains/Role/DTOs/RoleChangePlan.php
?? app/Domains/Role/DTOs/RoleChangeResult.php
?? app/Domains/Role/Enums/RoleChangeOperation.php
?? app/Domains/Role/Events/RoleChangeRejected.php
?? app/Domains/Role/Exceptions/RoleChangeException.php
?? app/Domains/Role/Listeners/AuditRoleChange.php
?? app/Domains/Role/Policies/SchoolAdminRolePolicy.php
?? app/Domains/Role/RoleServiceProvider.php
?? app/Domains/Role/Services/UserRoleChangeService.php
?? app/Domains/School/DTOs/SchoolAdminLifecycleCommand.php
?? app/Domains/School/DTOs/SchoolAdminLifecycleResult.php
?? app/Domains/School/DTOs/SchoolAdminReplacementCommand.php
?? app/Domains/School/DTOs/SchoolAdminReplacementResult.php
?? app/Domains/School/Enums/SchoolAdminLifecycleFailure.php
?? app/Domains/School/Exceptions/SchoolAdminLifecycleException.php
?? app/Domains/School/Services/SchoolAdminLifecycleService.php
?? app/Domains/User/DTOs/AccessRevocationCommand.php
?? app/Domains/User/DTOs/AccessRevocationResult.php
?? app/Domains/User/DTOs/PasswordChangeCommand.php
?? app/Domains/User/DTOs/PasswordChangeResult.php
?? app/Domains/User/Enums/AccessRevocationFailure.php
?? app/Domains/User/Enums/AccessRevocationTrigger.php
?? app/Domains/User/Enums/PasswordChangeFailure.php
?? app/Domains/User/Exceptions/AccessRevocationException.php
?? app/Domains/User/Exceptions/PasswordChangeException.php
?? app/Domains/User/Policies/PasswordPolicy.php
?? app/Domains/User/Rules/PrivilegedPasswordRule.php
?? app/Domains/User/Services/UserAccessRevocationService.php
?? app/Domains/User/Services/UserPasswordChangeService.php
?? app/Filament/Pages/ChangePassword.php
?? docs/docs/00_Project_Management/TASK-09_Student_Guardian_Account_Provisioning_Discovery_Report.md
?? docs/docs/00_Project_Management/TASK-10A_Account_Recovery_Administrative_Access_Discovery_Report.md
?? docs/docs/00_Project_Management/TASK-10C_Account_Recovery_Access_Lifecycle_Report.md
?? resources/views/account-recovery/password-reset/done.blade.php
?? resources/views/account-recovery/password-reset/invalid.blade.php
?? resources/views/account-recovery/password-reset/reset.blade.php
?? resources/views/filament/pages/change-password.blade.php
?? tests/Feature/AccountRecovery/PasswordChangeTest.php
?? tests/Feature/AccountRecovery/SchoolAdminLifecycleTest.php
?? tests/Feature/AccountRecovery/SchoolAdminPasswordResetTest.php
?? tests/Feature/AccountRecovery/SuperAdminLifecycleActionsTest.php
?? tests/Feature/Filament/SchoolAdminRoleAssignmentSecurityTest.php
```

Aucune opération Git interdite n’a été effectuée : aucun ajout à l’index, commit, push, reset, checkout, clean, restore ni stash. Les rapports Task 09 et Task 10A sont intacts.

## 13. Limites restantes et éléments explicitement non implémentés (Remaining limitations and explicitly unimplemented items)

- **Aucun transport d’e-mail ou de SMS** n’a été configuré ni simulé : les liens sont des capacités à usage unique affichées à l’opérateur autorisé dans une notification Filament. Leur transmission sécurisée reste une responsabilité humaine ; aucune garantie de remise hors plateforme n’est apportée.
- Le lien de réinitialisation apparaît intentionnellement dans l’URL de la notification persistante : c’est le seul emplacement où le jeton en clair est visible. Il n’est ni journalisé, ni stocké en clair, ni renvoyé dans une seconde lecture de la file de notifications.
- La suspension **autonome** du dernier School Admin actif reste refusée ; seule la voie de remplacement avec un nouveau compte et une invitation est autorisée.
- Le remplacement crée un compte `Pending` : l’établissement dépend de la rédemption du lien d’activation par le remplaçant pour disposer d’un administrateur actif.
- Aucune suppression (même douce) du compte sortant n’est prévue : les données historiques restent conservées par conception.
- Le provisionnement des élèves et des parents, les politiques de rotation périodique des mots de passe, la vérification en deux étapes et tout fournisseur externe de messagerie restent hors périmètre.
- Le Pint global du dépôt n’est pas vert en raison de fichiers préexistants sans lien avec Task 10C ; seul le contrôle ciblé sur les fichiers modifiés a été exigé et obtenu.
- Ce rapport ne constitue pas une déclaration de disponibilité en production (« production readiness ») : il documente uniquement le comportement implémenté et vérifié par les tests et outils ci-dessus.
