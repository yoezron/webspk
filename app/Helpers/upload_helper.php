<?php

/**
 * Upload Helper
 * Handle file uploads with validation
 */

if (!function_exists('upload_file')) {
    /**
     * Upload file with validation
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

        // Validate file extension
        $extension = $file->getClientExtension();
        if (!in_array(strtolower($extension), $allowedTypes)) {
            $result['error'] = 'Tipe file tidak diizinkan. Hanya: ' . implode(', ', $allowedTypes);
            return $result;
        }

        // Validate file size
        $fileSize = $file->getSize() / 1024; // Convert to KB
        if ($fileSize > $maxSize) {
            $result['error'] = 'Ukuran file terlalu besar. Maksimal: ' . number_format($maxSize / 1024, 2) . ' MB';
            return $result;
        }

        // Generate unique filename
        $newName = generate_unique_filename($file->getClientName(), $extension);

        // Create upload directory if not exists
        $fullPath = WRITEPATH . $uploadPath;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
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
     * Get public URL for uploaded file
     *
     * @param string $fileName File name
     * @param string $uploadPath Upload directory path
     * @return string|null File URL or null if not exists
     */
    function get_file_url(string $fileName, string $uploadPath = 'uploads/'): ?string
    {
        if (empty($fileName)) {
            return null;
        }

        $fullPath = WRITEPATH . $uploadPath . $fileName;

        if (file_exists($fullPath)) {
            // For writable files, we need a download route
            return base_url('files/' . $uploadPath . $fileName);
        }

        return null;
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
