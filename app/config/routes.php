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
	
}, [ SecurityHeadersMiddleware::class ]);