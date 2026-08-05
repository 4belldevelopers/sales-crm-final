<?php

namespace Tests\Unit;

use App\Helper\GeneralMethod;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

/**
 * Unit tests for GeneralMethod::value_decrypt().
 *
 * Covers the four scenarios described in the V2 Edit Candidates Step 5 spec:
 *   1. Valid encrypted payload returns the original value.
 *   2. Raw numeric input returns an integer directly (no DecryptException).
 *   3. Invalid / corrupted base64 returns null instead of throwing.
 *   4. URL space-mutated base64 ('+' replaced by ' ') decrypts successfully.
 */
class GeneralMethodDecryptTest extends TestCase
{
    // -----------------------------------------------------------------------
    // Scenario 1 — valid encrypted payload
    // -----------------------------------------------------------------------

    public function test_valid_encrypted_string_returns_original_value(): void
    {
        $original = 'hello-world';
        $encrypted = encrypt($original);

        $result = GeneralMethod::value_decrypt($encrypted);

        $this->assertSame($original, $result);
    }

    public function test_valid_encrypted_integer_returns_original_integer(): void
    {
        $original = 42;
        $encrypted = encrypt($original);

        $result = GeneralMethod::value_decrypt($encrypted);

        $this->assertEquals($original, $result);
    }

    // -----------------------------------------------------------------------
    // Scenario 2 — raw numeric input passes through as integer
    // -----------------------------------------------------------------------

    public function test_raw_integer_string_returns_integer_without_crashing(): void
    {
        $result = GeneralMethod::value_decrypt('123');

        $this->assertSame(123, $result);
    }

    public function test_raw_zero_returns_integer_zero(): void
    {
        $result = GeneralMethod::value_decrypt('0');

        $this->assertSame(0, $result);
    }

    public function test_raw_large_integer_returns_integer(): void
    {
        $result = GeneralMethod::value_decrypt('999999');

        $this->assertSame(999999, $result);
    }

    // -----------------------------------------------------------------------
    // Scenario 3 — invalid / corrupted base64 returns null (no exception)
    // -----------------------------------------------------------------------

    public function test_invalid_base64_returns_null_not_exception(): void
    {
        $result = GeneralMethod::value_decrypt('not-a-valid-encrypted-string');

        $this->assertNull($result);
    }

    public function test_corrupted_encrypted_payload_returns_null(): void
    {
        $encrypted = encrypt('test');
        $corrupted = substr($encrypted, 0, -5) . 'XXXXX'; // corrupt the tail

        $result = GeneralMethod::value_decrypt($corrupted);

        $this->assertNull($result);
    }

    public function test_random_base64_like_string_returns_null(): void
    {
        $result = GeneralMethod::value_decrypt(base64_encode('random garbage'));

        $this->assertNull($result);
    }

    // -----------------------------------------------------------------------
    // Scenario 4 — URL space-mutation: '+' replaced with ' ' in base64 payload
    // -----------------------------------------------------------------------

    public function test_url_space_mutated_payload_decrypts_successfully(): void
    {
        $original = 'space-mutation-test';
        $encrypted = encrypt($original);

        // Simulate browser/proxy URL mutation: base64 '+' → ' '
        $mutated = str_replace('+', ' ', $encrypted);

        // Only run this assertion when the mutation actually changed something,
        // because not every encrypted value contains '+'.
        if ($mutated !== $encrypted) {
            $result = GeneralMethod::value_decrypt($mutated);
            $this->assertSame($original, $result);
        } else {
            // Generate a string that produces a '+' in the base64 output
            $found = false;
            for ($i = 0; $i < 100; $i++) {
                $candidate = 'payload-' . $i;
                $enc       = encrypt($candidate);
                $mut       = str_replace('+', ' ', $enc);
                if ($mut !== $enc) {
                    $result = GeneralMethod::value_decrypt($mut);
                    $this->assertSame($candidate, $result);
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $this->markTestSkipped('Could not produce a + character in encrypted output within 100 attempts.');
            }
        }
    }

    // -----------------------------------------------------------------------
    // Edge cases
    // -----------------------------------------------------------------------

    public function test_null_input_returns_null(): void
    {
        $result = GeneralMethod::value_decrypt(null);

        $this->assertNull($result);
    }

    public function test_empty_string_returns_null(): void
    {
        $result = GeneralMethod::value_decrypt('');

        $this->assertNull($result);
    }
}
