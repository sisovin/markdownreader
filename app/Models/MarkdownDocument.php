<?php

declare(strict_types=1);

namespace App\Models;

use RuntimeException;

final class MarkdownDocument
{
  private const EXCLUDED_FILES = [
    'full-documents.md',
  ];

  private static ?array $documents = null;

  public static function all(): array
  {
    if (self::$documents !== null) {
      return self::$documents;
    }

    $files = glob(self::markdownDirectory() . DS . '*.md') ?: [];

    usort($files, static fn(string $left, string $right): int => filemtime($right) <=> filemtime($left));

    $documents = [];

    foreach ($files as $filePath) {
      $fileName = basename($filePath);

      if (in_array(strtolower($fileName), self::EXCLUDED_FILES, true)) {
        continue;
      }

      $markdown = file_get_contents($filePath);

      if (!is_string($markdown)) {
        continue;
      }

      $plainText = self::plainText($markdown);
      $wordCount = str_word_count($plainText);

      $documents[] = [
        'title' => self::title($fileName, $markdown),
        'name' => pathinfo($fileName, PATHINFO_FILENAME),
        'file_name' => $fileName,
        'path' => $filePath,
        'url' => book_url($fileName),
        'raw_url' => url(ROUTE_MARKDOWN . '/' . rawurlencode($fileName)),
        'updated_at' => date('M d, Y g:i A', (int) filemtime($filePath)),
        'excerpt' => self::excerpt($plainText),
        'word_count' => $wordCount,
        'reading_time' => max(1, (int) ceil(max($wordCount, 1) / 220)),
        'cover_image' => self::coverImage($markdown),
      ];
    }

    self::$documents = $documents;

    return self::$documents;
  }

  public static function count(): int
  {
    return count(self::all());
  }

  public static function find(string $encodedFileName): ?array
  {
    $requestedName = basename(rawurldecode($encodedFileName));

    foreach (self::all() as $document) {
      if (strcasecmp($document['file_name'], $requestedName) === 0) {
        $content = file_get_contents($document['path']);

        if (!is_string($content)) {
          return null;
        }

        $document['content'] = $content;

        return $document;
      }
    }

    return null;
  }

  public static function related(string $currentFileName, int $limit = 4): array
  {
    $related = array_values(array_filter(
      self::all(),
      static fn(array $document): bool => strcasecmp($document['file_name'], $currentFileName) !== 0
    ));

    return array_slice($related, 0, $limit);
  }

  public static function create(array $attributes): array
  {
    $title = trim((string) ($attributes['title'] ?? ''));
    $content = (string) ($attributes['content'] ?? '');
    $fileName = self::normalizedFileName((string) ($attributes['file_name'] ?? ''), $title);
    $path = self::filePath($fileName);

    if (self::isProtected($fileName)) {
      throw new RuntimeException('That file name is reserved and cannot be used.');
    }

    if (is_file($path)) {
      throw new RuntimeException('A markdown file with that name already exists.');
    }

    self::writeFile($path, self::normalizedContent($title, $content));
    self::resetCache();

    $document = self::find(rawurlencode($fileName));

    if ($document === null) {
      throw new RuntimeException('The markdown file was created, but it could not be loaded.');
    }

    return $document;
  }

  public static function update(string $encodedCurrentFileName, array $attributes): array
  {
    $existing = self::find($encodedCurrentFileName);

    if ($existing === null) {
      throw new RuntimeException('The markdown file could not be found.');
    }

    $title = trim((string) ($attributes['title'] ?? ''));
    $content = (string) ($attributes['content'] ?? '');
    $fileName = self::normalizedFileName((string) ($attributes['file_name'] ?? ''), $title, $existing['file_name']);
    $newPath = self::filePath($fileName);
    $existingPath = $existing['path'];

    if (self::isProtected($fileName) && strcasecmp($fileName, $existing['file_name']) !== 0) {
      throw new RuntimeException('That file name is reserved and cannot be used.');
    }

    if (strcasecmp($fileName, $existing['file_name']) !== 0 && is_file($newPath)) {
      throw new RuntimeException('A markdown file with that name already exists.');
    }

    self::writeFile($newPath, self::normalizedContent($title, $content));

    if (strcasecmp($existingPath, $newPath) !== 0 && is_file($existingPath)) {
      unlink($existingPath);
    }

    self::resetCache();

    $document = self::find(rawurlencode($fileName));

    if ($document === null) {
      throw new RuntimeException('The markdown file was updated, but it could not be loaded.');
    }

    return $document;
  }

  public static function delete(string $encodedFileName): void
  {
    $document = self::find($encodedFileName);

    if ($document === null) {
      throw new RuntimeException('The markdown file could not be found.');
    }

    if (self::isProtected($document['file_name'])) {
      throw new RuntimeException('This markdown file is protected and cannot be deleted.');
    }

    if (!is_file($document['path']) || !unlink($document['path'])) {
      throw new RuntimeException('The markdown file could not be deleted.');
    }

    self::resetCache();
  }

  public static function editorValues(?array $document = null): array
  {
    if ($document === null) {
      return [
        'title' => '',
        'file_name' => '',
        'content' => "# Untitled Document\n\nStart writing here.\n",
      ];
    }

    return [
      'title' => (string) ($document['title'] ?? ''),
      'file_name' => pathinfo((string) ($document['file_name'] ?? ''), PATHINFO_FILENAME),
      'content' => (string) ($document['content'] ?? ''),
    ];
  }

  public static function previewContent(array $attributes): string
  {
    $title = trim((string) ($attributes['title'] ?? ''));
    $content = (string) ($attributes['content'] ?? '');

    return self::normalizedContent($title, $content);
  }

  private static function title(string $fileName, string $markdown): string
  {
    $inCodeBlock = false;

    foreach (preg_split('/\R/', $markdown) ?: [] as $line) {
      $trimmed = trim($line);

      if (preg_match('/^```/', $trimmed) === 1) {
        $inCodeBlock = !$inCodeBlock;
        continue;
      }

      if ($inCodeBlock || $trimmed === '') {
        continue;
      }

      if (preg_match('/^#\s+(.+)$/', $trimmed, $matches) === 1) {
        return trim($matches[1]);
      }
    }

    return pathinfo($fileName, PATHINFO_FILENAME);
  }

  private static function normalizedFileName(string $candidate, string $title, ?string $currentFileName = null): string
  {
    $candidate = trim($candidate);

    if ($candidate === '') {
      $candidate = self::slugify($title !== '' ? $title : ($currentFileName !== null ? pathinfo($currentFileName, PATHINFO_FILENAME) : 'document'));
    }

    $candidate = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $candidate);
    $candidate = preg_replace('/\s+/', ' ', $candidate) ?? $candidate;
    $candidate = trim($candidate, " .\t\n\r\0\x0B");

    if ($candidate === '') {
      $candidate = 'document-' . date('Ymd-His');
    }

    $name = pathinfo($candidate, PATHINFO_FILENAME);

    if ($name === '' || $name === '.') {
      $name = 'document-' . date('Ymd-His');
    }

    return $name . '.md';
  }

  private static function normalizedContent(string $title, string $content): string
  {
    $normalized = str_replace(["\r\n", "\r"], "\n", trim($content));

    if ($normalized === '') {
      return '# ' . $title . "\n\nStart writing here.\n";
    }

    if (preg_match('/^#\s+/m', $normalized) !== 1 && $title !== '') {
      $normalized = '# ' . $title . "\n\n" . $normalized;
    }

    return rtrim($normalized) . "\n";
  }

  private static function excerpt(string $plainText): string
  {
    $excerpt = trim(preg_replace('/\s+/', ' ', $plainText) ?? '');

    if ($excerpt === '') {
      return 'No preview text is available for this document yet.';
    }

    if (mb_strlen($excerpt) <= 180) {
      return $excerpt;
    }

    return rtrim(mb_substr($excerpt, 0, 177)) . '...';
  }

  private static function plainText(string $markdown): string
  {
    $text = preg_replace('/```.*?```/s', ' ', $markdown) ?? $markdown;
    $text = preg_replace('/!\[([^\]]*)\]\(([^)]+)\)/', '$1', $text) ?? $text;
    $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '$1', $text) ?? $text;
    $text = preg_replace('/^>+/m', ' ', $text) ?? $text;
    $text = preg_replace('/^#{1,6}\s+/m', '', $text) ?? $text;
    $text = preg_replace('/[*_~`>-]+/', ' ', $text) ?? $text;

    return trim(strip_tags($text));
  }

  private static function slugify(string $value): string
  {
    $transliterated = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

    if (is_string($transliterated) && $transliterated !== '') {
      $value = $transliterated;
    }

    $value = strtolower($value);
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? $value;

    return trim($value, '-');
  }

  private static function writeFile(string $path, string $content): void
  {
    $directory = dirname($path);

    if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
      throw new RuntimeException('The markdown storage directory could not be created.');
    }

    $result = file_put_contents($path, $content);

    if ($result === false) {
      throw new RuntimeException('The markdown file could not be saved.');
    }
  }

  private static function isProtected(string $fileName): bool
  {
    return in_array(strtolower($fileName), self::EXCLUDED_FILES, true);
  }

  private static function resetCache(): void
  {
    self::$documents = null;
  }

  private static function markdownDirectory(): string
  {
    return public_path('markdown');
  }

  private static function filePath(string $fileName): string
  {
    return self::markdownDirectory() . DS . basename($fileName);
  }

  private static function coverImage(string $markdown): ?string
  {
    if (preg_match('/!\[[^\]]*\]\(([^)]+)\)/', $markdown, $matches) !== 1) {
      return null;
    }

    $path = trim($matches[1]);

    if ($path === '') {
      return null;
    }

    if (preg_match('#^(?:[a-z][a-z0-9+.-]*:)?//#i', $path) === 1 || str_starts_with($path, 'data:')) {
      return $path;
    }

    $normalizedPath = str_replace('\\', '/', $path);

    while (str_starts_with($normalizedPath, './')) {
      $normalizedPath = substr($normalizedPath, 2);
    }

    $segments = array_map(
      static fn(string $segment): string => rawurlencode(rawurldecode($segment)),
      array_filter(explode('/', ltrim($normalizedPath, '/')), static fn(string $segment): bool => $segment !== '')
    );

    if (str_starts_with($normalizedPath, '/')) {
      return url('/' . implode('/', $segments));
    }

    return url(ROUTE_MARKDOWN . '/' . implode('/', $segments));
  }
}