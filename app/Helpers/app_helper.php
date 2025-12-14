<?php

/**
 * Application Helper
 * General helper functions for the application
 */

if (!function_exists('format_currency')) {
    /**
     * Format number to Indonesian currency
     *
     * @param float $amount Amount to format
     * @param bool $withSymbol Include Rp symbol
     * @return string Formatted currency
     */
    function format_currency(float $amount, bool $withSymbol = true): string
    {
        $formatted = number_format($amount, 0, ',', '.');
        return $withSymbol ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('format_date_indonesia')) {
    /**
     * Format date to Indonesian format
     *
     * @param string $date Date string
     * @param string $format Format type: 'short', 'long', 'datetime'
     * @return string Formatted date
     */
    function format_date_indonesia(string $date, string $format = 'short'): string
    {
        if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return '-';
        }

        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $timestamp = strtotime($date);
        $day = date('d', $timestamp);
        $month = (int)date('m', $timestamp);
        $year = date('Y', $timestamp);
        $time = date('H:i', $timestamp);

        switch ($format) {
            case 'short':
                return $day . ' ' . $months[$month] . ' ' . $year;
            case 'long':
                return $day . ' ' . $months[$month] . ' ' . $year;
            case 'datetime':
                return $day . ' ' . $months[$month] . ' ' . $year . ' ' . $time;
            default:
                return $day . ' ' . $months[$month] . ' ' . $year;
        }
    }
}

if (!function_exists('get_user_role_label')) {
    /**
     * Get Indonesian label for user role
     *
     * @param string $role Role code
     * @return string Role label
     */
    function get_user_role_label(string $role): string
    {
        $roles = [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin (Pengurus Pusat)',
            'coordinator' => 'Koordinator (Pengurus Wilayah)',
            'treasurer' => 'Bendahara',
            'member' => 'Anggota',
            'candidate' => 'Calon Anggota',
        ];

        return $roles[$role] ?? ucfirst($role);
    }
}

if (!function_exists('get_membership_status_label')) {
    /**
     * Get Indonesian label for membership status
     *
     * @param string $status Status code
     * @return string Status label
     */
    function get_membership_status_label(string $status): string
    {
        $statuses = [
            'candidate' => 'Calon Anggota',
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            'disabled' => 'Dinonaktifkan',
            'rejected' => 'Ditolak',
        ];

        return $statuses[$status] ?? ucfirst($status);
    }
}

if (!function_exists('get_onboarding_state_label')) {
    /**
     * Get Indonesian label for onboarding state
     *
     * @param string $state State code
     * @return string State label
     */
    function get_onboarding_state_label(string $state): string
    {
        $states = [
            'registered' => 'Terdaftar',
            'payment_submitted' => 'Pembayaran Terkirim',
            'email_verified' => 'Email Terverifikasi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        return $states[$state] ?? ucfirst($state);
    }
}

if (!function_exists('get_account_status_badge')) {
    /**
     * Get Bootstrap badge class for account status
     *
     * @param string $status Status code
     * @return string Bootstrap badge class
     */
    function get_account_status_badge(string $status): string
    {
        $badges = [
            'pending' => 'warning',
            'active' => 'success',
            'suspended' => 'danger',
            'rejected' => 'danger',
        ];

        $class = $badges[$status] ?? 'secondary';
        return 'badge bg-' . $class;
    }
}

if (!function_exists('get_membership_status_badge')) {
    /**
     * Get Bootstrap badge class for membership status
     *
     * @param string $status Status code
     * @return string Bootstrap badge class
     */
    function get_membership_status_badge(string $status): string
    {
        $badges = [
            'candidate' => 'info',
            'active' => 'success',
            'inactive' => 'warning',
            'disabled' => 'danger',
            'rejected' => 'danger',
        ];

        $class = $badges[$status] ?? 'secondary';
        return 'badge bg-' . $class;
    }
}

if (!function_exists('time_elapsed_string')) {
    /**
     * Get time elapsed string (e.g., "2 jam yang lalu")
     *
     * @param string $datetime Datetime string
     * @return string Elapsed time string
     */
    function time_elapsed_string(string $datetime): string
    {
        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        if ($diff->y > 0) {
            return $diff->y . ' tahun yang lalu';
        } elseif ($diff->m > 0) {
            return $diff->m . ' bulan yang lalu';
        } elseif ($diff->d > 0) {
            return $diff->d . ' hari yang lalu';
        } elseif ($diff->h > 0) {
            return $diff->h . ' jam yang lalu';
        } elseif ($diff->i > 0) {
            return $diff->i . ' menit yang lalu';
        } else {
            return 'Baru saja';
        }
    }
}

if (!function_exists('generate_member_number')) {
    /**
     * Generate unique member number
     *
     * @param int $memberId Member ID
     * @param string $regionCode Region code (optional)
     * @return string Member number (e.g., SPK-2024-001-JKT)
     */
    function generate_member_number(int $memberId, string $regionCode = ''): string
    {
        $year = date('Y');
        $sequence = str_pad($memberId, 4, '0', STR_PAD_LEFT);

        if (!empty($regionCode)) {
            return 'SPK-' . $year . '-' . $sequence . '-' . strtoupper($regionCode);
        }

        return 'SPK-' . $year . '-' . $sequence;
    }
}

if (!function_exists('mask_email')) {
    /**
     * Mask email address for privacy
     *
     * @param string $email Email address
     * @return string Masked email
     */
    function mask_email(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        $name = $parts[0];
        $domain = $parts[1];

        $nameLength = strlen($name);
        if ($nameLength <= 2) {
            $masked = $name[0] . '*';
        } else {
            $visibleChars = ceil($nameLength / 3);
            $masked = substr($name, 0, $visibleChars) . str_repeat('*', $nameLength - $visibleChars);
        }

        return $masked . '@' . $domain;
    }
}

if (!function_exists('mask_phone')) {
    /**
     * Mask phone number for privacy
     *
     * @param string $phone Phone number
     * @return string Masked phone
     */
    function mask_phone(string $phone): string
    {
        $length = strlen($phone);
        if ($length <= 4) {
            return $phone;
        }

        $visible = 4;
        $masked = substr($phone, 0, $visible) . str_repeat('*', $length - $visible - 2) . substr($phone, -2);

        return $masked;
    }
}

if (!function_exists('sanitize_filename')) {
    /**
     * Sanitize filename for safe storage
     *
     * @param string $filename Original filename
     * @return string Sanitized filename
     */
    function sanitize_filename(string $filename): string
    {
        // Remove path info
        $filename = basename($filename);

        // Replace spaces with underscores
        $filename = str_replace(' ', '_', $filename);

        // Remove special characters
        $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '', $filename);

        // Limit length
        if (strlen($filename) > 100) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($name, 0, 95) . '.' . $ext;
        }

        return $filename;
    }
}

if (!function_exists('is_production')) {
    /**
     * Check if environment is production
     *
     * @return bool
     */
    function is_production(): bool
    {
        return ENVIRONMENT === 'production';
    }
}

if (!function_exists('get_initials')) {
    /**
     * Get initials from name
     *
     * @param string $name Full name
     * @return string Initials (max 2 chars)
     */
    function get_initials(string $name): string
    {
        $words = explode(' ', $name);
        $initials = '';

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
                if (strlen($initials) >= 2) {
                    break;
                }
            }
        }

        return substr($initials, 0, 2);
    }
}

if (!function_exists('calculate_dues_amount')) {
    /**
     * Calculate monthly dues amount based on type and tier
     *
     * @param string $type 'golongan' or 'gaji'
     * @param string $tier Tier code (GOL1-4 or GAJI1-4)
     * @return float Dues amount
     */
    function calculate_dues_amount(string $type, string $tier): float
    {
        $rates = [
            'golongan' => [
                'GOL1' => 20000,
                'GOL2' => 30000,
                'GOL3' => 35000,
                'GOL4' => 45000,
            ],
            'gaji' => [
                'GAJI1' => 7500,
                'GAJI2' => 15000,
                'GAJI3' => 30000,
                'GAJI4' => 60000,
            ],
        ];

        return $rates[$type][$tier] ?? 0;
    }
}
