# Academic Module
# Laravel Implementation
## Form Requests

Version : 1.0

---

# Objectif

Ce document décrit les conventions d'utilisation des Form Requests dans le module Academic.

Les Form Requests sont responsables de :

- l'autorisation des requêtes ;
- la validation des données entrantes ;
- la normalisation des données avant leur traitement.

Ils constituent la première ligne de défense contre les données invalides.

---

# Organisation

Les Form Requests sont regroupés dans :

```
app/
└── Domain/
    └── Academic/
        └── Requests/
```

Exemples :

```
CreateAcademicYearRequest.php

UpdateAcademicYearRequest.php

CreateAcademicTermRequest.php

UpdateAcademicTermRequest.php

CreateProgramRequest.php

UpdateProgramRequest.php

CreateClassroomRequest.php

UpdateClassroomRequest.php

CreateSubjectRequest.php

UpdateSubjectRequest.php

CreateEnrollmentRequest.php

UpdateEnrollmentRequest.php
```

---

# Responsabilités

Chaque Form Request doit uniquement :

- autoriser ou refuser une requête ;
- valider les données ;
- préparer les données si nécessaire.

Les règles métier restent dans les Services.

---

# Méthodes principales

Chaque Request implémente :

```php
authorize()

rules()
```

Peut également utiliser :

```php
messages()

attributes()

prepareForValidation()

passedValidation()
```

---

# Autorisation

L'autorisation utilise les Policies.

Exemple :

```php
public function authorize(): bool
{
    return $this->user()
        ->can('academic.create');
}
```

---

# Validation

Les règles utilisent :

- Validation Rules Laravel
- Enums
- Rules personnalisées

Exemple :

```php
public function rules(): array
{
    return [

        'name' => ['required','string','max:255'],

        'code' => ['required','string'],

        'status' => [
            'required',
            new Enum(AcademicYearStatus::class),
        ],

    ];
}
```

---

# Messages

Les messages personnalisés sont centralisés.

Exemple :

```php
public function messages(): array
{
    return [

        'name.required' => 'Le nom est obligatoire.',

    ];
}
```

---

# Attributs

Les noms affichés peuvent être personnalisés.

Exemple :

```php
public function attributes(): array
{
    return [

        'academic_year_id' => 'année académique',

    ];
}
```

---

# Préparation des données

Les données peuvent être normalisées avant validation.

Exemple :

```php
protected function prepareForValidation()
{
    $this->merge([

        'code' => strtoupper($this->code),

    ]);
}
```

---

# Validation après succès

Utiliser :

```php
passedValidation()
```

pour préparer les données destinées aux Services.

Aucune logique métier ne doit être exécutée ici.

---

# Réutilisation

Une Request est créée pour chaque cas d'utilisation.

Éviter :

```
AcademicRequest.php
```

Préférer :

```
CreateAcademicYearRequest

UpdateAcademicYearRequest
```

---

# Gestion des erreurs

Les erreurs de validation retournent automatiquement :

- HTTP 422 pour les API ;
- messages utilisateur dans Filament ;
- redirection dans les interfaces Web.

---

# Tests

Chaque Request doit être testée pour :

- autorisation ;
- validation ;
- messages ;
- règles personnalisées ;
- Enums ;
- données invalides.

---

# Bonnes pratiques

- Une responsabilité par Request.
- Aucune logique métier.
- Validation explicite.
- Utilisation des Enums.
- Utilisation des Rules personnalisées lorsque nécessaire.
- Réutilisation entre Web et API.

---

# Résumé

Les Form Requests assurent la validation et l'autorisation des données entrantes avant leur traitement.

Ils garantissent une séparation claire entre la validation, les règles métier et la persistance des données, conformément aux bonnes pratiques Laravel.