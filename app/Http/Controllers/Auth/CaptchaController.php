<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

/**
 * Serves the login-form captcha PNG.
 *
 * The GD image is rendered per session and cached in Redis for 2 minutes.
 * Subsequent requests within the same session receive the cached PNG without a GD render
 * unless explicitly refreshed with query parameter ?t=...
 */
class CaptchaController
{
    public function generate(Request $request): Response
    {
        $sessionId = session()->getId();
        $cacheKey  = "captcha:png:{$sessionId}";
        $isRefresh = $request->has('t') || $request->has('refresh');

        // Serve cached PNG if still valid for this session AND not explicitly refreshing
        if (!$isRefresh && Cache::has($cacheKey)) {
            return response(Cache::get($cacheKey))
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache');
        }

        // Generate a new 4-digit numeric code and store it in the session.
        $captcha = random_int(1000, 9999);
        Session::put('captcha_code', $captcha);
        Session::put('captcha_expires', now()->addMinutes(2));

        // Render PNG with truecolor canvas and clean white background
        $width  = 160;
        $height = 50;
        $image  = imagecreatetruecolor($width, $height);

        $bgColor   = imagecolorallocate($image, 255, 255, 255); // White background
        $textColor = imagecolorallocate($image, 15, 23, 58);    // Dark navy text (#0F173A)
        $dotColor  = imagecolorallocate($image, 148, 163, 184); // Slate noise (#94A3B8)

        imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

        $fontFile = public_path('fonts/DejaVuSerif-Italic.ttf');
        if (file_exists($fontFile)) {
            imagettftext(
                $image, 24, rand(-4, 4), 30, 36,
                $textColor,
                $fontFile,
                (string) $captcha
            );
        } else {
            imagestring($image, 5, 55, 16, (string) $captcha, $textColor);
        }

        // Noise dots
        for ($i = 0; $i < 60; $i++) {
            imagesetpixel($image, rand(0, $width), rand(0, $height), $dotColor);
        }

        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        // Cache PNG bytes — TTL matches session captcha_expires (2 min).
        Cache::put($cacheKey, $imageData, now()->addMinutes(2));

        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }
}
