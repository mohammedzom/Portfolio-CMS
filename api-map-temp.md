# Portfolio CMS API Map

Generated from `routes/api.php`, `app/Http/Controllers/Api`, `app/Http/Requests`, `app/Http/Resources`, `app/Models`, related actions, middleware, and API exception handling.

## Global Contract

- Base prefix: `/api/v1`
- All routes require header `x-api-key: <API_KEY>` via `check-api-key`.
- Admin routes under `/api/v1/admin/*` also require Sanctum bearer token: `Authorization: Bearer <token>`.
- Public throttles:
  - `POST /admin/login`: `throttle:10,1`
  - `POST /message`: `contact_form`, 3 requests/hour per IP
  - `GET /portfolio`: `throttle:25,1`
- Success envelope:

```json
{
  "success": true,
  "message": "Success message",
  "data": {}
}
```

- Controller-level error envelope:

```json
{
  "success": false,
  "message": "Error message",
  "data": [],
  "error_code": null
}
```

- Exception error envelope:

```json
{
  "success": false,
  "message": "Error message",
  "data": null,
  "error_code": "ERROR_CODE"
}
```

- Standard API exception codes:
  - `403 FORBIDDEN`: missing/invalid `x-api-key`, message `You are not authorized to perform this action.`
  - `401 UNAUTHENTICATED`: missing/invalid Sanctum token, message `Unauthenticated. Please log in first.`
  - `404 MODEL_NOT_FOUND`: missing model, message `No results found for {Model}.`
  - `404 ENDPOINT_NOT_FOUND`: unknown API endpoint.
  - `422 VALIDATION_ERROR`: `data` is `{ field: ["message"] }`.
  - `409 CONFLICT`: unique constraint conflict.
  - `500 SERVER_ERROR`: unhandled exception.

## Resource Shapes

### Project

```json
{
  "id": 1,
  "title": "string",
  "slug": "string",
  "description": "string|null",
  "category": "Web|App|Mobile|Script|Other",
  "tech_stack": ["string"],
  "images": ["https://host/storage/projects/file.webp"],
  "live_url": "string|null",
  "repo_url": "string|null",
  "is_featured": true,
  "sort_order": 1,
  "deleted_at_human": "string|null",
  "deleted_at": "datetime|null",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### Service

```json
{
  "id": 1,
  "title": "string",
  "description": "string",
  "icon": "url|null",
  "tags": ["string"],
  "sort_order": 1,
  "deleted_at": "datetime|null",
  "deleted_at_human": "string|null",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### Skill

```json
{
  "id": 1,
  "name": "string",
  "proficiency": 0,
  "icon": "url|null",
  "category": "Skill category name",
  "deleted_at_human": "string|null",
  "deleted_at": "datetime|null",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

Important: the `Skill` model belongs to `SkillCategory`, and skill create/update requests require `skill_category_id`. There is currently no API endpoint for listing or managing skill categories.

### Experience

```json
{
  "id": 1,
  "job_title": "string",
  "company": "string",
  "description": "string",
  "period": "2024 - Present",
  "deleted_at_human": "string|null",
  "deleted_at": "datetime|null",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

Note: resource does not expose raw `start_date`, `end_date`, or `is_current`, only computed `period`.

### Education

```json
{
  "id": 1,
  "degree": "string",
  "institution": "string",
  "field_of_study": "string",
  "start_year": 2020,
  "end_year": 2024,
  "gpa": 95,
  "description": "string|null",
  "deleted_at_human": "string|null",
  "deleted_at": "datetime|null",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### Achievement

```json
{
  "id": 1,
  "title": "string",
  "issuer": "string",
  "date": "date",
  "url": "url|null",
  "description": "string|null",
  "certificate_url": "https://host/storage/achievements/file.pdf|null",
  "deleted_at_human": "string|null",
  "deleted_at": "datetime|null",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### Message

```json
{
  "id": 1,
  "name": "string",
  "email": "string",
  "subject": "string|null",
  "body": "string",
  "is_read": true,
  "read_at_human": "string|null",
  "read_at": "YYYY-MM-DD HH:mm|null",
  "deleted_at_human": "string|null",
  "deleted_at": "datetime|null",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### Site Settings

```json
{
  "id": 1,
  "first_name": "string",
  "last_name": "string",
  "full_name": "string string",
  "tagline": "string|null",
  "bio": "string|null",
  "about_me": "string|null",
  "avatar": "https://host/storage/avatars/avatar.webp|null",
  "cv_file": "https://host/storage/cv/file.pdf|null",
  "url_prefix": "string|null",
  "url_suffix": "string|null",
  "languages": [{"name": "string", "level": "string"}],
  "email": "string|null",
  "phone": "string|null",
  "location": "string|null",
  "social_links": [{"name": "string", "url": "url"}],
  "years_experience": 0,
  "projects_count": 0,
  "clients_count": 0,
  "available_for_freelance": true,
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

## Public Endpoints

### `POST /api/v1/admin/login`

- Auth: `x-api-key` only.
- Body: JSON.
- Validation:
  - `email`: required, email, exists in `users.email`
  - `password`: required, string
- Success `200`:

```json
{
  "success": true,
  "message": "Logged in successfully.",
  "data": {
    "token": "sanctum_plain_text_token"
  }
}
```

- Invalid password/user after validation: `401`

```json
{
  "success": false,
  "message": "Invalid credentials",
  "data": {
    "error": "Invalid credentials"
  },
  "error_code": null
}
```

### `POST /api/v1/message`

- Auth: `x-api-key` only.
- Body: JSON.
- Validation:
  - `name`: required, string, max 255
  - `email`: required, email, max 255
  - `subject`: required, string, max 255
  - `body`: required, string
- Success `201`:

```json
{
  "success": true,
  "message": "Thank you for your message. I will get back to you as soon as possible.",
  "data": []
}
```

### `GET /api/v1/portfolio`

- Auth: `x-api-key` only.
- Query: none.
- Success `200`:

```json
{
  "success": true,
  "message": "Portfolio Data Retrieved Successfully.",
  "data": {
    "skills": {
      "Category Name": ["SkillResource"]
    },
    "projects": ["ProjectResource"],
    "services": ["ServiceResource"],
    "information": "SiteSettingsResource",
    "experiences": ["ExperienceResource"],
    "achievements": ["AchievementResource"],
    "educations": ["EducationResource"]
  }
}
```

## Admin Endpoints

### `POST /api/v1/admin/logout`

- Auth: `x-api-key` + Sanctum bearer token.
- Body: none.
- Deletes current access token.
- Success `200`: `data: []`, message `Logged out successfully.`

### `GET /api/v1/admin/dashboard`

- Auth: `x-api-key` + Sanctum bearer token.
- Query: none.
- Success `200`:

```json
{
    "success": true,
    "message": "Dashboard Data Retrieved Successfully",
    "data": {
        "projects": [
            {
                "id": 1,
                "title": "Shaghalni — Backoffice Dashboard",
                "slug": "shaghalni-backoffice-dashboard",
                "description": "A modern, feature-rich administration panel for the Shaghalni Job Platform. This panel gives platform administrators and company managers full control over every entity inside the system.",
                "category": "Web",
                "tech_stack": [
                    "PHP 8.5",
                    "Laravel 13",
                    "Tailwind CSS v4",
                    "Alpine.js v3",
                    "MySQL"
                ],
                "images": [
                    "https://api.mohammedzomlot.online/storage/projects/project_Screenshot_2026-04-25_12-52-41_69ec8f181caf5.png"
                ],
                "live_url": "https://dashboard.mohammedzom.online",
                "repo_url": "https://github.com/mohammedzom/job-backoffice",
                "is_featured": true,
                "sort_order": 1,
                "deleted_at_human": null,
                "deleted_at": null,
                "created_at": "2026-04-25T14:57:00.000000Z",
                "updated_at": "2026-04-25T14:57:00.000000Z"
            },
            {
                "id": 2,
                "title": "Shaghalni — Job Platform",
                "slug": "shaghalni-job-platform",
                "description": "A modern, bilingual (Arabic/English) job marketplace connecting employers and job seekers. Features integrated Resume Parsing and AI Analysis, providing a seamless experience for job seekers.",
                "category": "Web",
                "tech_stack": [
                    "PHP 8.5",
                    "Laravel 13",
                    "Tailwind CSS v4",
                    "Alpine.js v3",
                    "MySQL"
                ],
                "images": [
                    "https://api.mohammedzomlot.online/storage/projects/project_screencapture-mohammedzom-online-2026-04-25-12_55_22_69ec90431b945.png",
                    "https://api.mohammedzomlot.online/storage/https://api.mohammedzomlot.online/storage/projects/project_screencapture-mohammedzom-online-resumes-2026-04-25-12_57_23_69ec90431be4d.png",
                    "https://api.mohammedzomlot.online/storage/https://api.mohammedzomlot.online/storage/projects/project_screencapture-mohammedzom-online-job-applications-2026-04-25-12_57_08_69ec90431c0c5.png",
                    "https://api.mohammedzomlot.online/storage/https://api.mohammedzomlot.online/storage/projects/project_Screenshot_2026-04-25_12-56-57_69ec90431c33f.png"
                ],
                "live_url": "https://mohammedzom.online",
                "repo_url": "https://github.com/mohammedzom/job-app",
                "is_featured": false,
                "sort_order": 2,
                "deleted_at_human": null,
                "deleted_at": null,
                "created_at": "2026-04-25T14:57:00.000000Z",
                "updated_at": "2026-04-25T14:57:00.000000Z"
            },
            {
                "id": 3,
                "title": "Portfolio CMS",
                "slug": "portfolio-cms",
                "description": "A robust backend content management system designed to manage personal portfolios, including dynamic projects, skills, services, and site settings.",
                "category": "Web",
                "tech_stack": [
                    "PHP",
                    "Laravel",
                    "MySQL"
                ],
                "images": [
                    "https://api.mohammedzomlot.online/storage/projects/project_Screenshot_2026-04-27_06-02-06_69eed1d47d23c.png"
                ],
                "live_url": "https://mohammedzomlot.dev/",
                "repo_url": "https://github.com/mohammedzom/Portfolio-CMS",
                "is_featured": true,
                "sort_order": 3,
                "deleted_at_human": null,
                "deleted_at": null,
                "created_at": "2026-04-25T14:57:00.000000Z",
                "updated_at": "2026-04-27T03:02:44.000000Z"
            },
            {
                "id": 4,
                "title": "HR System API",
                "slug": "hr-system-api",
                "description": "An advanced HR system built for the future. Manage attendance, payroll, leaves, and assets with a powerful RESTful API and sleek interface. Features enterprise-grade security with Laravel Sanctum.",
                "category": "Web",
                "tech_stack": [
                    "PHP",
                    "Laravel 12",
                    "REST API",
                    "MySQL"
                ],
                "images": [
                    "https://api.mohammedzomlot.online/storage/projects/project_screencapture-api-mohammedzom-online-2026-04-25-13_01_59_69ec9314e57d8.png",
                    "https://api.mohammedzomlot.online/storage/projects/project_Screenshot_2026-04-25_13-01-35_69ec9314e5c67.png"
                ],
                "live_url": "https://api.mohammedzom.online/",
                "repo_url": "https://github.com/mohammedzom/HR-System-API",
                "is_featured": false,
                "sort_order": 4,
                "deleted_at_human": null,
                "deleted_at": null,
                "created_at": "2026-04-25T14:57:00.000000Z",
                "updated_at": "2026-04-25T15:07:19.000000Z"
            }
        ],
        "skills": {
            "Backend Development": [
                {
                    "id": 1,
                    "name": "PHP",
                    "proficiency": 95,
                    "icon": "https://cdn-icons-png.flaticon.com/512/5968/5968332.png",
                    "category": "Backend Development",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                },
                {
                    "id": 2,
                    "name": "Laravel",
                    "proficiency": 90,
                    "icon": "https://icon.icepanel.io/Technology/svg/Laravel.svg",
                    "category": "Backend Development",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                },
                {
                    "id": 3,
                    "name": "RESTful APIs",
                    "proficiency": 90,
                    "icon": "https://www.svgrepo.com/show/489281/api.svg",
                    "category": "Backend Development",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                },
                {
                    "id": 4,
                    "name": "MySQL",
                    "proficiency": 90,
                    "icon": "https://icon.icepanel.io/Technology/svg/MySQL.svg",
                    "category": "Backend Development",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                },
                {
                    "id": 5,
                    "name": "Database Design",
                    "proficiency": 85,
                    "icon": "https://cdn-icons-png.flaticon.com/512/2758/2758751.png",
                    "category": "Backend Development",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                }
            ],
            "Programming & Tools": [
                {
                    "id": 9,
                    "name": "Linux/Unix",
                    "proficiency": 95,
                    "icon": "https://icon.icepanel.io/Technology/png-shadow-512/Linux.png",
                    "category": "Programming & Tools",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                },
                {
                    "id": 8,
                    "name": "Git & GitHub",
                    "proficiency": 90,
                    "icon": "https://icon.icepanel.io/Technology/png-shadow-512/GitHub.png",
                    "category": "Programming & Tools",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                },
                {
                    "id": 10,
                    "name": "Postman",
                    "proficiency": 90,
                    "icon": "https://icon.icepanel.io/Technology/svg/Postman.svg",
                    "category": "Programming & Tools",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                },
                {
                    "id": 11,
                    "name": "Python",
                    "proficiency": 85,
                    "icon": "https://icon.icepanel.io/Technology/svg/Python.svg",
                    "category": "Programming & Tools",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                },
                {
                    "id": 12,
                    "name": "C++",
                    "proficiency": 80,
                    "icon": "https://icon.icepanel.io/Technology/svg/C%2B%2B-%28CPlusPlus%29.svg",
                    "category": "Programming & Tools",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                },
                {
                    "id": 13,
                    "name": "Java",
                    "proficiency": 70,
                    "icon": "https://icon.icepanel.io/Technology/svg/Java.svg",
                    "category": "Programming & Tools",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                }
            ],
            "Core Concepts": [
                {
                    "id": 17,
                    "name": "DevOps Basics",
                    "proficiency": 90,
                    "icon": "https://api.mohammedzomlot.online/storage/icons/devops.png",
                    "category": "Core Concepts",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                },
                {
                    "id": 14,
                    "name": "Problem Solving",
                    "proficiency": 80,
                    "icon": "https://api.mohammedzomlot.online/storage/icons/problem_solving.svg",
                    "category": "Core Concepts",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                },
                {
                    "id": 15,
                    "name": "System Design",
                    "proficiency": 80,
                    "icon": "https://api.mohammedzomlot.online/storage/icons/system-design.png",
                    "category": "Core Concepts",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                },
                {
                    "id": 16,
                    "name": "Algorithms",
                    "proficiency": 80,
                    "icon": "https://icon.icepanel.io/Technology/svg/The-Algorithms.svg",
                    "category": "Core Concepts",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                }
            ],
            "Mobile Development": [
                {
                    "id": 6,
                    "name": "Flutter",
                    "proficiency": 70,
                    "icon": "https://icon.icepanel.io/Technology/svg/Flutter.svg",
                    "category": "Mobile Development",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                },
                {
                    "id": 7,
                    "name": "Dart",
                    "proficiency": 70,
                    "icon": "https://icon.icepanel.io/Technology/svg/Dart.svg",
                    "category": "Mobile Development",
                    "deleted_at_human": null,
                    "deleted_at": null,
                    "created_at": "2026-04-25T14:57:00.000000Z",
                    "updated_at": "2026-04-25T14:57:00.000000Z"
                }
            ]
        },
        "messages": [
            {
                "id": 9,
                "name": "Beverly Harris",
                "email": "schulist.triston@yahoo.com",
                "subject": "Placeat veniam voluptatem non animi ullam illo.",
                "body": "Neque reprehenderit magnam occaecati. Et velit officia sed. Voluptatem fugiat accusamus facere aut consequuntur vel. Deserunt officia nihil nisi aliquid.",
                "is_read": false,
                "read_at_human": null,
                "read_at": null,
                "deleted_at_human": null,
                "deleted_at": null,
                "created_at": "2026-04-25T14:57:00.000000Z",
                "updated_at": "2026-04-25T14:57:00.000000Z"
            },
            {
                "id": 1,
                "name": "Kali Goldner",
                "email": "zschmitt@pagac.com",
                "subject": "Voluptatem est in maxime voluptatem.",
                "body": "Cumque sed fugiat quas debitis alias amet. Cupiditate error voluptas accusantium eum reprehenderit. Corrupti quia quia aut quo. Numquam quasi ab quidem molestiae quaerat quia est.",
                "is_read": true,
                "read_at_human": "1 day ago",
                "read_at": "2026-04-25 14:57",
                "deleted_at_human": null,
                "deleted_at": null,
                "created_at": "2026-04-25T14:57:00.000000Z",
                "updated_at": "2026-04-25T14:57:00.000000Z"
            },
            {
                "id": 2,
                "name": "Mr. Nicola Torphy II",
                "email": "trinity72@williamson.com",
                "subject": "Consequatur tempora aut repellendus error et reiciendis voluptatem tenetur.",
                "body": "Voluptatem eum veritatis distinctio labore. Consequatur dolorem cupiditate velit vel. Enim et ipsum est quia.",
                "is_read": true,
                "read_at_human": "1 day ago",
                "read_at": "2026-04-25 14:57",
                "deleted_at_human": null,
                "deleted_at": null,
                "created_at": "2026-04-25T14:57:00.000000Z",
                "updated_at": "2026-04-25T14:57:00.000000Z"
            }
        ],
        "information": {
            "id": 1,
            "first_name": "Mohammed",
            "last_name": "Zomlot",
            "full_name": "Mohammed Zomlot",
            "tagline": "Software Engineer",
            "bio": "Specialized in building robust, scalable server-side architectures using PHP & Laravel. Passionate about clean code, RESTful APIs, and database optimization.",
            "about_me": "Resilient Software Engineering student with a strong foundation in algorithmic problem-solving. I began my journey as a passionate Mobile Developer (Flutter), building several feature-rich applications. However, due to the war in Gaza and the resulting hardware limitations of my available device, I demonstrated adaptability by pivoting to Backend Development. I now specialize in PHP, Laravel, and RESTful APIs, leveraging my engineering mindset to build robust server-side solutions while continuing to excel in regional programming contests.",
            "avatar": "https://api.mohammedzomlot.online/storage/avatars/avatar.jpg",
            "cv_file": "https://api.mohammedzomlot.online/storage/cv/Mohammed_Zomlot-CV.pdf",
            "url_prefix": "Mohammedzomlot",
            "url_suffix": "dev",
            "languages": [
                {
                    "name": "Arabic",
                    "level": "Native"
                },
                {
                    "name": "English",
                    "level": "Intermediate"
                }
            ],
            "email": "mohammedzomlot2@gmail.com",
            "phone": "+970593628153",
            "location": "Gaza Strip, Palestine",
            "social_links": [
                {
                    "name": "github",
                    "url": "https://github.com/mohammedzom"
                },
                {
                    "name": "linkedin",
                    "url": "https://www.linkedin.com/in/mohammedzom/"
                },
                {
                    "name": "Telegram",
                    "url": "https://t.me/mohammedzom"
                }
            ],
            "years_experience": 1,
            "projects_count": 4,
            "clients_count": 3,
            "available_for_freelance": true,
            "created_at": "2026-04-25T14:57:00.000000Z",
            "updated_at": "2026-04-25T14:57:00.000000Z"
        },
        "projects_count": 4,
        "messages_count": {
            "total": 14,
            "unread": 1
        },
        "skills_count": 17
    }
}
```


## Projects

### `GET /api/v1/admin/projects`

- Auth: admin.
- Query:
  - `archived`: truthy value uses `onlyTrashed`; otherwise active records.
  - `search`: filters title or description with `LIKE`.
- Success: `data` is array of `ProjectResource`, message `Projects retreived successfully.`

### `GET /api/v1/admin/projects/{id}`

- Auth: admin.
- Only non-trashed project.
- Success: `data` is `ProjectResource`, message `Project fetched successfully.`

### `POST /api/v1/admin/projects`

- Auth: admin.
- Body: `multipart/form-data` recommended because `images` are files.
- Validation:
  - `title`: required, string, max 255
  - `slug`: nullable, string, max 255, unique `projects.slug`
  - `description`: required, string
  - `category`: required, one of `Web`, `App`, `Mobile`, `Script`, `Other`
  - `tech_stack`: nullable, array
  - `images`: nullable, array
  - `images.*`: nullable, image, mimes `jpeg,png,jpg,gif,webp`, max 15360 KB
  - `live_url`: nullable, url
  - `repo_url`: nullable, url
  - `is_featured`: nullable, boolean
  - `sort_order`: nullable, integer
- Server behavior:
  - Generates slug from title if missing.
  - Adds unique suffix if slug exists.
  - Stores images under `storage/app/public/projects`.
  - Defaults `is_featured` to false.
  - Defaults `sort_order` to current max + 1.
- Success `201`: `data` is `ProjectResource`, message `Project created successfully.`

### `PATCH /api/v1/admin/projects/{id}`

- Auth: admin.
- Body: `multipart/form-data` recommended.
- Validation:
  - `title`: sometimes, string, max 255
  - `slug`: nullable, string, max 255, unique ignored for current `{id}`
  - `description`: sometimes, string
  - `category`: sometimes, one of `Web`, `App`, `Mobile`, `Script`, `Other`
  - `tech_stack`: sometimes, array
  - `images`: sometimes, array
  - `images.*`: required when `images` present, image, mimes `jpeg,png,jpg,gif,webp`, max 15360 KB
  - `deleted_images`: nullable, array
  - `deleted_images.*`: required, string
  - `live_url`: sometimes, url
  - `repo_url`: sometimes, url
  - `is_featured`: sometimes, boolean
  - `sort_order`: sometimes, integer
- Server behavior:
  - `deleted_images` can be full asset URLs; basename is converted to `projects/{basename}`.
  - New images are appended.
  - If slug changes, it is slugified and made unique.
- Success `200`: `data` is `ProjectResource`, message `Project updated successfully.`

### `DELETE /api/v1/admin/projects/{id}`

- Auth: admin.
- Soft deletes non-trashed project.
- Success `200`: `data: []`, message `Project Archived successfully.`

### `PATCH /api/v1/admin/projects/{id}/restore`

- Auth: admin.
- Restores trashed project.
- Success `200`: `data` is `ProjectResource`, message `Project restored successfully.`

### `DELETE /api/v1/admin/projects/{id}/force-delete`

- Auth: admin.
- Force deletes project and deletes stored images.
- Success `200`: `data: []`, message `Project deleted successfully.`

## Services

### `GET /api/v1/admin/services`

- Auth: admin.
- Query:
  - `archived`: truthy value uses `onlyTrashed`; otherwise active records.
  - `search`: filters title or description with `LIKE`.
- Success: `data` is array of `ServiceResource`, message `Services fetched successfully.`

### `GET /api/v1/admin/services/{id}`

- Auth: admin.
- Only non-trashed service.
- Success: `data` is `ServiceResource`, message `Service fetched successfully.`

### `POST /api/v1/admin/services`

- Auth: admin.
- Body: JSON.
- Validation:
  - `title`: required, string, max 255
  - `description`: required, string
  - `icon`: required, url, max 255
  - `sort_order`: required, integer
  - `tags`: nullable, array
  - `tags.*`: string
- Success `201`: `data` is `ServiceResource`, message `Service created successfully.`

### `PATCH /api/v1/admin/services/{id}`

- Auth: admin.
- Body: JSON.
- Validation:
  - `title`: required, string, max 255
  - `description`: required, string
  - `icon`: required, url, max 255
  - `sort_order`: required, integer
  - `tags`: nullable, array
  - Current rule contains `tags.*.string` as a key instead of `tags.* => string`; frontend should still submit an array of strings.
- Success `200`: `data` is `ServiceResource`, message `Service updated successfully.`

### `DELETE /api/v1/admin/services/{id}`

- Auth: admin.
- Soft deletes non-trashed service.
- Success `200`: `data: []`, message `Service Archived successfully.`

### `PATCH /api/v1/admin/services/{id}/restore`

- Auth: admin.
- Restores trashed service.
- Success `200`: `data` is `ServiceResource`, message `Service restored successfully.`

### `DELETE /api/v1/admin/services/{id}/force-delete`

- Auth: admin.
- Force deletes service.
- Success `200`: `data: []`, message `Service deleted successfully.`

## Skills

### `GET /api/v1/admin/skills`

- Auth: admin.
- Query:
  - `archived`: truthy value uses `onlyTrashed`; otherwise active records.
  - `category`: code tries `where('category', value)`, but `skills` table does not have a `category` column. Filtering by category is likely broken.
  - `search`: filters `name` with `LIKE`.
- Success: `data` is array of `SkillResource`, message `Skills retreived successfully.`

### `GET /api/v1/admin/skills/{id}`

- Auth: admin.
- Only non-trashed skill.
- Success: `data` is `SkillResource`, message `Skill fetched successfully.`

### `POST /api/v1/admin/skills`

- Auth: admin.
- Body: JSON.
- Validation:
  - `name`: required, string, max 255, unique `skills.name`
  - `icon`: nullable, url
  - `proficiency`: required, integer, min 0, max 100
  - `skill_category_id`: required, exists `skill_categories.id`
- Success `201`: `data` is `SkillResource`, message `Skill created successfully.`

### `PATCH /api/v1/admin/skills/{id}`

- Auth: admin.
- Body: JSON.
- Validation:
  - `name`: sometimes, string, max 255, unique `skills.name` ignored for current id
  - `icon`: sometimes, url
  - `proficiency`: sometimes, integer, min 0, max 100
  - `skill_category_id`: sometimes, exists `skill_categories.id`
- Success `200`: `data` is `SkillResource`, message `Skill updated successfully.`

### `DELETE /api/v1/admin/skills/{id}`

- Auth: admin.
- Soft deletes non-trashed skill.
- Success `200`: `data: []`, message `Skill Archived successfully.`

### `PATCH /api/v1/admin/skills/{id}/restore`

- Auth: admin.
- Restores trashed skill.
- Success `200`: `data` is `SkillResource`, message `Skill restored successfully.`

### `DELETE /api/v1/admin/skills/{id}/force-delete`

- Auth: admin.
- Force deletes skill.
- Success `200`: `data: []`, message `Skill deleted successfully.`

## Experiences

### `GET /api/v1/admin/experiences`

- Auth: admin.
- Query:
  - `archived`: truthy value uses `onlyTrashed`; otherwise active records.
  - `search`: filters job title, company, or description with `LIKE`.
- Success: `data` is array of `ExperienceResource`, message `Experiences retrieved successfully`.
- Note: result is cached under `portfolio_experiences` even when `archived` or `search` is provided, so stale/wrong lists are possible.

### `GET /api/v1/admin/experiences/{id}`

- Auth: admin.
- Uses `findOrFail`, so trashed records may not be returned unless soft delete scope allows.
- Success: `data` is `ExperienceResource`, message `Experience retrieved successfully.`

### `POST /api/v1/admin/experiences`

- Auth: admin.
- Body: JSON.
- Validation:
  - `job_title`: required, string, max 255
  - `company`: required, string, max 255
  - `description`: required, string, max 255
  - `start_date`: required, digits 4, integer, max current year
  - `end_date`: excluded if `is_current=true`; required if `is_current=false`; digits 4; integer; after or equal `start_date`
  - `is_current`: required, boolean
- Extra server validation:
  - If `is_current` is true and `end_date` is present, returns `422 VALIDATION_ERROR` for `end_date`.
- Success `201`: `data` is `ExperienceResource`, message `Experience created successfully.`

### `PATCH /api/v1/admin/experiences/{id}`

- Auth: admin.
- Body: JSON.
- Validation:
  - `job_title`: sometimes, string, max 255
  - `company`: sometimes, string, max 255
  - `description`: sometimes, string, max 255
  - `start_date`: sometimes, integer
  - `end_date`: required if `is_current` is false or 0; nullable; integer; after `start_date`
  - `is_current`: sometimes, boolean
- Extra server validation:
  - If `is_current` is true and `end_date` is present, returns `422 VALIDATION_ERROR` for `end_date`.
- Success `200`: `data` is `ExperienceResource`, message `Experience updated successfully.`

### `DELETE /api/v1/admin/experiences/{id}`

- Auth: admin.
- Soft deletes non-trashed experience.
- Success `200`: `data: []`, message `Experience Archived successfully.`

### `PATCH /api/v1/admin/experiences/{id}/restore`

- Auth: admin.
- Restores trashed experience.
- Success `200`: `data` is `ExperienceResource`, message `Experience restored successfully.`

### `DELETE /api/v1/admin/experiences/{id}/force-delete`

- Auth: admin.
- Force deletes experience.
- Success `200`: `data: []`, message `Experience deleted successfully.`

## Education

### `GET /api/v1/admin/education`

- Auth: admin.
- Query:
  - `archived`: only if request input is boolean `true`; uses `withTrashed`, not `onlyTrashed`.
- Success: `data` is array of `EducationResource`, message `Education index fetched successfully.`

### `GET /api/v1/admin/education/{id}`

- Auth: admin.
- Success: `data` is `EducationResource`, message `Education fetched successfully.`

### `POST /api/v1/admin/education`

- Auth: admin.
- Body: JSON.
- Validation:
  - `degree`: required, string, max 255
  - `institution`: required, string, max 255
  - `field_of_study`: required, string, max 255
  - `start_year`: required, integer, min 1950, max current year + 1
  - `end_year`: nullable, integer, after or equal `start_year`
  - `gpa`: nullable, numeric, min 0, max 100
  - `description`: nullable, string
- Success `200`: `data` is `EducationResource`, message `Education created successfully.`

### `PATCH /api/v1/admin/education/{id}`

- Auth: admin.
- Body: JSON.
- Validation:
  - `degree`: sometimes, string, max 255
  - `institution`: sometimes, string, max 255
  - `field_of_study`: sometimes, string, max 255
  - `start_year`: sometimes, integer, min 1950, max current year + 1
  - `end_year`: nullable, integer, after or equal `start_year`
  - `gpa`: nullable, numeric, min 0, max 100
  - `description`: nullable, string
- Success `200`: `data` is `EducationResource`, message `Education updated successfully.`

### `DELETE /api/v1/admin/education/{id}`

- Auth: admin.
- Soft deletes non-trashed education.
- Success `200`: `data: null`, message `Education Archived successfully.`

### `PATCH /api/v1/admin/education/{id}/restore`

- Auth: admin.
- Restores trashed education.
- Success `200`: `data` is `EducationResource`, message `Education restored successfully.`

### `DELETE /api/v1/admin/education/{id}/force-delete`

- Auth: admin.
- Force deletes education.
- Success `200`: `data: null`, message `Education deleted permanently successfully.`

## Achievements

### `GET /api/v1/admin/achievements`

- Auth: admin.
- Query:
  - `archived`: only if request input is boolean `true`; uses `withTrashed`, not `onlyTrashed`.
- Success: `data` is array of `AchievementResource`, message `Achievement index fetched successfully.`

### `GET /api/v1/admin/achievements/{id}`

- Auth: admin.
- Success: `data` is `AchievementResource`, message `Achievement fetched successfully.`

### `POST /api/v1/admin/achievements`

- Auth: admin.
- Body: `multipart/form-data` recommended because `file` can be uploaded.
- Validation:
  - `title`: required, string, max 255
  - `issuer`: required, string, max 255
  - `date`: required, date
  - `url`: nullable, url, max 255
  - `description`: nullable, string, max 255
  - `file`: nullable, file, mimes `jpeg,png,jpg,pdf,doc,docx`, max 2048 KB
- Server behavior:
  - Stores uploaded file under `storage/app/public/achievements` using original filename.
- Success `201`: `data` is `AchievementResource`, message `Achievement created successfully.`

### `PATCH /api/v1/admin/achievements/{id}`

- Auth: admin.
- Body: `multipart/form-data` recommended.
- Validation:
  - `title`: sometimes, string, max 255
  - `issuer`: sometimes, string, max 255
  - `date`: sometimes, date
  - `url`: sometimes, url, max 255
  - `description`: sometimes, string, max 255
  - `file`: sometimes, file, mimes `jpeg,png,jpg,pdf,doc,docx`, max 2048 KB
- Server behavior:
  - Replaces existing `certificate_url` file when new file is uploaded.
- Success `200`: `data` is `AchievementResource`, message `Achievement updated successfully.`

### `DELETE /api/v1/admin/achievements/{id}`

- Auth: admin.
- Soft deletes non-trashed achievement.
- Success `200`: `data: null`, message `Achievement Archived successfully.`

### `PATCH /api/v1/admin/achievements/{id}/restore`

- Auth: admin.
- Restores trashed achievement.
- Success `200`: `data` is `AchievementResource`, message `Achievement restored successfully.`

### `DELETE /api/v1/admin/achievements/{id}/force-delete`

- Auth: admin.
- Force deletes achievement.
- Success `200`: `data: null`, message `Achievement deleted permanently successfully.`

## Messages

### `GET /api/v1/admin/messages`

- Auth: admin.
- Query:
  - `archived`: truthy value uses `onlyTrashed`; otherwise active records.
  - `search`: filters name, email, subject, or body with `LIKE`.
  - `page`: paginator page.
- Success `200`:

```json
{
  "success": true,
  "message": "Messages retrieved successfully.",
  "data": {
    "messages": ["MessageResource"],
    "meta": {
      "current_page": 1,
      "last_page": 1,
      "total": 0,
      "per_page": 10
    },
    "paginationLinks": {
      "self": "url",
      "first": "url",
      "last": "url",
      "prev": "url|null",
      "next": "url|null"
    }
  }
}
```

### `GET /api/v1/admin/messages/{id}`

- Auth: admin.
- Marks message read by setting `read_at` to now.
- Success: `data` is `MessageResource`, message `Message retrieved successfully.`

### `PATCH /api/v1/admin/messages/{id}/read`

- Auth: admin.
- If already read: `400`, `success: false`, message `Message is already read.`
- Success `200`:

```json
{
  "success": true,
  "message": "Message marked as read.",
  "data": {
    "read_at": "relative time string"
  }
}
```

### `PATCH /api/v1/admin/messages/{id}/unread`

- Auth: admin.
- If already unread: `400`, `success: false`, message `Message is already unread.`
- Success `200`: `data: []`, message `Message marked as unread.`

### `DELETE /api/v1/admin/messages/{id}`

- Auth: admin.
- Soft deletes non-trashed message.
- Success `200`: `data: []`, message `Message Archived successfully.`

### `PATCH /api/v1/admin/messages/{id}/restore`

- Auth: admin.
- Restores trashed message.
- Success `200`: `data` is `MessageResource`, message `Message restored successfully.`

### `DELETE /api/v1/admin/messages/{id}/force-delete`

- Auth: admin.
- Uses `Message::findOrFail($id)`, so force-delete may not find already trashed messages.
- Success `200`: `data: []`, message `Message deleted successfully.`

## Site Settings

### `GET /api/v1/admin/site-info`

- Auth: admin.
- Success: `data` is `SiteSettingsResource`, message `Site settings fetched successfully.`

### `PATCH /api/v1/admin/site-info`

- Auth: admin.
- Body: `multipart/form-data` recommended because `avatar` and `cv_file` can be uploaded.
- Validation:
  - `first_name`: sometimes, string, max 255
  - `last_name`: sometimes, string, max 255
  - `tagline`: sometimes, string, max 255
  - `bio`: sometimes, string, max 255
  - `avatar`: sometimes, image, mimes `jpeg,png,jpg,gif,webp`, max 15360 KB
  - `cv_file`: sometimes, file, mimes `pdf,doc,docx`, max 15360 KB
  - `email`: sometimes, email, max 255
  - `phone`: sometimes, valid phone `AUTO,AR,US,GB,PS`
  - `location`: sometimes, string
  - `social_links`: nullable, array
  - `social_links.*.name`: sometimes, string, max 255
  - `social_links.*.url`: sometimes, url, max 255
  - `languages`: nullable, array
  - `languages.*.name`: sometimes, string, max 255
  - `languages.*.level`: sometimes, string, max 255
  - `years_experience`: sometimes, integer
  - `projects_count`: sometimes, integer
  - `clients_count`: sometimes, integer
  - `available_for_freelance`: sometimes, boolean
  - `about_me`: sometimes, string
  - `url_prefix`: sometimes, string, max 255
  - `url_suffix`: sometimes, string, max 255
- Server behavior:
  - `avatar` is stored as `avatars/avatar.{ext}` and replaces existing avatar.
  - `cv_file` is stored under `cv/{original_name_with_spaces_replaced}` and replaces existing CV.
- Success `200`: `data` is `SiteSettingsResource`, message `Site settings updated successfully.`

## Skill Categories Gap

- Model/table exists: `SkillCategory` with fields `id`, `name`, `slug`, `created_at`, `updated_at`, `deleted_at`.
- Skills require `skill_category_id`.
- No routes exist for:
  - listing skill categories
  - creating skill categories
  - updating skill categories
  - deleting/restoring skill categories
- The requested future admin dashboard includes Skill Categories CRUD, but the backend currently does not expose API endpoints for that feature.

