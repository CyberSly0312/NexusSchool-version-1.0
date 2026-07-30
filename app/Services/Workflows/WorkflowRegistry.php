<?php

declare(strict_types=1);

namespace App\Services\Workflows;

/**
 * Workflow Registry — Architecture préparatoire.
 *
 * Définit les étapes et acteurs de chaque workflow métier.
 * Ce registre n'exécute pas encore les workflows — il documente l'architecture
 * pour guider le développement futur.
 *
 * Workflows prévus :
 *  - Inscription (Secretary → Director → Accountant → Admin)
 *  - Paiement (Accountant → Director validation)
 *  - Bulletin (Teacher → Director validation)
 *  - Présence (Teacher → Director review)
 *  - Communication (Secretary → Director approval)
 */
class WorkflowRegistry
{
    public const STATUSES = [
        'draft' => 'Brouillon',
        'pending_validation' => 'En attente de validation',
        'validated' => 'Validé',
        'rejected' => 'Rejeté',
        'completed' => 'Terminé',
    ];

    public static function getWorkflows(): array
    {
        return [
            'enrollment' => self::enrollmentWorkflow(),
            'payment' => self::paymentWorkflow(),
            'bulletin' => self::bulletinWorkflow(),
            'attendance' => self::attendanceWorkflow(),
            'communication' => self::communicationWorkflow(),
        ];
    }

    protected static function enrollmentWorkflow(): array
    {
        return [
            'name' => 'Inscription',
            'model' => 'StudentEnrollment',
            'steps' => [
                1 => [
                    'action' => 'Créer l\'inscription',
                    'actor' => 'secretary',
                    'panel' => 'secretary',
                    'description' => 'La Secrétaire crée l\'inscription avec les informations de l\'élève et du parent.',
                ],
                2 => [
                    'action' => 'Valider l\'inscription',
                    'actor' => 'director',
                    'panel' => 'director',
                    'description' => 'Le Directeur valide ou rejette l\'inscription.',
                ],
                3 => [
                    'action' => 'Enregistrer le paiement',
                    'actor' => 'accountant',
                    'panel' => 'accountant',
                    'description' => 'Le Comptable enregistre les frais d\'inscription.',
                ],
                4 => [
                    'action' => 'Affecter la classe',
                    'actor' => 'school_admin',
                    'panel' => 'school-admin',
                    'description' => 'L\'Administrateur affecte l\'élève à une classe.',
                ],
                5 => [
                    'action' => 'Visible par l\'enseignant',
                    'actor' => 'teacher',
                    'panel' => 'teacher',
                    'description' => 'L\'élève apparaît dans la liste de l\'enseignant.',
                ],
            ],
        ];
    }

    protected static function paymentWorkflow(): array
    {
        return [
            'name' => 'Paiement',
            'model' => 'Payment',
            'steps' => [
                1 => [
                    'action' => 'Initier le paiement',
                    'actor' => 'accountant',
                    'panel' => 'accountant',
                    'description' => 'Le Comptable crée le paiement et enregistre la réception.',
                ],
                2 => [
                    'action' => 'Valider le paiement',
                    'actor' => 'director',
                    'panel' => 'director',
                    'description' => 'Le Directeur valide le paiement reçu.',
                ],
                3 => [
                    'action' => 'Générer le reçu',
                    'actor' => 'system',
                    'panel' => null,
                    'description' => 'Le système génère automatiquement le reçu PDF.',
                ],
            ],
        ];
    }

    protected static function bulletinWorkflow(): array
    {
        return [
            'name' => 'Bulletin',
            'model' => 'AssessmentResult',
            'steps' => [
                1 => [
                    'action' => 'Saisir les résultats',
                    'actor' => 'teacher',
                    'panel' => 'teacher',
                    'description' => 'L\'Enseignant saisit les notes des élèves.',
                ],
                2 => [
                    'action' => 'Valider les résultats',
                    'actor' => 'director',
                    'panel' => 'director',
                    'description' => 'Le Directeur valide les résultats avant publication.',
                ],
                3 => [
                    'action' => 'Générer les bulletins',
                    'actor' => 'system',
                    'panel' => null,
                    'description' => 'Le système génère les bulletins PDF.',
                ],
                4 => [
                    'action' => 'Publier',
                    'actor' => 'secretary',
                    'panel' => 'secretary',
                    'description' => 'La Secrétaire publie les bulletins pour les parents.',
                ],
            ],
        ];
    }

    protected static function attendanceWorkflow(): array
    {
        return [
            'name' => 'Présence',
            'model' => 'AttendanceRecord',
            'steps' => [
                1 => [
                    'action' => 'Enregistrer la présence',
                    'actor' => 'teacher',
                    'panel' => 'teacher',
                    'description' => 'L\'Enseignant enregistre les présences quotidiennes.',
                ],
                2 => [
                    'action' => 'Revoir les absences',
                    'actor' => 'director',
                    'panel' => 'director',
                    'description' => 'Le Directeur consulte les rapports d\'absence.',
                ],
                3 => [
                    'action' => 'Notifier les parents',
                    'actor' => 'system',
                    'panel' => null,
                    'description' => 'Le système notifie les parents en cas d\'absence.',
                ],
            ],
        ];
    }

    protected static function communicationWorkflow(): array
    {
        return [
            'name' => 'Communication',
            'model' => 'Announcement',
            'steps' => [
                1 => [
                    'action' => 'Rédiger l\'annonce',
                    'actor' => 'secretary',
                    'panel' => 'secretary',
                    'description' => 'La Secrétaire rédige l\'annonce ou le message.',
                ],
                2 => [
                    'action' => 'Approuver',
                    'actor' => 'director',
                    'panel' => 'director',
                    'description' => 'Le Directeur approuve l\'annonce avant diffusion.',
                ],
                3 => [
                    'action' => 'Diffuser',
                    'actor' => 'system',
                    'panel' => null,
                    'description' => 'Le système diffuse l\'annonce aux destinataires.',
                ],
            ],
        ];
    }
}
