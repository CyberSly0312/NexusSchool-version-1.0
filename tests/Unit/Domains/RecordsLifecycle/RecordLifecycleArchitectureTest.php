<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

test('the RecordsLifecycle domain stays within its bounded context', function (): void {
    $root = app_path('Domains/RecordsLifecycle');

    expect(File::exists($root))->toBeTrue();
    $files = File::allFiles($root);

    foreach ($files as $file) {
        $path = str_replace('\\', '/', $file->getRealPath());
        expect($path)->toContain('/app/Domains/RecordsLifecycle/');
    }
});
