# Silverwind Starter

Start a Silverstripe CMS 6 site on the **Silverwind** stack:

| Package | What it gives you |
|---|---|
| [silverwind](https://github.com/marcd1983/silverwind) | Theme: Tailwind CSS 4, daisyUI 5, Alpine.js; header, mega menus, footer, cookie banner |
| [silverwind-blocks](https://github.com/marcd1983/silverwind-blocks) | Content blocks: hero, cards, sections, carousels, accordions, tabs, gallery, sliders |
| [silverwind-settings](https://github.com/marcd1983/silverwind-settings) | Brand colours, fonts and layout from *Settings > Theme* |
| [silverwind-site](https://github.com/marcd1983/silverwind-site) | Services, ribbon CTAs, testimonials, partners, consent-gated scripts, block search, SEO, security headers, demo |
| [store-location](https://github.com/marcd1983/store-location) | Locations with hours, a store locator map, departments and staff |

It runs anywhere Silverstripe 6 runs: **Linux** (Apache or nginx), **Windows**
(IIS) or the included **Docker** stack.

## Create a site

You need [Composer](https://getcomposer.org/) and PHP 8.3+ (or Docker, below):

```sh
composer create-project antlion/silverwind-starter my-site \
  --repository='{"type":"vcs","url":"https://github.com/marcd1983/silverwind-starter"}'
```

This downloads everything and runs `bin/setup.php`, which creates `.env` with
generated passwords and prints the CMS login.

**No PHP on your machine?** Run Composer through Docker (Linux/macOS shell;
on Windows PowerShell use `${PWD}`):

```sh
docker run --rm -it -v "$PWD":/app -w /app composer:2 create-project \
  antlion/silverwind-starter my-site --ignore-platform-reqs \
  --repository='{"type":"vcs","url":"https://github.com/marcd1983/silverwind-starter"}'
```

(`--ignore-platform-reqs` because the Composer image lacks some PHP
extensions; the site's own container has them all.)

## Run it

### With Docker (local development)

```sh
cd my-site
docker compose up -d --build
docker compose exec web composer install
docker compose exec -u www-data web vendor/bin/sake db:build --flush
docker compose exec -u www-data web vendor/bin/sake db:build
docker compose exec -u www-data web vendor/bin/sake tasks:seed-demo-site --reset   # optional demo (replaces the default pages)
```

Site on http://localhost:8080 (CMS at `/admin`), email in Mailpit on
http://localhost:8025. Ports and ids are in `.env`.

Run `sake` and `phpunit` as `www-data` (`-u www-data`): files created as
root can't be written by the web server, and Silverstripe keeps a config
cache per system user.

### On a Linux server (Apache or nginx)

1. PHP 8.3+ with `intl`, `gd`, `zip`, `mysqli`/`pdo_mysql`, `bcmath`;
   MySQL 8 or MariaDB 10.6+.
2. Point the site's document root at **`public/`**.
   - Apache: `mod_rewrite` on and `AllowOverride All`; `public/.htaccess`
     does the rest.
   - nginx: send everything that isn't a file to `public/index.php`
     ([Silverstripe's nginx example](https://docs.silverstripe.org/en/getting_started/server_requirements/)).
3. Edit `.env`: database (`SS_DATABASE_SERVER` is usually `localhost`),
   `SS_BASE_URL`, `MAILER_DSN`, and `SS_ENVIRONMENT_TYPE="live"`.
4. As the web server's user (e.g. `sudo -u www-data`):
   ```sh
   composer install --no-dev --optimize-autoloader
   vendor/bin/sake db:build --flush
   ```
5. The web server's user must be able to write `public/assets/` and
   `silverstripe-cache/` (or set `SS_TEMP_PATH`).

### On a Windows server (IIS)

1. IIS with the **URL Rewrite** module, PHP 8.3+ via FastCGI (with the
   extensions above), MySQL or MariaDB.
2. Point the IIS site at **`public\`**. `public\web.config` (and
   `public\assets\web.config`) handle rewriting and block script execution
   in uploads; keep them.
3. Edit `.env` as for Linux.
4. In a terminal:
   ```bat
   composer install --no-dev --optimize-autoloader
   php vendor\bin\sake db:build --flush
   ```
5. Give the IIS application pool identity (e.g. `IIS AppPool\my-site`)
   modify rights on `public\assets\` and `silverstripe-cache\`.

Nothing in the composer scripts needs bash, so `composer install` behaves
the same on Windows.

### Deploying updates (any server)

```sh
composer install --no-dev --optimize-autoloader
vendor/bin/sake db:build --flush
```

The theme's CSS/JS (`themes/silverwind/dist`) comes prebuilt from the theme
package, so servers never need Node.

## Make it yours

- **Brand:** *Settings > Theme* (colours, fonts, header/footer, widths), or
  import the client's Figma variables: save them as `design/tokens.json`
  and run `vendor/bin/sake tasks:import-design-tokens` (see `design/README.md`).
- **Pages:** build them from blocks; *Services*, *Ribbon CTAs*,
  *Testimonials*, *Partners* and *Staff & Hours* have their own CMS sections.
- **Code:** `app/` is yours (`Page`, `HomePage`, `BlockPage`, config).
  Features live in the modules; update them with `composer update`.
- **Remove the demo:** archive the demo pages in the CMS, or create the site
  without running the seed task.
- **Tailwind classes in your own templates:** the theme's CSS only contains
  classes used in the theme and modules. If your templates in `app/` use
  others, rebuild the theme's CSS (`cd themes/silverwind && npm install &&
  npm run build`) and keep that build with the site, e.g. by forking the
  theme for the client.

## Working on the antlion packages

Set `ANTLION_MODULES_DIR` in `.env` to a folder with your checkouts
(`silverwind`, `silverwind-blocks`, ...). After `composer install`/`update`,
`bin/link-local-modules.php` links `vendor/antlion/*` and `themes/silverwind`
to them (junctions on Windows), while `composer.lock` keeps pointing at
GitHub. For Docker, also mount that folder at the same path in
`docker-compose.yml`.

## Checks

```sh
vendor/bin/phpunit                                    # app/tests
vendor/bin/phpunit vendor/antlion/silverwind-site/tests
vendor/bin/phpcs
```

GitHub Actions (`.github/workflows/ci.yml`) runs lint, coding standard,
`db:build` and the tests on every push.

## License

BSD-3-Clause
