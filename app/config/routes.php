<?php

use app\controllers\DiscussionController;
use app\controllers\MessageController;
use app\controllers\ObjetController;
use app\controllers\CategorieController;
use app\controllers\ExchangeController;
use app\controllers\ObjetImgController;
use app\controllers\ObjetHistoryController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\controllers\UserController;


/** 
 * @var Router $router 
 * @var Engine $app
 */

$app = isset($app) ? $app : \Flight::app();

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
		if (($_SESSION['user_role'] ?? '') !== 'admin') {
			$app->redirect('/profile');
			return;
		}
		$userController = new UserController();
		$categorieController = new CategorieController();
		$objetController = new ObjetController();
		$exchangeController = new ExchangeController();
		$exchangeStats = $exchangeController->getStatusStats();
		
		$data = [
			'count_users' => $userController->getCountUser(),
			'count_admins' => $userController->getCountAdmin(),
			'count_categories' => $categorieController->getCount(),
			'count_objects' => $objetController->getCount(),
			'count_exchanges' => $objetController->getCountExchanges(),
			'exchange_stats' => $exchangeStats,
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
		$userController = new UserController();
		$role = Flight::request()->query['role'] ?? '';
		$search = Flight::request()->query['search'] ?? '';
		$options = [
			'exclude_id' => (int)($_SESSION['user_id'] ?? 0),
		];
		if (is_string($role) && $role !== '') {
			$options['role'] = $role;
		}
		if (is_string($search) && trim($search) !== '') {
			$options['search'] = trim($search);
		}
		$users = $userController->getAll($options);
		$exchangeController = new ExchangeController();
		$exchanges = $exchangeController->getAllWithRequestedObjectDetails();
		$app->render('tables', [
			'users' => $users,
			'filters' => [
				'role' => $role,
				'search' => $search,
			],
			'exchanges' => $exchanges,
		]);
	});

	$router->get('/detailsprofill/@id', function($id) use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->redirect('/');
			return;
		}

		$userController = new UserController();
		$objetController = new ObjetController();
		$exchangeController = new ExchangeController();

		$userId = (int)$id;
		$user = $userController->getById($userId);
		$objets = $objetController->getObjet_User($userId);
		$receivedStats = $exchangeController->getReceivedStatsByUser($userId);
		$sentStats = $exchangeController->getSentStatsByUser($userId);

		$app->render('Detailsprofill', [
			'user_profile' => $user,
			'objets' => $objets,
			'received_stats' => $receivedStats,
			'sent_stats' => $sentStats,
		]);
	});

	$router->get('/billing', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->redirect('/');
			return;
		}
		$exchangeModel = new \app\models\Exchange();
		$objetModel = new \app\models\Objet();
		$sent = $exchangeModel->getSentByUser((int)$_SESSION['user_id']);
		$transactions = $exchangeModel->getUserTransactions((int)$_SESSION['user_id'], 50);
		$partners = $exchangeModel->getPartnersCountByUser((int)$_SESSION['user_id'], 50);
		$objetStats = $objetModel->getStatsByUser((int)$_SESSION['user_id']);
		$app->render('billing', [
			'sent_exchanges' => $sent,
			'transactions' => $transactions,
			'partners' => $partners,
			'objet_stats' => $objetStats,
		]);
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
		$app->json($result);
	});

	// Recupere tous les categories
	$router->get('/api/getAll/categorie', function() use ($app){
		$categorieController = new CategorieController();
		$result = $categorieController->getAll();
		$app->json($result);
	});

	$router->get('/api/admin/categories', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json(['success' => false, 'message' => 'Non connecté']);
			return;
		}
		if (($_SESSION['user_role'] ?? '') !== 'admin') {
			$app->json(['success' => false, 'message' => 'Accès refusé']);
			return;
		}
		try {
			$categorieController = new CategorieController();
			$app->json($categorieController->index());
		} catch (\Throwable $e) {
			error_log('Error in GET /api/admin/categories - ' . $e->getMessage());
			$app->json(['success' => false, 'message' => "Erreur serveur (catégories)" ]);
		}
	});

	$router->post('/api/admin/categories', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json(['success' => false, 'message' => 'Non connecté']);
			return;
		}
		if (($_SESSION['user_role'] ?? '') !== 'admin') {
			$app->json(['success' => false, 'message' => 'Accès refusé']);
			return;
		}
		try {
			$json_input = file_get_contents('php://input');
			$data = json_decode($json_input, true) ?: [];
			$categorieController = new CategorieController();
			$app->json($categorieController->create($data));
		} catch (\Throwable $e) {
			error_log('Error in POST /api/admin/categories - ' . $e->getMessage());
			$app->json(['success' => false, 'message' => "Erreur serveur (création catégorie)" ]);
		}
	});

	$router->post('/api/admin/categories/@id', function($id) use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json(['success' => false, 'message' => 'Non connecté']);
			return;
		}
		if (($_SESSION['user_role'] ?? '') !== 'admin') {
			$app->json(['success' => false, 'message' => 'Accès refusé']);
			return;
		}
		try {
			$json_input = file_get_contents('php://input');
			$data = json_decode($json_input, true) ?: [];
			$categorieController = new CategorieController();
			$app->json($categorieController->update((int)$id, $data));
		} catch (\Throwable $e) {
			error_log('Error in POST /api/admin/categories/@id - ' . $e->getMessage());
			$app->json(['success' => false, 'message' => "Erreur serveur (modification catégorie)" ]);
		}
	});

	$router->post('/api/admin/categories/@id/archive', function($id) use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json(['success' => false, 'message' => 'Non connecté']);
			return;
		}
		if (($_SESSION['user_role'] ?? '') !== 'admin') {
			$app->json(['success' => false, 'message' => 'Accès refusé']);
			return;
		}
		try {
			$categorieController = new CategorieController();
			$app->json($categorieController->archive((int)$id));
		} catch (\Throwable $e) {
			error_log('Error in POST /api/admin/categories/@id/archive - ' . $e->getMessage());
			$app->json(['success' => false, 'message' => "Erreur serveur (archivage catégorie)" ]);
		}
	});

	// Recupere un user
	$router->get('/api/get/user/@id', function($id) use ($app){
		$userController = new UserController();
		$result = $userController->getById($id);
		$app->json($result);
	});

	// Recupere tous les users
	$router->get('/api/get/users', function() use ($app){
		$userController = new UserController();
		$role = Flight::request()->query['role'] ?? '';
		$search = Flight::request()->query['search'] ?? '';
		$options = [];
		if (!empty($_SESSION['user_id'])) {
			$options['exclude_id'] = (int)$_SESSION['user_id'];
		}
		if (is_string($role) && $role !== '') {
			$options['role'] = $role;
		}
		if (is_string($search) && trim($search) !== '') {
			$options['search'] = trim($search);
		}
		$result = $userController->getAll($options);
		$app->json($result);
	});

	$router->get('/api/get/exchanges', function() use ($app){
		$exchangeController = new ExchangeController();
		$status = Flight::request()->query['status'] ?? null;
		$result = $exchangeController->getAllWithRequestedObjectDetails($status);
		$app->json($result);
	});

	$router->get('/api/objets/others', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json([]);
			return;
		}
		$objetController = new ObjetController();
		$result = $objetController->getObjetsNotOwnedByCurrentUserJson();
		$app->json($result);
	});

	$router->get('/api/objets/others/search', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json([]);
			return;
		}
		$objetController = new ObjetController();
		$result = $objetController->getObjetsNotOwnedByCurrentUserSearchJson();
		$app->json($result);
	});

	$router->get('/api/objets/categories', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json([]);
			return;
		}
		$objetController = new ObjetController();
		$app->json($objetController->getCategoriesJson());
	});

	$router->get('/objets', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->redirect('/');
			return;
		}
		$objetController = new ObjetController();
		$objets = $objetController->getObjetsNotOwnedByCurrentUser(60, 0);
		$app->render('listes_objects', ['objets' => $objets]);
	});

	$router->get('/exchange', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->redirect('/');
			return;
		}
		$app->render('exchange', []);
	});


	$router->get('/api/exchange/target', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json(null);
			return;
		}
		$controller = new ExchangeController();
		$app->json($controller->getTargetObjetJson());
	});

	$router->get('/api/exchange/my-objets', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json([]);
			return;
		}
		$controller = new ExchangeController();
		$app->json($controller->getMyObjetsJson());
	});

	$router->post('/api/exchange/create', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json(['ok' => false, 'error' => 'Non connecté']);
			return;
		}
		$controller = new ExchangeController();
		$app->json($controller->createExchangeJson());
	});

	$router->get('/api/exchange/received', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json([]);
			return;
		}
		$controller = new ExchangeController();
		$app->json($controller->getReceivedExchangesJson());
	});

	$router->get('/api/exchange/stats/received', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json(null);
			return;
		}
		$controller = new ExchangeController();
		$app->json($controller->getReceivedStatsByUser((int)$_SESSION['user_id']));
	});

	$router->get('/api/exchange/stats/sent', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json(null);
			return;
		}
		$controller = new ExchangeController();
		$app->json($controller->getSentStatsByUser((int)$_SESSION['user_id']));
	});

	$router->post('/api/exchange/accept', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json(['ok' => false, 'error' => 'Non connecté']);
			return;
		}
		$controller = new ExchangeController();
		$app->json($controller->acceptExchangeJson());
	});

	$router->post('/api/exchange/refuse', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json(['ok' => false, 'error' => 'Non connecté']);
			return;
		}
		$controller = new ExchangeController();
		$app->json($controller->refuseExchangeJson());
	});

	// Qui recupere la liste des demandes en attente d'un user
	$router->get('/api/getExchange/attente/@id', function($id) use ($app){
		$exchangeController = new ExchangeController();
		$result = $exchangeController->EchangesAttente($id);
		$app->json($result);
	});

	// Routes pour les images d'objets
	$router->post('/api/objet/upload-image', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json(['ok' => false, 'error' => 'Non connecté']);
			return;
		}
		$controller = new ObjetImgController();
		$app->json($controller->uploadImageJson());
	});

	$router->get('/api/objet/images', function() use ($app) {
		$controller = new ObjetImgController();
		$app->json($controller->getImagesByObjetJson());
	});

	$router->get('/api/objet/first-image', function() use ($app) {
		$controller = new ObjetImgController();
		$app->json($controller->getFirstImageByObjetJson());
	});

	$router->post('/api/objet/delete-image', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->json(['ok' => false, 'error' => 'Non connecté']);
			return;
		}
		$controller = new ObjetImgController();
		$app->json($controller->deleteImageJson());
	});

	// Recuperer un objet par son id 
	$router->post('/api/get/objet', function() use ($app) {
		$objetController = new ObjetController();
		
		// Récupérer les données JSON du corps de la requête
		$json_input = file_get_contents('php://input');
		$data = json_decode($json_input, true);
		
		$result = $objetController->getObjetById($data['id_objet']);
		$app->json($result);
	});

	// Diriger vers fiche_objet.php
	$router->get('/view/objet/@id', function($id) use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->redirect('/');
			return;
		}
		$app->render('fiche_objet', ['id_objet' => $id]);
	});

	// Recuperer les images d'un objet 
	$router->get('/api/get/img/@id', function($id) use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->redirect('/');
			return;
		}
		$objetImgController = new ObjetImgController();
		$result = $objetImgController->getByObjet($id);
		$app->json($result);
	});

	// Recuperer les historiques d'un objet
	$router->get('/api/get/objethistory/@id', function($id) use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->redirect('/');
			return;
		}
		$objetHistoryController = new ObjetHistoryController();
		$result = $objetHistoryController->getObjetHistory($id);
		$app->json($result);
	});

	// Modifier un objet 
	$router->post('/api/update/objet/@id', function($id) use ($app) {		
		$objetController = new ObjetController();
		
		// Récupérer les données JSON du corps de la requête
		$json_input = file_get_contents('php://input');
		
		$data = json_decode($json_input, true);
		
		$result = $objetController->updateObjet($id, $data);
		
		$app->json($result);
	});

	// Rendre un objet inactif
	$router->post('/api/inactif/objet/@id', function($id) use ($app) {		
		$objetController = new ObjetController();
		
		$result = $objetController->update_inactif($id);
		
		$app->json($result);
	});

	// Logout
	$router->get('/logout', function() use ($app) {
		if (!isset($_SESSION['user_id'])) {
			$app->redirect('/');
			return;
		}
		$userController = new UserController();
		$result = $userController->logout($_SESSION['user_id']);
		if ($result) {
			$app->redirect('/');
			return;
		}
	});

}, [ SecurityHeadersMiddleware::class ]);
