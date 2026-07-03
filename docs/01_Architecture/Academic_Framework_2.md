# NEXUS SCHOOL

# Academic Framework Engine

**Document Version :** 1.0.0

**Module :** DEV-02

**Status :** Draft

---

# 1. Introduction

## 1.1 Purpose

L'Academic Framework Engine constitue le cœur du système Nexus School.

Il définit l'ensemble des règles métier permettant d'organiser une structure académique complète, indépendamment du pays, du type d'établissement ou du calendrier scolaire utilisé.

Tous les autres modules du système reposent sur ce moteur :

* Student Management
* Teacher Management
* Timetable
* Attendance
* Examination
* Report Cards
* Finance
* Library
* Transport
* Mobile Application
* AI Assistant

Une erreur de conception dans ce module aurait un impact sur l'ensemble du système. C'est pourquoi ce document décrit les concepts métier avant toute implémentation technique.

---

# 2. Design Principles

Le moteur académique est conçu selon les principes suivants.

## 2.1 Multi-Tenant First

Chaque donnée académique appartient obligatoirement à une école.

Aucune information académique ne doit être partagée entre plusieurs établissements.

---

## 2.2 Configuration over Hard Coding

Le système ne doit jamais contenir de règles académiques codées en dur.

Les niveaux, séries, périodes, programmes et matières doivent être configurables.

---

## 2.3 Country Agnostic

Le moteur doit être capable de fonctionner dans différents systèmes éducatifs.

Exemples :

* Burkina Faso
* Ghana
* Côte d'Ivoire
* Sénégal
* Bénin
* Togo
* Niger

Sans modification du code source.

---

## 2.4 Extensible

Le moteur doit pouvoir évoluer vers :

* Universités
* Centres de formation
* Instituts techniques
* Écoles professionnelles

sans refonte de la base de données.

---

# 3. Academic Hierarchy

Chaque établissement est organisé selon la hiérarchie suivante :

School

↓

Academic Configuration

↓

Academic Year

↓

Academic Calendar

↓

Academic Term

↓

Program

↓

Level

↓

Stream (Series)

↓

Class

↓

Section

↓

Curriculum

↓

Subject

---

# 4. Business Concepts

## School

Représente un établissement scolaire.

Chaque école possède :

* ses utilisateurs
* ses années académiques
* ses classes
* ses matières
* ses enseignants
* ses élèves

---

## Academic Configuration

Contient les paramètres académiques propres à une école.

Exemples :

* système trimestriel
* système semestriel
* système annuel
* notation sur 20
* notation sur 100
* moyenne minimale
* nombre maximal d'absences

Cette configuration permet d'adapter Nexus School à différents pays sans modifier le code.

---

## Academic Year

Représente une année scolaire.

Exemple :

2026 - 2027

Une seule année académique peut être active à un instant donné.

---

## Academic Calendar

Décrit l'organisation générale de l'année.

Exemples :

* Trimestriel
* Semestriel
* Quadrimestriel
* Personnalisé

---

## Academic Term

Représente une période d'enseignement.

Exemple :

Premier trimestre

Deuxième trimestre

Troisième trimestre

Chaque période possède :

* date de début
* date de fin
* statut

---

## Program

Décrit un programme d'enseignement.

Exemples :

Enseignement Général

Enseignement Technique

Enseignement Professionnel

---

## Level

Correspond au niveau pédagogique.

Exemples :

6ème

5ème

4ème

3ème

Seconde

Première

Terminale

---

## Stream (Series)

Permet de spécialiser un niveau.

Exemples :

A

C

D

E

F

Commerciale

Scientifique

Industrielle

---

## Class

Représente une classe réelle.

Exemple :

Terminale D1

Terminale D2

Terminale D3

Chaque classe appartient à :

* une année académique
* un programme
* un niveau
* une série

---

## Section

Découpe optionnelle d'une classe.

Exemples :

Morning

Evening

Blue

Green

Section A

Section B

---

## Curriculum

Le Curriculum définit le contenu pédagogique officiel d'une classe.

Il contient :

* les matières
* les coefficients
* les volumes horaires
* les matières obligatoires
* les matières optionnelles

Le Curriculum est réutilisable.

---

## Subject

Représente une matière.

Exemples :

Mathématiques

Physique

SVT

Français

Anglais

Informatique

Une matière ne possède aucun coefficient.

Le coefficient est défini dans le Curriculum.

---

## Subject Group

Permet de regrouper plusieurs matières.

Exemples :

Sciences

Langues

Humanités

Technologie

---

## Class Subject

Table de liaison entre :

Classe

↓

Curriculum

↓

Matière

Elle définit :

* coefficient
* nombre d'heures
* ordre d'affichage
* obligatoire ou optionnelle

---

## Grading System

Décrit le système de notation.

Exemples :

Notation sur 20

Notation sur 100

Notation par lettres

Notation GPA

---

## Grading Scale

Décrit les plages de notes.

Exemple :

16 - 20 → Excellent

14 - 15.99 → Très Bien

12 - 13.99 → Bien

10 - 11.99 → Passable

0 - 9.99 → Échec

---

# 5. Business Rules

Une seule année académique peut être active.

Une classe appartient toujours à une seule année académique.

Une matière peut appartenir à plusieurs curriculums.

Les coefficients sont définis par Curriculum.

Une Section est facultative.

Les Programmes peuvent être activés ou désactivés.

Chaque école possède sa propre configuration académique.

Les modifications d'un Curriculum ne doivent jamais modifier les résultats des années précédentes.

Toutes les opérations importantes doivent être enregistrées dans les Audit Logs.

---

# 6. Future Extensions

Le moteur est conçu pour accueillir :

* Universités
* Crédits académiques
* ECTS
* Double diplôme
* Formation à distance
* LMS
* IA pédagogique
* Marketplace de contenus
* Certification numérique

---

# 7. Definition of Done

Le module DEV-02 sera considéré comme terminé lorsque :

✓ Toutes les entités métier seront définies.

✓ Les relations entre les concepts seront validées.

✓ Les règles métier seront documentées.

✓ Le modèle logique sera approuvé.

✓ Les migrations Laravel pourront être générées sans ambiguïté.

---

**Fin du document - Version 1.0.0**
