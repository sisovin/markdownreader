<?php

declare(strict_types=1);
?>
<section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr] lg:items-start">
  <div class="glass-panel overflow-hidden px-6 py-8 sm:px-8 sm:py-10">
    <div
      class="mb-6 inline-flex items-center gap-2 rounded-full border border-emerald-300/20 bg-emerald-300/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-emerald-200">
      Modern markdown admin experience
    </div>
    <h1 class="max-w-3xl text-4xl font-extrabold leading-tight text-white sm:text-5xl">
      Beautiful reading surfaces, native PHP authentication, and a dashboard designed for daily publishing work.
    </h1>
    <p class="mt-5 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg">
      Launch a lightweight markdown reader with clean sign-in flows, automatic admin access for the first account, and a
      responsive control panel built for desktop and mobile.
    </p>
    <div class="mt-8 flex flex-wrap gap-3">
      <a href="<?= e(url(ROUTE_SIGNUP)) ?>" class="btn-primary">Create your account</a>
      <a href="<?= e(url(ROUTE_LOGIN)) ?>" class="btn-secondary">Login</a>
      <a href="<?= e(url(ROUTE_DASHBOARD)) ?>" class="btn-ghost">Open dashboard</a>
    </div>
    <div class="mt-10 grid gap-4 sm:grid-cols-3">
      <article class="stat-card">
        <p class="stat-label">Markdown documents</p>
        <p class="stat-value"><?= e((string) $stats['documents']) ?></p>
      </article>
      <article class="stat-card">
        <p class="stat-label">Registered users</p>
        <p class="stat-value"><?= e((string) $stats['users']) ?></p>
      </article>
      <article class="stat-card">
        <p class="stat-label">Stack</p>
        <p class="stat-copy"><?= e($stats['stack']) ?></p>
      </article>
    </div>
  </div>

  <aside class="glass-panel px-6 py-8 sm:px-7">
    <p class="eyebrow">Why this build works</p>
    <div class="mt-5 space-y-4 text-sm leading-7 text-slate-300">
      <div>
        <h2 class="text-base font-semibold text-white">Native MVC without framework weight</h2>
        <p class="mt-1">Routing, sessions, CSRF protection, and views stay readable and easy to extend.</p>
      </div>
      <div>
        <h2 class="text-base font-semibold text-white">Responsive interface first</h2>
        <p class="mt-1">The layout uses spacious cards, contrast-rich typography, and touch-friendly actions from the
          first screen.</p>
      </div>
      <div>
        <h2 class="text-base font-semibold text-white">Admin onboarding built in</h2>
        <p class="mt-1">The first account becomes the admin automatically, then lands directly in the dashboard after
          sign-up.</p>
      </div>
    </div>
  </aside>
</section>

<section class="mt-8 grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
  <article class="glass-panel px-6 py-7 sm:px-8">
    <p class="eyebrow">Workflow</p>
    <h2 class="mt-3 text-2xl font-bold text-white">From landing page to dashboard in three small steps</h2>
    <div class="mt-6 space-y-4">
      <div class="step-row">
        <span class="step-number">1</span>
        <div>
          <h3 class="text-base font-semibold text-white">Explore the home page</h3>
          <p class="mt-1 text-sm text-slate-300">Use the overview cards to see how many markdown files and users the
            project already has.</p>
        </div>
      </div>
      <div class="step-row">
        <span class="step-number">2</span>
        <div>
          <h3 class="text-base font-semibold text-white">Create an account or log in</h3>
          <p class="mt-1 text-sm text-slate-300">The forms use CSRF protection, friendly validation feedback, and
            persistent field values on error.</p>
        </div>
      </div>
      <div class="step-row">
        <span class="step-number">3</span>
        <div>
          <h3 class="text-base font-semibold text-white">Manage content in the dashboard</h3>
          <p class="mt-1 text-sm text-slate-300">Review document links, user totals, environment status, and session
            metadata from one place.</p>
        </div>
      </div>
    </div>
  </article>

  <article class="glass-panel px-6 py-7 sm:px-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="eyebrow">Library snapshot</p>
        <h2 class="mt-3 text-2xl font-bold text-white">Available markdown titles</h2>
      </div>
      <span
        class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-300">
        Live from project root
      </span>
    </div>

    <?php if ($documents === []): ?>
      <p class="mt-6 text-sm text-slate-300">No markdown documents were found yet. Add a new `.md` file to the project
        root and it will appear here.</p>
    <?php else: ?>
      <div class="mt-6 grid gap-4">
        <?php foreach ($documents as $document): ?>
          <a href="<?= e($document['url']) ?>" class="document-card">
            <div>
              <p class="text-base font-semibold text-white"><?= e($document['title']) ?></p>
              <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-300"><?= e($document['excerpt']) ?></p>
              <div
                class="mt-3 flex flex-wrap items-center gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                <span><?= e((string) $document['reading_time']) ?> min read</span>
                <span><?= e((string) $document['word_count']) ?> words</span>
                <span>Updated <?= e($document['updated_at']) ?></span>
              </div>
            </div>
            <span class="text-sm font-semibold text-emerald-200">Open reader</span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </article>
</section>