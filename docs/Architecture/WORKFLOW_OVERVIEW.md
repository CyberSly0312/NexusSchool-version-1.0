# Vue d'ensemble des Workflows — NexusSchool

## Introduction

Chaque processus métier dans NexusSchool suit un workflow défini avec des étapes claires et des acteurs identifiés. L'architecture des workflows est préparée dans `App\Services\Workflows\WorkflowRegistry`.

## Statuts d'un workflow

| Statut | Description |
|--------|-------------|
| `draft` | Brouillon, en cours de rédaction |
| `pending_validation` | En attente de validation |
| `validated` | Validé par l'acteur responsable |
| `rejected` | Rejeté, nécessite des corrections |
| `completed` | Terminé avec succès |

---

## Workflow 1 : Inscription

```
Nouvelle inscription
        │
        ▼
┌─────────────────────┐
│  Secrétaire          │  Crée l'inscription
│  (secretary panel)   │  Informations élève + parent
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│  Directeur           │  Valide ou rejette
│  (director panel)    │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│  Comptable           │  Enregistre le paiement
│  (accountant panel)  │  des frais d'inscription
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│  Administrateur      │  Affecte la classe
│  (school-admin)      │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│  Enseignant          │  L'élève apparaît
│  (teacher panel)     │  dans la liste
└─────────────────────┘
```

---

## Workflow 2 : Paiement

```
Paiement reçu
        │
        ▼
┌─────────────────────┐
│  Comptable           │  Initie le paiement
│  (accountant panel)  │  Enregistre la réception
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│  Directeur           │  Valide le paiement
│  (director panel)    │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│  Système             │  Génère le reçu PDF
│  (automatique)       │
└─────────────────────┘
```

---

## Workflow 3 : Bulletin

```
Fin de période
        │
        ▼
┌─────────────────────┐
│  Enseignant          │  Saisit les notes
│  (teacher panel)     │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│  Directeur           │  Valide les résultats
│  (director panel)    │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│  Système             │  Génère les bulletins PDF
│  (automatique)       │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│  Secrétaire          │  Publie pour les parents
│  (secretary panel)   │
└─────────────────────┘
```

---

## Workflow 4 : Présence

```
Quotidien
        │
        ▼
┌─────────────────────┐
│  Enseignant          │  Enregistre les présences
│  (teacher panel)     │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│  Directeur           │  Revoit les absences
│  (director panel)    │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│  Système             │  Notifie les parents
│  (automatique)       │  en cas d'absence
└─────────────────────┘
```

---

## Workflow 5 : Communication

```
Nouvelle annonce
        │
        ▼
┌─────────────────────┐
│  Secrétaire          │  Rédige l'annonce
│  (secretary panel)   │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│  Directeur           │  Approuve l'annonce
│  (director panel)    │
└─────────┬───────────┘
          │
          ▼
┌─────────────────────┐
│  Système             │  Diffuse aux destinataires
│  (automatique)       │
└─────────────────────┘
```

---

## Architecture technique

### Registre des workflows

Le fichier `App\Services\Workflows\WorkflowRegistry` définit :

- La liste de tous les workflows
- Les étapes de chaque workflow
- Les acteurs responsables de chaque étape
- Les panels associés

### Implémentation future

Chaque workflow sera implémenté via :

1. **States** sur le modèle (enum `WorkflowStatus`)
2. **Actions Filament** pour les transitions d'état
3. **Notifications** pour les acteurs suivants
4. **Historique** via `ActivityLog` pour l'audit trail
