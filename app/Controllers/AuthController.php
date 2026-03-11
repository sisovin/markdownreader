<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Core\View;
use App\Models\User;

final class AuthController
{
  public function showLogin(): void
  {
    if (Auth::check()) {
      redirect(ROUTE_DASHBOARD);
    }

    View::render('pages/auth/login', [
      'title' => 'Login',
      'errors' => Session::pullFlash('errors', []),
      'old' => Session::pullFlash('old', []),
    ]);
  }

  public function login(): void
  {
    if (!Csrf::validate($_POST['_token'] ?? null)) {
      $this->redirectBackWithErrors(ROUTE_LOGIN, ['form' => 'Your session expired. Please try signing in again.']);
    }

    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $errors = [];

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = 'Use a valid email address.';
    }

    if ($password === '') {
      $errors['password'] = 'Password is required.';
    }

    if ($errors !== []) {
      $this->redirectBackWithErrors(ROUTE_LOGIN, $errors, ['email' => $email]);
    }

    $user = User::findByEmail($email);

    if (!is_array($user) || !password_verify($password, (string) $user['password'])) {
      $this->redirectBackWithErrors(ROUTE_LOGIN, ['form' => 'We could not match that email and password.'], ['email' => $email]);
    }

    Session::regenerate();
    Auth::login($user);
    Session::flash('status', [
      'type' => 'success',
      'message' => 'Welcome back. Your dashboard is ready.',
    ]);

    redirect((string) Session::pull('intended', ROUTE_DASHBOARD));
  }

  public function showSignup(): void
  {
    if (Auth::check()) {
      redirect(ROUTE_DASHBOARD);
    }

    View::render('pages/auth/signup', [
      'title' => 'Sign Up',
      'errors' => Session::pullFlash('errors', []),
      'old' => Session::pullFlash('old', []),
      'userCount' => User::count(),
    ]);
  }

  public function signup(): void
  {
    if (!Csrf::validate($_POST['_token'] ?? null)) {
      $this->redirectBackWithErrors(ROUTE_SIGNUP, ['form' => 'Your session expired. Please try creating the account again.']);
    }

    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $passwordConfirmation = (string) ($_POST['password_confirmation'] ?? '');
    $errors = [];

    if ($name === '' || mb_strlen($name) < 3) {
      $errors['name'] = 'Name must be at least 3 characters long.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = 'Use a valid email address.';
    }

    if (mb_strlen($password) < 8) {
      $errors['password'] = 'Password must contain at least 8 characters.';
    }

    if ($password !== $passwordConfirmation) {
      $errors['password_confirmation'] = 'Password confirmation does not match.';
    }

    if (User::findByEmail($email) !== null) {
      $errors['email'] = 'An account with that email already exists.';
    }

    if ($errors !== []) {
      $this->redirectBackWithErrors(ROUTE_SIGNUP, $errors, [
        'name' => $name,
        'email' => $email,
      ]);
    }

    $role = User::count() === 0 ? 'admin' : 'editor';
    User::create([
      'name' => $name,
      'email' => $email,
      'password' => password_hash($password, PASSWORD_DEFAULT),
      'role' => $role,
    ]);

    $user = User::findByEmail($email);

    if (!is_array($user)) {
      $this->redirectBackWithErrors(ROUTE_SIGNUP, ['form' => 'The account was created, but automatic sign-in failed. Please log in.'], ['email' => $email]);
    }

    Session::regenerate();
    Auth::login($user);
    Session::flash('status', [
      'type' => 'success',
      'message' => $role === 'admin'
        ? 'Your admin account is active. Welcome to the dashboard.'
        : 'Your account is ready. You now have access to the dashboard.',
    ]);

    redirect(ROUTE_DASHBOARD);
  }

  public function logout(): void
  {
    if (!Csrf::validate($_POST['_token'] ?? null)) {
      redirect(ROUTE_HOME);
    }

    Auth::logout();
    Session::invalidate();
    Session::start();
    Session::flash('status', [
      'type' => 'success',
      'message' => 'You have been signed out.',
    ]);

    redirect(ROUTE_HOME);
  }

  private function redirectBackWithErrors(string $route, array $errors, array $old = []): never
  {
    Session::flash('errors', $errors);
    Session::flash('old', $old);
    Session::flash('status', [
      'type' => 'error',
      'message' => 'Please review the form and try again.',
    ]);

    redirect($route);
  }
}
