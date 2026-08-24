SYNCHRONISATION SPR-014 — NE PAS CODER

Tu as correctement détecté une divergence critique entre le workspace actuel
et l'état SPR-014 que nous avions précédemment développé.

CONFIRME D'ABORD CET ÉTAT :

Workspace actuel :
- branch: main
- HEAD: ac8acc2
- working tree: clean
- SPR-013 = dernier état présent dans ce checkout

Le travail SPR-014 précédemment réalisé était NON COMMITÉ dans un autre état
du workspace et n'est donc pas présent ici.

IMPORTANT :
NE RÉIMPLÉMENTE PAS SPR-014.
NE CRÉE AUCUN FICHIER.
NE MODIFIE AUCUN FICHIER.
NE FAIS AUCUN COMMIT.
NE SUPPOSE PAS QUE LE TRAVAIL EST PERDU.

OBJECTIF :
Nous devons d'abord retrouver/synchroniser le workspace qui contient le
travail SPR-014 déjà réalisé.

==================================================
PHASE 1 — RECHERCHE LOCALE UNIQUEMENT
==================================================

Cherche si une autre copie du projet NexusSchool existe localement.

Recherche notamment :

- autres dossiers NexusSchool
- autres workspaces Windsurf
- branches locales
- worktrees Git
- dossiers temporaires
- copies du projet
- éventuels répertoires de sauvegarde

Commandes utiles :

git branch -a
git worktree list
git reflog --all --date=local

Puis recherche dans les emplacements locaux accessibles des fichiers :

RecordLifecycle.php
RecordLifecycleState.php
RecordLifecycleService.php
SPR-014_Records_Lifecycle_Engine_Report.md

Tu peux utiliser PowerShell pour rechercher, mais NE MODIFIE RIEN.

==================================================
PHASE 2 — VÉRIFIER LES RÉSULTATS
==================================================

Si tu trouves une autre copie contenant SPR-014 :

NE LA MODIFIE PAS.

Rapporte :

- chemin exact
- branche éventuelle
- HEAD éventuel
- état git
- présence des fichiers RecordsLifecycle
- présence des intégrations ReportCard
- présence des intégrations AcademicCommunication
- présence des tests
- présence des documents SPR-014

Si tu trouves un worktree Git contenant SPR-014, indique-le explicitement.

==================================================
PHASE 3 — RECHERCHE GIT
==================================================

Vérifie également si les travaux SPR-014 ont éventuellement été commités
dans une branche ou un commit qui n'est pas actuellement checkouté.

Utilise :

git log --all --oneline --decorate --graph
git reflog --all
git fsck --no-reflogs --unreachable

Cherche particulièrement :

RecordsLifecycle
SPR-014
AcademicCommunication
ReportCard

Si tu trouves un commit pertinent, NE LE CHERRY-PICK PAS.

Donne-moi seulement :

commit hash
message
branche éventuelle
date
fichiers concernés

==================================================
PHASE 4 — SI LE TRAVAIL EST RETROUVÉ
==================================================

NE FAIS AUCUNE SYNCHRONISATION AUTOMATIQUE.

Ne fais pas :

git reset
git checkout
git restore
git clean
git cherry-pick
git merge
git rebase

tant que je ne t'ai pas donné explicitement l'autorisation.

Nous déciderons ensuite de la méthode de récupération la plus sûre.

==================================================
PHASE 5 — SI LE TRAVAIL N'EST PAS RETROUVÉ
==================================================

Ne réimplémente toujours rien.

Indique clairement :

SPR-014 ARTIFACTS NOT FOUND LOCALLY

Puis donne :

- recherches effectuées
- chemins examinés
- branches trouvées
- worktrees trouvés
- commits pertinents trouvés ou non
- reflog pertinent ou non
- conclusion

==================================================
CONTRAINTE ABSOLUE
==================================================

Cette session est UNIQUEMENT une session de récupération/synchronisation.

Aucun changement de code.

Aucun changement documentaire.

Aucun commit.

Aucune restauration destructive.

Aucun reset.

Aucun cherry-pick.

Aucun merge.

Aucun rebase.

Nous devons d'abord retrouver le véritable état SPR-014.

==================================================
RÉSULTAT FINAL
==================================================

# SPR-014 SYNCHRONIZATION STATUS

Current workspace:
HEAD:
Working tree:

## SPR-014 artifacts found
- ...

## Alternative workspaces
- ...

## Git branches
- ...

## Relevant commits
- ...

## Reflog findings
- ...

## Recovery candidate
- ...

## Changes made
NONE

## Next safe action
...

COMMENCE PAR LA RECHERCHE LOCALE ET GIT.
NE MODIFIE RIEN.
