<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingForeignKeyConstraints extends Migration
{
    public function up()
    {
        // Add foreign key constraints to enforce referential integrity
        // All foreign keys use ON DELETE SET NULL to prevent cascade deletion of records
        // when admin accounts are deleted, preserving audit trail

        // 1. sp_dues_payments.verified_by → sp_members.id
        // Admin who verified the payment
        try {
            $this->db->query('
                ALTER TABLE sp_dues_payments
                ADD CONSTRAINT fk_dues_payments_verified_by
                FOREIGN KEY (verified_by) REFERENCES sp_members(id)
                ON DELETE SET NULL
                ON UPDATE CASCADE
            ');
            log_message('info', 'Foreign key fk_dues_payments_verified_by added successfully');
        } catch (\Exception $e) {
            log_message('warning', 'Foreign key fk_dues_payments_verified_by might already exist: ' . $e->getMessage());
        }

        // 2. sp_members.registration_verified_by → sp_members.id
        // Admin who verified the registration payment
        try {
            $this->db->query('
                ALTER TABLE sp_members
                ADD CONSTRAINT fk_members_registration_verified_by
                FOREIGN KEY (registration_verified_by) REFERENCES sp_members(id)
                ON DELETE SET NULL
                ON UPDATE CASCADE
            ');
            log_message('info', 'Foreign key fk_members_registration_verified_by added successfully');
        } catch (\Exception $e) {
            log_message('warning', 'Foreign key fk_members_registration_verified_by might already exist: ' . $e->getMessage());
        }

        // 3. sp_members.approved_by → sp_members.id
        // Admin who approved the member
        try {
            $this->db->query('
                ALTER TABLE sp_members
                ADD CONSTRAINT fk_members_approved_by
                FOREIGN KEY (approved_by) REFERENCES sp_members(id)
                ON DELETE SET NULL
                ON UPDATE CASCADE
            ');
            log_message('info', 'Foreign key fk_members_approved_by added successfully');
        } catch (\Exception $e) {
            log_message('warning', 'Foreign key fk_members_approved_by might already exist: ' . $e->getMessage());
        }

        // 4. sp_members.rejected_by → sp_members.id
        // Admin who rejected the member
        try {
            $this->db->query('
                ALTER TABLE sp_members
                ADD CONSTRAINT fk_members_rejected_by
                FOREIGN KEY (rejected_by) REFERENCES sp_members(id)
                ON DELETE SET NULL
                ON UPDATE CASCADE
            ');
            log_message('info', 'Foreign key fk_members_rejected_by added successfully');
        } catch (\Exception $e) {
            log_message('warning', 'Foreign key fk_members_rejected_by might already exist: ' . $e->getMessage());
        }
    }

    public function down()
    {
        // Drop foreign keys in reverse order
        try {
            $this->db->query('ALTER TABLE sp_members DROP FOREIGN KEY fk_members_rejected_by');
        } catch (\Exception $e) {
            log_message('info', 'Foreign key fk_members_rejected_by might not exist: ' . $e->getMessage());
        }

        try {
            $this->db->query('ALTER TABLE sp_members DROP FOREIGN KEY fk_members_approved_by');
        } catch (\Exception $e) {
            log_message('info', 'Foreign key fk_members_approved_by might not exist: ' . $e->getMessage());
        }

        try {
            $this->db->query('ALTER TABLE sp_members DROP FOREIGN KEY fk_members_registration_verified_by');
        } catch (\Exception $e) {
            log_message('info', 'Foreign key fk_members_registration_verified_by might not exist: ' . $e->getMessage());
        }

        try {
            $this->db->query('ALTER TABLE sp_dues_payments DROP FOREIGN KEY fk_dues_payments_verified_by');
        } catch (\Exception $e) {
            log_message('info', 'Foreign key fk_dues_payments_verified_by might not exist: ' . $e->getMessage());
        }
    }
}
