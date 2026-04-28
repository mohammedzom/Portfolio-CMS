# Portfolio CMS API 🚀
A modern, high-performance headless Content Management System (CMS) built with **Laravel 13** and **PHP 8.5**. This API serves as the robust backend for a personal portfolio, featuring a "Cosmic Dark" aesthetic on the upcoming frontend.

[![Laravel CI/CD](https://github.com/mohammedzom/Portfolio-CMS/actions/workflows/laravel-ci.yml/badge.svg)](https://github.com/mohammedzom/Portfolio-CMS/actions/workflows/laravel-ci.yml)

## 🌟 Features

- **Project Management:** Complete CRUD with multi-image uploads, slug generation, and order sorting.
- **Skill Tracking:** Categorize and rank technical and tooling skills.
- **Services & Experiences:** Showcase professional history and offered services.
- **Site Settings:** Manage global portfolio information (bio, social links, resume).
- **Messaging System:** Secure contact form endpoints with read/unread tracking.
- **Admin Dashboard:** Aggregated analytics and recent data overview.
- **Robust Authentication:** Secured via Laravel Sanctum.

## 🏗️ Architecture & Best Practices

This project strictly adheres to modern Laravel architecture and "Skinny Controller" principles:

- **Action Classes:** Complex business logic (like handling multiple file uploads and database transactions) is extracted into single-purpose Action classes using `lorisleiva/laravel-actions` (e.g., `StoreProjectAction`, `UpdateProjectAction`).
- **Data Integrity:** Database operations involving multiple steps or file uploads are wrapped in `DB::transaction()` to prevent data corruption.
- **Performance:** Extensive use of Laravel Cache (`Cache::remember`, `Cache::forget`) to serve read-heavy endpoints (like the public portfolio) blazingly fast. 
- **Type Safety:** Strict return type hinting (`JsonResponse`) across all API controllers.
- **Validation:** Dedicated `FormRequest` classes handle incoming data validation and custom error messages.
- **API Resources:** Consistent JSON output formatted via Laravel Eloquent API Resources.

## 🧪 Testing

The API is covered by feature tests written in **Pest** (`pestphp/pest`).
To run the test suite:

```bash
vendor/bin/pest
```

## 🛠️ Tech Stack

- **Framework:** Laravel 13
- **Language:** PHP 8.5
- **Authentication:** Laravel Sanctum
- **Testing:** Pest v4
- **Formatting:** Laravel Pint

## 🚀 Getting Started

1. Clone the repository.
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy `.env.example` to `.env` and configure your database and cache settings.
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Run migrations and seed the database:
   ```bash
   php artisan migrate --seed
   ```
6. Link storage (for project images and CV uploads):
   ```bash
   php artisan storage:link
   ```
7. Start the local development server:
   ```bash
   php artisan serve
   ```

## 📚 API Documentation

A comprehensive Postman collection is available in the `resources/` directory (`Portfolio-cms.postman_collection.json`) containing all available endpoints, required headers, and payload examples for both the public API and the protected Admin API.
