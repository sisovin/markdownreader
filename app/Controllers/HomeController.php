<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Models\MarkdownDocument;
use App\Models\User;

final class HomeController
{
  public function index(): void
  {
    $documents = MarkdownDocument::all();

    View::render('pages/home', [
      'title' => 'Home',
      'isAuthenticated' => Auth::check(),
      'user' => Auth::user(),
      'stats' => [
        'documents' => MarkdownDocument::count(),
        'users' => User::count(),
        'stack' => BRAND_HIGHLIGHT,
      ],
      'documents' => array_slice($documents, 0, 6),
    ]);
  }
}
