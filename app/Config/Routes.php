<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Public Routes
$routes->get('/', 'Home::index');

// Public CMS Routes - News
$routes->group('berita', ['namespace' => 'App\Controllers\Public'], function($routes) {
    $routes->get('/', 'NewsController::index');
    $routes->get('arsip/(:num)', 'NewsController::archive/$1');
    $routes->get('(:segment)', 'NewsController::show/$1');
});

// Public CMS Routes - Documents
$routes->group('', ['namespace' => 'App\Controllers\Public'], function($routes) {
    $routes->get('publikasi', 'DocumentController::publikasi');
    $routes->get('regulasi', 'DocumentController::regulasi');
    $routes->get('documents/download/(:num)', 'DocumentController::download/$1');
    $routes->get('documents/preview/(:num)', 'DocumentController::preview/$1');
    $routes->get('documents/search', 'DocumentController::search');
});

// Public CMS Routes - Contact
$routes->group('contact', ['namespace' => 'App\Controllers\Public'], function($routes) {
    $routes->get('/', 'ContactController::index');
    $routes->post('submit', 'ContactController::submit');
    $routes->post('ajax-submit', 'ContactController::ajaxSubmit');
});

// Public CMS Routes - Static Pages
$routes->group('', ['namespace' => 'App\Controllers\Public'], function($routes) {
    $routes->get('tentang', 'PageController::tentang');
    $routes->get('sejarah', 'PageController::sejarah');
    $routes->get('manifesto', 'PageController::manifesto');
    $routes->get('visi-misi', 'PageController::visimisi');
    $routes->get('ad-art', 'PageController::adart');
    $routes->get('pengurus', 'PageController::pengurus');
    $routes->get('kontak', 'PageController::kontak');
    $routes->get('page/(:segment)', 'PageController::show/$1');
});

// Registration Routes (Public - Multi-step)
$routes->group('registrasi', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'Register::index');
    $routes->post('step-1', 'Register::processStep1');
    $routes->get('verifikasi-email', 'Register::verificationEmail');
    $routes->get('step-2', 'Register::step2');
    $routes->post('step-2', 'Register::processStep2');
    $routes->get('step-3', 'Register::step3');
    $routes->post('step-3', 'Register::processStep3');
    $routes->get('step-4', 'Register::step4');
    $routes->post('step-4', 'Register::processStep4');
    $routes->get('selesai', 'Register::complete');
});

// Email Verification Routes
$routes->group('', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('verify-email/(:any)', 'EmailVerification::verify/$1');
    $routes->post('email-verification/resend', 'EmailVerification::resend');
});

// Authentication Routes
$routes->group('', ['namespace' => 'App\Controllers'], function($routes) {
    // Login
    $routes->get('login', 'Auth::login');
    $routes->post('auth/login', 'Auth::processLogin');

    // Logout
    $routes->get('logout', 'Auth::logout');

    // Forgot Password
    $routes->get('forgot-password', 'Auth::forgotPassword');
    $routes->post('auth/forgot-password', 'Auth::processForgotPassword');

    // Reset Password
    $routes->get('reset-password/(:any)', 'Auth::resetPassword/$1');
    $routes->post('auth/reset-password', 'Auth::processResetPassword');
});

// Secure File Download - Require Authentication
$routes->group('', ['filter' => 'auth', 'namespace' => 'App\Controllers'], function($routes) {
    $routes->get('files/(:segment)/(:segment)', 'FileController::download/$1/$2');
});

// Protected Routes - Require Authentication
$routes->group('', ['filter' => 'auth', 'namespace' => 'App\Controllers'], function($routes) {
    // General Dashboard (for all authenticated users)
    $routes->get('dashboard', 'Dashboard::index');
});

// Admin Routes - Require super_admin or admin role
$routes->group('admin', ['filter' => 'rbac:super_admin,admin', 'namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // Member Management
    $routes->get('members', 'MemberManagement::index');
    $routes->get('members/pending', 'MemberManagement::pendingApprovals');
    $routes->get('members/suspended', 'MemberManagement::suspendedMembers');
    $routes->get('members/view/(:num)', 'MemberManagement::view/$1');
    $routes->post('members/approve/(:num)', 'MemberManagement::approve/$1');
    $routes->post('members/reject/(:num)', 'MemberManagement::reject/$1');
    $routes->post('members/suspend/(:num)', 'MemberManagement::suspend/$1');
    $routes->post('members/activate/(:num)', 'MemberManagement::activate/$1');
    $routes->post('members/delete/(:num)', 'MemberManagement::delete/$1');

    // Payment Management
    $routes->get('payments', 'PaymentManagement::index');
    $routes->get('payments/pending', 'PaymentManagement::pendingVerifications');
    $routes->get('payments/view/(:num)', 'PaymentManagement::view/$1');
    $routes->post('payments/verify/(:num)', 'PaymentManagement::verify/$1');
    $routes->post('payments/reject/(:num)', 'PaymentManagement::reject/$1');
});

// Super Admin Only Routes - Settings, RBAC, Audit
$routes->group('admin/settings', ['filter' => 'rbac:super_admin', 'namespace' => 'App\Controllers\Admin'], function($routes) {
    // System Settings
    $routes->get('/', 'Settings::index');
    $routes->post('update', 'Settings::update');
    $routes->get('create', 'Settings::create');
    $routes->post('create', 'Settings::create');
    $routes->post('delete/(:num)', 'Settings::delete/$1');
    $routes->post('reset/(:num)', 'Settings::reset/$1');

    // RBAC Management
    $routes->get('rbac', 'RBACManagement::index');
    $routes->get('rbac/roles', 'RBACManagement::roles');
    $routes->get('rbac/permissions', 'RBACManagement::permissions');
    $routes->get('rbac/assign', 'RBACManagement::assignRoles');
    $routes->post('rbac/update-user-roles', 'RBACManagement::updateUserRoles');
    $routes->get('rbac/role/(:num)/permissions', 'RBACManagement::editRolePermissions/$1');
    $routes->post('rbac/role/(:num)/permissions', 'RBACManagement::editRolePermissions/$1');
    $routes->get('rbac/role/create', 'RBACManagement::createRole');
    $routes->post('rbac/role/create', 'RBACManagement::createRole');
    $routes->post('rbac/role/(:num)/toggle', 'RBACManagement::toggleRoleStatus/$1');

    // Audit Log
    $routes->get('audit', 'AuditLog::index');
    $routes->get('audit/view/(:num)', 'AuditLog::view/$1');
    $routes->get('audit/export', 'AuditLog::export');
    $routes->post('audit/clean', 'AuditLog::clean');
    $routes->get('audit/statistics', 'AuditLog::statistics');
});

// Admin Routes - Dues Rate Management
$routes->group('admin/dues-rates', ['filter' => 'rbac:super_admin,admin', 'namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('/', 'DuesRateController::index');
    $routes->get('create', 'DuesRateController::create');
    $routes->post('create', 'DuesRateController::create');
    $routes->get('edit/(:num)', 'DuesRateController::edit/$1');
    $routes->post('edit/(:num)', 'DuesRateController::edit/$1');
    $routes->get('view/(:num)', 'DuesRateController::view/$1');
    $routes->post('toggle-status', 'DuesRateController::toggleStatus');
    $routes->post('delete/(:num)', 'DuesRateController::delete/$1');
    $routes->get('duplicate/(:num)', 'DuesRateController::duplicate/$1');
});

// Admin CMS Routes - Pages Management
$routes->group('admin/cms/pages', ['filter' => 'rbac:super_admin,admin', 'namespace' => 'App\Controllers\Admin\CMS'], function($routes) {
    $routes->get('/', 'PageController::index');
    $routes->get('create', 'PageController::create');
    $routes->post('create', 'PageController::create');
    $routes->get('edit/(:num)', 'PageController::edit/$1');
    $routes->post('edit/(:num)', 'PageController::edit/$1');
    $routes->get('revisions/(:num)', 'PageController::revisions/$1');
    $routes->post('restore-revision/(:num)', 'PageController::restoreRevision/$1');
    $routes->post('delete/(:num)', 'PageController::delete/$1');
});

// Admin CMS Routes - News Management
$routes->group('admin/cms/news', ['filter' => 'rbac:super_admin,admin', 'namespace' => 'App\Controllers\Admin\CMS'], function($routes) {
    $routes->get('/', 'NewsController::index');
    $routes->get('create', 'NewsController::create');
    $routes->post('create', 'NewsController::create');
    $routes->get('edit/(:num)', 'NewsController::edit/$1');
    $routes->post('edit/(:num)', 'NewsController::edit/$1');
    $routes->post('delete/(:num)', 'NewsController::delete/$1');
});

// Admin CMS Routes - Documents Management
$routes->group('admin/cms/documents', ['filter' => 'rbac:super_admin,admin', 'namespace' => 'App\Controllers\Admin\CMS'], function($routes) {
    $routes->get('/', 'DocumentController::index');
    $routes->get('create', 'DocumentController::create');
    $routes->post('create', 'DocumentController::create');
    $routes->get('edit/(:num)', 'DocumentController::edit/$1');
    $routes->post('edit/(:num)', 'DocumentController::edit/$1');
    $routes->post('delete/(:num)', 'DocumentController::delete/$1');
    $routes->get('categories', 'DocumentController::categories');
    $routes->post('categories', 'DocumentController::categories');
});

// Admin CMS Routes - Officers Management
$routes->group('admin/cms/officers', ['filter' => 'rbac:super_admin,admin', 'namespace' => 'App\Controllers\Admin\CMS'], function($routes) {
    $routes->get('/', 'OfficerController::index');
    $routes->get('create', 'OfficerController::create');
    $routes->post('create', 'OfficerController::create');
    $routes->get('edit/(:num)', 'OfficerController::edit/$1');
    $routes->post('edit/(:num)', 'OfficerController::edit/$1');
    $routes->post('delete/(:num)', 'OfficerController::delete/$1');
    $routes->post('toggle-active/(:num)', 'OfficerController::toggleActive/$1');
});

// Admin CMS Routes - Landing Page Management (Super Admin Only)
$routes->group('admin/cms/landing', ['filter' => 'rbac:super_admin', 'namespace' => 'App\Controllers\Admin\CMS'], function($routes) {
    $routes->get('/', 'LandingController::index');
    $routes->get('create', 'LandingController::create');
    $routes->post('create', 'LandingController::create');
    $routes->get('edit/(:num)', 'LandingController::edit/$1');
    $routes->post('edit/(:num)', 'LandingController::edit/$1');
    $routes->post('delete/(:num)', 'LandingController::delete/$1');
    $routes->post('toggle-active/(:num)', 'LandingController::toggleActive/$1');
    $routes->post('reorder', 'LandingController::reorder');
});

// Admin CMS Routes - Media Library
$routes->group('admin/cms/media', ['filter' => 'rbac:super_admin,admin', 'namespace' => 'App\Controllers\Admin\CMS'], function($routes) {
    $routes->get('/', 'MediaController::index');
    $routes->get('upload', 'MediaController::upload');
    $routes->post('upload', 'MediaController::upload');
    $routes->post('ajax-upload', 'MediaController::ajaxUpload');
    $routes->get('edit/(:num)', 'MediaController::edit/$1');
    $routes->post('edit/(:num)', 'MediaController::edit/$1');
    $routes->post('delete/(:num)', 'MediaController::delete/$1');
    $routes->get('get-url/(:num)', 'MediaController::getUrl/$1');
});

// Admin CMS Routes - Subscribers Management
$routes->group('admin/cms/subscribers', ['filter' => 'rbac:super_admin,admin', 'namespace' => 'App\Controllers\Admin\CMS'], function($routes) {
    $routes->get('/', 'SubscriberController::index');
    $routes->get('create', 'SubscriberController::create');
    $routes->post('create', 'SubscriberController::create');
    $routes->get('edit/(:num)', 'SubscriberController::edit/$1');
    $routes->post('edit/(:num)', 'SubscriberController::edit/$1');
    $routes->post('delete/(:num)', 'SubscriberController::delete/$1');
    $routes->get('export', 'SubscriberController::export');
    $routes->post('bulk-delete', 'SubscriberController::bulkDelete');
});

// Admin CMS Routes - Contact Messages Management
$routes->group('admin/cms/contact', ['filter' => 'rbac:super_admin,admin', 'namespace' => 'App\Controllers\Admin\CMS'], function($routes) {
    $routes->get('/', 'ContactController::index');
    $routes->get('view/(:num)', 'ContactController::view/$1');
    $routes->post('assign/(:num)', 'ContactController::assign/$1');
    $routes->post('mark-replied/(:num)', 'ContactController::markReplied/$1');
    $routes->post('add-note/(:num)', 'ContactController::addNote/$1');
    $routes->post('archive/(:num)', 'ContactController::archive/$1');
    $routes->post('delete/(:num)', 'ContactController::delete/$1');
    $routes->post('bulk-archive', 'ContactController::bulkArchive');
    $routes->get('export', 'ContactController::export');
});

// Admin Routes - Comprehensive Reports
$routes->group('admin/reports', ['filter' => 'rbac:super_admin,admin', 'namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('/', 'ReportsController::index');
    $routes->get('export', 'ReportsController::export');
});

// Admin Routes - Enhanced Analytics
$routes->group('admin/analytics', ['filter' => 'rbac:super_admin,admin', 'namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('/', 'AnalyticsController::index');
    $routes->get('kpi-api', 'AnalyticsController::kpiApi');
});

// Coordinator Routes - Regional management
$routes->group('coordinator', ['filter' => 'rbac:coordinator', 'namespace' => 'App\Controllers\Coordinator'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // Member Management
    $routes->get('members', 'MemberController::index');
    $routes->get('members/pending', 'MemberController::pending');
    $routes->get('members/view/(:num)', 'MemberController::view/$1');
    $routes->post('members/approve/(:num)', 'MemberController::approve/$1');
    $routes->post('members/reject/(:num)', 'MemberController::reject/$1');

    // Regional Reports
    $routes->get('reports', 'ReportsController::index');
    $routes->get('reports/export', 'ReportsController::export');
});

// Admin Routes - Coordinator Management
$routes->group('admin/coordinators', ['filter' => 'rbac:super_admin,admin', 'namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('/', 'CoordinatorManagement::index');
    $routes->get('assign/(:num)', 'CoordinatorManagement::assign/$1');
    $routes->post('assign/(:num)', 'CoordinatorManagement::assign/$1');
    $routes->post('unassign', 'CoordinatorManagement::unassign');
    $routes->get('stats', 'CoordinatorManagement::regionalStats');
});

// Member Routes - Require member, coordinator, or treasurer role
$routes->group('member', ['filter' => 'rbac:member,coordinator,treasurer', 'namespace' => 'App\Controllers\Member'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');

    // Profile Management
    $routes->get('profile', 'ProfileController::index');
    $routes->get('profile/edit', 'ProfileController::edit');
    $routes->post('profile/edit', 'ProfileController::edit');
    $routes->get('profile/employment', 'ProfileController::employment');
    $routes->post('profile/employment', 'ProfileController::employment');
    $routes->get('profile/change-password', 'ProfileController::changePassword');
    $routes->post('profile/change-password', 'ProfileController::changePassword');
    $routes->post('profile/upload-photo', 'ProfileController::uploadPhoto');
    $routes->post('profile/delete-photo', 'ProfileController::deletePhoto');

    // Payment/Dues
    $routes->get('payment', 'Payment::index');
    $routes->get('payment/submit', 'Payment::submit');
    $routes->post('payment/process', 'Payment::processSubmit');
    $routes->get('payment/view/(:num)', 'Payment::view/$1');
});

// Candidate Routes - For registration completion
$routes->group('member/registration', ['filter' => 'auth', 'namespace' => 'App\Controllers\Member'], function($routes) {
    $routes->get('complete', 'Registration::complete');
    $routes->post('complete', 'Registration::processComplete');
    $routes->get('status', 'Registration::status');
});
