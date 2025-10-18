<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;

class DynamicCssController extends Controller
{
    /**
     * Generate dynamic CSS based on current settings
     */
    public function generate(Request $request)
    {
        $theme = AppSetting::getValue('theme_color', 'theme-blue');

        // Define theme colors
        $themeColors = [
            'theme-blue' => '#0d6efd',
            'theme-red' => '#dc3545',
            'theme-green' => '#198754',
            'theme-purple' => '#6f42c1',
            'theme-orange' => '#fd7e14',
            'theme-pink' => '#e91e63',
            'theme-teal' => '#20c997',
            'theme-dark' => '#212529',
        ];

        $primaryColor = $themeColors[$theme] ?? '#0d6efd';

        // Convert hex to RGB
        $rgb = $this->hexToRgb($primaryColor);

        $css = "
        :root {
            --bs-primary: {$primaryColor};
            --bs-primary-rgb: {$rgb['r']}, {$rgb['g']}, {$rgb['b']};
        }

        /* Theme-specific overrides */
        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        .btn-primary:hover {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
            opacity: 0.9;
        }

        .bg-primary {
            background-color: var(--bs-primary) !important;
        }

        .text-primary {
            color: var(--bs-primary) !important;
        }

        .border-primary {
            border-color: var(--bs-primary) !important;
        }

        .nav-pills .nav-link.active {
            background-color: var(--bs-primary) !important;
        }

        .sidebar {
            background-color: var(--bs-primary) !important;
        }

        .page-title-box {
            border-left-color: var(--bs-primary) !important;
        }
        ";

        return response($css)
            ->header('Content-Type', 'text/css')
            ->header('Cache-Control', 'no-cache, must-revalidate');
    }

    /**
     * Convert hex color to RGB
     */
    private function hexToRgb($hex)
    {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return ['r' => $r, 'g' => $g, 'b' => $b];
    }
}
