<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\CmsPageModel;
use App\Models\CmsOfficerModel;

class PageController extends BaseController
{
    protected $pageModel;
    protected $officerModel;

    public function __construct()
    {
        $this->pageModel = new CmsPageModel();
        $this->officerModel = new CmsOfficerModel();
    }

    /**
     * Generic page display by slug
     */
    public function show($slug)
    {
        $page = $this->pageModel->getPageBySlug($slug);

        if (!$page) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Halaman '$slug' tidak ditemukan.");
        }

        // Check if member-only page
        if ($page['visibility'] === 'member_only' && !session()->has('member_id')) {
            return redirect()->to('/login')
                           ->with('error', 'Halaman ini hanya dapat diakses oleh anggota. Silakan login terlebih dahulu.');
        }

        $data = [
            'title' => $page['title'] . ' - Serikat Pekerja Kampus',
            'meta_description' => strip_tags(substr($page['content_html'], 0, 160)),
            'page' => $page,
        ];

        // Select template based on page template setting
        $template = match($page['template']) {
            'legal' => 'public/page_legal',
            'contact' => 'public/page_contact',
            default => 'public/page_default',
        };

        return view($template, $data);
    }

    /**
     * Sejarah Page - Shortcut
     */
    public function sejarah()
    {
        return $this->show('sejarah');
    }

    /**
     * Manifesto Page - Shortcut
     */
    public function manifesto()
    {
        return $this->show('manifesto');
    }

    /**
     * Visi Misi Page - Shortcut
     */
    public function visimisi()
    {
        return $this->show('visi-misi');
    }

    /**
     * AD/ART Page - Shortcut
     */
    public function adart()
    {
        return $this->show('ad-art');
    }

    /**
     * Struktur Pengurus Page
     */
    public function pengurus()
    {
        // Get active officers by level
        $officersPusat = $this->officerModel->getActiveOfficers('pusat');
        $officersWilayah = $this->officerModel->getActiveOfficers('wilayah');

        // Group regional officers by region_code
        $wilayahGrouped = [];
        foreach ($officersWilayah as $officer) {
            $region = $officer['region_code'] ?? 'Lainnya';
            if (!isset($wilayahGrouped[$region])) {
                $wilayahGrouped[$region] = [];
            }
            $wilayahGrouped[$region][] = $officer;
        }

        $data = [
            'title' => 'Struktur Pengurus - Serikat Pekerja Kampus',
            'meta_description' => 'Struktur kepengurusan Serikat Pekerja Kampus (SPK) tingkat pusat dan wilayah',
            'officers_pusat' => $officersPusat,
            'officers_wilayah_grouped' => $wilayahGrouped,
        ];

        return view('public/pengurus', $data);
    }

    /**
     * Tentang SPK - About Page
     */
    public function tentang()
    {
        return $this->show('tentang-spk');
    }

    /**
     * Kontak Page
     */
    public function kontak()
    {
        // Check if there's a contact page in CMS
        $page = $this->pageModel->getPageBySlug('kontak');

        $data = [
            'title' => 'Hubungi Kami - Serikat Pekerja Kampus',
            'meta_description' => 'Hubungi Serikat Pekerja Kampus (SPK) - Form kontak, alamat, dan informasi kontak',
            'page' => $page,
        ];

        return view('public/contact', $data);
    }
}
