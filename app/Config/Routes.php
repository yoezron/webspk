<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Public Routes
$routes->get('/', 'Home::index');

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
