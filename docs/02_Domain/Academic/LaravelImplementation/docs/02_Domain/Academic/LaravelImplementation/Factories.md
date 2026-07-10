# Academic Module
# Laravel Implementation
## Factories

Version : 1.0

---

# Objectif

Ce document décrit les conventions d'implémentation des Factories du module Academic.

Les Factories permettent de générer des données de test réalistes, cohérentes et reproductibles.

Elles sont utilisées pour :

- les tests unitaires ;
- les Feature Tests ;
- les Seeders ;
- les démonstrations ;
- le développement local.

---

# Organisation

Toutes les Factories sont stockées dans :

```
database/
└── factories/
```

Le module Academic contient notamment :

```
AcademicYearFactory.php

AcademicTermFactory.php

ProgramFactory.php

ClassroomFactory.php

SubjectFactory.php

EnrollmentFactory.php
```

---

# Héritage

Toutes les Factories héritent de :

```php
Illuminate\Database\Eloquent\Factories\Factory
```

---

# Génération des données

Les données sont générées avec Faker.

Exemples :

```php
fake()->word()

fake()->sentence()

fake()->boolean()

fake()->date()

fake()->randomElement()
```

Les données doivent être crédibles et représentatives du contexte scolaire.

---

# UUID

Les identifiants sont générés automatiquement grâce au trait :

```php
HasUuids
```

Les Factories ne doivent jamais générer manuellement les UUID.

---

# Relations

Les relations utilisent également des Factories.

Exemple :

```php
AcademicTerm::factory()

Subject::factory()

Program::factory()
```

Éviter l'utilisation d'identifiants codés en dur.

---

# États (States)

Les Factories utilisent des States pour représenter différents scénarios.

Exemple :

```php
AcademicYearFactory

->active()

->closed()

->archived()
```

Autres exemples :

```php
EnrollmentFactory

->pending()

->active()

->graduated()
```

---

# Séquences

Lorsque plusieurs enregistrements doivent être créés avec des valeurs différentes, utiliser les Sequences.

Exemple :

```php
Sequence(...)
```

---

# afterMaking()

Utilisé pour préparer un objet avant son enregistrement.

Exemple :

```php
afterMaking(function (...) {

});
```

---

# afterCreating()

Utilisé lorsque des relations doivent être créées automatiquement.

Exemple :

```php
afterCreating(function (...) {

});
```

Cas d'utilisation :

- créer les périodes après une année académique ;
- créer les matières d'un programme ;
- créer les inscriptions d'une classe.

---

# Données réalistes

Les Factories doivent produire :

- des codes valides ;
- des dates cohérentes ;
- des statuts réalistes ;
- des relations correctes.

Éviter les valeurs aléatoires incohérentes.

---

# Réutilisation

Les Factories doivent rester génériques.

Les cas particuliers sont gérés par des States.

Éviter :

```php
AcademicYearFactory2026
```

Préférer :

```php
AcademicYearFactory

->active()
```

---

# Tests

Les Factories doivent être testées pour vérifier :

- la création des objets ;
- les relations ;
- les States ;
- les callbacks ;
- les contraintes métier.

---

# Bonnes pratiques

- Une Factory par modèle.
- Utiliser des States plutôt que du code conditionnel.
- Produire des données réalistes.
- Réutiliser les Factories dans tous les tests.
- Éviter les dépendances circulaires.

---

# Résumé

Les Factories du module Academic permettent de générer des données fiables, cohérentes et facilement réutilisables.

Elles constituent la base des tests automatisés, des Seeders et des environnements de démonstration de Nexus School.