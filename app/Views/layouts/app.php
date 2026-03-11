<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Session;

$pageTitle = isset($title) ? $title . ' | ' . APP_NAME : APP_NAME;
$status = Session::pullFlash('status');
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#08111f">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="<?= e(BRAND_TAGLINE) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="<?= e(asset_url('css/app.css')) ?>">
</head>

<body class="min-h-screen bg-slate-950 text-slate-100">
  <div
    class="absolute inset-x-0 top-0 -z-10 h-[32rem] bg-[radial-gradient(circle_at_top_left,_rgba(45,212,191,0.22),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(56,189,248,0.16),_transparent_26%),linear-gradient(180deg,_rgba(8,17,31,0.98),_rgba(8,17,31,1))]">
  </div>

  <header class="shell pt-5">
    <nav class="glass-panel flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
      <div>
        <a href="<?= e(url('/')) ?>" class="text-lg font-extrabold tracking-tight text-white"><?= e(APP_NAME) ?></a>
        <p class="mt-1 text-sm text-slate-300"><?= e(BRAND_HIGHLIGHT) ?></p>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <a href="<?= e(url('/')) ?>" class="nav-link">Home</a>
        <?php if ($user !== null): ?>
          <a href="<?= e(url(ROUTE_DASHBOARD)) ?>" class="nav-link">Dashboard</a>
          <form action="<?= e(url(ROUTE_LOGOUT)) ?>" method="post" class="inline-flex">
            <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">
            <button type="submit" class="btn-secondary">Logout</button>
          </form>
        <?php else: ?>
          <a href="<?= e(url(ROUTE_LOGIN)) ?>" class="nav-link">Login</a>
          <a href="<?= e(url(ROUTE_SIGNUP)) ?>" class="btn-primary">Sign Up</a>
        <?php endif; ?>
      </div>
    </nav>
  </header>

  <main class="shell pb-16 pt-8">
    <?php if (is_array($status)): ?>
      <div
        class="mb-6 rounded-2xl border px-4 py-3 text-sm font-medium <?= $status['type'] === 'success' ? 'border-emerald-400/40 bg-emerald-400/10 text-emerald-100' : 'border-rose-400/40 bg-rose-400/10 text-rose-100' ?>">
        <?= e($status['message'] ?? '') ?>
      </div>
    <?php endif; ?>

    <?php require $viewPath; ?>
  </main>
</body>

</html>