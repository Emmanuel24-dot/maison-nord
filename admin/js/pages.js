const Pages = (() => {

  /* ── Icons ── */
  const ICONS = {
    grid:        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
    package:     '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16.5 9.4 7.55 4.24M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="m3.27 6.96 8.73 5.05 8.73-5.05M12 22.08V12"/></svg>',
    cart:        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>',
    users:       '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    message:     '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
    file:        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><path d="M14 2v6h6"/></svg>',
    settings:    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>',
    clock:       '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
    check:       '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>',
    truck:       '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2M15 18H9M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>',
    packageCheck:'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m16 16 2 2 4-4M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/><path d="m7.5 4.27 9 5.15M3.29 7 12 12l8.71-5M12 22V12"/></svg>',
    x:           '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/></svg>',
    euro:        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 10h12M4 14h9M19 6a7.7 7.7 0 0 0-5.2-2A7.9 7.9 0 0 0 6 12c0 4.4 3.5 8 7.8 8 2 0 3.8-.8 5.2-2"/></svg>',
    trend:       '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 7-8.5 8.5-5-5L2 17"/><path d="M16 7h6v6"/></svg>',
    eye:         '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>',
    userCheck:   '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m16 11 2 2 4-4"/></svg>',
    userX:       '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m17 8 5 5M22 8l-5 5"/></svg>',
    mail:        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>',
    calendar:    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',
    bag:         '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/></svg>',
    pencil:      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>',
    trash:       '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>',
    plus:        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5v14"/></svg>',
    search:      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>',
    reply:       '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>',
  };

  /* ── Helpers ── */
  const FCFA = n => Number(n||0).toLocaleString('fr-FR') + ' FCFA';
  const esc  = s => String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  const init = n => String(n||'').split(' ').map(s=>s[0]||'').slice(0,2).join('').toUpperCase() || '?';

  /* shared unread count, updated whenever messages are loaded */
  let _unread = 0;
  function getUnread() { return _unread; }

  function setContent(html) { document.getElementById('page-content').innerHTML = html; }
  function setLoading()     { setContent('<div class="page-loading">Chargement…</div>'); }
  function setError(msg)    {
    setContent(`<div class="page-header"><div><h1 class="page-title">Erreur</h1>
      <p class="page-desc" style="color:var(--destructive)">${esc(msg)}</p></div></div>`);
  }

  function openModal(html, large) {
    const overlay = document.getElementById('modal-overlay');
    const modal   = document.getElementById('modal');
    modal.className = large ? 'modal modal-lg' : 'modal';
    modal.innerHTML = html;
    overlay.classList.add('open');
  }
  function closeModal() { document.getElementById('modal-overlay').classList.remove('open'); }

  /* ── Statut commandes ── */
  const statutCfg = {
    en_attente: { label:'En attente', icon:'clock',        badge:'badge-amber'   },
    validee:    { label:'Validée',    icon:'check',        badge:'badge-blue'    },
    expediee:   { label:'Expédiée',   icon:'truck',        badge:'badge-purple'  },
    livree:     { label:'Livrée',     icon:'packageCheck', badge:'badge-emerald' },
    annulee:    { label:'Annulée',    icon:'x',            badge:'badge-red'     },
  };
  const flow = ['en_attente','validee','expediee','livree'];

  /* ── CRUD configs ── */
  const crudCfg = {
    produits: {
      title: 'Produits', desc: 'Gérez votre catalogue de produits.',
      fields: [
        { name:'nom',       label:'Nom du produit' },
        { name:'categorie', label:'Catégorie'       },
        { name:'prix',      label:'Prix (FCFA)', type:'number' },
        { name:'stock',     label:'Stock',       type:'number' },
      ],
    },
    articles: {
      title: 'Articles', desc: 'Publiez et gérez les articles du blog.',
      fields: [
        { name:'titre',   label:'Titre'   },
        { name:'auteur',  label:'Auteur'  },
        { name:'extrait', label:'Extrait', type:'textarea' },
        { name:'contenu', label:'Contenu', type:'textarea' },
      ],
    },
    parametres: {
      title: 'Paramètres', desc: "Configuration générale de l'application.",
      fields: [
        { name:'cle',    label:'Clé'    },
        { name:'valeur', label:'Valeur' },
      ],
    },
  };

  /* ══════════════ DASHBOARD ══════════════ */
  async function renderDashboard() {
    setLoading();
    let s;
    try { s = await AdminStore.load('stats'); }
    catch(e) { setError(e.message); return; }

    _unread = s.nb_non_lus || 0;

    /* ── KPI Cards ── */
    const statCards = [
      { label:'Produits actifs',  val: s.produits,              icon:'package', sub: '' },
      { label:'Commandes total',  val: s.commandes,             icon:'cart',    sub: `${s.nb_attente} en attente` },
      { label:'Clients',          val: s.users,                 icon:'users',   sub: '' },
      { label:"Chiffre d'aff.",   val: FCFA(s.revenue),         icon:'euro',    sub: `${s.nb_livrees} livrées` },
    ].map(c=>`<div class="card stat-card">
      <div class="stat-icon">${ICONS[c.icon]}</div>
      <div>
        <div class="stat-label">${c.label}</div>
        <div class="stat-value">${c.val}</div>
        ${c.sub ? `<div class="stat-sub">${c.sub}</div>` : ''}
      </div>
    </div>`).join('');

    /* ── Graphique barres CA mensuel ── */
    const monthly = s.monthly || [];
    const maxCA = Math.max(1, ...monthly.map(m => m.ca));
    const barChart = monthly.map(m => {
      const pct = Math.round((m.ca / maxCA) * 100);
      return `<div class="db-bar-col">
        <div class="db-bar-wrap">
          <div class="db-bar" style="height:${pct}%" title="${FCFA(m.ca)}"></div>
        </div>
        <span class="db-bar-lbl">${esc(m.label)}</span>
        <span class="db-bar-val">${m.ca > 0 ? FCFA(m.ca) : '—'}</span>
      </div>`;
    }).join('');

    /* ── Top produits ── */
    const topProdRows = (s.top_produits||[]).map((p,i)=>`
      <tr>
        <td><span class="db-rank">${i+1}</span> ${esc(p.name)}</td>
        <td style="text-align:right">${p.total_qty} vte${p.total_qty>1?'s':''}</td>
        <td style="text-align:right">${FCFA(p.total_ca)}</td>
      </tr>`).join('') || `<tr><td colspan="3" class="td-empty">Aucune donnée</td></tr>`;

    /* ── Top clients ── */
    const topClientsRows = (s.top_clients||[]).map((c,i)=>`
      <tr>
        <td><span class="db-rank">${i+1}</span> ${esc(c.name)}
          <div class="text-muted" style="font-size:.72rem">${esc(c.email)}</div>
        </td>
        <td style="text-align:right">${c.nb_orders} cmd${c.nb_orders>1?'s':''}</td>
        <td style="text-align:right">${FCFA(c.total_ca)}</td>
      </tr>`).join('') || `<tr><td colspan="3" class="td-empty">Aucune donnée</td></tr>`;

    /* ── Commandes récentes ── */
    const cmdRows = (s.recent_cmds||[]).map(c=>{
      const cfg = statutCfg[c.statut]||{label:c.statut,badge:'badge-secondary'};
      return `<tr><td><strong>${esc(c.client)}</strong>
        <div class="text-muted" style="font-size:.75rem">${esc(c.date)}</div></td>
        <td>${FCFA(c.total)}</td><td><span class="badge ${cfg.badge}">${cfg.label}</span></td></tr>`;
    }).join('') || `<tr><td colspan="3" class="td-empty">Aucune commande</td></tr>`;

    /* ── Messages récents ── */
    const msgRows = (s.recent_msgs||[]).map(m=>`
      <tr ${!m.lu?'style="font-weight:600"':''}>
        <td>${!m.lu?'<span class="unread-dot"></span>':''}${esc(m.expediteur)}</td>
        <td class="td-truncate">${esc(m.sujet)}</td>
      </tr>`).join('') || `<tr><td colspan="2" class="td-empty">Aucun message</td></tr>`;

    setContent(`
      <div class="page-header">
        <div><h1 class="page-title">Tableau de bord</h1>
        <p class="page-desc">Vue d'ensemble de l'activité Maison Nord.</p></div>
      </div>

      <div class="stats-grid stats-grid-4">${statCards}</div>

      <!-- Graphique CA + résumé -->
      <div class="dashboard-grid" style="margin-bottom:1.25rem">
        <div class="card" style="grid-column:1/-1">
          <div class="card-header card-header-padded">
            <div class="card-header-title">${ICONS.trend} Chiffre d'affaires — 6 derniers mois</div>
          </div>
          <div style="padding:1.25rem 1.5rem">
            <div class="db-bar-chart">${barChart}</div>
          </div>
        </div>
      </div>

      <!-- Top produits + Top clients -->
      <div class="dashboard-grid" style="margin-bottom:1.25rem">
        <div class="card">
          <div class="card-header card-header-padded">
            <div class="card-header-title">${ICONS.package} Top produits</div>
            <a href="#produits" class="btn btn-ghost btn-sm" id="lnk-prod">Voir tout →</a>
          </div>
          <div class="card-body-flush table-wrap"><table>
            <thead><tr><th>Produit</th><th style="text-align:right">Ventes</th><th style="text-align:right">CA</th></tr></thead>
            <tbody>${topProdRows}</tbody>
          </table></div>
        </div>
        <div class="card">
          <div class="card-header card-header-padded">
            <div class="card-header-title">${ICONS.users} Meilleurs clients</div>
            <a href="#users" class="btn btn-ghost btn-sm" id="lnk-usr">Voir tout →</a>
          </div>
          <div class="card-body-flush table-wrap"><table>
            <thead><tr><th>Client</th><th style="text-align:right">Cmdtes</th><th style="text-align:right">CA</th></tr></thead>
            <tbody>${topClientsRows}</tbody>
          </table></div>
        </div>
      </div>

      <!-- Commandes récentes + Messages -->
      <div class="dashboard-grid">
        <div class="card">
          <div class="card-header card-header-padded">
            <div class="card-header-title">${ICONS.cart} Commandes récentes</div>
            <a href="#commandes" class="btn btn-ghost btn-sm" id="lnk-cmd">Voir tout →</a>
          </div>
          <div class="card-body-flush table-wrap"><table>
            <thead><tr><th>Client</th><th>Total</th><th>Statut</th></tr></thead>
            <tbody>${cmdRows}</tbody>
          </table></div>
        </div>
        <div class="card">
          <div class="card-header card-header-padded">
            <div class="card-header-title">${ICONS.message} Messages
              ${_unread?`<span class="badge badge-red" style="margin-left:.5rem">${_unread}</span>`:''}
            </div>
            <a href="#messages" class="btn btn-ghost btn-sm" id="lnk-msg">Voir tout →</a>
          </div>
          <div class="card-body-flush table-wrap"><table>
            <thead><tr><th>Expéditeur</th><th>Sujet</th></tr></thead>
            <tbody>${msgRows}</tbody>
          </table></div>
        </div>
      </div>`);

    document.getElementById('lnk-prod')?.addEventListener('click', e=>{e.preventDefault();location.hash='produits';});
    document.getElementById('lnk-usr')?.addEventListener('click', e=>{e.preventDefault();location.hash='users';});
    document.getElementById('lnk-cmd')?.addEventListener('click', e=>{e.preventDefault();location.hash='commandes';});
    document.getElementById('lnk-msg')?.addEventListener('click', e=>{e.preventDefault();location.hash='messages';});
  }

  /* ══════════════ CRUD GÉNÉRIQUE ══════════════ */
  async function renderCrud(storeKey, openNew) {
    setLoading();
    let rows;
    try { rows = await AdminStore.load(storeKey); }
    catch(e) { setError(e.message); return; }

    const cfg    = crudCfg[storeKey];
    const cols   = cfg.fields.slice(0, 3);

    const tableRows = rows.length ? rows.map(row=>`<tr>
      ${cols.map(f=>{
        if (f.name==='prix')  return `<td>${FCFA(row[f.name]||0)}</td>`;
        if (f.name==='stock') return `<td><span class="badge ${Number(row[f.name])>10?'badge-emerald':Number(row[f.name])>0?'badge-amber':'badge-red'}">${row[f.name]??0}</span></td>`;
        return `<td class="td-truncate">${esc(row[f.name]??'')}</td>`;
      }).join('')}
      <td class="td-actions">
        <div style="display:flex;justify-content:flex-end;gap:.25rem">
          <button class="btn btn-ghost btn-icon" data-action="edit" data-id="${esc(row.id)}" title="Modifier">${ICONS.pencil}</button>
          <button class="btn btn-ghost btn-icon" data-action="del"  data-id="${esc(row.id)}" title="Supprimer">${ICONS.trash}</button>
        </div>
      </td></tr>`).join('')
      : `<tr><td colspan="${cols.length+1}" class="td-empty">Aucun élément. Cliquez sur « Nouveau ».</td></tr>`;

    setContent(`
      <div class="page-header">
        <div><h1 class="page-title">${cfg.title}</h1><p class="page-desc">${cfg.desc}</p></div>
        <button class="btn btn-primary" id="crud-new">${ICONS.plus} Nouveau</button>
      </div>
      <div class="card"><div class="table-wrap"><table>
        <thead><tr>
          ${cols.map(f=>f.name==='prix'?'<th>Prix (FCFA)</th>':`<th>${f.label}</th>`).join('')}
          <th class="td-actions">Actions</th>
        </tr></thead>
        <tbody>${tableRows}</tbody>
      </table></div></div>`);

    document.getElementById('crud-new').addEventListener('click', ()=>openCrudForm(storeKey, null, rows));

    document.querySelectorAll('[data-action=edit]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const row = rows.find(r=>String(r.id)===btn.dataset.id);
        if (row) openCrudForm(storeKey, row, rows);
      });
    });
    document.querySelectorAll('[data-action=del]').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        if (!confirm('Confirmer la suppression ?')) return;
        try {
          await AdminStore.remove(storeKey, btn.dataset.id);
          Toast.show('Supprimé avec succès');
          await renderCrud(storeKey);
        } catch(e) { Toast.show(e.message, 'error'); }
      });
    });

    if (openNew) openCrudForm(storeKey, null, rows);
  }

  function openCrudForm(storeKey, editing, _rows) {
    const cfg = crudCfg[storeKey];
    const fieldsHtml = cfg.fields.map(f=>{
      const val = editing ? esc(editing[f.name]??'') : '';
      const disabled = (storeKey==='parametres' && f.name==='cle' && editing) ? 'disabled' : '';
      const input = f.type==='textarea'
        ? `<textarea class="form-textarea" id="f-${f.name}" name="${f.name}" ${disabled}>${val}</textarea>`
        : `<input class="form-input" id="f-${f.name}" name="${f.name}" type="${f.type==='number'?'number':'text'}" value="${val}" ${disabled}>`;
      return `<div class="form-group"><label for="f-${f.name}">${f.label}</label>${input}</div>`;
    }).join('');

    openModal(`
      <div class="modal-title">${editing?'Modifier':'Créer'} — ${cfg.title}</div>
      <form id="crud-form">
        ${fieldsHtml}
        <div class="form-footer">
          <button class="btn btn-outline" type="button" id="form-cancel">Annuler</button>
          <button class="btn btn-primary" type="submit" id="form-submit">${editing?'Enregistrer':'Créer'}</button>
        </div>
      </form>`);

    document.getElementById('form-cancel').addEventListener('click', closeModal);
    document.getElementById('crud-form').addEventListener('submit', async e=>{
      e.preventDefault();
      const btn = document.getElementById('form-submit');
      btn.disabled = true; btn.textContent = 'Enregistrement…';
      try {
        const data = {};
        for (const f of cfg.fields) {
          const el = document.getElementById(`f-${f.name}`);
          if (!el||el.disabled) continue;
          data[f.name] = f.type==='number' ? Number(el.value||0) : el.value;
        }
        if (editing) { await AdminStore.update(storeKey, editing.id, data); Toast.show('Modifié avec succès'); }
        else         { await AdminStore.create(storeKey, data);             Toast.show('Créé avec succès');    }
        closeModal();
        await renderCrud(storeKey);
      } catch(err) {
        Toast.show(err.message||'Erreur', 'error');
        btn.disabled=false; btn.textContent=editing?'Enregistrer':'Créer';
      }
    });
  }

  /* ══════════════ MESSAGES ══════════════ */
  async function renderMessages() {
    setLoading();
    let msgs;
    try { msgs = await AdminStore.load('messages'); }
    catch(e) { setError(e.message); return; }

    _unread = msgs.filter(m=>!m.lu).length;

    const rows = msgs.map(m=>`
      <tr ${!m.lu?'style="font-weight:600"':''}>
        <td>${!m.lu?'<span class="unread-dot"></span>':''}${esc(m.expediteur)}</td>
        <td class="td-truncate">${esc(m.sujet)}</td>
        <td>${esc(m.date)}</td>
        <td class="td-actions">
          <div style="display:flex;justify-content:flex-end;gap:.25rem">
            <button class="btn btn-ghost btn-icon" data-action="view" data-id="${m.id}" title="Voir">${ICONS.eye}</button>
            ${!m.lu?`<button class="btn btn-ghost btn-sm" data-action="read" data-id="${m.id}">Marquer lu</button>`:''}
            <button class="btn btn-ghost btn-icon" data-action="del" data-id="${m.id}" title="Supprimer">${ICONS.trash}</button>
          </div>
        </td>
      </tr>`).join('') || `<tr><td colspan="4" class="td-empty">Aucun message</td></tr>`;

    setContent(`
      <div class="page-header">
        <div><h1 class="page-title">Messages
          ${_unread?`<span class="badge badge-red" style="font-size:.875rem;vertical-align:middle;margin-left:.5rem">${_unread}</span>`:''}
        </h1><p class="page-desc">Boîte de réception des messages clients.</p></div>
        ${_unread?`<button class="btn btn-outline" id="mark-all">Tout marquer lu</button>`:''}
      </div>
      <div class="card"><div class="table-wrap"><table>
        <thead><tr><th>Expéditeur</th><th>Sujet</th><th>Date</th><th class="td-actions">Actions</th></tr></thead>
        <tbody>${rows}</tbody>
      </table></div></div>`);

    document.getElementById('mark-all')?.addEventListener('click', async ()=>{
      try {
        await AdminStore.update('messages', 0, { all_read: true });
        Toast.show('Tous marqués comme lus');
        await renderMessages();
      } catch(e) { Toast.show(e.message,'error'); }
    });

    document.querySelectorAll('[data-action=view]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const m = msgs.find(x=>String(x.id)===btn.dataset.id);
        if (m) openMessageModal(m, msgs);
      });
    });

    document.querySelectorAll('[data-action=read]').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        try {
          await AdminStore.update('messages', btn.dataset.id, { lu: true });
          Toast.show('Marqué comme lu');
          await renderMessages();
        } catch(e) { Toast.show(e.message,'error'); }
      });
    });

    document.querySelectorAll('[data-action=del]').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        if (!confirm('Supprimer ce message ?')) return;
        try {
          await AdminStore.remove('messages', btn.dataset.id);
          Toast.show('Message supprimé');
          await renderMessages();
        } catch(e) { Toast.show(e.message,'error'); }
      });
    });
  }

  function openMessageModal(m, msgs) {
    openModal(`
      <div class="modal-title">Message — ${esc(m.sujet)}</div>
      <div class="order-info" style="margin-bottom:1rem">
        <div><div class="text-muted">Expéditeur</div><div><strong>${esc(m.expediteur)}</strong></div>
          <div class="text-muted" style="font-size:.75rem">${esc(m.email)}</div></div>
        <div><div class="text-muted">Date</div><div><strong>${esc(m.date)}</strong></div>
          <div><span class="badge ${m.lu?'badge-secondary':'badge-blue'}">${m.lu?'Lu':'Non lu'}</span></div></div>
      </div>
      <div style="margin-bottom:.5rem;font-size:.875rem;font-weight:500">Contenu</div>
      <div style="background:color-mix(in oklch,var(--muted) 20%,transparent);border:1px solid var(--border);
           border-radius:var(--radius);padding:1rem;line-height:1.7;white-space:pre-wrap;font-size:.9rem">${esc(m.contenu)}</div>
      <div style="margin-top:1rem;display:flex;gap:.5rem">
        <a href="mailto:${esc(m.email)}" class="btn btn-primary">${ICONS.reply} Répondre</a>
        <button class="btn btn-outline" id="msg-close">Fermer</button>
      </div>`);

    /* marquer lu si ouvert */
    if (!m.lu) AdminStore.update('messages', m.id, { lu: true }).catch(()=>{});

    document.getElementById('msg-close').addEventListener('click', async ()=>{
      closeModal();
      await renderMessages();
    });
  }

  /* ══════════════ COMMANDES ══════════════ */
  async function renderCommandes() {
    setLoading();
    let cmds;
    try { cmds = await AdminStore.load('commandes'); }
    catch(e) { setError(e.message); return; }

    const attente  = cmds.filter(c=>c.statut==='en_attente').length;
    const livrees  = cmds.filter(c=>c.statut==='livree').length;
    const revenue  = cmds.filter(c=>c.statut!=='annulee').reduce((s,c)=>s+Number(c.total),0);

    const stats = [
      {label:'Total',          val:cmds.length,  icon:'cart'},
      {label:'En attente',     val:attente,       icon:'clock'},
      {label:'Livrées',        val:livrees,       icon:'packageCheck'},
      {label:"Chiffre d'aff.", val:FCFA(revenue), icon:'euro'},
    ].map(s=>`<div class="card stat-card"><div class="stat-icon">${ICONS[s.icon]}</div>
      <div><div class="stat-label">${s.label}</div><div class="stat-value">${s.val}</div></div></div>`).join('');

    const rows = cmds.map(c=>{
      const cfg = statutCfg[c.statut]||{label:c.statut,badge:'badge-secondary',icon:'clock'};
      return `<tr>
        <td><strong>${esc(c.numero)}</strong></td>
        <td><div>${esc(c.client)}</div><div class="text-muted" style="font-size:.75rem">${esc(c.email)}</div></td>
        <td>${esc(c.date)}</td>
        <td><strong>${FCFA(c.total)}</strong></td>
        <td><span class="badge ${cfg.badge}">${ICONS[cfg.icon]||''} ${cfg.label}</span></td>
        <td class="td-actions">
          <div style="display:flex;justify-content:flex-end;gap:.5rem">
            ${c.statut==='en_attente'?`<button class="btn btn-primary btn-sm" data-action="validate" data-id="${c.id}">${ICONS.check} Valider</button>`:''}
            <button class="btn btn-outline btn-sm" data-action="details" data-id="${c.id}">${ICONS.eye} Détails</button>
            <button class="btn btn-ghost btn-icon" data-action="del" data-id="${c.id}">${ICONS.trash}</button>
          </div>
        </td></tr>`;
    }).join('') || `<tr><td colspan="6" class="td-empty">Aucune commande</td></tr>`;

    setContent(`
      <div class="page-header"><div><h1 class="page-title">Commandes</h1>
        <p class="page-desc">Validez les commandes et suivez leur progression.</p></div></div>
      <div class="stats-grid stats-grid-4">${stats}</div>
      <div class="card">
        <div class="card-header"><div class="card-header-title">${ICONS.trend} Toutes les commandes</div></div>
        <div class="card-body-flush table-wrap"><table>
          <thead><tr><th>N°</th><th>Client</th><th>Date</th><th>Total</th><th>Statut</th><th class="td-actions">Actions</th></tr></thead>
          <tbody>${rows}</tbody>
        </table></div>
      </div>`);

    document.querySelectorAll('[data-action=validate]').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        try { await updateCommande(btn.dataset.id,'validee'); await renderCommandes(); }
        catch(e) { Toast.show(e.message,'error'); }
      });
    });
    document.querySelectorAll('[data-action=details]').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const c = cmds.find(x=>String(x.id)===btn.dataset.id);
        if (c) openCommandeModal(c, cmds);
      });
    });
    document.querySelectorAll('[data-action=del]').forEach(btn=>{
      btn.addEventListener('click', async ()=>{
        if (!confirm('Supprimer cette commande ?')) return;
        try { await AdminStore.remove('commandes',btn.dataset.id); Toast.show('Commande supprimée'); await renderCommandes(); }
        catch(e) { Toast.show(e.message,'error'); }
      });
    });
  }

  async function updateCommande(id, statut) {
    await AdminStore.update('commandes', id, { statut });
    Toast.show(`Statut mis à jour : ${statutCfg[statut]?.label||statut}`);
  }

  function openCommandeModal(c, cmds) {
    const cfg     = statutCfg[c.statut]||{label:c.statut,badge:'badge-secondary'};
    const stepIdx = flow.indexOf(c.statut);
    const pct     = stepIdx<=0 ? 0 : (stepIdx/(flow.length-1))*100;
    const arts    = c.articles||[];

    const artsHtml = arts.map(a=>`
      <div class="order-article-row">
        <div>${esc(a.nom)} <span class="text-muted">× ${a.qte}</span></div>
        <div><strong>${FCFA(Number(a.prix)*Number(a.qte))}</strong></div>
      </div>`).join('') || `<div class="order-article-row"><div class="text-muted">Aucun article</div></div>`;

    const tracking = flow.map((s,i)=>{
      const sc = statutCfg[s];
      return `<div class="tracking-step">
        <div class="tracking-dot ${stepIdx>=i?'reached':''}">${ICONS[sc.icon]||''}</div>
        <span class="tracking-label">${sc.label}</span></div>`;
    }).join('');

    const options = Object.keys(statutCfg).map(s=>
      `<option value="${s}" ${c.statut===s?'selected':''}>${statutCfg[s].label}</option>`
    ).join('');

    openModal(`
      <div class="modal-title">
        <span>${esc(c.numero)}</span>
        <span class="badge ${cfg.badge}">${cfg.label}</span>
      </div>
      <div class="order-info">
        <div><div class="text-muted">Client</div><div><strong>${esc(c.client)}</strong></div>
          <div class="text-muted" style="font-size:.75rem">${esc(c.email)}</div></div>
        <div><div class="text-muted">Date</div><div><strong>${esc(c.date)}</strong></div>
          <div class="text-muted" style="font-size:.75rem">${esc(c.ville||'')}</div></div>
      </div>
      <div style="margin-bottom:.5rem;font-size:.875rem;font-weight:500">Articles</div>
      <div class="order-articles">
        ${artsHtml}
        <div class="order-total-row"><span>Total</span><span>${FCFA(c.total)}</span></div>
      </div>
      <div style="margin-bottom:.75rem;font-size:.875rem;font-weight:500">Suivi</div>
      <div class="tracking">
        <div class="tracking-line"></div>
        <div class="tracking-progress" style="width:calc(${pct}% - 2.5rem);max-width:calc(100% - 2.5rem)"></div>
        ${tracking}
      </div>
      <div class="modal-footer">
        <div class="status-select-wrap">
          <span class="text-muted">Changer le statut :</span>
          <select class="form-select status-select" id="cmd-status">${options}</select>
        </div>
        ${c.statut==='en_attente'?`<button class="btn btn-primary" id="cmd-validate">${ICONS.check} Valider</button>`:''}
      </div>`,true);

    document.getElementById('cmd-status').addEventListener('change', async e=>{
      try { await updateCommande(c.id, e.target.value); openCommandeModal({...c,statut:e.target.value},cmds); }
      catch(err) { Toast.show(err.message,'error'); }
    });
    document.getElementById('cmd-validate')?.addEventListener('click', async ()=>{
      try { await updateCommande(c.id,'validee'); openCommandeModal({...c,statut:'validee'},cmds); }
      catch(err) { Toast.show(err.message,'error'); }
    });
  }

  /* ══════════════ UTILISATEURS ══════════════ */
  async function renderUsers() {
    setLoading();
    let users;
    try { users = await AdminStore.load('users'); }
    catch(e) { setError(e.message); return; }

    const stats = [
      {label:'Utilisateurs', val:users.length,                                    icon:'users'    },
      {label:'Actifs',       val:users.filter(u=>u.statut==='Actif').length,       icon:'userCheck'},
      {label:'Bloqués',      val:users.filter(u=>u.statut==='Bloqué').length,      icon:'userX'    },
    ].map(s=>`<div class="card stat-card"><div class="stat-icon">${ICONS[s.icon]}</div>
      <div><div class="stat-label">${s.label}</div><div class="stat-value">${s.val}</div></div></div>`).join('');

    function renderTable(q='') {
      const filtered = q ? users.filter(u=>(u.nom+' '+u.email).toLowerCase().includes(q)) : users;
      const tbody = document.getElementById('users-tbody');
      if (!tbody) return;
      tbody.innerHTML = filtered.map(u=>`<tr>
        <td><div class="user-cell">
          <span class="avatar avatar-sm">${init(u.nom)}</span>
          <div><div><strong>${esc(u.nom)}</strong></div>
            <div class="text-muted" style="font-size:.75rem">${esc(u.email)}</div></div>
        </div></td>
        <td><span class="badge ${u.role==='Admin'?'badge-default':'badge-secondary'}">${esc(u.role)}</span></td>
        <td><span class="badge ${u.statut==='Actif'?'badge-emerald':'badge-red'}">${esc(u.statut)}</span></td>
        <td>${u.commandes||0}</td>
        <td class="text-muted">${esc(u.inscrit)}</td>
        <td class="td-actions"><div style="display:flex;justify-content:flex-end;gap:.5rem">
          <button class="btn btn-outline btn-sm" data-action="view" data-id="${u.id}">${ICONS.eye} Voir</button>
          <button class="btn btn-sm ${u.statut==='Actif'?'btn-destructive':'btn-primary'}"
            data-action="toggle" data-id="${u.id}">${u.statut==='Actif'?'Bloquer':'Activer'}</button>
        </div></td></tr>`).join('')
        || `<tr><td colspan="6" class="td-empty">Aucun utilisateur trouvé</td></tr>`;

      tbody.querySelectorAll('[data-action=view]').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          const u=users.find(x=>String(x.id)===btn.dataset.id); if(u) openUserModal(u);
        });
      });
      tbody.querySelectorAll('[data-action=toggle]').forEach(btn=>{
        btn.addEventListener('click', async ()=>{
          const u=users.find(x=>String(x.id)===btn.dataset.id); if(!u) return;
          const nv=u.statut==='Actif'?'Bloqué':'Actif';
          try {
            await AdminStore.update('users',u.id,{statut:nv});
            u.statut=nv; Toast.show('Statut mis à jour');
            renderTable(document.getElementById('users-search')?.value.toLowerCase()||'');
          } catch(e){ Toast.show(e.message,'error'); }
        });
      });
    }

    setContent(`
      <div class="page-header"><div><h1 class="page-title">Utilisateurs</h1>
        <p class="page-desc">Gérez les comptes des utilisateurs.</p></div></div>
      <div class="stats-grid stats-grid-3">${stats}</div>
      <div class="card">
        <div class="card-header">
          <div class="card-header-title">Liste des utilisateurs</div>
          <div class="topnav-search" style="display:block;max-width:16rem;width:100%">
            ${ICONS.search}<input type="search" id="users-search" placeholder="Rechercher…">
          </div>
        </div>
        <div class="card-body-flush table-wrap"><table>
          <thead><tr><th>Utilisateur</th><th>Rôle</th><th>Statut</th><th>Commandes</th><th>Inscrit le</th><th class="td-actions">Actions</th></tr></thead>
          <tbody id="users-tbody"></tbody>
        </table></div>
      </div>`);

    renderTable();
    document.getElementById('users-search').addEventListener('input', e=>renderTable(e.target.value.toLowerCase()));
  }

  function openUserModal(u) {
    openModal(`
      <div class="modal-title">Profil utilisateur</div>
      <div class="profile-header">
        <span class="avatar avatar-lg">${init(u.nom)}</span>
        <div><div class="profile-name">${esc(u.nom)}</div>
          <div class="profile-email">${ICONS.mail} ${esc(u.email)}</div></div>
      </div>
      <div class="profile-grid">
        <div class="profile-stat"><div class="profile-stat-label">${ICONS.calendar} Inscrit le</div>
          <div class="profile-stat-value">${esc(u.inscrit)}</div></div>
        <div class="profile-stat"><div class="profile-stat-label">${ICONS.bag} Commandes</div>
          <div class="profile-stat-value">${u.commandes||0}</div></div>
        <div class="profile-stat"><div class="profile-stat-label">Rôle</div>
          <div class="profile-stat-value"><span class="badge ${u.role==='Admin'?'badge-default':'badge-secondary'}">${esc(u.role)}</span></div></div>
        <div class="profile-stat"><div class="profile-stat-label">Statut</div>
          <div class="profile-stat-value"><span class="badge ${u.statut==='Actif'?'badge-emerald':'badge-red'}">${esc(u.statut)}</span></div></div>
      </div>`);
  }

  /* ══════════════ PRODUITS (custom) ══════════════ */
  function adminImgSrc(url) {
    if (!url) return '';
    if (url.startsWith('http') || url.startsWith('/')) return url;
    return '../' + url;
  }

  async function renderProduits(openNew) {
    setLoading();
    let rows;
    try { rows = await AdminStore.load('produits'); }
    catch(e) { setError(e.message); return; }

    const tableRows = rows.length ? rows.map(function(row) {
      const src = adminImgSrc(row.image);
      const thumb = src
        ? `<img class="prod-thumb" src="${esc(src)}" alt="" loading="lazy">`
        : `<div class="prod-thumb-ph">🖼</div>`;
      const stock = Number(row.stock);
      const stockBadge = stock > 10
        ? `<span class="badge badge-emerald">${stock}</span>`
        : stock > 0
          ? `<span class="badge badge-amber">${stock}</span>`
          : `<span class="badge badge-red">0</span>`;
      return `<tr>
        <td><div class="prod-thumb-wrap">${thumb}</div></td>
        <td><strong>${esc(row.nom)}</strong></td>
        <td class="text-muted">${esc(row.categorie)}</td>
        <td>${FCFA(row.prix)}</td>
        <td>${stockBadge}</td>
        <td><span class="badge ${Number(row.active)?'badge-emerald':'badge-red'}">${Number(row.active)?'Actif':'Inactif'}</span></td>
        <td class="td-actions">
          <div style="display:flex;justify-content:flex-end;gap:.25rem">
            <button class="btn btn-ghost btn-icon" data-action="edit" data-id="${esc(row.id)}" title="Modifier">${ICONS.pencil}</button>
            <button class="btn btn-ghost btn-icon" data-action="del"  data-id="${esc(row.id)}" title="Supprimer">${ICONS.trash}</button>
          </div>
        </td></tr>`;
    }).join('') : `<tr><td colspan="7" class="td-empty">Aucun produit. Cliquez sur « Nouveau produit ».</td></tr>`;

    setContent(`
      <div class="page-header">
        <div><h1 class="page-title">Produits</h1><p class="page-desc">Gérez votre catalogue de produits.</p></div>
        <button class="btn btn-primary" id="prod-new">${ICONS.plus} Nouveau produit</button>
      </div>
      <div class="card"><div class="table-wrap"><table>
        <thead><tr>
          <th style="width:3.5rem">Photo</th>
          <th>Nom</th><th>Catégorie</th><th>Prix</th><th>Stock</th><th>Statut</th>
          <th class="td-actions">Actions</th>
        </tr></thead>
        <tbody>${tableRows}</tbody>
      </table></div></div>`);

    document.getElementById('prod-new').addEventListener('click', function() { openProduitForm(null, rows); });

    document.querySelectorAll('[data-action=edit]').forEach(function(btn) {
      btn.addEventListener('click', function() {
        const row = rows.find(function(r) { return String(r.id) === btn.dataset.id; });
        if (row) openProduitForm(row, rows);
      });
    });

    document.querySelectorAll('[data-action=del]').forEach(function(btn) {
      btn.addEventListener('click', async function() {
        if (!confirm('Supprimer ce produit ?')) return;
        try {
          await AdminStore.remove('produits', btn.dataset.id);
          Toast.show('Produit supprimé');
          await renderProduits();
        } catch(e) { Toast.show(e.message, 'error'); }
      });
    });

    if (openNew) openProduitForm(null, rows);
  }

  function openProduitForm(editing) {
    const v   = editing || {};
    const src = adminImgSrc(v.image || '');

    openModal(`
      <div class="modal-title">${editing ? 'Modifier le produit' : 'Nouveau produit'}</div>
      <form id="prod-form">
        <div class="prod-form-grid">
          <div>
            <div class="form-group"><label for="f-nom">Nom du produit</label>
              <input class="form-input" id="f-nom" type="text" value="${esc(v.nom||'')}" required placeholder="Ex: Canapé Roma"></div>
            <div class="form-group"><label for="f-cat">Catégorie</label>
              <input class="form-input" id="f-cat" type="text" value="${esc(v.categorie||'')}" placeholder="Ex: Canapés"></div>
            <div class="form-group"><label for="f-prix">Prix (FCFA)</label>
              <input class="form-input" id="f-prix" type="number" min="0" value="${v.prix||0}"></div>
            <div class="form-group"><label for="f-stock">Stock</label>
              <input class="form-input" id="f-stock" type="number" min="0" value="${v.stock||0}"></div>
            <div class="form-group"><label for="f-active">Statut</label>
              <select class="form-select" id="f-active">
                <option value="1" ${Number(v.active)===0?'':'selected'}>Actif</option>
                <option value="0" ${Number(v.active)===0?'selected':''}>Inactif</option>
              </select></div>
          </div>
          <div>
            <div class="form-group" style="height:100%;display:flex;flex-direction:column">
              <label>Photo du produit</label>
              <div class="img-upload-zone" id="img-zone" style="flex:1;min-height:160px">
                ${src
                  ? `<img class="img-zone-preview" id="img-preview" src="${esc(src)}" alt="">`
                  : `<div class="img-ph" id="img-ph">${ICONS.package}<span>Cliquer ou glisser une image</span></div>
                     <img class="img-zone-preview" id="img-preview" src="" style="display:none" alt="">`}
              </div>
              <input type="file" id="img-file" accept="image/*" style="display:none">
              <input class="form-input" id="f-image" type="text" value="${esc(v.image||'')}"
                     placeholder="ou coller une URL…" style="margin-top:.5rem">
              <small id="img-status" style="font-size:.75rem;min-height:1rem;display:block;margin-top:.25rem"></small>
            </div>
          </div>
        </div>
        <div class="form-footer">
          <button type="button" class="btn btn-outline" id="form-cancel">Annuler</button>
          <button type="submit" class="btn btn-primary" id="form-submit">${editing?'Enregistrer':'Créer le produit'}</button>
        </div>
      </form>`, true);

    const zone    = document.getElementById('img-zone');
    const fileIn  = document.getElementById('img-file');
    const preview = document.getElementById('img-preview');
    const ph      = document.getElementById('img-ph');
    const urlIn   = document.getElementById('f-image');
    const status  = document.getElementById('img-status');

    function showPreview(src) {
      if (preview) { preview.src = src; preview.style.display = 'block'; }
      if (ph)      { ph.style.display = 'none'; }
    }

    zone.addEventListener('click', function() { fileIn.click(); });

    fileIn.addEventListener('change', async function() {
      const file = fileIn.files[0];
      if (!file) return;
      /* instant local preview */
      const reader = new FileReader();
      reader.onload = function(ev) { showPreview(ev.target.result); };
      reader.readAsDataURL(file);
      /* upload to server */
      status.style.color = 'var(--muted-foreground)';
      status.textContent = 'Upload en cours…';
      try {
        const fd = new FormData();
        fd.append('image', file);
        const r = await fetch('api/upload.php', { method: 'POST', body: fd });
        const d = await r.json();
        if (!r.ok) throw new Error(d.error || 'Erreur upload');
        urlIn.value = d.url;
        showPreview(adminImgSrc(d.url));
        status.style.color = 'oklch(0.78 0.12 155)';
        status.textContent = '✓ Image enregistrée';
      } catch(err) {
        status.style.color = 'var(--destructive)';
        status.textContent = '✗ ' + err.message;
      }
    });

    urlIn.addEventListener('input', function() {
      const url = urlIn.value.trim();
      if (url) showPreview(url.startsWith('http') || url.startsWith('/') ? url : ('../' + url));
      else { if(preview) preview.style.display='none'; if(ph) ph.style.display='flex'; }
    });

    document.getElementById('form-cancel').addEventListener('click', closeModal);

    document.getElementById('prod-form').addEventListener('submit', async function(e) {
      e.preventDefault();
      const btn = document.getElementById('form-submit');
      btn.disabled = true; btn.textContent = 'Enregistrement…';
      try {
        const data = {
          nom:       document.getElementById('f-nom').value.trim(),
          categorie: document.getElementById('f-cat').value.trim(),
          prix:      Number(document.getElementById('f-prix').value || 0),
          stock:     Number(document.getElementById('f-stock').value || 0),
          active:    Number(document.getElementById('f-active').value),
          image:     urlIn.value.trim(),
        };
        if (!data.nom) throw new Error('Le nom est obligatoire');
        if (editing) { await AdminStore.update('produits', editing.id, data); Toast.show('Produit modifié'); }
        else         { await AdminStore.create('produits', data);             Toast.show('Produit créé');    }
        closeModal();
        await renderProduits();
      } catch(err) {
        Toast.show(err.message || 'Erreur', 'error');
        btn.disabled = false; btn.textContent = editing ? 'Enregistrer' : 'Créer le produit';
      }
    });
  }

  /* ══════════════ LIVRAISONS ══════════════ */
  async function renderLivraisons() {
    setLoading();
    let zones;
    try { zones = await AdminStore.load('livraisons'); }
    catch(e) { setError(e.message); return; }

    const rows = zones.map(z => `
      <tr>
        <td><strong>${esc(z.name)}</strong></td>
        <td class="td-truncate" style="max-width:180px;font-size:.82rem">${esc(z.cities)}</td>
        <td>${Number(z.fee).toLocaleString('fr-FR')} FCFA</td>
        <td>${esc(z.delay)}</td>
        <td><span class="badge ${z.active?'badge-emerald':'badge-secondary'}">${z.active?'Active':'Inactive'}</span></td>
        <td class="td-actions">
          <button class="btn btn-ghost btn-sm btn-icon" title="Modifier" data-edit="${z.id}">${ICONS.pencil}</button>
          <button class="btn btn-ghost btn-sm btn-icon btn-danger" title="Supprimer" data-del="${z.id}">${ICONS.trash}</button>
        </td>
      </tr>`).join('') || `<tr><td colspan="6" class="td-empty">Aucune zone définie</td></tr>`;

    setContent(`
      <div class="page-header">
        <div>
          <h1 class="page-title">Zones de livraison</h1>
          <p class="page-desc">Définissez les zones, tarifs et délais de livraison.</p>
        </div>
        <button class="btn btn-primary" id="btn-add-zone">${ICONS.plus} Nouvelle zone</button>
      </div>
      <div class="card">
        <div class="card-body-flush table-wrap">
          <table>
            <thead><tr><th>Zone</th><th>Villes / Quartiers</th><th>Frais</th><th>Délai</th><th>Statut</th><th></th></tr></thead>
            <tbody>${rows}</tbody>
          </table>
        </div>
      </div>`);

    document.getElementById('btn-add-zone').addEventListener('click', () => openZoneForm(null));

    document.querySelectorAll('[data-edit]').forEach(btn => {
      btn.addEventListener('click', () => {
        const z = zones.find(x => x.id == btn.dataset.edit);
        if (z) openZoneForm(z);
      });
    });

    document.querySelectorAll('[data-del]').forEach(btn => {
      btn.addEventListener('click', async () => {
        if (!confirm('Supprimer cette zone ?')) return;
        await AdminStore.remove('livraisons', btn.dataset.del);
        renderLivraisons();
      });
    });
  }

  function openZoneForm(zone) {
    const editing = !!zone;
    openModal(`
      <div class="modal-header">
        <h2 class="modal-title">${editing ? 'Modifier la zone' : 'Nouvelle zone de livraison'}</h2>
        <button class="modal-close" onclick="Pages.closeModal()">×</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nom de la zone *</label>
          <input id="zn-name" class="form-input" value="${editing ? esc(zone.name) : ''}" placeholder="ex: Lomé Centre">
        </div>
        <div class="form-group">
          <label class="form-label">Villes / Quartiers couverts</label>
          <input id="zn-cities" class="form-input" value="${editing ? esc(zone.cities) : ''}" placeholder="Lomé, Bè, Agoè (séparés par virgule)">
        </div>
        <div class="row" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
          <div class="form-group">
            <label class="form-label">Frais de livraison (FCFA) *</label>
            <input id="zn-fee" class="form-input" type="number" min="0" value="${editing ? zone.fee : 0}">
          </div>
          <div class="form-group">
            <label class="form-label">Délai estimé</label>
            <input id="zn-delay" class="form-input" value="${editing ? esc(zone.delay) : '24-48h'}" placeholder="ex: 2-4h, 1-2j">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Ordre d'affichage</label>
          <input id="zn-sort" class="form-input" type="number" min="0" value="${editing ? zone.sort_order : 0}">
        </div>
        <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;margin-bottom:1rem">
          <input id="zn-active" type="checkbox" ${(!editing || zone.active) ? 'checked' : ''}> Zone active
        </label>
      </div>
      <div class="modal-footer">
        <button class="btn btn-ghost" onclick="Pages.closeModal()">Annuler</button>
        <button class="btn btn-primary" id="btn-save-zone">Enregistrer</button>
      </div>`);

    document.getElementById('btn-save-zone').addEventListener('click', async () => {
      const payload = {
        name:       document.getElementById('zn-name').value.trim(),
        cities:     document.getElementById('zn-cities').value.trim(),
        fee:        parseInt(document.getElementById('zn-fee').value) || 0,
        delay:      document.getElementById('zn-delay').value.trim() || '24-48h',
        sort_order: parseInt(document.getElementById('zn-sort').value) || 0,
        active:     document.getElementById('zn-active').checked ? 1 : 0,
      };
      if (!payload.name) { Toast.show('Le nom est requis', 'error'); return; }

      try {
        if (editing) {
          await AdminStore.update('livraisons', zone.id, payload);
        } else {
          await AdminStore.create('livraisons', payload);
        }
        closeModal();
        Toast.show(editing ? 'Zone mise à jour' : 'Zone créée');
        renderLivraisons();
      } catch(e) {
        Toast.show(e.message, 'error');
      }
    });
  }

  /* ══════════════ ROUTER ══════════════ */
  const routes = {
    dashboard:  ()       => renderDashboard(),
    produits:   function(p) { return renderProduits(p.new); },
    commandes:  ()       => renderCommandes(),
    livraisons: ()       => renderLivraisons(),
    users:      ()       => renderUsers(),
    messages:   ()       => renderMessages(),
    articles:   (p)      => renderCrud('articles', p.new),
    parametres: (p)      => renderCrud('parametres', p.new),
  };

  async function render(page, params={}) {
    const fn = routes[page];
    await (fn ? fn(params) : renderDashboard());
  }

  return { render, ICONS, closeModal, getUnread };
})();
