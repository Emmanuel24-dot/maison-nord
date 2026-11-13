(() => {
  const navItems = [
    { title: 'Tableau de bord', page: 'dashboard',  icon: 'grid'     },
    { divider: true },
    { title: 'Produits',        page: 'produits',   icon: 'package'  },
    { title: 'Commandes',       page: 'commandes',  icon: 'cart'     },
    { title: 'Livraisons',      page: 'livraisons', icon: 'truck'    },
    { title: 'Utilisateurs',    page: 'users',      icon: 'users'    },
    { title: 'Messages',        page: 'messages',   icon: 'message'  },
    { divider: true },
    { title: 'Articles',        page: 'articles',   icon: 'file'     },
    { title: 'Paramètres',      page: 'parametres', icon: 'settings' },
  ];

  const sidebar      = document.getElementById('sidebar');
  const sidebarNav   = document.getElementById('sidebar-nav');
  const backdrop     = document.getElementById('sidebar-backdrop');
  const modalOverlay = document.getElementById('modal-overlay');

  function getRoute() {
    const hash = location.hash.slice(1) || 'dashboard';
    const [page, query] = hash.split('?');
    const params = {};
    if (query) new URLSearchParams(query).forEach((v, k) => { params[k] = v; });
    return { page, params };
  }

  function renderSidebar() {
    const { page } = getRoute();
    const unread = Pages.getUnread();

    sidebarNav.innerHTML = navItems.map(item => {
      if (item.divider) return '<div class="sidebar-divider"></div>';

      const isActive = page === item.page;
      const badge = (item.page === 'messages' && unread > 0)
        ? `<span class="nav-unread-badge">${unread}</span>`
        : '';

      return `<div class="sidebar-item">
        <a class="sidebar-btn${isActive ? ' active' : ''}" href="#${item.page}">
          ${Pages.ICONS[item.icon]}
          <span class="sidebar-text">${item.title}</span>
          ${badge}
        </a>
      </div>`;
    }).join('');

    const currentItem = navItems.filter(i => !i.divider).find(i => i.page === page);
    const titleEl = document.getElementById('topnav-title');
    if (titleEl && currentItem) titleEl.textContent = currentItem.title;

    sidebarNav.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', closeMobileSidebar);
    });
  }

  async function renderPage() {
    const { page, params } = getRoute();
    renderSidebar();
    await Pages.render(page, { new: params.new === '1' });
    renderSidebar(); /* refresh badge after message pages */
    const currentItem = navItems.filter(i => !i.divider).find(i => i.page === page);
    document.title = `Admin — ${currentItem ? currentItem.title : 'Maison Nord'}`;
  }

  function closeMobileSidebar() {
    sidebar.classList.remove('mobile-open');
    backdrop.classList.remove('open');
  }

  function toggleSidebar() {
    if (window.innerWidth <= 768) {
      sidebar.classList.toggle('mobile-open');
      backdrop.classList.toggle('open');
    } else {
      sidebar.classList.toggle('collapsed');
    }
  }

  document.getElementById('sidebar-toggle').addEventListener('click', toggleSidebar);
  backdrop.addEventListener('click', closeMobileSidebar);
  modalOverlay.addEventListener('click', function(e) {
    if (e.target === modalOverlay) Pages.closeModal();
  });
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') Pages.closeModal();
  });

  window.addEventListener('hashchange', renderPage);

  Toast.init();
  if (!location.hash) location.hash = 'dashboard';
  else renderPage();
})();
