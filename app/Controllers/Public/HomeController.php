<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\CmsHomeSectionModel;
use App\Models\CmsNewsPostModel;
use App\Models\CmsDocumentModel;
use App\Models\CmsOfficerModel;
use App\Models\MemberModel;
use App\Models\CmsSubscriberModel;

class HomeController extends BaseController
{
    protected $homeSectionModel;
    protected $newsModel;
    protected $documentModel;
    protected $officerModel;
    protected $memberModel;
    protected $subscriberModel;

    public function __construct()
    {
        $this->homeSectionModel = new CmsHomeSectionModel();
        $this->newsModel = new CmsNewsPostModel();
        $this->documentModel = new CmsDocumentModel();
        $this->officerModel = new CmsOfficerModel();
        $this->memberModel = new MemberModel();
        $this->subscriberModel = new CmsSubscriberModel();
    }

    /**
     * Landing Page
     */
    public function index()
    {
        // Get enabled home sections
        $sections = $this->homeSectionModel->getEnabledSections();

        // Get latest news
        $latestNews = $this->newsModel->getLatestPosts(3);

        // Get latest publications
        $latestPublications = $this->documentModel->getLatestPublications(6);

        // Get active officers (pusat)
        $officers = $this->officerModel->getActiveOfficers('pusat');

        // Get statistics
        $stats = [
            'total_members' => $this->memberModel->where('membership_status', 'active')->countAllResults(),
            'total_regions' => $this->memberModel->select('region_code')
                                                 ->distinct()
                                                 ->where('membership_status', 'active')
                                                 ->where('region_code IS NOT NULL')
                                                 ->countAllResults(),
            'total_universities' => $this->memberModel->select('university_name')
                                                      ->distinct()
                                                      ->where('membership_status', 'active')
                                                      ->countAllResults(),
            'total_subscribers' => $this->subscriberModel->getActiveCount(),
        ];

        $data = [
            'title' => 'Beranda - Serikat Pekerja Kampus',
            'meta_description' => 'Serikat Pekerja Kampus (SPK) - Organisasi yang memperjuangkan hak dan kesejahteraan pekerja di perguruan tinggi',
            'sections' => $sections,
            'latest_news' => $latestNews,
            'latest_publications' => $latestPublications,
            'officers' => $officers,
            'stats' => $stats,
        ];

        return view('public/home', $data);
    }

    /**
     * Newsletter Subscription
     */
    public function subscribe()
    {
        // Only accept POST requests
        if (!$this->request->is('post')) {
            return redirect()->to('/');
        }

        $email = $this->request->getPost('email');

        // Validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => 'required|valid_email'
        ]);

        if (!$validation->run(['email' => $email])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email tidak valid'
            ]);
        }

        // Check if already subscribed
        $existing = $this->subscriberModel->where('email', $email)->first();
        if ($existing) {
            if ($existing['status'] === 'active') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email sudah terdaftar sebagai subscriber aktif'
                ]);
            } elseif ($existing['status'] === 'pending') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email sudah terdaftar, silakan cek inbox untuk verifikasi'
                ]);
            }
        }

        // Generate verification token
        $token = bin2hex(random_bytes(32));

        // Insert subscriber
        try {
            $this->subscriberModel->subscribe($email, $token);

            // TODO: Send verification email
            // $emailService = new \App\Libraries\EmailService();
            // $emailService->sendSubscriberVerification($email, $token);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Terima kasih! Silakan cek email Anda untuk verifikasi.'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Newsletter subscription error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ]);
        }
    }

    /**
     * Verify Newsletter Subscription
     */
    public function verifySubscription($token)
    {
        if ($this->subscriberModel->verifyByToken($token)) {
            return redirect()->to('/')
                           ->with('success', 'Email Anda berhasil diverifikasi. Terima kasih telah berlangganan!');
        }

        return redirect()->to('/')
                       ->with('error', 'Token verifikasi tidak valid atau sudah kadaluarsa.');
    }

    /**
     * Unsubscribe from Newsletter
     */
    public function unsubscribe()
    {
        $email = $this->request->getGet('email');

        if (!$email) {
            return redirect()->to('/')
                           ->with('error', 'Email tidak ditemukan.');
        }

        if ($this->subscriberModel->unsubscribe($email)) {
            return redirect()->to('/')
                           ->with('success', 'Anda telah berhenti berlangganan newsletter.');
        }

        return redirect()->to('/')
                       ->with('error', 'Email tidak ditemukan dalam daftar subscriber.');
    }
}
