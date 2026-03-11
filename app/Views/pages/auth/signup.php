<?php

declare(strict_types=1);

use App\Core\Csrf;

$formError = $errors['form'] ?? null;
$isFirstUser = (int) $userCount === 0;
?>
<section class="mx-auto grid max-w-6xl gap-6 lg:grid-cols-[0.92fr_1.08fr] lg:items-start">
  <article class="glass-panel px-6 py-8 sm:px-8 sm:py-10">
    <p class="eyebrow">Create account</p>
    <h1 class="mt-3 text-3xl font-bold text-white sm:text-4xl">Build your admin access in one step</h1>
    <p class="mt-4 text-sm leading-7 text-slate-300">Start with a polished onboarding flow, strong password hashing, and
      a dashboard redirect right after registration.</p>

    <div class="mt-8 space-y-4">
      <div class="mini-panel">
        <p class="mini-title">First account logic</p>
        <p class="mini-copy">
          <?= $isFirstUser ? 'The first account registered right now will become the admin automatically.' : 'The first account is already registered, and new accounts still get dashboard access immediately.' ?>
        </p>
      </div>
      <div class="mini-panel">
        <p class="mini-title">Project stack</p>
        <p class="mini-copy"><?= e(BRAND_HIGHLIGHT) ?></p>
      </div>
    </div>
  </article>

  <article class="glass-panel px-6 py-8 sm:px-8 sm:py-10">
    <p class="eyebrow">Get started</p>
    <h2 class="mt-3 text-2xl font-bold text-white">Sign up</h2>

    <?php if ($formError !== null): ?>
      <div class="mt-5 rounded-2xl border border-rose-400/40 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
        <?= e($formError) ?>
      </div>
    <?php endif; ?>

    <form action="<?= e(url(ROUTE_SIGNUP)) ?>" method="post" class="mt-6 space-y-5">
      <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">

      <div>
        <label for="name" class="form-label">Full name</label>
        <input id="name" name="name" type="text" value="<?= e($old['name'] ?? '') ?>" class="form-input"
          placeholder="Your full name" autocomplete="name">
        <?php if (isset($errors['name'])): ?>
          <p class="form-error"><?= e($errors['name']) ?></p>
        <?php endif; ?>
      </div>

      <div>
        <label for="email" class="form-label">Email address</label>
        <input id="email" name="email" type="email" value="<?= e($old['email'] ?? '') ?>" class="form-input"
          placeholder="you@example.com" autocomplete="email">
        <?php if (isset($errors['email'])): ?>
          <p class="form-error"><?= e($errors['email']) ?></p>
        <?php endif; ?>
      </div>

      <div class="grid gap-5 sm:grid-cols-2">
        <div>
          <label for="password" class="form-label">Password</label>
          <input id="password" name="password" type="password" class="form-input" placeholder="At least 8 characters"
            autocomplete="new-password">
          <?php if (isset($errors['password'])): ?>
            <p class="form-error"><?= e($errors['password']) ?></p>
          <?php endif; ?>
        </div>
        <div>
          <label for="password_confirmation" class="form-label">Confirm password</label>
          <input id="password_confirmation" name="password_confirmation" type="password" class="form-input"
            placeholder="Repeat your password" autocomplete="new-password">
          <?php if (isset($errors['password_confirmation'])): ?>
            <p class="form-error"><?= e($errors['password_confirmation']) ?></p>
          <?php endif; ?>
        </div>
      </div>

      <button type="submit" class="btn-primary w-full justify-center">Create account and continue</button>
    </form>

    <p class="mt-5 text-sm text-slate-400">
      Already registered?
      <a href="<?= e(url(ROUTE_LOGIN)) ?>" class="font-semibold text-emerald-200 hover:text-emerald-100">Login here</a>
    </p>
  </article>
</section>