<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SystemSettingsModel;
use App\Models\AuditLogModel;

class Settings extends BaseController
{
    protected $settingsModel;
    protected $auditModel;

    public function __construct()
    {
        $this->settingsModel = new SystemSettingsModel();
        $this->auditModel = new AuditLogModel();
        helper(['form', 'url', 'settings']);
    }

    /**
     * Settings dashboard
     */
    public function index()
    {
        // Get all settings grouped by category
        $settings = $this->settingsModel->getAllGrouped();
        $categories = $this->settingsModel->getCategories();

        $data = [
            'title' => 'Pengaturan Sistem',
            'settings' => $settings,
            'categories' => $categories,
        ];

        return view('admin/settings/index', $data);
    }

    /**
     * Update settings
     */
    public function update()
    {
        if (!$this->request->isAJAX() && !$this->request->is('post')) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        $category = $this->request->getPost('category');
        $settings = $this->request->getPost('settings');

        if (!$settings || !is_array($settings)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pengaturan tidak valid',
            ]);
        }

        // Validate and update each setting
        $errors = [];
        $updated = [];

        foreach ($settings as $key => $value) {
            $setting = $this->settingsModel->where('setting_key', $key)->first();

            if (!$setting) {
                continue;
            }

            // Apply validation rules if defined
            if (!empty($setting['validation_rules'])) {
                $validation = \Config\Services::validation();
                $validation->setRule($key, 'Setting', $setting['validation_rules']);

                if (!$validation->run([$key => $value])) {
                    $errors[$key] = $validation->getError($key);
                    continue;
                }
            }

            // Get old value for audit
            $oldValue = $setting['setting_value'];

            // Update setting
            if ($this->settingsModel->setSetting($key, $value, session()->get('user_id'))) {
                $updated[] = $key;

                // Log the change
                $this->auditModel->log(
                    'update',
                    'system_settings',
                    $setting['id'],
                    "Updated setting: {$key}",
                    ['setting_value' => $oldValue],
                    ['setting_value' => $value]
                );
            }
        }

        if (!empty($errors)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Beberapa pengaturan gagal diperbarui',
                'errors' => $errors,
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => count($updated) . ' pengaturan berhasil diperbarui',
        ]);
    }

    /**
     * Create new setting
     */
    public function create()
    {
        if (!$this->request->is('post')) {
            $categories = $this->settingsModel->getCategories();
            $data = [
                'title' => 'Tambah Pengaturan',
                'categories' => $categories,
            ];
            return view('admin/settings/create', $data);
        }

        $rules = [
            'setting_key' => 'required|max_length[100]|is_unique[system_settings.setting_key]|alpha_dash',
            'setting_value' => 'permit_empty',
            'setting_type' => 'required|in_list[string,integer,boolean,json,decimal]',
            'category' => 'required|max_length[50]',
            'description' => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'setting_key' => $this->request->getPost('setting_key'),
            'setting_value' => $this->request->getPost('setting_value'),
            'setting_type' => $this->request->getPost('setting_type'),
            'category' => $this->request->getPost('category'),
            'description' => $this->request->getPost('description'),
            'is_public' => $this->request->getPost('is_public') ? 1 : 0,
            'is_encrypted' => $this->request->getPost('is_encrypted') ? 1 : 0,
            'validation_rules' => $this->request->getPost('validation_rules'),
            'updated_by' => session()->get('user_id'),
        ];

        if ($this->settingsModel->createSetting($data)) {
            // Log the creation
            $this->auditModel->log(
                'create',
                'system_settings',
                $this->settingsModel->getInsertID(),
                "Created new setting: {$data['setting_key']}"
            );

            return redirect()->to(base_url('admin/settings'))
                ->with('success', 'Pengaturan berhasil ditambahkan');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal menambahkan pengaturan');
    }

    /**
     * Delete setting
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'ID tidak valid');
        }

        $setting = $this->settingsModel->find($id);

        if (!$setting) {
            return redirect()->back()->with('error', 'Pengaturan tidak ditemukan');
        }

        // Prevent deletion of critical settings
        $protectedKeys = [
            'app_name',
            'default_monthly_dues',
            'max_login_attempts',
            'session_timeout_minutes',
        ];

        if (in_array($setting['setting_key'], $protectedKeys)) {
            return redirect()->back()->with('error', 'Pengaturan ini tidak dapat dihapus');
        }

        if ($this->settingsModel->delete($id)) {
            // Log the deletion
            $this->auditModel->log(
                'delete',
                'system_settings',
                $id,
                "Deleted setting: {$setting['setting_key']}",
                $setting
            );

            return redirect()->to(base_url('admin/settings'))
                ->with('success', 'Pengaturan berhasil dihapus');
        }

        return redirect()->back()->with('error', 'Gagal menghapus pengaturan');
    }

    /**
     * Reset setting to default
     */
    public function reset($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID tidak valid',
            ]);
        }

        $setting = $this->settingsModel->find($id);

        if (!$setting) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pengaturan tidak ditemukan',
            ]);
        }

        // Default values map (same as migration)
        $defaults = [
            'maintenance_mode' => '0',
            'member_registration_open' => '1',
            'default_monthly_dues' => '50000',
            'arrears_penalty_rate' => '0',
            'max_arrears_months' => '6',
            'payment_verification_days' => '3',
            'max_login_attempts' => '5',
            'lockout_duration_minutes' => '30',
            'session_timeout_minutes' => '120',
            'password_reset_token_expiry' => '60',
            'audit_log_retention_days' => '365',
            'pagination_per_page' => '20',
            'email_notifications_enabled' => '1',
            'notify_member_approval' => '1',
            'notify_payment_verified' => '1',
            'notify_arrears_warning' => '1',
        ];

        $key = $setting['setting_key'];
        $defaultValue = $defaults[$key] ?? null;

        if ($defaultValue === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada nilai default untuk pengaturan ini',
            ]);
        }

        $oldValue = $setting['setting_value'];

        if ($this->settingsModel->setSetting($key, $defaultValue, session()->get('user_id'))) {
            // Log the reset
            $this->auditModel->log(
                'update',
                'system_settings',
                $id,
                "Reset setting to default: {$key}",
                ['setting_value' => $oldValue],
                ['setting_value' => $defaultValue]
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengaturan berhasil direset ke nilai default',
                'value' => $defaultValue,
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mereset pengaturan',
        ]);
    }
}
