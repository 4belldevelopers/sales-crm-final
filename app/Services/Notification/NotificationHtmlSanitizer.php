<?php

namespace App\Services\Notification;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

/**
 * NotificationHtmlSanitizer — Server-Side Allowlist HTML Sanitizer.
 *
 * Intent: Neutralizes Cross-Site Scripting (XSS), script injections, malicious URIs,
 * and dangerous DOM attributes in email template authoring and live preview drawers.
 * Uses native PHP DOMDocument and XPath for deterministic, secure HTML parsing.
 * Tenancy: Global Super Admin scope (tenant_id = 0).
 */
class NotificationHtmlSanitizer
{
    /**
     * Forbidden HTML tags that must be entirely removed along with their children.
     *
     * @var array<string>
     */
    protected const FORBIDDEN_TAGS_WITH_CHILDREN = [
        'script',
        'iframe',
        'object',
        'embed',
        'applet',
        'meta',
        'link',
        'base',
        'frame',
        'frameset',
        'canvas',
    ];

    /**
     * Interactive/Form elements whose wrapper tag is unwrap/stripped to avoid unauthorized actions.
     *
     * @var array<string>
     */
    protected const STRIP_TAGS = [
        'form',
        'input',
        'button',
        'select',
        'textarea',
        'option',
        'optgroup',
    ];

    /**
     * Allowed URL schemes in href / src attributes.
     *
     * @var array<string>
     */
    protected const ALLOWED_SCHEMES = [
        'http',
        'https',
        'mailto',
        'tel',
    ];

    /**
     * Sanitize rich HTML template body.
     */
    public function sanitize(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        // Pre-clean dangerous expressions and null bytes
        $cleaned = str_replace(chr(0), '', $html);

        // Remove XML processing instructions and doctype
        $cleaned = preg_replace('/<\?xml.*?\?>/i', '', $cleaned);

        $libxmlState = libxml_use_internal_errors(true);
        $doc = new DOMDocument('1.0', 'UTF-8');

        // Use mb_convert_encoding to ensure proper UTF-8 HTML entities
        $encodedHtml = mb_convert_encoding($cleaned, 'HTML-ENTITIES', 'UTF-8');

        // Wrap with container to retain fragments
        $wrapper = '<div id="jero-email-wrapper">' . $encodedHtml . '</div>';
        $doc->loadHTML($wrapper, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new DOMXPath($doc);

        // 1. Remove dangerous tags and their content
        foreach (self::FORBIDDEN_TAGS_WITH_CHILDREN as $tag) {
            $elements = $xpath->query("//{$tag}");
            if ($elements) {
                foreach ($elements as $el) {
                    $el->parentNode?->removeChild($el);
                }
            }
        }

        // 2. Strip interactive/form elements but preserve child nodes
        foreach (self::STRIP_TAGS as $tag) {
            $elements = $xpath->query("//{$tag}");
            if ($elements) {
                foreach ($elements as $el) {
                    $parent = $el->parentNode;
                    if ($parent) {
                        while ($el->firstChild) {
                            $parent->insertBefore($el->firstChild, $el);
                        }
                        $parent->removeChild($el);
                    }
                }
            }
        }

        // 3. Inspect all remaining elements and clean attributes
        $allElements = $xpath->query('//*');
        if ($allElements) {
            foreach ($allElements as $element) {
                if ($element instanceof DOMElement) {
                    $this->cleanElementAttributes($element);
                }
            }
        }

        // Extract inner HTML of wrapper
        $wrapperNode = $doc->getElementById('jero-email-wrapper');
        $output = '';
        if ($wrapperNode) {
            foreach ($wrapperNode->childNodes as $child) {
                $output .= $doc->saveHTML($child);
            }
        } else {
            $output = $doc->saveHTML();
        }

        libxml_clear_errors();
        libxml_use_internal_errors($libxmlState);

        // Fallback safety regex sweep for residual on* event handlers
        $output = preg_replace('/\s+on[a-zA-Z]+\s*=\s*([\'"][^\'"]*[\'"]|[^\s>]+)/i', '', $output);

        return trim($output);
    }

    /**
     * Sanitize email subject line (strip all tags and control characters).
     */
    public function sanitizeSubject(string $subject): string
    {
        $subject = strip_tags($subject);
        $subject = preg_replace('/[\r\n\t]+/', ' ', $subject);
        return trim(preg_replace('/\s+/', ' ', $subject));
    }

    /**
     * Clean element attributes: remove on* event handlers, sanitize CSS and URIs.
     */
    protected function cleanElementAttributes(DOMElement $element): void
    {
        $attributesToRemove = [];

        foreach ($element->attributes as $attr) {
            $name = strtolower($attr->name);
            $value = trim($attr->value);

            // Strip inline event handlers: onclick, onload, onerror, etc.
            if (str_starts_with($name, 'on')) {
                $attributesToRemove[] = $attr->name;
                continue;
            }

            // Clean href, src, background, formaction attributes
            if (in_array($name, ['href', 'src', 'background', 'action', 'data', 'dynsrc', 'lowsrc'], true)) {
                if (! $this->isSafeUri($value)) {
                    $attributesToRemove[] = $attr->name;
                    continue;
                }
            }

            // Clean inline style attribute
            if ($name === 'style') {
                $safeStyle = $this->cleanInlineStyle($value);
                if ($safeStyle === '') {
                    $attributesToRemove[] = $attr->name;
                } else {
                    $element->setAttribute('style', $safeStyle);
                }
                continue;
            }
        }

        foreach ($attributesToRemove as $attrName) {
            $element->removeAttribute($attrName);
        }
    }

    /**
     * Check if a URI uses a safe scheme (http, https, mailto, tel, anchor, or merge token).
     */
    public function isSafeUri(string $uri): bool
    {
        $uri = trim($uri);

        // Allow merge tokens in URL context, e.g. [{JOB_LINK}] or [URL_LINK]
        if (preg_match('/^(\[\{[^}]+\}\]|\[[A-Za-z0-9_-]+\]|\{\{[^}]+\}\})$/', $uri)) {
            return true;
        }

        // Allow anchor links
        if (str_starts_with($uri, '#')) {
            return true;
        }

        // Allow protocol-relative URLs
        if (str_starts_with($uri, '//')) {
            return true;
        }

        // Check for dangerous schemes
        $lower = strtolower($uri);
        if (str_starts_with($lower, 'javascript:') ||
            str_starts_with($lower, 'data:') ||
            str_starts_with($lower, 'vbscript:') ||
            str_starts_with($lower, 'file:') ||
            str_starts_with($lower, 'blob:')) {
            return false;
        }

        // Parse scheme if present
        $colonPos = strpos($uri, ':');
        if ($colonPos !== false) {
            $scheme = strtolower(substr($uri, 0, $colonPos));
            if (! in_array($scheme, self::ALLOWED_SCHEMES, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Clean inline CSS style: strip expression(), javascript URLs, and dangerous bindings.
     */
    protected function cleanInlineStyle(string $style): string
    {
        $style = preg_replace('/expression\s*\([^)]*\)/i', '', $style);
        $style = preg_replace('/url\s*\(\s*[\'"]?\s*javascript:[^)]*\)/i', '', $style);
        $style = preg_replace('/url\s*\(\s*[\'"]?\s*data:[^)]*\)/i', '', $style);
        $style = preg_replace('/-moz-binding/i', '', $style);
        $style = preg_replace('/behavior\s*:/i', '', $style);

        return trim($style);
    }

    /**
     * Wrap sanitized HTML into a secure responsive preview envelope for sandboxed iframes.
     */
    public function wrapForSandboxedPreview(string $safeHtml, string $title = 'Email Preview'): string
    {
        $escapedTitle = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$escapedTitle}</title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #1e293b;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        .jero-email-preview-container {
            max-width: 680px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 32px 36px;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }
        table {
            max-width: 100% !important;
            border-collapse: collapse;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        a {
            color: #009ef7;
            text-decoration: underline;
        }
        .preview-watermark {
            display: block;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #cbd5e1;
        }
    </style>
</head>
<body>
    <div class="jero-email-preview-container">
        <div class="preview-watermark">
            🔒 Simulated Template Preview &bull; No External Dispatch &bull; Authoritative Sandbox
        </div>
        {$safeHtml}
    </div>
</body>
</html>
HTML;
    }
}
