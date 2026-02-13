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
[] lier le bouton 'Voir' de chaque objet du user connecter vers '/view/objet'{
  [] ajouter url dans routes
   - rediriger vers le view fiche_objet.php
  [] creer un view fiche_objet.php
}

/ 11 fevrier 2026 /
FICHE_OBJET:
[ok] afficher les informations de l objet{
  [ok] ajouter fonctions dans met_objet.js{
    [ok] getObjetById(id_objet)
     - faire appel a fecth vers '/api/getObjet/' avec data={id_objet}
     - return objet en json
  }
  [ok] ajouter url dans routes.php{
    [ok] '/api/getObjet/' avec data={id_objet}
     - appel getById() de ObjetController
     - return l objet en json
  } 
  [ok] recupere les informations de l objet {
    [ok] mettre dans une constante l objet obtenue
    [ok] afficher les informations de l objet
  }
  [ok] creer script met_objetHistory.js{
    [ok] fonction getHistoryById(id_objet)
     - faire appel a fecth vers '/api/getHistory/' avec data={id_objet}
     - return l historique en json
  }
  [ok] ajouter url dans routes.php{
    [ok] '/api/getHistory/' avec data={id_objet}
     - appel getById() de ObjetController
     - return l historique en json
  }
  [ok] ajouter fonction dans ficheObjet.js{
    [ok] loadObjet()
     - affiche le titre et categorie
    [ok] loadImg()
     - affiche ces images
    [ok] loadHistory()
     - affiche l historique de proprietaire
    [ok] loadProprio()
     - affiche les informations du proprio actuel
  }
}

[] ajouter plus de couleur et de style dans les pages que j ai creer{
  [] profile.php
  [] fiche_objet.php
} 


/ 12 fevrier 2026 /

[] ajouter fonctionnalite dans fiche_objet.php{
  [] ajouter 2 liens = +-10% et +-20%
  [] si on clique sur un lien -> afficher les objets qui ont un prix 
    dans la fourchette de +-10% ou +-20% du prix de l objet afficher
  [] ajouter fonction dans met_objet.js{
    [] getMargeObjet(id_objet, marge%)
      - faire appel a fecth vers '/api/getMarge/' avec data={id_objet, marge%}
      - return la liste des objet en json
  }
  [] ajouter url dans routes.php{
    [] '/api/getMarge/' avec data={id_objet, marge%}
     - appel getMarge() de ObjetController
     - return la liste des objet en json
  }
  [] ajouter fonction dans ficheObjet.js{
    [] loadMarge(marge%)
     - affiche les objets qui ont un prix dans la fourchette de 
       +-10% ou +-20% du prix de l objet afficher
     - montre le % de difference de prix entre objet du user et 
       objet de la liste 
     - met  un bouton 'Echanger' sur chaque objet afficher
      . cliquer sur 'Echanger'-> rediriger vers la page de demande d echange avec 
        l objet selectionner
  }
}