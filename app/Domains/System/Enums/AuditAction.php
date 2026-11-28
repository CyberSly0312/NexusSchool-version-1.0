<?php

declare(strict_types=1);

namespace App\Domains\System\Enums;

enum AuditAction: string
{
    case Created = 'created';
    case Updated = 'updated';
    case Deleted = 'deleted';
    case Restored = 'restored';
    case Viewed = 'viewed';
    case Exported = 'exported';
    case Login = 'login';
    case Logout = 'logout';
    case FailedLogin = 'failed_login';
    case Provisioned = 'provisioned';
    case ProvisioningRequested = 'provisioning_requested';
    case ProvisioningBlocked = 'provisioning_blocked';
    case InvitationReused = 'invitation_reused';
    case InvitationReissued = 'invitation_reissued';
    case SetupLinkIssued = 'setup_link_issued';
    case PasswordSet = 'password_set';
    case RoleAssigned = 'role_assigned';
    case RoleRemoved = 'role_removed';
    case RoleChangeRejected = 'role_change_rejected';
    case PasswordChanged = 'password_changed';
    case PasswordResetRequested = 'password_reset_requested';
    case PasswordResetCompleted = 'password_reset_completed';
    case PasswordResetRejected = 'password_reset_rejected';
    case AccessSuspended = 'access_suspended';
    case SchoolAdminReplaced = 'school_admin_replaced';
    case SensitiveOperationRejected = 'sensitive_operation_rejected';
    case GuardianRelationshipRequested = 'guardian_relationship_requested';
    case GuardianRelationshipVerified = 'guardian_relationship_verified';
    case GuardianRelationshipRejected = 'guardian_relationship_rejected';
    case GuardianRelationshipRevoked = 'guardian_relationship_revoked';
    case GuardianChildAccessDenied = 'guardian_child_access_denied';
    case GuardianChildViewed = 'guardian_child_viewed';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Créé',
            self::Updated => 'Mis à jour',
            self::Deleted => 'Supprimé',
            self::Restored => 'Restauré',
            self::Viewed => 'Consulté',
            self::Exported => 'Exporté',
            self::Login => 'Connexion',
            self::Logout => 'Déconnexion',
            self::FailedLogin => 'Échec de connexion',
            self::Provisioned => 'Provisionné',
            self::ProvisioningRequested => 'Provisionnement demandé',
            self::ProvisioningBlocked => 'Provisionnement bloqué',
            self::InvitationReused => 'Invitation réutilisée',
            self::InvitationReissued => 'Invitation réémise',
            self::SetupLinkIssued => 'Lien de configuration émis',
            self::PasswordSet => 'Mot de passe défini',
            self::RoleAssigned => 'Rôle attribué',
            self::RoleRemoved => 'Rôle retiré',
            self::RoleChangeRejected => 'Modification de rôle refusée',
            self::PasswordChanged => 'Mot de passe modifié',
            self::PasswordResetRequested => 'Réinitialisation du mot de passe demandée',
            self::PasswordResetCompleted => 'Réinitialisation du mot de passe terminée',
            self::PasswordResetRejected => 'Réinitialisation du mot de passe refusée',
            self::AccessSuspended => 'Accès suspendu',
            self::SchoolAdminReplaced => 'Administrateur d’établissement remplacé',
            self::SensitiveOperationRejected => 'Opération sensible refusée',
            self::GuardianRelationshipRequested => 'Lien tuteur–élève demandé',
            self::GuardianRelationshipVerified => 'Lien tuteur–élève vérifié',
            self::GuardianRelationshipRejected => 'Lien tuteur–élève refusé',
            self::GuardianRelationshipRevoked => 'Lien tuteur–élève révoqué',
            self::GuardianChildAccessDenied => 'Accès à un élève refusé',
            self::GuardianChildViewed => 'Fiche élève consultée par un tuteur',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Created => 'success',
            self::Updated => 'info',
            self::Deleted => 'danger',
            self::Restored => 'warning',
            self::Viewed => 'gray',
            self::Exported => 'secondary',
            self::Login => 'success',
            self::Logout => 'secondary',
            self::FailedLogin => 'danger',
            self::Provisioned => 'success',
            self::ProvisioningRequested => 'info',
            self::ProvisioningBlocked => 'danger',
            self::InvitationReused => 'success',
            self::InvitationReissued => 'warning',
            self::SetupLinkIssued => 'warning',
            self::PasswordSet => 'info',
            self::RoleAssigned => 'success',
            self::RoleRemoved => 'warning',
            self::RoleChangeRejected => 'danger',
            self::PasswordChanged => 'info',
            self::PasswordResetRequested => 'warning',
            self::PasswordResetCompleted => 'success',
            self::PasswordResetRejected => 'danger',
            self::AccessSuspended => 'danger',
            self::SchoolAdminReplaced => 'success',
            self::SensitiveOperationRejected => 'danger',
            self::GuardianRelationshipRequested => 'info',
            self::GuardianRelationshipVerified => 'success',
            self::GuardianRelationshipRejected => 'danger',
            self::GuardianRelationshipRevoked => 'warning',
            self::GuardianChildAccessDenied => 'danger',
            self::GuardianChildViewed => 'info',
        };
    }

    public static function labels(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases()),
        );
    }
}
