<?php

/**
 * Upload Helper
 * Handle file uploads with validation
 */

if (!function_exists('upload_file')) {
    /**
     * Upload file with validation (MIME type + extension)
     *
     * @param object $file Uploaded file object
     * @param string $uploadPath Upload directory path (relative to writable/)
     * @param array $allowedTypes Allowed file extensions
     * @param int $maxSize Maximum file size in KB (default 2048 = 2MB)
     * @return array ['success' => bool, 'file_name' => string, 'error' => string]
     */
    function upload_file($file, string $uploadPath = 'uploads/', array $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'], int $maxSize = 2048): array
    {
        $result = [
            'success' => false,
            'file_name' => null,
            'error' => null,
        ];

        // Check if file was uploaded
        if (!$file || !$file->isValid()) {
            $result['error'] = 'File tidak valid atau gagal diupload';
            return $result;
        }

        // CRITICAL: Validate MIME type FIRST (can't be spoofed easily)
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientExtension());

        // Extension to MIME type mapping (whitelist)
        $allowedMimeTypes = [
            'jpg' => ['image/jpeg', 'image/jpg'],
            'jpeg' => ['image/jpeg', 'image/jpg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'xls' => ['application/vnd.ms-excel'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        ];

        // Validate extension is in allowed list
        if (!in_array($extension, $allowedTypes)) {
            $result['error'] = 'Tipe file tidak diizinkan. Hanya: ' . implode(', ', $allowedTypes);
            return $result;
        }

        // Validate MIME type matches extension
        if (!isset($allowedMimeTypes[$extension])) {
            $result['error'] = 'Extension tidak didukung untuk validasi MIME';
            return $result;
        }

        if (!in_array($mimeType, $allowedMimeTypes[$extension])) {
            $result['error'] = sprintf(
                'MIME type tidak sesuai. File extension: %s, MIME type: %s. File mungkin telah dimanipulasi.',
                $extension,
                $mimeType
            );
            log_message('warning', "MIME type mismatch detected: Extension={$extension}, MIME={$mimeType}, IP=" . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            return $result;
        }

        // Additional validation for images (magic number check)
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $imageValidation = validate_image($file);
            if (!$imageValidation['valid']) {
                $result['error'] = $imageValidation['error'];
                return $result;
            }
        }

        // Additional validation for PDFs (magic number check)
        if ($extension === 'pdf') {
            $pdfValidation = validate_pdf_header($file);
            if (!$pdfValidation['valid']) {
                $result['error'] = $pdfValidation['error'];
                return $result;
            }
        }

        // Validate file size
        $fileSize = $file->getSize() / 1024; // Convert to KB
        if ($fileSize > $maxSize) {
            $result['error'] = 'Ukuran file terlalu besar. Maksimal: ' . number_format($maxSize / 1024, 2) . ' MB';
            return $result;
        }

        // Generate unique filename with sanitization
        $newName = generate_unique_filename($file->getClientName(), $extension);

        // SECURITY: Sanitize upload path to prevent directory traversal
        $uploadPath = str_replace(['..', '\\'], ['', '/'], $uploadPath);
        $uploadPath = trim($uploadPath, '/') . '/';

        // Validate upload path is within writable directory
        $fullPath = realpath(WRITEPATH) . '/' . $uploadPath;
        $writablePath = realpath(WRITEPATH);

        // Ensure path is within WRITEPATH (prevent directory traversal)
        if (strpos($fullPath, $writablePath) !== 0) {
            $result['error'] = 'Path upload tidak valid (directory traversal detected)';
            log_message('error', "Directory traversal attempt blocked: {$uploadPath}");
            return $result;
        }

        // Create upload directory if not exists
        if (!is_dir($fullPath)) {
            if (!mkdir($fullPath, 0755, true)) {
                $result['error'] = 'Gagal membuat direktori upload';
                return $result;
            }
        }

        // Move file
        try {
            if ($file->move($fullPath, $newName)) {
                $result['success'] = true;
                $result['file_name'] = $newName;
            } else {
                $result['error'] = 'Gagal memindahkan file ke direktori tujuan';
            }
        } catch (\Exception $e) {
            $result['error'] = 'Error: ' . $e->getMessage();
        }

        return $result;
    }
}

if (!function_exists('generate_unique_filename')) {
    /**
     * Generate unique filename
     *
     * @param string $originalName Original filename
     * @param string $extension File extension
     * @return string Unique filename
     */
    function generate_unique_filename(string $originalName, string $extension): string
    {
        $timestamp = date('YmdHis');
        $random = bin2hex(random_bytes(8));
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($originalName, PATHINFO_FILENAME));
        $safeName = substr($safeName, 0, 50); // Limit length

        return $safeName . '_' . $timestamp . '_' . $random . '.' . $extension;
    }
}

if (!function_exists('delete_file')) {
    /**
     * Delete file from upload directory
     *
     * @param string $fileName File name
     * @param string $uploadPath Upload directory path
     * @return bool
     */
    function delete_file(string $fileName, string $uploadPath = 'uploads/'): bool
    {
        if (empty($fileName)) {
            return false;
        }

        $fullPath = WRITEPATH . $uploadPath . $fileName;

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }
}

if (!function_exists('get_file_url')) {
    /**
     * Get secure authenticated URL for uploaded file
     *
     * @param string $fileName File name
     * @param string $uploadPath Upload directory path (e.g., 'uploads/payments/')
     * @return string|null Secure file URL or null if not exists
     */
    function get_file_url(string $fileName, string $uploadPath = 'uploads/'): ?string
    {
        if (empty($fileName)) {
            return null;
        }

        // Construct full path to check existence
        $fullPath = WRITEPATH . $uploadPath . $fileName;

        if (!file_exists($fullPath)) {
            return null;
        }

        // Extract file type from uploadPath for secure route
        // uploadPath format: 'uploads/TYPE/' where TYPE is payments, documents, or photos
        $uploadPath = trim($uploadPath, '/');
        $pathParts = explode('/', $uploadPath);

        // Get the last segment which should be the type (payments, documents, photos)
        $type = end($pathParts);

        // Validate type is one of the allowed types
        $allowedTypes = ['payments', 'documents', 'photos'];
        if (!in_array($type, $allowedTypes)) {
            log_message('error', "Invalid file type in get_file_url: {$type} from path {$uploadPath}");
            return null;
        }

        // Return secure authenticated route
        // Route format: files/{type}/{filename}
        return base_url("files/{$type}/{$fileName}");
    }
}

if (!function_exists('validate_image')) {
    /**
     * Validate if file is a valid image
     *
     * @param object $file Uploaded file object
     * @return array ['valid' => bool, 'error' => string]
     */
    function validate_image($file): array
    {
        $result = ['valid' => true, 'error' => null];

        if (!$file || !$file->isValid()) {
            $result['valid'] = false;
            $result['error'] = 'File tidak valid';
            return $result;
        }

        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
        $mimeType = $file->getMimeType();

        if (!in_array($mimeType, $allowedMimes)) {
            $result['valid'] = false;
            $result['error'] = 'File bukan gambar yang valid (JPG/PNG)';
            return $result;
        }

        // Additional image validation
        try {
            $imageInfo = getimagesize($file->getTempName());
            if ($imageInfo === false) {
                $result['valid'] = false;
                $result['error'] = 'File bukan gambar yang valid';
            }
        } catch (\Exception $e) {
            $result['valid'] = false;
            $result['error'] = 'Gagal memvalidasi gambar';
        }

        return $result;
    }
}

if (!function_exists('validate_document')) {
    /**
     * Validate if file is a valid document
     *
     * @param object $file Uploaded file object
     * @return array ['valid' => bool, 'error' => string]
     */
    function validate_document($file): array
    {
        $result = ['valid' => true, 'error' => null];

        if (!$file || !$file->isValid()) {
            $result['valid'] = false;
            $result['error'] = 'File tidak valid';
            return $result;
        }

        $allowedMimes = [
            'application/pdf',
            'image/jpeg',
            'image/jpg',
            'image/png',
        ];
        $mimeType = $file->getMimeType();

        if (!in_array($mimeType, $allowedMimes)) {
            $result['valid'] = false;
            $result['error'] = 'File harus berupa PDF, JPG, atau PNG';
        }

        return $result;
    }
}

if (!function_exists('validate_pdf_header')) {
    /**
     * Validate PDF file by checking magic number (file signature)
     * PDF files must start with '%PDF-' (hex: 25 50 44 46 2D)
     *
     * @param object $file Uploaded file object
     * @return array ['valid' => bool, 'error' => string]
     */
    function validate_pdf_header($file): array
    {
        $result = ['valid' => true, 'error' => null];

        if (!$file || !$file->isValid()) {
            $result['valid'] = false;
            $result['error'] = 'File tidak valid';
            return $result;
        }

        try {
            $handle = fopen($file->getTempName(), 'rb');
            if ($handle === false) {
                $result['valid'] = false;
                $result['error'] = 'Gagal membaca file PDF';
                return $result;
            }

            // Read first 5 bytes (PDF magic number)
            $header = fread($handle, 5);
            fclose($handle);

            // PDF files must start with '%PDF-'
            if ($header !== '%PDF-') {
                $result['valid'] = false;
                $result['error'] = 'File bukan PDF yang valid (magic number tidak sesuai)';
                log_message('warning', 'Invalid PDF magic number detected: ' . bin2hex($header));
            }
        } catch (\Exception $e) {
            $result['valid'] = false;
            $result['error'] = 'Gagal memvalidasi file PDF';
            log_message('error', 'PDF validation error: ' . $e->getMessage());
        }

        return $result;
    }
}

if (!function_exists('sanitize_filename')) {
    /**
     * Sanitize filename to prevent directory traversal and other attacks
     *
     * @param string $filename Original filename
     * @return string Sanitized filename
     */
    function sanitize_filename(string $filename): string
    {
        // Remove any path information
        $filename = basename($filename);

        // Remove special characters that could be dangerous
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        // Remove multiple consecutive dots (prevent ../ attacks)
        $filename = preg_replace('/\.{2,}/', '.', $filename);

        // Limit length
        if (strlen($filename) > 255) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $basename = pathinfo($filename, PATHINFO_FILENAME);
            $basename = substr($basename, 0, 255 - strlen($extension) - 1);
            $filename = $basename . '.' . $extension;
        }

        return $filename;
    }
}

if (!function_exists('format_file_size')) {
    /**
     * Format file size to human readable
     *
     * @param int $bytes File size in bytes
     * @return string Formatted size
     */
    function format_file_size(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
