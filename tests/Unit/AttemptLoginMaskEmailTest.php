<?php

namespace Tests\Unit;

use App\Actions\Auth\AttemptLoginAction;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * V2_login Phase 6.1 — AttemptLoginAction::maskEmail() correctness.
 *
 * maskEmail() is private; accessed via reflection. Tests guard that PII is
 * redacted in login_attempts log entries before they hit persistent storage.
 */
class AttemptLoginMaskEmailTest extends TestCase
{
    private function mask(string $email): string
    {
        $method = new ReflectionMethod(AttemptLoginAction::class, 'maskEmail');
        $method->setAccessible(true);
        return $method->invoke(new AttemptLoginAction(), $email);
    }

    public function test_standard_email_is_masked(): void
    {
        $this->assertSame('j***@example.com', $this->mask('john@example.com'));
    }

    public function test_short_local_part_is_masked(): void
    {
        $this->assertSame('a***@x.io', $this->mask('a@x.io'));
    }

    public function test_email_without_at_sign_is_handled_safely(): void
    {
        // Malformed input: no @, so $parts[1] is empty string.
        $result = $this->mask('notanemail');
        $this->assertStringStartsWith('n***@', $result);
    }

    public function test_empty_string_is_handled_safely(): void
    {
        $result = $this->mask('');
        $this->assertStringContainsString('***@', $result);
    }

    public function test_domain_part_is_preserved_verbatim(): void
    {
        $result = $this->mask('recruiter@hire.jero.works');
        $this->assertStringEndsWith('@hire.jero.works', $result);
    }

    public function test_only_first_char_of_local_part_is_visible(): void
    {
        $result = $this->mask('verylongemail@domain.com');
        // Should be "v***@domain.com"
        $this->assertSame('v***@domain.com', $result);
    }
}
