<?php

/**
 * Local development only: replace the GitHub copies of the antlion packages
 * with links to your own checkouts, so edits there show up immediately.
 * composer.json and composer.lock keep pointing at GitHub, so the project
 * still installs anywhere.
 *
 * Runs after `composer install` / `composer update` (see composer.json
 * "scripts"). Does nothing unless ANTLION_MODULES_DIR is set (in the
 * environment or .env) and exists, so servers and CI are unaffected.
 *
 *   ANTLION_MODULES_DIR=/path/to/checkouts
 *     vendor/antlion/<name>  -> <dir>/<name>    (silverwind-blocks, ...)
 *     themes/silverwind      -> <dir>/silverwind  (if that checkout exists)
 *
 * Plain PHP, no dependencies: works on Linux, macOS and Windows (on Windows,
 * directory links are created as junctions, which need no admin rights).
 */

$root = dirname(__DIR__);
$modulesDir = getenv('ANTLION_MODULES_DIR') ?: envFileValue($root . '/.env', 'ANTLION_MODULES_DIR');

if (!$modulesDir) {
    exit(0);
}
if (!is_dir($modulesDir)) {
    fwrite(STDOUT, "link-local-modules: {$modulesDir} not found, using the GitHub copies\n");
    exit(0);
}

$targets = [];
foreach (glob($root . '/vendor/antlion/*', GLOB_ONLYDIR) ?: [] as $path) {
    $targets[$path] = $modulesDir . '/' . basename($path);
}
$targets[$root . '/themes/silverwind'] = $modulesDir . '/silverwind';

foreach ($targets as $path => $checkout) {
    if (!is_file($checkout . '/composer.json') || !file_exists($path)) {
        continue;
    }
    if (realpath($path) === realpath($checkout)) {
        continue; // already linked
    }

    removePath($path);
    $linked = PHP_OS_FAMILY === 'Windows'
        ? linkWindows($checkout, $path)
        : @symlink($checkout, $path);

    $relative = substr($path, strlen($root) + 1);
    fwrite(STDOUT, $linked
        ? "link-local-modules: {$relative} -> {$checkout}\n"
        : "link-local-modules: could not link {$relative}; run composer install again to restore it\n");
}

function linkWindows(string $target, string $link): bool
{
    // Junctions work for directories without elevated rights
    exec(sprintf('mklink /J %s %s', escapeshellarg($link), escapeshellarg($target)), $output, $code);

    return $code === 0;
}

function removePath(string $path): void
{
    if (is_link($path) || (PHP_OS_FAMILY === 'Windows' && is_dir($path) && readlink($path) !== false)) {
        // A link (or junction): remove the link, never the target's contents
        PHP_OS_FAMILY === 'Windows' ? @rmdir($path) : @unlink($path);
        return;
    }
    if (is_dir($path)) {
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() && !$item->isLink() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }
        rmdir($path);
    }
}

function envFileValue(string $file, string $name): ?string
{
    if (!is_file($file)) {
        return null;
    }
    foreach (file($file, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
        if (preg_match('/^\s*' . preg_quote($name, '/') . '\s*=\s*"?([^"#]*)"?\s*$/', $line, $m)) {
            return trim($m[1]) ?: null;
        }
    }

    return null;
}
