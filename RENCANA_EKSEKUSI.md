# RENCANA EKSEKUSI FITUR BELUM TERIMPLEMENTASI
**Proyek:** Web Serikat Pekerja Kampus (SPK)
**Tanggal:** 20 Desember 2025
**Status:** Roadmap untuk Sprint 4, 5, 6, 7 (partial), 8

---

## OVERVIEW EKSEKUSI

Dokumen ini berisi rencana detail eksekusi untuk fitur-fitur yang belum terimplementasi berdasarkan analisis ANALISIS_IMPLEMENTASI.md.

**Total Remaining Story Points:** 210/381 (55%)
**Estimated Timeline:** 11-15 weeks
**Target Completion:** Maret 2026

---

## PHASE 1: CRITICAL FOUNDATIONS (P0)
**Duration:** 3-4 weeks
**Story Points:** 57
**Sprint:** 4 (CMS) + 7 (Bulk Import)

### Objective:
Membangun halaman publik dan sistem import data untuk migrasi 1700+ anggota existing.

---

### TASK 1.1: CMS Database Schema (Sprint 4 - S4-01)
**Priority:** P0 | **Points:** 5 | **Duration:** 1 day

#### Deliverables:
- [x] Migration file: `app/Database/Migrations/2025-12-21-000001_CreateCMSTables.php`

#### Detailed Steps:

**Step 1: Create Migration File**
```bash
php spark make:migration CreateCMSTables
```

**Step 2: Define Tables**
Tables to create:
1. `cms_pages` - Halaman statis (sejarah, manifesto, visi-misi, ad-art, dll)
2. `cms_page_revisions` - History perubahan halaman
3. `cms_home_sections` - Section landing page (about, stats, publications, cta, footer)
4. `cms_documents` - Publikasi & Regulasi PDF
5. `cms_document_categories` - Kategori dokumen
6. `cms_news_posts` - Berita/Blog
7. `cms_media` - Media library (gambar, file)
8. `cms_officers` - Struktur pengurus
9. `cms_subscribers` - Newsletter subscribers
10. `cms_contact_messages` - Inbox contact form

**Schema Reference:** Lihat Panduan_Pengembangan_Web_SPK.md Section 3.4

**Step 3: Run Migration**
```bash
php spark migrate
```

**Acceptance Criteria:**
- ✅ All 10 CMS tables created successfully
- ✅ Foreign keys properly defined
- ✅ Indexes on commonly queried fields
- ✅ No migration errors

---

### TASK 1.2: CMS Models (Sprint 4)
**Priority:** P0 | **Points:** 3 | **Duration:** 0.5 day

#### Files to Create:
1. `app/Models/CmsPageModel.php`
2. `app/Models/CmsPageRevisionModel.php`
3. `app/Models/CmsHomeSectionModel.php`
4. `app/Models/CmsDocumentModel.php`
5. `app/Models/CmsDocumentCategoryModel.php`
6. `app/Models/CmsNewsPostModel.php`
7. `app/Models/CmsMediaModel.php`
8. `app/Models/CmsOfficerModel.php`
9. `app/Models/CmsSubscriberModel.php`
10. `app/Models/CmsContactMessageModel.php`

#### Implementation Template:
```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class CmsPageModel extends Model
{
    protected $table = 'cms_pages';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['slug', 'title', 'content_html', 'template', 'status', 'visibility', 'published_at', 'created_by', 'updated_by'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'slug' => 'required|min_length[3]|max_length[100]|is_unique[cms_pages.slug,id,{id}]',
        'title' => 'required|min_length[3]|max_length[200]',
        'status' => 'required|in_list[draft,published,archived]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    // Helper methods
    public function getPublishedPages()
    {
        return $this->where('status', 'published')
                    ->where('published_at <=', date('Y-m-d H:i:s'))
                    ->orderBy('title', 'ASC')
                    ->findAll();
    }

    public function getPageBySlug($slug)
    {
        return $this->where('slug', $slug)
                    ->where('status', 'published')
                    ->first();
    }
}
```

**Acceptance Criteria:**
- ✅ All models created with proper validation rules
- ✅ Helper methods for common queries
- ✅ Relationships defined where needed

---

### TASK 1.3: Public Controllers (Sprint 4 - S4-02, S4-03, S4-07, S4-08)
**Priority:** P0 | **Points:** 21 | **Duration:** 3-4 days

#### Files to Create:
1. `app/Controllers/Public/HomeController.php` - Landing page
2. `app/Controllers/Public/PageController.php` - Static pages
3. `app/Controllers/Public/NewsController.php` - News/blog
4. `app/Controllers/Public/DocumentController.php` - Downloads
5. `app/Controllers/Public/ContactController.php` - Contact form

#### Implementation Details:

**1. HomeController.php (Landing Page Dinamis)**
```php
<?php
namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\CmsHomeSectionModel;
use App\Models\CmsNewsPostModel;
use App\Models\CmsDocumentModel;
use App\Models\CmsOfficerModel;
use App\Models\MemberModel;

class HomeController extends BaseController
{
    public function index()
    {
        $homeSectionModel = new CmsHomeSectionModel();
        $newsModel = new CmsNewsPostModel();
        $documentModel = new CmsDocumentModel();
        $officerModel = new CmsOfficerModel();
        $memberModel = new MemberModel();

        $data = [
            'title' => 'Beranda - Serikat Pekerja Kampus',
            'sections' => $homeSectionModel->getEnabledSections(),
            'latest_news' => $newsModel->getLatestPosts(3),
            'latest_publications' => $documentModel->getLatestPublications(6),
            'officers' => $officerModel->getActiveOfficers('pusat'),
            'stats' => [
                'total_members' => $memberModel->where('membership_status', 'active')->countAllResults(),
                'total_regions' => $memberModel->select('region_code')->distinct()->countAllResults(),
                'total_universities' => $memberModel->select('university_name')->distinct()->countAllResults(),
            ]
        ];

        return view('public/home', $data);
    }

    public function subscribe()
    {
        $subscriberModel = new \App\Models\CmsSubscriberModel();

        $email = $this->request->getPost('email');

        // Validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email tidak valid'
            ]);
        }

        // Check existing
        if ($subscriberModel->where('email', $email)->first()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email sudah terdaftar'
            ]);
        }

        // Generate token
        $token = bin2hex(random_bytes(32));

        // Insert
        $subscriberModel->insert([
            'email' => $email,
            'status' => 'pending',
            'token_hash' => hash('sha256', $token),
        ]);

        // Send verification email (TODO: implement EmailService)

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Terima kasih! Silakan cek email untuk verifikasi.'
        ]);
    }
}
```

**2. PageController.php (Static Pages)**
```php
<?php
namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\CmsPageModel;

class PageController extends BaseController
{
    protected $pageModel;

    public function __construct()
    {
        $this->pageModel = new CmsPageModel();
    }

    public function show($slug)
    {
        $page = $this->pageModel->getPageBySlug($slug);

        if (!$page) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => $page['title'] . ' - SPK',
            'page' => $page
        ];

        return view('public/page', $data);
    }

    // Shortcut methods for commonly accessed pages
    public function sejarah()
    {
        return $this->show('sejarah');
    }

    public function manifesto()
    {
        return $this->show('manifesto');
    }

    public function visimisi()
    {
        return $this->show('visi-misi');
    }

    public function adart()
    {
        return $this->show('ad-art');
    }

    public function pengurus()
    {
        $officerModel = new \App\Models\CmsOfficerModel();

        $data = [
            'title' => 'Struktur Pengurus - SPK',
            'officers_pusat' => $officerModel->getActiveOfficers('pusat'),
            'officers_wilayah' => $officerModel->getActiveOfficers('wilayah'),
        ];

        return view('public/pengurus', $data);
    }
}
```

**3. NewsController.php (News/Blog)**
```php
<?php
namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\CmsNewsPostModel;

class NewsController extends BaseController
{
    protected $newsModel;
    protected $perPage = 12;

    public function __construct()
    {
        $this->newsModel = new CmsNewsPostModel();
    }

    public function index()
    {
        $posts = $this->newsModel->getPublishedPosts($this->perPage);

        $data = [
            'title' => 'Berita - SPK',
            'posts' => $posts,
            'pager' => $this->newsModel->pager,
        ];

        return view('public/news/index', $data);
    }

    public function show($slug)
    {
        $post = $this->newsModel->getPostBySlug($slug);

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Increment view count
        $this->newsModel->incrementViewCount($post['id']);

        $data = [
            'title' => $post['title'] . ' - Berita SPK',
            'post' => $post,
            'related' => $this->newsModel->getRelatedPosts($post['id'], 3),
        ];

        return view('public/news/show', $data);
    }
}
```

**4. DocumentController.php (Publikasi & Regulasi)**
```php
<?php
namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\CmsDocumentModel;
use App\Models\CmsDocumentCategoryModel;

class DocumentController extends BaseController
{
    protected $documentModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->documentModel = new CmsDocumentModel();
        $this->categoryModel = new CmsDocumentCategoryModel();
    }

    public function publikasi()
    {
        $documents = $this->documentModel->getPublishedDocuments('publikasi');
        $categories = $this->categoryModel->getCategories('publikasi');

        $data = [
            'title' => 'Publikasi - SPK',
            'doc_type' => 'publikasi',
            'documents' => $documents,
            'categories' => $categories,
        ];

        return view('public/documents/index', $data);
    }

    public function regulasi()
    {
        $documents = $this->documentModel->getPublishedDocuments('regulasi');
        $categories = $this->categoryModel->getCategories('regulasi');

        $data = [
            'title' => 'Regulasi - SPK',
            'doc_type' => 'regulasi',
            'documents' => $documents,
            'categories' => $categories,
        ];

        return view('public/documents/index', $data);
    }

    public function download($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document || $document['status'] !== 'published') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Increment download count
        $this->documentModel->incrementDownloadCount($id);

        // Audit log
        $auditLog = new \App\Models\AuditLogModel();
        $auditLog->log([
            'actor_id' => session('member_id') ?? null,
            'actor_type' => session('member_id') ? 'member' : 'anonymous',
            'target_type' => 'document',
            'target_id' => $id,
            'action' => 'document.downloaded',
            'ip_address' => $this->request->getIPAddress(),
        ]);

        // Serve file
        $filepath = WRITEPATH . 'uploads/documents/' . $document['file_path'];

        if (!file_exists($filepath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response->download($filepath, null)
                              ->setFileName($document['original_name']);
    }
}
```

**5. ContactController.php (Contact Form)**
```php
<?php
namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\CmsContactMessageModel;

class ContactController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Hubungi Kami - SPK',
        ];

        return view('public/contact', $data);
    }

    public function submit()
    {
        $contactModel = new CmsContactMessageModel();

        $rules = [
            'name' => 'required|min_length[3]|max_length[150]',
            'email' => 'required|valid_email',
            'subject' => 'permit_empty|max_length[255]',
            'message' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'subject' => $this->request->getPost('subject'),
            'message' => $this->request->getPost('message'),
            'status' => 'new',
        ];

        $contactModel->insert($data);

        // Send notification to admin (TODO: EmailService)

        return redirect()->to('/contact')
                       ->with('success', 'Pesan Anda telah terkirim. Kami akan segera merespons.');
    }
}
```

**Routes to Add (app/Config/Routes.php):**
```php
// Public CMS Routes
$routes->get('/', 'Public\HomeController::index');
$routes->post('subscribe', 'Public\HomeController::subscribe');

$routes->get('sejarah', 'Public\PageController::sejarah');
$routes->get('manifesto', 'Public\PageController::manifesto');
$routes->get('visi-misi', 'Public\PageController::visimisi');
$routes->get('ad-art', 'Public\PageController::adart');
$routes->get('pengurus', 'Public\PageController::pengurus');

$routes->get('news', 'Public\NewsController::index');
$routes->get('news/(:segment)', 'Public\NewsController::show/$1');

$routes->get('publikasi', 'Public\DocumentController::publikasi');
$routes->get('regulasi', 'Public\DocumentController::regulasi');
$routes->get('documents/download/(:num)', 'Public\DocumentController::download/$1');

$routes->get('contact', 'Public\ContactController::index');
$routes->post('contact', 'Public\ContactController::submit');
```

**Acceptance Criteria:**
- ✅ Landing page accessible at `/`
- ✅ Static pages accessible (sejarah, manifesto, visi-misi, ad-art, pengurus)
- ✅ News listing and detail pages work
- ✅ Publikasi & Regulasi pages show documents
- ✅ Document download works with audit logging
- ✅ Contact form submits successfully
- ✅ Newsletter subscription works

---

### TASK 1.4: Admin CMS Controllers (Sprint 4 - S4-10)
**Priority:** P0 | **Points:** 8 | **Duration:** 2 days

#### Files to Create:
1. `app/Controllers/Admin/CMS/PageController.php`
2. `app/Controllers/Admin/CMS/NewsController.php`
3. `app/Controllers/Admin/CMS/DocumentController.php`
4. `app/Controllers/Admin/CMS/OfficerController.php`
5. `app/Controllers/Admin/CMS/LandingController.php`
6. `app/Controllers/Admin/CMS/MediaController.php`
7. `app/Controllers/Admin/CMS/SubscriberController.php`
8. `app/Controllers/Admin/CMS/ContactController.php`

#### Implementation Notes:
- Use RBAC filter: `['filter' => 'rbac:super_admin,admin']`
- Implement full CRUD operations
- Add rich text editor for content (TinyMCE or CKEditor)
- File upload handling for media & documents
- Audit logging for all changes

**Example: PageController.php**
```php
<?php
namespace App\Controllers\Admin\CMS;

use App\Controllers\BaseController;
use App\Models\CmsPageModel;
use App\Models\AuditLogModel;

class PageController extends BaseController
{
    protected $pageModel;
    protected $auditLog;

    public function __construct()
    {
        $this->pageModel = new CmsPageModel();
        $this->auditLog = new AuditLogModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Kelola Halaman - CMS',
            'pages' => $this->pageModel->orderBy('title', 'ASC')->findAll(),
        ];

        return view('admin/cms/pages/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processCreate();
        }

        $data = [
            'title' => 'Buat Halaman Baru - CMS',
        ];

        return view('admin/cms/pages/create', $data);
    }

    protected function processCreate()
    {
        $rules = $this->pageModel->getValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'slug' => $this->request->getPost('slug'),
            'title' => $this->request->getPost('title'),
            'content_html' => $this->request->getPost('content_html'),
            'template' => $this->request->getPost('template'),
            'status' => $this->request->getPost('status'),
            'visibility' => $this->request->getPost('visibility'),
            'created_by' => session('member_id'),
        ];

        if ($data['status'] === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        $id = $this->pageModel->insert($data);

        // Audit log
        $this->auditLog->log([
            'actor_id' => session('member_id'),
            'target_type' => 'cms_page',
            'target_id' => $id,
            'action' => 'cms.page.created',
            'new_values' => json_encode($data),
        ]);

        return redirect()->to('/admin/cms/pages')
                       ->with('success', 'Halaman berhasil dibuat.');
    }

    public function edit($id)
    {
        $page = $this->pageModel->find($id);

        if (!$page) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processEdit($id, $page);
        }

        $data = [
            'title' => 'Edit Halaman - CMS',
            'page' => $page,
        ];

        return view('admin/cms/pages/edit', $data);
    }

    protected function processEdit($id, $oldPage)
    {
        $rules = $this->pageModel->getValidationRules();
        $rules['slug'] = "required|min_length[3]|max_length[100]|is_unique[cms_pages.slug,id,{$id}]";

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'slug' => $this->request->getPost('slug'),
            'title' => $this->request->getPost('title'),
            'content_html' => $this->request->getPost('content_html'),
            'template' => $this->request->getPost('template'),
            'status' => $this->request->getPost('status'),
            'visibility' => $this->request->getPost('visibility'),
            'updated_by' => session('member_id'),
        ];

        if ($data['status'] === 'published' && !$oldPage['published_at']) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        $this->pageModel->update($id, $data);

        // Audit log
        $this->auditLog->log([
            'actor_id' => session('member_id'),
            'target_type' => 'cms_page',
            'target_id' => $id,
            'action' => 'cms.page.updated',
            'old_values' => json_encode($oldPage),
            'new_values' => json_encode($data),
        ]);

        return redirect()->to('/admin/cms/pages')
                       ->with('success', 'Halaman berhasil diupdate.');
    }

    public function delete($id)
    {
        $page = $this->pageModel->find($id);

        if (!$page) {
            return redirect()->back()
                           ->with('error', 'Halaman tidak ditemukan.');
        }

        $this->pageModel->delete($id);

        // Audit log
        $this->auditLog->log([
            'actor_id' => session('member_id'),
            'target_type' => 'cms_page',
            'target_id' => $id,
            'action' => 'cms.page.deleted',
            'old_values' => json_encode($page),
        ]);

        return redirect()->to('/admin/cms/pages')
                       ->with('success', 'Halaman berhasil dihapus.');
    }
}
```

**Routes to Add:**
```php
// Admin CMS Routes
$routes->group('admin/cms', ['filter' => 'rbac:super_admin,admin', 'namespace' => 'App\Controllers\Admin\CMS'], function($routes) {
    // Pages
    $routes->get('pages', 'PageController::index');
    $routes->get('pages/create', 'PageController::create');
    $routes->post('pages/create', 'PageController::create');
    $routes->get('pages/edit/(:num)', 'PageController::edit/$1');
    $routes->post('pages/edit/(:num)', 'PageController::edit/$1');
    $routes->post('pages/delete/(:num)', 'PageController::delete/$1');

    // News
    $routes->get('news', 'NewsController::index');
    $routes->get('news/create', 'NewsController::create');
    $routes->post('news/create', 'NewsController::create');
    $routes->get('news/edit/(:num)', 'NewsController::edit/$1');
    $routes->post('news/edit/(:num)', 'NewsController::edit/$1');
    $routes->post('news/delete/(:num)', 'NewsController::delete/$1');

    // Documents
    $routes->get('documents', 'DocumentController::index');
    $routes->get('documents/create', 'DocumentController::create');
    $routes->post('documents/create', 'DocumentController::create');
    $routes->get('documents/edit/(:num)', 'DocumentController::edit/$1');
    $routes->post('documents/edit/(:num)', 'DocumentController::edit/$1');
    $routes->post('documents/delete/(:num)', 'DocumentController::delete/$1');

    // Officers
    $routes->get('officers', 'OfficerController::index');
    $routes->get('officers/create', 'OfficerController::create');
    $routes->post('officers/create', 'OfficerController::create');
    $routes->get('officers/edit/(:num)', 'OfficerController::edit/$1');
    $routes->post('officers/edit/(:num)', 'OfficerController::edit/$1');
    $routes->post('officers/delete/(:num)', 'OfficerController::delete/$1');

    // Landing Page Builder
    $routes->get('landing', 'LandingController::index', ['filter' => 'rbac:super_admin']);
    $routes->post('landing/update', 'LandingController::update', ['filter' => 'rbac:super_admin']);

    // Media Library
    $routes->get('media', 'MediaController::index');
    $routes->post('media/upload', 'MediaController::upload');
    $routes->post('media/delete/(:num)', 'MediaController::delete/$1');

    // Subscribers
    $routes->get('subscribers', 'SubscriberController::index');
    $routes->post('subscribers/export', 'SubscriberController::export');

    // Contact Inbox
    $routes->get('contact', 'ContactController::index');
    $routes->get('contact/view/(:num)', 'ContactController::view/$1');
    $routes->post('contact/reply/(:num)', 'ContactController::reply/$1');
    $routes->post('contact/close/(:num)', 'ContactController::close/$1');
});
```

**Acceptance Criteria:**
- ✅ Admin can CRUD pages, news, documents, officers
- ✅ Landing page builder works (section management)
- ✅ Media library allows upload/delete images
- ✅ Contact inbox shows messages and allows replies
- ✅ All actions are audit logged

---

### TASK 1.5: CMS Views (Sprint 4)
**Priority:** P0 | **Points:** 8 | **Duration:** 2 days

#### Views to Create:

**Public Views:**
- `app/Views/public/home.php` - Landing page
- `app/Views/public/page.php` - Static page template
- `app/Views/public/pengurus.php` - Officers page
- `app/Views/public/news/index.php` - News listing
- `app/Views/public/news/show.php` - News detail
- `app/Views/public/documents/index.php` - Documents listing
- `app/Views/public/contact.php` - Contact form

**Admin Views:**
- `app/Views/admin/cms/pages/index.php`
- `app/Views/admin/cms/pages/create.php`
- `app/Views/admin/cms/pages/edit.php`
- `app/Views/admin/cms/news/index.php`
- `app/Views/admin/cms/news/create.php`
- `app/Views/admin/cms/news/edit.php`
- `app/Views/admin/cms/documents/index.php`
- `app/Views/admin/cms/documents/create.php`
- `app/Views/admin/cms/documents/edit.php`
- `app/Views/admin/cms/officers/index.php`
- `app/Views/admin/cms/landing/index.php`
- `app/Views/admin/cms/media/index.php`
- `app/Views/admin/cms/contact/index.php`

**Acceptance Criteria:**
- ✅ Public views use responsive layout
- ✅ Admin views use Neptune dashboard theme
- ✅ Forms have proper validation display
- ✅ Rich text editor integrated (TinyMCE/CKEditor)

---

### TASK 1.6: CMS Seeder (Sprint 4)
**Priority:** P1 | **Points:** 2 | **Duration:** 0.5 day

#### File to Create:
`app/Database/Seeds/CMSSeeder.php`

#### Content:
- Default pages (sejarah, manifesto, visi-misi, ad-art) dengan placeholder content
- Default landing page sections
- Sample news posts
- Document categories

**Acceptance Criteria:**
- ✅ Default content seeded successfully
- ✅ All pages accessible after seeding

---

### TASK 1.7: Bulk Import System (Sprint 7 - S7-03, S7-04)
**Priority:** P0 | **Points:** 13 | **Duration:** 3 days

#### Objective:
Memungkinkan import 1700+ anggota existing dari Excel ke database.

#### Files to Create:
1. `app/Controllers/Admin/BulkImportController.php`
2. `app/Libraries/ExcelImportService.php`
3. `app/Views/admin/bulk-import/index.php`
4. `app/Views/admin/bulk-import/mapping.php`
5. `app/Views/admin/bulk-import/preview.php`
6. `app/Views/admin/bulk-import/result.php`

#### Implementation:

**ExcelImportService.php (Using PhpSpreadsheet)**
```php
<?php
namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\MemberModel;
use App\Models\AuditLogModel;

class ExcelImportService
{
    protected $memberModel;
    protected $auditLog;
    protected $errors = [];
    protected $success = 0;
    protected $skipped = 0;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->auditLog = new AuditLogModel();
    }

    public function parseExcel($filepath)
    {
        $spreadsheet = IOFactory::load($filepath);
        $worksheet = $spreadsheet->getActiveSheet();

        $headers = [];
        $data = [];

        foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
            if ($rowIndex === 1) {
                // Parse headers
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                foreach ($cellIterator as $cell) {
                    $headers[] = $cell->getValue();
                }
            } else {
                // Parse data
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $rowData = [];
                foreach ($cellIterator as $colIndex => $cell) {
                    $rowData[$headers[$colIndex]] = $cell->getValue();
                }

                $data[] = $rowData;
            }
        }

        return [
            'headers' => $headers,
            'data' => $data,
        ];
    }

    public function importMembers($data, $mapping, $options = [])
    {
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($data as $index => $row) {
            try {
                $memberData = $this->mapRow($row, $mapping);

                // Validate
                if (!$this->validateMemberData($memberData)) {
                    $this->errors[] = [
                        'row' => $index + 2, // +2 for header + 0-index
                        'data' => $row,
                        'errors' => $this->memberModel->errors(),
                    ];
                    $this->skipped++;
                    continue;
                }

                // Check duplicate email
                if ($this->memberModel->where('email', $memberData['email'])->first()) {
                    if ($options['skip_duplicates']) {
                        $this->skipped++;
                        continue;
                    } else {
                        $this->errors[] = [
                            'row' => $index + 2,
                            'data' => $row,
                            'errors' => ['Email sudah terdaftar'],
                        ];
                        $this->skipped++;
                        continue;
                    }
                }

                // Set default values for imported members
                $memberData['uuid'] = \Ramsey\Uuid\Uuid::uuid4()->toString();
                $memberData['password_hash'] = password_hash('default123', PASSWORD_DEFAULT); // Must change on first login
                $memberData['role'] = $options['default_role'] ?? 'member';
                $memberData['membership_status'] = $options['default_status'] ?? 'pending';
                $memberData['account_status'] = 'active';

                // Insert
                $id = $this->memberModel->insert($memberData);

                if ($id) {
                    $this->success++;

                    // Audit log
                    $this->auditLog->log([
                        'actor_id' => session('member_id'),
                        'target_type' => 'member',
                        'target_id' => $id,
                        'action' => 'member.imported',
                        'new_values' => json_encode($memberData),
                    ]);
                } else {
                    $this->errors[] = [
                        'row' => $index + 2,
                        'data' => $row,
                        'errors' => $this->memberModel->errors(),
                    ];
                    $this->skipped++;
                }

            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'data' => $row,
                    'errors' => [$e->getMessage()],
                ];
                $this->skipped++;
            }
        }

        $db->transComplete();

        return [
            'success' => $this->success,
            'skipped' => $this->skipped,
            'errors' => $this->errors,
        ];
    }

    protected function mapRow($row, $mapping)
    {
        $data = [];

        foreach ($mapping as $dbField => $excelColumn) {
            if (isset($row[$excelColumn])) {
                $data[$dbField] = $row[$excelColumn];
            }
        }

        return $data;
    }

    protected function validateMemberData($data)
    {
        return $this->memberModel->validate($data);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
```

**BulkImportController.php**
```php
<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ExcelImportService;

class BulkImportController extends BaseController
{
    protected $importService;

    public function __construct()
    {
        $this->importService = new ExcelImportService();
    }

    public function index()
    {
        $data = [
            'title' => 'Bulk Import Anggota - Admin',
        ];

        return view('admin/bulk-import/index', $data);
    }

    public function upload()
    {
        $file = $this->request->getFile('excel_file');

        if (!$file->isValid()) {
            return redirect()->back()
                           ->with('error', 'File tidak valid');
        }

        // Validate file type
        $allowedMimes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return redirect()->back()
                           ->with('error', 'Hanya file Excel (.xls, .xlsx) yang diperbolehkan');
        }

        // Move file
        $filename = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads/imports/', $filename);

        $filepath = WRITEPATH . 'uploads/imports/' . $filename;

        // Parse Excel
        $parsed = $this->importService->parseExcel($filepath);

        // Store in session for mapping step
        session()->set('import_data', [
            'filepath' => $filepath,
            'headers' => $parsed['headers'],
            'data' => $parsed['data'],
        ]);

        return redirect()->to('/admin/bulk-import/mapping');
    }

    public function mapping()
    {
        $importData = session('import_data');

        if (!$importData) {
            return redirect()->to('/admin/bulk-import')
                           ->with('error', 'Tidak ada data import');
        }

        $data = [
            'title' => 'Mapping Kolom - Bulk Import',
            'headers' => $importData['headers'],
            'db_fields' => $this->getRequiredFields(),
        ];

        return view('admin/bulk-import/mapping', $data);
    }

    public function preview()
    {
        $mapping = $this->request->getPost('mapping');
        $importData = session('import_data');

        if (!$importData) {
            return redirect()->to('/admin/bulk-import')
                           ->with('error', 'Tidak ada data import');
        }

        // Store mapping in session
        session()->set('import_mapping', $mapping);

        // Preview first 10 rows
        $preview = array_slice($importData['data'], 0, 10);

        $data = [
            'title' => 'Preview Data - Bulk Import',
            'preview' => $preview,
            'mapping' => $mapping,
            'total_rows' => count($importData['data']),
        ];

        return view('admin/bulk-import/preview', $data);
    }

    public function process()
    {
        $importData = session('import_data');
        $mapping = session('import_mapping');

        if (!$importData || !$mapping) {
            return redirect()->to('/admin/bulk-import')
                           ->with('error', 'Tidak ada data import');
        }

        $options = [
            'skip_duplicates' => $this->request->getPost('skip_duplicates') === '1',
            'default_role' => $this->request->getPost('default_role') ?? 'member',
            'default_status' => $this->request->getPost('default_status') ?? 'pending',
        ];

        // Process import
        $result = $this->importService->importMembers($importData['data'], $mapping, $options);

        // Clean up
        @unlink($importData['filepath']);
        session()->remove('import_data');
        session()->remove('import_mapping');

        $data = [
            'title' => 'Hasil Import - Bulk Import',
            'result' => $result,
        ];

        return view('admin/bulk-import/result', $data);
    }

    protected function getRequiredFields()
    {
        return [
            'email' => 'Email (Wajib)',
            'full_name' => 'Nama Lengkap (Wajib)',
            'phone_number' => 'No. Telepon',
            'university_name' => 'Nama Universitas (Wajib)',
            'faculty' => 'Fakultas',
            'department' => 'Jurusan',
            'employment_status' => 'Status Kepegawaian',
            'academic_rank' => 'Jabatan Akademik',
            'region_code' => 'Kode Wilayah',
            'province' => 'Provinsi',
            'city' => 'Kota',
            'member_number' => 'Nomor Anggota',
            'joined_at' => 'Tanggal Bergabung',
        ];
    }
}
```

**Routes:**
```php
$routes->group('admin/bulk-import', ['filter' => 'rbac:super_admin', 'namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('/', 'BulkImportController::index');
    $routes->post('upload', 'BulkImportController::upload');
    $routes->get('mapping', 'BulkImportController::mapping');
    $routes->post('mapping', 'BulkImportController::preview');
    $routes->post('process', 'BulkImportController::process');
});
```

**Composer Dependencies:**
```bash
composer require phpoffice/phpspreadsheet
```

**Acceptance Criteria:**
- ✅ Upload Excel file (.xls, .xlsx)
- ✅ Map Excel columns to database fields
- ✅ Preview data before import
- ✅ Import with validation
- ✅ Skip duplicates option
- ✅ Set default role & status
- ✅ Error reporting for failed rows
- ✅ Export error log

---

### TASK 1.8: QR Code Payment Info (Sprint 3 - S3-03)
**Priority:** P1 | **Points:** 3 | **Duration:** 1 day

#### Objective:
Generate QR Code untuk info pembayaran iuran pendaftaran.

#### Files to Modify/Create:
1. Modify `app/Controllers/Register.php` - Add QR generation in step 4
2. Create library `app/Libraries/QRCodeService.php`

#### Implementation:

**Install QR Code Library:**
```bash
composer require endroid/qr-code
```

**QRCodeService.php**
```php
<?php
namespace App\Libraries;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QRCodeService
{
    public function generatePaymentQR($data)
    {
        // Format: BCA Virtual Account or QRIS
        // Example: QRIS format
        $qrContent = "00020101021226{$data['amount']}0303{$data['merchant_id']}0107{$data['bill_number']}6304";

        $qrCode = QrCode::create($qrContent)
            ->setSize(300)
            ->setMargin(10);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Save to file
        $filename = 'payment_' . $data['member_uuid'] . '_' . time() . '.png';
        $filepath = WRITEPATH . 'uploads/qrcodes/' . $filename;

        $result->saveToFile($filepath);

        return $filename;
    }

    public function generateMemberCardQR($memberNumber)
    {
        $qrContent = "SPK:MEMBER:{$memberNumber}";

        $qrCode = QrCode::create($qrContent)
            ->setSize(200)
            ->setMargin(5);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $filename = 'membercard_' . $memberNumber . '.png';
        $filepath = WRITEPATH . 'uploads/member-cards/' . $filename;

        $result->saveToFile($filepath);

        return $filename;
    }
}
```

**Modify Register::step4():**
```php
public function step4()
{
    // ... existing code ...

    // Generate QR Code
    $qrService = new \App\Libraries\QRCodeService();
    $qrFilename = $qrService->generatePaymentQR([
        'amount' => $registrationBill['bill_amount'],
        'merchant_id' => '1234567890', // From system settings
        'bill_number' => str_pad($registrationBill['id'], 10, '0', STR_PAD_LEFT),
        'member_uuid' => $member['uuid'],
    ]);

    $data['qr_code'] = $qrFilename;

    // ... rest of code ...
}
```

**Acceptance Criteria:**
- ✅ QR Code generated for payment info
- ✅ QR Code displayed in registration step 4
- ✅ QR Code contains correct payment data

---

## PHASE 1 SUMMARY

**Total Duration:** 3-4 weeks
**Total Story Points:** 57

**Deliverables:**
1. ✅ CMS Database Schema (10 tables)
2. ✅ CMS Models (10 models)
3. ✅ Public Website (Landing, Pages, News, Documents, Contact)
4. ✅ Admin CMS Panel (Full CRUD)
5. ✅ Bulk Import System (Excel)
6. ✅ QR Code Generation

**Testing Checklist:**
- [ ] All CMS tables created without errors
- [ ] Public pages accessible and responsive
- [ ] Admin CMS CRUD operations work
- [ ] Bulk import handles 1000+ rows
- [ ] QR codes generate correctly
- [ ] All actions audit logged

---

## PHASE 2: FINANCIAL AUTOMATION (P1)
**Duration:** 2-3 weeks
**Story Points:** 33
**Sprint:** 5 (Complete)

### Objective:
Mengotomasi sistem iuran bulanan, reminder, dan enforcement tunggakan.

---

### TASK 2.1: Dues Bills Table & Model (Sprint 5 - S5-03)
**Priority:** P1 | **Points:** 3 | **Duration:** 0.5 day

#### Files to Create:
1. `app/Database/Migrations/2025-12-22-000001_CreateSpDuesBillsTable.php`
2. `app/Models/DuesBillModel.php`

**Schema Reference:** Panduan Section 3.5 (sp_dues_bills)

**Acceptance Criteria:**
- ✅ sp_dues_bills table created
- ✅ Foreign keys to sp_members and sp_dues_rates
- ✅ Unique constraint on (member_id, bill_type, period_year, period_month)

---

### TASK 2.2: Dues Claims Table & Model (Sprint 5 - S5-05)
**Priority:** P1 | **Points:** 2 | **Duration:** 0.5 day

#### Files to Create:
1. `app/Database/Migrations/2025-12-22-000002_CreateSpDuesClaimsTable.php`
2. `app/Models/DuesClaimModel.php`

**Schema Reference:** Panduan Section 3.5 (sp_dues_claims)

---

### TASK 2.3: Billing Service (Sprint 5 - S5-03, S5-06)
**Priority:** P1 | **Points:** 8 | **Duration:** 2 days

#### File to Create:
`app/Libraries/BillingService.php`

#### Implementation:
```php
<?php
namespace App\Libraries;

use App\Models\MemberModel;
use App\Models\DuesBillModel;
use App\Models\AuditLogModel;

class BillingService
{
    protected $memberModel;
    protected $billModel;
    protected $auditLog;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->billModel = new DuesBillModel();
        $this->auditLog = new AuditLogModel();
    }

    /**
     * Generate monthly bills for all active members
     */
    public function generateMonthlyBills($year = null, $month = null)
    {
        $year = $year ?? date('Y');
        $month = $month ?? date('m');

        // Get all active members
        $members = $this->memberModel
            ->where('membership_status', 'active')
            ->where('account_status', 'active')
            ->findAll();

        $generated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($members as $member) {
            try {
                // Check if bill already exists
                $existing = $this->billModel
                    ->where('member_id', $member['id'])
                    ->where('bill_type', 'monthly')
                    ->where('period_year', $year)
                    ->where('period_month', $month)
                    ->first();

                if ($existing) {
                    $skipped++;
                    continue;
                }

                // Get member's dues rate
                $rateId = $member['dues_rate_id'];
                $amount = $member['dues_amount'];

                if (!$amount) {
                    $errors[] = "Member {$member['member_number']} tidak memiliki tarif iuran";
                    $skipped++;
                    continue;
                }

                // Calculate due date (end of month)
                $dueDate = date('Y-m-t', strtotime("{$year}-{$month}-01"));

                // Create bill
                $billId = $this->billModel->insert([
                    'member_id' => $member['id'],
                    'bill_type' => 'monthly',
                    'period_year' => $year,
                    'period_month' => $month,
                    'rate_id' => $rateId,
                    'bill_amount' => $amount,
                    'bill_status' => 'unpaid',
                    'due_date' => $dueDate,
                    'arrears_level' => 0,
                ]);

                if ($billId) {
                    $generated++;

                    // Audit log
                    $this->auditLog->log([
                        'actor_type' => 'system',
                        'target_type' => 'dues_bill',
                        'target_id' => $billId,
                        'action' => 'dues.bill.generated',
                        'new_values' => json_encode([
                            'member_id' => $member['id'],
                            'period' => "{$year}-{$month}",
                            'amount' => $amount,
                        ]),
                    ]);
                }

            } catch (\Exception $e) {
                $errors[] = "Member {$member['member_number']}: " . $e->getMessage();
                $skipped++;
            }
        }

        return [
            'generated' => $generated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Flag members with arrears >= 3 months
     */
    public function flagArrears()
    {
        // Get all unpaid bills older than 3 months
        $threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));

        $unpaidBills = $this->billModel
            ->where('bill_status', 'unpaid')
            ->where('due_date <', $threeMonthsAgo)
            ->findAll();

        // Group by member
        $memberArrears = [];
        foreach ($unpaidBills as $bill) {
            if (!isset($memberArrears[$bill['member_id']])) {
                $memberArrears[$bill['member_id']] = [];
            }
            $memberArrears[$bill['member_id']][] = $bill;
        }

        $flagged = 0;

        foreach ($memberArrears as $memberId => $bills) {
            if (count($bills) >= 3) {
                // Flag member as having arrears
                $this->memberModel->update($memberId, [
                    'dues_status' => 'overdue',
                    'account_status' => 'pending', // or 'suspended' based on policy
                ]);

                // Update bills
                foreach ($bills as $bill) {
                    $this->billModel->update($bill['id'], [
                        'bill_status' => 'overdue',
                        'arrears_level' => count($bills),
                    ]);
                }

                $flagged++;

                // Audit log
                $this->auditLog->log([
                    'actor_type' => 'system',
                    'target_type' => 'member',
                    'target_id' => $memberId,
                    'action' => 'member.arrears.flagged',
                    'new_values' => json_encode([
                        'arrears_count' => count($bills),
                        'status' => 'pending',
                    ]),
                ]);
            }
        }

        return $flagged;
    }

    /**
     * Get members with arrears
     */
    public function getMembersWithArrears()
    {
        return $this->memberModel
            ->where('dues_status', 'overdue')
            ->findAll();
    }
}
```

---

### TASK 2.4: Payment Reminder Service (Sprint 5 - S5-07)
**Priority:** P1 | **Points:** 5 | **Duration:** 1 day

#### File to Create:
`app/Libraries/PaymentReminderService.php`

#### Implementation:
```php
<?php
namespace App\Libraries;

use App\Models\MemberModel;
use App\Models\DuesBillModel;

class PaymentReminderService
{
    protected $memberModel;
    protected $billModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->billModel = new DuesBillModel();
    }

    /**
     * Send reminders based on arrears level
     */
    public function sendReminders()
    {
        $sent = [
            'month_1' => 0,
            'month_2' => 0,
            'month_3' => 0,
        ];

        // Get unpaid bills
        $unpaidBills = $this->billModel
            ->where('bill_status', 'unpaid')
            ->where('due_date <', date('Y-m-d'))
            ->findAll();

        foreach ($unpaidBills as $bill) {
            $member = $this->memberModel->find($bill['member_id']);

            if (!$member || !$member['email']) {
                continue;
            }

            $monthsOverdue = $this->getMonthsOverdue($bill['due_date']);

            if ($monthsOverdue == 1) {
                $this->sendReminderEmail($member, $bill, 'reminder');
                $sent['month_1']++;
            } elseif ($monthsOverdue == 2) {
                $this->sendWarningEmail($member, $bill, 'warning');
                $sent['month_2']++;
            } elseif ($monthsOverdue >= 3) {
                $this->sendFinalNoticeEmail($member, $bill, 'final_notice');
                $sent['month_3']++;
            }
        }

        return $sent;
    }

    protected function getMonthsOverdue($dueDate)
    {
        $due = new \DateTime($dueDate);
        $now = new \DateTime();
        $interval = $due->diff($now);

        return $interval->m + ($interval->y * 12);
    }

    protected function sendReminderEmail($member, $bill, $type)
    {
        // TODO: Implement EmailService
        $emailService = new \App\Libraries\EmailService();

        $data = [
            'member' => $member,
            'bill' => $bill,
            'type' => $type,
        ];

        $emailService->send([
            'to' => $member['email'],
            'subject' => 'Reminder: Iuran Bulanan Belum Dibayar',
            'template' => 'emails/payment_reminder',
            'data' => $data,
        ]);
    }

    protected function sendWarningEmail($member, $bill, $type)
    {
        $emailService = new \App\Libraries\EmailService();

        $data = [
            'member' => $member,
            'bill' => $bill,
            'type' => $type,
        ];

        $emailService->send([
            'to' => $member['email'],
            'subject' => 'PERINGATAN: Tunggakan Iuran 2 Bulan',
            'template' => 'emails/payment_warning',
            'data' => $data,
        ]);
    }

    protected function sendFinalNoticeEmail($member, $bill, $type)
    {
        $emailService = new \App\Libraries\EmailService();

        $data = [
            'member' => $member,
            'bill' => $bill,
            'type' => $type,
        ];

        $emailService->send([
            'to' => $member['email'],
            'subject' => 'FINAL NOTICE: Akun Akan Disuspend',
            'template' => 'emails/payment_final_notice',
            'data' => $data,
        ]);
    }
}
```

---

### TASK 2.5: Cron Job Commands (Sprint 5 - S5-10)
**Priority:** P1 | **Points:** 3 | **Duration:** 1 day

#### Files to Create:
1. `app/Commands/GenerateMonthlyBills.php`
2. `app/Commands/SendPaymentReminders.php`
3. `app/Commands/FlagArrears.php`

#### Implementation:

**GenerateMonthlyBills.php**
```php
<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\BillingService;

class GenerateMonthlyBills extends BaseCommand
{
    protected $group = 'Billing';
    protected $name = 'billing:generate';
    protected $description = 'Generate monthly bills for all active members';

    protected $usage = 'billing:generate [year] [month]';
    protected $arguments = [
        'year' => 'Year (YYYY)',
        'month' => 'Month (1-12)',
    ];

    public function run(array $params)
    {
        $year = $params[0] ?? date('Y');
        $month = $params[1] ?? date('m');

        CLI::write("Generating monthly bills for {$year}-{$month}...", 'yellow');

        $billingService = new BillingService();
        $result = $billingService->generateMonthlyBills($year, $month);

        CLI::write("Generated: {$result['generated']}", 'green');
        CLI::write("Skipped: {$result['skipped']}", 'yellow');

        if (!empty($result['errors'])) {
            CLI::write("Errors:", 'red');
            foreach ($result['errors'] as $error) {
                CLI::write("  - {$error}", 'red');
            }
        }

        CLI::write("Done!", 'green');
    }
}
```

**Crontab Configuration:**
```bash
# Generate monthly bills on the 1st of each month at 00:00
0 0 1 * * cd /path/to/webspk && php spark billing:generate

# Send payment reminders daily at 08:00
0 8 * * * cd /path/to/webspk && php spark billing:reminders

# Flag arrears monthly on the 5th at 00:00
0 0 5 * * cd /path/to/webspk && php spark billing:flag-arrears
```

---

### TASK 2.6: Treasury Controllers (Sprint 5 - S5-04, S5-05, S5-09)
**Priority:** P1 | **Points:** 8 | **Duration:** 2 days

#### Files to Create:
1. `app/Controllers/Treasury/DashboardController.php`
2. `app/Controllers/Treasury/BillingController.php`
3. `app/Controllers/Treasury/PaymentVerificationController.php`
4. `app/Controllers/Treasury/ClaimController.php`
5. `app/Controllers/Treasury/ArrearController.php`
6. `app/Controllers/Treasury/ReportController.php`

**Note:** This task is similar to Admin controllers but focused on treasury role.

**Routes:**
```php
$routes->group('treasury', ['filter' => 'rbac:treasurer,super_admin', 'namespace' => 'App\Controllers\Treasury'], function($routes) {
    $routes->get('dashboard', 'DashboardController::index');

    // Billing
    $routes->get('billing', 'BillingController::index');
    $routes->post('billing/generate', 'BillingController::generate');
    $routes->get('billing/view/(:num)', 'BillingController::view/$1');

    // Payment Verification
    $routes->get('payments', 'PaymentVerificationController::index');
    $routes->get('payments/pending', 'PaymentVerificationController::pending');
    $routes->post('payments/verify/(:num)', 'PaymentVerificationController::verify/$1');
    $routes->post('payments/reject/(:num)', 'PaymentVerificationController::reject/$1');

    // Claims
    $routes->get('claims', 'ClaimController::index');
    $routes->get('claims/view/(:num)', 'ClaimController::view/$1');
    $routes->post('claims/approve/(:num)', 'ClaimController::approve/$1');
    $routes->post('claims/reject/(:num)', 'ClaimController::reject/$1');

    // Arrears
    $routes->get('arrears', 'ArrearController::index');
    $routes->post('arrears/flag', 'ArrearController::flag');
    $routes->post('arrears/waive/(:num)', 'ArrearController::waive/$1');

    // Reports
    $routes->get('reports', 'ReportController::index');
    $routes->get('reports/export', 'ReportController::export');
});
```

---

### TASK 2.7: Member Claims Feature (Sprint 5 - S5-05)
**Priority:** P2 | **Points:** 4 | **Duration:** 1 day

#### Files to Create:
1. Modify `app/Controllers/Member/Payment.php` - Add claim submission
2. `app/Views/member/payment/claim.php`

**Routes:**
```php
$routes->group('member/payment', ['filter' => 'rbac:member,coordinator,treasurer'], function($routes) {
    $routes->get('claim', 'Member\Payment::claimForm');
    $routes->post('claim', 'Member\Payment::submitClaim');
    $routes->get('claims', 'Member\Payment::myClaims');
    $routes->get('claim/view/(:num)', 'Member\Payment::viewClaim/$1');
});
```

---

## PHASE 2 SUMMARY

**Total Duration:** 2-3 weeks
**Total Story Points:** 33

**Deliverables:**
1. ✅ sp_dues_bills & sp_dues_claims tables
2. ✅ BillingService (auto-generate monthly bills)
3. ✅ PaymentReminderService (email reminders)
4. ✅ Cron jobs (billing, reminders, arrears)
5. ✅ Treasury controllers & dashboard
6. ✅ Claims management
7. ✅ Financial reports

**Testing Checklist:**
- [ ] Monthly bills generated correctly
- [ ] Reminders sent based on overdue levels
- [ ] Arrears flagged after 3 months
- [ ] Claims can be submitted and processed
- [ ] Treasury dashboard shows accurate data
- [ ] Reports export correctly

---

## PHASE 3: MEMBER ENGAGEMENT (P2)
**Duration:** 3-4 weeks
**Story Points:** 52
**Sprint:** 6 (Communication & Survey)

### Objective:
Membangun forum diskusi, sistem survei, dan messaging untuk engagement anggota.

---

### TASK 3.1: Forum Database Schema (Sprint 6 - S6-01)
**Priority:** P2 | **Points:** 3 | **Duration:** 0.5 day

#### Migration to Create:
`app/Database/Migrations/2025-12-23-000001_CreateForumTables.php`

**Tables:**
1. `forum_threads` - Thread forum
2. `forum_posts` - Posts/replies

**Schema Example:**
```sql
CREATE TABLE forum_threads (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NULL,
    author_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    is_pinned TINYINT(1) DEFAULT 0,
    is_locked TINYINT(1) DEFAULT 0,
    view_count INT UNSIGNED DEFAULT 0,
    reply_count INT UNSIGNED DEFAULT 0,
    last_post_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,

    INDEX idx_author (author_id),
    INDEX idx_created (created_at),
    FOREIGN KEY (author_id) REFERENCES sp_members(id)
);

CREATE TABLE forum_posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    thread_id INT UNSIGNED NOT NULL,
    author_id INT UNSIGNED NOT NULL,
    content_html TEXT NOT NULL,
    is_edited TINYINT(1) DEFAULT 0,
    edited_at DATETIME NULL,
    edited_by INT UNSIGNED NULL,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_thread (thread_id),
    INDEX idx_author (author_id),
    FOREIGN KEY (thread_id) REFERENCES forum_threads(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES sp_members(id)
);
```

---

### TASK 3.2: Forum Models (Sprint 6)
**Priority:** P2 | **Points:** 2 | **Duration:** 0.5 day

#### Files to Create:
1. `app/Models/ForumThreadModel.php`
2. `app/Models/ForumPostModel.php`

---

### TASK 3.3: Forum Controllers (Sprint 6 - S6-02, S6-03)
**Priority:** P2 | **Points:** 13 | **Duration:** 3 days

#### Files to Create:
1. `app/Controllers/Member/ForumController.php` - Member forum access
2. `app/Controllers/Admin/ForumModerationController.php` - Admin moderation

**Routes:**
```php
// Member Forum
$routes->group('member/forum', ['filter' => ['auth', 'membership:active'], 'namespace' => 'App\Controllers\Member'], function($routes) {
    $routes->get('/', 'ForumController::index');
    $routes->get('thread/(:num)', 'ForumController::showThread/$1');
    $routes->get('create', 'ForumController::createThread');
    $routes->post('create', 'ForumController::storeThread');
    $routes->post('thread/(:num)/reply', 'ForumController::reply/$1');
    $routes->post('post/(:num)/edit', 'ForumController::editPost/$1');
    $routes->post('post/(:num)/delete', 'ForumController::deletePost/$1');
});

// Admin Forum Moderation
$routes->group('admin/forum', ['filter' => 'rbac:admin,super_admin'], function($routes) {
    $routes->get('moderation', 'Admin\ForumModerationController::index');
    $routes->post('thread/(:num)/pin', 'Admin\ForumModerationController::pinThread/$1');
    $routes->post('thread/(:num)/lock', 'Admin\ForumModerationController::lockThread/$1');
    $routes->post('thread/(:num)/delete', 'Admin\ForumModerationController::deleteThread/$1');
    $routes->post('post/(:num)/delete', 'Admin\ForumModerationController::deletePost/$1');
});
```

---

### TASK 3.4: Survey Database Schema (Sprint 6 - S6-06)
**Priority:** P2 | **Points:** 5 | **Duration:** 1 day

#### Migration to Create:
`app/Database/Migrations/2025-12-23-000002_CreateSurveyTables.php`

**Tables:**
1. `surveys` - Master survei
2. `survey_questions` - Pertanyaan survei
3. `survey_options` - Opsi jawaban (untuk multiple choice)
4. `survey_responses` - Jawaban anggota

**Schema Example:**
```sql
CREATE TABLE surveys (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    start_date DATETIME NULL,
    end_date DATETIME NULL,
    target_audience ENUM('all','active','region','specific') DEFAULT 'all',
    target_region_code VARCHAR(10) NULL,
    is_active TINYINT(1) DEFAULT 1,
    is_anonymous TINYINT(1) DEFAULT 0,
    created_by INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (created_by) REFERENCES sp_members(id)
);

CREATE TABLE survey_questions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    survey_id INT UNSIGNED NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('text','textarea','radio','checkbox','rating') NOT NULL,
    is_required TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,

    FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE CASCADE
);

CREATE TABLE survey_options (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id INT UNSIGNED NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,

    FOREIGN KEY (question_id) REFERENCES survey_questions(id) ON DELETE CASCADE
);

CREATE TABLE survey_responses (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    survey_id INT UNSIGNED NOT NULL,
    member_id INT UNSIGNED NULL,
    question_id INT UNSIGNED NOT NULL,
    response_text TEXT NULL,
    selected_option_id INT UNSIGNED NULL,
    responded_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_survey_member (survey_id, member_id),
    FOREIGN KEY (survey_id) REFERENCES surveys(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES sp_members(id),
    FOREIGN KEY (question_id) REFERENCES survey_questions(id) ON DELETE CASCADE,
    FOREIGN KEY (selected_option_id) REFERENCES survey_options(id) ON DELETE SET NULL
);
```

---

### TASK 3.5: Survey Models (Sprint 6)
**Priority:** P2 | **Points:** 3 | **Duration:** 0.5 day

#### Files to Create:
1. `app/Models/SurveyModel.php`
2. `app/Models/SurveyQuestionModel.php`
3. `app/Models/SurveyOptionModel.php`
4. `app/Models/SurveyResponseModel.php`

---

### TASK 3.6: Survey Controllers (Sprint 6 - S6-07, S6-08, S6-09)
**Priority:** P2 | **Points:** 18 | **Duration:** 4 days

#### Files to Create:
1. `app/Controllers/Member/SurveyController.php` - Member survey participation
2. `app/Controllers/Admin/SurveyManagementController.php` - Admin survey CRUD
3. `app/Controllers/Admin/SurveyResultsController.php` - Survey results & analytics

**Routes:**
```php
// Member Survey
$routes->group('member/survey', ['filter' => ['auth', 'membership:active']], function($routes) {
    $routes->get('/', 'Member\SurveyController::index');
    $routes->get('(:num)', 'Member\SurveyController::show/$1');
    $routes->post('(:num)/submit', 'Member\SurveyController::submit/$1');
});

// Admin Survey Management
$routes->group('admin/surveys', ['filter' => 'rbac:admin,super_admin'], function($routes) {
    $routes->get('/', 'Admin\SurveyManagementController::index');
    $routes->get('create', 'Admin\SurveyManagementController::create');
    $routes->post('create', 'Admin\SurveyManagementController::store');
    $routes->get('edit/(:num)', 'Admin\SurveyManagementController::edit/$1');
    $routes->post('edit/(:num)', 'Admin\SurveyManagementController::update/$1');
    $routes->post('delete/(:num)', 'Admin\SurveyManagementController::delete/$1');
    $routes->get('results/(:num)', 'Admin\SurveyResultsController::show/$1');
    $routes->get('results/(:num)/export', 'Admin\SurveyResultsController::export/$1');
});
```

---

### TASK 3.7: Messaging System (Sprint 6 - S6-04, S6-05)
**Priority:** P2 | **Points:** 8 | **Duration:** 2 days

#### Migration to Create:
`app/Database/Migrations/2025-12-23-000003_CreateMessagesTable.php`

**Schema:**
```sql
CREATE TABLE messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    from_member_id INT UNSIGNED NOT NULL,
    to_member_id INT UNSIGNED NULL,
    to_role ENUM('super_admin','admin','coordinator','treasurer') NULL,
    subject VARCHAR(255) NULL,
    message_text TEXT NOT NULL,
    status ENUM('new','read','replied','closed') DEFAULT 'new',
    parent_message_id INT UNSIGNED NULL,
    replied_by INT UNSIGNED NULL,
    replied_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_from (from_member_id),
    INDEX idx_to (to_member_id),
    INDEX idx_status (status),
    FOREIGN KEY (from_member_id) REFERENCES sp_members(id),
    FOREIGN KEY (to_member_id) REFERENCES sp_members(id),
    FOREIGN KEY (parent_message_id) REFERENCES messages(id) ON DELETE CASCADE
);
```

#### Files to Create:
1. `app/Models/MessageModel.php`
2. `app/Controllers/Member/MessageController.php`
3. `app/Controllers/Admin/MessageInboxController.php`

---

### TASK 3.8: Broadcast Notifications (Sprint 6 - S6-10)
**Priority:** P2 | **Points:** 5 | **Duration:** 1 day

#### File to Create:
`app/Controllers/Admin/BroadcastController.php`

#### Implementation:
- Email broadcast to all members
- Email broadcast to specific region
- Email broadcast to specific status (active/pending)
- In-app notification (optional)

---

## PHASE 3 SUMMARY

**Total Duration:** 3-4 weeks
**Total Story Points:** 52

**Deliverables:**
1. ✅ Forum (threads, posts, moderation)
2. ✅ Survey system (create, participate, results)
3. ✅ Messaging (member to admin)
4. ✅ Broadcast notifications

**Testing Checklist:**
- [ ] Forum threads and replies work
- [ ] Moderation actions work (pin, lock, delete)
- [ ] Surveys can be created and filled
- [ ] Survey results accurate
- [ ] Messages sent and replied
- [ ] Broadcast emails sent successfully

---

## PHASE 4: POLISH & DEPLOYMENT (P2)
**Duration:** 2-3 weeks
**Story Points:** 62
**Sprint:** 7 (partial) + 8

### Objective:
Melengkapi fitur member, testing, dan deployment.

---

### TASK 4.1: Digital Member Card (Sprint 7 - S7-08)
**Priority:** P2 | **Points:** 5 | **Duration:** 1 day

#### Files to Create:
1. `app/Controllers/Member/MemberCardController.php`
2. `app/Libraries/MemberCardGenerator.php`
3. `app/Views/member/card.php`

#### Implementation:
- Generate PDF member card with QR code
- Include member photo, number, name, region
- Download & print functionality

---

### TASK 4.2: Unit Testing (Sprint 8 - S8-01)
**Priority:** P2 | **Points:** 8 | **Duration:** 2 days

#### Tests to Create:
- `tests/unit/Models/MemberModelTest.php`
- `tests/unit/Models/DuesBillModelTest.php`
- `tests/unit/Models/DuesPaymentModelTest.php`
- `tests/unit/Libraries/BillingServiceTest.php`
- `tests/unit/Libraries/QRCodeServiceTest.php`

---

### TASK 4.3: Integration Testing (Sprint 8 - S8-02)
**Priority:** P2 | **Points:** 8 | **Duration:** 2 days

#### Tests to Create:
- Registration flow test
- Payment verification flow test
- Approval/rejection flow test
- Billing generation test
- Forum CRUD test
- Survey CRUD test

---

### TASK 4.4: Security Audit (Sprint 8 - S8-04)
**Priority:** P1 | **Points:** 5 | **Duration:** 1 day

#### Checklist:
- [ ] CSRF protection enabled
- [ ] XSS prevention in all user inputs
- [ ] SQL injection prevention (use Query Builder)
- [ ] File upload security (mime, size, path)
- [ ] Password hashing verification
- [ ] Session security configuration
- [ ] HTTPS enforcement
- [ ] Rate limiting on login

---

### TASK 4.5: Performance Optimization (Sprint 8 - S8-05)
**Priority:** P2 | **Points:** 5 | **Duration:** 1 day

#### Optimizations:
- Database query optimization (N+1 problem)
- Caching frequently accessed data
- Image optimization
- CSS/JS minification
- Database indexing

---

### TASK 4.6: Documentation (Sprint 8 - S8-09)
**Priority:** P2 | **Points:** 5 | **Duration:** 1 day

#### Documents to Create:
1. User Manual (Member)
2. Admin Manual (Admin/Coordinator/Treasurer)
3. API Documentation
4. Deployment Guide
5. Maintenance Guide

---

### TASK 4.7: Deployment (Sprint 8 - S8-07, S8-08)
**Priority:** P1 | **Points:** 5 | **Duration:** 1 day

#### Steps:
1. Setup production server
2. Configure environment variables
3. Run migrations & seeders
4. Configure cron jobs
5. Setup SSL certificate
6. Configure backups
7. Go-live

---

## OVERALL TIMELINE SUMMARY

| Phase | Duration | Story Points | Target Completion |
|-------|----------|--------------|-------------------|
| Phase 1 (CMS + Bulk Import) | 3-4 weeks | 57 | Week 4 |
| Phase 2 (Auto Billing) | 2-3 weeks | 33 | Week 7 |
| Phase 3 (Forum & Survey) | 3-4 weeks | 52 | Week 11 |
| Phase 4 (Testing & Deploy) | 2-3 weeks | 62 | Week 15 |
| **TOTAL** | **11-15 weeks** | **210** | **~3-4 months** |

---

## PRIORITIZED BACKLOG

**SPRINT 1 (Next 2 weeks):**
- [ ] TASK 1.1: CMS Database Schema
- [ ] TASK 1.2: CMS Models
- [ ] TASK 1.3: Public Controllers
- [ ] TASK 1.4: Admin CMS Controllers
- [ ] TASK 1.5: CMS Views
- [ ] TASK 1.6: CMS Seeder

**SPRINT 2 (Week 3-4):**
- [ ] TASK 1.7: Bulk Import System
- [ ] TASK 1.8: QR Code Payment Info
- [ ] TASK 2.1: Dues Bills Table & Model
- [ ] TASK 2.2: Dues Claims Table & Model

**SPRINT 3 (Week 5-6):**
- [ ] TASK 2.3: Billing Service
- [ ] TASK 2.4: Payment Reminder Service
- [ ] TASK 2.5: Cron Job Commands
- [ ] TASK 2.6: Treasury Controllers

**SPRINT 4 (Week 7-8):**
- [ ] TASK 2.7: Member Claims Feature
- [ ] TASK 3.1: Forum Database Schema
- [ ] TASK 3.2: Forum Models
- [ ] TASK 3.3: Forum Controllers

**SPRINT 5 (Week 9-10):**
- [ ] TASK 3.4: Survey Database Schema
- [ ] TASK 3.5: Survey Models
- [ ] TASK 3.6: Survey Controllers

**SPRINT 6 (Week 11-12):**
- [ ] TASK 3.7: Messaging System
- [ ] TASK 3.8: Broadcast Notifications
- [ ] TASK 4.1: Digital Member Card

**SPRINT 7 (Week 13-14):**
- [ ] TASK 4.2: Unit Testing
- [ ] TASK 4.3: Integration Testing
- [ ] TASK 4.4: Security Audit

**SPRINT 8 (Week 15):**
- [ ] TASK 4.5: Performance Optimization
- [ ] TASK 4.6: Documentation
- [ ] TASK 4.7: Deployment

---

**Generated:** 2025-12-20
**Author:** Claude Code Execution Plan
**Version:** 1.0
