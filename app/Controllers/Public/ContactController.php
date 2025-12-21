<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\CmsContactMessageModel;

class ContactController extends BaseController
{
    protected $contactModel;

    public function __construct()
    {
        $this->contactModel = new CmsContactMessageModel();
    }

    /**
     * Contact Form Page
     */
    public function index()
    {
        $data = [
            'title' => 'Hubungi Kami - Serikat Pekerja Kampus',
            'meta_description' => 'Hubungi Serikat Pekerja Kampus (SPK) melalui form kontak atau informasi kontak yang tersedia',
        ];

        return view('public/contact', $data);
    }

    /**
     * Submit Contact Form
     */
    public function submit()
    {
        // Only accept POST requests
        if (!$this->request->is('post')) {
            return redirect()->to('/contact');
        }

        // Validation rules
        $rules = [
            'name' => 'required|min_length[3]|max_length[150]',
            'email' => 'required|valid_email',
            'subject' => 'permit_empty|max_length[255]',
            'message' => 'required|min_length[10]',
        ];

        $validationMessages = [
            'name' => [
                'required' => 'Nama wajib diisi.',
                'min_length' => 'Nama minimal 3 karakter.',
                'max_length' => 'Nama maksimal 150 karakter.',
            ],
            'email' => [
                'required' => 'Email wajib diisi.',
                'valid_email' => 'Format email tidak valid.',
            ],
            'message' => [
                'required' => 'Pesan wajib diisi.',
                'min_length' => 'Pesan minimal 10 karakter.',
            ],
        ];

        if (!$this->validate($rules, $validationMessages)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        // Prepare data
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'subject' => $this->request->getPost('subject'),
            'message' => $this->request->getPost('message'),
            'status' => 'new',
        ];

        try {
            // Insert contact message
            $this->contactModel->insert($data);

            // TODO: Send email notification to admin
            // $emailService = new \App\Libraries\EmailService();
            // $emailService->sendContactNotification($data);

            // TODO: Send auto-reply to sender
            // $emailService->sendContactAutoReply($data['email'], $data['name']);

            return redirect()->to('/contact')
                           ->with('success', 'Terima kasih! Pesan Anda telah terkirim. Tim kami akan segera merespons.');
        } catch (\Exception $e) {
            log_message('error', 'Contact form submission error: ' . $e->getMessage());

            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.');
        }
    }

    /**
     * AJAX Submit for better UX
     */
    public function ajaxSubmit()
    {
        // Only accept AJAX POST requests
        if (!$this->request->isAJAX() || !$this->request->is('post')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }

        // Validation
        $rules = [
            'name' => 'required|min_length[3]|max_length[150]',
            'email' => 'required|valid_email',
            'subject' => 'permit_empty|max_length[255]',
            'message' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(422);
        }

        // Prepare data
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'subject' => $this->request->getPost('subject'),
            'message' => $this->request->getPost('message'),
            'status' => 'new',
        ];

        try {
            // Insert contact message
            $messageId = $this->contactModel->insert($data);

            // TODO: Send email notifications

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pesan berhasil dikirim. Terima kasih!',
                'message_id' => $messageId
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Contact form AJAX submission error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get contact information (for API or AJAX)
     */
    public function getContactInfo()
    {
        // This could be from system settings or hardcoded
        $contactInfo = [
            'email' => 'info@serikatpekerkakampus.org',
            'phone' => '+62 21 1234 5678',
            'whatsapp' => '+62 812 3456 7890',
            'address' => 'Jl. Example No. 123, Jakarta 12345',
            'office_hours' => 'Senin - Jumat, 09:00 - 17:00 WIB',
            'social_media' => [
                'facebook' => 'https://facebook.com/serikatpekerkakampus',
                'twitter' => 'https://twitter.com/spk_id',
                'instagram' => 'https://instagram.com/spk_id',
                'youtube' => 'https://youtube.com/@spk_id',
            ],
        ];

        return $this->response->setJSON($contactInfo);
    }
}
