<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;
use App\Models\MarkdownAsset;
use RuntimeException;

final class AdminAssetController
{
  public function store(): void
  {
    Auth::requireAuth();
    $this->ensureValidCsrf();

    try {
      $asset = MarkdownAsset::upload($_FILES['asset'] ?? []);
    } catch (RuntimeException $exception) {
      Session::flash('status', [
        'type' => 'error',
        'message' => $exception->getMessage(),
      ]);

      redirect(ROUTE_DASHBOARD);
    }

    Session::flash('status', [
      'type' => 'success',
      'message' => sprintf('Image uploaded successfully as %s.', $asset['file_name']),
    ]);

    redirect(ROUTE_DASHBOARD);
  }

  public function destroy(string $asset): void
  {
    Auth::requireAuth();
    $this->ensureValidCsrf();

    try {
      MarkdownAsset::delete($asset);
    } catch (RuntimeException $exception) {
      Session::flash('status', [
        'type' => 'error',
        'message' => $exception->getMessage(),
      ]);

      redirect(ROUTE_DASHBOARD);
    }

    Session::flash('status', [
      'type' => 'success',
      'message' => 'Image asset deleted successfully.',
    ]);

    redirect(ROUTE_DASHBOARD);
  }

  private function ensureValidCsrf(): void
  {
    if (Csrf::validate($_POST['_token'] ?? null)) {
      return;
    }

    Session::flash('status', [
      'type' => 'error',
      'message' => 'Your session expired. Please try again.',
    ]);

    redirect(ROUTE_DASHBOARD);
  }
}