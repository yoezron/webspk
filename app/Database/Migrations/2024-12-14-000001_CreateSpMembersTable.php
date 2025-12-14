<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSpMembersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'member_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],

            // Authentication
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'password_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['super_admin', 'admin', 'coordinator', 'treasurer', 'member', 'candidate'],
                'default' => 'candidate',
            ],
            'membership_status' => [
                'type' => 'ENUM',
                'constraint' => ['candidate', 'active', 'inactive', 'suspended', 'terminated'],
                'default' => 'candidate',
            ],
            'onboarding_state' => [
                'type' => 'ENUM',
                'constraint' => ['registered', 'email_verified', 'profile_completed', 'payment_submitted', 'approved', 'rejected'],
                'default' => 'registered',
            ],
            'account_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'active', 'suspended', 'rejected'],
                'default' => 'pending',
            ],

            // Security & Login
            'email_verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_login_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_login_ip' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'last_user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'failed_login_attempts' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'locked_until' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'password_changed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'reset_token_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'reset_token_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'remember_token_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],

            // Personal Data
            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'gender' => [
                'type' => 'ENUM',
                'constraint' => ['L', 'P'],
                'null' => true,
            ],
            'birth_place' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'birth_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'identity_number' => [
                'type' => 'VARCHAR',
                'constraint' => 16,
                'null' => true,
            ],
            'phone_number' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
            ],
            'alt_phone_number' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,
            ],

            // Address
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'province' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'district' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'postal_code' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
            ],
            'region_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],

            // Emergency Contact
            'emergency_contact_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'emergency_contact_relation' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'emergency_contact_phone' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => true,
            ],

            // Work Data
            'university_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'campus_location' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'faculty' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'department' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'work_unit' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'employee_id_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'lecturer_id_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'academic_rank' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'employment_status' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'work_start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],

            // Salary & Dues
            'gross_salary' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'salary_range' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'functional_allowance' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'structural_allowance' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'other_allowances' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'dues_rate_type' => [
                'type' => 'ENUM',
                'constraint' => ['golongan', 'gaji'],
                'null' => true,
            ],
            'dues_rate_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'monthly_dues_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],

            // Banking
            'bank_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'bank_account_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'bank_account_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'npwp_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'bpjs_tk_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'bpjs_kes_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],

            // Education
            'education_level' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'graduation_year' => [
                'type' => 'YEAR',
                'null' => true,
            ],
            'institution_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
            ],
            'field_of_study' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'certifications' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'languages_spoken' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'skills' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // Registration & Approval
            'registration_payment_proof' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'registration_payment_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'registration_verified_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'registration_verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'registration_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'approval_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'rejection_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'rejected_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],

            // Dues Tracking
            'last_dues_payment_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'total_arrears' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'arrears_months' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],

            // Documents
            'profile_photo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'id_card_photo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'family_card_photo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'sk_pengangkatan_photo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],

            // Agreements
            'agreement_accepted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'privacy_accepted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            // Notes
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // Timestamps
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('uuid');
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('member_number');
        $this->forge->addKey('membership_status');
        $this->forge->addKey('account_status');
        $this->forge->createTable('sp_members');
    }

    public function down()
    {
        $this->forge->dropTable('sp_members');
    }
}
