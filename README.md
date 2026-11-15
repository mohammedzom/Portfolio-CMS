# Portfolio CMS API 🚀

<a href="https://mohammedzomlot.dev"><img src="./portfolio.png" alt="Portfolio CMS" width="100%"></a>

A modern, high-performance headless Content Management System (CMS) built with **Laravel 13** and **PHP 8.5**. This application serves as a pure backend API for my personal portfolio at [mohammedzomlot.dev](https://mohammedzomlot.dev). It is designed with an API-first approach, providing exclusively RESTful JSON endpoints without any integrated frontend tooling or templates.

[![Laravel CI/CD](https://github.com/mohammedzom/Portfolio-CMS/actions/workflows/laravel-ci.yml/badge.svg)](https://github.com/mohammedzom/Portfolio-CMS/actions/workflows/laravel-ci.yml)

## 🌟 Features

- **Project Management:** Complete CRUD with multi-image uploads, slug generation, and order sorting.
- **Skill Tracking:** Categorize and rank technical skills using dedicated Skill Categories.
- **Professional Showcase:** Manage Services, Experiences, Educations, and Achievements to display a complete professional history.
- **Site Settings:** Manage global portfolio information (bio, social links, etc.).
- **Messaging System:** Secure contact form endpoints with read/unread tracking.
- **Admin Dashboard:** Aggregated analytics and recent data overview.
- **Robust Authentication:** Secured via Laravel Sanctum and API key validation.

## 🏗️ Architecture & Best Practices

This project strictly adheres to modern Laravel architecture and "Skinny Controller" principles:

- **API first:** All functionality is exposed through versioned REST endpoints (`/api/v1`) using Eloquent API Resources. The repository currently ships no frontend build tooling or Blade views. A server-rendered Blade + Tailwind CSS frontend is planned and will reuse the same Actions (see `docs/PHASE-1-REPORT.md`).
- **Action Classes:** Business logic (create/update/archive/restore/force-delete, file uploads, reordering) lives in single-purpose Action classes using `lorisleiva/laravel-actions`, so API controllers stay thin and future web controllers can share the same logic.
- **Data Integrity:** Multi-step writes run in `DB::transaction()`. Uploaded files are coordinated with the database: new files are written first under generated names, removed if the transaction fails, and replaced files are deleted only after the commit (`App\Traits\ManagesUploadedFiles`).
- **Performance:** Read-heavy endpoints (like the public portfolio) are cached for `CACHE_TTL` hours. Every write, archive, restore, force delete and reorder invalidates the affected keys through `App\Traits\ManagesCache`.
- **Type Safety:** Strict return type hinting (`JsonResponse`) across all API controllers.
- **Validation:** Dedicated `FormRequest` classes handle incoming data validation and custom error messages.

## 🧪 Testing

The API is covered by feature tests written in **Pest** (`pestphp/pest`). They run against in-memory SQLite and a faked
public disk, so no real data or files are touched.

```bash
composer run test          # full suite
vendor/bin/pint --test     # code style check (CI runs both)
```

## 🔀 Branches & CI/CD

- `main` is the production branch; `dev` is the integration branch. Work happens on task branches that are merged into `dev` through pull requests.
- GitHub Actions (`.github/workflows/laravel-ci.yml`) runs `composer validate`, Pint and the full test suite for every push and pull request on `main` and `dev`.
- Deployment runs **only** for pushes to `main`, and only after the `test` job succeeds. It deploys the exact commit that was tested (fast-forward only), refuses to start on a dirty server working tree, and restores the previous commit and leaves maintenance mode if any step fails. Partially applied migrations must be reviewed manually.

## 🛠️ Tech Stack

- **Framework:** Laravel 13
- **Language:** PHP 8.5
- **Authentication:** Laravel Sanctum
- **Testing:** Pest v4
- **Formatting:** Laravel Pint

## 🚀 Getting Started

1. Clone the repository.
2. Run the setup script which will install dependencies, copy `.env.example` to `.env`, generate the app key, and run migrations:
   ```bash
   composer run setup
   ```
3. Link storage (for project images and uploads):
   ```bash
   php artisan storage:link
   ```
4. Start the local development server, queue worker and log viewer (API only):
   ```bash
   composer run dev
   ```

### Configuration

| Variable | Purpose |
| --- | --- |
| `API_KEY` | Required `x-api-key` header for every `/api/v1` request. It identifies a client; do not embed it in browser JavaScript you consider private. |
| `SANCTUM_EXPIRATION` | Minutes before admin API tokens expire (default `10080`, i.e. 7 days). |
| `CACHE_TTL` | Hours that cached API responses are kept (default `24`). |

### Production requirements

- A queue worker (`php artisan queue:work`). Visit tracking is queued on the `database` queue by default.
- The scheduler (`* * * * * php artisan schedule:run`), which prunes expired Sanctum tokens daily.
- `APP_DEBUG=false`.

## 📚 API Documentation

A comprehensive Postman collection is available in the `resources/` directory (`Portfolio-cms.postman_collection.json`) containing all available endpoints, required headers, and payload examples for both the public API and the protected Admin API.
