<?php

declare(strict_types=1);

namespace App\Core;

use DOMDocument;
use DOMElement;
use DOMXPath;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

final class MarkdownRenderer
{
  private MarkdownConverter $converter;

  public function __construct()
  {
    $environment = new Environment([
      'html_input' => 'allow',
      'allow_unsafe_links' => false,
      'max_nesting_level' => 50,
    ]);

    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new GithubFlavoredMarkdownExtension());

    $this->converter = new MarkdownConverter($environment);
  }

  public function render(string $markdown): array
  {
    $html = $this->converter->convert($markdown)->getContent();

    return $this->decorate($html);
  }

  private function decorate(string $html): array
  {
    $previousSetting = libxml_use_internal_errors(true);
    $document = new DOMDocument('1.0', 'UTF-8');
    $document->loadHTML(
      mb_convert_encoding('<!DOCTYPE html><html><body>' . $html . '</body></html>', 'HTML-ENTITIES', 'UTF-8')
    );
    libxml_clear_errors();
    libxml_use_internal_errors($previousSetting);

    $xpath = new DOMXPath($document);
    $toc = $this->decorateHeadings($xpath);
    $this->decorateImages($xpath);
    $this->decorateLinks($xpath);

    $body = $document->getElementsByTagName('body')->item(0);
    $content = '';

    if ($body !== null) {
      foreach ($body->childNodes as $childNode) {
        $content .= $document->saveHTML($childNode);
      }
    }

    return [
      'html' => $content,
      'toc' => $toc,
    ];
  }

  private function decorateHeadings(DOMXPath $xpath): array
  {
    $nodes = $xpath->query('//h1 | //h2 | //h3');
    $usedIds = [];
    $toc = [];

    if ($nodes === false) {
      return $toc;
    }

    foreach ($nodes as $node) {
      if (!$node instanceof DOMElement) {
        continue;
      }

      $text = trim($node->textContent);

      if ($text === '') {
        continue;
      }

      $level = (int) substr($node->tagName, 1);
      $id = $this->uniqueSlug($text, $usedIds);

      $node->setAttribute('id', $id);
      $toc[] = [
        'id' => $id,
        'level' => $level,
        'text' => $text,
      ];
    }

    return $toc;
  }

  private function decorateImages(DOMXPath $xpath): void
  {
    $nodes = $xpath->query('//img');

    if ($nodes === false) {
      return;
    }

    foreach ($nodes as $node) {
      if (!$node instanceof DOMElement) {
        continue;
      }

      $src = trim($node->getAttribute('src'));

      if ($src !== '') {
        $node->setAttribute('src', $this->resolveUrl($src));
      }

      $node->setAttribute('loading', 'lazy');
      $node->setAttribute('decoding', 'async');
    }
  }

  private function decorateLinks(DOMXPath $xpath): void
  {
    $nodes = $xpath->query('//a');

    if ($nodes === false) {
      return;
    }

    foreach ($nodes as $node) {
      if (!$node instanceof DOMElement) {
        continue;
      }

      $href = trim($node->getAttribute('href'));

      if ($href === '') {
        continue;
      }

      if (preg_match('#^(?:[a-z][a-z0-9+.-]*:)?//#i', $href) === 1) {
        $node->setAttribute('target', '_blank');
        $node->setAttribute('rel', 'noopener noreferrer');
        continue;
      }

      $node->setAttribute('href', $this->resolveUrl($href));
    }
  }

  private function resolveUrl(string $value): string
  {
    if ($value === '' || str_starts_with($value, '#') || str_starts_with($value, 'mailto:') || str_starts_with($value, 'tel:') || str_starts_with($value, 'data:')) {
      return $value;
    }

    $parts = parse_url($value);

    if ($parts === false) {
      return $value;
    }

    if (isset($parts['scheme']) || isset($parts['host'])) {
      return $value;
    }

    $path = $parts['path'] ?? '';
    $query = isset($parts['query']) ? '?' . $parts['query'] : '';
    $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

    if ($path === '') {
      return $query . $fragment;
    }

    $normalizedPath = str_replace('\\', '/', $path);

    while (str_starts_with($normalizedPath, './')) {
      $normalizedPath = substr($normalizedPath, 2);
    }

    $encodedPath = $this->encodePath($normalizedPath);

    if (str_ends_with(strtolower($normalizedPath), '.md')) {
      return book_url(basename(rawurldecode($normalizedPath))) . $query . $fragment;
    }

    if (str_starts_with($normalizedPath, '/')) {
      return url('/' . ltrim($encodedPath, '/')) . $query . $fragment;
    }

    return markdown_url(ltrim($encodedPath, '/')) . $query . $fragment;
  }

  private function encodePath(string $path): string
  {
    $prefix = str_starts_with($path, '/') ? '/' : '';
    $segments = explode('/', ltrim($path, '/'));
    $segments = array_map(
      static fn(string $segment): string => rawurlencode(rawurldecode($segment)),
      array_filter($segments, static fn(string $segment): bool => $segment !== '')
    );

    return $prefix . implode('/', $segments);
  }

  private function uniqueSlug(string $text, array &$usedIds): string
  {
    $slug = strtolower($text);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug) ?? $slug;
    $slug = trim($slug, '-');
    $slug = $slug !== '' ? $slug : 'section';
    $candidate = $slug;
    $suffix = 2;

    while (in_array($candidate, $usedIds, true)) {
      $candidate = $slug . '-' . $suffix;
      $suffix++;
    }

    $usedIds[] = $candidate;

    return $candidate;
  }
}