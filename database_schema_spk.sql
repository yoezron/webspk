-- =====================================================
-- DATABASE SCHEMA: SERIKAT PEKERJA KAMPUS (SPK)
-- Framework: CodeIgniter 4
-- Database: MySQL 8.0+
-- Versi: 1.0
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- DROP EXISTING TABLES (untuk fresh install)
-- =====================================================

DROP TABLE IF EXISTS `sp_audit_logs`;
DROP TABLE IF EXISTS `sp_email_verifications`;
DROP TABLE IF EXISTS `sp_dues_claims`;
DROP TABLE IF EXISTS `sp_dues_payments`;
DROP TABLE IF EXISTS `sp_dues_bills`;
DROP TABLE IF EXISTS `sp_member_documents`;
DROP TABLE IF EXISTS `cms_contact_messages`;
DROP TABLE IF EXISTS `cms_subscribers`;
DROP TABLE IF EXISTS `cms_officers`;
DROP TABLE IF EXISTS `cms_media`;
DROP TABLE IF EXISTS `cms_news_posts`;
DROP TABLE IF EXISTS `cms_document_categories`;
DROP TABLE IF EXISTS `cms_documents`;
DROP TABLE IF EXISTS `cms_home_sections`;
DROP TABLE IF EXISTS `cms_page_revisions`;
DROP TABLE IF EXISTS `cms_pages`;
DROP TABLE IF EXISTS `rbac_submenu_permissions`;
DROP TABLE IF EXISTS `rbac_menu_permissions`;
DROP TABLE IF EXISTS `rbac_submenus`;
DROP TABLE IF EXISTS `rbac_menus`;
DROP TABLE IF EXISTS `rbac_role_permissions`;
DROP TABLE IF EXISTS `rbac_permissions`;
DROP TABLE IF EXISTS `rbac_roles`;
DROP TABLE IF EXISTS `sp_dues_rates`;
DROP TABLE IF EXISTS `sp_members`;

-- =====================================================
-- TABEL: sp_members (Anggota)
-- =====================================================

CREATE TABLE `sp_members` (
    -- IDENTITAS SISTEM
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` CHAR(36) NOT NULL,
    `member_number` VARCHAR(50) NULL DEFAULT NULL,
    `email` VARCHAR(150) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    
    -- STATUS & ROLE
    `role` ENUM('super_admin','admin','coordinator','treasurer','member','candidate') NOT NULL DEFAULT 'candidate',
    `membership_status` ENUM('candidate','active','inactive','disabled','rejected') NOT NULL DEFAULT 'candidate',
    `onboarding_state` ENUM('registered','payment_submitted','email_verified','approved','rejected') NOT NULL DEFAULT 'registered',
    `account_status` ENUM('pending','active','suspended','rejected') NOT NULL DEFAULT 'pending',
    
    -- VERIFIKASI & KEAMANAN
    `email_verified_at` DATETIME NULL DEFAULT NULL,
    `last_login_at` DATETIME NULL DEFAULT NULL,
    `last_login_ip` VARCHAR(45) NULL DEFAULT NULL,
    `last_user_agent` VARCHAR(255) NULL DEFAULT NULL,
    `failed_login_attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `locked_until` DATETIME NULL DEFAULT NULL,
    `password_changed_at` DATETIME NULL DEFAULT NULL,
    `reset_token_hash` VARCHAR(255) NULL DEFAULT NULL,
    `reset_token_expires_at` DATETIME NULL DEFAULT NULL,
    `remember_token_hash` VARCHAR(255) NULL DEFAULT NULL,
    
    -- DATA DEMOGRAFIS
    `full_name` VARCHAR(150) NOT NULL,
    `gender` ENUM('L','P') NULL DEFAULT NULL,
    `birth_place` VARCHAR(100) NULL DEFAULT NULL,
    `birth_date` DATE NULL DEFAULT NULL,
    `identity_number` VARCHAR(50) NULL DEFAULT NULL,
    `phone_number` VARCHAR(20) NULL DEFAULT NULL,
    `alt_phone_number` VARCHAR(20) NULL DEFAULT NULL,
    `address` TEXT NULL DEFAULT NULL,
    `province` VARCHAR(100) NULL DEFAULT NULL,
    `city` VARCHAR(100) NULL DEFAULT NULL,
    `district` VARCHAR(100) NULL DEFAULT NULL,
    `postal_code` VARCHAR(10) NULL DEFAULT NULL,
    `region_code` VARCHAR(10) NULL DEFAULT NULL,
    
    -- KONTAK DARURAT
    `emergency_contact_name` VARCHAR(150) NULL DEFAULT NULL,
    `emergency_contact_relation` VARCHAR(50) NULL DEFAULT NULL,
    `emergency_contact_phone` VARCHAR(20) NULL DEFAULT NULL,
    
    -- DATA PROFESI
    `university_name` VARCHAR(150) NOT NULL,
    `campus_location` VARCHAR(150) NULL DEFAULT NULL,
    `faculty` VARCHAR(150) NULL DEFAULT NULL,
    `department` VARCHAR(100) NULL DEFAULT NULL,
    `work_unit` VARCHAR(100) NULL DEFAULT NULL,
    `employee_id_number` VARCHAR(50) NULL DEFAULT NULL,
    `lecturer_id_number` VARCHAR(50) NULL DEFAULT NULL,
    `academic_rank` ENUM('Tenaga Pengajar','Asisten Ahli','Lektor','Lektor Kepala','Guru Besar','Tendik/Staff','Lainnya') NOT NULL DEFAULT 'Lainnya',
    `employment_status` ENUM('PNS','PPPK','Tetap Non-PNS','Kontrak/PKWT','Dosen Luar Biasa','Honorer','Lainnya') NOT NULL DEFAULT 'Lainnya',
    `employment_start_date` DATE NULL DEFAULT NULL,
    `contract_end_date` DATE NULL DEFAULT NULL,
    
    -- DATA EKONOMI
    `payroll_source` VARCHAR(100) NULL DEFAULT NULL,
    `salary_range` VARCHAR(50) NULL DEFAULT NULL,
    `base_salary` DECIMAL(15,2) NULL DEFAULT NULL,
    `take_home_pay` DECIMAL(15,2) NULL DEFAULT NULL,
    `consent_sensitive_data` TINYINT(1) NOT NULL DEFAULT 0,
    
    -- IURAN
    `dues_rate_id` INT UNSIGNED NULL DEFAULT NULL,
    `dues_method` ENUM('fixed','percentage','manual') NOT NULL DEFAULT 'manual',
    `dues_amount` DECIMAL(15,2) NULL DEFAULT NULL,
    `dues_percentage` DECIMAL(5,2) NULL DEFAULT NULL,
    `dues_status` ENUM('unpaid','paid','overdue','waived') NOT NULL DEFAULT 'unpaid',
    `dues_last_paid_at` DATE NULL DEFAULT NULL,
    
    -- ADVOKASI & ORGANISASI
    `expertise` TEXT NULL DEFAULT NULL,
    `motivation` TEXT NULL DEFAULT NULL,
    `advocacy_interests` TEXT NULL DEFAULT NULL,
    `is_volunteer` TINYINT(1) NOT NULL DEFAULT 0,
    `branch_name` VARCHAR(150) NULL DEFAULT NULL,
    `union_position` VARCHAR(100) NULL DEFAULT NULL,
    `membership_start_date` DATE NULL DEFAULT NULL,
    `membership_end_date` DATE NULL DEFAULT NULL,
    
    -- DOKUMEN & VERIFIKASI
    `id_proof_file` VARCHAR(255) NULL DEFAULT NULL,
    `employee_card_file` VARCHAR(255) NULL DEFAULT NULL,
    `membership_form_file` VARCHAR(255) NULL DEFAULT NULL,
    `profile_photo_file` VARCHAR(255) NULL DEFAULT NULL,
    `verification_status` ENUM('unverified','under_review','verified','rejected') NOT NULL DEFAULT 'unverified',
    `verified_by` INT UNSIGNED NULL DEFAULT NULL,
    `verified_at` DATETIME NULL DEFAULT NULL,
    `rejection_reason` TEXT NULL DEFAULT NULL,
    
    -- PERSETUJUAN
    `agreed_to_terms_at` DATETIME NULL DEFAULT NULL,
    `agreed_to_privacy_at` DATETIME NULL DEFAULT NULL,
    `admin_notes` TEXT NULL DEFAULT NULL,
    
    -- TIMESTAMPS
    `joined_at` DATE NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_uuid` (`uuid`),
    UNIQUE KEY `uk_email` (`email`),
    UNIQUE KEY `uk_member_number` (`member_number`),
    INDEX `idx_membership_status` (`membership_status`),
    INDEX `idx_role` (`role`),
    INDEX `idx_region_code` (`region_code`),
    INDEX `idx_university` (`university_name`),
    INDEX `idx_onboarding_state` (`onboarding_state`),
    INDEX `idx_account_status` (`account_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: sp_dues_rates (Tarif Iuran)
-- =====================================================

CREATE TABLE `sp_dues_rates` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `scheme_type` ENUM('golongan','gaji') NOT NULL,
    `label` VARCHAR(100) NOT NULL,
    `code` VARCHAR(20) NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `min_salary` DECIMAL(15,2) NULL DEFAULT NULL,
    `max_salary` DECIMAL(15,2) NULL DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_code` (`code`),
    INDEX `idx_scheme_active` (`scheme_type`, `is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL RBAC: rbac_roles
-- =====================================================

CREATE TABLE `rbac_roles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `display_name` VARCHAR(100) NULL DEFAULT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `level` TINYINT UNSIGNED NOT NULL DEFAULT 5,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_name` (`name`),
    INDEX `idx_level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL RBAC: rbac_permissions
-- =====================================================

CREATE TABLE `rbac_permissions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `display_name` VARCHAR(150) NULL DEFAULT NULL,
    `module` VARCHAR(50) NOT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_name` (`name`),
    INDEX `idx_module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL RBAC: rbac_role_permissions
-- =====================================================

CREATE TABLE `rbac_role_permissions` (
    `role_id` INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`role_id`, `permission_id`),
    CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `rbac_roles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `rbac_permissions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL RBAC: rbac_menus
-- =====================================================

CREATE TABLE `rbac_menus` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `icon` VARCHAR(50) NULL DEFAULT NULL,
    `url` VARCHAR(255) NULL DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `is_public` TINYINT(1) NOT NULL DEFAULT 0,
    `permission_logic` ENUM('ANY','ALL') NOT NULL DEFAULT 'ANY',
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_sort_active` (`sort_order`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL RBAC: rbac_submenus
-- =====================================================

CREATE TABLE `rbac_submenus` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `menu_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `icon` VARCHAR(50) NULL DEFAULT NULL,
    `url` VARCHAR(255) NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `permission_logic` ENUM('ANY','ALL') NOT NULL DEFAULT 'ANY',
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_menu_sort` (`menu_id`, `sort_order`),
    CONSTRAINT `fk_submenu_menu` FOREIGN KEY (`menu_id`) REFERENCES `rbac_menus`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL RBAC: rbac_menu_permissions
-- =====================================================

CREATE TABLE `rbac_menu_permissions` (
    `menu_id` INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    
    PRIMARY KEY (`menu_id`, `permission_id`),
    CONSTRAINT `fk_mp_menu` FOREIGN KEY (`menu_id`) REFERENCES `rbac_menus`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_mp_permission` FOREIGN KEY (`permission_id`) REFERENCES `rbac_permissions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL RBAC: rbac_submenu_permissions
-- =====================================================

CREATE TABLE `rbac_submenu_permissions` (
    `submenu_id` INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    
    PRIMARY KEY (`submenu_id`, `permission_id`),
    CONSTRAINT `fk_sp_submenu` FOREIGN KEY (`submenu_id`) REFERENCES `rbac_submenus`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_sp_permission` FOREIGN KEY (`permission_id`) REFERENCES `rbac_permissions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL CMS: cms_pages
-- =====================================================

CREATE TABLE `cms_pages` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slug` VARCHAR(100) NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `content_html` LONGTEXT NULL DEFAULT NULL,
    `template` ENUM('default','legal','contact') NOT NULL DEFAULT 'default',
    `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    `visibility` ENUM('public','member_only') NOT NULL DEFAULT 'public',
    `primary_document_id` INT UNSIGNED NULL DEFAULT NULL,
    `published_at` DATETIME NULL DEFAULT NULL,
    `created_by` INT UNSIGNED NULL DEFAULT NULL,
    `updated_by` INT UNSIGNED NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_slug` (`slug`),
    INDEX `idx_status_published` (`status`, `published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL CMS: cms_page_revisions
-- =====================================================

CREATE TABLE `cms_page_revisions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `page_id` INT UNSIGNED NOT NULL,
    `content_html` LONGTEXT NOT NULL,
    `note` TEXT NULL DEFAULT NULL,
    `created_by` INT UNSIGNED NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_page` (`page_id`),
    CONSTRAINT `fk_revision_page` FOREIGN KEY (`page_id`) REFERENCES `cms_pages`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL CMS: cms_home_sections
-- =====================================================

CREATE TABLE `cms_home_sections` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `section_key` VARCHAR(50) NOT NULL,
    `title` VARCHAR(200) NULL DEFAULT NULL,
    `body_html` TEXT NULL DEFAULT NULL,
    `config_json` JSON NULL DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `updated_by` INT UNSIGNED NULL DEFAULT NULL,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_section_key` (`section_key`),
    INDEX `idx_sort_enabled` (`sort_order`, `is_enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL CMS: cms_document_categories
-- =====================================================

CREATE TABLE `cms_document_categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `doc_type` ENUM('publikasi','regulasi') NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_type_slug` (`doc_type`, `slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL CMS: cms_documents
-- =====================================================

CREATE TABLE `cms_documents` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `doc_type` ENUM('publikasi','regulasi') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NULL DEFAULT NULL,
    `description` TEXT NULL DEFAULT NULL,
    `category_id` INT UNSIGNED NULL DEFAULT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL DEFAULT 'application/pdf',
    `file_size` INT UNSIGNED NOT NULL,
    `checksum_sha256` CHAR(64) NULL DEFAULT NULL,
    `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    `published_at` DATETIME NULL DEFAULT NULL,
    `download_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_by` INT UNSIGNED NULL DEFAULT NULL,
    `updated_by` INT UNSIGNED NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_slug` (`slug`),
    INDEX `idx_type_status` (`doc_type`, `status`, `published_at`),
    INDEX `idx_category` (`category_id`),
    CONSTRAINT `fk_doc_category` FOREIGN KEY (`category_id`) REFERENCES `cms_document_categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL CMS: cms_media
-- =====================================================

CREATE TABLE `cms_media` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `media_type` ENUM('image') NOT NULL DEFAULT 'image',
    `file_path` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `file_size` INT UNSIGNED NOT NULL,
    `checksum_sha256` CHAR(64) NULL DEFAULT NULL,
    `alt_text` VARCHAR(255) NULL DEFAULT NULL,
    `uploaded_by` INT UNSIGNED NULL DEFAULT NULL,
    `uploaded_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_type` (`media_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL CMS: cms_news_posts
-- =====================================================

CREATE TABLE `cms_news_posts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `excerpt` TEXT NULL DEFAULT NULL,
    `content_html` LONGTEXT NOT NULL,
    `cover_image_id` INT UNSIGNED NULL DEFAULT NULL,
    `status` ENUM('draft','published','archived') NOT NULL DEFAULT 'draft',
    `published_at` DATETIME NULL DEFAULT NULL,
    `author_id` INT UNSIGNED NOT NULL,
    `view_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_slug` (`slug`),
    INDEX `idx_status_published` (`status`, `published_at`),
    INDEX `idx_author` (`author_id`),
    CONSTRAINT `fk_news_cover` FOREIGN KEY (`cover_image_id`) REFERENCES `cms_media`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL CMS: cms_officers
-- =====================================================

CREATE TABLE `cms_officers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id` INT UNSIGNED NULL DEFAULT NULL,
    `full_name` VARCHAR(150) NOT NULL,
    `position_title` VARCHAR(100) NOT NULL,
    `level` ENUM('pusat','wilayah') NOT NULL DEFAULT 'pusat',
    `region_code` VARCHAR(10) NULL DEFAULT NULL,
    `photo_media_id` INT UNSIGNED NULL DEFAULT NULL,
    `bio_html` TEXT NULL DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `period_start` DATE NULL DEFAULT NULL,
    `period_end` DATE NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_level_active` (`level`, `is_active`, `sort_order`),
    INDEX `idx_region` (`region_code`),
    CONSTRAINT `fk_officer_member` FOREIGN KEY (`member_id`) REFERENCES `sp_members`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_officer_photo` FOREIGN KEY (`photo_media_id`) REFERENCES `cms_media`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL CMS: cms_subscribers
-- =====================================================

CREATE TABLE `cms_subscribers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(150) NOT NULL,
    `status` ENUM('pending','active','unsubscribed') NOT NULL DEFAULT 'pending',
    `token_hash` CHAR(64) NULL DEFAULT NULL,
    `verified_at` DATETIME NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_email` (`email`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL CMS: cms_contact_messages
-- =====================================================

CREATE TABLE `cms_contact_messages` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `subject` VARCHAR(255) NULL DEFAULT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('new','in_progress','closed') NOT NULL DEFAULT 'new',
    `assigned_to` INT UNSIGNED NULL DEFAULT NULL,
    `admin_reply` TEXT NULL DEFAULT NULL,
    `replied_at` DATETIME NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_assigned` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: sp_member_documents
-- =====================================================

CREATE TABLE `sp_member_documents` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id` INT UNSIGNED NOT NULL,
    `doc_type` ENUM('id_proof','employee_card','dues_payment_proof','profile_photo','membership_form','other') NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `file_size` INT UNSIGNED NOT NULL,
    `checksum_sha256` CHAR(64) NULL DEFAULT NULL,
    `review_status` ENUM('not_reviewed','approved','rejected') NOT NULL DEFAULT 'not_reviewed',
    `reviewer_id` INT UNSIGNED NULL DEFAULT NULL,
    `reviewed_at` DATETIME NULL DEFAULT NULL,
    `review_note` TEXT NULL DEFAULT NULL,
    `uploaded_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_member_type` (`member_id`, `doc_type`),
    CONSTRAINT `fk_doc_member` FOREIGN KEY (`member_id`) REFERENCES `sp_members`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: sp_dues_bills (Tagihan)
-- =====================================================

CREATE TABLE `sp_dues_bills` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id` INT UNSIGNED NOT NULL,
    `bill_type` ENUM('registration','monthly') NOT NULL,
    `period_year` SMALLINT UNSIGNED NULL DEFAULT NULL,
    `period_month` TINYINT UNSIGNED NULL DEFAULT NULL,
    `rate_id` INT UNSIGNED NULL DEFAULT NULL,
    `bill_amount` DECIMAL(15,2) NOT NULL,
    `bill_status` ENUM('unpaid','paid','overdue','waived') NOT NULL DEFAULT 'unpaid',
    `due_date` DATE NULL DEFAULT NULL,
    `arrears_level` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `waived_reason` TEXT NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_member_period` (`member_id`, `bill_type`, `period_year`, `period_month`),
    INDEX `idx_status` (`bill_status`),
    INDEX `idx_period` (`period_year`, `period_month`),
    CONSTRAINT `fk_bill_member` FOREIGN KEY (`member_id`) REFERENCES `sp_members`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_bill_rate` FOREIGN KEY (`rate_id`) REFERENCES `sp_dues_rates`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: sp_dues_payments (Pembayaran)
-- =====================================================

CREATE TABLE `sp_dues_payments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `bill_id` INT UNSIGNED NOT NULL,
    `member_id` INT UNSIGNED NOT NULL,
    `paid_amount` DECIMAL(15,2) NOT NULL,
    `paid_at` DATETIME NULL DEFAULT NULL,
    `payment_method` VARCHAR(50) NULL DEFAULT NULL,
    `ref_no` VARCHAR(100) NULL DEFAULT NULL,
    `proof_document_id` INT UNSIGNED NULL DEFAULT NULL,
    `payment_status` ENUM('submitted','verified','rejected') NOT NULL DEFAULT 'submitted',
    `verified_by` INT UNSIGNED NULL DEFAULT NULL,
    `verified_at` DATETIME NULL DEFAULT NULL,
    `verification_note` TEXT NULL DEFAULT NULL,
    `rejected_reason` TEXT NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_status` (`payment_status`),
    INDEX `idx_bill` (`bill_id`),
    INDEX `idx_member` (`member_id`),
    CONSTRAINT `fk_payment_bill` FOREIGN KEY (`bill_id`) REFERENCES `sp_dues_bills`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_payment_member` FOREIGN KEY (`member_id`) REFERENCES `sp_members`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_payment_proof` FOREIGN KEY (`proof_document_id`) REFERENCES `sp_member_documents`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: sp_dues_claims (Klaim)
-- =====================================================

CREATE TABLE `sp_dues_claims` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id` INT UNSIGNED NOT NULL,
    `bill_id` INT UNSIGNED NULL DEFAULT NULL,
    `payment_id` INT UNSIGNED NULL DEFAULT NULL,
    `claim_type` ENUM('already_paid','wrong_amount','wrong_period','double_payment','waiver_request','other') NOT NULL,
    `description` TEXT NOT NULL,
    `supporting_doc_id` INT UNSIGNED NULL DEFAULT NULL,
    `status` ENUM('submitted','in_review','approved','rejected') NOT NULL DEFAULT 'submitted',
    `processed_by` INT UNSIGNED NULL DEFAULT NULL,
    `processed_at` DATETIME NULL DEFAULT NULL,
    `decision_note` TEXT NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_member` (`member_id`),
    CONSTRAINT `fk_claim_member` FOREIGN KEY (`member_id`) REFERENCES `sp_members`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_claim_bill` FOREIGN KEY (`bill_id`) REFERENCES `sp_dues_bills`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_claim_payment` FOREIGN KEY (`payment_id`) REFERENCES `sp_dues_payments`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: sp_email_verifications
-- =====================================================

CREATE TABLE `sp_email_verifications` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id` INT UNSIGNED NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `token_hash` CHAR(64) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `used_at` DATETIME NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_token` (`token_hash`),
    INDEX `idx_member` (`member_id`),
    CONSTRAINT `fk_verify_member` FOREIGN KEY (`member_id`) REFERENCES `sp_members`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: sp_audit_logs
-- =====================================================

CREATE TABLE `sp_audit_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `actor_id` INT UNSIGNED NULL DEFAULT NULL,
    `actor_type` ENUM('member','system','anonymous') NOT NULL DEFAULT 'member',
    `target_type` VARCHAR(50) NOT NULL,
    `target_id` INT UNSIGNED NULL DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `old_values` JSON NULL DEFAULT NULL,
    `new_values` JSON NULL DEFAULT NULL,
    `ip_address` VARCHAR(45) NULL DEFAULT NULL,
    `user_agent` VARCHAR(255) NULL DEFAULT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_actor` (`actor_id`),
    INDEX `idx_target` (`target_type`, `target_id`),
    INDEX `idx_action` (`action`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATA SEEDER: sp_dues_rates
-- =====================================================

INSERT INTO `sp_dues_rates` (`scheme_type`, `label`, `code`, `amount`, `min_salary`, `max_salary`, `sort_order`, `is_active`) VALUES
('golongan', 'Golongan I (Ia, Ib, Ic, Id)', 'GOL1', 20000.00, NULL, NULL, 1, 1),
('golongan', 'Golongan II (IIa, IIb, IIc, IId)', 'GOL2', 30000.00, NULL, NULL, 2, 1),
('golongan', 'Golongan III (IIIa, IIIb, IIIc, IIId)', 'GOL3', 35000.00, NULL, NULL, 3, 1),
('golongan', 'Golongan IV (IVa, IVb, IVc, IVd, IVe)', 'GOL4', 45000.00, NULL, NULL, 4, 1),
('gaji', 'Rp 0 - Rp 1.500.000', 'GAJI1', 7500.00, 0.00, 1500000.00, 5, 1),
('gaji', 'Rp 1.500.000 - Rp 3.000.000', 'GAJI2', 15000.00, 1500000.00, 3000000.00, 6, 1),
('gaji', 'Rp 3.000.001 - Rp 6.000.000', 'GAJI3', 30000.00, 3000001.00, 6000000.00, 7, 1),
('gaji', 'Diatas Rp 6.000.000', 'GAJI4', 60000.00, 6000001.00, NULL, 8, 1);

-- =====================================================
-- DATA SEEDER: rbac_roles
-- =====================================================

INSERT INTO `rbac_roles` (`name`, `display_name`, `description`, `level`, `is_active`) VALUES
('super_admin', 'Super Admin', 'Akses penuh ke seluruh sistem termasuk konfigurasi', 1, 1),
('admin', 'Admin (Pengurus Pusat)', 'Manajemen anggota, konten, survei, dan komunikasi', 2, 1),
('coordinator', 'Pengurus Wilayah', 'Admin terbatas pada wilayah tertentu', 3, 1),
('treasurer', 'Bendahara', 'Fokus manajemen iuran dan keuangan', 3, 1),
('member', 'Anggota', 'Akses fitur anggota aktif', 4, 1),
('candidate', 'Calon Anggota', 'Akses terbatas untuk calon anggota', 5, 1);

-- =====================================================
-- DATA SEEDER: rbac_permissions
-- =====================================================

INSERT INTO `rbac_permissions` (`name`, `display_name`, `module`, `description`) VALUES
-- Profile & Account
('profile.view_self', 'Lihat Profil Sendiri', 'profile', 'Melihat profil sendiri'),
('profile.edit_self', 'Edit Profil Sendiri', 'profile', 'Mengedit profil sendiri'),
('password.change_self', 'Ubah Password', 'profile', 'Mengubah password sendiri'),
('membercard.view_self', 'Lihat Kartu Anggota', 'profile', 'Melihat kartu anggota sendiri'),

-- Content
('content.view_public', 'Lihat Konten Publik', 'content', 'Melihat konten publik'),
('content.view_member', 'Lihat Konten Member', 'content', 'Melihat konten khusus anggota'),
('content.manage', 'Kelola Konten', 'content', 'CRUD konten'),
('blog.create', 'Buat Blog', 'content', 'Membuat blog/news'),
('blog.manage', 'Kelola Blog', 'content', 'Mengelola semua blog'),
('notification.send_all', 'Broadcast Semua', 'content', 'Kirim notifikasi ke semua'),
('notification.send_region', 'Broadcast Wilayah', 'content', 'Kirim notifikasi ke wilayah'),

-- Member Management
('member.view_list', 'Lihat Daftar Anggota', 'member', 'Melihat daftar anggota'),
('member.view_detail', 'Lihat Detail Anggota', 'member', 'Melihat detail anggota'),
('member.approve_candidate', 'Approve Calon', 'member', 'Menyetujui calon anggota'),
('member.reject_candidate', 'Reject Calon', 'member', 'Menolak calon anggota'),
('member.disable', 'Nonaktifkan Anggota', 'member', 'Menonaktifkan anggota'),
('member.enable', 'Aktifkan Anggota', 'member', 'Mengaktifkan kembali anggota'),
('member.delete', 'Hapus Anggota', 'member', 'Menghapus anggota (soft delete)'),
('member.change_role', 'Ubah Role', 'member', 'Mengubah role anggota'),

-- Forum & Messages
('forum.view', 'Lihat Forum', 'forum', 'Melihat forum diskusi'),
('forum.post', 'Posting Forum', 'forum', 'Membuat posting di forum'),
('forum.moderate', 'Moderasi Forum', 'forum', 'Moderasi forum'),
('message.send_to_board', 'Kirim Pesan ke Pengurus', 'message', 'Mengirim pesan ke pengurus'),
('message.reply_to_member', 'Balas Pesan Anggota', 'message', 'Membalas pesan dari anggota'),
('complaint.view_inbox', 'Lihat Inbox Pengaduan', 'message', 'Melihat inbox pengaduan'),
('complaint.respond', 'Respon Pengaduan', 'message', 'Merespon pengaduan'),

-- Survey
('survey.fill', 'Isi Survei', 'survey', 'Mengisi survei'),
('survey.create', 'Buat Survei', 'survey', 'Membuat survei baru'),
('survey.view_results', 'Lihat Hasil Survei', 'survey', 'Melihat hasil survei'),

-- Dues
('dues.rate.manage', 'Kelola Tarif Iuran', 'dues', 'Mengelola master tarif iuran'),
('dues.bill.generate', 'Generate Tagihan', 'dues', 'Generate tagihan iuran'),
('dues.bill.view_all', 'Lihat Semua Tagihan', 'dues', 'Melihat semua tagihan'),
('dues.payment.view_all', 'Lihat Semua Pembayaran', 'dues', 'Melihat semua pembayaran'),
('dues.payment.verify', 'Verifikasi Pembayaran', 'dues', 'Memverifikasi pembayaran'),
('dues.payment.reject', 'Tolak Pembayaran', 'dues', 'Menolak pembayaran'),
('dues.claim.view_all', 'Lihat Semua Klaim', 'dues', 'Melihat semua klaim'),
('dues.claim.process', 'Proses Klaim', 'dues', 'Memproses klaim'),
('dues.arrears.enforce_pending', 'Set Pending Tunggakan', 'dues', 'Menetapkan status pending'),
('dues.arrears.reactivate', 'Reaktivasi Tunggakan', 'dues', 'Reaktivasi setelah lunas'),
('dues.report.export_all', 'Export Laporan Keuangan', 'dues', 'Export laporan keuangan'),
('dues.dashboard.view_all', 'Lihat Dashboard Keuangan', 'dues', 'Melihat dashboard keuangan'),

-- Region
('region.member.download', 'Download Data Wilayah', 'region', 'Download data anggota wilayah'),
('region.link.update', 'Update Link WA Wilayah', 'region', 'Update link WhatsApp wilayah'),
('region.stats.view', 'Lihat Statistik Wilayah', 'region', 'Melihat statistik wilayah'),
('region.dues.view', 'Lihat Rekap Iuran Wilayah', 'region', 'Melihat rekap iuran wilayah'),

-- System
('role.manage', 'Kelola Role', 'system', 'Mengelola role'),
('menu.manage', 'Kelola Menu', 'system', 'Mengelola menu'),
('submenu.manage', 'Kelola Submenu', 'system', 'Mengelola submenu'),
('audit.view', 'Lihat Audit Log', 'system', 'Melihat audit log'),
('masterdata.bulk_import', 'Bulk Import Master', 'system', 'Bulk import master data'),
('members.bulk_import', 'Bulk Import Anggota', 'system', 'Bulk import anggota');

-- =====================================================
-- DATA SEEDER: cms_home_sections
-- =====================================================

INSERT INTO `cms_home_sections` (`section_key`, `title`, `body_html`, `config_json`, `sort_order`, `is_enabled`) VALUES
('about', 'Tentang Serikat Pekerja Kampus', '<p>Deskripsi tentang SPK...</p>', NULL, 1, 1),
('stats', 'Statistik Anggota', NULL, '{"mode":"dynamic","cache_minutes":60,"show_gender":true,"show_province":true}', 2, 1),
('latest_publications', 'Publikasi Terkini', NULL, '{"source":"cms_documents","type":"publikasi","limit":6}', 3, 1),
('cta_join', 'Bergabung', NULL, '{"button_text":"Bergabung Sekarang","url":"/register"}', 4, 1),
('cta_login', 'Login', NULL, '{"button_text":"Login","url":"/login"}', 5, 1),
('officers', 'Pengurus SPK', NULL, '{"level":"pusat","limit":10}', 6, 1),
('subscribe', 'Subscribe Newsletter', NULL, '{"double_opt_in":true}', 7, 1),
('footer', 'Footer', NULL, '{"address":"Alamat SPK","email":"info@spk.id","phone":"08xx","socials":[{"label":"Instagram","url":"https://instagram.com/spk"},{"label":"Twitter","url":"https://twitter.com/spk"}]}', 8, 1);

-- =====================================================
-- DATA SEEDER: cms_pages
-- =====================================================

INSERT INTO `cms_pages` (`slug`, `title`, `content_html`, `template`, `status`, `visibility`, `published_at`) VALUES
('sejarah', 'Sejarah SPK', '<p>Konten sejarah SPK...</p>', 'default', 'published', 'public', NOW()),
('manifesto', 'Manifesto SPK', '<p>Konten manifesto SPK...</p>', 'legal', 'published', 'public', NOW()),
('visimisi', 'Visi dan Misi', '<p>Konten visi misi SPK...</p>', 'default', 'published', 'public', NOW()),
('ad-art', 'Anggaran Dasar dan Anggaran Rumah Tangga', '<p>Konten AD/ART SPK...</p>', 'legal', 'published', 'public', NOW()),
('contact', 'Hubungi Kami', '<p>Informasi kontak SPK...</p>', 'contact', 'published', 'public', NOW());

-- =====================================================
-- DATA SEEDER: cms_document_categories
-- =====================================================

INSERT INTO `cms_document_categories` (`doc_type`, `name`, `slug`, `sort_order`) VALUES
('publikasi', 'Policy Brief', 'policy-brief', 1),
('publikasi', 'Kajian', 'kajian', 2),
('publikasi', 'Laporan', 'laporan', 3),
('regulasi', 'Undang-Undang', 'undang-undang', 1),
('regulasi', 'Peraturan Pemerintah', 'peraturan-pemerintah', 2),
('regulasi', 'Peraturan Menteri', 'peraturan-menteri', 3);

-- =====================================================
-- SUPER ADMIN DEFAULT (password: Admin@123)
-- =====================================================

INSERT INTO `sp_members` (
    `uuid`, `email`, `password_hash`, `full_name`, `role`, 
    `membership_status`, `onboarding_state`, `account_status`,
    `university_name`, `email_verified_at`, `member_number`
) VALUES (
    UUID(), 'superadmin@spk.id', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Super Administrator', 'super_admin',
    'active', 'approved', 'active',
    'SPK Pusat', NOW(), 'SPK-2024-00001'
);

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- END OF SCHEMA
-- =====================================================
