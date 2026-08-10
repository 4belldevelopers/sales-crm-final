<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

/**
 * Serves the login-form captcha PNG.
 *
 * The GD image is rendered once per session and cached in Redis for 2 minutes.
 * Subsequent requests within the same session receive the cached PNG without a GD render.
 * The captcha code stays in the PHP session; only the PNG bytes are cached.
 *
 * V2_login Phase 1.1 (docs/V2_login/IMPLEMENTATION_PLAN.md §1.1).
 */
class CaptchaController
{
    public function generate(): Response
    {
        $sessionId = session()->getId();
        $cacheKey  = "captcha:png:{$sessionId}";

        // Serve cached PNG if still valid for this session.
        if (Cache::has($cacheKey)) {
            return response(Cache::get($cacheKey))
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'private, max-age=120');
        }

        // Generate a new 4-digit numeric code and store it in the session.
        $captcha = random_int(1000, 9999);
        Session::put('captcha_code', $captcha);
        Session::put('captcha_expires', now()->addMinutes(2));

        // Render PNG.
        $image     = imagecreate(160, 50);
        $textColor = imagecolorallocate($image, 30, 30, 30);
        imagecolorallocate($image, 255, 255, 255); // background

        imagettftext(
            $image, 26, rand(-5, 5), 35, 38,
            $textColor,
            public_path('fonts/DejaVuSerif-Italic.ttf'),
            (string) $captcha
        );

        // Noise
        for ($i = 0; $i < 80; $i++) {
            imagesetpixel($image, rand(0, 160), rand(0, 50), $textColor);
        }

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        // Cache PNG bytes — TTL matches session captcha_expires (2 min).
        Cache::put($cacheKey, $imageData, now()->addMinutes(2));

        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'private, max-age=120');
    }
}
