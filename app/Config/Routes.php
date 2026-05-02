<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'DashboardController::index', ['filter' => 'auth']);

$routes->group('auth', static function($routes) {
    $routes->get('login', 'Auth\LoginController::index', ['filter' => 'guest']);
    $routes->post('login', 'Auth\LoginController::attemptLogin', ['filter' => 'guest']);
    $routes->post('logout', 'Auth\LoginController::logout', ['filter' => 'auth']);
    $routes->get('change-password', 'Auth\PasswordController::change', ['filter' => 'auth']);
    $routes->post('change-password', 'Auth\PasswordController::update', ['filter' => 'auth']);
});

$routes->group('master', ['filter' => 'auth'], static function($routes) {
    $routes->get('unit-kerja', 'Master\UnitKerjaController::index', ['filter' => 'permission:unit_kerja.view']);
    $routes->get('unit-kerja/(:segment)', 'Master\UnitKerjaController::show/$1', ['filter' => 'permission:unit_kerja.view']);
    $routes->get('penanggung-jawab', 'Master\PenanggungJawabController::index', ['filter' => 'permission:penanggung_jawab.view']);
    $routes->get('penanggung-jawab/(:segment)', 'Master\PenanggungJawabController::show/$1', ['filter' => 'permission:penanggung_jawab.view']);

    $routes->resource('sub-units', ['controller' => 'Master\SubUnitController']);
    $routes->resource('gedung', ['controller' => 'Master\GedungController']);
    $routes->get('lantai/by-gedung/(:num)', 'Master\LantaiController::byGedung/$1');
    $routes->resource('lantai', ['controller' => 'Master\LantaiController']);
    $routes->resource('ruangan', ['controller' => 'Master\RuanganController']);
    $routes->resource('kategori', ['controller' => 'Master\KategoriController']);
    $routes->resource('sub-kategori', ['controller' => 'Master\SubKategoriController']);
    $routes->resource('golongan', ['controller' => 'Master\GolonganController']);
    $routes->resource('merk', ['controller' => 'Master\MerkController']);
    $routes->resource('type', ['controller' => 'Master\TypeController']);
    $routes->resource('kondisi-barang', ['controller' => 'Master\KondisiBarangController']);
    $routes->resource('sumber-dana', ['controller' => 'Master\SumberDanaController']);
    $routes->get('aset/get-sub-kategori/(:num)', 'Master\AsetController::getSubKategoriByKategori/$1');
    $routes->get('aset/get-golongan/(:num)', 'Master\AsetController::getGolonganByKategori/$1');
    $routes->get('aset/get-type/(:num)', 'Master\AsetController::getTypeByMerk/$1');
    $routes->get('aset/get-sub-unit/(:num)', 'Master\AsetController::getSubUnitByUnitKerja/$1');
    $routes->get('aset/get-ruangan/(:num)', 'Master\AsetController::getRuanganBySubUnitJson/$1');
    $routes->get('aset/lookup-sub-kategori/(:num)', 'Master\AsetController::lookupSubKategori/$1');
    $routes->resource('aset', ['controller' => 'Master\AsetController']);
});

$routes->group('system', ['filter' => 'auth'], static function($routes) {
    $routes->resource('users', ['controller' => 'System\UserController']);
    $routes->resource('roles', ['controller' => 'System\RoleController']);
    $routes->resource('permissions', ['controller' => 'System\PermissionController']);
});

// Search API endpoints for Select2 autocomplete
$routes->get('search/ruangan', 'SearchController::searchRuangan', ['filter' => 'auth']);
$routes->get('search/penanggung-jawab', 'SearchController::searchPenanggungJawab', ['filter' => 'auth']);
$routes->get('search/unit-kerja', 'SearchController::searchUnitKerja', ['filter' => 'auth']);

// Legacy routes for compatibility - should be removed eventually
$routes->get('admin/dashboard', 'DashboardController::index', ['filter' => 'auth']);
$routes->get('subunit', 'Master\SubUnitController::index', ['filter' => 'auth']);
$routes->get('subunit/create', 'Master\SubUnitController::create', ['filter' => 'auth']);
$routes->post('subunit/store', 'Master\SubUnitController::store', ['filter' => 'auth']);
$routes->get('subunit/edit/(:num)', 'Master\SubUnitController::edit', ['filter' => 'auth']);
$routes->post('subunit/update/(:num)', 'Master\SubUnitController::update', ['filter' => 'auth']);
$routes->post('subunit/delete/(:num)', 'Master\SubUnitController::delete', ['filter' => 'auth']);
$routes->get('gedung', 'Master\GedungController::index', ['filter' => 'auth']);
$routes->get('gedung/create', 'Master\GedungController::create', ['filter' => 'auth']);
$routes->post('gedung/store', 'Master\GedungController::store', ['filter' => 'auth']);
$routes->get('gedung/edit/(:num)', 'Master\GedungController::edit', ['filter' => 'auth']);
$routes->post('gedung/update/(:num)', 'Master\GedungController::update', ['filter' => 'auth']);
$routes->post('gedung/delete/(:num)', 'Master\GedungController::delete', ['filter' => 'auth']);
