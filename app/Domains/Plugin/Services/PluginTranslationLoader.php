<?php
declare(strict_types=1);

namespace App\Domains\Plugin\Services;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Lang;

final class PluginTranslationLoader
{
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function loadTranslations(string $pluginName, string $pluginPath): void
    {
        $langPath = $pluginPath . '/Resources/Lang';

        if (!$this->filesystem->isDirectory($langPath)) {
            return;
        }

        $locales = $this->filesystem->directories($langPath);

        foreach ($locales as $localePath) {
            $locale = basename($localePath);
            $files = $this->filesystem->glob($localePath . '/*.php');

            foreach ($files as $file) {
                $group = basename($file, '.php');
                $translations = require $file;

                if (is_array($translations)) {
                    foreach ($translations as $key => $value) {
                        Lang::override(
                            "{$pluginName}.{$group}.{$key}",
                            $value,
                            $locale
                        );
                    }
                }
            }
        }
    }

    public function unloadTranslations(string $pluginName): void
    {
    }
}
