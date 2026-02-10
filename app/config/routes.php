<?php

use app\controllers\DiscussionController;
use app\controllers\MessageController;
use app\controllers\ObjetController;
use app\controllers\CategorieController;
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
		if (!isset($_SESSION['user_id'])) {
			$app->redirect('/');
			return;
		}
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
		if (!isset($_SESSION['user_id'])) {
			$app->redirect('/');
			return;
		}
		$userController = new UserController();
		$categorieController = new CategorieController(Flight::db());
		$objetController = new ObjetController();
		
		$data = [
			'count_users' => $userController->getCountUser(),
			'count_admins' => $userController->getCountAdmin(),
			'count_categories' => $categorieController->getCount(),
			'count_objects' => $objetController->getCount(),
			'count_exchanges' => $objetController->getCountExchanges()
		];
		
		$app->render('dashboard', $data);
	});

	$router->get('/sign-up', function() use ($app) {
		$app->render('sign-up', []);
	});

	$router->get('/tables', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->redirect('/');
			return;
		}
		$app->render('tables', []);
	});

	$router->get('/billing', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->redirect('/');
			return;
		}
		$app->render('billing', []);
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

	// Prendre les objet d'un user
	$router->get('/api/getObjet/@id', function($id) use ($app){
		$objetController = new ObjetController();
		$result = $objetController->getObjet_User($id);
		$app->json($result);
	});

	// Ajout de nouvel objet
	$router->post('/api/add/objet', function() use ($app) {
		$objetController = new ObjetController();
		
		// Récupérer les données JSON du corps de la requête
		$json_input = file_get_contents('php://input');
		$data = json_decode($json_input, true);
		
		$result = $objetController->create($data);
		$app->json(['success' => $result !== false, 'id_objet' => $result]);
	});

	// Recupere tous les categories
	$router->get('/api/getAll/categorie', function() use ($app){
		$categorieController = new CategorieController();
		$result = $categorieController->getAll();
		$app->json($result);
	});

	// Recupere un user
	$router->get('/api/get/user/@id', function($id) use ($app){
		$userController = new UserController();
		$result = $userController->getById($id);
		$app->json($result);
	});

	
}, [ SecurityHeadersMiddleware::class ]);