/ 10 fevrier 2026 /
PROFILE :
[ok] afficher les informations du user connecter (en tete)
[ok] afficher la liste de tous ses objets{

  [ok] creer un script met_objet.js{
    [ok] fonction getObjet_User(id_user)
      - faire appel a fecth vers '/api/getObjet/@id_user'
      - return liste des objet json
    [ok] fonction addObjet(data)
     - fecth vers '/api/add/objet'
     - avec action 'post' et data
     - return true => insertion reussi
  }

  [ok] creer un script met_categorie{
    [ok] fonction getAllCategorie()
     - fecth vers '/api/getAll/categorie'
     - return liste des categorie en json
  }

  [ok] creer un script profile.js{
    [ok] fonction loadListObjet(listObjet)
     - affiche les objets recuperer par getObjet_User(id_user)
     - mettre une partie 'Nouvel objet' ou le user peux ajouter un nouvel objet
     - lorsqu on clique sur le div 'New Objet', on appel la fonction loadNewObjet()
    [ok] fonction loadNewObjet()
     - formulaire pour ajouter un nouvel objet
      . title, categorie, description, prix
     - apres validation on appel la fonction createObjet(data)
    [ok] fonction createObjet(data)
     - recupre les informations obtenue par le formulaire de newObjet 
     - fecth vers '/api/add/objet' avec les donnees et methode 'post'
     - afficher le resultat de createObjet() {error / succes}
     - refrech la liste des objets
  }

  [ok] ajouter dans routes.php{
    [ok] '/api/getObjet/@id_user'
     - appel getObjet_User() de ObjetController
     - return la liste en json
    [ok] '/api/getAll/categorie'
     - appel getAll() de CategorieController
     - return en json la liste
    [ok] '/api/add/objet'
     - appel create() de ObjetController
     - return true / false
  }
}

[ok] afficher les informations du user{
  [ok] ajouter fonctions dans profile.js{
    [ok] loadInformation()
     - affiche les informations du user 
      (nom, email, role, status, tel, date inscription)

  }
  [ok] creer un script met_user.js{
    [ok] creer fonction getUserById(id_user)
     - faire appel a fecth vers '/api/getUser/@id_user'
     - return le user en json
  }
  [ok] ajouter dans routes.php{
    [ok] '/api/getUser/@id_user'
     - appel getById() de UserController
     - return le user en json
  }
}

[ok] afficher la liste des demandes en attente{
  [ok] ajouter fonctions dans profile.js{
    [ok] mettre dans une constante la listeDemande obtenue
    [ok] fonction loadDemande(listeDemande)
     - affiche la liste des utilisateurs qui ont fais une demande 
      d echange avec les objets du user connecter
     - mettre un lien 'Voir plus' pour afficher les details
    [ok] fonction voirPlus()
     - affiche l'image de l'objet proposer et l'image de l'objet requise 
     - afficher la date de la demande
     - mettre un bouton 'Accepter' et 'Refuser'
     - lorsqu on clique sur 'Accepter' on appel la fonction acceptEchange()
     - lorsqu on clique sur 'Refuser' on appel la fonction refusEchange()
    
  }
  [ok] script met_exchange.js{
    [ok] fonction EchangesAttente(id_user)
      - fetch vers (`/api/getExchange/attente/${id_user}`)
      - return la liste en json
    [ok] fonction acceptEchange(id_echange)
     - fecth vers '/api/exchange/accept'
     - return true / false
    [ok] fonction refusEchange(id_echange)
     - fecth vers '/api/exchange/refuse'
     - return true / false
  }
}
