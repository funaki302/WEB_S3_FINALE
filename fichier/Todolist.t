/ 10 fevrier 2026 /
PROFILE :
[] afficher les informations du user connecter
[] afficher la liste de tous ses objets{
  [] creer un script met_objet.js{
    [] fonction getObjet_User(id_user)
      - faire appel a fecth vers '/api/getObjet/@id_user'
      - return json
    [] fonction loadListObjet(listObjet)
     - affiche les objets recuperer par getObjet_User(id_user)
     - mettre une partie 'Nouvel objet' ou le user peux ajouter un nouvel objet
  }
  [] creer un script profile.js{
    [] fonction loadListObjet(listObjet)
     - affiche les objets recuperer par getObjet_User(id_user)
     - mettre une partie 'Nouvel objet' ou le user peux ajouter un nouvel objet
  }
}