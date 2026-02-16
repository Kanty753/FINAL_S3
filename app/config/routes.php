<?php

use app\controllers\RegionController;
use app\controllers\VilleController;
use app\controllers\ArticleController;
use app\controllers\TypeBesoinController;
use app\controllers\BesoinController;
use app\controllers\DonController;
use app\controllers\DispatchController;
use app\controllers\DashboardController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function (Router $router) use ($app) {

	// Redirection de l'accueil vers le tableau de bord
	$router->get('/', function () use ($app) {
		$app->redirect('/dashboard');
	});

	// ===========================
	// Pages HTML (Vues)
	// ===========================
	$router->get('/dashboard', [DashboardController::class, 'page']);

	$router->get('/regions', [RegionController::class, 'page']);
	$router->get('/regions/create', [RegionController::class, 'createPage']);
	$router->post('/regions', [RegionController::class, 'store']);

	$router->get('/villes', [VilleController::class, 'page']);
	$router->get('/villes/create', [VilleController::class, 'createPage']);
	$router->post('/villes', [VilleController::class, 'store']);

	$router->get('/articles', [ArticleController::class, 'page']);
	$router->get('/articles/create', [ArticleController::class, 'createPage']);
	$router->post('/articles', [ArticleController::class, 'store']);

	$router->get('/besoins', [BesoinController::class, 'page']);
	$router->get('/besoins/create', [BesoinController::class, 'createPage']);
	$router->post('/besoins', [BesoinController::class, 'store']);

	$router->get('/dons', [DonController::class, 'page']);
	$router->get('/dons/create', [DonController::class, 'createPage']);
	$router->post('/dons', [DonController::class, 'store']);

	$router->get('/dispatches', [DispatchController::class, 'page']);
	$router->get('/dispatches/create', [DispatchController::class, 'createPage']);
	$router->post('/dispatches', [DispatchController::class, 'store']);
	$router->post('/dispatches/simuler', [DispatchController::class, 'simulerPage']);
	$router->post('/dispatches/reset', [DispatchController::class, 'resetPage']);

	// ===========================
	// API BNGRC — Tableau de bord
	// ===========================
	$router->get('/api/dashboard', [DashboardController::class, 'index']);

	// ===========================
	// API BNGRC — Régions
	// ===========================
	$router->group('/api/regions', function () use ($router) {
		$router->get('', [RegionController::class, 'index']);
		$router->get('/@id:[0-9]+', [RegionController::class, 'show']);
		$router->post('', [RegionController::class, 'create']);
		$router->put('/@id:[0-9]+', [RegionController::class, 'update']);
		$router->delete('/@id:[0-9]+', [RegionController::class, 'destroy']);
	});

	// ===========================
	// API BNGRC — Villes
	// ===========================
	$router->group('/api/villes', function () use ($router) {
		$router->get('', [VilleController::class, 'index']);
		$router->get('/@id:[0-9]+', [VilleController::class, 'show']);
		$router->post('', [VilleController::class, 'create']);
		$router->put('/@id:[0-9]+', [VilleController::class, 'update']);
		$router->delete('/@id:[0-9]+', [VilleController::class, 'destroy']);
	});

	// ===========================
	// API BNGRC — Types de besoins
	// ===========================
	$router->group('/api/types-besoins', function () use ($router) {
		$router->get('', [TypeBesoinController::class, 'index']);
		$router->get('/@id:[0-9]+', [TypeBesoinController::class, 'show']);
		$router->post('', [TypeBesoinController::class, 'create']);
		$router->put('/@id:[0-9]+', [TypeBesoinController::class, 'update']);
		$router->delete('/@id:[0-9]+', [TypeBesoinController::class, 'destroy']);
	});

	// ===========================
	// API BNGRC — Articles
	// ===========================
	$router->group('/api/articles', function () use ($router) {
		$router->get('', [ArticleController::class, 'index']);
		$router->get('/@id:[0-9]+', [ArticleController::class, 'show']);
		$router->post('', [ArticleController::class, 'create']);
		$router->put('/@id:[0-9]+', [ArticleController::class, 'update']);
		$router->delete('/@id:[0-9]+', [ArticleController::class, 'destroy']);
	});

	// ===========================
	// API BNGRC — Besoins
	// ===========================
	$router->group('/api/besoins', function () use ($router) {
		$router->get('', [BesoinController::class, 'index']);
		$router->get('/par-ville', [BesoinController::class, 'parVille']);
		$router->get('/@id:[0-9]+', [BesoinController::class, 'show']);
		$router->post('', [BesoinController::class, 'create']);
		$router->put('/@id:[0-9]+', [BesoinController::class, 'update']);
		$router->delete('/@id:[0-9]+', [BesoinController::class, 'destroy']);
	});

	// ===========================
	// API BNGRC — Dons
	// ===========================
	$router->group('/api/dons', function () use ($router) {
		$router->get('', [DonController::class, 'index']);
		$router->get('/disponibles', [DonController::class, 'disponibles']);
		$router->get('/etat', [DonController::class, 'etat']);
		$router->get('/@id:[0-9]+', [DonController::class, 'show']);
		$router->post('', [DonController::class, 'create']);
		$router->put('/@id:[0-9]+', [DonController::class, 'update']);
		$router->delete('/@id:[0-9]+', [DonController::class, 'destroy']);
	});

	// ===========================
	// API BNGRC — Dispatches
	// ===========================
	$router->group('/api/dispatches', function () use ($router) {
		$router->get('', [DispatchController::class, 'index']);
		$router->get('/par-ville', [DispatchController::class, 'parVille']);
		$router->get('/@id:[0-9]+', [DispatchController::class, 'show']);
		$router->post('', [DispatchController::class, 'create']);
		$router->post('/simuler', [DispatchController::class, 'simuler']);
		$router->delete('/@id:[0-9]+', [DispatchController::class, 'destroy']);
		$router->delete('', [DispatchController::class, 'destroyAll']);
	});

}, [SecurityHeadersMiddleware::class]);