<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/signup','Home::Signup');

$routes->post('/signup','Home::Signup');
$routes->get('/login','Home::Login');
$routes->post('/login','Home::Login');
$routes->get('/dashboard','Home::Dashboard');
$routes->get('/logout', 'Home::logout');
$routes->get('/delete/(:num)/(:any)', 'Home::deleteUser/$1/$2');
$routes->delete('/delete/(:num)/(:any)', 'Home::deleteUser/$1/$2');
$routes->get('/user-upload', 'Home::index1');
$routes->post('/user-upload/upload', 'Home::upload');
$routes->get('/admin-dashboard', 'Home::admin');  // Route for admin dashboard
$routes->get('/team-dashboard', 'Home::team');  // Route for team member dashboard

$routes->get('/login', 'AuthController::loginView'); // Route to display the login form
$routes->post('/auth/login', 'AuthController::login'); // Route to process the login request


$routes->post('/update', 'Home::update');

