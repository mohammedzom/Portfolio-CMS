# Phase 1 Report — Foundation, Hardening & Web Readiness

Status: **in progress**. This file is updated as each Phase 1 task is merged into `dev`.

| Task | Branch | PR | Status |
| --- | --- | --- | --- |
| 01 Environment audit & bootstrap | `chore/phase1-task-01-bootstrap` | — | in progress |
| 02 Soft deletes & resource recovery | `fix/phase1-task-02-soft-deletes` | — | pending |
| 03 Safe file lifecycle | `fix/phase1-task-03-file-lifecycle` | — | pending |
| 04 Security & authentication hardening | `fix/phase1-task-04-security` | — | pending |
| 05 Cache & visitor tracking | `refactor/phase1-task-05-cache-visitors` | — | pending |
| 06 CI/CD, quality & web readiness | `chore/phase1-task-06-ci-documentation` | — | pending |

---

## 1. Baseline environment

Recorded on 2026-11-15 on the development VPS (not production).

| Item | Value |
| --- | --- |
| PHP (CLI) | 8.5.12 |
| Laravel framework | 13.42.0 (`composer.json`: `^13.0`) |
| Sanctum | 4.4.0 |
| lorisleiva/laravel-actions | 2.13.0 |
| Pest / pest-plugin-laravel | 4.7.8 / 4.1.0 |
| Pint | 1.32.3 |
| Laravel Boost | 2.10.4 (MCP server `php artisan boost:mcp`) |
| Node / npm | 26.13.1 / 11.20.0 (no `package.json` in the repository) |
| Local database | SQLite (`database/database.sqlite`) |
| Test database | SQLite in-memory (`phpunit.xml`) |
| Cache / queue (default `.env.example`) | `database` / `database` |
| Cache / queue (tests) | `array` / `sync` |

Git state at start: `main` at `7902e1e` (identical to `origin/main`). No `dev` branch existed locally or remotely.
Remote branches `admin-dashboard-rebuild`, `feature/final-ui-rebuild`, `feature/frontend-rebuild` and one Arabic-named
branch exist from earlier experiments; they were left untouched.

### Baseline tests

```
php artisan test --compact
Tests: 30 passed (103 assertions)
```

### Laravel Boost MCP

The MCP server failed to connect at the very start of the session, then connected on retry. It was verified working:
`search-docs` returned Laravel 13 / Sanctum 4 documentation, and `database-schema` read the local SQLite schema.
No configuration change was needed.

---

## 2. Task 01 — Environment audit & bootstrap

### Previous setup session (uncommitted work found on `main`)

| Change | Assessment | Decision |
| --- | --- | --- |
| `composer.lock`: broad `composer update` (Laravel 13.6 → 13.42, Sanctum 4.3 → 4.4, Pest 4.x, Symfony 8.0 → 8.1, Guzzle 7 → 8, brick/math 0.14 → 1.0, laravel/mcp 0.7 → 1.2, laravel/roster 0.5 → 1.0) | All versions satisfy the existing `composer.json` constraints. The major bumps are transitive (Guzzle is not used directly by the app; laravel/mcp and roster are Boost internals). The full suite passes on the updated lock. | **Retained.** No further dependency update was run. |
| `composer.json`: `laravel/boost` constraint changed from `^2.2` to `*` | An unbounded constraint would let a future Boost 3.x install silently. | **Fixed** to `^2.10` (the installed line). Lock hash refreshed with `composer update --lock --no-scripts --no-install`; installed versions verified unchanged. |
| `boost.json`: agents changed from junie/claude_code/gemini to junie/claude_code/codex/cursor/copilot/antigravity; skills `pest-testing` → `testing-best-practices`, `laravel-actions`, `infer-conventions` | Matches the guideline files Boost regenerated. | **Retained** (owner's tooling choice). |
| Regenerated `CLAUDE.md`, `AGENTS.md`, `.agents/`, `.claude/`, `.junie/`, `.cursor/`, `.github/skills/` | Boost-generated guidelines and skills. The `pest-testing` skill was replaced upstream by `testing-best-practices`. | **Committed** so every agent sees the same rules. |
| `.junie/mcp/mcp.json` | Contains absolute paths (`/usr/bin/php8.5`, `/home/ubuntu/...`). Not portable. | **Ignored** via `.gitignore`. |
| `package-lock.json` (untracked) | Empty lockfile (`"packages": {}`) produced by an `npm install` run before a `package.json` existed. | **Deleted.** |
| `GEMINI.md`, `.gemini/settings.json` | Still tracked, but `gemini` is no longer listed in `boost.json`, so they will no longer be regenerated. | Left in place; **deferred** to the owner (delete if Gemini is no longer used). |

### `boost:update` failure

`composer.json` runs `@php artisan boost:update --ansi` after every `composer update`. The command defaults to
`--discover`, which prompts interactively for newly available guidelines and skills, so it can fail or hang in
non-interactive runs. No failure log was available to confirm the exact error. The script was left unchanged. Run
`php artisan boost:update --no-discover` in automation.

### Audit findings (verified against current code)

Severity: **High** = broken feature or data loss/security risk; **Medium** = incorrect behaviour or a hardening gap;
**Low** = quality or maintainability.

| # | Area | Finding | Severity | Task |
| --- | --- | --- | --- | --- |
| F1 | Soft deletes | `ProjectController::restore()` type-hints `App\Http\Requests\Projects\RestoreProjectRequest`, which does not exist. `PATCH /api/v1/admin/projects/{id}/restore` throws a class-not-found error, so archived projects cannot be restored through the API. | High | 02 |
| F2 | Soft deletes | `App\Traits\ManageSoftDeletes` is unused dead code (it references `$modelClass`/`$resourceClass` properties no controller defines). | Low | 02 |
| F3 | Soft deletes | Achievement and Project lifecycle logic (restore, force delete, file cleanup) lives in controllers, unlike every other resource, which uses Actions. | Low | 02/03 |
| F4 | Files | Every file write/delete runs *inside* `DB::transaction()` but is not rolled back with it. Old files are deleted **before** the DB update commits (project images, achievement certificate, avatar, CV), so a failed update loses the previous valid file. New files written before a failing DB write become orphans. | High | 03 |
| F5 | Files | Force delete removes files before the row is deleted (Project, Achievement). | Medium | 03 |
| F6 | Files | Achievement certificates and CVs are stored under the **client-supplied original filename**. Two uploads with the same name overwrite each other (and the second record then points at the other record's file). | High | 03 |
| F7 | Files | Avatar is always stored as `avatars/avatar.<ext>`: the old file is deleted first, and identical URLs defeat browser/CDN caches. | Medium | 03 |
| F8 | Files | Project image names embed the raw client filename (`project_<name>_<uniqid>`). Unsanitised characters reach the filesystem. | Medium | 03 |
| F9 | Files | `StoreAchievementAction` deletes `$data['certificate_url']` from disk if present. It is unreachable today (not a validated field), but it is a latent arbitrary-delete primitive. | Medium | 03 |
| F10 | Security | `LoginRequest` uses `exists:users,email` and returns "The email address is not registered." (account enumeration). | Medium | 04 |
| F11 | Security | Sanctum `expiration` is `null`: admin API tokens never expire, and nothing prunes old tokens. | Medium | 04 |
| F12 | Security / errors | The catch-all `Throwable` renderer turns every non-mapped HTTP exception (405, 413, 503 maintenance mode, …) into **500 "Something went wrong."** | Medium | 04 |
| F13 | Security | Public contact form `body` has no maximum length. | Low | 04 |
| F14 | Cache | Project and Service `reorder` only forget the admin list cache, so `portfolio_all` (the public payload) stays stale for up to `CACHE_TTL` hours. | High | 05 |
| F15 | Cache | Skill and skill-category admin lists are not cached, so their `forget('skills')`/`forget('skills_archived')` calls are no-ops (harmless dead code). The public `portfolio_all` payload is correctly forgotten on every skill/category change. | Low | 05 |
| F16 | Cache | Invalidation logic is duplicated across controllers, actions (`Cache::forget(...)` lists) and the `ManagesCache` trait, and some calls run twice. | Low | 05 |
| F17 | Visitors | `LogVisitJob::handle()` catches every exception and only logs a warning, so `$tries = 3` never triggers. Failures are never retried and never reach `failed_jobs`. | Medium | 05 |
| F18 | Visitors | Raw visitor IP addresses are stored indefinitely. There is no retention or pruning. | Medium (privacy) | 05 |
| F19 | CI/CD | The workflow only runs on `main`; PRs to `dev` are not validated. No Pint check. | Medium | 06 |
| F20 | CI/CD | Deploy script: `php artisan down || true` then `git pull`/`composer install`/`migrate` with no `set -e` and no `trap`, so a failure after `down` can leave the site in maintenance mode or run later steps on a half-updated tree. There is no rollback point (previous commit not recorded). | High | 06 |
| F21 | Docs | README states the app has "no frontend build tools, no Blade templates" — still true in the tracked tree (`public/build` is git-ignored and only exists locally from older branches). The CI badge implies CI/CD covers all branches; it covers `main` only. There are no build-provenance claims to verify beyond that. | Low | 06 |

---

## 3. Remaining sections

Filled in as each task is merged: implemented solutions, test evidence, CI verification, commit SHAs, PR URLs,
known limitations, and Phase 2 readiness.
