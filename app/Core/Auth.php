<?php

declare(strict_types=1);

namespace App\Core;

final class Auth
{
  public static function check(): bool
  {
    return is_array(Session::get('user'));
  }

  public static function user(): ?array
  {
    $user = Session::get('user');

    return is_array($user) ? $user : null;
  }

  public static function login(array $user): void
  {
    unset($user['password']);
    Session::put('user', $user);
  }

  public static function logout(): void
  {
    Session::forget('user');
    Session::forget('intended');
  }

  public static function requireAuth(): void
  {
    if (self::check()) {
      return;
    }

    Session::put('intended', request_path());
    Session::flash('status', [
      'type' => 'error',
      'message' => 'Please sign in to open the admin dashboard.',
    ]);

    redirect(ROUTE_LOGIN);
  }
}
