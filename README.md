# vylwyn.com

[![CI](https://github.com/Vylwyn/vylwyn.com/actions/workflows/ci.yml/badge.svg)](https://github.com/Vylwyn/vylwyn.com/actions/workflows/ci.yml)

My personal site, built in public. Live at **[vylwyn.com](https://vylwyn.com)**.

Laravel 13, Livewire 4, Filament 5, Tailwind 4. Deployed to shared hosting by GitHub Actions.

---

## Why this repo exists

I lead a 22-person IT support and procurement team, and I build software in the evenings. This site is both the portfolio and a worked example — the commit history shows how it was put together, including the parts that broke.

## Stack

| | |
|---|---|
| Backend | PHP 8.4 · Laravel 13 |
| Frontend | Blade · Livewire 4 · Alpine · Tailwind 4 · Vite |
| Admin | Filament 5 |
| Database | MySQL 8.4 |
| Testing | Pest 5 · Laravel Pint |
| CI/CD | GitHub Actions → Hostinger |

## Decisions worth explaining

**Technologies are a real relationship, not a JSON column.** `Project` and `Technology` are many-to-many through a pivot with a composite primary key, so the database — not application code — prevents a project being tagged twice. The same table powers both project tags and the public skills grid, with a `show_in_skills` flag, so the two lists can't drift apart.

**No `is_current` column on experiences.** It's derived from `ended_on IS NULL`. Storing both invites a row that has an end date *and* claims to be current.

**`published_at` instead of a boolean.** It records when, supports scheduling, and reads well as a scope: `whereNotNull('published_at')`.

**Page copy is a cached singleton.** `SiteContent` holds the editable hero and About text. It caches a **plain array**, never the model — caching an Eloquent object serialises its class name, and if that class can't be resolved on a later request you get `__PHP_Incomplete_Class`. With `rememberForever` that's a permanent 500. This one reached production before I understood it; there's a regression test now.

**SQLite locally, MySQL in CI.** Fast feedback while developing, real parity before anything ships. The two disagree often enough to matter.

**`config.platform.php` is pinned.** Composer resolves against the running PHP version, not the declared one. Pinning it means the lock file can't drift from the production runtime — the first CI failure on this repo was exactly that.

## Deployment

The document root on this host is locked to `public_html`, which also contains unrelated subdomains. So the app deploys to a sibling directory and only its `public/` contents are published:

```
domains/vylwyn.com/
├── laravel/          # application — rsync --delete target, safe
│   ├── storage/      # above the web root, never servable
│   └── .env          # above the web root, never servable
└── public_html/      # document root
    ├── index.php     # bootstraps ../laravel
    └── <subdomains>  # untouched by deploys
```

`rsync --delete` runs only against `laravel/`. The publish step into `public_html` deliberately omits `--delete`, because deleting there would take the other sites with it.

Every push to `main` runs tests and Pint; only if both pass does the deploy job build assets (the host has no Node), upload, migrate, and rebuild caches. It finishes by asserting the live site returns 200 with rendered content — a deploy that "succeeds" while serving a 500 is worse than one that fails.

## Local setup

```bash
git clone git@github.com:Vylwyn/vylwyn.com.git
cd vylwyn.com

composer install
npm install

cp .env.example .env
php artisan key:generate
# set DB_* for a local MySQL database

php artisan migrate --seed
npm run dev
```

Admin panel at `/vrdstudio`. Access requires an address listed in `ADMIN_EMAILS` — the list fails closed, so an empty value locks everyone out.

## Tests

```bash
php artisan test          # full suite
vendor/bin/pint           # fix formatting
```

Unit tests cover pure logic — enums, no database. Feature tests cover models, pages, the contact form and its spam handling, SEO output, and admin access rules.

## Licence

MIT for the code. The written content and images are mine.
