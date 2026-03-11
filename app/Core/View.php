<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class View
{
  public static function render(string $view, array $data = [], string $layout = 'app'): void
  {
    $viewPath = base_path('app/Views/' . $view . '.php');
    $layoutPath = base_path('app/Views/layouts/' . $layout . '.php');

    if (!is_file($viewPath)) {
      throw new RuntimeException(sprintf('View "%s" does not exist.', $view));
    }

    if (!is_file($layoutPath)) {
      throw new RuntimeException(sprintf('Layout "%s" does not exist.', $layout));
    }

    extract($data, EXTR_SKIP);

    require $layoutPath;
  }
}
