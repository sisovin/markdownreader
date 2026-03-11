<?php

declare(strict_types=1);
?>
<section class="space-y-6">
  <article class="glass-panel overflow-hidden px-6 py-8 sm:px-8 sm:py-10">
    <div class="grid gap-8 xl:grid-cols-[1.08fr_0.92fr] xl:items-start">
      <div>
        <a href="<?= e(url('/')) ?>" class="btn-ghost -ml-4 mb-4">Back to library</a>
        <p class="eyebrow">Markdown reader</p>
        <h1 class="mt-3 max-w-4xl text-3xl font-extrabold leading-tight text-white sm:text-5xl">
          <?= e($book['title']) ?>
        </h1>
        <p class="mt-5 max-w-3xl text-base leading-8 text-slate-300 sm:text-lg">
          <?= e($book['excerpt']) ?>
        </p>

        <div class="mt-8 flex flex-wrap gap-3">
          <span class="meta-chip"><?= e((string) $book['reading_time']) ?> min read</span>
          <span class="meta-chip"><?= e((string) $book['word_count']) ?> words</span>
          <span class="meta-chip">Updated <?= e($book['updated_at']) ?></span>
          <a href="<?= e($book['raw_url']) ?>" class="btn-secondary">Open raw markdown</a>
        </div>
      </div>

      <div class="space-y-4">
        <?php if (is_string($book['cover_image'] ?? null) && $book['cover_image'] !== ''): ?>
          <img src="<?= e($book['cover_image']) ?>" alt="<?= e($book['title']) ?> cover preview"
            class="w-full rounded-[2rem] border border-white/10 bg-white/5 object-cover shadow-2xl shadow-slate-950/40">
        <?php endif; ?>

        <div class="reader-panel">
          <p class="eyebrow">Reader notes</p>
          <p class="mt-3 text-sm leading-7 text-slate-300">
            Relative images and markdown links are normalized for browser reading, so the original book files stay
            intact while the reading surface remains clean and mobile-friendly.
          </p>
        </div>
      </div>
    </div>
  </article>

  <div class="reader-layout">
    <aside class="reader-sidebar space-y-6">
      <div class="reader-panel">
        <p class="eyebrow">Contents</p>

        <?php if ($toc === []): ?>
          <p class="mt-4 text-sm leading-7 text-slate-300">This document has no heading structure yet, so the reader is
            displaying the full text without a table of contents.</p>
        <?php else: ?>
          <nav class="mt-4 space-y-1">
            <?php foreach ($toc as $item): ?>
              <a href="#<?= e($item['id']) ?>"
                class="toc-link <?= $item['level'] === 2 ? 'toc-link-level-2' : ($item['level'] === 3 ? 'toc-link-level-3' : '') ?>">
                <?= e($item['text']) ?>
              </a>
            <?php endforeach; ?>
          </nav>
        <?php endif; ?>
      </div>

      <?php if ($relatedDocuments !== []): ?>
        <div class="reader-panel">
          <p class="eyebrow">Continue reading</p>
          <div class="mt-4 space-y-3">
            <?php foreach ($relatedDocuments as $relatedDocument): ?>
              <a href="<?= e($relatedDocument['url']) ?>"
                class="block rounded-3xl border border-white/10 bg-white/5 px-4 py-4 transition duration-200 hover:border-emerald-300/30 hover:bg-white/8">
                <p class="text-sm font-semibold text-white"><?= e($relatedDocument['title']) ?></p>
                <p class="mt-2 text-sm leading-7 text-slate-300"><?= e($relatedDocument['excerpt']) ?></p>
                <p class="mt-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                  <?= e((string) $relatedDocument['reading_time']) ?> min read
                </p>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </aside>

    <article class="glass-panel order-1 px-6 py-8 sm:px-8 sm:py-10 xl:order-2">
      <div class="reader-markdown">
        <?= $bookHtml ?>
      </div>
    </article>
  </div>
</section>