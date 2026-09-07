<?php

namespace Tests\Unit;

use App\Support\OpenRedirect;
use Tests\TestCase;

/**
 * V2_login Phase 6.1 — OpenRedirect::sanitize() correctness.
 *
 * Covers every attack vector the IMPLEMENTATION_PLAN Phase 0.4 documents.
 * No DB, no Redis required — pure function under Laravel config.
 */
class OpenRedirectTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'https://jero.works']);
    }

    public function test_relative_path_is_preserved(): void
    {
        $this->assertSame('/internal/page', OpenRedirect::sanitize('/internal/page'));
    }

    public function test_relative_path_with_query_and_fragment_is_preserved(): void
    {
        $this->assertSame('/page?foo=bar#sec', OpenRedirect::sanitize('/page?foo=bar#sec'));
    }

    public function test_absolute_url_to_same_host_is_preserved(): void
    {
        $result = OpenRedirect::sanitize('https://jero.works/dashboard');
        $this->assertSame('/dashboard', $result);
    }

    public function test_null_returns_null(): void
    {
        $this->assertNull(OpenRedirect::sanitize(null));
    }

    public function test_empty_string_returns_null(): void
    {
        $this->assertNull(OpenRedirect::sanitize(''));
    }

    public function test_external_https_url_is_blocked(): void
    {
        $this->assertNull(OpenRedirect::sanitize('https://evil.com'));
    }

    public function test_protocol_relative_url_is_blocked(): void
    {
        // //evil.com is interpreted by browsers as https://evil.com
        $this->assertNull(OpenRedirect::sanitize('//evil.com'));
    }

    public function test_backslash_trick_is_blocked(): void
    {
        // /\evil.com — some parsers treat \ as /
        $this->assertNull(OpenRedirect::sanitize('/\\evil.com'));
    }

    public function test_backslash_only_url_is_blocked(): void
    {
        $this->assertNull(OpenRedirect::sanitize('\\evil.com'));
    }

    public function test_javascript_scheme_is_blocked(): void
    {
        $this->assertNull(OpenRedirect::sanitize('javascript:alert(1)'));
    }

    public function test_data_scheme_is_blocked(): void
    {
        $this->assertNull(OpenRedirect::sanitize('data:text/html,<script>'));
    }

    public function test_vbscript_scheme_is_blocked(): void
    {
        $this->assertNull(OpenRedirect::sanitize('vbscript:msgbox(1)'));
    }

    public function test_url_with_userinfo_is_blocked(): void
    {
        // https://jero.works@evil.com — browser follows the host, not the userinfo
        $this->assertNull(OpenRedirect::sanitize('https://jero.works@evil.com'));
    }

    public function test_url_with_double_slash_path_is_blocked(): void
    {
        // //foo is a protocol-relative URL, not a path
        $this->assertNull(OpenRedirect::sanitize('//foo'));
    }
}
