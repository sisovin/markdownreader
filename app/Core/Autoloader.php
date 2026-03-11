<?php

declare(strict_types=1);

namespace App\Core;

final class Autoloader
{
  public static function register(string $baseDirectory): void
  {
    spl_autoload_register(static function (string $className) use ($baseDirectory): void {
      $prefix = 'App\\';

      if (!str_starts_with($className, $prefix)) {
        return;
      }

      $relativeClass = substr($className, strlen($prefix));
      $file = rtrim($baseDirectory, '/\\') . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

      if (is_file($file)) {
        require_once $file;
      }
    });
  }
}
