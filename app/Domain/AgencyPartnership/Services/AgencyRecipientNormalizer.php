<?php

namespace App\Domain\AgencyPartnership\Services;

/**
 * AgencyRecipientNormalizer
 *
 * Authoritative single canonical normalizer for agency invitation recipients.
 * Enforces Punycode IDN conversion, lowercase normalization, and strictly preserves
 * dots and plus-addressing (+tag) per Specification F-01.
 */
class AgencyRecipientNormalizer
{
    /**
     * Normalize an email address for unique active slot calculations.
     */
    public static function normalize(string $email): string
    {
        $email = trim($email);
        if ($email === '') {
            return '';
        }

        $parts = explode('@', $email, 2);
        if (count($parts) !== 2) {
            return strtolower($email);
        }

        [$local, $domain] = $parts;

        // Punycode conversion for internationalized domain names
        if (function_exists('idn_to_ascii')) {
            $asciiDomain = idn_to_ascii($domain, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
            if ($asciiDomain !== false) {
                $domain = $asciiDomain;
            }
        }

        // Full lowercase; preserve dots and plus-addressing in local part
        return strtolower($local.'@'.$domain);
    }

    /**
     * Extract the normalized domain name from an email address.
     */
    public static function extractDomain(string $email): string
    {
        $normalized = self::normalize($email);
        $parts = explode('@', $normalized, 2);

        return $parts[1] ?? '';
    }
}
