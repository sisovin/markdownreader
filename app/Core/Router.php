<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class Router
{
  private array $routes = [
    'GET' => [],
    'POST' => [],
  ];

  public function get(string $path, array $handler): void
  {
    $this->routes['GET'][] = [
      'path' => $this->normalize($path),
      'handler' => $handler,
    ];
  }

  public function post(string $path, array $handler): void
  {
    $this->routes['POST'][] = [
      'path' => $this->normalize($path),
      'handler' => $handler,
    ];
  }

  public function dispatch(string $path, string $method): void
  {
    $normalizedPath = $this->normalize($path);
    $normalizedMethod = strtoupper($method);
    $matchedRoute = $this->match($normalizedMethod, $normalizedPath);

    if ($matchedRoute === null) {
      http_response_code(404);
      echo 'Page not found.';
      return;
    }

    [$handler, $parameters] = $matchedRoute;

    [$controllerClass, $controllerMethod] = $handler;

    if (!class_exists($controllerClass)) {
      throw new RuntimeException(sprintf('Controller "%s" was not found.', $controllerClass));
    }

    $controller = new $controllerClass();

    if (!method_exists($controller, $controllerMethod)) {
      throw new RuntimeException(sprintf('Method "%s::%s" was not found.', $controllerClass, $controllerMethod));
    }

    $controller->{$controllerMethod}(...array_values($parameters));
  }

  private function match(string $method, string $path): ?array
  {
    foreach ($this->routes[$method] ?? [] as $route) {
      $parameterNames = [];
      $pattern = preg_replace_callback(
        '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
        static function (array $matches) use (&$parameterNames): string {
          $parameterNames[] = $matches[1];

          return '___ROUTE_PARAM___';
        },
        $route['path']
      );

      $pattern = preg_quote($pattern, '#');
      $pattern = str_replace('___ROUTE_PARAM___', '([^/]+)', $pattern);
      $pattern = '#^' . $pattern . '$#';

      if (!preg_match($pattern, $path, $matches)) {
        continue;
      }

      array_shift($matches);
      $parameters = [];

      foreach ($parameterNames as $index => $name) {
        $parameters[$name] = $matches[$index] ?? null;
      }

      return [$route['handler'], $parameters];
    }

    return null;
  }

  private function normalize(string $path): string
  {
    $trimmed = trim($path);

    if ($trimmed === '') {
      return '/';
    }

    $normalized = '/' . ltrim($trimmed, '/');

    return rtrim($normalized, '/') ?: '/';
  }
}
