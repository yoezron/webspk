<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemSettingsModel extends Model
{
    protected $table = 'system_settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'setting_key',
        'setting_value',
        'setting_type',
        'category',
        'description',
        'is_public',
        'is_encrypted',
        'validation_rules',
        'updated_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'setting_key' => 'required|max_length[100]|is_unique[system_settings.setting_key,id,{id}]',
        'setting_type' => 'required|in_list[string,integer,boolean,json,decimal]',
        'category' => 'required|max_length[50]',
    ];

    protected $validationMessages = [
        'setting_key' => [
            'required' => 'Setting key harus diisi',
            'is_unique' => 'Setting key sudah ada',
        ],
    ];

    /**
     * Get setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return $this->castValue($setting['setting_value'], $setting['setting_type']);
    }

    /**
     * Set setting value
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $userId
     * @return bool
     */
    public function set(string $key, $value, ?int $userId = null): bool
    {
        $setting = $this->where('setting_key', $key)->first();

        if (!$setting) {
            return false;
        }

        $data = [
            'setting_value' => $this->prepareValue($value, $setting['setting_type']),
            'updated_by' => $userId ?? session()->get('user_id'),
        ];

        return $this->update($setting['id'], $data);
    }

    /**
     * Get all settings by category
     *
     * @param string $category
     * @return array
     */
    public function getByCategory(string $category): array
    {
        $settings = $this->where('category', $category)->findAll();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = [
                'value' => $this->castValue($setting['setting_value'], $setting['setting_type']),
                'type' => $setting['setting_type'],
                'description' => $setting['description'],
                'validation_rules' => $setting['validation_rules'],
            ];
        }

        return $result;
    }

    /**
     * Get all public settings
     *
     * @return array
     */
    public function getPublicSettings(): array
    {
        $settings = $this->where('is_public', 1)->findAll();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $this->castValue(
                $setting['setting_value'],
                $setting['setting_type']
            );
        }

        return $result;
    }

    /**
     * Get all settings grouped by category
     *
     * @return array
     */
    public function getAllGrouped(): array
    {
        $settings = $this->orderBy('category, setting_key')->findAll();
        $result = [];

        foreach ($settings as $setting) {
            if (!isset($result[$setting['category']])) {
                $result[$setting['category']] = [];
            }

            $result[$setting['category']][$setting['setting_key']] = [
                'id' => $setting['id'],
                'value' => $this->castValue($setting['setting_value'], $setting['setting_type']),
                'raw_value' => $setting['setting_value'],
                'type' => $setting['setting_type'],
                'description' => $setting['description'],
                'is_public' => $setting['is_public'],
                'is_encrypted' => $setting['is_encrypted'],
                'validation_rules' => $setting['validation_rules'],
            ];
        }

        return $result;
    }

    /**
     * Bulk update settings
     *
     * @param array $settings
     * @param int|null $userId
     * @return bool
     */
    public function bulkUpdate(array $settings, ?int $userId = null): bool
    {
        $userId = $userId ?? session()->get('user_id');

        foreach ($settings as $key => $value) {
            $setting = $this->where('setting_key', $key)->first();

            if ($setting) {
                $this->update($setting['id'], [
                    'setting_value' => $this->prepareValue($value, $setting['setting_type']),
                    'updated_by' => $userId,
                ]);
            }
        }

        return true;
    }

    /**
     * Cast setting value to appropriate type
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    private function castValue($value, string $type)
    {
        if ($value === null) {
            return null;
        }

        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'decimal':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Prepare value for storage
     *
     * @param mixed $value
     * @param string $type
     * @return string
     */
    private function prepareValue($value, string $type): string
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'json':
                return json_encode($value);
            default:
                return (string) $value;
        }
    }

    /**
     * Create new setting
     *
     * @param array $data
     * @return int|false
     */
    public function createSetting(array $data)
    {
        $data['updated_by'] = $data['updated_by'] ?? session()->get('user_id');
        return $this->insert($data);
    }

    /**
     * Delete setting by key
     *
     * @param string $key
     * @return bool
     */
    public function deleteSetting(string $key): bool
    {
        $setting = $this->where('setting_key', $key)->first();
        if ($setting) {
            return $this->delete($setting['id']);
        }
        return false;
    }

    /**
     * Get category list
     *
     * @return array
     */
    public function getCategories(): array
    {
        return [
            'general' => 'Umum',
            'dues' => 'Iuran',
            'email' => 'Email',
            'notification' => 'Notifikasi',
            'security' => 'Keamanan',
            'system' => 'Sistem',
        ];
    }
}
