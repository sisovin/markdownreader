<?php

declare(strict_types=1);
?>
<section class="mx-auto max-w-4xl">
  <article class="glass-panel px-6 py-10 text-center sm:px-10 sm:py-14">
    <p class="eyebrow">Missing document</p>
    <h1 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">That markdown book could not be found.</h1>
    <p class="mx-auto mt-5 max-w-2xl text-sm leading-8 text-slate-300 sm:text-base">
      The requested file <span class="font-semibold text-white"><?= e($requested ?? 'unknown document') ?></span> is not
      available in the project root or the link no longer matches the file name.
    </p>

    <div class="mt-8 flex flex-wrap justify-center gap-3">
      <a href="<?= e(url('/')) ?>" class="btn-primary">Return to home</a>
      <a href="<?= e(url(ROUTE_DASHBOARD)) ?>" class="btn-secondary">Open dashboard</a>
    </div>
  </article>
</section>