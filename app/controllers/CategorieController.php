<?php
// controllers/CategorieController.php

class CategorieController {

    private $categorieModel;

    public function __construct($db) {
        $this->categorieModel = new Categorie_model($db);
    }

    // Liste toutes les catégories (pour affichage public)
    public function index() {
        $categories = $this->categorieModel->getAllWithCount();
        // → on renvoie souvent à une vue
        return [
            'success' => true,
            'categories' => $categories,
            'title' => 'Catégories disponibles'
        ];
    }

    // API / JSON - pour AJAX ou futur frontend
    public function apiList() {
        $categories = $this->categorieModel->getAll();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $categories
        ]);
        exit;
    }

    // Formulaire création (admin)
    public function create($postData = []) {
        if (empty($postData)) {
            return ['success' => false, 'message' => 'Aucune donnée reçue'];
        }

        $nom = trim($postData['nom_categorie'] ?? '');

        if (empty($nom)) {
            return ['success' => false, 'message' => 'Le nom de la catégorie est requis'];
        }

        if (strlen($nom) > 200) {
            return ['success' => false, 'message' => 'Nom trop long (max 200 caractères)'];
        }

        $result = $this->categorieModel->create($nom);

        if ($result === -1) {
            return ['success' => false, 'message' => 'Cette catégorie existe déjà'];
        }

        if ($result === false) {
            return ['success' => false, 'message' => 'Erreur lors de la création'];
        }

        return [
            'success' => true,
            'message' => 'Catégorie créée avec succès',
            'id' => $result
        ];
    }

    // Modification (admin)
    public function update($id, $postData = []) {
        if (empty($postData)) {
            return ['success' => false, 'message' => 'Aucune donnée reçue'];
        }

        $nom = trim($postData['nom_categorie'] ?? '');

        if (empty($nom)) {
            return ['success' => false, 'message' => 'Le nom est requis'];
        }

        $result = $this->categorieModel->update($id, $nom);

        if ($result === -1) {
            return ['success' => false, 'message' => 'Ce nom est déjà utilisé'];
        }

        if ($result === false) {
            return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
        }

        return ['success' => true, 'message' => 'Catégorie modifiée'];
    }

    // Suppression (admin)
    public function delete($id) {
        $result = $this->categorieModel->delete($id);

        if ($result === -2) {
            return ['success' => false, 'message' => 'Impossible de supprimer : des objets sont encore dans cette catégorie'];
        }

        if ($result === false) {
            return ['success' => false, 'message' => 'Erreur lors de la suppression'];
        }

        return ['success' => true, 'message' => 'Catégorie supprimée'];
    }

    // Pour un select dans un formulaire d'ajout d'objet
    public function getForSelect() {
        $cats = $this->categorieModel->getAll('nom_categorie ASC');
        $options = [];
        foreach ($cats as $cat) {
            $options[$cat['id_categorie']] = $cat['nom_categorie'];
        }
        return $options;
    }
}