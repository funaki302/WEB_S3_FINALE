(function () {
  const els = {
    list: document.getElementById('catList'),
    addBtn: document.getElementById('catAddBtn'),
    formWrap: document.getElementById('catFormWrap'),
    formTitle: document.getElementById('catFormTitle'),
    nameInput: document.getElementById('catNameInput'),
    saveBtn: document.getElementById('catSaveBtn'),
    cancelBtn: document.getElementById('catFormCancelBtn'),
    resetBtn: document.getElementById('catFormResetBtn'),
    msg: document.getElementById('catFormMsg'),
  };

  if (!els.list || !els.addBtn || !els.formWrap || !els.nameInput || !els.saveBtn) {
    return;
  }

  const ICON_EDIT = '/assets/icons/modified/pencil-square.svg';
  const ICON_ARCHIVE = '/assets/icons/modified/archive-fill.svg';

  let categories = [];
  let editingId = null;
  let loading = false;

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function setMsg(text, type) {
    if (!els.msg) return;
    if (!text) {
      els.msg.classList.add('d-none');
      els.msg.textContent = '';
      els.msg.classList.remove('text-success', 'text-danger', 'text-warning');
      return;
    }

    els.msg.classList.remove('d-none');
    els.msg.classList.remove('text-success', 'text-danger', 'text-warning');
    if (type === 'success') els.msg.classList.add('text-success');
    else if (type === 'warning') els.msg.classList.add('text-warning');
    else els.msg.classList.add('text-danger');
    els.msg.textContent = text;
  }

  function showForm(mode, cat) {
    els.formWrap.classList.remove('d-none');

    if (mode === 'edit' && cat) {
      editingId = cat.id_categorie;
      els.formTitle.textContent = 'Modifier la catégorie';
      els.nameInput.value = cat.nom_categorie || '';
    } else {
      editingId = null;
      els.formTitle.textContent = 'Nouvelle catégorie';
      els.nameInput.value = '';
    }

    setMsg('', '');
    els.nameInput.focus();
  }

  function hideForm() {
    els.formWrap.classList.add('d-none');
    editingId = null;
    setMsg('', '');
  }

  function normalizeList(data) {
    if (Array.isArray(data)) return data;
    if (data && Array.isArray(data.categories)) return data.categories;
    if (data && Array.isArray(data.data)) return data.data;
    return [];
  }

  async function apiFetch(url, options) {
    const finalOptions = options || {};
    finalOptions.headers = finalOptions.headers || {};
    if (!finalOptions.headers['Accept']) {
      finalOptions.headers['Accept'] = 'application/json';
    }

    const res = await fetch(url, finalOptions);
    const contentType = res.headers.get('content-type') || '';
    let json = null;

    if (contentType.includes('application/json')) {
      json = await res.json();
    } else {
      const text = await res.text();
      try {
        json = JSON.parse(text);
      } catch (e) {
        const trimmed = (text || '').trim();
        if (trimmed.startsWith('<')) {
          json = { success: false, message: 'Réponse HTML (probable erreur PHP ou session expirée)' };
        } else {
          json = { success: false, message: trimmed || 'Réponse non JSON' };
        }
      }
    }

    if (!res.ok) {
      const msg = (json && (json.message || json.error)) || 'Erreur serveur';
      throw new Error(msg);
    }

    return json;
  }

  async function loadCategories() {
    if (loading) return;
    loading = true;

    els.list.innerHTML = `
      <li class="list-group-item border-0 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
          <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
          <span class="text-sm">Chargement...</span>
        </div>
      </li>
    `;

    try {
      const data = await apiFetch('/api/admin/categories', { method: 'GET' });
      categories = normalizeList(data);
      render();
    } catch (e) {
      els.list.innerHTML = `
        <li class="list-group-item border-0">
          <div class="text-sm text-danger">${escapeHtml(e.message || 'Erreur lors du chargement')}</div>
        </li>
      `;
    } finally {
      loading = false;
    }
  }

  function render() {
    if (!categories.length) {
      els.list.innerHTML = `
        <li class="list-group-item border-0">
          <div class="text-sm text-secondary">Aucune catégorie</div>
        </li>
      `;
      return;
    }

    els.list.innerHTML = categories
      .map((cat) => {
        const id = cat.id_categorie;
        const name = escapeHtml(cat.nom_categorie || '');
        const nbObjets = typeof cat.nb_objets !== 'undefined' ? Number(cat.nb_objets) : null;

        return `
          <li class="list-group-item border-0 d-flex align-items-center justify-content-between" data-cat-id="${id}">
            <div class="d-flex flex-column">
              <h6 class="mb-0 text-sm">${name}</h6>
              ${nbObjets !== null ? `<span class="text-xs text-secondary">Objets: ${nbObjets}</span>` : `<span class="text-xs text-secondary"> </span>`}
            </div>
            <div class="d-flex align-items-center gap-2">
              <button class="btn btn-link text-dark text-sm mb-0 px-2 py-1" type="button" data-action="edit" title="Modifier">
                <img src="${ICON_EDIT}" alt="Modifier" style="width: 16px; height: 16px;">
              </button>
              <button class="btn btn-link text-danger text-sm mb-0 px-2 py-1" type="button" data-action="archive" title="Archiver">
                <img src="${ICON_ARCHIVE}" alt="Archiver" style="width: 16px; height: 16px;">
              </button>
            </div>
          </li>
        `;
      })
      .join('');
  }

  function getCatById(id) {
    return categories.find((c) => String(c.id_categorie) === String(id));
  }

  async function createCategory(nom) {
    return apiFetch('/api/admin/categories', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nom_categorie: nom }),
    });
  }

  async function updateCategory(id, nom) {
    return apiFetch(`/api/admin/categories/${encodeURIComponent(id)}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ nom_categorie: nom }),
    });
  }

  async function archiveCategory(id) {
    return apiFetch(`/api/admin/categories/${encodeURIComponent(id)}/archive`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({}),
    });
  }

  function openCreate() {
    showForm('create');
  }

  function openEdit(id) {
    const cat = getCatById(id);
    if (!cat) return;
    showForm('edit', cat);
  }

  async function handleSave() {
    const nom = (els.nameInput.value || '').trim();
    if (!nom) {
      setMsg('Le nom est requis', 'danger');
      return;
    }

    els.saveBtn.disabled = true;
    setMsg('Enregistrement...', 'warning');

    try {
      if (editingId) {
        const res = await updateCategory(editingId, nom);
        if (!res.success) {
          throw new Error(res.message || 'Mise à jour impossible');
        }
      } else {
        const res = await createCategory(nom);
        if (!res.success) {
          throw new Error(res.message || 'Création impossible');
        }
      }

      setMsg('OK', 'success');
      await loadCategories();
      hideForm();
    } catch (e) {
      setMsg(e.message || 'Erreur', 'danger');
    } finally {
      els.saveBtn.disabled = false;
    }
  }

  async function handleArchive(id) {
    const cat = getCatById(id);
    if (!cat) return;

    const ok = confirm(`Archiver la catégorie "${cat.nom_categorie}" ?\nElle ne sera plus affichée.`);
    if (!ok) return;

    try {
      const res = await archiveCategory(id);
      if (!res.success) {
        throw new Error(res.message || "Archivage impossible");
      }
      await loadCategories();
      if (editingId && String(editingId) === String(id)) {
        hideForm();
      }
    } catch (e) {
      alert(e.message || 'Erreur');
    }
  }

  els.addBtn.addEventListener('click', openCreate);
  if (els.cancelBtn) els.cancelBtn.addEventListener('click', hideForm);
  if (els.resetBtn) {
    els.resetBtn.addEventListener('click', function () {
      if (editingId) {
        const cat = getCatById(editingId);
        if (cat) {
          els.nameInput.value = cat.nom_categorie || '';
        }
      } else {
        els.nameInput.value = '';
      }
      setMsg('', '');
    });
  }

  els.saveBtn.addEventListener('click', handleSave);

  els.list.addEventListener('click', function (e) {
    const btn = e.target.closest('button[data-action]');
    if (!btn) return;

    const li = e.target.closest('li[data-cat-id]');
    if (!li) return;

    const id = li.getAttribute('data-cat-id');
    const action = btn.getAttribute('data-action');

    if (action === 'edit') {
      openEdit(id);
    } else if (action === 'archive') {
      handleArchive(id);
    }
  });

  loadCategories();
})();
