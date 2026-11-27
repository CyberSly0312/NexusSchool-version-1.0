# TASK 10C — Rapport d’implémentation : récupération de compte et cycle de vie des accès administratifs

## 1. Résumé de l’implémentation (Implementation summary)

Task 10C livre un dispositif centralisé de récupération de compte et de gestion du cycle de vie des accès administratifs, bâti sur la découverte Task 10A, l’autorisation des rôles Task 10B, la passerelle scolaire, l’architecture existante de provisionnement/invitation et l’infrastructure d’authentification installée.

Trois parcours sont couverts :

1. **Modification personnelle du mot de passe** : tout utilisateur authentifié modifie uniquement son propre compte, après vérification du mot de passe actuel, avec validation et confirmation. La révocation couvre désormais **toutes** les sessions, les jetons « se souvenir de moi », les jetons API, les invitations, la cache de permissions et les jetons de configuration persistants. Aucun hash stocké n’est exposé.
2. **Réinitialisation d’un School Admin initiée par un Super Admin** : le Super Admin ne voit, ne choisit, ne transporte et ne transmet aucun mot de passe en clair. Il est redirigé vers une **page centralisée de remise à usage unique** ; le jeton en clair voyage uniquement dans le fragment `#jeton=` de l’URL, n’est jamais renvoyé au serveur dans une requête HTTP, n’est pas persisté dans une notification, et est retiré de l’adresse et de l’historique du navigateur par le script de la page. Le School Admin définit lui-même son nouveau mot de passe sur le domaine de son établissement.
3. **Départ et remplacement d’un School Admin** : l’ancien compte est suspendu (jamais supprimé), tous ses accès persistants sont révoqués, son historique et ses références sont conservés, et un remplaçant distinct est créé par le service de provisionnement autorisé avec une invitation d’activation séparée, remise par la même page de capacité à usage unique.

Aucun fournisseur d’e-mail ni de SMS n’a été inventé : conformément à Task 10A, la transmission des liens reste une remise manuelle par un opérateur autorisé, avec la mention obligatoire « Aucun e-mail ni SMS n’a été envoyé. ». Le périmètre reste strictement limité aux comptes administratifs ; le provisionnement des élèves et des parents n’a pas été étendu.

La phase finale de vérification a aussi renforcé : la protection du dernier administrateur actif sous verrou transactionnel, l’unicité de l’adresse e-mail du remplaçant sous verrou, l’audit des échecs de validation/persistance/refus, la normalisation de l’e-mail à la passerelle scolaire, le contrôle serveur de l’acteur sur l’action de configuration, et l’interception HTTP d’un jeton présenté sur un autre établissement.

## 2. Mécanismes existants vérifiés et réutilisés (Verified existing mechanisms reused)

La version installée a été vérifiée avant toute modification : Laravel `v12.63.0`, Filament `v5.6.8`, Livewire `v4.3.3`, Spatie Permission `6.25.0`, Stancl Tenancy `v3.10.0`, Sanctum `v4.3.2`.

Mécanismes réutilisés, sans duplication :

- **Authentification et hachage** : `Hash`, `Validator`, garde `web` et statut `UserStatus` existants.
- **Sessions en base** : table `sessions`, déjà utilisée par l’application ; aucune nouvelle table de session n’a été introduite.
- **Jetons API** : `ApiTokenService`, `ApiToken` et `OAuthService` existants. `ApiTokenService::revokeAllForUser()` reste le point de révocation groupée, tenant-scopé ; la validation des jetons révoqués/inactifs reste assurée par `OAuthService`. Le même point est désormais appelé lors d’un changement de mot de passe personnel.
- **Invitations** : `UserInvitation` (jeton aléatoire de 64 caractères hexadécimaux, `token_hash` SHA-256 uniquement, `expires_at`, `consumed_at`, `revoked_at`, portée `school_id`), déjà utilisé pour l’activation initiale et la configuration. Les purposes `school_admin_reset` (24 h) et `school_admin_initial` (48 h) réutilisent exactement ce mécanisme.
- **Provisionnement** : `AccountProvisioningService`, `SchoolAdminProvisioningService` et `SchoolAdminPermissionGrantService` pour la création du remplaçant et l’attribution des permissions school-level.
- **Audit** : `AuditService` et `AuditAction` existants ; aucun second système d’audit n’a été créé.
- **Autorisation des rôles Task 10B** : `App\Domains\Role\Policies\SchoolAdminRolePolicy` (liste autorisée `teacher`, `secretary`, `accountant`) et `UserRoleChangeService` restent l’unique source de vérité pour les mutations de rôles.
- **Passerelle et panneaux** : `SchoolGatewayAuthController` et `InitializeTenancyForPanel` vérifient `isActive()` ; la suspension est immédiatement effective sans nouveau middleware. La passerelle normalise désormais l’e-mail (`trim` + minuscules) avant la recherche **et** avant `Auth::attempt()`.
- **Remise de capacité** : au lieu d’une notification Filament persistante contenant le secret, la phase finale utilise une route centrale authentifiée, un contrôleur dédié et deux vues Blade. Le fragment d’URL est utilisé comme canal client-only : il n’est pas transmis dans les requêtes HTTP et n’entre donc ni dans les journaux serveur ni dans la session.

## 3. Nouvelle architecture et nouveaux services (New architecture and services)

Les règles métier restent dans les couches Domain/Application ; les pages Filament et les contrôleurs ne font que traduire les résultats en interface.

### Domaine `AccountRecovery`

- `DTOs/` : `SchoolAdminResetCommand`, `SchoolAdminResetResult`, `CompletePasswordResetCommand`, `CompletedPasswordResetResult`.
- `Enums/PasswordResetFailure.php` : acteur, établissement, cible, jeton, jeton expiré, jeton révoqué, jeton déjà utilisé, validation, persistance.
- `Exceptions/PasswordResetException.php`.
- `Services/SchoolAdminPasswordResetService.php` : initiation et finalisation de la réinitialisation, dans une transaction, avec audit des refus et des échecs de persistance.
- `Presentation/Http/SchoolAdminPasswordResetController.php` : pages tenant du lien de réinitialisation.
- `Presentation/Http/CapabilityDeliveryController.php` **ajouté lors de la phase finale** : page centrale de remise unique. Il vérifie l’acteur Super Admin actif, l’établissement actif, l’appartenance et le statut de la cible, le purpose et l’existence d’une invitation non consommée, non révoquée et non expirée. Il ne reçoit jamais le jeton en clair.

### Domaine `User`

- `Policies/PasswordPolicy.php` : source unique de validation (`min:8` + confirmation pour le mot de passe personnel ; `min:12` + `PrivilegedPasswordRule` pour les réinitialisations privilégiées) et messages français.
- `Rules/PrivilegedPasswordRule.php` : minuscule, majuscule, chiffre et symbole obligatoires.
- `DTOs/` : `PasswordChangeCommand`, `PasswordChangeResult`, `AccessRevocationCommand`, `AccessRevocationResult`.
- `Enums/` : `PasswordChangeFailure`, `AccessRevocationFailure`, `AccessRevocationTrigger`.
- `Exceptions/` : `PasswordChangeException`, `AccessRevocationException`.
- `Services/UserPasswordChangeService.php` : modification personnelle, acteur = cible par construction (aucun identifiant de cible dans la commande), avec audit des refus d’acteur, de validation et de persistance.
- `Services/UserAccessRevocationService.php` : point d’entrée central et transactionnel de toutes les révocations.

### Domaine `School`

- `DTOs/` : `SchoolAdminLifecycleCommand`, `SchoolAdminLifecycleResult`, `SchoolAdminReplacementCommand`, `SchoolAdminReplacementResult`.
- `SchoolAdminReplacementResult` expose désormais un champ transitoire `plainToken` : il est consommé immédiatement par la couche de présentation, puis n’est ni audité, ni journalisé, ni persisté.
- `Enums/SchoolAdminLifecycleFailure.php`, `Exceptions/SchoolAdminLifecycleException.php`.
- `Services/SchoolAdminLifecycleService.php` : suspension, remplacement et protection du dernier administrateur actif, avec re-vérifications sous verrou et audit des échecs.

### Présentation et infrastructure

- `app/Filament/Pages/ChangePassword.php` : page enregistrée dans les huit panneaux ; formulaire sans sélecteur de compte.
- `resources/views/filament/pages/change-password.blade.php`.
- `routes/tenant.php` : routes `school.account.password-reset.show`, `.store` et `.done`, liées au tenant.
- `resources/views/account-recovery/password-reset/{reset,invalid,done}.blade.php` : vues françaises.
- `routes/web.php` **modifié** : route `central.capability.delivery`, `GET /admin/remise-lien/{school}/{user}`, avec middlewares `web`, `EnsureCentralDomain` et `auth`.
- `resources/views/central/capability-delivery.blade.php` **ajouté** : page française qui lit `#jeton=`, valide le format 64 hexadécimales, construit l’URL tenant avec le domaine canonique et supprime le fragment avec `history.replaceState()`.
- `resources/views/central/capability-delivery-expired.blade.php` **ajouté** : page française « Lien indisponible » en cas d’invitation introuvable/expirée/révoquée/consommée.
- `app/Filament/Resources/SuperAdminSchoolResource/Pages/EditSuperAdminSchool.php` : actions `generate_school_admin_setup_link`, `initiate_school_admin_reset`, `suspend_school_admin`, `replace_school_admin`, toutes gardées côté serveur ; les trois actions de cycle de vie ciblent uniquement un administrateur **actif** et redirigent vers la page de remise au lieu d’envoyer une notification contenant un secret.
- `AuditAction` : nouveaux cas `PasswordChanged`, `PasswordResetRequested`, `PasswordResetCompleted`, `PasswordResetRejected`, `AccessSuspended`, `SchoolAdminReplaced`, `SensitiveOperationRejected`, avec libellés et couleurs français.
- `UserInvitation` : purpose `PURPOSE_SCHOOL_ADMIN_RESET`, `RESET_EXPIRATION_HOURS = 24`, `findByPlainToken()` par hash.
- `ApiTokenService` : `revokeAllForUser()`, filtré par `user_id` et `school_id`.

## 4. Cycle de vie de la modification et de la réinitialisation du mot de passe (Password change and reset lifecycle)

### Modification personnelle

- `PasswordChangeCommand` contient l’acteur authentifié, le mot de passe actuel, le nouveau mot de passe et sa confirmation, ainsi que l’identifiant de session à conserver.
- Validation : mot de passe actuel obligatoire ; nouveau mot de passe `required|string|min:8|confirmed`, messages français ; le nouveau mot de passe doit être différent de l’actuel.
- Un acteur supprimé, inactif ou introuvable est refusé et audité en `sensitive_operation_rejected`. Les échecs de validation sont audités avec la raison, la portée `self` et les **messages de politique français** (jamais les valeurs soumises). Les échecs de persistance sont remplacés par une exception française générique et audités de la même manière.
- Transaction : verrouillage `lockForUpdate()` de l’utilisateur, écriture de `Hash::make()`, révocation centralisée des autres sessions/tokens, puis audit `password_changed`.
- `AccessRevocationCommand::forPasswordChange()` positionne désormais `revokeApiTokens: true` : un jeton bearer ne reste pas utilisable après le remplacement du mot de passe qui protège le compte. Le test associé vérifie que le jeton API est bien `trashed()`.
- Le hash stocké n’est jamais sérialisé dans le résultat, la notification, l’audit ni les exceptions.
- La page Filament `ChangePassword` est disponible dans les huit panneaux et n’offre aucun moyen de cibler un autre utilisateur.

### Réinitialisation initiée par le Super Admin

1. L’acteur est rechargé et doit être actif, non supprimé et posséder `super_admin` ; l’établissement doit être actif ; la cible doit appartenir à cet établissement, être active et posséder `school_admin`.
2. `UserInvitation::issue()` révoque d’abord toute invitation non consommée du même user/purpose/établissement, puis crée une invitation `school_admin_reset` avec `expires_at = now()+24h` et `token_hash = sha256(jeton)` ; le jeton en clair n’est renvoyé que dans le DTO de résultat.
3. Audit `password_reset_requested` : identifiants et date d’expiration uniquement, jamais le jeton ni le hash du mot de passe.
4. L’action Filament construit une redirection vers `/admin/remise-lien/{school}/{user}?purpose=reset#jeton=<jeton>`. Le serveur ne reçoit pas le fragment ; la page de remise affiche le lien complet à transmettre, la date d’expiration et la mention « Aucun e-mail ni SMS n’a été envoyé. ». Le jeton n’apparaît pas dans le HTML de la page ni dans une notification persistante.
5. Le contrôleur de remise vérifie l’acteur (Super Admin actif), l’établissement actif, la cible active du même établissement avec le rôle `school_admin`, le purpose `reset`, et l’existence d’une invitation valide. Sinon il répond par la page française « Lien indisponible » (HTTP 404).
6. La cible ouvre le lien sur le domaine de son établissement (`/account/password-reset/{token}`), choisit son propre mot de passe (`min:12`, minuscule/majuscule/chiffre/symbole, confirmation), puis est redirigée vers `/account/password-reset-termine`.
7. La finalisation revérifie le purpose, l’expiration, la révocation, la consommation, l’établissement et le rôle, consomme le jeton de façon atomique sous relecture verrouillée, écrit le nouveau hash, révoque les accès persistants et audite `password_reset_completed`. Les échecs connus sont audités en `password_reset_rejected` / `sensitive_operation_rejected`, y compris le refus d’établissement ; les erreurs inattendues sont auditées avec la raison `persistance` puis remplacées par une exception française générique.

## 5. Cycle de vie de suspension et de remplacement d’un School Admin (School Admin suspension and replacement lifecycle)

- **Suspension** : réservée à un Super Admin actif ; l’établissement doit être actif ; la cible doit être un School Admin actif du même établissement. Le dernier administrateur actif ne peut pas être suspendu isolément (`SchoolAdminLifecycleFailure::LastActive`). Une **seconde vérification sous `lockForUpdate()`** empêche deux suspensions concurrentes de laisser l’établissement sans administrateur actif.
- La suspension passe le statut à `Suspended`, révoque centralement tous les accès, conserve la ligne `users`, le rôle, les audits et les références historiques. Aucune suppression, même douce, n’est effectuée.
- **Remplacement** : le service vérifie la validité du nom/e-mail, refuse toute réutilisation de l’adresse de l’ancien compte (`IdentityTransfer`) et toute adresse déjà existante. Une **re-vérification sous verrou** couvre la course avec un remplacement concurrent. Il crée ensuite un **nouveau** compte via `AccountProvisioningService` avec le rôle `school_admin`.
- Le remplaçant est `Pending`, reçoit une invitation d’activation distincte (48 h, même infrastructure à usage unique) et définit son propre mot de passe. Le jeton d’activation est transmis au DTO de résultat puis à la même page de remise centralisée, avec `purpose=activation` et le libellé « Activation du compte ».
- L’ancien compte est suspendu et ses accès révoqués dans la même transaction. Les audits `school_admin_replaced`, `access_suspended` et, en cas de refus ou d’erreur inattendue, `sensitive_operation_rejected` sont écrits via `AuditService`.
- Les actions Super Admin correspondantes sont visibles uniquement pour un acteur `super_admin` actif avec un administrateur **actif** existant. Le serveur revérifie l’acteur indépendamment de cette visibilité, y compris pour l’action de génération du lien de configuration. L’action de création initiale reste masquée tant qu’un administrateur existe.
- Les journaux ne contiennent ni adresse e-mail, ni mot de passe, ni jeton.

## 6. Comportement de révocation des sessions et des jetons (Session and token revocation behavior)

`UserAccessRevocationService::revoke()` est le seul point de révocation. Dans une transaction, il traite selon les indicateurs de `AccessRevocationCommand` :

- lignes de la table `sessions` (avec possibilité de conserver la session courante) ;
- `remember_token` neutralisé par `forceFill(['remember_token' => null])`, sans créer de nouveau secret ;
- jetons API révoqués en masse via `ApiTokenService::revokeAllForUser()`, requête filtrée par `user_id` et `school_id` — y compris lors d’un **changement de mot de passe personnel** ;
- jetons Laravel `password_reset_tokens` supprimés pour l’adresse concernée ;
- invitations non consommées révoquées ;
- jetons de configuration `SchoolAdminSetupToken` non utilisés invalidés via `used_at` (aucune colonne `revoked_at` n’existe dans cette table historique) ;
- caches Spatie Permission purgés pour l’utilisateur et le registre global.

Le service vérifie la correspondance `school_id` attendu avant toute révocation et n’expose aucun secret dans son résultat. La passerelle scolaire rejette les comptes inactifs avant l’authentification ; avec l’e-mail normalisé, un compte suspendu ou une casse différente ne peuvent pas contourner ce contrôle. Le message d’erreur et la description d’audit de la passerelle sont en français. Le middleware des panneaux rejette tout utilisateur authentifié devenu inactif à chaque requête, et le chemin API rejette les jetons révoqués/inactifs via `OAuthService`.

## 7. Isolation tenant et garanties d’autorisation (Tenant isolation and authorization safeguards)

- Toutes les lectures d’acteur, d’établissement et de cible sont rechargées depuis la base puis filtrées par `school_id` ; une cible d’une autre école est rejetée avant toute mutation.
- Les URL de réinitialisation et d’activation sont construites à partir du domaine canonique de l’établissement. Un jeton valide présenté sur une autre école est refusé en HTTP 404 avec la page « Lien invalide », sans modification du mot de passe d’origine et sans consommation du jeton.
- La route centrale de remise est protégée par `EnsureCentralDomain` et `auth`, puis par un contrôle serveur explicite du rôle `super_admin`, du statut actif et de la non-suppression de l’acteur. Un acteur non autorisé reçoit HTTP 403.
- Les autorisations métier sont vérifiées côté serveur dans les services, indépendamment de la visibilité des actions Filament. Toute tentative directe de révocation/réinitialisation est refusée et auditée.
- Task 10B reste autoritaire : aucune mutation de rôle n’a été réécrite dans Task 10C ; le remplacement passe par le service de provisionnement existant.
- Le dernier administrateur actif est protégé, y compris sous concurrence ; le transfert d’identité par réutilisation d’e-mail est interdit, y compris sous concurrence.
- Les champs de mot de passe directs sont masqués/déshydratés pour les cibles `school_admin` dans `CentralUserResource` et `UserResource`, afin qu’aucun opérateur ne puisse fixer leur mot de passe depuis une ressource d’administration.

## 8. Comportement d’audit et garanties de traitement des secrets (Audit behavior and secret-handling guarantees)

- Tous les événements métier passent par `AuditService` ; aucun journal secondaire n’a été ajouté.
- Les actions ajoutées portent des libellés français (`Mot de passe modifié`, `Réinitialisation du mot de passe demandée/terminée/refusée`, `Accès suspendu`, `Administrateur d’établissement remplacé`, `Opération sensible refusée`). Les confirmations, notifications, messages d’erreur et exceptions sont également en français.
- Les identifiants `auditable_id` et `user_id` sont explicitement convertis en chaînes pour satisfaire le DTO d’audit strict.
- Les journaux ne contiennent aucun hash, aucun mot de passe en clair, aucun jeton de réinitialisation ni URL secrète. Les tests vérifient que la sérialisation JSON des audits ne contient ni le jeton ni le hash.
- Le jeton en clair n’existe que dans le DTO de résultat, puis dans le fragment de la redirection vers la page de remise. Le fragment n’étant pas envoyé dans les requêtes HTTP, le contrôleur ne le reçoit pas : il n’est donc ni journalisé, ni stocké en base, ni audité, ni renvoyé dans le HTML de la page. La base ne stocke que `token_hash` (SHA-256). La page supprime le fragment de l’adresse et de l’historique dès son chargement.
- Aucune notification Filament persistante ne contient désormais de capacité en clair ; les tests vérifient que la file de notifications est vide après l’action et que le HTML de remise ne contient pas le jeton.
- Les exceptions métier sont françaises et ne contiennent ni secret ni détail technique ; les échecs inattendus sont remplacés par des messages génériques. Aucun secret n’est écrit dans les logs applicatifs, la base de données ou la sortie de débogage.

### Flux exact de remise du secret par fragment d’URL

1. Le service métier crée l’invitation et ne stocke que `token_hash = sha256(jeton)` ; le `plainToken` reste uniquement dans le DTO de résultat retourné à l’action Filament.
2. L’action Filament répond par une redirection vers `route('central.capability.delivery', …).'#jeton='.rawurlencode($plainToken)`. Le secret est donc placé dans le **fragment** de l’URL de redirection, pas dans un paramètre de requête, un corps de formulaire, une notification Filament ni une session.
3. Le navigateur charge `/admin/remise-lien/{school}/{user}?purpose=reset|activation` : conformément au comportement HTTP des navigateurs, le fragment n’est pas joint à cette requête. Le contrôleur ne reçoit donc que les identifiants d’établissement/utilisateur et le purpose ; il retrouve l’invitation valide par requête sur le hash et vérifie tous les garde-fous serveur.
4. Le script de `capability-delivery.blade.php` lit `window.location.hash`, accepte uniquement 64 caractères hexadécimaux, construit le lien tenant final avec le domaine canonique et l’affiche dans un champ en lecture seule. Il appelle immédiatement `history.replaceState(null, '', pathname + search)` : le fragment disparaît alors de la barre d’adresse et de l’historique du navigateur.
5. Vérifications correspondantes : la page rendue est en français, `assertDontSee($jeton)` confirme l’absence du secret dans le HTML, la file de notifications est vide, les audits sérialisés ne contiennent ni jeton ni hash de mot de passe, et la table `user_invitations` ne contient que le hash SHA-256. Aucune requête HTTP supplémentaire n’est générée par le script : il ne fait que lire le fragment et mettre à jour le DOM/localisation du navigateur.
6. Limite honnête : le secret est brièvement présent dans l’en-tête `Location` de la redirection et dans la barre d’adresse avant exécution du script ; un opérateur peut le copier, un outil de capture d’écran ou une extension de navigateur peut le lire. La transmission humaine reste donc le maillon non automatisé, comme documenté au §13.

## 9. Fichiers exacts créés et modifiés (Exact files created and modified)

### Créés par l’implémentation Task 10C déjà présente dans `HEAD`

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

### Créés pendant la phase finale de vérification/hardening

```
app/Domains/AccountRecovery/Presentation/Http/CapabilityDeliveryController.php
resources/views/central/capability-delivery.blade.php
resources/views/central/capability-delivery-expired.blade.php
```

### Modifiés pendant la phase finale de vérification/hardening

```
app/Domains/AccountRecovery/Services/SchoolAdminPasswordResetService.php
app/Domains/School/DTOs/SchoolAdminReplacementResult.php
app/Domains/School/Services/SchoolAdminLifecycleService.php
app/Domains/User/DTOs/AccessRevocationCommand.php
app/Domains/User/Services/UserPasswordChangeService.php
app/Filament/Resources/SuperAdminSchoolResource/Pages/EditSuperAdminSchool.php
app/Http/Controllers/Auth/SchoolGatewayAuthController.php
routes/web.php
tests/Feature/AccountRecovery/PasswordChangeTest.php
tests/Feature/AccountRecovery/SchoolAdminPasswordResetTest.php
tests/Feature/AccountRecovery/SuperAdminLifecycleActionsTest.php
docs/docs/00_Project_Management/TASK-10C_Account_Recovery_Access_Lifecycle_Report.md
```

### Modifiés par l’implémentation Task 10C déjà présente dans `HEAD`

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

Les fichiers `app/Domains/Role/...`, `app/Filament/Resources/CentralUserResource.php`, `app/Filament/Resources/UserResource.php`, `app/Filament/Resources/UserResource/RelationManagers/RolesRelationManager.php`, `app/Domains/User/Services/AccountProvisioningService.php`, `app/Providers/EventServiceProvider.php`, `bootstrap/providers.php`, `tests/Feature/Filament/SchoolAdminStaffProvisioningTest.php` et `tests/Feature/Filament/SchoolAdminRoleAssignmentSecurityTest.php` étaient déjà présents avant Task 10C et n’ont pas été écrasés. Les rapports Task 09 et Task 10A sont intacts.

## 10. Tests exécutés et résultats exacts (Tests executed and exact results)

Tous les tests ont été lancés avec `php artisan test` (jamais avec `vendor/bin/phpunit`, qui échoue sur le helper `makeTenant()`). L’implémentation de base était déjà présente dans l’arbre commité au début de la session (`HEAD` = `daf4b71 account management`) ; les résultats ci-dessous sont ceux des exécutions effectivement rejouées après les renforcements finaux.

| Commande | Résultat exact |
|---|---|
| `php artisan test tests/Feature/AccountRecovery --testdox --no-ansi` | `OK (28 tests, 176 assertions)`, `Duration: 26.78s` |
| `php artisan test tests/Feature/AccountRecovery/SuperAdminLifecycleActionsTest.php --testdox --no-ansi` (sous-ensemble rejoué séparément) | `OK (4 tests, 48 assertions)`, `Duration: 7.68s` |
| `php artisan test --filter='(SchoolGatewayAuthenticationTest\|ApiToken\|OAuth\|SchoolAdminProvisioningTest\|SuperAdminInitialSchoolAdminProvisioningTest\|SchoolAdminStaffProvisioningTest\|SchoolAdminRoleAssignmentSecurityTest\|TenantIsolation\|AuditActionTest\|AuditServiceTest\|UserResourceTest)' --testdox --no-ansi` | `OK (227 tests, 1104 assertions)`, `Duration: 211.55s` |
| `php artisan test --no-ansi` (première exécution complète, avant instrumentation du code de sortie) | `Tests: 2363 passed (7281 assertions)`, `Duration: 799.42s` ; code de sortie non capturé lors de cette exécution |
| `php artisan test --no-ansi` (exécution finale avec capture du code de sortie) | `Tests: 1 failed, 2362 passed (7281 assertions)`, `Duration: 841.54s`, `EXIT_CODE=1` |
| `php artisan test tests/Unit/Domains/AcademicYear/AcademicYearServiceTest.php --no-ansi` (réexécution isolée de l’unique échec) | `Tests: 23 passed (40 assertions)`, `Duration: 25.03s`, `EXIT_CODE=0` |

L’unique échec de l’exécution finale est `FAILED Tests\Unit\Domains\AcademicYear\AcademicYearServiceTest > can search by name` : `Failed asserting that 2 matches expected 1.` à `tests/Unit/Domains/AcademicYear/AcademicYearServiceTest.php:245`. Ce fichier est sans rapport avec Task 10C et passe intégralement lors de sa réexécution isolée (`EXIT_CODE=0`) ; l’échec observé seulement en suite complète indique une instabilité préexistante d’isolation du test/factory AcademicYear (données résiduelles ou collision de nom dans la base tenant de test), pas une régression Task 10C. Aucun code applicatif n’a été modifié pour tenter de le masquer ; le défaut est signalé séparément.

La suite Task 10C couvre explicitement : modification propre, mot de passe actuel erroné, validation et confirmation, impossibilité de cibler un autre utilisateur, révocation du jeton API, audit des refus, initiation/refus/expiration/usage unique/révocation du jeton précédent/finalisation de la réinitialisation, refus inter-établissements en HTTP, suspension, rejet d’authentification de l’ancien administrateur, révocation des sessions et jetons, provisionnement du remplaçant, protection du dernier administrateur actif, isolation inter-écoles, audits de succès et de refus, absence de notification persistante contenant le secret et absence de fuite de mot de passe ou de jeton dans le HTML et les audits.

## 11. Résultats Pint et PHPStan (Pint and PHPStan results)

- `vendor/bin/pint --test <tous les fichiers PHP créés/modifiés listés au §9>` → `{"tool":"pint","result":"passed"}` (réexécuté après les derniers changements).
- `vendor/bin/phpstan analyse --no-progress --memory-limit=2G --no-ansi` → `[OK] No errors` (réexécuté après les derniers changements, avec `phpstan.neon`).
- `phpstan-baseline.neon` n’a pas été modifié. Le Pint global du dépôt n’a pas été rejoué pendant cette session ; le contrôle pertinent porte sur l’ensemble des fichiers PHP créés/modifiés, qui inclut tout Task 10C, et il est vert.

## 12. Comparaison des états Git initial et final (Initial/final Git status comparison)

Commande : `git status --porcelain=v1 -uall`.

État initial de cette session de vérification (avant toute modification de ce rapport) — sortie exacte :

```text
 M docs/docs/00_Project_Management/TASK-10C_Account_Recovery_Access_Lifecycle_Report.md
```

`HEAD` au début de la session : `daf4b71 account management`. L’implémentation Task 10C de base était déjà commitée ; les renforcements décrits au §9 ont été apportés sans indexation.

État final de cette session de vérification — sortie exacte :

```text
 M app/Domains/AccountRecovery/Services/SchoolAdminPasswordResetService.php
 M app/Domains/School/DTOs/SchoolAdminReplacementResult.php
 M app/Domains/School/Services/SchoolAdminLifecycleService.php
 M app/Domains/User/DTOs/AccessRevocationCommand.php
 M app/Domains/User/Services/UserPasswordChangeService.php
 M app/Filament/Resources/SuperAdminSchoolResource/Pages/EditSuperAdminSchool.php
 M app/Http/Controllers/Auth/SchoolGatewayAuthController.php
 M docs/docs/00_Project_Management/TASK-10C_Account_Recovery_Access_Lifecycle_Report.md
 M routes/web.php
 M tests/Feature/AccountRecovery/PasswordChangeTest.php
 M tests/Feature/AccountRecovery/SchoolAdminPasswordResetTest.php
 M tests/Feature/AccountRecovery/SuperAdminLifecycleActionsTest.php
?? app/Domains/AccountRecovery/Presentation/Http/CapabilityDeliveryController.php
?? resources/views/central/capability-delivery-expired.blade.php
?? resources/views/central/capability-delivery.blade.php
```

Différences par rapport à l’état initial — explication complète :

- Le rapport Task 10C était déjà modifié au départ ; il reste le seul fichier de rapport touché et sa modification a été remplacée par la version factuelle finale, sans être annulée ni réinitialisée.
- Onze fichiers suivis supplémentaires sont apparus comme modifiés pendant la phase finale : les sept fichiers d’application (`SchoolAdminPasswordResetService`, `SchoolAdminReplacementResult`, `SchoolAdminLifecycleService`, `AccessRevocationCommand`, `UserPasswordChangeService`, `EditSuperAdminSchool`, `SchoolGatewayAuthController`), la route `routes/web.php` et les trois fichiers de tests AccountRecovery. Ils correspondent exactement aux renforcements décrits au §9.
- Trois fichiers non suivis ont été créés : `CapabilityDeliveryController.php` et les deux vues `central/capability-delivery*.blade.php`.
- Aucun autre chemin n’a été ajouté, supprimé ou modifié. Les rapports Task 09 et Task 10A, `phpstan-baseline.neon` et tous les fichiers Task 10B préexistants ont été laissés intacts ; aucune modification sans rapport n’a été écrasée.

Pendant cette session, aucune opération Git interdite n’a été effectuée : aucun ajout à l’index, commit, push, reset, checkout, clean, restore ni stash. `phpstan-baseline.neon` n’a pas été modifié. Les rapports Task 09 et Task 10A sont intacts.

## 13. Limites restantes et éléments explicitement non implémentés (Remaining limitations and explicitly unimplemented items)

- **Aucun transport d’e-mail ou de SMS** n’a été configuré ni simulé : les liens sont des capacités à usage unique affichées à l’opérateur autorisé sur une page dédiée. Leur transmission sécurisée reste une responsabilité humaine ; aucune garantie de remise hors plateforme n’est apportée.
- Le jeton en clair transite par le **fragment de l’URL de redirection**. Les navigateurs n’envoient pas le fragment dans les requêtes HTTP, et la page le retire de l’adresse/historique dès son chargement, mais il peut rester brièvement visible dans la barre d’adresse ou être copié avant l’exécution du script. Aucun secret n’est écrit dans les logs, la base de données, les audits ou la sortie de débogage ; cette garantie ne couvre pas un navigateur compromis ni une capture d’écran humaine.
- Le lien final transmis contient le jeton dans son chemin (`/account/password-reset/{token}` ou `/account/activate/{token}`) : il est envoyé au domaine de l’établissement lors de l’usage prévu. C’est le canal fonctionnel voulu ; le serveur ne conserve que son hash SHA-256.
- La suspension **autonome** du dernier School Admin actif reste refusée ; seule la voie de remplacement avec un nouveau compte et une invitation est autorisée.
- Le remplacement crée un compte `Pending` : l’établissement dépend de la rédemption du lien d’activation par le remplaçant pour disposer d’un administrateur actif.
- Aucune suppression (même douce) du compte sortant n’est prévue : les données historiques restent conservées par conception.
- Le provisionnement des élèves et des parents, les politiques de rotation périodique des mots de passe, la vérification en deux étapes et tout fournisseur externe de messagerie restent hors périmètre.
- Le Pint global du dépôt n’a pas été rejoué pendant la session de vérification ; le contrôle retenu porte sur tous les fichiers PHP créés/modifiés, qui inclut Task 10C, et il est vert.
- La suite complète finale n’est pas verte : `EXIT_CODE=1` avec un échec isolé dans `AcademicYearServiceTest > can search by name`, sans rapport avec Task 10C et vert en réexécution isolée. Les suites ciblées Task 10C et les suites connexes (gateway, API, provisionnement, rôles, isolation tenant) sont vertes ; aucune conclusion globale de stabilité de tout le dépôt ne doit être tirée de ce rapport.
- Ce rapport ne constitue pas une déclaration de disponibilité en production (« production readiness ») : il documente uniquement le comportement implémenté et vérifié par les tests et outils ci-dessus.
