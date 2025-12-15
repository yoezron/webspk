<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\MemberModel;
use App\Models\DuesPaymentModel;
use App\Libraries\EmailService;

/**
 * Check and enforce member arrears
 * Run this command via cron job daily/weekly
 *
 * Usage: php spark arrears:check
 */
class CheckArrears extends BaseCommand
{
    protected $group = 'Membership';
    protected $name = 'arrears:check';
    protected $description = 'Check member arrears and enforce suspension for members with 3+ months arrears';

    public function run(array $params)
    {
        CLI::write('Starting arrears check...', 'yellow');

        $memberModel = new MemberModel();
        $paymentModel = new DuesPaymentModel();
        $emailService = new EmailService();

        // Get all active members
        $activeMembers = $memberModel->where('status', 'active')
            ->where('onboarding_state', 'approved')
            ->findAll();

        CLI::write('Found ' . count($activeMembers) . ' active members to check', 'green');

        $suspendedCount = 0;
        $warningCount = 0;
        $currentDate = new \DateTime();

        foreach ($activeMembers as $member) {
            // Calculate arrears
            $arrears = $this->calculateArrears($member, $paymentModel, $currentDate);

            if ($arrears['months'] >= 3) {
                // Suspend member
                $this->suspendMember($member, $arrears, $memberModel);
                $suspendedCount++;

                CLI::write("Suspended: {$member['full_name']} ({$member['member_number']}) - {$arrears['months']} months arrears", 'red');

                // Send suspension notification
                try {
                    $this->sendSuspensionNotification($member, $arrears, $emailService);
                } catch (\Exception $e) {
                    CLI::write("Failed to send email to {$member['email']}: " . $e->getMessage(), 'red');
                }

            } elseif ($arrears['months'] >= 2) {
                // Warning for 2 months arrears
                $this->updateArrears($member['id'], $arrears, $memberModel);
                $warningCount++;

                CLI::write("Warning: {$member['full_name']} ({$member['member_number']}) - {$arrears['months']} months arrears", 'yellow');

                // Send warning notification
                try {
                    $this->sendWarningNotification($member, $arrears, $emailService);
                } catch (\Exception $e) {
                    CLI::write("Failed to send email to {$member['email']}: " . $e->getMessage(), 'red');
                }

            } elseif ($arrears['months'] > 0) {
                // Just update arrears info
                $this->updateArrears($member['id'], $arrears, $memberModel);
            }
        }

        CLI::write('Arrears check completed!', 'green');
        CLI::write("Suspended: {$suspendedCount} members", 'red');
        CLI::write("Warned: {$warningCount} members", 'yellow');
    }

    /**
     * Calculate member arrears
     */
    private function calculateArrears(array $member, DuesPaymentModel $paymentModel, \DateTime $currentDate): array
    {
        // Get member registration date
        $registrationDate = new \DateTime($member['created_at']);
        $monthlyDues = $member['dues_amount'] ?? 0;

        // If no dues amount set, calculate from rate
        if ($monthlyDues == 0) {
            $monthlyDues = 20000; // Default minimum
        }

        // Calculate months since registration
        $interval = $registrationDate->diff($currentDate);
        $monthsSinceRegistration = ($interval->y * 12) + $interval->m;

        // Get total paid months
        $totalPaid = $paymentModel->where('member_id', $member['id'])
            ->where('status', 'verified')
            ->where('payment_type', 'monthly_dues')
            ->countAllResults();

        // Calculate arrears
        $monthsInArrears = max(0, $monthsSinceRegistration - $totalPaid);
        $totalArrears = $monthsInArrears * $monthlyDues;

        // Get last payment date
        $lastPayment = $paymentModel->where('member_id', $member['id'])
            ->where('status', 'verified')
            ->orderBy('payment_date', 'DESC')
            ->first();

        $lastPaymentDate = $lastPayment ? $lastPayment['payment_date'] : null;

        return [
            'months' => $monthsInArrears,
            'amount' => $totalArrears,
            'monthly_dues' => $monthlyDues,
            'last_payment_date' => $lastPaymentDate,
            'months_since_registration' => $monthsSinceRegistration,
            'total_paid' => $totalPaid,
        ];
    }

    /**
     * Suspend member due to arrears
     */
    private function suspendMember(array $member, array $arrears, MemberModel $memberModel): void
    {
        $memberModel->update($member['id'], [
            'status' => 'suspended',
            'arrears_months' => $arrears['months'],
            'arrears_amount' => $arrears['amount'],
            'suspension_reason' => "Tunggakan iuran {$arrears['months']} bulan (Rp " . number_format($arrears['amount'], 0, ',', '.') . ")",
            'suspended_at' => date('Y-m-d H:i:s'),
        ]);

        log_message('info', "Member suspended due to arrears: {$member['member_number']} - {$arrears['months']} months");
    }

    /**
     * Update member arrears info
     */
    private function updateArrears(int $memberId, array $arrears, MemberModel $memberModel): void
    {
        $memberModel->update($memberId, [
            'arrears_months' => $arrears['months'],
            'arrears_amount' => $arrears['amount'],
        ]);
    }

    /**
     * Send suspension notification email
     */
    private function sendSuspensionNotification(array $member, array $arrears, EmailService $emailService): void
    {
        helper('app');

        $subject = 'Pemberitahuan Pembekuan Keanggotaan - Tunggakan Iuran';
        $message = view('emails/member_suspension', [
            'name' => $member['full_name'],
            'member_number' => $member['member_number'],
            'arrears_months' => $arrears['months'],
            'arrears_amount' => format_currency($arrears['amount']),
            'monthly_dues' => format_currency($arrears['monthly_dues']),
            'last_payment_date' => $arrears['last_payment_date'] ? date('d F Y', strtotime($arrears['last_payment_date'])) : 'Belum ada pembayaran',
            'contact_url' => base_url('kontak'),
            'payment_url' => base_url('member/payment/submit'),
        ]);

        // Note: This is a simplified version. The actual EmailService method needs to be added.
        // For now, we'll use a generic send method or extend EmailService
        log_message('info', "Suspension notification prepared for: {$member['email']}");
    }

    /**
     * Send warning notification email
     */
    private function sendWarningNotification(array $member, array $arrears, EmailService $emailService): void
    {
        helper('app');

        $subject = 'Peringatan Tunggakan Iuran - Serikat Pekerja Kampus';
        $message = view('emails/arrears_warning', [
            'name' => $member['full_name'],
            'member_number' => $member['member_number'],
            'arrears_months' => $arrears['months'],
            'arrears_amount' => format_currency($arrears['amount']),
            'monthly_dues' => format_currency($arrears['monthly_dues']),
            'last_payment_date' => $arrears['last_payment_date'] ? date('d F Y', strtotime($arrears['last_payment_date'])) : 'Belum ada pembayaran',
            'payment_url' => base_url('member/payment/submit'),
        ]);

        log_message('info', "Warning notification prepared for: {$member['email']}");
    }
}
