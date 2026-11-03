<?php

declare(strict_types=1);

use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Filament published assets
|--------------------------------------------------------------------------
|
| Filament ships precompiled JS/CSS inside each package's `dist/` directory
| and serves copies from `public/`. Those copies are committed, so they can
| silently drift from the installed package after a `composer update`.
|
| A stale `support.js` / `schemas.js` can stop Alpine from booting. On the
| login page that looks like a dead password reveal toggle and a password
| that appears filled while Livewire still receives `null`, producing
| "The password field is required."
|
| Keep the published copies byte-for-byte in sync with the vendor sources.
|
*/

uses(TestCase::class);

/**
 * Assert that a published Filament asset matches its vendor source.
 *
 * Line endings are normalised so a Windows checkout and the composer-installed
 * package do not produce false failures.
 */
function assertFilamentAssetIsPublished(string $source, string $published): void
{
    expect(File::exists($source))->toBeTrue("Missing vendor Filament asset: {$source}");
    expect(File::exists($published))->toBeTrue("Missing published Filament asset: {$published}. Run `php artisan filament:assets`.");

    $normalise = static fn (string $path): string => str_replace(["\r\n", "\r"], "\n", File::get($path));

    expect($normalise($published))->toBe(
        $normalise($source),
        "Published Filament asset is stale: {$published}. Run `php artisan filament:assets`.",
    );
}

test('published Filament scripts, styles, themes and Alpine components match the installed vendor packages', function (): void {
    $assets = [
        ...FilamentAsset::getScripts(),
        ...FilamentAsset::getStyles(),
        ...FilamentAsset::getThemes(),
        ...FilamentAsset::getAlpineComponents(),
    ];

    $checked = 0;

    foreach ($assets as $asset) {
        if ($asset->isRemote() || ! str_starts_with((string) $asset->getPackage(), 'filament/')) {
            continue;
        }

        assertFilamentAssetIsPublished($asset->getPath(), $asset->getPublicPath());
        $checked++;
    }

    expect($checked)->toBeGreaterThan(0, 'No Filament assets were registered, so the published assets could not be verified.');
});

test('published Filament fonts match the installed vendor packages', function (): void {
    $checked = 0;

    foreach (FilamentAsset::getFonts() as $asset) {
        if ($asset->isRemote() || ! str_starts_with((string) $asset->getPackage(), 'filament/')) {
            continue;
        }

        foreach (File::allFiles($asset->getPath()) as $file) {
            if ($file->getExtension() === 'js') {
                continue;
            }

            assertFilamentAssetIsPublished(
                $file->getPathname(),
                $asset->getPublicPath() . DIRECTORY_SEPARATOR . $file->getFilename(),
            );
            $checked++;
        }
    }

    expect($checked)->toBeGreaterThan(0, 'No Filament fonts were registered, so the published fonts could not be verified.');
});
