<?php

declare(strict_types=1);
?>
<section class="space-y-6">
  <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
    <article class="glass-panel px-6 py-8 sm:px-8 sm:py-10">
      <p class="eyebrow">Admin dashboard</p>
      <div class="mt-3 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
        <div>
          <h1 class="text-3xl font-bold text-white sm:text-4xl">Welcome back, <?= e($user['name'] ?? 'Reader') ?></h1>
          <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-300">This control panel keeps your markdown collection,
            project health, and session state visible at a glance.</p>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-200">
          <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Role</p>
          <p class="mt-1 text-lg font-semibold text-white"><?= e(ucfirst((string) ($user['role'] ?? 'member'))) ?></p>
        </div>
      </div>
    </article>

    <article class="glass-panel grid gap-4 px-6 py-8 sm:grid-cols-2 sm:px-8 sm:py-10">
      <div class="stat-card">
        <p class="stat-label">Users</p>
        <p class="stat-value"><?= e((string) $stats['users']) ?></p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Markdown files</p>
        <p class="stat-value"><?= e((string) $stats['documents']) ?></p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Image assets</p>
        <p class="stat-value"><?= e((string) $stats['assets']) ?></p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Environment</p>
        <p class="stat-copy"><?= e($stats['environment']) ?></p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Session</p>
        <p class="stat-copy"><?= e($stats['session']) ?></p>
      </div>
    </article>
  </div>

  <div class="grid gap-6 lg:grid-cols-[0.88fr_1.12fr]">
    <article class="glass-panel px-6 py-7 sm:px-8">
      <p class="eyebrow">Account summary</p>
      <div class="mt-5 space-y-4 text-sm text-slate-300">
        <div class="info-row">
          <span>Name</span>
          <strong><?= e($user['name'] ?? '') ?></strong>
        </div>
        <div class="info-row">
          <span>Email</span>
          <strong><?= e($user['email'] ?? '') ?></strong>
        </div>
        <div class="info-row">
          <span>Role</span>
          <strong><?= e(ucfirst((string) ($user['role'] ?? 'member'))) ?></strong>
        </div>
        <div class="info-row">
          <span>Joined</span>
          <strong><?= e(isset($user['created_at']) ? date('M d, Y', strtotime((string) $user['created_at'])) : 'Today') ?></strong>
        </div>
      </div>

      <div
        class="mt-6 rounded-3xl border border-emerald-300/20 bg-emerald-300/10 p-5 text-sm leading-7 text-emerald-100">
        The first registered account becomes admin automatically. This makes local setup fast while keeping the flow
        intuitive for demos and internal tooling.
      </div>
    </article>

    <article class="glass-panel px-6 py-7 sm:px-8">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p class="eyebrow">Asset library</p>
          <h2 class="mt-3 text-2xl font-bold text-white">Upload and reuse book images</h2>
          <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-300">Manage images stored in /public/markdown/images and
            paste the generated paths directly into your markdown files.</p>
        </div>
      </div>

      <form action="<?= e(admin_asset_upload_url()) ?>" method="post" enctype="multipart/form-data"
        class="asset-upload-panel mt-6">
        <input type="hidden" name="_token" value="<?= e(\App\Core\Csrf::token()) ?>">
        <div>
          <label for="asset" class="form-label">Upload image</label>
          <input id="asset" name="asset" type="file"
            accept="image/png,image/jpeg,image/gif,image/webp,image/avif,image/svg+xml"
            class="form-input file:mr-4 file:rounded-full file:border-0 file:bg-emerald-300 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-950 hover:file:bg-emerald-200">
          <p class="form-help">PNG, JPG, GIF, WEBP, AVIF, or SVG up to 8 MB. Files are saved into
            /public/markdown/images.</p>
        </div>
        <button type="submit" class="btn-primary">Upload image</button>
      </form>

      <?php if (($assets ?? []) === []): ?>
        <div class="empty-state mt-6">
          <p class="text-base font-semibold text-white">No image assets have been uploaded yet.</p>
          <p class="mt-2 text-sm leading-7 text-slate-300">Upload a cover, diagram, or screenshot and then reference it in
            markdown as `images/your-file.png`.</p>
        </div>
      <?php else: ?>
        <div class="asset-grid mt-6">
          <?php foreach (($assets ?? []) as $asset): ?>
            <article class="asset-card">
              <a href="<?= e($asset['url']) ?>" target="_blank" rel="noopener noreferrer" class="asset-preview-link">
                <img src="<?= e($asset['url']) ?>" alt="<?= e($asset['file_name']) ?>" class="asset-preview-image">
              </a>

              <div class="space-y-3">
                <div>
                  <p class="asset-title"><?= e($asset['file_name']) ?></p>
                  <p class="asset-meta"><?= e($asset['extension']) ?> • <?= e($asset['size']) ?> •
                    <?= e($asset['updated_at']) ?></p>
                </div>

                <label class="asset-field-label"
                  for="asset-markdown-path-<?= e(md5((string) $asset['file_name'])) ?>">Markdown path</label>
                <div class="asset-inline-field">
                  <input id="asset-markdown-path-<?= e(md5((string) $asset['file_name'])) ?>" type="text" readonly
                    class="form-input" value="<?= e($asset['markdown_path']) ?>">
                  <button type="button" class="btn-secondary px-4 py-3"
                    data-copy-text="<?= e($asset['markdown_path']) ?>">Copy</button>
                </div>

                <label class="asset-field-label"
                  for="asset-markdown-snippet-<?= e(md5('snippet-' . (string) $asset['file_name'])) ?>">Markdown image
                  snippet</label>
                <div class="asset-inline-field">
                  <input id="asset-markdown-snippet-<?= e(md5('snippet-' . (string) $asset['file_name'])) ?>" type="text"
                    readonly class="form-input" value="<?= e($asset['markdown_snippet']) ?>">
                  <button type="button" class="btn-secondary px-4 py-3"
                    data-copy-text="<?= e($asset['markdown_snippet']) ?>">Copy</button>
                </div>

                <div class="flex flex-wrap gap-3 pt-1">
                  <a href="<?= e($asset['url']) ?>" target="_blank" rel="noopener noreferrer" class="btn-ghost">Open
                    image</a>
                  <form action="<?= e(admin_asset_delete_url($asset['file_name'])) ?>" method="post" class="inline-flex">
                    <input type="hidden" name="_token" value="<?= e(\App\Core\Csrf::token()) ?>">
                    <button type="submit" class="btn-danger"
                      onclick="return confirm('Delete this image asset permanently?');">Delete</button>
                  </form>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </article>
  </div>

  <article class="glass-panel px-6 py-7 sm:px-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="eyebrow">Document library</p>
        <h2 class="mt-3 text-2xl font-bold text-white">Readable markdown collection</h2>
        <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-300">Create new markdown books, edit existing files, or
          remove drafts directly from the admin area.</p>
      </div>
      <div class="flex flex-wrap gap-3">
        <a href="<?= e(admin_document_create_url()) ?>" class="btn-primary">New markdown file</a>
        <a href="<?= e(url('/')) ?>" class="btn-ghost">Back to home</a>
      </div>
    </div>

    <?php if ($documents === []): ?>
      <div class="empty-state mt-6">
        <p class="text-base font-semibold text-white">There are no markdown documents in /public/markdown yet.</p>
        <p class="mt-2 text-sm leading-7 text-slate-300">Create your first draft to start building the library.</p>
        <a href="<?= e(admin_document_create_url()) ?>" class="btn-primary mt-5">Create first document</a>
      </div>
    <?php else: ?>
      <div class="mt-6 grid gap-4">
        <?php foreach ($documents as $document): ?>
          <article class="document-card-stack">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
              <div>
                <p class="text-base font-semibold text-white"><?= e($document['title']) ?></p>
                <p class="mt-1 text-sm text-slate-400"><?= e($document['file_name']) ?></p>
                <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-300"><?= e($document['excerpt']) ?></p>
              </div>
              <div class="text-sm text-slate-300 lg:text-right">
                <p><?= e((string) $document['reading_time']) ?> min read</p>
                <p class="mt-1"><?= e($document['updated_at']) ?></p>
              </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-3">
              <a href="<?= e($document['url']) ?>" class="btn-ghost">Open reader</a>
              <a href="<?= e(admin_document_edit_url($document['file_name'])) ?>" class="btn-secondary">Edit</a>
              <form action="<?= e(admin_document_delete_url($document['file_name'])) ?>" method="post" class="inline-flex">
                <input type="hidden" name="_token" value="<?= e(\App\Core\Csrf::token()) ?>">
                <button type="submit" class="btn-danger"
                  onclick="return confirm('Delete this markdown file permanently?');">Delete</button>
              </form>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </article>
</section>

<script>
  (() => {
    const buttons = document.querySelectorAll('[data-copy-text]');

    if (buttons.length === 0 || !navigator.clipboard) {
      return;
    }

    buttons.forEach((button) => {
      button.addEventListener('click', async () => {
        const originalText = button.textContent;

        try {
          await navigator.clipboard.writeText(button.dataset.copyText || '');
          button.textContent = 'Copied';
          window.setTimeout(() => {
            button.textContent = originalText;
          }, 1200);
        } catch {
          button.textContent = 'Copy failed';
          window.setTimeout(() => {
            button.textContent = originalText;
          }, 1200);
        }
      });
    });
  })();
</script>