STATISTIQUE : 
DATA :
-[ok] creation de 2026-02-10-01-donneF.sql :
    pour les donner de teste 


page : Dashboard.php :

-[ok] creation de met_user.js (pour fetch les json retourner apres)
   -> [ok] function getCountAdmin() 
   -> [ok] function getCountUser() 

-[ok] Ajouter une affichage pour :
  -> [ok] nombre de Admin :
    [ok] fonction : User.php -> getCountAdmin() ;
                
  -> [ok] nombre de User :
    [ok] fonction : User.php -> getCountUser() ;

  -> [ok] nombre de Categorie :
    [ok] fonction : Categorie.php -> getCountCategorie() ;

  -> [ok] nombre d objet :
    [ok] fonction : object.php -> getCountObject() ;
  
 - [ok] contrroler :
  -> [ok]  Ajouter des controller pour chaque fonction 
      - [ok] CategorieController.php 
      - [ok] UserController.php
      - [ok] objectController.php

 - [ok] routes.php :
  -> [ok] Ajouter les routes pour chaque fonction :
  

Liste des objects :
pages listes_objects.php : qui affcihera tout les liste des objects avec :
<div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                  <div class="card card-blog card-plain">
                    <div class="position-relative">
                      <a class="d-block">
                        <img src="/assets/img/home-decor-1.jpg" alt="img-blur-shadow" class="img-fluid shadow border-radius-md">
                      </a>
                    </div>
                    <div class="card-body px-1 pb-0">
                      <p class="text-secondary mb-0 text-sm">Project #2</p>
                      <a href="javascript:;">
                        <h5 class="font-weight-bolder">
                          Modern
                        </h5>
                      </a>
                      <p class="mb-4 text-sm">
                        As Uber works through a huge amount of internal management turmoil.
                      </p>
                      <div class="d-flex align-items-center justify-content-between">
                        <button type="button" class="btn btn-outline-primary btn-sm mb-0">View Project</button>
                        <div class="avatar-group mt-2">
                          <a href="javascript:;" class="avatar avatar-xs rounded-circle" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Elena Morison">
                            <img alt="Image placeholder" src="/assets/img/team-1.jpg">
                          </a>
                          <a href="javascript:;" class="avatar avatar-xs rounded-circle" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Ryan Milly">
                            <img alt="Image placeholder" src="/assets/img/team-2.jpg">
                          </a>
                          <a href="javascript:;" class="avatar avatar-xs rounded-circle" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Nick Daniel">
                            <img alt="Image placeholder" src="/assets/img/team-3.jpg">
                          </a>
                          <a href="javascript:;" class="avatar avatar-xs rounded-circle" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Peterson">
                            <img alt="Image placeholder" src="/assets/img/team-4.jpg">
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
ajout du liens de cette nouvelle page dans menu.php 

ajout dans header aussi 

pages Objet.php : fonctions qui prends toutes les objects qui n'appartiennent pas aux user connecter 

objectController.php : controlle qui appelle le fonctions dans jsp 

routes.php : gerer les chemins pour voir la listes des objects

pages met_object : pour l'affichage des json faites


pages exchange.php : enfaite voici mon but pour ce projet :
On veut mettre en place un site qui permet de faire des échanges d’objet : Takalo-takalo.
Les utilisateurs inscrits sur le site vont mettre en ligne leurs objets (vêtement, livre, DVD,
etc…). Ils vont voir les objets des autres utilisateurs et proposer un échange entre 2 objets.
Si l’autre utilisateur accepte, l’objet change de propriétaire

maintenant je voudrais une pages exhange.php pour proceder a une echange . l'echange ne se fera pas directement mais sera 
en attente d'abord et et si c'est accepter par le proprietaire de l'object ou on a fait la demande alors ca sera accepter et 
entrera dans la tables tk_objet_history

[] on ajoute un lien : exchange dans listes_objects.php qui envoie vers la page exchange :
[] dans la page exchange.php on aura a gauche l'object qu'on veut et au milieu un logo de trade et a droite on put defiler de haut en 
  bas nos objects et selectionner celle que l'on veut echanger a celui que l'on 
