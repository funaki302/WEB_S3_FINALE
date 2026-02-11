/* function buildUrl(path) {
    const baseUrl = (document.querySelector('meta[name="base-url"]')?.content || '').replace(/\/$/, '');
    if (!baseUrl) return path;
    if (path.startsWith('/')) return `${baseUrl}${path}`;
    return `${baseUrl}/${path}`;
}
*/
function getCurrentUserId() {
    const meta = document.querySelector('meta[name="user-id"]');
    const id = meta ? parseInt(meta.content || '0', 10) : 0;
    return Number.isFinite(id) ? id : 0;
}

window.addEventListener('load', async function () {
    try {
      const id_user = getCurrentUserId();
      if (!id_user) {
        alert('id_user manquant');
      }

      // Ses informations
      const info = await getUserById(id_user);
      loadInformation(info);

      // Liste de demandes en attente
      const listeDemande = await EchangesAttente(id_user);
      loadListDemande(listeDemande);

      // Liste de ses objets
      const liste = await getObjet_User(id_user);
      await loadListObjet(liste);

    } catch (e) {
      this.alert('Erreur chargement de la page :'+ (e && e.message ? e.message : String(e)));
    }
});

async function loadListObjet(liste) {
  try {
    
    const listObjet = document.querySelector('#liste');
    if (!listObjet) {
      alert("Div #liste non trouvé");
      return;
    }
    
    // effacer le contenu du div
    listObjet.innerHTML = "";
    
    // Vérifier si liste est null ou undefined
    if (!liste) {
      console.warn('liste est null ou undefined');
      liste = [];
    }
    
    // ajouter la liste des objets
    if (Array.isArray(liste) && liste.length > 0) {
      liste.forEach((objet, index) => {
        
        const col = document.createElement('div');
        col.classList.add('col-xl-3', 'col-md-6', 'mb-xl-0', 'mb-4');
        col.innerHTML = `
          <div class="card card-blog card-plain">
            <div class="position-relative">
              <a class="d-block">
                <img src="" alt="" class="img-fluid shadow border-radius-md">
              </a>
            </div>
            <div class="card-body px-1 pb-0">
              <p class="text-secondary mb-0 text-sm">${objet.prix_estime || '0'} Ar</p>
              <a href="javascript:;">
                <h5 class="font-weight-bolder">
                  ${objet.title || 'Sans titre'}
                </h5>
              </a>
              <p class="mb-4 text-sm">
                ${objet.description || 'Pas de description'}
              </p>
              <div class="d-flex align-items-center justify-content-between">
                <button type="button" class="btn btn-outline-primary btn-sm mb-0">Voir</button>
                <div class="avatar-group mt-2">
                  <a href="javascript:;" class="avatar avatar-xs rounded-circle" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Elena Morison">
                    <img alt="Image placeholder" src="../assets/img/team-1.jpg">
                  </a>
                  <a href="javascript:;" class="avatar avatar-xs rounded-circle" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Ryan Milly">
                    <img alt="Image placeholder" src="../assets/img/team-2.jpg">
                  </a>
                  <a href="javascript:;" class="avatar avatar-xs rounded-circle" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Nick Daniel">
                    <img alt="Image placeholder" src="../assets/img/team-3.jpg">
                  </a>
                  <a href="javascript:;" class="avatar avatar-xs rounded-circle" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Peterson">
                    <img alt="Image placeholder" src="../assets/img/team-4.jpg">
                  </a>
                </div>
              </div>
            </div>
          </div>
        `;
        
        listObjet.appendChild(col);
      });
    }
    
    // Ajouter le bouton "New Objet" à la fin
    const newObjet = document.createElement('div');
    newObjet.classList.add('col-xl-3', 'col-md-6', 'mb-xl-0', 'mb-4');
    newObjet.id = "newObjet";
    newObjet.innerHTML = `
      <div class="card h-100 card-plain border">
        <div class="card-body d-flex flex-column justify-content-center text-center">
          <a href="javascript:;">
            <i class="fa fa-plus text-secondary mb-3"></i>
            <h5 class="text-secondary">New Objet</h5>
          </a>
        </div>
      </div>
    `;
    
    listObjet.appendChild(newObjet);
    
    // Ajouter l'événement click sur le bouton "New Objet"
    const addObjet = document.querySelector('#newObjet');
    if (addObjet) {
      addObjet.addEventListener('click', async function (e) {
        e.preventDefault();
        await loadNewObjet();
      });
    }
    
  } catch (error) {
    alert("Error de loadListObjet: " + error.message);
  }
}

async function loadNewObjet() {
  const div_newObjet = document.querySelector('#form-newObjet');
  if (!div_newObjet) {
    alert("Div form-newObjet non trouvé");
    return;
  }
  
  // vider le contenu
  div_newObjet.innerHTML = "";
  // creer le formulaire
  div_newObjet.innerHTML = `
  <div class="card-body p-3">
    <form id="formAddObjet">

      <div class="mb-4">
        <h6 class="font-weight-bolder mb-4 text-gradient text-primary">Ajouter un nouvel objet</h6>
        
        <div class="form-group">
          <label class="form-label">Titre</label>
          <input type="text" class="form-control border-radius-lg" name="titre" required placeholder="Nom de l'objet">
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Prix (Ar)</label>
              <input type="number" class="form-control border-radius-lg" name="prix" required min="0" step="5">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Catégorie</label>
              <select class="form-control border-radius-lg" name="categorie" required>
                <option value="">Sélectionner...</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Description</label>
          <textarea class="form-control border-radius-lg" name="description" rows="4" placeholder="Décrivez l'état, la couleur, les accessoires..."></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Photo de l'objet</label>
          <input type="file" class="form-control border-radius-lg" name="image" accept="image/*" required>
          <small class="text-muted">Formats acceptés: JPG, PNG, GIF, WebP (max 5MB)</small>
        </div>

        <div class="text-end mt-4">
          <button type="button" id="btn_annuler" class="btn btn-link text-secondary px-3">Annuler</button>
          <button type="submit" id="btn_ajouter" class="btn bg-gradient-dark">Ajouter l'objet</button>
        </div>

      </div>

    </form>
  </div>
  `;

  // liste des categories
  const categories = await getAllCategorie();

  if (Array.isArray(categories) && categories.length > 0) {
    // Charger les catégories
    const deroulante = div_newObjet.querySelector('select[name="categorie"]');
    if (!deroulante) {
      alert("Select catégorie non trouvé");
      return;
    }
    categories.forEach(cat => { 
      const option = document.createElement('option');
      option.value = cat.id_categorie;
      option.innerHTML = cat.nom_categorie;
      deroulante.appendChild(option);
    });
  } else{
    deroulante.innerHTML = "<option value=''>Aucune catégorie disponible</option>";
  }

  const btn_annuler = div_newObjet.querySelector('#btn_annuler');
  const btn_ajouter = div_newObjet.querySelector('#btn_ajouter');
  const form = div_newObjet.querySelector('#formAddObjet');

  if (!btn_annuler || !btn_ajouter || !form) {
    alert("Éléments du formulaire non trouvés");
    return;
  }

  // Bouton annuler: vide le formulaire
  btn_annuler.addEventListener('click', function(e) {
    e.preventDefault();
    div_newObjet.innerHTML = "";
  });

  // Soumission du formulaire
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    await createObjet(form);
  });
}

async function createObjet(form) {
  try {
    const formData = new FormData(form);
    const data = {
      id_proprietaire: getCurrentUserId(),
      title: formData.get('titre'),
      id_categorie: formData.get('categorie'),
      description: formData.get('description'),
      prix_estime: formData.get('prix')
    };

    const response = await fetch('/api/add/objet', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });

    if (!response.ok) {
      throw new Error('Erreur lors de l\'ajout de l\'objet');
    }

    const result = await response.json();
    if (result.success) {
      const idObjet = result.id_objet;
      console.log('Objet créé avec ID:', idObjet);
      
      const imageFile = formData.get('image');
      console.log('Fichier image:', imageFile);
      console.log('Taille fichier:', imageFile ? imageFile.size : 'null');
      
      if (imageFile && imageFile.size > 0) {
        console.log('Début upload image...');
        const imageFormData = new FormData();
        imageFormData.append('image', imageFile);
        imageFormData.append('id_objet', idObjet);

        console.log('FormData contenu:');
        for (let pair of imageFormData.entries()) {
          console.log(pair[0] + ':', pair[1]);
        }

        const imageResponse = await fetch('/api/objet/upload-image', {
          method: 'POST',
          body: imageFormData
        });

        console.log('Response upload status:', imageResponse.status);
        const imageResult = await imageResponse.json();
        console.log('Response upload result:', imageResult);
        
        if (!imageResult.ok) {
          console.warn('Image non uploadée:', imageResult.error);
          alert('Erreur upload image: ' + imageResult.error);
        } else {
          console.log('Image uploadée avec succès!');
        }
      } else {
        console.log('Aucune image à uploader');
      }

      document.querySelector('#form-newObjet').innerHTML = "";
      const id_user = getCurrentUserId();
      const liste = await getObjet_User(id_user);
      loadListObjet(liste);
    } else {
      alert('Erreur: ' + (result.message || 'Échec de l\'ajout'));
    }
  } catch (error) {
    alert('Erreur lors de l\'ajout de l\'objet');
  }
}

function loadInformation(data) {
  const div_information = document.querySelector('#profile-information');
  if (!div_information) {
    alert("Div profile-information non trouvé");
    return;
  }
  // Vider son contenue
  div_information.innerHTML = "";

  // Ajouter les information du user
  div_information.innerHTML = `
  <p class="text-sm">
      Salut, Je m'appelle ${data.name}, si vous voulez me contacter, vous pouvez me contacter par email ou téléphone.
    </p>
    <hr class="horizontal gray-light my-4">
    <ul class="list-group">
      <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Nom:</strong> &nbsp; ${data.name}</li>
      <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Role:</strong> &nbsp; ${data.role}</li>
      <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Email:</strong> &nbsp; ${data.email}</li>
      <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Mobile:</strong> &nbsp; ${data.phone}</li>
      <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Date inscription:</strong> &nbsp; ${data.join_date}</li>
    </ul>
  `;
}

function loadListDemande(demandes) {
  const div_demande = document.querySelector('#liste-demande');
  // Vider son contenue
  div_demande.innerHTML = "";

  // Ajouter le style CSS pour les animations
  if (!document.querySelector('#demande-styles')) {
    const style = document.createElement('style');
    style.id = 'demande-styles';
    style.textContent = `
      @keyframes slideDown {
        from {
          opacity: 0;
          transform: translateY(-20px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
      
      .demande-item {
        border-left: 3px solid #e3f2fd !important;
        background: #f8f9fa !important;
        border-radius: 8px !important;
        transition: all 0.3s ease !important;
        cursor: pointer !important;
        padding: 12px 16px !important;
        margin-bottom: 12px !important;
      }
      
      .demande-item:hover {
        border-left: 3px solid #2dce89 !important;
        background: #ffffff !important;
        border-radius: 8px !important;
        transition: all 0.3s ease !important;
        cursor: pointer !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;
        transform: translateX(5px);
      }
      
      .demande-item.expanded {
        border-left: 3px solid #fd7e14 !important;
        background: #ffffff !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important;
      }
      
      .detail-demande {
        background: #ffffff !important;
        border: 2px solid #fd7e14 !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 20px rgba(253, 126, 20, 0.15) !important;
        animation: slideDown 0.3s ease-out !important;
      }
      
      .list-group-scrollable {
        max-height: 500px !important;
        overflow-y: auto !important;
        padding-right: 10px !important;
      }
      
      .list-group-scrollable::-webkit-scrollbar {
        width: 6px !important;
      }
      
      .list-group-scrollable::-webkit-scrollbar-track {
        background: #f1f1f1 !important;
        border-radius: 3px !important;
      }
      
      .list-group-scrollable::-webkit-scrollbar-thumb {
        background: #fd7e14 !important;
        border-radius: 3px !important;
      }
      
      .list-group-scrollable::-webkit-scrollbar-thumb:hover {
        background: #e67100 !important;
      }
    `;
    document.head.appendChild(style);
  }
  if (Array.isArray(demandes) && demandes.length > 0) {
    const ul = document.createElement('ul');
    ul.classList.add('list-group', 'list-group-scrollable');

    demandes.forEach(dm => {
      const li = document.createElement('li');
      li.classList.add('list-group-item', 'border-0', 'd-flex', 'align-items-center', 'px-0', 'mb-2');
      li.innerHTML = `
        <div class="avatar me-3">
          <img src="/assets/img/avatar.svg" alt="" class="border-radius-lg shadow">
        </div>
        <div class="d-flex align-items-start flex-column justify-content-center">
          <h6 class="mb-0 text-sm">${dm.name_proposeur}</h6>
          <p class="mb-0 text-xs">Propose son : <strong>${dm.objet_proposer}</strong> contre ton : <strong>${dm.objet_requise}</strong> </p>
          <a href="#" class="text-primary text-xs mt-1" style="opacity: 0.85;">
            Voir plus…
          </a>
        </div>
      `;
      
      const voir = li.querySelector('a');
      voir.addEventListener('click', function (e) {
        e.preventDefault();
        voirPlus(dm);
      });
      
      ul.appendChild(li);
    
    });
    div_demande.appendChild(ul);
  } else{
    div_demande.innerHTML = `
      <p class="small">Aucune demande trouvee</p>
    `;
  }
}

function voirPlus(dm) {
  // Fermer tous les autres détails ouverts
  document.querySelectorAll('.detail-demande').forEach(detail => {
    detail.remove();
  });
  
  // Retirer la classe 'expanded' de tous les li
  document.querySelectorAll('#liste-demande li').forEach(li => {
    li.classList.remove('expanded');
  });
  
  // Trouver le li actuel et ajouter la classe expanded
  const liActuel = event.target.closest('li');
  liActuel.classList.add('expanded');
  
  // Créer le contenu détaillé
  const detailDiv = document.createElement('div');
  detailDiv.className = 'detail-demande mt-3 p-3 bg-light rounded';
  
  detailDiv.innerHTML = `
    <div class="row align-items-center">
      <div class="col-md-4">
        <div class="text-center mb-3">
          <p class="text-xs text-muted mb-2">Objet proposé</p>
          <img src="/assets/img/home-decor-1.jpg" alt="${dm.objet_proposer}" class="img-fluid rounded shadow" style="max-height: 120px; object-fit: cover;">
          <p class="mt-2 mb-0"><strong>${dm.objet_proposer}</strong></p>
          <p class="text-xs text-muted mb-0">${dm.prix_proposer || 'Prix non spécifié'} Ar</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center mb-3">
          <p class="text-xs text-muted mb-2">Objet requis</p>
          <img src="/assets/img/home-decor-1.jpg" alt="${dm.objet_requise}" class="img-fluid rounded shadow" style="max-height: 120px; object-fit: cover;">
          <p class="mt-2 mb-0"><strong>${dm.objet_requise}</strong></p>
          <p class="text-xs text-muted mb-0">${dm.prix_requise || 'Prix non spécifié'} Ar</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center">
          <p class="text-xs text-muted mb-2">Date de la demande</p>
          <p class="mb-3"><strong>${formatDate(dm.date_proposition)}</strong></p>
          <div class="d-grid gap-2">
            <button class="btn btn-success btn-sm" id="btn-accept" >
              <i class="fas fa-check me-1"></i>Accepter
            </button>
            <button class="btn btn-danger btn-sm" id="btn-refuse" >
              <i class="fas fa-times me-1"></i>Refuser
            </button>
          </div>
        </div>
      </div>
    </div>
  `;
  
  const btn_accept = detailDiv.querySelector('#btn-accept');
  const btn_refus = detailDiv.querySelector('#btn-refuse');

  btn_accept.addEventListener('click',async function (e) {
    e.preventDefault();
    const success = await acceptEchange(dm.id_echange);
    if (success) {
      //alert('Échange accepté avec succès!');
      // Recharger la liste des demandes
      const id_user = getCurrentUserId();
      const listeDemande = await EchangesAttente(id_user);
      loadListDemande(listeDemande);
    }
  });
  btn_refus.addEventListener('click',async function (e) {
    e.preventDefault();
    const success = await refusEchange(dm.id_echange);
    if (success) {
      //alert('Échange refusé avec succès!');
      // Recharger la liste des demandes
      const id_user = getCurrentUserId();
      const listeDemande = await EchangesAttente(id_user);
      loadListDemande(listeDemande);
    }
  });
  
  liActuel.parentNode.insertBefore(detailDiv, liActuel.nextSibling);
}

function formatDate(dateString) {
  if (!dateString) return 'Date non spécifiée';
  
  const date = new Date(dateString);
  const options = { 
    day: '2-digit', 
    month: 'short', 
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  
  return date.toLocaleDateString('fr-FR', options);
}