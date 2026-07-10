# Academic Calendar Engine

## Module

Academic

---

# Purpose

Le `AcademicCalendarEngine` est responsable de la génération, de la validation et du contrôle du calendrier académique d'un établissement.

Il garantit la cohérence des périodes académiques (années, semestres, trimestres, vacances, examens) afin qu'aucun chevauchement ou incohérence temporelle ne soit possible.

Toutes les fonctionnalités du système utilisant une date académique s'appuient sur ce moteur.

---

# Responsibilities

Le moteur est chargé de :

- Générer un calendrier académique.
- Vérifier la continuité des périodes.
- Détecter les chevauchements.
- Valider les dates limites.
- Calculer les jours ouvrables.
- Identifier la période académique correspondant à une date.
- Vérifier si une période est ouverte ou fermée.

---

# Inputs

## Academic Year

- start_date
- end_date

## Academic Terms

- start_date
- end_date

## Holidays

- national holidays
- school holidays

## Weekend Configuration

- Saturday
- Sunday

---

# Outputs

Le moteur fournit notamment :

- calendrier validé
- liste des jours ouvrables
- période académique active
- erreurs de validation
- alertes de conflit

---

# Core Operations

## Generate Calendar

Construit automatiquement le calendrier académique à partir des paramètres définis.

---

## Validate Calendar

Vérifie :

- chevauchement des périodes
- dates invalides
- périodes manquantes
- ordre chronologique

---

## Get Current Academic Period

Retourne :

- année académique active
- semestre actif
- trimestre actif

selon la date courante.

---

## Calculate Working Days

Calcule :

- nombre de jours ouvrables
- exclusion des week-ends
- exclusion des vacances
- exclusion des jours fériés

---

## Is Academic Day

Retourne :

- TRUE
- FALSE

selon que la date est un jour académique.

---

## Get Remaining Days

Retourne le nombre de jours restants avant :

- fin du trimestre
- fin du semestre
- fin de l'année

---

# Business Rules

## BR-001

Une année académique ne peut jamais chevaucher une autre.

---

## BR-002

Chaque terme doit appartenir à une seule année académique.

---

## BR-003

La date de début doit toujours être antérieure à la date de fin.

---

## BR-004

Les périodes académiques doivent être continues.

---

## BR-005

Une seule année académique peut être active simultanément.

---

## BR-006

Les jours fériés ne sont jamais considérés comme des jours ouvrables.

---

# Dependencies

Le moteur utilise :

- AcademicYear
- AcademicTerm
- Holiday
- AcademicSettings

---

# Used By

- Enrollment
- Attendance
- Timetable
- Examination
- Finance
- Reports

---

# Events Triggered

- AcademicCalendarGenerated
- AcademicCalendarValidated

---

# Possible Exceptions

- InvalidAcademicDatesException
- AcademicOverlapException
- MissingAcademicTermException
- CalendarGenerationException

---

# Performance Considerations

Le moteur doit être capable de traiter plusieurs années académiques sans recalcul complet en utilisant un mécanisme de cache lorsque les données ne changent pas.

---

# Future Extensions

- Calendrier multi-campus
- Calendrier par programme
- Calendrier universitaire
- Calendrier personnalisé par filière