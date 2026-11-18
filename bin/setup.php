<?php

/**
 * First-run setup, called by `composer create-project` (and safe to run
 * again: `php bin/setup.php`). Creates .env from .env.example with
 * generated passwords, unless .env already exists, and prints next steps.
 *
 * Plain PHP, no dependencies: works on Linux, macOS and Windows.
 */

$root = dirname(__DIR__);
$env = $root . '/.env';
$example = $root . '/.env.example';

if (is_file($env)) {
    fwrite(STDOUT, ".env already exists; leaving it alone.\n");
} elseif (!is_file($example)) {
    fwrite(STDERR, "No .env.example found; create .env by hand.\n");
    exit(1);
} else {
    $name = strtolower(preg_replace('/[^a-z0-9]+/i', '-', basename($root))) ?: 'silverwind';
    $adminPassword = password();
    $values = [
        'SS_DATABASE_PASSWORD' => password(),
        'SS_DEFAULT_ADMIN_PASSWORD' => $adminPassword,
        'SS_DATABASE_NAME' => str_replace('-', '_', $name),
        'COMPOSE_PROJECT_NAME' => $name,
    ];

    $contents = file_get_contents($example);
    foreach ($values as $key => $value) {
        $contents = preg_replace('/^' . $key . '=.*$/m', $key . '="' . $value . '"', $contents);
    }
    file_put_contents($env, $contents);

    fwrite(STDOUT, <<<TXT

    Created .env with generated passwords.
      CMS login: admin / {$adminPassword}  (also in .env as SS_DEFAULT_ADMIN_PASSWORD)

    TXT);
}

fwrite(STDOUT, <<<'TXT'

Next steps (see README.md for details):

  With Docker
    docker compose up -d --build
    docker compose exec web composer install
    docker compose exec -u www-data web vendor/bin/sake db:build --flush
    docker compose exec -u www-data web vendor/bin/sake db:build
    docker compose exec -u www-data web vendor/bin/sake tasks:seed-demo-site --reset    (optional demo)

  Without Docker (Linux or Windows server with PHP 8.3+, MySQL, Apache/nginx/IIS)
    edit .env (database, SS_BASE_URL, mail)
    vendor/bin/sake db:build --flush   (as the web server's user)
    point the web server at public/

TXT);

function password(): string
{
    // URL- and .env-safe
    return rtrim(strtr(base64_encode(random_bytes(18)), '+/', '-_'), '=');
}
