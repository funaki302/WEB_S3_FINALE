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
            this.alert('id_user manquant');
        }
        const liste = await getObjet_User(id_user);
        loadListObjet(liste);
    } catch (err) {
        console.error('Erreur chargement des objets:', err);
    }
});

async function loadListObjet(liste) {
    try {
        const listObjet = document.querySelector('#liste');
        if (!listObjet) alert("Div non trouver");
        // effacer le contenue du div
        listObjet.innerHTML = "";
        // ajouter la liste des objets
        if (Array.isArray(liste) && liste.length > 0) {
            liste.forEach(objet => {
                const col = document.createElement('div');
                col.classList.add('col-xl-3 col-md-6 mb-xl-0 mb-4');
                col.innerHTML = `
                 <div class="card card-blog card-plain">
                    <div class="position-relative">
                      <a class="d-block">
                        <img src="" alt="" class="img-fluid shadow border-radius-md">
                      </a>
                    </div>
                    <div class="card-body px-1 pb-0">
                      <p class="text-secondary mb-0 text-sm">Project #2</p>
                      <a href="javascript:;">
                        <h5 class="font-weight-bolder">
                          ${objet.title}  (${objet.prix_estime}$)
                        </h5>
                      </a>
                      <p class="mb-4 text-sm">
                        ${objet.description}
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
        } else{
            listObjet.innerHTML = `
            <div class="col-xl-3 col-md-6 mb-xl-0 mb-4">
                <div class="card h-100 card-plain border">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                      <a href="javascript:;">
                        <i class="fa fa-plus text-secondary mb-3"></i>
                        <h5 class=" text-secondary"> New project </h5>
                      </a>
                    </div>
                </div>
            </div>
            `;
        }

    } catch (error) {
        alert("Error de loadListObjet");
    }
}