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

      // Liste de ses objets
      const liste = await getObjet_User(id_user);
      await loadListObjet(liste);

    } catch (err) {
      console.error('Erreur chargement des objets:', err);
    }
});

async function loadListObjet(liste) {
  try {
    console.log('loadListObjet appelée avec:', liste);
    
    const listObjet = document.querySelector('#liste');
    if (!listObjet) {
      console.error("Div #liste non trouvé");
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
      console.log(`Affichage de ${liste.length} objets`);
      liste.forEach((objet, index) => {
        console.log(`Objet ${index}:`, objet);
        
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
                <button type="button" class="btn btn-outline-primary btn-sm mb-0">View Project</button>
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
    console.error("Error de loadListObjet:", error);
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
  //alert("Ajouter Objet !!");
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
      //alert('Objet ajouté avec succès!');
      // Vider le formulaire
      document.querySelector('#form-newObjet').innerHTML = "";
      // Recharger la liste des objets
      const id_user = getCurrentUserId();
      const liste = await getObjet_User(id_user);
      loadListObjet(liste);
    } else {
      alert('Erreur: ' + (result.message || 'Échec de l\'ajout'));
    }
  } catch (error) {
    console.error('Erreur:', error);
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