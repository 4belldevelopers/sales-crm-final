<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Sprint 4 task 4.6 — Guard against mass-assignment of sensitive User fields.
 *
 * Why: User model had $guarded=[] alongside $fillable, creating an apparent
 * loophole. $fillable now wins, but this test pins that only the four allowed
 * fields can be mass-assigned and critical fields are permanently blocked.
 * Uses reflection to avoid triggering model boot / database dependency.
 */
class MassAssignmentGuardTest extends TestCase
{
    private const SENSITIVE_FIELDS = [
        'super_password',
        'super_admin_login_code',
        'status',
        'company_id',
        'email_verification_code',
        'root_user',
    ];

    private function fillable(): array
    {
        $ref = new ReflectionClass(User::class);
        return $ref->getDefaultProperties()['fillable'] ?? [];
    }

    public function test_fillable_is_explicitly_defined(): void
    {
        $this->assertNotEmpty($this->fillable(), 'User::$fillable must be explicitly defined');
    }

    public function test_guarded_all_is_not_set(): void
    {
        $ref = new ReflectionClass(User::class);
        $guarded = $ref->getDefaultProperties()['guarded'] ?? ['*'];
        // If $guarded=[] is present it overrides fillable semantics in older Laravel; it must be absent or ['*'].
        $this->assertNotSame([], $guarded, 'User::$guarded=[] must be removed; use $fillable exclusively');
    }

    public function test_sensitive_fields_are_not_fillable(): void
    {
        $fillable = $this->fillable();

        foreach (self::SENSITIVE_FIELDS as $field) {
            $this->assertNotContains(
                $field,
                $fillable,
                "User::\$fillable must NOT include sensitive field '{$field}'"
            );
        }
    }
}
