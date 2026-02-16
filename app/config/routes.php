<?php

use app\controllers\ApiExampleController;
<<<<<<< HEAD
use app\controllers\DashboardController;
use app\controllers\VilleController;
use app\controllers\BesoinController;
use app\controllers\DonController;
use app\controllers\DispatchController;
use app\controllers\RegionController;
use app\controllers\ArticleController;
=======
>>>>>>> 46677c7 (main template)
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function (Router $router) use ($app) {

<<<<<<< HEAD
	// Page d'accueil -> redirige vers le dashboard
	$router->get('/', function () use ($app) {
		$app->redirect('/dashboard');
	});

	// ========================
	// DASHBOARD
	// ========================
	$router->get('/dashboard', [DashboardController::class, 'index']);

	// ========================
	// REGIONS
	// ========================
	$router->get('/regions', [RegionController::class, 'index']);
	$router->get('/regions/create', [RegionController::class, 'create']);
	$router->post('/regions', [RegionController::class, 'store']);

	// ========================
	// VILLES
	// ========================
	$router->get('/villes', [VilleController::class, 'index']);
	$router->get('/villes/create', [VilleController::class, 'create']);
	$router->post('/villes', [VilleController::class, 'store']);

	// ========================
	// ARTICLES
	// ========================
	$router->get('/articles', [ArticleController::class, 'index']);
	$router->get('/articles/create', [ArticleController::class, 'create']);
	$router->post('/articles', [ArticleController::class, 'store']);

	// ========================
	// BESOINS
	// ========================
	$router->get('/besoins', [BesoinController::class, 'index']);
	$router->get('/besoins/create', [BesoinController::class, 'create']);
	$router->post('/besoins', [BesoinController::class, 'store']);
	$router->post('/besoins/delete/@id', [BesoinController::class, 'delete']);

	// ========================
	// DONS
	// ========================
	$router->get('/dons', [DonController::class, 'index']);
	$router->get('/dons/create', [DonController::class, 'create']);
	$router->post('/dons', [DonController::class, 'store']);
	$router->post('/dons/delete/@id', [DonController::class, 'delete']);

	// ========================
	// DISPATCHES
	// ========================
	$router->get('/dispatches', [DispatchController::class, 'index']);
	$router->get('/dispatches/create', [DispatchController::class, 'create']);
	$router->post('/dispatches', [DispatchController::class, 'store']);
	$router->post('/dispatches/simuler', [DispatchController::class, 'simuler']);

	// ========================
	// API (existante)
	// ========================
=======
	$router->get('/', function () use ($app) {
		$app->render('welcome', ['message' => 'Niova ve? You are gonna do great things!']);
	});

	$router->get('/route-iray', function () {
		echo '<h1>Route iray ve!</h1>';
	});

	$router->get('/hello-world/@name', function ($name) {
		echo '<h1>Hello world! Oh hey ' . $name . '!</h1>';
	});

>>>>>>> 46677c7 (main template)
	$router->group('/api', function () use ($router) {
		$router->get('/users', [ApiExampleController::class, 'getUsers']);
		$router->get('/users/@id:[0-9]', [ApiExampleController::class, 'getUser']);
		$router->post('/users/@id:[0-9]', [ApiExampleController::class, 'updateUser']);
	});

}, [SecurityHeadersMiddleware::class]);