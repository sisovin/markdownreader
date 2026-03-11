<?php

declare(strict_types=1);

namespace App\Models;

use RuntimeException;

final class MarkdownAsset
{
  private const MAX_UPLOAD_BYTES = 8_388_608;
  private const ALLOWED_EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'avif'];
  private const MIME_TYPES = [
    'png' => ['image/png'],
    'jpg' => ['image/jpeg'],
    'jpeg' => ['image/jpeg'],
    'gif' => ['image/gif'],
    'webp' => ['image/webp'],
    'avif' => ['image/avif'],
  ];

  public static function all(): array
  {
    self::ensureDirectory();

    $files = glob(self::directory() . DS . '*') ?: [];
    $files = array_values(array_filter($files, static fn(string $path): bool => is_file($path)));

    usort($files, static fn(string $left, string $right): int => filemtime($right) <=> filemtime($left));

    return array_map(static fn(string $path): array => self::metadata($path), $files);
  }

  public static function count(): int
  {
    return count(self::all());
  }

  public static function upload(array $file): array
  {
    self::ensureDirectory();

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
      throw new RuntimeException(self::uploadErrorMessage((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)));
    }

    $tmpName = (string) ($file['tmp_name'] ?? '');
    $originalName = trim((string) ($file['name'] ?? ''));
    $size = (int) ($file['size'] ?? 0);

    if ($originalName === '') {
      throw new RuntimeException('Choose an image file to upload.');
    }

    if ($size <= 0) {
      throw new RuntimeException('The selected image appears to be empty.');
    }

    if ($size > self::MAX_UPLOAD_BYTES) {
      throw new RuntimeException('Images must be 8 MB or smaller.');
    }

    if (!is_uploaded_file($tmpName)) {
      throw new RuntimeException('The uploaded image could not be verified.');
    }

    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
      throw new RuntimeException('Upload a PNG, JPG, GIF, WEBP, AVIF, or SVG image.');
    }

    self::assertMimeType($tmpName, $extension);

    $fileName = self::uniqueFileName(self::sanitizeBaseName(pathinfo($originalName, PATHINFO_FILENAME)), $extension);
    $destination = self::directory() . DS . $fileName;

    if (!move_uploaded_file($tmpName, $destination)) {
      throw new RuntimeException('The uploaded image could not be saved.');
    }

    return self::metadata($destination);
  }

  public static function delete(string $encodedFileName): void
  {
    $fileName = basename(rawurldecode($encodedFileName));
    $path = self::directory() . DS . $fileName;

    if (!is_file($path)) {
      throw new RuntimeException('That image asset could not be found.');
    }

    if (!unlink($path)) {
      throw new RuntimeException('The image asset could not be deleted.');
    }
  }

  private static function metadata(string $path): array
  {
    $fileName = basename($path);
    $encodedName = rawurlencode($fileName);

    return [
      'file_name' => $fileName,
      'path' => $path,
      'url' => markdown_url('images/' . $encodedName),
      'markdown_path' => 'images/' . $encodedName,
      'markdown_snippet' => '![Alt text](images/' . $encodedName . ')',
      'size' => self::humanFileSize((int) filesize($path)),
      'updated_at' => date('M d, Y g:i A', (int) filemtime($path)),
      'extension' => strtoupper((string) pathinfo($fileName, PATHINFO_EXTENSION)),
    ];
  }

  private static function directory(): string
  {
    return public_path('markdown/images');
  }

  private static function ensureDirectory(): void
  {
    $directory = self::directory();

    if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
      throw new RuntimeException('The markdown image directory could not be created.');
    }
  }

  private static function sanitizeBaseName(string $value): string
  {
    $transliterated = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

    if (is_string($transliterated) && $transliterated !== '') {
      $value = $transliterated;
    }

    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? $value;
    $value = trim($value, '-');

    return $value !== '' ? $value : 'image-' . date('Ymd-His');
  }

  private static function uniqueFileName(string $baseName, string $extension): string
  {
    $candidate = $baseName . '.' . $extension;
    $suffix = 2;

    while (is_file(self::directory() . DS . $candidate)) {
      $candidate = $baseName . '-' . $suffix . '.' . $extension;
      $suffix++;
    }

    return $candidate;
  }

  private static function assertMimeType(string $tmpName, string $extension): void
  {
    if ($extension === 'svg') {
      $snippet = file_get_contents($tmpName, false, null, 0, 4096);

      if (!is_string($snippet) || stripos($snippet, '<svg') === false) {
        throw new RuntimeException('The uploaded SVG file is invalid.');
      }

      return;
    }

    $mimeType = function_exists('mime_content_type') ? mime_content_type($tmpName) : false;

    if (!is_string($mimeType) || $mimeType === '') {
      $fileInfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;
      $mimeType = $fileInfo !== false ? finfo_file($fileInfo, $tmpName) : false;

      if ($fileInfo !== false) {
        finfo_close($fileInfo);
      }
    }

    if (!is_string($mimeType) || !in_array($mimeType, self::MIME_TYPES[$extension] ?? [], true)) {
      throw new RuntimeException('The uploaded file does not match the selected image format.');
    }
  }

  private static function humanFileSize(int $bytes): string
  {
    if ($bytes < 1024) {
      return $bytes . ' B';
    }

    $units = ['KB', 'MB', 'GB'];
    $size = $bytes / 1024;
    $unitIndex = 0;

    while ($size >= 1024 && $unitIndex < count($units) - 1) {
      $size /= 1024;
      $unitIndex++;
    }

    return rtrim(rtrim(number_format($size, 1), '0'), '.') . ' ' . $units[$unitIndex];
  }

  private static function uploadErrorMessage(int $errorCode): string
  {
    return match ($errorCode) {
      UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The uploaded image exceeds the maximum upload size.',
      UPLOAD_ERR_PARTIAL => 'The image upload was interrupted. Please try again.',
      UPLOAD_ERR_NO_FILE => 'Choose an image file to upload.',
      UPLOAD_ERR_NO_TMP_DIR => 'The server is missing a temporary upload directory.',
      UPLOAD_ERR_CANT_WRITE => 'The server could not write the uploaded image to disk.',
      UPLOAD_ERR_EXTENSION => 'A PHP extension blocked the image upload.',
      default => 'The image upload failed. Please try again.',
    };
  }
}