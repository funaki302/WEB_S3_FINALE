// Fonctions utilitaires manquantes
function getCurrentUserId() {
    const meta = document.querySelector('meta[name="user-id"]');
    const id = meta ? parseInt(meta.content || '0', 10) : 0;
    return Number.isFinite(id) ? id : 0;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

async function getMargeObjet(data, marge) {
  try {
    const min = data.prix_estime - (data.prix_estime * marge / 100);
    const max = data.prix_estime + (data.prix_estime * marge / 100);
    const id = data.id_proprietaire;
    const result = await getObjetsByMarge(min, max, id);
    return result;
  } catch (error) {
    alert('Erreur lors de la récupération des objets dans la marge : +- ' + marge + '% :' + error.message);
  }
}

function getIdObjet() {
    const meta = document.querySelector('meta[name="objet-id"]');
    const id = meta ? parseInt(meta.content || '0', 10) : 0;
    return Number.isFinite(id) ? id : 0;
}

function getMarge() {
    // Essayer d'abord depuis le meta-tag
    const meta = document.querySelector('meta[name="marge"]');
    let marge = meta ? parseInt(meta.content || '0', 10) : 0;
    
    // Alternative: lire depuis l'URL si le meta-tag ne fonctionne pas
    if (!marge) {
        try {
            const urlParams = new URLSearchParams(window.location.search);
            marge = parseInt(urlParams.get('marge') || '0', 10);
        } catch (error) {
            // Méthode manuelle si URLSearchParams n'est pas supporté
            const url = window.location.search;
            const params = url.substring(1).split('&');
            for (let param of params) {
                const pair = param.split('=');
                if (pair[0] === 'marge') {
                    marge = parseInt(pair[1] || '0', 10);
                    break;
                }
            }
        }
    }
    
    console.log('Marge récupérée:', marge, 'depuis meta:', meta ? meta.content : 'null');
    return Number.isFinite(marge) ? marge : 0;
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

      const marge = getMarge();
      if (!marge || (marge !== 10 && marge !== 20)) {
        alert('marge manquant ou invalide (doit être 10 ou 20)');
        return;
      }

      const data_objet = await getObjetById(id_objet);
      const liste = await getMargeObjet(data_objet, marge);
      loadObjet(data_objet);
      loadListeMargeObjet(liste, data_objet.prix_estime);

    } catch (e) {
      this.alert('Erreur chargement de la page margeObjet :'+ (e && e.message ? e.message : String(e)));
    }
});

function loadObjet(objet) {
    // afficher les informations de l'objet 
    const container = document.querySelector('#info-objet-reference');
    if (!container) {
        console.error("Container #info-objet-reference non trouvé");
        return;
    }
    
    const rawImg = objet && (objet.image || objet.img || objet.photo) ? String(objet.image || objet.img || objet.photo) : '';
    const imgUrl = rawImg.trim() !== '' ? ('/uploads/objets/' + rawImg.replace(/^[/\\]+/, '')) : '../assets/img/home-decor-1.jpg';
    
    container.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <img src="${escapeHtml(imgUrl)}" alt="${escapeHtml(objet.title || 'Objet')}" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
            </div>
            <div class="col-md-8">
                <h5 class="font-weight-bolder mb-3">${escapeHtml(objet.title || 'Sans titre')}</h5>
                <p class="text-secondary mb-2">
                    <strong>Prix estimé:</strong> ${objet.prix_estime || '0'} Ar
                </p>
                <p class="text-secondary mb-2">
                    <strong>Catégorie:</strong> ${escapeHtml(objet.categorie_name || 'Non spécifiée')}
                </p>
                <p class="text-secondary mb-2">
                    <strong>Propriétaire:</strong> ${escapeHtml(objet.proprietaire_name || 'Inconnu')}
                </p>
                <p class="text-secondary mb-3">
                    <strong>Description:</strong> ${escapeHtml(objet.description || 'Pas de description')}
                </p>
            </div>
        </div>
    `;
}
async function loadListeMargeObjet(liste, prixReference) {
  try {
    
    const listObjet = document.querySelector('#liste-marge');
    if (!listObjet) {
      alert("Div #liste-marge non trouvé");
      return;
    }
    
    // effacer le contenu du div
    listObjet.innerHTML = "";
    
    // Vérifier si liste est null ou undefined
    if (!liste) {
      console.warn('liste est null ou undefined');
      liste = [];
    }

    // Message si aucun objet trouvé
    if (!Array.isArray(liste) || liste.length === 0) {
      listObjet.innerHTML = `
        <div class="w-100 text-center py-4">
          <i class="fas fa-search fa-3x text-muted mb-3"></i>
          <p class="text-muted">Aucun objet trouvé dans cette marge de prix.</p>
        </div>
      `;
      return;
    }
    
    // Créer le conteneur avec défilement horizontal comme dans listes_objects.php
    const scrollContainer = document.createElement('div');
    scrollContainer.className = 'd-flex flex-row flex-nowrap objects-scroll pb-2';
    scrollContainer.style.gap = '1.5rem';
    
    // ajouter la liste des objets
    if (Array.isArray(liste) && liste.length > 0) {
      liste.forEach(objet => {
        
        const rawImg = objet && (objet.image || objet.img || objet.photo) ? String(objet.image || objet.img || objet.photo) : '';
        const imgUrl = rawImg.trim() !== '' ? ('/uploads/objets/' + rawImg.replace(/^[/\\]+/, '')) : '../assets/img/home-decor-1.jpg';
        
        // calcul la difference de % avec le prix de référence
        const diff = objet.prix_estime - prixReference;
        const diffPercent = prixReference > 0 ? (diff / prixReference) * 100 : 0;
        const badgeClass = diffPercent > 0 ? 'bg-success' : (diffPercent < 0 ? 'bg-danger' : 'bg-secondary');

        // lien vers page echange
        const exchangeUrl = `/exchange?target=${objet.id_objet}`;

        const objetCard = document.createElement('div');
        objetCard.className = 'objects-scroll-item';
        objetCard.style.flex = '0 0 520px';
        objetCard.innerHTML = `
          <div class="card card-blog card-plain h-100">
            <div class="position-relative">
              <a href="/fiche_objet?id=${objet.id_objet}" class="d-block shadow-lg">
                <img src="${escapeHtml(imgUrl)}" alt="${escapeHtml(objet.title || 'Objet')}" class="img-fluid rounded shadow border-radius-md" style="width: 100%; height: 280px; object-fit: cover;">
              </a>
              <div class="position-absolute top-0 end-0 m-2">
                <span class="badge ${badgeClass} text-white">
                  ${diffPercent > 0 ? '+' : ''}${diffPercent.toFixed(1)}%
                </span>
              </div>
            </div>
            <div class="card-body px-1 pb-0">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="font-weight-bolder mb-0 text-truncate" style="max-width: 200px;">
                  ${escapeHtml(objet.title || 'Sans titre')}
                </h6>
                <span class="badge bg-gradient-primary text-white text-xs">
                  ${objet.prix_estime || '0'} Ar
                </span>
              </div>
              <p class="text-secondary text-sm mb-2">
                <i class="fas fa-user me-1"></i> ${escapeHtml(objet.proprietaire_name || 'Inconnu')}
              </p>
              <p class="text-sm text-muted mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                ${escapeHtml(objet.description || 'Pas de description')}
              </p>
              <div class="d-flex align-items-center justify-content-between">
                <a href="/fiche_objet?id=${objet.id_objet}" class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-eye me-1"></i> Voir
                </a>
                <a href="${exchangeUrl}" class="btn bg-gradient-primary btn-sm">
                  <i class="fas fa-exchange-alt me-1"></i> Échanger
                </a>
              </div>
            </div>
          </div>
        `;
    
        scrollContainer.appendChild(objetCard);
      });
      
      listObjet.appendChild(scrollContainer);
    }
    
  } catch (error) {
    console.error("Error de loadListeMargeObjet:", error);
    alert("Error de loadListeMargeObjet: " + error.message);
  }
}