<?php

declare(strict_types=1);

use App\Core\Csrf;

$formError = $errors['form'] ?? null;
?>
<section class="mx-auto grid max-w-6xl gap-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
  <article class="glass-panel px-6 py-8 sm:px-8 sm:py-10">
    <p class="eyebrow">Secure access</p>
    <h1 class="mt-3 text-3xl font-bold text-white sm:text-4xl">Login to your markdown control room</h1>
    <p class="mt-4 text-sm leading-7 text-slate-300">Continue where you left off, review the document library, and
      manage your workspace from the dashboard.</p>
    <div class="mt-8 grid gap-4 sm:grid-cols-2">
      <div class="mini-panel">
        <p class="mini-title">Fast sign-in</p>
        <p class="mini-copy">Clean validation, session protection, and a single path back to your dashboard.</p>
      </div>
      <div class="mini-panel">
        <p class="mini-title">Mobile ready</p>
        <p class="mini-copy">The form stacks smoothly on phones and keeps action buttons easy to reach.</p>
      </div>
    </div>
  </article>

  <article class="glass-panel px-6 py-8 sm:px-8 sm:py-10">
    <p class="eyebrow">Welcome back</p>
    <h2 class="mt-3 text-2xl font-bold text-white">Sign in</h2>

    <?php if ($formError !== null): ?>
      <div class="mt-5 rounded-2xl border border-rose-400/40 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
        <?= e($formError) ?>
      </div>
    <?php endif; ?>

    <form action="<?= e(url(ROUTE_LOGIN)) ?>" method="post" class="mt-6 space-y-5">
      <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">

      <div>
        <label for="email" class="form-label">Email address</label>
        <input id="email" name="email" type="email" value="<?= e($old['email'] ?? '') ?>" class="form-input"
          placeholder="you@example.com" autocomplete="email">
        <?php if (isset($errors['email'])): ?>
          <p class="form-error"><?= e($errors['email']) ?></p>
        <?php endif; ?>
      </div>

      <div>
        <label for="password" class="form-label">Password</label>
        <input id="password" name="password" type="password" class="form-input" placeholder="Enter your password"
          autocomplete="current-password">
        <?php if (isset($errors['password'])): ?>
          <p class="form-error"><?= e($errors['password']) ?></p>
        <?php endif; ?>
      </div>

      <button type="submit" class="btn-primary w-full justify-center">Open dashboard</button>
    </form>

    <p class="mt-5 text-sm text-slate-400">
      Need an account?
      <a href="<?= e(url(ROUTE_SIGNUP)) ?>" class="font-semibold text-emerald-200 hover:text-emerald-100">Create one
        now</a>
    </p>
  </article>
</section>