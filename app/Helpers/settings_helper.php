<?php

use App\Models\SystemSettingsModel;

if (!function_exists('setting')) {
    /**
     * Get system setting value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        static $settings = [];

        // Cache settings to avoid multiple database queries
        if (!isset($settings[$key])) {
            $model = new SystemSettingsModel();
            $settings[$key] = $model->get($key, $default);
        }

        return $settings[$key];
    }
}

if (!function_exists('update_setting')) {
    /**
     * Update system setting value
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $userId
     * @return bool
     */
    function update_setting(string $key, $value, ?int $userId = null): bool
    {
        $model = new SystemSettingsModel();
        return $model->setSetting($key, $value, $userId);
    }
}

if (!function_exists('settings_by_category')) {
    /**
     * Get all settings by category
     *
     * @param string $category
     * @return array
     */
    function settings_by_category(string $category): array
    {
        $model = new SystemSettingsModel();
        return $model->getByCategory($category);
    }
}

if (!function_exists('public_settings')) {
    /**
     * Get all public settings
     *
     * @return array
     */
    function public_settings(): array
    {
        static $publicSettings = null;

        if ($publicSettings === null) {
            $model = new SystemSettingsModel();
            $publicSettings = $model->getPublicSettings();
        }

        return $publicSettings;
    }
}

if (!function_exists('is_maintenance_mode')) {
    /**
     * Check if application is in maintenance mode
     *
     * @return bool
     */
    function is_maintenance_mode(): bool
    {
        return (bool) setting('maintenance_mode', false);
    }
}

if (!function_exists('app_name')) {
    /**
     * Get application name
     *
     * @return string
     */
    function app_name(): string
    {
        return setting('app_name', 'Serikat Pekerja Kampus');
    }
}
