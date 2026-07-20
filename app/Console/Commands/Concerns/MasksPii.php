<?php

namespace App\Console\Commands\Concerns;

/**
 * Shared masking for Ownership Engine operational commands whose output may reach a shared
 * terminal or log file (Phase 5 "Risks discovered": PII exposure risk in a naive
 * implementation). Not business logic — purely a display concern.
 */
trait MasksPii
{
    protected function maskEmail(?string $email): string
    {
        if (!$email || !str_contains($email, '@')) {
            return '(none)';
        }

        [$local, $domain] = explode('@', $email, 2);

        return substr($local, 0, 2).'***@'.$domain;
    }

    protected function maskPhone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if (strlen($digits) < 4) {
            return '(none)';
        }

        return str_repeat('*', max(0, strlen($digits) - 4)).substr($digits, -4);
    }
}
