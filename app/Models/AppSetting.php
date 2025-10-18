<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $fillable = [
        'category',
        'key',
        'label',
        'description',
        'value',
        'type',
        'options',
        'is_required',
        'is_public',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get setting value by key with caching
     */
    public static function getValue($key, $default = null)
    {
        return Cache::remember("app_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value and clear cache
     */
    public static function setValue($key, $value)
    {
        $setting = self::where('key', $key)->first();
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            self::create(['key' => $key, 'value' => $value]);
        }
        Cache::forget("app_setting_{$key}");
    }

    /**
     * Get all settings grouped by category
     */
    public static function getByCategory($category = null)
    {
        $query = self::orderBy('sort_order')->orderBy('label');

        if ($category) {
            $query->where('category', $category);
        }

        return $query->get()->groupBy('category');
    }

    /**
     * Get available categories
     */
    public static function getCategories()
    {
        return [
            'general' => 'General Settings',
            'inventory' => 'Inventory & Stock Management',
            'orders' => 'Order Management',
            'integration' => 'Integration Settings',
            'ui' => 'UI/UX Customization',
        ];
    }

    /**
     * Get setting types
     */
    public static function getTypes()
    {
        return [
            'text' => 'Text Input',
            'textarea' => 'Textarea',
            'number' => 'Number Input',
            'boolean' => 'Yes/No Toggle',
            'select' => 'Dropdown Select',
            'file' => 'File Upload',
            'email' => 'Email Input',
            'url' => 'URL Input',
        ];
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache()
    {
        $keys = self::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("app_setting_{$key}");
        }
    }
}
