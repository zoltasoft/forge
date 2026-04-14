<?php

declare(strict_types=1);

namespace Zolta\Framework;

use JsonException;

/**
 * Responsible for discovering framework adapters via Composer metadata.
 */
final class FrameworkBootstrap
{
    private const EXTRA_KEY = 'zolta-framework-adapter';

    private static bool $bootstrapped = false;

    public static function boot(): void
    {
        if (self::$bootstrapped) {
            return;
        }

        foreach (self::discoverAdapters() as $adapter) {
            FrameworkRegistry::register($adapter);
        }

        self::$bootstrapped = true;
    }

    /**
     * @return array<class-string<FrameworkAdapterInterface>>
     */
    private static function discoverAdapters(): array
    {
        $adapters = [];

        foreach (self::composerMetadata() as $package) {
            if (! isset($package['extra'][self::EXTRA_KEY])) {
                continue;
            }

            $value = $package['extra'][self::EXTRA_KEY];
            if (is_string($value)) {
                $adapters[] = $value;

                continue;
            }

            if (! is_iterable($value)) {
                continue;
            }

            foreach ($value as $adapter) {
                if (! is_string($adapter) || $adapter === '') {
                    continue;
                }

                $adapters[] = $adapter;
            }
        }

        return array_values(array_unique($adapters));
    }

    /**
     * @return array<array<string, mixed>>
     */
    private static function composerMetadata(): array
    {
        $metadata = self::metadataFromInstalledFiles();

        if (class_exists(\Composer\InstalledVersions::class)) {
            $metadata = array_merge($metadata, self::metadataFromInstalledVersions());
        }

        return $metadata;
    }

    /**
     * @return array<array<string, mixed>>
     */
    private static function metadataFromInstalledVersions(): array
    {
        $metadata = [];

        try {
            $entries = \Composer\InstalledVersions::getAllRawData();
        } catch (\Throwable) {
            return [];
        }

        foreach ($entries as $entry) {
            if (isset($entry['packages']) && is_array($entry['packages'])) {
                $metadata = array_merge($metadata, $entry['packages']);
            }

            if (isset($entry['root']) && is_array($entry['root'])) {
                $metadata[] = $entry['root'];
            }
        }

        return $metadata;
    }

    /**
     * @return array<array<string, mixed>>
     */
    private static function metadataFromInstalledFiles(): array
    {
        $metadata = [];

        foreach (self::installedComposerFiles() as $file) {
            $packageData = self::readInstalledFile($file);
            if ($packageData === null) {
                continue;
            }

            if (isset($packageData['packages']) && is_array($packageData['packages'])) {
                $metadata = array_merge($metadata, $packageData['packages']);
            }

            if (isset($packageData['versions']) && is_array($packageData['versions'])) {
                foreach ($packageData['versions'] as $possible) {
                    if (is_array($possible)) {
                        $metadata[] = $possible;
                    }
                }
            }

            if (isset($packageData['root']) && is_array($packageData['root'])) {
                $metadata[] = $packageData['root'];
            }
        }

        return $metadata;
    }

    /**
     * @return array<string>
     */
    private static function installedComposerFiles(): array
    {
        $paths = [];

        foreach (self::composerSearchRoots() as $root) {
            $paths = array_merge($paths, self::composerFilesForRoot($root));
        }

        return array_unique($paths);
    }

    /**
     * @return array<string>
     */
    private static function composerSearchRoots(): array
    {
        $roots = [__DIR__];

        $cwd = getcwd();
        if (! in_array($cwd, ['', '0', false], true)) {
            $roots[] = $cwd;
        }

        $autoloadDir = self::composerAutoloadDirectory();
        if ($autoloadDir !== null && $autoloadDir !== '') {
            $roots[] = $autoloadDir;
        }

        return array_values(array_unique($roots));
    }

    private static function composerAutoloadDirectory(): ?string
    {
        if (! class_exists(\Composer\Autoload\ClassLoader::class)) {
            return null;
        }

        try {
            $file = (new \ReflectionClass(\Composer\Autoload\ClassLoader::class))->getFileName();
        } catch (\ReflectionException) {
            return null;
        }

        if (! is_string($file)) {
            return null;
        }

        return dirname($file);
    }

    /**
     * @return array<int,string>
     */
    private static function composerFilesForRoot(string $root): array
    {
        $paths = [];
        $current = $root;

        while (true) {
            $composerDir = $current.'/vendor/composer';
            if (is_dir($composerDir)) {
                foreach (['installed.json', 'installed.php'] as $file) {
                    $path = $composerDir.'/'.$file;
                    if (is_file($path)) {
                        $paths[] = $path;
                    }
                }
            }

            $parent = dirname($current);
            if ($parent === $current) {
                break;
            }

            $current = $parent;
        }

        return $paths;
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function readInstalledFile(string $path): ?array
    {
        if (! is_file($path)) {
            return null;
        }

        if (str_ends_with($path, '.json')) {
            try {
                $contents = file_get_contents($path);
            } catch (\Throwable) {
                return null;
            }

            if ($contents === false) {
                return null;
            }

            try {
                $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                return null;
            }

            return is_array($data) ? $data : null;
        }

        if (str_ends_with($path, '.php')) {
            try {
                $data = require $path;
            } catch (\Throwable) {
                return null;
            }

            return is_array($data) ? $data : null;
        }

        return null;
    }
}
