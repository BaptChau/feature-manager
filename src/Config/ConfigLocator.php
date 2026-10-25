<?php

namespace BaptChau\Config;

use BaptChau\Exception\InvalidFlagException;
use Composer\InstalledVersions;

/**
 * Find the feature flag file of the project.
 *
 * Default is config/feature_flag.yaml, relative to the project root.
 * A project can override it in its composer.json:
 *
 *   "extra": {
 *     "feature-manager": {
 *       "config-file": "path/to/flags.yaml"
 *     }
 *   }
 */
class ConfigLocator
{
    public const DEFAULT_CONFIG_FILE = "config/feature_flag.yaml";
    public const EXTRA_KEY = "feature-manager";
    public const CONFIG_FILE_KEY = "config-file";

    /**
     * @param string|null $projectRoot defaults to the root of the composer project
     * @return string absolute path of the feature flag file
     * @throws InvalidFlagException if the override is not a valid path
     */
    public static function getConfigFile(?string $projectRoot = null): string
    {
        $projectRoot ??= self::getProjectRoot();
        $path = self::readExtra($projectRoot)[self::CONFIG_FILE_KEY] ?? self::DEFAULT_CONFIG_FILE;

        if (!is_string($path) || trim($path) === "") {
            throw new InvalidFlagException(
                "extra." . self::EXTRA_KEY . "." . self::CONFIG_FILE_KEY . " in composer.json must be a file path"
            );
        }

        return self::isAbsolute($path) ? $path : rtrim($projectRoot, "/\\") . DIRECTORY_SEPARATOR . $path;
    }

    public static function getProjectRoot(): string
    {
        if (class_exists(InstalledVersions::class)) {
            $root = realpath(InstalledVersions::getRootPackage()['install_path']);
            if ($root !== false) {
                return $root;
            }
        }

        return getcwd();
    }

    private static function readExtra(string $projectRoot): array
    {
        $composerFile = $projectRoot . DIRECTORY_SEPARATOR . "composer.json";
        if (!is_file($composerFile)) {
            return [];
        }

        $composer = json_decode(file_get_contents($composerFile), true);
        $extra = $composer['extra'][self::EXTRA_KEY] ?? [];

        return is_array($extra) ? $extra : [];
    }

    private static function isAbsolute(string $path): bool
    {
        return str_starts_with($path, "/")
            || str_starts_with($path, "\\\\")
            || preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1;
    }
}
