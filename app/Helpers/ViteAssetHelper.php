<?php

namespace App\Helpers;

class ViteAssetHelper
{
    public static function assetPath($path)
    {
        // Check if we're in production or local environment
        if (app()->environment('local')) {
            // Use Vite for local development
            return asset($path);
        }

        // Try to load the manifest file
        $manifestPath = public_path('build/manifest.json');
        
        if (!file_exists($manifestPath)) {
            // Fallback to direct asset path if manifest doesn't exist
            return asset($path);
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        $key = ltrim($path, '/');

        // Check if the asset is in the manifest
        if (isset($manifest[$key])) {
            return asset('build/' . $manifest[$key]['file']);
        }

        // Fallback to direct path
        return asset($path);
    }
}
