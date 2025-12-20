<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DuesRateModel;
use App\Models\RegionCodeModel;
use App\Models\AuditLogModel;

class DuesRateController extends BaseController
{
    protected $duesRateModel;
    protected $regionModel;
    protected $auditModel;

    public function __construct()
    {
        $this->duesRateModel = new DuesRateModel();
        $this->regionModel = new RegionCodeModel();
        $this->auditModel = new AuditLogModel();
        helper(['form', 'url', 'rbac']);
    }

    /**
     * Dues rate management dashboard
     */
    public function index()
    {
        $rates = $this->duesRateModel->getRatesWithStats();

        // Statistics
        $stats = [
            'total_rates' => count($rates),
            'active_rates' => count(array_filter($rates, fn($r) => $r['is_active'] == 1)),
            'monthly_rates' => count(array_filter($rates, fn($r) => $r['rate_type'] === 'monthly')),
            'yearly_rates' => count(array_filter($rates, fn($r) => $r['rate_type'] === 'yearly')),
        ];

        $data = [
            'title' => 'Manajemen Tarif Iuran',
            'rates' => $rates,
            'stats' => $stats,
        ];

        return view('admin/dues_rates/index', $data);
    }

    /**
     * Create new rate
     */
    public function create()
    {
        if ($this->request->is('post')) {
            $postData = $this->request->getPost();

            // Convert empty strings to null for optional fields
            $postData['member_category'] = $postData['member_category'] ?: null;
            $postData['region_code'] = $postData['region_code'] ?: null;
            $postData['effective_to'] = $postData['effective_to'] ?: null;
            $postData['description'] = $postData['description'] ?: null;
            $postData['created_by'] = session()->get('user_id');

            if ($this->duesRateModel->insert($postData)) {
                $rateId = $this->duesRateModel->getInsertID();

                // Log creation
                $this->duesRateModel->logChange(
                    $rateId,
                    'created',
                    null,
                    $postData['amount'],
                    'Rate created'
                );

                // Audit log
                $this->auditModel->log(
                    'create',
                    'dues_rates',
                    $rateId,
                    "Created new dues rate: {$postData['rate_name']}",
                    null,
                    $postData
                );

                return redirect()->to(base_url('admin/dues-rates'))
                    ->with('success', 'Tarif iuran berhasil ditambahkan');
            }

            return redirect()->back()
                ->withInput()
                ->with('errors', $this->duesRateModel->errors());
        }

        $regions = $this->regionModel->getActiveRegions();

        $data = [
            'title' => 'Tambah Tarif Iuran',
            'regions' => $regions,
        ];

        return view('admin/dues_rates/create', $data);
    }

    /**
     * Edit rate
     */
    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'Rate ID tidak valid');
        }

        $rate = $this->duesRateModel->find($id);

        if (!$rate) {
            return redirect()->back()->with('error', 'Tarif tidak ditemukan');
        }

        if ($this->request->is('post')) {
            $postData = $this->request->getPost();

            // Convert empty strings to null
            $postData['member_category'] = $postData['member_category'] ?: null;
            $postData['region_code'] = $postData['region_code'] ?: null;
            $postData['effective_to'] = $postData['effective_to'] ?: null;
            $postData['description'] = $postData['description'] ?: null;
            $postData['updated_by'] = session()->get('user_id');

            // Track amount change
            $oldAmount = $rate['amount'];
            $newAmount = $postData['amount'];

            if ($this->duesRateModel->update($id, $postData)) {
                // Log if amount changed
                if ($oldAmount != $newAmount) {
                    $this->duesRateModel->logChange(
                        $id,
                        'updated',
                        $oldAmount,
                        $newAmount,
                        'Rate amount updated'
                    );
                }

                // Audit log
                $this->auditModel->log(
                    'update',
                    'dues_rates',
                    $id,
                    "Updated dues rate: {$postData['rate_name']}",
                    $rate,
                    $postData
                );

                return redirect()->to(base_url('admin/dues-rates'))
                    ->with('success', 'Tarif iuran berhasil diperbarui');
            }

            return redirect()->back()
                ->withInput()
                ->with('errors', $this->duesRateModel->errors());
        }

        $regions = $this->regionModel->getActiveRegions();

        $data = [
            'title' => 'Edit Tarif Iuran',
            'rate' => $rate,
            'regions' => $regions,
        ];

        return view('admin/dues_rates/edit', $data);
    }

    /**
     * View rate details and history
     */
    public function view($id = null)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'Rate ID tidak valid');
        }

        $rate = $this->duesRateModel->find($id);

        if (!$rate) {
            return redirect()->back()->with('error', 'Tarif tidak ditemukan');
        }

        $history = $this->duesRateModel->getRateHistory($id);

        $data = [
            'title' => 'Detail Tarif Iuran',
            'rate' => $rate,
            'history' => $history,
        ];

        return view('admin/dues_rates/view', $data);
    }

    /**
     * Toggle rate status (AJAX)
     */
    public function toggleStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $rateId = $this->request->getPost('rate_id');

        if (!$rateId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Rate ID tidak valid']);
        }

        if ($this->duesRateModel->toggleStatus($rateId)) {
            $rate = $this->duesRateModel->find($rateId);

            // Audit log
            $this->auditModel->log(
                'update',
                'dues_rates',
                $rateId,
                "Toggled status for rate: {$rate['rate_name']}"
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status tarif berhasil diubah',
                'new_status' => $rate['is_active'],
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengubah status']);
    }

    /**
     * Delete rate
     */
    public function delete($id = null)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'Rate ID tidak valid');
        }

        $rate = $this->duesRateModel->find($id);

        if (!$rate) {
            return redirect()->back()->with('error', 'Tarif tidak ditemukan');
        }

        // Check if rate is in use (implement based on your payment logic)
        // For now, allow deletion

        if ($this->duesRateModel->delete($id)) {
            // Audit log
            $this->auditModel->log(
                'delete',
                'dues_rates',
                $id,
                "Deleted dues rate: {$rate['rate_name']}",
                $rate,
                null
            );

            return redirect()->to(base_url('admin/dues-rates'))
                ->with('success', 'Tarif iuran berhasil dihapus');
        }

        return redirect()->back()->with('error', 'Gagal menghapus tarif');
    }

    /**
     * Duplicate rate
     */
    public function duplicate($id = null)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'Rate ID tidak valid');
        }

        $newId = $this->duesRateModel->duplicateRate($id);

        if ($newId) {
            return redirect()->to(base_url('admin/dues-rates/edit/' . $newId))
                ->with('success', 'Tarif berhasil diduplikasi. Silakan edit sesuai kebutuhan.');
        }

        return redirect()->back()->with('error', 'Gagal menduplikasi tarif');
    }
}
