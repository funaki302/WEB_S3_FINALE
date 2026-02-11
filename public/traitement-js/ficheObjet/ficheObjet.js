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

      const id_objet = getCurrentUserId();
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
    if (!container) {
        console.warn("Conteneur #info-img introuvé");
        return;
    }

    container.innerHTML = "";

    if (!Array.isArray(images) || images.length === 0) {
        container.innerHTML = `
            <div class="text-center mb-4">
                <div class="bg-light rounded p-5">
                    <i class="fas fa-image fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune image disponible</p>
                </div>
            </div>
        `;
        return;
    }

    // ────────────────────────────────────────────────
    // Construction du carrousel
    // ────────────────────────────────────────────────

    const carousel = document.createElement('div');
    carousel.id = 'objectCarousel';
    carousel.classList.add('carousel', 'slide', 'mb-4');
    carousel.setAttribute('data-bs-ride', 'carousel');

    // Indicateurs
    const indicators = document.createElement('div');
    indicators.classList.add('carousel-indicators');

    images.forEach((img, index) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('data-bs-target', '#objectCarousel');
        btn.setAttribute('data-bs-slide-to', index);
        if (index === 0) btn.classList.add('active');
        indicators.appendChild(btn);
    });

    // Slides
    const inner = document.createElement('div');
    inner.classList.add('carousel-inner');

    images.forEach((img, index) => {
        const item = document.createElement('div');
        item.classList.add('carousel-item');
        if (index === 0) item.classList.add('active');

        const imageElement = document.createElement('img');
        imageElement.src = `/uploads/objets/${img.image}`;
        imageElement.classList.add('d-block', 'w-100', 'rounded');
        imageElement.alt = "Image de l'objet";
        
        // ← Modification principale ici
        imageElement.style.maxHeight = '280px';       // ← 280px pour s'adapter à la hauteur réduite
        imageElement.style.objectFit = 'cover';
        imageElement.style.borderRadius = '0.75rem';  // un peu plus doux que juste 'rounded'

        item.appendChild(imageElement);
        inner.appendChild(item);
    });

    carousel.appendChild(indicators);
    carousel.appendChild(inner);

    // Contrôles (flèches) seulement si plusieurs images
    if (images.length > 1) {
        const prevBtn = document.createElement('button');
        prevBtn.classList.add('carousel-control-prev');
        prevBtn.type = 'button';
        prevBtn.setAttribute('data-bs-target', '#objectCarousel');
        prevBtn.setAttribute('data-bs-slide', 'prev');
        prevBtn.innerHTML = '<span class="carousel-control-prev-icon"></span>';
        carousel.appendChild(prevBtn);

        const nextBtn = document.createElement('button');
        nextBtn.classList.add('carousel-control-next');
        nextBtn.type = 'button';
        nextBtn.setAttribute('data-bs-target', '#objectCarousel');
        nextBtn.setAttribute('data-bs-slide', 'next');
        nextBtn.innerHTML = '<span class="carousel-control-next-icon"></span>';
        carousel.appendChild(nextBtn);
    }

    container.appendChild(carousel);
}

function loadObjet(data){
    const div_objet = document.querySelector('#info-objet');
    div_objet.innerHTML = "";

    div_objet.innerHTML = `
        <div class="d-flex align-items-center">
            <div>
              <h5 class="mb-0">${data.title}</h5>
              <p class="text-sm text-secondary mb-0">Catégorie: ${data.nom_categorie}</p>
            </div>
        </div>
    `;
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

    const div_desc = document.createElement('div');
    div_desc.innerHTML = "";
    div_desc.innerHTML = `
    <div class="mb-4">
      <h6 class="text-uppercase text-secondary text-sm font-weight-bolder mb-3">Description</h6>
      <p class="text-sm">${data.description}</p>
    </div>
    
    <div class="row">
      <div class="col-md-6">
        <div class="d-flex align-items-center mb-3">
          <div>
            <p class="text-sm mb-0">Valeur estimée</p>
            <h6 class="mb-0">${data.prix_estime} Ar</h6>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="d-flex align-items-center mb-3">
          <div>
            <p class="text-sm mb-0">Date de création</p>
            <h6 class="mb-0">${data.date_creation}</h6>
          </div>
        </div>
      </div>
    </div>
    `;

    container.appendChild(div_desc);
}

function loadHistory(historiques) {
  const div_historique = document.querySelector('#info-historique');
  // Vider son contenue
  div_historique.innerHTML = "";

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
        max-height: 100% !important;
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
  if (Array.isArray(historiques) && historiques.length > 0) {
    const ul = document.createElement('ul');
    ul.classList.add('list-group', 'list-group-scrollable');

    historiques.forEach(histo => {
      const li = document.createElement('li');
      li.classList.add('list-group-item', 'border-0', 'd-flex', 'align-items-center', 'px-0', 'mb-2');
      li.innerHTML = `
        <div class="avatar me-3">
          <img src="/assets/img/avatar.svg" alt="" class="border-radius-lg shadow">
        </div>
        <div class="d-flex align-items-start flex-column justify-content-center">
          <h6 class="mb-0 text-sm">${histo.proprietaire_nom}</h6>
          <p class="mb-0 text-xs">Etait son proprietaire a la date <strong>${histo.date_echange}</strong> </p>
        </div>
      `;
      
      ul.appendChild(li);
    
    });
    div_historique.appendChild(ul);
  } else{
    div_historique.innerHTML = `
      <p class="small">Aucune historique trouvee</p>
    `;
  }
}