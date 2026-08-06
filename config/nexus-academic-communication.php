<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Infrastructure\Channels\InternalPortalChannel;
use App\Domains\AcademicCommunication\Infrastructure\Channels\ParentPortalChannel;
use App\Domains\AcademicCommunication\Infrastructure\Channels\PdfExportChannel;
use App\Domains\AcademicCommunication\Infrastructure\Channels\StudentPortalChannel;
use App\Domains\AcademicCommunication\Infrastructure\Channels\TeacherPortalChannel;
use App\Domains\AcademicCommunication\Infrastructure\Renderers\HtmlDocumentRenderer;

return [

    /**
     * Platform token embedded in the permanent document identity
     * (e.g. NS-2027-SCH001-RC-000000145).
     */
    'platform' => env('NEXUS_DOCUMENT_PLATFORM', 'NS'),

    /**
     * Storage disk used for the official immutable PDF files and pdf_export
     * deliveries.
     */
    'disk' => env('NEXUS_DOCUMENT_DISK', 'local'),

    /**
     * Default HTML renderer. Rendering is Infrastructure — swap this binding to
     * use a different rendering engine without touching the domain.
     */
    'renderer' => HtmlDocumentRenderer::class,

    /**
     * Publication channel drivers, keyed by DocumentChannel value.
     *
     * A channel is plugged in by implementing the PublicationChannelContract,
     * declaring its driver here and enabling it in the school's
     * PublicationChannel. Future channels (email, sms, push_notification) are
     * declared in the DocumentChannel enum but intentionally have no driver
     * yet — the dispatcher refuses them through DocumentChannel::isImplemented().
     */
    'channels' => [
        DocumentChannel::InternalPortal->value => InternalPortalChannel::class,
        DocumentChannel::ParentPortal->value => ParentPortalChannel::class,
        DocumentChannel::TeacherPortal->value => TeacherPortalChannel::class,
        DocumentChannel::StudentPortal->value => StudentPortalChannel::class,
        DocumentChannel::PdfExport->value => PdfExportChannel::class,
    ],
];
