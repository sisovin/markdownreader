<?php

declare(strict_types=1);

namespace App\Core;

final class Session
{
  public static function start(): void
  {
    if (session_status() === PHP_SESSION_ACTIVE) {
      return;
    }

    session_name(APP_SESSION_NAME);
    session_set_cookie_params([
      'lifetime' => APP_SESSION_LIFETIME * 60,
      'path' => '/',
      'httponly' => true,
      'samesite' => 'Lax',
      'secure' => str_starts_with(APP_URL, 'https://'),
    ]);

    session_start();
  }

  public static function put(string $key, mixed $value): void
  {
    $_SESSION[$key] = $value;
  }

  public static function get(string $key, mixed $default = null): mixed
  {
    return $_SESSION[$key] ?? $default;
  }

  public static function has(string $key): bool
  {
    return array_key_exists($key, $_SESSION);
  }

  public static function forget(string $key): void
  {
    unset($_SESSION[$key]);
  }

  public static function pull(string $key, mixed $default = null): mixed
  {
    $value = $_SESSION[$key] ?? $default;
    unset($_SESSION[$key]);

    return $value;
  }

  public static function flash(string $key, mixed $value): void
  {
    $_SESSION['_flash'][$key] = $value;
  }

  public static function pullFlash(string $key, mixed $default = null): mixed
  {
    $value = $_SESSION['_flash'][$key] ?? $default;
    unset($_SESSION['_flash'][$key]);

    return $value;
  }

  public static function regenerate(): void
  {
    session_regenerate_id(true);
  }

  public static function invalidate(): void
  {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
    }

    session_destroy();
  }
}
