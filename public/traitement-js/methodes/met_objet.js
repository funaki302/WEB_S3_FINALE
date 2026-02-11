function buildUrl(path) {
    const baseUrl = window.location.origin;
    if (path.startsWith('/')) return `${baseUrl}${path}`;
    return `${baseUrl}/${path}`;
}

async function getAllObjet() {
    const objets = await fetch('/api/getAll/objet');
    if (!objets.ok) {
        alert("Erreur lors de la recuperation des objets");
    }

    const data = await objets.json();
    return data;
}

async function getCategoriesForObjets() {
    const res = await fetch('/api/objets/categories');
    if (!res.ok) {
        return [];
    }
    return await res.json();
}

async function searchOthersObjets(params = {}) {
    const url = new URL('/api/objets/others/search', window.location.origin);
    Object.entries(params).forEach(([k, v]) => {
        if (v === undefined || v === null || String(v).trim() === '') return;
        url.searchParams.set(k, String(v));
    });
    const res = await fetch(url.toString());
    if (!res.ok) {
        return [];
    }
    return await res.json();
}

function escapeHtml(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function renderObjectsCards(objets) {
    const container = document.querySelector('#objects-container');
    const empty = document.querySelector('#objects-empty');
    if (!container) return;

    container.innerHTML = '';

    if (!Array.isArray(objets) || objets.length === 0) {
        if (empty) empty.classList.remove('d-none');
        return;
    }

    if (empty) empty.classList.add('d-none');

    objets.forEach((objet) => {
        const id = parseInt(objet.id_objet || 0, 10);
        const cat = escapeHtml(objet.nom_categorie || 'Objet');
        const title = escapeHtml(objet.title || '');
        const desc = escapeHtml(objet.description || '');
        const prix = escapeHtml(objet.prix_estime ?? '');
        const proprietaire = escapeHtml(objet.proprietaire ?? '-');
        const exchangeUrl = `/exchange?target=${encodeURIComponent(String(id))}`;
        const pendingCount = parseInt(objet.pending_count || 0, 10) || 0;
        const pendingBadge = `<span class="badge bg-dark text-white ms-2" title="Demandes en attente">Demandes en attente: ${pendingCount}</span>`;
        const imgSrc = objet.image ? "../uploads/objets/" + objet.image : "../assets/img/home-decor-1.jpg";

        const card = document.createElement('div');
        card.className = 'objects-scroll-item';
        card.innerHTML = `
          <div class="card card-blog card-plain">
            <div class="position-relative">
              <a class="d-block">
                <img src="${imgSrc}" alt="img-blur-shadow" class="img-fluid shadow border-radius-md">
              </a>
              <div class="position-absolute top-0 end-0 mt-2 me-2">
                ${pendingCount > 0 ? `<span class=\"badge bg-dark text-white\">${pendingCount} demandes en attente</span>` : ''}
              </div>
            </div>
            <div class="card-body px-1 pb-0">
              <p class="text-secondary mb-0 text-sm">${cat}</p>
              <a href="javascript:;">
                <h5 class="font-weight-bolder">${title}</h5>
              </a>
              <p class="mb-3 text-sm">${desc}</p>
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center" style="gap: .5rem;">
                  <button type="button" class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#modalObjet${id}">Voir</button>
                  <a class="btn bg-gradient-primary btn-sm mb-0" href="${exchangeUrl}">Exchange</a>
                </div>
                <p class="text-sm text-dark font-weight-bold mb-0">${prix} Ar</p>
              </div>
            </div>
          </div>

          <div class="modal fade" id="modalObjet${id}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">${title}${pendingBadge}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <p class="text-sm mb-2"><span class="text-secondary">Catégorie:</span> <span class="text-dark font-weight-bold">${cat}</span></p>
                  <p class="text-sm mb-2"><span class="text-secondary">Propriétaire:</span> <span class="text-dark font-weight-bold">${proprietaire}</span></p>
                  <p class="text-sm mb-2"><span class="text-secondary">Prix estimé:</span> <span class="text-dark font-weight-bold">${prix} Ar</span></p>
                  <p class="text-sm mb-2"><span class="text-secondary">Demandes en attente:</span> <span class="text-dark font-weight-bold">${pendingCount}</span></p>
                  <hr class="horizontal dark my-3">
                  <p class="text-sm mb-0">${desc}</p>
                </div>
                <div class="modal-footer">
                  <a href="${exchangeUrl}" class="btn bg-gradient-primary">Proposer un échange</a>
                  <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
              </div>
            </div>
          </div>
        `;

        container.appendChild(card);
    });
}

async function initObjetsSearchPage() {
    const form = document.querySelector('#object-search-form');
    const keywordInput = document.querySelector('#search-keyword');
    const categorySelect = document.querySelector('#search-categorie');
    const container = document.querySelector('#objects-container');

    if (!form || !keywordInput || !categorySelect || !container) {
        return;
    }

    try {
        const categories = await getCategoriesForObjets();
        if (Array.isArray(categories)) {
            categories.forEach((c) => {
                const opt = document.createElement('option');
                opt.value = String(c.id_categorie);
                opt.textContent = c.nom_categorie;
                categorySelect.appendChild(opt);
            });
        }
    } catch (e) {
    }

    const initial = Array.isArray(window.__OBJECTS_FALLBACK__) ? window.__OBJECTS_FALLBACK__ : null;
    if (initial) {
        renderObjectsCards(initial);
    } else {
        const data = await searchOthersObjets({});
        renderObjectsCards(data);
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const keyword = keywordInput.value || '';
        const categorie = categorySelect.value || '';
        const data = await searchOthersObjets({ keyword, categorie });
        renderObjectsCards(data);
    });
}

async function add(data) {
    const url = buildUrl("/api/add/objet");
    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    };
    const send = await fetch(url, options);
    if (!send.ok){
        alert("Echec de ADD Objet");
        return false;
    }
    return true;
}

async function delet(id_objet) {
    const objet = await fetch(`/api/delete/objet/${id_objet}`);
    if (!objet.ok) {
        alert("Echec de delete objet "+id_objet); 
        return false;
    }

    alert("Objet "+id_objet+" supprimer!"); 
    return true;
}

async function update(data) {
    const url = buildUrl("/api/update/objet");
    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    };
    const send = await fetch(url, options);
    if (!send.ok){
        alert("Update Objet non reussi");
        return false;
    }
    return true;
}

async function getObjet_User(id_user) {
    try {
        const objets = await fetch(`/api/getObjet/${id_user}`);
        if (!objets.ok) {
            console.error(`Erreur HTTP: ${objets.status} - ${objets.statusText}`);
            throw new Error("Erreur lors de la recuperation des objets");
        }
        const data = await objets.json();
        return data;
    } catch (error) {
        console.error('Erreur getObjet_User:', error);
        throw error;
    }
}

async function loadJsonIntoPre() {
    const pre = document.querySelector('#json');
    if (!pre) return;

    const endpoint = pre.getAttribute('data-endpoint') || '/api/objets/others';

    try {
        const res = await fetch(endpoint);
        if (!res.ok) {
            pre.textContent = 'Erreur API: ' + res.status;
            return;
        }
        const data = await res.json();
        pre.textContent = JSON.stringify(data, null, 2);
    } catch (e) {
        pre.textContent = 'Erreur: ' + (e && e.message ? e.message : String(e));
    }
}

window.addEventListener('DOMContentLoaded', function () {
    loadJsonIntoPre();
    initObjetsSearchPage();
});

async function getObjetById(id_objet) {
    try {
        const url = buildUrl("/api/get/objet");
        const donne = {'id_objet': id_objet};
        const options = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(donne)
        };
        const objet = await fetch(url, options);
        if (!objet.ok){
            alert("Erreur de getObjetById: "+id_objet);
            return null;
        }
        const data = await objet.json();
        return data;
    } catch (error) {
        console.error('Erreur getObjetById:('+id_objet+") "+ error);
        throw error;
    }
}