<?php

declare(strict_types=1);

use App\Core\Csrf;

$values = array_merge($document ?? [], $old ?? []);
$isEdit = ($mode ?? 'create') === 'edit';
$formAction = $isEdit ? admin_document_update_url((string) ($currentFileName ?? '')) : url(ROUTE_ADMIN_DOCUMENTS);
$formHeading = $isEdit ? 'Edit markdown file' : 'Create markdown file';
$formDescription = $isEdit
  ? 'Update the title, file name, or markdown content without leaving the admin area.'
  : 'Create a new markdown book with a clean filename and polished reader output.';
?>
<section class="space-y-6">
  <article class="glass-panel overflow-hidden px-6 py-8 sm:px-8 sm:py-10">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
      <div>
        <a href="<?= e(url(ROUTE_DASHBOARD)) ?>" class="btn-ghost -ml-4 mb-4">Back to dashboard</a>
        <p class="eyebrow">Admin editor</p>
        <h1 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl"><?= e($formHeading) ?></h1>
        <p class="mt-4 max-w-3xl text-sm leading-8 text-slate-300 sm:text-base"><?= e($formDescription) ?></p>
      </div>
      <div class="flex flex-wrap gap-3">
        <?php if ($isEdit && isset($readerUrl)): ?>
          <a href="<?= e((string) $readerUrl) ?>" class="btn-secondary">Open reader</a>
        <?php endif; ?>
        <a href="<?= e(url(ROUTE_DASHBOARD)) ?>" class="btn-ghost">Manage files</a>
      </div>
    </div>
  </article>

  <div class="editor-layout">
    <article class="glass-panel order-1 px-6 py-8 sm:px-8 sm:py-10">
      <?php if (isset($errors['form'])): ?>
        <div class="mb-6 rounded-2xl border border-rose-400/40 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
          <?= e($errors['form']) ?>
        </div>
      <?php endif; ?>

      <form action="<?= e($formAction) ?>" method="post" class="space-y-6" data-markdown-editor
        data-preview-url="<?= e(admin_document_preview_url()) ?>">
        <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">

        <div class="grid gap-5 lg:grid-cols-[1.1fr_0.9fr]">
          <div>
            <label for="title" class="form-label">Document title</label>
            <input id="title" name="title" type="text" class="form-input"
              value="<?= e((string) ($values['title'] ?? '')) ?>" placeholder="Getting Started with Markdown">
            <?php if (isset($errors['title'])): ?>
              <p class="form-error"><?= e($errors['title']) ?></p>
            <?php endif; ?>
          </div>

          <div>
            <label for="file_name" class="form-label">File name</label>
            <input id="file_name" name="file_name" type="text" class="form-input"
              value="<?= e((string) ($values['file_name'] ?? '')) ?>" placeholder="getting-started-with-markdown">
            <p class="form-help">Use a plain file name. The `.md` extension is added automatically.</p>
            <?php if (isset($errors['file_name'])): ?>
              <p class="form-error"><?= e($errors['file_name']) ?></p>
            <?php endif; ?>
          </div>
        </div>

        <div class="editor-workbench">
          <div>
            <div class="mb-3 flex items-center justify-between gap-3">
              <label for="content" class="form-label mb-0">Markdown content</label>
              <span class="preview-status" data-preview-status>Live preview ready</span>
            </div>

            <textarea id="content" name="content" class="form-textarea" data-preview-content
              placeholder="# Document title&#10;&#10;Write your markdown here...\n"><?= e((string) ($values['content'] ?? '')) ?></textarea>
            <p class="form-help">If the content does not begin with a heading, the editor will prepend `# Title`
              automatically when saving.</p>
            <?php if (isset($errors['content'])): ?>
              <p class="form-error"><?= e($errors['content']) ?></p>
            <?php endif; ?>
          </div>

          <section class="preview-pane" aria-live="polite" aria-label="Live markdown preview">
            <div class="preview-pane-header">
              <div>
                <p class="eyebrow">Live preview</p>
                <p class="mt-2 text-sm leading-7 text-slate-300">This pane uses the same PHP renderer as the public
                  reader.</p>
              </div>
              <button type="button" class="btn-ghost px-3 py-2" data-preview-refresh>Refresh</button>
            </div>

            <div class="preview-pane-meta" data-preview-meta>Waiting for content...</div>

            <div class="preview-empty" data-preview-empty>
              Start typing to see headings, images, links, and formatting rendered here.
            </div>

            <div class="preview-toc hidden" data-preview-toc-wrap>
              <p class="preview-toc-title">Preview contents</p>
              <nav class="mt-3 space-y-1" data-preview-toc></nav>
            </div>

            <article class="preview-surface hidden" data-preview-surface>
              <div class="reader-markdown" data-preview-output></div>
            </article>
          </section>
        </div>

        <div class="flex flex-wrap gap-3">
          <button type="submit" class="btn-primary"><?= $isEdit ? 'Save changes' : 'Create markdown file' ?></button>
          <a href="<?= e(url(ROUTE_DASHBOARD)) ?>" class="btn-ghost">Cancel</a>
        </div>
      </form>
    </article>

    <aside class="editor-sidebar">
      <div class="reader-panel">
        <p class="eyebrow">Writing tips</p>
        <div class="mt-4 space-y-3 text-sm leading-7 text-slate-300">
          <p>Use headings to generate the reader table of contents automatically.</p>
          <p>Markdown files and book assets are stored under /public/markdown.</p>
          <p>Relative image paths like `images/example.png` continue to work in the reader.</p>
          <p>External links open safely in a new tab from the reader page.</p>
        </div>
      </div>

      <div class="reader-panel">
        <p class="eyebrow">Example starter</p>
        <pre
          class="mt-4 overflow-x-auto rounded-3xl border border-white/10 bg-slate-950 px-4 py-4 text-sm leading-7 text-sky-100"><code># Your Document Title

Write a short introduction.

## Section One

- First point
- Second point

![Cover image](images/example.png)</code></pre>
      </div>
    </aside>
  </div>
</section>

<script>
  (() => {
    const form = document.querySelector('[data-markdown-editor]');

    if (!form) {
      return;
    }

    const titleInput = form.querySelector('#title');
    const contentInput = form.querySelector('[data-preview-content]');
    const tokenInput = form.querySelector('input[name="_token"]');
    const output = form.querySelector('[data-preview-output]');
    const surface = form.querySelector('[data-preview-surface]');
    const emptyState = form.querySelector('[data-preview-empty]');
    const tocWrap = form.querySelector('[data-preview-toc-wrap]');
    const toc = form.querySelector('[data-preview-toc]');
    const status = form.querySelector('[data-preview-status]');
    const meta = form.querySelector('[data-preview-meta]');
    const refreshButton = form.querySelector('[data-preview-refresh]');
    const previewUrl = form.dataset.previewUrl;

    if (!titleInput || !contentInput || !tokenInput || !output || !surface || !emptyState || !tocWrap || !toc || !status || !meta || !refreshButton || !previewUrl) {
      return;
    }

    let controller = null;
    let debounceId = 0;
    let requestSequence = 0;

    const setStatus = (text, tone = 'idle') => {
      status.textContent = text;
      status.dataset.state = tone;
    };

    const showEmpty = (message) => {
      emptyState.textContent = message;
      emptyState.classList.remove('hidden');
      surface.classList.add('hidden');
      tocWrap.classList.add('hidden');
      output.innerHTML = '';
      toc.innerHTML = '';
    };

    const renderToc = (items) => {
      toc.innerHTML = '';

      if (!Array.isArray(items) || items.length === 0) {
        tocWrap.classList.add('hidden');
        return;
      }

      tocWrap.classList.remove('hidden');

      items.forEach((item) => {
        const link = document.createElement('a');
        const level = Number(item.level || 1);
        link.href = `#${item.id || ''}`;
        link.className = `toc-link ${level === 2 ? 'toc-link-level-2' : (level === 3 ? 'toc-link-level-3' : '')}`;
        link.textContent = item.text || 'Untitled section';
        toc.appendChild(link);
      });
    };

    const updateMeta = () => {
      const text = contentInput.value.trim();
      const words = text === '' ? 0 : text.split(/\s+/).filter(Boolean).length;
      const minutes = Math.max(1, Math.ceil(Math.max(words, 1) / 220));
      meta.textContent = words === 0 ? 'Waiting for content...' : `${words} words • about ${minutes} min read`;
    };

    const requestPreview = async () => {
      const sequence = ++requestSequence;

      if (controller) {
        controller.abort();
      }

      controller = new AbortController();
      updateMeta();

      const title = titleInput.value.trim();
      const content = contentInput.value;

      if (title === '' && content.trim() === '') {
        setStatus('Live preview ready');
        showEmpty('Start typing to see headings, images, links, and formatting rendered here.');
        return;
      }

      setStatus('Updating preview...', 'loading');

      try {
        const response = await fetch(previewUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: new URLSearchParams({
            _token: tokenInput.value,
            title,
            file_name: form.querySelector('#file_name')?.value || '',
            content,
          }),
          signal: controller.signal,
        });

        const payload = await response.json();

        if (sequence !== requestSequence) {
          return;
        }

        if (!response.ok) {
          throw new Error(payload.message || 'Preview could not be generated.');
        }

        if (!payload.html) {
          setStatus(payload.message || 'Live preview ready');
          showEmpty(payload.message || 'Start typing to see a live preview.');
          return;
        }

        output.innerHTML = payload.html;
        renderToc(payload.toc || []);
        emptyState.classList.add('hidden');
        surface.classList.remove('hidden');
        setStatus(payload.message || 'Preview updated', 'success');
      } catch (error) {
        if (error.name === 'AbortError') {
          return;
        }

        setStatus('Preview unavailable', 'error');
        showEmpty(error.message || 'Preview could not be generated.');
      }
    };

    const schedulePreview = () => {
      window.clearTimeout(debounceId);
      debounceId = window.setTimeout(requestPreview, 220);
    };

    titleInput.addEventListener('input', schedulePreview);
    contentInput.addEventListener('input', schedulePreview);
    form.querySelector('#file_name')?.addEventListener('input', schedulePreview);
    refreshButton.addEventListener('click', requestPreview);

    updateMeta();
    requestPreview();
  })();
</script>