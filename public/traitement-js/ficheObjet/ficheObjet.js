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
      loadObjet(donnees);
      loadProprio(donnees);
      loadImages(images);
      loadDescription(donnees);
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
        imageElement.style.maxHeight = '320px';       // ← 320px au lieu de 400px (ou mets 280px si tu veux plus petit)
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

