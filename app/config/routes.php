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
		$app->render('sign-in', []);
	});


// route pour la page login : 
	$router->post('/login', [UserController::class, 'login']);

// route pour la page profile:
	$router->get('/profile', function() use ($app) {
		$app->render('profile', []);
	});

// route pour la page sign-up :
	$router->post('/sign', [UserController::class, 'register']);




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

	$router->get('/dashboard', function() use ($app) {
		$app->render('dashboard', []);
	});

	$router->get('/sign-up', function() use ($app) {
		$app->render('sign-up', []);
	});

	$router->get('/tables', function() use ($app) {
		$app->render('tables', []);
	});

	$router->get('/billing', function() use ($app) {
		$app->render('billing', []);
	});

	$router->get('/virtual-reality', function() use ($app) {
		$app->render('virtual-reality', []);
	});

	$router->get('/sign-in', function() use ($app) {
		$app->render('sign-in', []);
	});
	

	$router->get('/api/check-email', function() use ($app) {
		$email = Flight::request()->query['email'];
		if (!$email) {
			$app->json(['error' => 'Email parameter required']);
			return;
		}
		$userController = new UserController();
		$exists = $userController->checkEmailExists($email);
		$app->json(['exists' => $exists]);
	});
	
}, [ SecurityHeadersMiddleware::class ]);