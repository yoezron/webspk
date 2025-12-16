<?php

namespace App\Controllers;

use App\Models\MemberModel;
use App\Models\DuesPaymentModel;

class FileController extends BaseController
{
    protected $memberModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->paymentModel = new DuesPaymentModel();
    }

    /**
     * Secure file download with authentication and authorization
     *
     * @param string $type File type (payments, documents, photos)
     * @param string $filename Filename to download
     * @return mixed
     */
    public function download(string $type, string $filename)
    {
        // CRITICAL: Authentication check
        if (!session()->has('user_id') || !session()->get('is_logged_in')) {
            log_message('warning', "Unauthorized file access attempt: {$type}/{$filename} from IP " . $this->request->getIPAddress());
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Unauthorized access. Please login first.'
            ]);
        }

        $userId = session()->get('user_id');
        $userRole = session()->get('user_role');

        // Sanitize inputs to prevent directory traversal
        $type = $this->sanitizePathSegment($type);
        $filename = $this->sanitizePathSegment($filename);

        if (!$type || !$filename) {
            log_message('warning', "Invalid file path detected: {$type}/{$filename}");
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid file path'
            ]);
        }

        // Validate file type
        $allowedTypes = ['payments', 'documents', 'photos'];
        if (!in_array($type, $allowedTypes)) {
            log_message('warning', "Invalid file type requested: {$type}");
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid file type'
            ]);
        }

        // Construct file path
        $filePath = WRITEPATH . 'uploads/' . $type . '/' . $filename;

        // Check file exists
        if (!file_exists($filePath) || !is_file($filePath)) {
            log_message('info', "File not found: {$filePath}");
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'File not found'
            ]);
        }

        // CRITICAL: Authorization check - verify user owns the file or is admin
        if (!$this->canAccessFile($userId, $userRole, $type, $filename)) {
            log_message('warning', "Unauthorized file access by user {$userId}: {$type}/{$filename}");
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You do not have permission to access this file'
            ]);
        }

        // Get MIME type for proper content-type header
        $mimeType = $this->getMimeType($filePath);

        // Audit log file download
        log_message('info', "File downloaded by user {$userId} ({$userRole}): {$type}/{$filename}");

        // Serve file with security headers
        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($filename) . '"')
            ->setHeader('Content-Length', filesize($filePath))
            ->setHeader('X-Content-Type-Options', 'nosniff')  // Prevent MIME sniffing
            ->setHeader('Cache-Control', 'private, max-age=3600')  // Cache for 1 hour
            ->setBody(file_get_contents($filePath));
    }

    /**
     * Check if user can access the file
     *
     * @param int $userId User ID
     * @param string $userRole User role
     * @param string $type File type
     * @param string $filename Filename
     * @return bool
     */
    private function canAccessFile(int $userId, string $userRole, string $type, string $filename): bool
    {
        // Super admins and admins can access all files
        if (in_array($userRole, ['super_admin', 'admin'])) {
            return true;
        }

        // Get user's own files
        $member = $this->memberModel->find($userId);
        if (!$member) {
            return false;
        }

        // Check ownership based on file type
        switch ($type) {
            case 'payments':
                // Check if user owns this payment proof
                return $this->isPaymentOwnedByUser($userId, $filename);

            case 'documents':
                // Check if document belongs to user
                $userDocuments = [
                    $member['id_card_photo'],
                    $member['family_card_photo'],
                    $member['sk_pengangkatan_photo'],
                ];
                return in_array($filename, $userDocuments);

            case 'photos':
                // Check if photo belongs to user
                return $member['profile_photo'] === $filename;

            default:
                return false;
        }
    }

    /**
     * Check if payment proof is owned by user
     *
     * @param int $userId User ID
     * @param string $filename Filename
     * @return bool
     */
    private function isPaymentOwnedByUser(int $userId, string $filename): bool
    {
        // Check in dues_payments table
        $payment = $this->paymentModel
            ->where('member_id', $userId)
            ->where('payment_proof', $filename)
            ->first();

        if ($payment) {
            return true;
        }

        // Also check registration_payment_proof in members table
        $member = $this->memberModel->find($userId);
        return $member && $member['registration_payment_proof'] === $filename;
    }

    /**
     * Sanitize path segment to prevent directory traversal
     *
     * @param string $segment Path segment
     * @return string|null Sanitized segment or null if invalid
     */
    private function sanitizePathSegment(string $segment): ?string
    {
        // Remove any path separators
        $segment = basename($segment);

        // Remove dangerous characters
        $segment = preg_replace('/[^a-zA-Z0-9._-]/', '', $segment);

        // Prevent empty or dot-only segments
        if (empty($segment) || $segment === '.' || $segment === '..') {
            return null;
        }

        return $segment;
    }

    /**
     * Get MIME type for file
     *
     * @param string $filePath File path
     * @return string MIME type
     */
    private function getMimeType(string $filePath): string
    {
        // Use finfo to detect MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        // Fallback based on extension
        if (!$mimeType) {
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeTypes = [
                'pdf' => 'application/pdf',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
            ];
            $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        }

        return $mimeType;
    }
}
