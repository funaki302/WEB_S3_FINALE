/ 10 fevrier 2026 /
PROFILE :
[] afficher les informations du user connecter
[] afficher la liste de tous ses objets{

  [ok] creer un script met_objet.js{
    [ok] fonction getObjet_User(id_user)
      - faire appel a fecth vers '/api/getObjet/@id_user'
      - return liste des objet json
    [] fonction addObjet(data)
     - fecth vers '/api/add/objet'
     - avec action 'post' et data
     - return true => insertion reussi
  }

  [] creer un script met_categorie{
    [] fonction getAllCategorie()
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
    [] fonction createObjet(data)
     
  }
}