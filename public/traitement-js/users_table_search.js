function escapeHtml(str) {
  return String(str ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function debounce(fn, delayMs) {
  let t = null;
  return (...args) => {
    if (t) clearTimeout(t);
    t = setTimeout(() => fn(...args), delayMs);
  };
}

async function fetchUsers(search, role) {
  const url = new URL('/api/get/users', window.location.origin);
  if (typeof search === 'string' && search.trim() !== '') {
    url.searchParams.set('search', search.trim());
  }
  if (typeof role === 'string' && role.trim() !== '') {
    url.searchParams.set('role', role.trim());
  }

  const res = await fetch(url.toString(), {
    headers: { 'Accept': 'application/json' }
  });

  if (!res.ok) {
    return { ok: false, data: [] };
  }

  const data = await res.json();
  return { ok: true, data };
}

function renderUsersRows(users) {
  const tbody = document.querySelector('#users-tbody');
  if (!tbody) return;

  if (!Array.isArray(users) || users.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-4">
          <span class="text-sm text-secondary">Aucun utilisateur trouvé.</span>
        </td>
      </tr>
    `;
    return;
  }

  const teamImages = [
    '../assets/img/team-2.jpg',
    '../assets/img/team-3.jpg',
    '../assets/img/team-4.jpg',
  ];

  tbody.innerHTML = users.map((user) => {
    const idUser = parseInt(user.id_user || 0, 10);
    const name = escapeHtml(user.name || '');
    const email = escapeHtml(user.email || '');
    const role = escapeHtml(user.role || 'user');
    const phone = escapeHtml(user.phone || '');
    const status = String(user.status || 'inactive').toLowerCase();
    const joinDate = String(user.join_date || '');

    const badgeClass = status === 'active' ? 'bg-gradient-success' : 'bg-gradient-secondary';
    const statusLabel = status === 'active' ? 'Online' : 'Offline';

    const avatar = teamImages[Math.abs(idUser) % teamImages.length];

    let formattedJoinDate = '—';
    if (joinDate) {
      const d = new Date(joinDate);
      if (!Number.isNaN(d.getTime())) {
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = String(d.getFullYear()).slice(-2);
        formattedJoinDate = `${day}/${month}/${year}`;
      } else {
        formattedJoinDate = escapeHtml(joinDate);
      }
    }

    const profileUrl = `/detailsprofill/${encodeURIComponent(String(idUser))}`;

    return `
      <tr>
        <td>
          <div class="d-flex px-2 py-1">
            <div>
              <a href="${profileUrl}" class="text-decoration-none">
                <img src="${escapeHtml(avatar)}" class="avatar avatar-sm me-3" alt="user${escapeHtml(String(idUser))}">
              </a>
            </div>
            <div class="d-flex flex-column justify-content-center">
              <h6 class="mb-0 text-sm">
                <a href="${profileUrl}" class="text-dark text-decoration-none">${name}</a>
              </h6>
              <p class="text-xs text-secondary mb-0">${email}</p>
            </div>
          </div>
        </td>
        <td>
          <p class="text-xs font-weight-bold mb-0">${role ? role.charAt(0).toUpperCase() + role.slice(1) : ''}</p>
          <p class="text-xs text-secondary mb-0">${phone || '—'}</p>
        </td>
        <td class="align-middle text-center text-sm">
          <span class="badge badge-sm ${escapeHtml(badgeClass)}">${escapeHtml(statusLabel)}</span>
        </td>
        <td class="align-middle text-center">
          <span class="text-secondary text-xs font-weight-bold">${escapeHtml(formattedJoinDate)}</span>
        </td>
        <td class="align-middle">
          <a href="javascript:;" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit user">
            <i class="fa fa-pen me-1"></i>
            Edit
          </a>
        </td>
      </tr>
    `;
  }).join('');
}

async function refreshUsers() {
  const input = document.querySelector('#users-search');
  const select = document.querySelector('#users-role');
  const search = input ? input.value : '';
  const role = select ? select.value : '';

  const res = await fetchUsers(search, role);
  if (!res.ok) {
    renderUsersRows([]);
    return;
  }

  renderUsersRows(res.data);
}

function initUsersReactiveSearch() {
  const input = document.querySelector('#users-search');
  const select = document.querySelector('#users-role');

  if (!input && !select) return;

  const debounced = debounce(() => {
    refreshUsers();
  }, 250);

  if (input) {
    input.addEventListener('input', debounced);
  }

  if (select) {
    select.addEventListener('change', () => {
      refreshUsers();
    });
  }
}

document.addEventListener('DOMContentLoaded', initUsersReactiveSearch);
