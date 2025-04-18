<?php
/**
 * Admin Panel Entry Point
 * 
 * This file serves as the main entry point for the admin panel of the application.
 * It initializes the admin panel, loads configuration, sets up the router,
 * and handles all admin panel requests.
 */

// Start session
@ini_set('session.save_path', sys_get_temp_dir());
session_start();

// Define the application root path
define('APP_ROOT', dirname(__DIR__));
define('ADMIN_ROOT', __DIR__);

// Set default timezone
date_default_timezone_set('UTC');

// Load configuration
require_once APP_ROOT . '/config/config.php';
require_once APP_ROOT . '/config/database.php';

// Load helper functions
require_once APP_ROOT . '/includes/helpers.php';
require_once ADMIN_ROOT . '/includes/admin_helpers.php';

// Load core classes
require_once APP_ROOT . '/classes/Database.php';
require_once APP_ROOT . '/classes/Router.php';
require_once ADMIN_ROOT . '/classes/Admin.php';
require_once ADMIN_ROOT . '/classes/AdminAuth.php';

// Initialize database connection
try {
    $dbConnection = new DatabaseConnection();
    $conn = $dbConnection->getConnection();
} catch (Exception $e) {
    // Log the error
    error_log("Database connection error: " . $e->getMessage());
    
    // Continue without database connection
    $conn = null;
}

// Initialize router
$router = new Router();

// Check if admin is logged in
$adminAuth = new AdminAuth();
$isLoggedIn = $adminAuth->isLoggedIn();

// Define routes
if (!$isLoggedIn && !in_array($_SERVER['REQUEST_URI'], ['/admin/login', '/admin/auth'])) {
    // Redirect to login page if not logged in
    header('Location: /admin/login');
    exit;
}

// Public routes (no authentication required)
$router->addRoute('/admin/login', 'AdminAuthController@showLogin');
$router->addRoute('/admin/auth', 'AdminAuthController@login', 'POST');

// Protected routes (authentication required)
$router->addRoute('/admin', 'AdminDashboardController@index');
$router->addRoute('/admin/logout', 'AdminAuthController@logout');

// Users management
$router->addRoute('/admin/users', 'AdminUsersController@index');
$router->addRoute('/admin/users/create', 'AdminUsersController@create');
$router->addRoute('/admin/users/store', 'AdminUsersController@store', 'POST');
$router->addRoute('/admin/users/view/{id}', 'AdminUsersController@view');
$router->addRoute('/admin/users/edit/{id}', 'AdminUsersController@edit');
$router->addRoute('/admin/users/update/{id}', 'AdminUsersController@update', 'POST');
$router->addRoute('/admin/users/delete/{id}', 'AdminUsersController@delete');

// Courses management
$router->addRoute('/admin/courses', 'AdminCoursesController@index');
$router->addRoute('/admin/courses/create', 'AdminCoursesController@create');
$router->addRoute('/admin/courses/store', 'AdminCoursesController@store', 'POST');
$router->addRoute('/admin/courses/view/{id}', 'AdminCoursesController@view');
$router->addRoute('/admin/courses/edit/{id}', 'AdminCoursesController@edit');
$router->addRoute('/admin/courses/update/{id}', 'AdminCoursesController@update', 'POST');
$router->addRoute('/admin/courses/delete/{id}', 'AdminCoursesController@delete');

// Exams management
$router->addRoute('/admin/exams', 'AdminExamController@index');
$router->addRoute('/admin/exams/create', 'AdminExamController@create');
$router->addRoute('/admin/exams/store', 'AdminExamController@store', 'POST');
$router->addRoute('/admin/exams/view/{id}', 'AdminExamController@view');
$router->addRoute('/admin/exams/edit/{id}', 'AdminExamController@edit');
$router->addRoute('/admin/exams/update/{id}', 'AdminExamController@update', 'POST');
$router->addRoute('/admin/exams/delete/{id}', 'AdminExamController@delete');

// Questions management
$router->addRoute('/admin/questions', 'AdminQuestionController@index');
$router->addRoute('/admin/questions/create', 'AdminQuestionController@create');
$router->addRoute('/admin/questions/store', 'AdminQuestionController@store', 'POST');
$router->addRoute('/admin/questions/view/{id}', 'AdminQuestionController@view');
$router->addRoute('/admin/questions/edit/{id}', 'AdminQuestionController@edit');
$router->addRoute('/admin/questions/update/{id}', 'AdminQuestionController@update', 'POST');
$router->addRoute('/admin/questions/delete/{id}', 'AdminQuestionController@delete');

// Admin profile
$router->addRoute('/admin/profile', 'AdminProfileController@index');
$router->addRoute('/admin/profile/update', 'AdminProfileController@update', 'POST');
$router->addRoute('/admin/profile/password', 'AdminProfileController@updatePassword', 'POST');

// Handle 404 errors
$router->setNotFoundHandler(function() {
    http_response_code(404);
    include ADMIN_ROOT . '/templates/404.php';
});

// Load controllers
require_once ADMIN_ROOT . '/controllers/AdminAuthController.php';
require_once ADMIN_ROOT . '/controllers/AdminDashboardController.php';
require_once ADMIN_ROOT . '/controllers/AdminUsersController.php';
require_once ADMIN_ROOT . '/controllers/AdminCoursesController.php';
require_once ADMIN_ROOT . '/controllers/AdminExamController.php';
require_once ADMIN_ROOT . '/controllers/AdminQuestionController.php';
require_once ADMIN_ROOT . '/controllers/AdminProfileController.php';

// Dispatch the request
$router->dispatch();
?>