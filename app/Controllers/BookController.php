<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\MarkdownRenderer;
use App\Core\View;
use App\Models\MarkdownDocument;

final class BookController
{
  public function show(string $document): void
  {
    $book = MarkdownDocument::find($document);

    if ($book === null) {
      http_response_code(404);

      View::render('pages/books/not-found', [
        'title' => 'Book Not Found',
        'requested' => rawurldecode($document),
      ]);

      return;
    }

    $renderer = new MarkdownRenderer();
    $rendered = $renderer->render($book['content']);

    View::render('pages/books/show', [
      'title' => $book['title'],
      'book' => $book,
      'bookHtml' => $rendered['html'],
      'toc' => $rendered['toc'],
      'relatedDocuments' => MarkdownDocument::related($book['file_name']),
    ]);
  }
}