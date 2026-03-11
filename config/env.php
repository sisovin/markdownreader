<?php

declare(strict_types=1);

if (!function_exists('loadEnvironment')) {
  function loadEnvironment(?string $file = null): void
  {
    $path = $file ?? dirname(__DIR__) . '/.env';

    if (!is_file($path)) {
      return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines === false) {
      return;
    }

    foreach ($lines as $line) {
      $line = trim($line);

      if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
        continue;
      }

      [$name, $value] = explode('=', $line, 2);
      $name = trim($name);
      $value = trim($value);

      if ($value !== '' && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === '\'' && str_ends_with($value, '\'')))) {
        $value = substr($value, 1, -1);
      }

      if ($name === '' || getenv($name) !== false) {
        continue;
      }

      $_ENV[$name] = $value;
      $_SERVER[$name] = $value;
      putenv(sprintf('%s=%s', $name, $value));
    }
  }
}

if (!function_exists('env')) {
  function env(string $key, mixed $default = null): mixed
  {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    if ($value === false || $value === null || $value === '') {
      return $default;
    }

    if (!is_string($value)) {
      return $value;
    }

    return match (strtolower($value)) {
      'true', '(true)' => true,
      'false', '(false)' => false,
      'null', '(null)' => null,
      default => $value,
    };
  }
}

loadEnvironment();
