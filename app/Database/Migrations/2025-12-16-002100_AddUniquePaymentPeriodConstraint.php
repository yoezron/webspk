<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniquePaymentPeriodConstraint extends Migration
{
    public function up()
    {
        // Add unique constraint to prevent duplicate payments for same period
        // This ensures atomicity at database level (prevents race conditions)
        $this->forge->addUniqueKey(
            ['member_id', 'payment_type', 'payment_month', 'payment_year'],
            'unique_payment_period'
        );

        $this->db->query(
            'ALTER TABLE sp_dues_payments
            ADD CONSTRAINT unique_payment_period
            UNIQUE KEY (member_id, payment_type, payment_month, payment_year)'
        );
    }

    public function down()
    {
        // Remove unique constraint
        if ($this->db->DBDriver === 'MySQLi') {
            $this->db->query('ALTER TABLE sp_dues_payments DROP INDEX unique_payment_period');
        }
    }
}
