function getCurrentUserId() {
    const meta = document.querySelector('meta[name="user-id"]');
    const id = meta ? parseInt(meta.content || '0', 10) : 0;
    return Number.isFinite(id) ? id : 0;
}

function getIdObjet() {
    const meta = document.querySelector('meta[name="objet-id"]');
    const id = meta ? parseInt(meta.content || '0', 10) : 0;
    return Number.isFinite(id) ? id : 0;
}

window.addEventListener('load', async function () {
    try {
      const id_user = getCurrentUserId();
      if (!id_user) {
        alert('id_user manquant');
      }

      const id_objet = getIdObjet();
      if (!id_objet) {
        alert('id_objet manquant');
      }

      const donnees = await getObjetById(id_objet);
      const images = await getAllImg(id_objet);
      const historiques = await getAllHistory(id_objet);
      loadObjet(donnees);
      loadProprio(donnees);
      loadImages(images);
      loadDescription(donnees);
      loadHistory(historiques);
    } catch (e) {
      this.alert('Erreur chargement de la page :'+ (e && e.message ? e.message : String(e)));
    }
});

function loadImages(images) {
    const container = document.querySelector('#info-img');
    if (!container) return;

    container.innerHTML = "";

    if (!Array.isArray(images) || images.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5 my-5">
                <div class="icon icon-shape icon-xl bg-gradient-primary shadow text-center mb-4 mx-auto">
                    <i class="fas fa-image text-white opacity-8"></i>
                </div>
                <h5 class="text-secondary">Aucune photo pour le moment</h5>
                <p class="text-sm text-muted">Ajoutez des images pour rendre cet objet plus attractif !</p>
            </div>
        `;
        return;
    }

    const carousel = document.createElement('div');
    carousel.id = 'objectCarousel';
    carousel.className = 'carousel slide mb-4 shadow-lg';
    carousel.setAttribute('data-bs-ride', 'carousel');

    // Indicateurs
    const indicators = document.createElement('div');
    indicators.className = 'carousel-indicators';
    images.forEach((_, i) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('data-bs-target', '#objectCarousel');
        btn.setAttribute('data-bs-slide-to', i);
        if (i === 0) btn.classList.add('active');
        indicators.appendChild(btn);
    });

    // Inner
    const inner = document.createElement('div');
    inner.className = 'carousel-inner';

    images.forEach((img, i) => {
        const item = document.createElement('div');
        item.className = 'carousel-item' + (i === 0 ? ' active' : '');

        const imgEl = document.createElement('img');
        imgEl.src = `/uploads/objets/${img.image}`;
        imgEl.className = 'd-block w-100 rounded';
        imgEl.alt = "Photo de l'objet";
        imgEl.style.maxHeight = '340px';          // ← un peu plus grand que 280px pour plus d'impact
        imgEl.style.objectFit = 'cover';
        imgEl.style.borderRadius = '1rem';

        item.appendChild(imgEl);
        inner.appendChild(item);
    });

    carousel.appendChild(indicators);
    carousel.appendChild(inner);

    if (images.length > 1) {
        // Prev / Next avec couleur primary
        ['prev', 'next'].forEach(dir => {
            const btn = document.createElement('button');
            btn.className = `carousel-control-${dir}`;
            btn.type = 'button';
            btn.setAttribute('data-bs-target', '#objectCarousel');
            btn.setAttribute('data-bs-slide', dir);
            btn.innerHTML = `<span class="carousel-control-${dir}-icon"></span>`;
            carousel.appendChild(btn);
        });
    }

    container.appendChild(carousel);
}

function loadObjet(data){
    const div_objet = document.querySelector('#info-objet');
    const currentUserId = getCurrentUserId();
    const isOwner = currentUserId && String(data.id_proprietaire) === String(currentUserId);
    const exchangeUrl = `/exchange?target=${data.id_objet}`;
    
    console.log("Debug propriétaire :", {
        currentUserId,
        objetProprietaire: data.id_proprietaire,
        isOwner
    });

    div_objet.innerHTML = `
        <div class="d-flex align-items-center justify-content-between">
            <div>
              <h5 class="mb-0">${data.title || 'Sans titre'}</h5>
              <p class="text-sm text-secondary mb-0">
                <i class="fas fa-layer-group me-2"></i>${data.nom_categorie || '?'}
              </p>
            </div>
            ${isOwner ? `
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary" id="min" title="10%">
                    10%
                </button>
                <button class="btn btn-sm btn-outline-primary" id="max" title="20%">
                    20%
                </button>
                <button class="btn btn-link text-dark text-sm mb-0 px-2 py-1" id="edit" type="button" data-action="edit" title="Modifier">
                  <img src="/assets/icons/modified/pencil-square.svg" alt="Modifier" style="width: 16px; height: 16px;">
                </button>
                <button class="btn btn-link text-danger text-sm mb-0 px-2 py-1" id="delete" type="button" data-action="archive" title="Archiver">
                  <img src="/assets/icons/modified/archive-fill.svg" alt="Archiver" style="width: 16px; height: 16px;">
                </button>
            </div>
            ` : `<div class="modal-footer">
                  <a href="${exchangeUrl}" class="btn bg-gradient-primary">Proposer un échange</a>
                </div>`
            }
        </div>
    `;
    if (isOwner) {
      // bouton modifier
      const btn_modofier = div_objet.querySelector('#edit');
      btn_modofier.addEventListener('click',async function (e) {
        e.preventDefault();
        await editObjet(data.id_objet);
      });
  
      // bouton supprimer
      const btn_supprimer = div_objet.querySelector('#delete');
       btn_supprimer.addEventListener('click',async function (e) {
        e.preventDefault();
        await supObjet(data.id_objet);
      });

      // bouton + - 10%
      const btn_10 = div_objet.querySelector('#min');
      btn_10.addEventListener('click',async function (e) {
        e.preventDefault();
        try {
          voirMargeObjets(data.id_objet, 10);
        } catch (error) {
         alert("Erreur lors du chargement des objets dans la marge de 10%: " + error.message);
        }
      });
  
      // bouton + - 20%
      const btn_20 = div_objet.querySelector('#max');
       btn_20.addEventListener('click',async function (e) {
        e.preventDefault();
        try {
          voirMargeObjets(data.id_objet, 20);
        } catch (error) {
          alert("Erreur lors du chargement des objets dans la marge de 20%: " + error.message);
        }
      });

    }
}

function loadProprio(data){
    const div_proprio = document.querySelector('#info-proprio');
    div_proprio.innerHTML = "";
    div_proprio.innerHTML = `
    <div class="d-flex align-items-center mb-3">
        <ul class="list-group">
            <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Nom:</strong> &nbsp; ${data.proprietaire_name}</li>
            <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Role:</strong> &nbsp; ${data.proprietaire_role}</li>
            <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Email:</strong> &nbsp; ${data.proprietaire_email}</li>
            <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Mobile:</strong> &nbsp; ${data.proprietaire_phone}</li>
            <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Date inscription:</strong> &nbsp; ${data.proprietaire_join_date}</li>
        </ul>
    </div>
    `;
}

function loadDescription(data) {
    const container = document.querySelector('#info-img');
    if (!container) return;

    const div = document.createElement('div');
    div.className = 'mt-4 px-1';

    div.innerHTML = `
        <!-- Description -->
        <div class="mb-5">
            <div class="bg-white border border-light rounded-3 p-4 shadow-xs">
                <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mb-3 d-flex align-items-center">
                    <i class="fas fa-info-circle me-2 text-muted"></i>
                    Description de l'objet
                </h6>
                <p class="text-dark lh-lg mb-0" style="white-space: pre-line; font-size: 0.95rem;">
                    ${data.description || '<span class="text-muted fst-italic">Aucune description fournie pour cet objet.</span>'}
                </p>
            </div>
        </div>

        <!-- Valeur + Date – mise en valeur discrète -->
        <div class="row g-4">
            <!-- Valeur estimée -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="background: rgba(248,249,250,0.6);">
                    <div class="card-body d-flex align-items-center p-4">
                        <div>
                            <p class="text-xs text-uppercase text-secondary mb-1 fw-bold">
                                Valeur estimée
                            </p>
                            <h5 class="mb-0 text-dark fw-bold">
                                ${Number(data.prix_estime || 0).toLocaleString('fr-MG')} Ar
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date de création -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100" style="background: rgba(248,249,250,0.6);">
                    <div class="card-body d-flex align-items-center p-4">
                        <div>
                            <p class="text-xs text-uppercase text-secondary mb-1 fw-bold">
                                Ajouté le
                            </p>
                            <h5 class="mb-0 text-dark fw-bold">
                                ${data.date_creation || '<span class="text-muted">—</span>'}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.appendChild(div);
}

function loadHistory(historiques) {
    const container = document.querySelector('#info-historique');
    container.innerHTML = "";

    if (!Array.isArray(historiques) || historiques.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-history fa-3x text-muted mb-3 opacity-6"></i>
                <p class="text-secondary">Cet objet n'a pas encore changé de mains</p>
            </div>
        `;
        return;
    }

    const timeline = document.createElement('div');
    timeline.className = 'history-timeline pt-3';

    historiques.forEach((h, index) => {
        const item = document.createElement('div');
        item.className = 'history-item d-flex align-items-start mb-4';

        item.innerHTML = `
            <div class="avatar avatar-lg me-3 flex-shrink-0">
                <img src="/assets/img/avatar.svg" alt="" class="border-radius-lg shadow">
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-0 text-dark">${h.proprietaire_nom}</h6>
                <p class="text-sm text-secondary mb-1">
                    Propriétaire du <strong>${h.date_echange}</strong>
                </p>
                ${index < historiques.length - 1 ? 
                    '<small class="text-muted">→ Transmis à la personne suivante</small>' : 
                    '<span class="badge bg-gradient-success mt-2">Propriétaire actuel</span>'}
            </div>
        `;

        timeline.appendChild(item);
    });

    container.appendChild(timeline);
}

async function editObjet(idObjet) {
  const data = await getObjetById(idObjet);
  loadForm(data);
}

async function supObjet(idObjet) {
    // Ajouter une confirmation avant de rendre l'objet inactif
    if (!confirm('Êtes-vous sûr de vouloir rendre cet objet inactif ? Il ne sera plus visible mais sera conservé.')) {
        return;
    }
    
    try {
      const result = await inactifObjet(idObjet);
      if (result) {
        alert('Objet rendu inactif avec succès');
        const url = `/profile`;
        window.location.href = url;
      }
    }catch(error) {
      console.error('Erreur lors de la supObjet:', error);
      alert('Erreur lors de la désactivation de l\'objet');
    }
}

async function loadForm(data) {
  const container = document.querySelector('#info-img');
    if (!container) return;
    container.innerHTML = "";

  // liste des categories
  const categories = await getAllCategorie();

  // creer le formulaire
  container.innerHTML = `
  <div class="card-body p-3">
    <form id="formAddObjet">

      <div class="mb-4">
        <h6 class="font-weight-bolder mb-4 text-gradient text-primary">Modifier Objet</h6>
        
        <div class="form-group">
          <label class="form-label">Titre</label>
          <input type="text" class="form-control border-radius-lg" name="titre" value="${data.title}" required placeholder="Nom de l'objet">
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Prix (Ar)</label>
              <input type="number" class="form-control border-radius-lg" name="prix" value="${data.prix_estime}" required min="0" step="5">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="form-label">Catégorie</label>
              <select class="form-control border-radius-lg" name="categorie" required>
                <option value="">Sélectionner...</option>
                ${categories.map(cat => `<option value="${cat.id_categorie}" ${cat.id_categorie === data.id_categorie ? 'selected' : ''}>${cat.nom_categorie}</option>`).join('')}
              </select>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Description</label>
          <textarea class="form-control border-radius-lg" name="description" rows="4" placeholder="Décrivez l'état, la couleur, les accessoires...">${data.description || ''}</textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Ajouter une photo</label>
          <input type="file" class="form-control border-radius-lg" name="image" accept="image/*" >
          <small class="text-muted">Formats acceptés: JPG, PNG, GIF, WebP (max 5MB)</small>
        </div>

        <div class="text-end mt-4">
          <button type="button" id="btn_annuler" class="btn btn-link text-secondary px-3">Annuler</button>
          <button type="submit" id="btn_ajouter" class="btn bg-gradient-dark">Modifier l'objet</button>
        </div>

      </div>

    </form>
  </div>
  `;

  const btn_annuler = container.querySelector('#btn_annuler');
  const btn_ajouter = container.querySelector('#btn_ajouter');
  const form = container.querySelector('#formAddObjet');

  if (!btn_annuler || !btn_ajouter || !form) {
    alert("Éléments du formulaire non trouvés");
    return;
  }

  // Bouton annuler: vide le formulaire
  btn_annuler.addEventListener('click',async function(e) {
    e.preventDefault();
    const id_objet = getIdObjet();
    const donnees = await getObjetById(id_objet);
    const images = await getAllImg(id_objet);
    loadObjet(donnees);
    loadImages(images);
    loadDescription(donnees);
  });


// Soumission du formulaire
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const id_objet = getIdObjet();    
    
    // Récupérer les données du formulaire
    const formData = new FormData(form);
    const data = {
        title: formData.get('titre'),
        prix_estime: formData.get('prix'),
        id_categorie: formData.get('categorie'),
        description: formData.get('description')
    };

    try {
      
      // 1. Mettre à jour l'objet avec la fonction normale
      const updateResult = await updateObjet(id_objet, data);
      
      if (!updateResult) {
        alert('Erreur lors de la modification de l\'objet');
        return;
      }

      // 2. Vérifier si une image a été sélectionnée
      const imageFile = form.querySelector('input[name="image"]').files[0];
      
      if (imageFile && imageFile.size > 0) {
        console.log('Upload image en cours...');
        // 3. Uploader la nouvelle image
        const uploadResult = await uploadImage(id_objet, imageFile);
        if (!uploadResult.ok) {
          alert('Objet modifié mais erreur lors de l\'upload de l\'image: ' + (uploadResult.error || 'Erreur inconnue'));
        } else {
          alert('Objet modifié et image ajoutée avec succès !');
        }
      } else {
        alert('Objet modifié avec succès !');
      }

      // 4. Recharger les données pour afficher les modifications
      const donnees = await getObjetById(id_objet);
      const images = await getAllImg(id_objet);
      loadObjet(donnees);
      loadImages(images);
      loadDescription(donnees);

    } catch (error) {
      console.error('Erreur détaillée:', error);
      alert('Erreur lors de la modification: ' + error.message);
    }
  });
}

function voirMargeObjets(id_objet, marge) {
  // rediriger vers une page qui affiche l'objet et la liste
  const url = `/margeObjet?id=${id_objet}&marge=${marge}`;
  window.location.href = url;
}