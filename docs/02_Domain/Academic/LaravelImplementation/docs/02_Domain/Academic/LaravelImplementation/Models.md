# Academic Module
# Laravel Implementation
## Models

Version : 1.0

---

# Objectif

Ce document décrit l'implémentation des modèles Eloquent du module Academic.

Les modèles représentent les entités métier du système et assurent l'interaction avec la base de données via Laravel Eloquent.

Ils doivent rester légers et ne contenir que les comportements directement liés à leur propre état.

---

# Modèles du module

Le module Academic contient les modèles suivants :

- AcademicYear
- AcademicTerm
- Program
- Classroom
- Subject
- Enrollment

Chaque modèle correspond à une table principale de la base de données.

---

# Organisation

Les modèles sont regroupés dans :

```

app/
└── Domain/
└── Academic/
└── Models/
├── AcademicYear.php
├── AcademicTerm.php
├── Program.php
├── Classroom.php
├── Subject.php
└── Enrollment.php

```

---

# Héritage

Tous les modèles héritent de :

```php
Illuminate\Database\Eloquent\Model
```

Les modèles utilisant les suppressions logiques utilisent également :

```php
SoftDeletes
```

---

# Traits recommandés

Tous les modèles devraient utiliser les traits suivants lorsque nécessaire :

```php
HasFactory
SoftDeletes
HasUuids
LogsActivity
```

Description

| Trait | Utilité |
|--------|----------|
| HasFactory | Génération de données de test |
| SoftDeletes | Suppression logique |
| HasUuids | Clés primaires UUID |
| LogsActivity | Historique des modifications |

---

# Attributs protégés

Chaque modèle doit définir :

```php
protected $fillable = [];
```

ou

```php
protected $guarded = [];
```

La préférence du projet est :

```
fillable
```

pour contrôler explicitement les champs modifiables.

---

# Casts

Les propriétés doivent être typées grâce aux casts Eloquent.

Exemple :

```php
protected function casts(): array
{
    return [
        'status' => AcademicYearStatus::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];
}
```

---

# Relations

Les relations doivent être définies directement dans les modèles.

Exemples :

```php
belongsTo()
hasMany()
belongsToMany()
hasOne()
```

Les relations sont documentées dans le dossier Relationships.

---

# Scopes

Les modèles peuvent exposer des scopes métier.

Exemples

```php
scopeActive()

scopeCurrent()

scopeArchived()

scopeBySchool()

scopeByAcademicYear()
```

Les scopes facilitent la lecture des requêtes.

---

# Accesseurs

Les Accessors servent uniquement à présenter les données.

Exemple :

```php
getDisplayNameAttribute()
```

Ils ne doivent pas contenir de logique métier.

---

# Mutateurs

Les Mutators servent à préparer les données avant leur sauvegarde.

Exemple :

```php
setCodeAttribute()
```

---

# Événements Eloquent

Les modèles peuvent écouter :

- creating
- created
- updating
- updated
- deleting
- deleted
- restoring
- restored

Les traitements complexes doivent être déplacés vers des Observers.

---

# Bonnes pratiques

Les modèles ne doivent pas :

- envoyer des emails ;
- créer des notifications ;
- exécuter des règles métier complexes ;
- effectuer des appels HTTP ;
- lancer des traitements longs.

Ces responsabilités appartiennent aux Services.

---

# Tests

Chaque modèle doit être testé pour :

- création ;
- suppression ;
- relations ;
- casts ;
- scopes ;
- accessors ;
- mutators ;
- Soft Deletes.

---

# Résumé

Les modèles Eloquent représentent les entités métier du module Academic.

Ils assurent la persistance des données, définissent les relations entre les entités et exposent uniquement les comportements directement liés à leur état, tout en laissant la logique métier aux Services.