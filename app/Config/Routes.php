<?php
namespace Config;
use CodeIgniter\Router\RouteCollection;
/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true); // Aktifkan auto-routing (kemudahan akses)

// Alias untuk base_url() helper (membuang index.php dari URL)
$routes->addRedirect('index.php', '/');