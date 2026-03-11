# Markdown Reader Project

This repository now contains a working native PHP 8.5 MVC starter for a Markdown Reader project with a responsive home page, login, sign-up, an authenticated admin dashboard, and a polished parsed reader page for `.md` books.

## Stack

- PHP 8.5 CLI
- Native MVC structure
- PDO MySQL
- Session-based authentication
- CSRF protection
- League CommonMark 2.8.1 for markdown parsing
- Tailwind CSS CLI
- Apache rewrite support through `.htaccess`

## Tailwind Version Note

The requested `Tailwind CSS 4.2.18` package is not published on npm in this environment. The closest valid published 4.2 release is `4.2.1`, so the project uses:

- `tailwindcss@4.2.1`
- `@tailwindcss/cli@4.2.1`

## Implemented Pages

- Home page
- Login page
- Sign-up page
- Admin dashboard
- Markdown reader page

## Authentication Rules

- The first registered account becomes `admin`
- New users are signed in immediately after successful sign-up
- The admin dashboard requires authentication
- Login redirects back to the intended protected route when available

## Project Structure

```text
python-books/
├── app/
│   ├── Controllers/
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── BookController.php
│   │   └── HomeController.php
│   ├── Core/
│   │   ├── Auth.php
│   │   ├── Autoloader.php
│   │   ├── Csrf.php
│   │   ├── Database.php
│   │   ├── MarkdownRenderer.php
│   │   ├── Router.php
│   │   ├── Session.php
│   │   └── View.php
│   ├── Models/
│   │   ├── MarkdownDocument.php
│   │   └── User.php
│   └── Views/
│       ├── layouts/
│       │   └── app.php
│       └── pages/
│           ├── admin/
│           │   └── dashboard.php
│           ├── auth/
│           │   ├── login.php
│           │   └── signup.php
│           ├── books/
│           │   ├── not-found.php
│           │   └── show.php
│           └── home.php
├── config/
│   ├── constants.php
│   └── env.php
├── public/
│   ├── assets/
│   │   └── css/
│   └── index.php
├── resources/
│   └── css/
│       └── app.css
├── storage/
│   ├── cache/
│   └── logs/
├── .env
├── .env.sample
├── .gitignore
├── .htaccess
├── composer.json
├── index.php
├── package.json
└── full-documents.md
```

## Routing

```text
GET   /
GET   /login
POST  /login
GET   /signup
POST  /signup
GET   /books/{document}
GET   /admin/dashboard
POST  /logout
```

## Configuration Files

### `.gitignore`

The repository includes a comprehensive ignore file for:

- `node_modules`
- `vendor`
- local `.env` files
- IDE settings
- generated maps and logs
- cache artifacts

### `.env.sample`

```dotenv
APP_NAME="Markdown Reader"
APP_ENV=development
APP_DEBUG=true
APP_URL=https://python-books.test
APP_TIMEZONE=UTC
APP_SESSION_NAME=markdown_reader_session
APP_SESSION_LIFETIME=120
APP_KEY=change-me-before-production
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=python_books
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
DB_AUTO_CREATE=true
```

### `.env`

The workspace already includes a local `.env` file using the same keys for development.

## `config/env.php`

This file:

- loads values from `.env`
- supports comments and empty lines
- writes values to `$_ENV`, `$_SERVER`, and `getenv()`
- provides an `env()` helper function

## `config/constants.php`

This file defines dynamic project parameters and helper functions.

### Main constants

- `APP_NAME`
- `APP_ENV`
- `APP_DEBUG`
- `APP_URL`
- `APP_SESSION_NAME`
- `APP_SESSION_LIFETIME`
- `APP_KEY`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `DB_CHARSET`
- `DB_AUTO_CREATE`
- `ROUTE_HOME`
- `ROUTE_LOGIN`
- `ROUTE_SIGNUP`
- `ROUTE_DASHBOARD`
- `ROUTE_LOGOUT`

### Helper functions

- `base_path()`
- `public_path()`
- `storage_path()`
- `request_path()`
- `url()`
- `asset_url()`
- `e()`
- `redirect()`

## Database Layer

The application uses PDO and can create the configured database automatically when `DB_AUTO_CREATE=true`.

The users table is created automatically by the user model.

```sql
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(30) NOT NULL DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

## UI and Styling

### Source stylesheet

- [resources/css/app.css](d:\laragon\www\python-books\resources\css\app.css)

### Compiled stylesheet

- [public/assets/css/app.css](d:\laragon\www\python-books\public\assets\css\app.css)

### Build commands

```bash
npm install
npm run build:css
```

### Watch command

```bash
npm run watch:css
```

## Application Entry Points

- [index.php](d:\laragon\www\python-books\index.php): root entry for Laragon or Apache
- [public/index.php](d:\laragon\www\python-books\public\index.php): application bootstrap and route registration

## Apache Rewrite Behavior

The root `.htaccess` file:

- maps `/assets/*` to `public/assets/*`
- lets existing files load directly
- sends application routes to the root `index.php`

That enables these URLs:

- `https://python-books.test/`
- `https://python-books.test/login`
- `https://python-books.test/signup`
- `https://python-books.test/admin/dashboard`

## Startup Steps

### 1. Check local database settings

Verify the values inside [`.env`](d:\laragon\www\python-books\.env).

### 2. Install frontend packages

```bash
npm install
```

### 3. Build Tailwind CSS

```bash
npm run build:css
```

### 4. Open the site

```text
https://python-books.test/
```

### 5. Register the first account

The first account becomes the admin automatically and is redirected to the dashboard.

## Key Files

- [config/env.php](d:\laragon\www\python-books\config\env.php)
- [config/constants.php](d:\laragon\www\python-books\config\constants.php)
- [app/Controllers/HomeController.php](d:\laragon\www\python-books\app\Controllers\HomeController.php)
- [app/Controllers/AuthController.php](d:\laragon\www\python-books\app\Controllers\AuthController.php)
- [app/Controllers/AdminController.php](d:\laragon\www\python-books\app\Controllers\AdminController.php)
- [app/Models/User.php](d:\laragon\www\python-books\app\Models\User.php)
- [app/Views/layouts/app.php](d:\laragon\www\python-books\app\Views\layouts\app.php)
- [package.json](d:\laragon\www\python-books\package.json)

## Current Result

The project now includes the requested home page, login, sign-up, admin dashboard, parsed markdown reader page, `.gitignore`, `.env.sample`, `.env`, `config/env.php`, and `config/constants.php`.

## Recommended Next Enhancements

1. Add markdown parsing and reader templates for document pages.
2. Add role-based authorization beyond basic authenticated access.
3. Add dashboard CRUD for markdown files.
4. Add password reset and email verification if the project will be used beyond local development.
