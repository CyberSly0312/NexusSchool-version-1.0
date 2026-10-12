<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

/**
 * The Identity & Verification engine references foreign domains only through
 * the contracts declared in Domain/Contracts and implemented by the adapters
 * living in Infrastructure/Contracts. Domain and Application layers stay pure:
 * they may never import a model, enum or service from another domain
 * (ADR-022 C1 / I10).
 */
test('the IdentityAndVerification Domain and Application layers never import foreign domain classes', function (): void {
    $base = app_path('Domains/IdentityAndVerification');

    $files = array_merge(
        File::allFiles($base.'/Domain'),
        File::allFiles($base.'/Application'),
    );

    expect($files)->not->toBeEmpty();

    $pattern = '/use\s+App\\Domains\\\\(?!IdentityAndVerification\\\\)[A-Z][A-Za-z]+\\\\/';

    foreach ($files as $file) {
        $contents = $file->getContents();

        preg_match_all($pattern, $contents, $matches);

        expect($matches[0], $file->getRelativePathname())
            ->toBeEmpty();
    }
});

/**
 * The Infrastructure/Contracts directory is the only sanctioned gateway to
 * foreign domains, and every adapter must implement one of the domain
 * contracts.
 */
test('every IdentityAndVerification adapter implements one of the domain contracts', function (): void {
    $contracts = [
        App\Domains\IdentityAndVerification\Domain\Contracts\AcademicDocumentVerificationContract::class,
    ];

    foreach (File::files(app_path('Domains/IdentityAndVerification/Infrastructure/Contracts')) as $file) {
        $class = 'App\\Domains\\IdentityAndVerification\\Infrastructure\\Contracts\\'.$file->getFilenameWithoutExtension();

        expect(class_exists($class), $class)->toBeTrue();

        $implements = false;
        foreach ($contracts as $contract) {
            if (is_subclass_of($class, $contract)) {
                $implements = true;
                break;
            }
        }

        expect($implements, $class)->toBeTrue();
    }
});

/**
 * Every cross-domain contract must have a container binding wired by the
 * provider.
 */
test('every IdentityAndVerification domain contract is bound in the container', function (): void {
    $contracts = [
        App\Domains\IdentityAndVerification\Domain\Contracts\AcademicDocumentVerificationContract::class,
    ];

    foreach ($contracts as $contract) {
        expect(app()->bound($contract), $contract)->toBeTrue();
    }
});

/**
 * The Presentation layer ships the public verification endpoint. Presentation
 * files may only reach the application service and the domain policy of their
 * own engine: never a foreign domain and never the Infrastructure layer
 * (ADR-022 Decision 3 / C1).
 */
test('the IdentityAndVerification Presentation layer stays thin and never reaches beneath the application service', function (): void {
    $presentation = app_path('Domains/IdentityAndVerification/Presentation/Http');

    expect(is_dir($presentation))->toBeTrue();

    $files = File::files($presentation);

    expect($files)->not->toBeEmpty();

    foreach ($files as $file) {
        $contents = $file->getContents();

        expect(str_contains($contents, 'use App\\Domains\\'), $file->getFilename())->toBeTrue();

        preg_match_all('/use\s+App\\Domains\\\\(?!IdentityAndVerification\\\\)[A-Z][A-Za-z]+\\\\/', $contents, $matches);

        expect($matches[0], $file->getFilename())->toBeEmpty();

        expect(str_contains($contents, 'Infrastructure\\'), $file->getFilename())->toBeFalse();
    }
});

/**
 * No legacy generated-document machinery may be referenced anywhere in the
 * engine, including its new Presentation layer (ADR-022 L1).
 */
test('the IdentityAndVerification engine ships no legacy references', function (): void {
    $base = app_path('Domains/IdentityAndVerification');

    $legacy = [
        'GeneratedDocument',
        'DocumentTemplate',
        'DocumentGenerationService',
        'DocumentCenter',
    ];

    expect(File::isDirectory($base))->toBeTrue();

    foreach (File::allFiles($base) as $file) {
        $contents = $file->getContents();

        foreach ($legacy as $symbol) {
            expect(str_contains($contents, $symbol), $file->getRelativePathname())->toBeFalse();
        }
    }
});

/**
 * SPR-016 Phase 5 provides the canonical verification URL payload
 * (ADR-022 Decision 5) but renders no QR code and ships no QR-generating
 * dependency (SPR-016 Phase 5 / "no-QR-rendering" scope cut).
 *
 * The engine may serialize the plaintext token into an absolute canonical URL
 * at the token-bearing boundary, but it must never embed a QR representation
 * (:PngRenderer, base64 data URIs, image encoders) or reference a QR library.
 * Only the plain URL string contract is allowed; the document-rendering/
 * QR consumer integration is deferred to a future phase that must first bring
 * its own bounded consumer and explicitly adopt a QR library.
 */
test('the IdentityAndVerification engine renders no QR code and ships no QR dependency', function (): void {
    $base = app_path('Domains/IdentityAndVerification');

    expect(File::isDirectory($base))->toBeTrue();

    expect(File::isDirectory($base.'/Infrastructure/Renderers'))->toBeFalse();

    $qrLibraries = [
        'BaconQrCode',
        'Endroid\\QrCode',
        'SimpleSoftwareIO',
        'Chillerlan',
        'khanamiryan',
        'QrCodeFacade',
        'PngRenderer',
        'data:image/png;base64',
    ];

    foreach (File::allFiles($base) as $file) {
        $contents = $file->getContents();

        foreach ($qrLibraries as $symbol) {
            expect(str_contains($contents, $symbol), $file->getRelativePathname())->toBeFalse();
        }
    }
});
