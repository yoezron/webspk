<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Public Routes
$routes->get('/', 'Home::index');

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

// Protected Routes - Require Authentication
$routes->group('', ['filter' => 'auth', 'namespace' => 'App\Controllers'], function($routes) {
    // General Dashboard (for all authenticated users)
    $routes->get('dashboard', 'Dashboard::index');
});

// Admin Routes - Require super_admin or admin role
$routes->group('admin', ['filter' => 'rbac:super_admin,admin', 'namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    // More admin routes will be added later
});

// Member Routes - Require member, coordinator, or treasurer role
$routes->group('member', ['filter' => 'rbac:member,coordinator,treasurer', 'namespace' => 'App\Controllers\Member'], function($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('profile', 'Profile::index');
    // More member routes will be added later
});

// Candidate Routes - For registration completion
$routes->group('member/registration', ['filter' => 'auth', 'namespace' => 'App\Controllers\Member'], function($routes) {
    $routes->get('complete', 'Registration::complete');
    $routes->post('complete', 'Registration::processComplete');
    $routes->get('status', 'Registration::status');
});
