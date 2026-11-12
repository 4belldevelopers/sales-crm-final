<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LanguageSetting extends Model
{
    protected $fillable = [
        'language_code',
        'language_name',
        'status',
    ];

    /**
     * Standard ISO-639-1 code to flag emoji mapping.
     */
    public const FLAGS = [
        'en' => '🇺🇸',
        'ar' => '🇸🇦',
        'de' => '🇩🇪',
        'es' => '🇪🇸',
        'et' => '🇪🇪',
        'fa' => '🇮🇷',
        'fr' => '🇫🇷',
        'gr' => '🇬🇷',
        'el' => '🇬🇷',
        'it' => '🇮🇹',
        'nl' => '🇳🇱',
        'pl' => '🇵🇱',
        'pt' => '🇵🇹',
        'pt-br' => '🇧🇷',
        'ro' => '🇷🇴',
        'ru' => '🇷🇺',
        'tr' => '🇹🇷',
        'zh-cn' => '🇨🇳',
        'zh-tw' => '🇹🇼',
        'hi' => '🇮🇳',
        'ja' => '🇯🇵',
        'ko' => '🇰🇷',
        'uk' => '🇺🇦',
        'vi' => '🇻🇳',
        'id' => '🇮🇩',
        'sv' => '🇸🇪',
        'no' => '🇳🇴',
        'da' => '🇩🇰',
        'fi' => '🇫🇮',
        'cs' => '🇨🇿',
        'hu' => '🇭🇺',
        'th' => '🇹🇭',
        'he' => '🇮🇱',
    ];

    /**
     * Get flag emoji for this language.
     */
    public function getFlagEmojiAttribute(): string
    {
        $code = strtolower((string) $this->language_code);

        return self::FLAGS[$code] ?? '🌐';
    }

    /**
     * Invalidate all tenant and global language cache keys synchronously.
     * Complies with Non-Negotiable Rule #6 (Multi-Tenant Atomic Cache Invalidation).
     */
    public static function flushCaches(): void
    {
        \App\Services\SuperAdmin\SuperAdminLanguageCacheManager::flushCaches();
    }


    public function frontCmsHeader()
    {
        return $this->hasOne(FrontCmsHeader::class, 'language_settings_id');
    }
}
