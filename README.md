# File Management System (FMS)

A company document management system built for the Full Stack Laravel Developer technical test — supports hierarchical folders, file management with metadata, and role-based access control (Administrator / Viewer).

**Note on Laravel version:** this project is pinned to Laravel 11 per the test's mandatory stack, even though Laravel 11 reached end of security support in March 2026. Running `composer install`/`create-project` may surface security-advisory warnings from Composer as a result — this is expected and documented, not an oversight. See [Notes](#notes) below.

## Tech Stack

| Layer            | Technology                      |
| ---------------- | ------------------------------- |
| Backend          | Laravel 11 (PHP 8.3)            |
| Frontend         | Vue 3 + TypeScript (Vite)       |
| Styling          | Tailwind CSS + shadcn-vue       |
| Database         | PostgreSQL                      |
| Auth             | Laravel Sanctum (Bearer tokens) |
| State management | Pinia                           |
| HTTP client      | Axios                           |

## Architecture

The backend follows a lightweight **Service Layer** pattern: Controllers stay thin (validate via Form Requests → call a Service method → return JSON), while all business logic (auth, folder hierarchy/breadcrumb, file storage handling, dashboard aggregation) lives in `app/Services/`. Authorization is enforced at two layers — an `admin` route middleware for a cheap early reject, and Eloquent Policies (`app/Policies/`) for per-instance checks on update/delete actions.

```
backend/app/
├── Http/Controllers/Api/   (thin — HTTP glue only)
├── Http/Requests/          (validation + role-gate via authorize())
├── Services/               (business logic)
├── Policies/                (per-instance authorization)
└── Models/                 (Eloquent, relations)
```

The frontend is a Vue 3 SPA (Vite) with TypeScript throughout — a `src/types/index.ts` file mirrors every backend model's JSON shape end-to-end, from Axios response generics through the Pinia store to component props.

## Features Implemented

- **Authentication** — Laravel Sanctum, two roles (`admin`, `viewer`)
- **Folder Management**
- **File Management**
- **File Detail**
- **Department Management**
- **Dashboard**
- **Search** — by filename, title
- **RBAC** — enforced server-side via Policies + middleware, mirrored in the UI (write actions hidden from viewers)

### Bonus Features Implemented

- [v] Search
- [v] Breadcrumb Folder
- [v] Preview PDF/Image
- [v] Soft Delete (folders and files)
- [v] Dark Mode
- [v] Clean Architecture (Service Pattern)
- [v] Docker

### Bonus Features Not Implemented

- [x] Filter
- [x] Drag & Drop Upload
- [x] Responsive, Drive-style unified folder/file browser UI
- [x] Activity Log
- [x] Unit Test / Feature Test (Pest)
- [x] API Documentation (Swagger/Postman)

These were deprioritized to focus engineering time on a complete, correct core feature set and clean architecture rather than partial bonus coverage across the board.

---

## Requirements

- PHP 8.3
- Composer 2.x
- Bun
- PostgreSQL
- (Optional, for containerized run) Docker + Docker Compose

## Installation

### 1. Clone and enter the repo

```bash
git clone https://github.com/zen-geohub/lion-technicaltest.git
cd lion-technicaltest
```

### 2. Backend setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env` and confirm the `DB_*` values match your local PostgreSQL setup (see [Environment Configuration](#environment-configuration) below), then create the database.

### 3. Frontend setup

```bash
cd ../frontend
bun install
cp .env.example .env
```

Confirm `VITE_API_URL` in `frontend/.env` points at your backend (`http://localhost:8000/api` for local development).

## Running the Project

Two terminal tabs, run simultaneously:

```bash
# Terminal 1 — backend
cd backend
php artisan serve
# → http://localhost:8000

# Terminal 2 — frontend
cd frontend
bun run dev
# → http://localhost:5173
```

Visit `http://localhost:5173` and log in with one of the [seeded accounts](#login-accounts) below.

## Environment Configuration

**`backend/.env`** (key values, copy from `.env.example` and adjust):

```env
APP_NAME=FMS
APP_ENV=local
APP_KEY=            # generated via php artisan key:generate
APP_DEBUG=true       # set to false for any non-local environment
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=fms
DB_USERNAME=fms_user
DB_PASSWORD=fms_password

FILESYSTEM_DISK=public

SANCTUM_STATEFUL_DOMAINS=localhost:5173
SESSION_DOMAIN=localhost
```

**`frontend/.env`**:

```env
VITE_API_URL=http://localhost:8000/api
```

## Running Migrations & Seeders

```bash
cd backend
php artisan migrate:fresh --seed
```

This creates all tables (`users`, `departments`, `folders`, `files`, plus Laravel's own `sessions`/`personal_access_tokens`) and seeds:

- 2 users (see [Login Accounts](#login-accounts))
- 4 departments (Finance, Human Resources, IT, Marketing)
- A small sample folder hierarchy (`Company Documents` → `Contracts` → `2026`)
- 1 sample file

Also run, once, to make uploaded files web-accessible:

```bash
php artisan storage:link
```

## Login Accounts

| Role          | Email              | Password |
| ------------- | ------------------ | -------- |
| Administrator | admin@example.com  | password |
| Viewer        | viewer@example.com | password |

---

## Notes

- **Laravel 11 EOL:** as noted above, Laravel 11 is past its security-support window. Running `composer install` may show advisory warnings; these were acknowledged and left as-is to satisfy the test's mandated stack version. Run `composer audit` inside `backend/` for details on which advisories apply.
- On how I work for this take-home test, I used AI (Claude), to help me structure and bootstrapping, and learn parts of the stack I'm not familiar with, especially Laravel. I did this take-home on nights around ongoing project-based work, so AI helped me cover the ground in the time I had. That said, the decisions are mine. I applied the best practices I know, pushed back when the AI suggested something I wasn't comfortable with, and made sure I understood the why rather than just accepting it.
