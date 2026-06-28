<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FaviconService
{
    public function fetch($url)
    {
        try {
            $domain = parse_url($url, PHP_URL_HOST);
            $base = 'https://' . $domain;

            $html = Http::timeout(8)->get($base)->body();

            // 1. Try to find <link rel="icon">
            preg_match('/<link[^>]+rel=["\']?(icon|shortcut icon)["\']?[^>]+href=["\']([^"\']+)["\']/i', $html, $m);

            if(!empty($m[2])){
                $icon = $this->makeAbsolute($m[2], $base);
            }else{
                // 2. Fallback to /favicon.ico
                $icon = $base.'/favicon.ico';
            }

            $image = Http::timeout(8)->get($icon);

            if(!$image->successful()) return null;

            $ext = pathinfo($icon, PATHINFO_EXTENSION) ?: 'png';

            $file = "favicons/{$domain}.{$ext}";

            Storage::disk('public')->put($file, $image->body());

            return Storage::url($file);

        } catch (\Exception $e) {
            return null;
        }
    }

    private function makeAbsolute($path, $base)
    {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return rtrim($base,'/').'/'.ltrim($path,'/');
    }
}
