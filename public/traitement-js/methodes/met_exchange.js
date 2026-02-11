function escapeHtml(str) {
  return String(str ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function getQueryParam(name) {
  const url = new URL(window.location.href);
  return url.searchParams.get(name);
}

async function fetchJson(url, options) {
  const res = await fetch(url, options);
  if (!res.ok) {
    return { ok: false, status: res.status };
  }
  const data = await res.json();
  return { ok: true, data };
}

function setMsg(html, type = 'info') {
  const el = document.querySelector('#exchange-msg');
  if (!el) return;
  if (!html) {
    el.innerHTML = '';
    return;
  }
  const cls = type === 'success' ? 'alert-success' : (type === 'danger' ? 'alert-danger' : 'alert-info');
  el.innerHTML = `<div class="alert ${cls} text-white mb-0" role="alert">${html}</div>`;
}

function renderTarget(obj) {
  const el = document.querySelector('#target-objet');
  if (!el) return;
  if (!obj) {
    el.innerHTML = '<div class="text-sm text-danger">Objet introuvable.</div>';
    return;
  }

  const title = escapeHtml(obj.title || '');
  const desc = escapeHtml(obj.description || '');
  const cat = escapeHtml(obj.nom_categorie || '');
  const owner = escapeHtml(obj.proprietaire_name || obj.proprietaire || '');
  const prix = escapeHtml(obj.prix_estime ?? '');

  el.innerHTML = `
    <div class="position-relative mb-3">
      <img src="../assets/img/home-decor-1.jpg" alt="img" class="img-fluid shadow border-radius-lg" style="width:100%; height:260px; object-fit:cover;">
    </div>
    <p class="text-secondary mb-1 text-sm">${cat}</p>
    <h5 class="font-weight-bolder mb-2">${title}</h5>
    <p class="text-sm mb-2">${desc}</p>
    <p class="text-sm mb-2"><span class="text-secondary">Propriétaire:</span> <span class="text-dark font-weight-bold">${owner || '-'}</span></p>
    <p class="text-sm mb-0"><span class="text-secondary">Prix estimé:</span> <span class="text-dark font-weight-bold">${prix} Ar</span></p>
  `;
}

function renderMyObjets(objets, selectedId) {
  const el = document.querySelector('#my-objets');
  if (!el) return;

  if (!Array.isArray(objets) || objets.length === 0) {
    el.innerHTML = '<div class="text-sm text-secondary">Tu n\'as aucun objet à proposer.</div>';
    return;
  }

  el.innerHTML = '';

  objets.forEach((o) => {
    const id = parseInt(o.id_objet || 0, 10);
    const title = escapeHtml(o.title || '');
    const cat = escapeHtml(o.nom_categorie || '');
    const desc = escapeHtml(o.description || '');

    const wrap = document.createElement('div');
    wrap.className = 'card mb-3 selectable-objet ' + (id === selectedId ? 'selected' : '');
    wrap.setAttribute('data-id', String(id));
    wrap.innerHTML = `
      <div class="card-body p-3">
        <p class="text-secondary mb-1 text-sm">${cat}</p>
        <h6 class="mb-2">${title}</h6>
        <p class="text-sm mb-0">${desc}</p>
      </div>
    `;
    el.appendChild(wrap);
  });
}

function renderReceived(exchanges) {
  const el = document.querySelector('#received-exchanges');
  if (!el) return;

  if (!Array.isArray(exchanges) || exchanges.length === 0) {
    el.innerHTML = '<div class="text-sm text-secondary">Aucune demande reçue.</div>';
    return;
  }

  el.innerHTML = '';

  exchanges.forEach((ex) => {
    const id = parseInt(ex.id_echange || 0, 10);
    const proposeur = escapeHtml(ex.proposeur_name || '-');
    const op = escapeHtml(ex.objet_proposer_title || ex.objet_proposer || '');
    const orq = escapeHtml(ex.objet_requise_title || ex.objet_requise || '');
    const status = escapeHtml(ex.status || '');

    const card = document.createElement('div');
    card.className = 'card mb-3';
    card.innerHTML = `
      <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h6 class="mb-1">Demande #${id}</h6>
            <p class="text-sm mb-0"><span class="text-secondary">Proposeur:</span> <span class="text-dark font-weight-bold">${proposeur}</span></p>
            <p class="text-sm mb-0"><span class="text-secondary">Il propose:</span> <span class="text-dark font-weight-bold">${op}</span></p>
            <p class="text-sm mb-0"><span class="text-secondary">Pour ton objet:</span> <span class="text-dark font-weight-bold">${orq}</span></p>
            <p class="text-xs mb-0 mt-2"><span class="badge bg-gradient-secondary">${status}</span></p>
          </div>
          <div class="text-end">
            ${status === 'attente' ? `
              <button class="btn btn-success btn-sm mb-2 w-100" data-action="accept" data-id="${id}">Accepter</button>
              <button class="btn btn-outline-danger btn-sm mb-0 w-100" data-action="refuse" data-id="${id}">Refuser</button>
            ` : ''}
          </div>
        </div>
      </div>
    `;
    el.appendChild(card);
  });
}

async function initExchangePage() {
  const targetId = parseInt(getQueryParam('target') || '0', 10);
  const btn = document.querySelector('#btn-propose');

  if (!btn) return;

  let selectedMyObjetId = 0;

  const targetRes = await fetchJson(`/api/exchange/target?id=${encodeURIComponent(String(targetId))}`);
  if (targetRes.ok) {
    renderTarget(targetRes.data);
  } else {
    renderTarget(null);
  }

  const myRes = await fetchJson('/api/exchange/my-objets');
  if (myRes.ok) {
    renderMyObjets(myRes.data, selectedMyObjetId);
  } else {
    renderMyObjets([], selectedMyObjetId);
  }

  const receivedRes = await fetchJson('/api/exchange/received?status=attente');
  if (receivedRes.ok) {
    renderReceived(receivedRes.data);
  } else {
    renderReceived([]);
  }

  const myList = document.querySelector('#my-objets');
  if (myList) {
    myList.addEventListener('click', (e) => {
      const card = e.target && e.target.closest ? e.target.closest('.selectable-objet') : null;
      if (!card) return;
      const id = parseInt(card.getAttribute('data-id') || '0', 10);
      if (!id) return;
      selectedMyObjetId = id;

      document.querySelectorAll('.selectable-objet').forEach((c) => c.classList.remove('selected'));
      card.classList.add('selected');

      btn.disabled = !targetId || !selectedMyObjetId;
      setMsg('');
    });
  }

  btn.disabled = !targetId || !selectedMyObjetId;

  btn.addEventListener('click', async () => {
    setMsg('');
    if (!targetId || !selectedMyObjetId) {
      setMsg('Sélectionne un objet à proposer.', 'danger');
      return;
    }

    btn.disabled = true;

    const res = await fetchJson('/api/exchange/create', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ objet_proposer: selectedMyObjetId, objet_requise: targetId })
    });

    if (!res.ok || !res.data || !res.data.ok) {
      const err = res.data && res.data.error ? res.data.error : 'Erreur lors de la création';
      setMsg(escapeHtml(err), 'danger');
      btn.disabled = false;
      return;
    }

    setMsg('Demande envoyée (en attente).', 'success');

    const receivedRes2 = await fetchJson('/api/exchange/received?status=attente');
    if (receivedRes2.ok) {
      renderReceived(receivedRes2.data);
    }

    btn.disabled = false;
  });

  const receivedEl = document.querySelector('#received-exchanges');
  if (receivedEl) {
    receivedEl.addEventListener('click', async (e) => {
      const btn2 = e.target && e.target.getAttribute ? e.target : null;
      if (!btn2) return;
      const action = btn2.getAttribute('data-action');
      const id = parseInt(btn2.getAttribute('data-id') || '0', 10);
      if (!action || !id) return;

      if (action !== 'accept' && action !== 'refuse') return;

      btn2.disabled = true;

      const endpoint = action === 'accept' ? '/api/exchange/accept' : '/api/exchange/refuse';
      const res = await fetchJson(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_echange: id })
      });

      if (!res.ok || !res.data || !res.data.ok) {
        const err = res.data && res.data.error ? res.data.error : 'Erreur';
        setMsg(escapeHtml(err), 'danger');
      } else {
        setMsg(action === 'accept' ? 'Échange accepté.' : 'Échange refusé.', 'success');
      }

      const receivedRes3 = await fetchJson('/api/exchange/received?status=attente');
      if (receivedRes3.ok) {
        renderReceived(receivedRes3.data);
      }
    });
  }
}

window.addEventListener('DOMContentLoaded', function () {
  initExchangePage();
});

async function EchangesAttente(id_user) {
  const echanges = await fetch(`/api/getExchange/attente/${id_user}`);
    if (!echanges.ok) {
      alert("Echec de Echanges Attente "+id_user); 
      return null;
    }

    const data = await echanges.json();
    return data;
}