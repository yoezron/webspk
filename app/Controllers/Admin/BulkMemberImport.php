<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use CodeIgniter\HTTP\ResponseInterface;

class BulkMemberImport extends BaseController
{
    protected $memberModel;
    protected $session;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->session = \Config\Services::session();
        helper(['upload', 'audit', 'app']);
    }

    /**
     * Display bulk import upload form
     */
    public function index()
    {
        $data = [
            'title' => 'Import Data Anggota',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('admin/dashboard')],
                ['title' => 'Manajemen Anggota', 'url' => base_url('admin/members')],
                ['title' => 'Import Data', 'url' => '']
            ]
        ];

        return view('admin/members/bulk_import', $data);
    }

    /**
     * Process uploaded CSV/Excel file
     */
    public function upload()
    {
        // Validate file upload
        $validationRules = [
            'import_file' => [
                'label' => 'File Import',
                'rules' => 'uploaded[import_file]|max_size[import_file,5120]|ext_in[import_file,csv,xlsx,xls]',
                'errors' => [
                    'uploaded' => 'File harus diupload',
                    'max_size' => 'Ukuran file maksimal 5MB',
                    'ext_in' => 'File harus berformat CSV atau Excel (.xlsx, .xls)'
                ]
            ]
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('import_file');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid');
        }

        // Move file to temp location
        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/temp', $newName);
        $filePath = WRITEPATH . 'uploads/temp/' . $newName;

        // Parse file based on extension
        $extension = $file->getExtension();

        try {
            if ($extension === 'csv') {
                $data = $this->parseCSV($filePath);
            } else {
                $data = $this->parseExcel($filePath);
            }

            // Validate imported data
            $validationResults = $this->validateImportData($data);

            // Store in session for preview
            $this->session->set('import_data', [
                'file_path' => $filePath,
                'file_name' => $file->getClientName(),
                'data' => $data,
                'validation' => $validationResults,
                'uploaded_at' => date('Y-m-d H:i:s')
            ]);

            // Audit log
            audit_log_member_action(
                'bulk_import_preview',
                null,
                session()->get('user_id'),
                session()->get('user_role'),
                null,
                ['file_name' => $file->getClientName(), 'total_rows' => count($data)]
            );

            return redirect()->to(base_url('admin/members/bulk-import/preview'));

        } catch (\Exception $e) {
            // Delete temp file
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            return redirect()->back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    /**
     * Preview import data before processing
     */
    public function preview()
    {
        $importData = $this->session->get('import_data');

        if (!$importData) {
            return redirect()->to(base_url('admin/members/bulk-import'))
                ->with('error', 'Tidak ada data untuk di-preview. Silakan upload file terlebih dahulu.');
        }

        $data = [
            'title' => 'Preview Import Data',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('admin/dashboard')],
                ['title' => 'Manajemen Anggota', 'url' => base_url('admin/members')],
                ['title' => 'Import Data', 'url' => base_url('admin/members/bulk-import')],
                ['title' => 'Preview', 'url' => '']
            ],
            'import_data' => $importData
        ];

        return view('admin/members/bulk_import_preview', $data);
    }

    /**
     * Execute the import process
     */
    public function process()
    {
        $importData = $this->session->get('import_data');

        if (!$importData) {
            return redirect()->to(base_url('admin/members/bulk-import'))
                ->with('error', 'Tidak ada data untuk diproses.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        try {
            foreach ($importData['data'] as $index => $row) {
                $rowNumber = $index + 1;

                // Skip if validation failed
                if (isset($importData['validation']['errors'][$index])) {
                    $errorCount++;
                    $errors[] = "Baris {$rowNumber}: " . implode(', ', $importData['validation']['errors'][$index]);
                    continue;
                }

                // Check if email already exists
                if ($this->memberModel->findByEmail($row['email'])) {
                    $errorCount++;
                    $errors[] = "Baris {$rowNumber}: Email {$row['email']} sudah terdaftar";
                    continue;
                }

                // Check data completeness
                $isComplete = $this->checkDataCompleteness($row);

                // Prepare member data
                $memberData = [
                    'email' => $row['email'],
                    'password' => $row['password'] ?? bin2hex(random_bytes(8)), // Generate random password if not provided
                    'full_name' => $row['full_name'],
                    'phone_number' => $row['phone_number'] ?? null,
                    'role' => 'member', // Default role for imported members
                    'membership_status' => $isComplete ? 'active' : 'inactive',
                    'account_status' => $isComplete ? 'active' : 'pending',
                    'onboarding_state' => $isComplete ? 'approved' : 'profile_completed',
                    'is_migrated' => 1,
                    'migrated_at' => date('Y-m-d H:i:s'),

                    // Personal data
                    'gender' => $row['gender'] ?? null,
                    'birth_place' => $row['birth_place'] ?? null,
                    'birth_date' => $row['birth_date'] ?? null,
                    'identity_number' => $row['identity_number'] ?? null,
                    'address' => $row['address'] ?? null,
                    'province' => $row['province'] ?? null,
                    'city' => $row['city'] ?? null,
                    'district' => $row['district'] ?? null,
                    'postal_code' => $row['postal_code'] ?? null,

                    // Work data
                    'university_name' => $row['university_name'] ?? null,
                    'campus_location' => $row['campus_location'] ?? null,
                    'faculty' => $row['faculty'] ?? null,
                    'department' => $row['department'] ?? null,
                    'employee_id_number' => $row['employee_id_number'] ?? null,
                    'employment_status' => $row['employment_status'] ?? null,
                    'work_start_date' => $row['work_start_date'] ?? null,

                    // Financial data
                    'gross_salary' => $row['gross_salary'] ?? null,
                    'bank_name' => $row['bank_name'] ?? null,
                    'bank_account_number' => $row['bank_account_number'] ?? null,
                    'bank_account_name' => $row['bank_account_name'] ?? null,

                    // Notes
                    'notes' => $row['notes'] ?? 'Diimport dari data lama',
                ];

                // Insert member
                if ($this->memberModel->insert($memberData)) {
                    $successCount++;

                    // Audit log
                    audit_log_member_action(
                        'bulk_import_create',
                        $this->memberModel->getInsertID(),
                        session()->get('user_id'),
                        session()->get('user_role'),
                        null,
                        [
                            'email' => $row['email'],
                            'is_complete' => $isComplete,
                            'status' => $isComplete ? 'active' : 'inactive'
                        ]
                    );
                } else {
                    $errorCount++;
                    $errors[] = "Baris {$rowNumber}: Gagal menyimpan data";
                }
            }

            $db->transComplete();

            // Delete temp file
            if (isset($importData['file_path']) && file_exists($importData['file_path'])) {
                unlink($importData['file_path']);
            }

            // Clear session data
            $this->session->remove('import_data');

            if ($db->transStatus() === false) {
                return redirect()->to(base_url('admin/members/bulk-import'))
                    ->with('error', 'Terjadi kesalahan saat memproses import.');
            }

            // Prepare result message
            $message = "Import selesai. Berhasil: {$successCount}, Gagal: {$errorCount}";

            $this->session->setFlashdata('import_result', [
                'success' => $successCount,
                'error' => $errorCount,
                'errors' => $errors
            ]);

            return redirect()->to(base_url('admin/members/bulk-import/result'))
                ->with('success', $message);

        } catch (\Exception $e) {
            $db->transRollback();

            return redirect()->to(base_url('admin/members/bulk-import'))
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display import result
     */
    public function result()
    {
        $result = $this->session->getFlashdata('import_result');

        if (!$result) {
            return redirect()->to(base_url('admin/members/bulk-import'));
        }

        $data = [
            'title' => 'Hasil Import',
            'breadcrumbs' => [
                ['title' => 'Dashboard', 'url' => base_url('admin/dashboard')],
                ['title' => 'Manajemen Anggota', 'url' => base_url('admin/members')],
                ['title' => 'Import Data', 'url' => base_url('admin/members/bulk-import')],
                ['title' => 'Hasil', 'url' => '']
            ],
            'result' => $result
        ];

        return view('admin/members/bulk_import_result', $data);
    }

    /**
     * Download sample CSV template
     */
    public function downloadTemplate()
    {
        $csvHeader = [
            'email',
            'password',
            'full_name',
            'phone_number',
            'gender',
            'birth_place',
            'birth_date',
            'identity_number',
            'address',
            'province',
            'city',
            'district',
            'postal_code',
            'university_name',
            'campus_location',
            'faculty',
            'department',
            'employee_id_number',
            'employment_status',
            'work_start_date',
            'gross_salary',
            'bank_name',
            'bank_account_number',
            'bank_account_name',
            'notes'
        ];

        $sampleData = [
            [
                'email' => 'contoh@email.com',
                'password' => 'password123',
                'full_name' => 'Nama Lengkap',
                'phone_number' => '081234567890',
                'gender' => 'L/P',
                'birth_place' => 'Jakarta',
                'birth_date' => '1990-01-01',
                'identity_number' => '3201234567890123',
                'address' => 'Jalan Contoh No. 123',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'district' => 'Kebayoran Baru',
                'postal_code' => '12345',
                'university_name' => 'Universitas Contoh',
                'campus_location' => 'Jakarta',
                'faculty' => 'Fakultas Teknik',
                'department' => 'Teknik Informatika',
                'employee_id_number' => 'EMP001',
                'employment_status' => 'permanent/contract/honorary',
                'work_start_date' => '2020-01-01',
                'gross_salary' => '5000000',
                'bank_name' => 'Bank BCA',
                'bank_account_number' => '1234567890',
                'bank_account_name' => 'Nama Lengkap',
                'notes' => 'Catatan tambahan'
            ]
        ];

        $filename = 'template_import_anggota_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write header
        fputcsv($output, $csvHeader);

        // Write sample data
        foreach ($sampleData as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Parse CSV file
     */
    private function parseCSV(string $filePath): array
    {
        $data = [];
        $header = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            // Read header
            $header = fgetcsv($handle);

            // Remove BOM if present
            if (isset($header[0])) {
                $header[0] = str_replace("\xEF\xBB\xBF", '', $header[0]);
            }

            // Read data rows
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) === count($header)) {
                    $data[] = array_combine($header, $row);
                }
            }

            fclose($handle);
        }

        return $data;
    }

    /**
     * Parse Excel file (basic implementation)
     * Note: For production, consider using PhpSpreadsheet library
     */
    private function parseExcel(string $filePath): array
    {
        // For now, return error - Excel parsing requires PhpSpreadsheet
        throw new \Exception('Excel parsing belum diimplementasikan. Silakan gunakan format CSV atau install library PhpSpreadsheet.');
    }

    /**
     * Validate import data
     */
    private function validateImportData(array $data): array
    {
        $results = [
            'valid_count' => 0,
            'invalid_count' => 0,
            'errors' => []
        ];

        foreach ($data as $index => $row) {
            $rowErrors = [];

            // Required fields validation
            if (empty($row['email'])) {
                $rowErrors[] = 'Email wajib diisi';
            } elseif (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                $rowErrors[] = 'Format email tidak valid';
            }

            if (empty($row['full_name'])) {
                $rowErrors[] = 'Nama lengkap wajib diisi';
            }

            // Optional validations
            if (!empty($row['gender']) && !in_array($row['gender'], ['L', 'P'])) {
                $rowErrors[] = 'Jenis kelamin harus L atau P';
            }

            if (!empty($row['birth_date']) && !strtotime($row['birth_date'])) {
                $rowErrors[] = 'Format tanggal lahir tidak valid (gunakan YYYY-MM-DD)';
            }

            if (!empty($row['work_start_date']) && !strtotime($row['work_start_date'])) {
                $rowErrors[] = 'Format tanggal mulai kerja tidak valid (gunakan YYYY-MM-DD)';
            }

            if (!empty($row['employment_status']) && !in_array($row['employment_status'], ['permanent', 'contract', 'honorary'])) {
                $rowErrors[] = 'Status kepegawaian harus: permanent, contract, atau honorary';
            }

            if (count($rowErrors) > 0) {
                $results['errors'][$index] = $rowErrors;
                $results['invalid_count']++;
            } else {
                $results['valid_count']++;
            }
        }

        return $results;
    }

    /**
     * Check if member data is complete
     * Data dianggap lengkap jika memiliki minimal:
     * - Data pribadi: email, nama, jenis kelamin, tanggal lahir, alamat
     * - Data pekerjaan: universitas, fakultas, departemen
     */
    private function checkDataCompleteness(array $data): bool
    {
        $requiredFields = [
            'email',
            'full_name',
            'gender',
            'birth_date',
            'address',
            'university_name',
            'faculty',
            'department'
        ];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Cancel import and clear session
     */
    public function cancel()
    {
        $importData = $this->session->get('import_data');

        if ($importData && isset($importData['file_path']) && file_exists($importData['file_path'])) {
            unlink($importData['file_path']);
        }

        $this->session->remove('import_data');

        return redirect()->to(base_url('admin/members/bulk-import'))
            ->with('info', 'Import dibatalkan');
    }
}
