<?php

use app\controllers\DiscussionController;
use app\controllers\MessageController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\controllers\UserController;


/** 
 * @var Router $router 
 * @var Engine $app
 */

// This wraps all routes in the group with the SecurityHeadersMiddleware
$router->group('', function(Router $router) use ($app) {
	/*
	$userController = new UserController();
	$discussionController = new DiscussionController();
	$messageController = new MessageController();
	*/


	$router->get('/', function() use ($app) {
		$app->render('dashboard', []);
	});

	$router->post('/', [UserController::class, 'login']);

	$router->get('/home', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->redirect('/');
			return;
		}
		$app->render('index', []);
	});

	$router->get('/users', function() use ($app) {
		$userController = new UserController();
		$users = $userController->getAll();
		$app->render('users', ['users' => $users]);
	});

	$router->get('/analytics', function() use ($app) {
		$app->render('analytics', []);
	});

	$router->get('/products', function() use ($app) {
		$app->render('products', []);
	});

	$router->get('/settings', function() use ($app) {
		$app->render('settings', []);
	});

	$router->get('/orders', function() use ($app) {
		$app->render('orders', []);
	});

	$router->get('/forms', function() use ($app) {
		$app->render('forms', []);
	});

	$router->get('/reports', function() use ($app) {
		$app->render('reports', []);
	});

	$router->get('/messages', function() use ($app) {
		// Vérifier que l'utilisateur est authentifié
		if (!isset($_SESSION['user_id'])) {
			$app->redirect('/');
			return;
		}
		
		$discussionController = new DiscussionController();
		$conversation = $discussionController->getDiscussions($_SESSION['user_id']);
		$app->render('messages', ['conversation' => $conversation]);
	});

	$router->get('/api/messages/@id', function($id) use ($app) {
		$messageController = new MessageController();
		$messages = $messageController->getByDiscussion($id);
		$app->json($messages);
	});

	$router->get('/calendar', function() use ($app) {
		$app->render('caledar', []);
	});

	$router->get('/files', function() use ($app) {
		$app->render('files', []);
	});
	
}, [ SecurityHeadersMiddleware::class ]);