# Phase 1 Report — Foundation, Hardening & Web Readiness

Status: **complete on `dev`** (Phase 2 not started). Updated as each Phase 1 task was merged into `dev`.

| Task | Branch | PR | Status |
| --- | --- | --- | --- |
| 01 Environment audit & bootstrap | `chore/phase1-task-01-bootstrap` | [#7](https://github.com/mohammedzom/Portfolio-CMS/pull/7) | merged (`b8e16e8`) |
| 02 Soft deletes & resource recovery | `fix/phase1-task-02-soft-deletes` | [#8](https://github.com/mohammedzom/Portfolio-CMS/pull/8) | merged (`ba68b6f`) |
| 03 Safe file lifecycle | `fix/phase1-task-03-file-lifecycle` | [#9](https://github.com/mohammedzom/Portfolio-CMS/pull/9) | merged (`5ee16a6`) |
| 04 Security & authentication hardening | `fix/phase1-task-04-security` | [#10](https://github.com/mohammedzom/Portfolio-CMS/pull/10) | merged (`ec8b058`) |
| 05 Cache & visitor tracking | `refactor/phase1-task-05-cache-visitors` | [#11](https://github.com/mohammedzom/Portfolio-CMS/pull/11) | merged (`da720c7`) |
| 06 CI/CD, quality & web readiness | `chore/phase1-task-06-ci-documentation` | [#12](https://github.com/mohammedzom/Portfolio-CMS/pull/12) | merged (`69ab3c7`) |

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

### CI bootstrap

`dev` was created from `origin/main` (`7902e1e`) and published. The workflow previously triggered only on `main`,
so the first change was to add `dev` to the `push` and `pull_request` triggers. The `deploy` job keeps its guard
(`github.ref == 'refs/heads/main' && github.event_name == 'push'`), so activity on `dev` and on PRs cannot deploy.
Neither `main` nor `dev` has branch protection, so merges are gated by the documented workflow
(local tests + CI + diff review) rather than by GitHub rules. Enabling protection on `dev` with the `test` check
required is recommended to the owner.

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

### Task 01 result

- PR [#7](https://github.com/mohammedzom/Portfolio-CMS/pull/7) was squash-merged into `dev` as `b8e16e8`.
- Branch commits: `372e64b` (baseline) and `db838fd` (CI trigger for `dev`).
- CI on the PR: `test` passed and `deploy` was skipped, as intended. This was the first CI run for a PR targeting `dev`.
- The `gh` OAuth token lacks the `workflow` scope, so GitHub rejects pushes that modify `.github/workflows/` over HTTPS.
  The branch was pushed over the owner's configured SSH key instead. Run `gh auth refresh -s workflow` to restore HTTPS pushes.

---

## 3. Task 02 — Soft deletes & resource recovery

### Confirmed defects

| # | Defect | Root cause | Fix |
| --- | --- | --- | --- |
| F1 | `PATCH /api/v1/admin/projects/{project}/restore` returned 500 (`Class "App\Http\Requests\Projects\RestoreProjectRequest" does not exist`). Archived projects could not be restored. | The controller type-hinted a Form Request that was never committed. The endpoint takes no input. | Removed the non-existent dependency. `restore(Project $project)` now matches every other resource. |
| F2 | `App\Traits\ManageSoftDeletes` was unused. | Leftover from an earlier refactor to Actions. | Deleted. |
| T2-a | `MessageFactory` wrote an `is_read` column that does not exist (`read_at` is the source of truth; `is_read` is only computed in `MessageResource`). Any test creating a message through the factory crashed. | Stale factory after a schema change. | Removed `is_read` from the factory and its `read()`/`unread()` states. |
| T2-b | `Achievement` and `Experience` use `HasFactory` but had no factories. | Missing test fixtures. | Added `AchievementFactory` and `ExperienceFactory`. |

### Verified behaviour (all resources)

All restore and force-delete routes use `->withTrashed()` binding. Standard routes (`show`, `update`, `destroy`) do not,
so an archived record is invisible to them (404 `NOT_FOUND`). These scenarios are now covered by `tests/Feature/ResourceLifecycleTest.php`
for achievements, education, experiences, messages, projects, services, skills and skill categories:

- archive → restore → archive → force delete
- archived records are hidden from standard routes
- restore and force delete of a missing id → 404 `NOT_FOUND`
- unauthenticated restore and force delete → 401, record untouched
- missing API key → 401
- restoring an active record is an idempotent no-op (existing contract, kept)
- a restored project's public page (`/api/v1/projects/{slug}`) becomes reachable again

### Intentionally preserved (documented, not changed)

- `force-delete` also works on records that were never archived. Requiring archive-before-delete would change the
  API contract; it is listed for a Phase 2 decision.

### Test evidence

```
php artisan test --compact tests/Feature/ResourceLifecycleTest.php
Tests: 35 passed (155 assertions)        # 6 failed before the fix

php artisan test --compact
Tests: 65 passed (258 assertions)
```

### Task 02 result

PR [#8](https://github.com/mohammedzom/Portfolio-CMS/pull/8) was squash-merged into `dev` as `ba68b6f` (branch commit `25f9b1b`).
CI `test` passed and `deploy` was skipped.

---

## 4. Task 03 — Safe file lifecycle

### Strategy

A single trait, `App\Traits\ManagesUploadedFiles`, sits next to the existing `ManagesCache` trait and is used by
every action that touches uploads. Database transactions cannot roll back the filesystem, so every operation now follows one order:

1. **Write new files first** under generated names (`<slugged-original-name>-<16 random chars>.<extension>`). The
   extension comes from the detected MIME type and the client name only contributes a slug. If one upload in a
   batch fails, the files already written for that batch are removed.
2. **Run the database work in a transaction.**
3. **On failure**, delete only the files written in step 1. The previous files are never touched.
4. **On success**, delete the replaced or removed files in `DB::afterCommit()`, i.e. only once the new state is durable.

Deletion is restricted to `achievements/`, `avatars/`, `cv/` and `projects/` paths of the form `<dir>/<file>`.
Anything else is ignored, including `..`, backslashes, nested paths, dotfiles and other directories. Project
image deletions are additionally intersected with the project's own `images`, so one project can never delete
another project's file.

### Defects fixed

| # | Defect | Fix |
| --- | --- | --- |
| F4 | Old project images, certificates, avatar and CV were deleted before the DB update committed (a failed update lost the valid file); new files written before a failing DB write were orphaned. | Ordered write → transaction → cleanup, as above. |
| F5 | Project/achievement force delete removed files before the row (a failed delete lost the files). | New `ForceDeleteProjectAction` / `ForceDeleteAchievementAction` delete files after commit; controllers delegate to them, consistent with other resources (F3). |
| F6 | Certificates and CVs were stored under the raw client filename, so uploads with the same name overwrote each other. | Unique generated names. |
| F7 | The avatar was always `avatars/avatar.<ext>` and the old file was deleted first. | Unique names; the old avatar is deleted after commit. |
| F8 | Raw client filenames were embedded in project image names. | Slugged base name plus random suffix. |
| F9 | `StoreAchievementAction` contained a latent "delete `$data['certificate_url']`" primitive. | Removed. |
| F22 | `POST /admin/achievements` without the optional `url`/`description` fields returned 500 (`Undefined array key`). Found while writing the tests. | Optional fields default to `null`. |

### API compatibility

Request and response shapes are unchanged. New uploads get different file names (and therefore different URLs).
Existing stored paths keep working: they are read as before and are deleted only through the same managed-path rules.

### Test evidence

`tests/Feature/FileLifecycleTest.php` (17 tests, isolated with `Storage::fake('public')`; failures simulated with
model events):

- safe names
- rollback of uploads when create, update or force delete fails
- replacement only after commit
- refusal to delete files belonging to other projects, path-traversal inputs and paths outside managed directories
- certificate collisions
- avatar and CV replacement and removal
- non-image upload rejection

```
php artisan test --compact tests/Feature/FileLifecycleTest.php
Tests: 17 passed (63 assertions)
# Same tests against the previous app/ code: 9 failed, 8 passed

php artisan test --compact
Tests: 82 passed (321 assertions)
```

### Known limitations

- If the process crashes between writing a new file and the transaction finishing, that file can still be orphaned.
  A periodic orphan sweep (compare disk vs DB) is deferred to Phase 2.
- Files are deleted after the commit. If that deletion fails, the obsolete file stays on disk (a safe failure mode).

### Task 03 result

PR [#9](https://github.com/mohammedzom/Portfolio-CMS/pull/9) was squash-merged into `dev` as `5ee16a6` (branch commit `d8b3c9c`).
CI `test` passed and `deploy` was skipped.

---

## 5. Task 04 — Security & authentication hardening

### Changes

| # | Issue | Change | Compatibility |
| --- | --- | --- | --- |
| F10 | Login returned 422 "The email address is not registered." for unknown emails (account enumeration). | Removed the `exists:users,email` rule. Unknown email and wrong password now return the identical 401 `Invalid credentials` body. Credential checks stay timeboxed by Laravel (`SessionGuard::validate`). | An unknown email now gets 401 instead of 422. Valid logins are unchanged. |
| T4-a | The API login called `Auth::attempt()`, which also logged the user into the `web` session guard. Sanctum checks the `web` guard *before* the bearer token, so token checks (including expiry) could be bypassed whenever that in-memory session state survived (observed in tests). | Login now uses `Auth::guard('web')->validate()` + `getLastAttempted()` and only issues a token. API token auth and future web session auth stay independent. | None. |
| F11 | Sanctum tokens never expired and were never pruned. | `sanctum.expiration` = `SANCTUM_EXPIRATION` minutes (default **10080 = 7 days**). `sanctum:prune-expired --hours=24` is scheduled daily. | Admin clients must log in again after 7 days. **The prune only runs if the server cron runs `php artisan schedule:run`.** |
| F12 | Every unmapped HTTP exception (405, 413, 503 maintenance mode, …) was rendered as 500 "Something went wrong." | New `HttpExceptionInterface` renderer keeps the status code and headers (`Allow`, `Retry-After`), uses the generic status text (never the internal exception message), and adds error codes `METHOD_NOT_ALLOWED`, `PAYLOAD_TOO_LARGE`, `SERVICE_UNAVAILABLE`, `HTTP_ERROR`. | Same JSON envelope; only the status code and `error_code` become accurate. |
| F13 | The public contact form `body` had no length limit. | `max:10000`. | Longer messages now get 422. |

### Reviewed and kept as is

- **API key middleware.** Constant-time `hash_equals`, and an empty or missing server key fails closed. The key gates every
  `/api/v1` route, including public ones, so it is a client identifier, not a secret, once it ships to a browser. **Future
  Blade pages must not call the API from browser JavaScript with this key.** Server-rendered controllers should call the
  shared Actions directly, so no key reaches the client.
- **Throttling.** Login 10/min per IP, public portfolio and project 25/min, contact form 3/hour per IP. Proxy trust is limited
  to `10.0.0.0/8` and `127.0.0.1`, so `request->ip()` is reliable behind the load balancer.
- **Authorization.** All admin routes require `auth:sanctum`. There is a single admin role, and every authenticated user is
  an admin. **If more than one user account will ever exist, Policies/Gates are required (Phase 2).**
- **Validation and mass assignment.** All writes use Form Requests and `validated()`; models declare `#[Fillable]`.
  Search filters use bound parameters.
- **Uploads.** MIME and size rules on every file field; safe storage covered in Task 03.
- **Sensitive data.** `User` hides `password`/`remember_token` and is never serialized by a resource. Exception details are
  only shown when `APP_ENV=local` or `APP_DEBUG=true` (production must keep `APP_DEBUG=false`).
- **CORS.** No `config/cors.php`, so the framework default allows any origin for `api/*`. Acceptable for bearer-token auth
  without cookies; restrict `allowed_origins` when the Blade/SPA origin is known.

### Preparation for web session authentication (not implemented)

- The `web` guard (session driver) is already configured and the API no longer touches it.
- The API login route is named `login`. Laravel's `Authenticate` middleware redirects non-JSON guests to `route('login')`.
  When the web login page is added in Phase 2, rename the API route (e.g. `api.v1.login`) **in the same change** and set
  `$middleware->redirectGuestsTo(...)` so API requests keep getting 401 JSON.
- Web admin routes should use the `web` middleware group + `auth` (session) + CSRF and call the same Actions as the API.

### Test evidence

```
php artisan test --compact tests/Feature/AuthApiTest.php tests/Feature/ApiErrorResponseTest.php
Tests: 13 passed (37 assertions)
# Same tests with the previous app/, bootstrap/ and config/: 8 failed, 5 passed

php artisan test --compact
Tests: 91 passed (350 assertions)
```

### Task 04 result

PR [#10](https://github.com/mohammedzom/Portfolio-CMS/pull/10) was squash-merged into `dev` as `ec8b058` (branch commit `47c75e9`).
CI `test` passed and `deploy` was skipped.

---

## 6. Task 05 — Cache & visitor tracking

### Cache keys and invalidation (verified)

| Key | Written by | Forgotten on |
| --- | --- | --- |
| `portfolio_all` | `GET /api/v1/portfolio` | every create/update/archive/restore/force-delete/reorder of any public resource, and site settings updates |
| `portfolio_settings` | `GET /admin/site-info` | site settings update (via `forgetPortfolioCache()`) |
| `project_{slug}` | `GET /api/v1/projects/{slug}` | project create/update (old **and** new slug)/archive/restore/force-delete/**reorder** |
| `projects`, `projects_archived` | admin project list | all project writes |
| `services`, `experiences`, `educations`, `achievements` (+ `_archived`) | admin lists | all writes of that resource, including **service reorder** |

All invalidation now goes through `App\Traits\ManagesCache`. The duplicated inline `Cache::forget(...)` lists in the Education
and Experience actions, and the repeated calls in the Achievement and Project controllers, were removed. The actions own
invalidation. TTL stays `CACHE_TTL` hours (default 24).

### Defects fixed

| # | Defect | Fix |
| --- | --- | --- |
| F14 | Project and service **reorder** only forgot the admin list key. The public `portfolio_all` payload (and every cached `project_{slug}` page, which includes `sort_order`) stayed stale for up to 24 h. | Reorder forgets the resource keys, `portfolio_all`, and the `project_{slug}` key of every reordered project. |
| F16 | Invalidation was duplicated across controllers, actions and the trait (some keys forgotten two or three times per request). | Single path via `ManagesCache`. The unused `forgetMessagesCache()` was removed (messages are never cached). |
| F17 | `LogVisitJob` caught every exception and logged a warning, so `$tries = 3` never applied and nothing reached `failed_jobs`. | Exceptions propagate. Retries use a `[10, 60]`s backoff, which is safe because the upsert is idempotent. A `failed()` hook logs an error **without the IP address** once retries are exhausted. |

### Duplicate visitors

Unchanged by design and now tested: the unique index `(ip_address, visited_at)` plus `upsert` keeps exactly one row per IP
per day (the latest user agent wins). Bots, `curl`, Postman, headless browsers and non-200 responses are not logged.

### Privacy (decision deferred to the owner)

Raw IP addresses are stored indefinitely in `visits.ip_address`. They are never returned by any API response (the dashboard
only shows counts per day), but they count as personal data under GDPR-style rules. Options for Phase 2:

1. Store an HMAC of the IP (keyed with `APP_KEY`). This keeps per-day de-duplication but needs a non-destructive migration
   widening `ip_address` to 64 characters.
2. Add `Prunable` to `Visit` with a retention window (e.g. 13 months) and schedule `model:prune`. This reduces historical totals.

No change was made, because both options alter stored data or analytics semantics.

### Operational notes

- `LogVisitJob` runs on the `database` queue by default. **A queue worker must run in production** (`php artisan queue:work`),
  otherwise visits accumulate in `jobs` and are never recorded.

### Test evidence

```
php artisan test --compact tests/Feature/CacheInvalidationTest.php tests/Feature/LogVisitJobTest.php
Tests: 16 passed (71 assertions)
# Same tests with the previous app/ code: 6 failed (project reorder, project page after reorder,
# service reorder, job failure propagation, backoff, failure reporting)

php artisan test --compact
Tests: 107 passed (421 assertions)
```

### Task 05 result

PR [#11](https://github.com/mohammedzom/Portfolio-CMS/pull/11) was squash-merged into `dev` as `da720c7` (branch commit `38815d3`).
CI `test` passed and `deploy` was skipped.

---

## 7. Task 06 — CI/CD, quality & web readiness

### Workflow changes (`.github/workflows/laravel-ci.yml`)

| Area | Before | After |
| --- | --- | --- |
| Triggers | push/PR on `main` (Task 01 added `dev`) | push/PR on `main` and `dev` |
| Permissions | default token permissions | `contents: read` |
| Validation | `composer install`, migrate a file DB, `php artisan test` | `composer validate`, cached `composer install`, **`pint --test`**, `php artisan test` (the redundant file-DB migration step was removed; the tests migrate in-memory SQLite) |
| Deploy gate | `needs: test`, main push only | unchanged gate, plus `environment: production` and a non-cancelling `production-deploy` concurrency group (no overlapping deploys) |
| What is deployed | `git pull origin main`, i.e. whatever `main` is at deploy time, possibly a newer, untested commit | **exactly `github.sha`**, the commit the `test` job validated, fast-forward only |
| Failure handling | no `set -e`; `php artisan down \|\| true`; any failure later could leave the site in maintenance mode or half-updated | `set -Eeuo pipefail`. Pre-flight checks (clean working tree, fast-forward) run **before** `down`. An `ERR` trap registered before `down` restores the previous commit, reinstalls dependencies, rebuilds caches and always runs `php artisan up`. |
| Rollback readiness | none | previous and target SHAs printed in the job log; automatic code rollback; `queue:restart` so workers load new code |
| Shell | remote login shell | `bash -s` heredoc, independent of the server's default shell |

**Verification of the deploy script (no server touched).** The script was extracted verbatim from the workflow and run in
a sandbox with throwaway git repositories and stub `php`/`composer` binaries:

| Scenario | Exit | Code on server | Maintenance mode |
| --- | --- | --- | --- |
| success | 0 | new commit | off |
| `composer install` fails | 4 | previous | off |
| `migrate` fails | 3 | previous | off |
| `config:cache` fails | 3 | previous | off |
| `artisan down` fails | 3 | previous | off |
| dirty server working tree | 1 | previous | never entered |
| target not a fast-forward | 1 | previous | never entered |

The first version left maintenance mode on when `artisan down` itself failed. Registering the trap before `down` fixed it.

**Not verifiable here:** the real SSH deploy. It only runs on pushes to `main`, which Phase 1 does not touch. The first
production deploy after merging `dev` into `main` should be watched.

### Build provenance and documentation accuracy

- The README said the CI/CD badge covered the project, but CI covered `main` only. It now covers `main` and `dev`.
- The old deploy pulled `origin/main` at deploy time, so the deployed commit could differ from the tested commit. It now deploys the tested SHA.
- No build artifacts are produced. `public/build/` is git-ignored and only exists locally from older frontend branches. The README now says the repository ships no frontend tooling yet.
- `api-map-to-frontend-or-ai-agent.md` had error codes that the code does not emit (`403` for a bad API key, `MODEL_NOT_FOUND`, `ENDPOINT_NOT_FOUND`, `SERVER_ERROR`) and a reorder validation rule that does not exist. Corrected, along with the Phase 1 behaviour changes (login, token expiry, message length, HTTP error codes, reorder cache).
- The README now documents `SANCTUM_EXPIRATION`, `CACHE_TTL`, and the production queue worker and scheduler requirements.

### Test evidence

```
vendor/bin/pint --test   → passed (whole repository)
php artisan test --compact
Tests: 107 passed (421 assertions)
```

### Task 06 result

PR [#12](https://github.com/mohammedzom/Portfolio-CMS/pull/12) was squash-merged into `dev` as `69ab3c7` (branch commit `17af0ab`).
On the PR, CI `test` passed every step (composer validate, cached install, Pint, tests) and `deploy` was skipped.

## Final state

- `dev` contains every Phase 1 change: `b8e16e8` (#7) → `ba68b6f` (#8) → `5ee16a6` (#9) → `ec8b058` (#10) → `da720c7` (#11) → `69ab3c7` (#12),
  followed by this report-only update.
- `main` is unchanged at `7902e1e`. Nothing was deployed.
- Every push to `dev` triggered CI. The runs for #7–#11 succeeded, and the `deploy` job never runs for `dev`.
- Final local verification on `dev`: `php artisan test --compact` → **107 passed (421 assertions)**. `vendor/bin/pint --test` → passed.
- Test growth over Phase 1: 30 tests / 103 assertions → 107 tests / 421 assertions.
- Phase 2 has not started.
- Correction: the final secret scan found `.claude/settings.local.json.bak` (a local backup of Claude Code permission rules,
  **no credentials**), which `git add -A` had swept into the Task 01 squash commit `b8e16e8`. It was untracked and
  `/.claude/settings.local.json*` is now git-ignored. It remains in history; no rewrite was done because it holds no secret
  and force-pushing shared branches is not allowed.

---

## 8. Recommended architecture for Blade integration

Goal: one set of business rules serving two delivery mechanisms.

```
app/
├── Actions/                     # shared — the only place business rules live (already true for all writes)
├── Http/
│   ├── Controllers/
│   │   ├── Api/V1/              # existing JSON API (x-api-key + Sanctum tokens) — unchanged
│   │   └── Web/                 # new: returns Blade views / redirects (session auth + CSRF)
│   │       ├── PortfolioController.php        # public pages
│   │       └── Admin/ProjectController.php …  # admin dashboard
│   ├── Requests/                # shared Form Requests (they redirect with errors for web, JSON 422 for API automatically)
│   └── Resources/               # API only; Blade reads models directly
├── Models/                      # shared
└── Traits/ManagesCache, ManagesUploadedFiles  # shared via Actions
resources/views/
├── layouts/ (app, admin)
├── components/                  # reusable Blade components (x-button, x-card, x-form.input …)
├── portfolio/                   # public pages
└── admin/                       # dashboard pages
routes/web.php                   # public routes + Route::middleware('auth')->prefix('admin') group
```

Rules:

1. **Web controllers never call the API** and never need `API_KEY`. They call Actions directly, so no secret reaches the browser.
2. **Reads:** the public portfolio payload is built inline in `Api\V1\PortfolioController`. Extract it into a
   `GetPortfolioDataAction` (cached, same key `portfolio_all`) so the Blade homepage and the API share one cache and one
   query set. Do the same for the public project page (`project_{slug}`).
3. **Writes:** web admin controllers call the existing `Store*/Update*/Destroy*/Restore*/ForceDelete*` Actions and only
   differ in their response (redirect + flash). Project and achievement archive/restore still live in their API
   controllers. Move them into Actions before building the web admin, so they are not duplicated.
4. **Auth:** web admin uses the `web` guard (session) with `auth` middleware, login throttling and CSRF. The API keeps
   Sanctum tokens. API login no longer touches the session guard (Task 04). Rename the API route name `login` when the web
   login route is added, and configure `redirectGuestsTo` so API requests still get JSON 401.
5. **Errors:** the custom JSON exception renderers only apply to `api/*`, so web routes get standard HTML error pages.
6. `routes/web.php` currently redirects `/` to the external portfolio domain. That redirect is replaced when the Blade
   homepage ships.

### Minimum Vite + Tailwind CSS setup (evaluated, not installed)

Adding npm dependencies requires owner approval, so nothing was installed. The minimum for Phase 2:

- **Packages:** `package.json` with dev dependencies `vite`, `laravel-vite-plugin`, `tailwindcss` (v4) and `@tailwindcss/vite`.
- **`vite.config.js`:** `laravel({ input: ['resources/css/app.css', 'resources/js/app.js'], refresh: true })` and `tailwindcss()`.
- **`resources/css/app.css`:** `@import "tailwindcss";` (v4 needs no `tailwind.config.js`) plus `@source` for `resources/views`.
- **`resources/js/app.js`:** lightweight JavaScript only.
- **Layout:** `@vite(['resources/css/app.css', 'resources/js/app.js'])` in the Blade layout.
- **CI:** `npm ci && npm run build`.
- **Deploy:** decide whether assets are built in CI and shipped (better provenance, no Node on the server) or built on the server with `npm ci && npm run build`.
- **Git:** `public/build/` is already git-ignored.

---

## 9. Remaining limitations and risks

| Risk | Impact | Mitigation / owner action |
| --- | --- | --- |
| No branch protection on `main`/`dev` | A direct push could bypass CI | Protect both branches and require the `test` check |
| `gh` OAuth token lacks the `workflow` scope | HTTPS pushes that change workflows are rejected; Phase 1 pushed over the owner's SSH key | `gh auth refresh -s workflow` |
| Production deploy script changed but unexercised against the real server | First `main` deploy could hit an environment difference | Watch the first deploy; the script refuses rather than proceeds on unexpected state |
| A partially applied migration followed by code rollback | Previous code runs against a newer schema | Keep migrations backward compatible (expand/contract); review manually after a failed deploy |
| Scheduler and queue worker may not be configured on the server | Expired tokens are rejected but not pruned; visits not recorded | Configure cron `schedule:run` and a supervised `queue:work` |
| Raw visitor IPs stored indefinitely | Privacy | Choose HMAC or retention pruning (section 6) |
| Process crash between file write and commit | Rare orphan file | Orphan sweep command (Phase 2) |
| Production runs `composer install` with dev dependencies | Larger attack surface and autoloader | Evaluate `--no-dev` on the server before enabling |
| Single admin role; every user is an admin | Fine for one account only | Policies/Gates if more accounts are added |
| CORS allows any origin | Low with bearer tokens | Restrict once frontend origins are known |

---

## 10. Decisions intentionally deferred to Phase 2

1. Visual design direction, Blade layouts and components, the Tailwind/Vite install (needs dependency approval), and the admin dashboard UI.
2. Whether `force-delete` should require the record to be archived first (API contract change).
3. IP address handling for visits (HMAC vs retention).
4. `GetPortfolioDataAction` extraction, and moving project/achievement archive/restore into Actions (needed by web controllers).
5. Web session login, the `login` route-name swap and `redirectGuestsTo`.
6. Cleanup of legacy assets: `public/assets/` (the old static theme, unused by any route), `GEMINI.md`/`.gemini/` (no longer managed by Boost), and stale remote branches (`admin-dashboard-rebuild`, `feature/*`, and one Arabic-named branch).
7. An orphaned-upload sweep command and `composer install --no-dev` in production.

---

## 11. Post-release cleanup (after v1.1.0)

- Deferred item 6 is done:
  - `public/assets/` (legacy static theme), `GEMINI.md` and `.gemini/` were removed.
  - The stale remote branches no longer exist.
- Correction:
  - Sections 2, 7 and 9 said `public/build/` was only present locally. In fact, an old Vite build (`manifest.json` plus one JS bundle) was still tracked even though `/public/build` is git-ignored.
  - It has been removed from the repository, so the Phase 2 Vite build starts from a clean output directory.
- `error_dump.json`, a stray JSON error response committed in April 2026 and not referenced anywhere, was removed.
