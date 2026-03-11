<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Models\MarkdownAsset;
use App\Models\MarkdownDocument;
use App\Models\User;

final class AdminController
{
  public function index(): void
  {
    Auth::requireAuth();

    $user = Auth::user();
    $documents = MarkdownDocument::all();
    $assets = MarkdownAsset::all();

    View::render('pages/admin/dashboard', [
      'title' => 'Admin Dashboard',
      'user' => $user,
      'stats' => [
        'users' => User::count(),
        'documents' => MarkdownDocument::count(),
        'assets' => MarkdownAsset::count(),
        'environment' => strtoupper(APP_ENV),
        'session' => APP_SESSION_NAME,
      ],
      'documents' => $documents,
      'assets' => $assets,
    ]);
  }
}
