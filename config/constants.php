<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

date_default_timezone_set((string) env('APP_TIMEZONE', 'UTC'));

const DS = DIRECTORY_SEPARATOR;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . DS . 'app');
define('CONFIG_PATH', BASE_PATH . DS . 'config');
define('PUBLIC_PATH', BASE_PATH . DS . 'public');
define('RESOURCE_PATH', BASE_PATH . DS . 'resources');
define('STORAGE_PATH', BASE_PATH . DS . 'storage');

define('APP_NAME', (string) env('APP_NAME', 'Markdown Reader'));
define('APP_ENV', (string) env('APP_ENV', 'production'));
define('APP_DEBUG', (bool) env('APP_DEBUG', false));
define('APP_URL', rtrim((string) env('APP_URL', ''), '/'));
define('APP_SESSION_NAME', (string) env('APP_SESSION_NAME', 'markdown_reader_session'));
define('APP_SESSION_LIFETIME', (int) env('APP_SESSION_LIFETIME', 120));
define('APP_KEY', (string) env('APP_KEY', 'change-me'));

define('DB_HOST', (string) env('DB_HOST', '127.0.0.1'));
define('DB_PORT', (int) env('DB_PORT', 3306));
define('DB_DATABASE', (string) env('DB_DATABASE', 'markdown_reader'));
define('DB_USERNAME', (string) env('DB_USERNAME', 'root'));
define('DB_PASSWORD', (string) env('DB_PASSWORD', ''));
define('DB_CHARSET', (string) env('DB_CHARSET', 'utf8mb4'));
define('DB_AUTO_CREATE', (bool) env('DB_AUTO_CREATE', true));

define('ROUTE_HOME', '/');
define('ROUTE_LOGIN', '/login');
define('ROUTE_SIGNUP', '/signup');
define('ROUTE_MARKDOWN', '/markdown');
define('ROUTE_BOOKS', '/books');
define('ROUTE_DASHBOARD', '/admin/dashboard');
define('ROUTE_ADMIN_DOCUMENTS', '/admin/documents');
define('ROUTE_ADMIN_ASSETS', '/admin/assets');
define('ROUTE_LOGOUT', '/logout');

define('BRAND_TAGLINE', 'A clean PHP dashboard for reading, managing, and growing your markdown library.');
define('BRAND_HIGHLIGHT', 'Native PHP 8.5, MVC structure, PDO MySQL, Tailwind CSS 4.2.1');

if (!function_exists('base_path')) {
  function base_path(string $path = ''): string
  {
    return BASE_PATH . ($path !== '' ? DS . ltrim($path, '/\\') : '');
  }
}

if (!function_exists('public_path')) {
  function public_path(string $path = ''): string
  {
    return PUBLIC_PATH . ($path !== '' ? DS . ltrim($path, '/\\') : '');
  }
}

if (!function_exists('storage_path')) {
  function storage_path(string $path = ''): string
  {
    return STORAGE_PATH . ($path !== '' ? DS . ltrim($path, '/\\') : '');
  }
}

if (!function_exists('request_path')) {
  function request_path(): string
  {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH);

    if (!is_string($path) || $path === '') {
      return '/';
    }

    return rtrim($path, '/') ?: '/';
  }
}

if (!function_exists('url')) {
  function url(string $path = ''): string
  {
    $normalized = '/' . ltrim($path, '/');

    if ($path === '' || $path === '/') {
      $normalized = '/';
    }

    return APP_URL !== '' ? APP_URL . $normalized : $normalized;
  }
}

if (!function_exists('asset_url')) {
  function asset_url(string $path = ''): string
  {
    return url('/assets/' . ltrim($path, '/'));
  }
}

if (!function_exists('markdown_url')) {
  function markdown_url(string $path = ''): string
  {
    $normalized = ltrim(str_replace('\\', '/', $path), '/');

    if ($normalized === '') {
      return url(ROUTE_MARKDOWN);
    }

    return url(ROUTE_MARKDOWN . '/' . $normalized);
  }
}

if (!function_exists('markdown_file_url')) {
  function markdown_file_url(string $fileName): string
  {
    return markdown_url(rawurlencode(basename($fileName)));
  }
}

if (!function_exists('book_url')) {
  function book_url(string $fileName): string
  {
    return url(ROUTE_BOOKS . '/' . rawurlencode(basename($fileName)));
  }
}

if (!function_exists('admin_document_create_url')) {
  function admin_document_create_url(): string
  {
    return url(ROUTE_ADMIN_DOCUMENTS . '/create');
  }
}

if (!function_exists('admin_document_preview_url')) {
  function admin_document_preview_url(): string
  {
    return url(ROUTE_ADMIN_DOCUMENTS . '/preview');
  }
}

if (!function_exists('admin_document_edit_url')) {
  function admin_document_edit_url(string $fileName): string
  {
    return url(ROUTE_ADMIN_DOCUMENTS . '/' . rawurlencode(basename($fileName)) . '/edit');
  }
}

if (!function_exists('admin_document_update_url')) {
  function admin_document_update_url(string $fileName): string
  {
    return url(ROUTE_ADMIN_DOCUMENTS . '/' . rawurlencode(basename($fileName)));
  }
}

if (!function_exists('admin_document_delete_url')) {
  function admin_document_delete_url(string $fileName): string
  {
    return url(ROUTE_ADMIN_DOCUMENTS . '/' . rawurlencode(basename($fileName)) . '/delete');
  }
}

if (!function_exists('admin_asset_upload_url')) {
  function admin_asset_upload_url(): string
  {
    return url(ROUTE_ADMIN_ASSETS);
  }
}

if (!function_exists('admin_asset_delete_url')) {
  function admin_asset_delete_url(string $fileName): string
  {
    return url(ROUTE_ADMIN_ASSETS . '/' . rawurlencode(basename($fileName)) . '/delete');
  }
}

if (!function_exists('e')) {
  function e(mixed $value): string
  {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
  }
}

if (!function_exists('redirect')) {
  function redirect(string $path): never
  {
    header('Location: ' . url($path));
    exit;
  }
}
