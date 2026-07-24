<?php

declare(strict_types=1);

namespace App\Domains\Document\Enums;

enum DocumentType: string
{
    case SchoolCertificate = 'school_certificate';
    case EnrollmentAttestation = 'enrollment_attestation';
    case AttendanceAttestation = 'attendance_attestation';
    case ReportCard = 'report_card';
    case Transcript = 'transcript';
    case SchoolId = 'school_id';
    case EndOfStudyCertificate = 'end_of_study_certificate';
    case TransferLetter = 'transfer_letter';
    case CustomCertificate = 'custom_certificate';

    public function label(): string
    {
        return match ($this) {
            self::SchoolCertificate => 'Certificat de scolarité',
            self::EnrollmentAttestation => 'Attestation d\'inscription',
            self::AttendanceAttestation => 'Attestation de fréquentation',
            self::ReportCard => 'Bulletin scolaire',
            self::Transcript => 'Relevé de notes',
            self::SchoolId => 'Carte d\'identité scolaire',
            self::EndOfStudyCertificate => 'Certificat de fin d\'études',
            self::TransferLetter => 'Lettre de transfert',
            self::CustomCertificate => 'Certificat personnalisé',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::SchoolCertificate => 'heroicon-o-document-check',
            self::EnrollmentAttestation => 'heroicon-o-document-text',
            self::AttendanceAttestation => 'heroicon-o-calendar',
            self::ReportCard => 'heroicon-o-academic-cap',
            self::Transcript => 'heroicon-o-document-duplicate',
            self::SchoolId => 'heroicon-o-identification',
            self::EndOfStudyCertificate => 'heroicon-o-trophy',
            self::TransferLetter => 'heroicon-o-paper-airplane',
            self::CustomCertificate => 'heroicon-o-document',
        };
    }
}
