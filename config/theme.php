<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Theme
    |--------------------------------------------------------------------------
    |
    | This value determines the theme class that will be applied to the entire
    | application. You can change this value in your .env file using APP_THEME.
    |
    | Available themes:
    | - theme-blue (Default)
    | - theme-green
    | - theme-purple
    | - theme-red
    | - theme-orange
    | - theme-pink
    | - theme-teal
    | - theme-dark
    |
    */

    'default_theme' => env('APP_THEME', 'theme-blue'),

    /*
    |--------------------------------------------------------------------------
    | Available Themes
    |--------------------------------------------------------------------------
    |
    | List of all available theme classes for validation and reference.
    |
    */

    'available_themes' => [
        'theme-blue',
        'theme-green',
        'theme-purple',
        'theme-red',
        'theme-orange',
        'theme-pink',
        'theme-teal',
        'theme-dark',
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme Configuration
    |--------------------------------------------------------------------------
    |
    | Additional theme configuration options.
    |
    */

    'enable_theme_switching' => env('APP_THEME_SWITCHING', false),
    'default_theme_fallback' => 'theme-blue',
];
