
  const clients = [
    {
      id: 1, name: 'client1', initials: 'C1', balance: '3 100 000 F', amount: '3,100.00', status: 'suspendu',
      activities: [
        { sinistre: 'Vol', statut: 'urgent', categorie: 'élevé', score: '15 pts', somme: '-200 000 F', date: '02.05.2026', heure: '10:30', neg: true }
      ],
      transfers: [
        { label: 'Vers client5', montant: '200 000 F', date: '28.04.2026', dir: 'out' },
        { label: 'De client3', montant: '50 000 F', date: '10.04.2026', dir: 'in' }
      ],
      budgets: [{ label: 'Habitation', pct: 72 }, { label: 'Auto', pct: 45 }, { label: 'Santé', pct: 90 }],
      notifs: [
        { type: 'danger', icon: 'ti-alert-triangle', title: 'Compte suspendu', time: 'Il y a 2 jours' },
        { type: 'warn', icon: 'ti-bell', title: 'Cotisation en retard', time: 'Il y a 5 jours' }
      ]
    },
    {
      id: 2, name: 'client2', initials: 'C2', balance: '7 235 674 F', amount: '2,794.00', status: 'actif',
      activities: [
        { sinistre: 'Incendies', statut: 'normal', categorie: 'élevé', score: '10 pts', somme: '-350 000 F', date: '07.05.2026', heure: '19:14', neg: true },
        { sinistre: 'Dégât des eaux', statut: 'en cours', categorie: 'moyen', score: '7 pts', somme: '-120 000 F', date: '01.05.2026', heure: '11:00', neg: true }
      ],
      transfers: [
        { label: 'Vers client3', montant: '150 000 F', date: '05.05.2026', dir: 'out' },
        { label: 'De client4', montant: '80 000 F', date: '02.05.2026', dir: 'in' },
        { label: 'Vers client1', montant: '50 000 F', date: '10.04.2026', dir: 'out' }
      ],
      budgets: [{ label: 'Habitation', pct: 55 }, { label: 'Auto', pct: 30 }, { label: 'Santé', pct: 60 }, { label: 'Vie', pct: 20 }],
      notifs: [
        { type: 'info', icon: 'ti-info-circle', title: 'Sinistre validé — Incendies', time: 'Il y a 1 heure' },
        { type: 'warn', icon: 'ti-bell', title: 'Renouvellement prévu le 01.06', time: 'Il y a 3 jours' }
      ]
    },
    {
      id: 3, name: 'client3', initials: 'C3', balance: '1 500 000 F', amount: '1,500.00', status: 'actif',
      activities: [
        { sinistre: 'Accident auto', statut: 'clôturé', categorie: 'moyen', score: '5 pts', somme: '+500 000 F', date: '20.04.2026', heure: '09:00', neg: false }
      ],
      transfers: [{ label: 'De client2', montant: '150 000 F', date: '05.05.2026', dir: 'in' }],
      budgets: [{ label: 'Auto', pct: 80 }, { label: 'Santé', pct: 40 }],
      notifs: [{ type: 'info', icon: 'ti-check', title: 'Remboursement effectué', time: 'Il y a 2 semaines' }]
    },
    {
      id: 4, name: 'client4', initials: 'C4', balance: '5 000 000 F', amount: '5,000.00', status: 'actif',
      activities: [
        { sinistre: 'Tempête', statut: 'urgent', categorie: 'élevé', score: '18 pts', somme: '-780 000 F', date: '06.05.2026', heure: '16:45', neg: true },
        { sinistre: 'Grêle', statut: 'normal', categorie: 'faible', score: '3 pts', somme: '-40 000 F', date: '30.04.2026', heure: '08:20', neg: true }
      ],
      transfers: [{ label: 'Vers client2', montant: '80 000 F', date: '02.05.2026', dir: 'out' }],
      budgets: [{ label: 'Habitation', pct: 65 }, { label: 'Auto', pct: 50 }, { label: 'Agricole', pct: 35 }],
      notifs: [{ type: 'warn', icon: 'ti-alert-circle', title: 'Sinistre Tempête en traitement', time: 'Il y a 1 jour' }]
    },
    {
      id: 5, name: 'client5', initials: 'C5', balance: '900 000 F', amount: '900.00', status: 'actif',
      activities: [],
      transfers: [{ label: 'De client1', montant: '200 000 F', date: '28.04.2026', dir: 'in' }],
      budgets: [{ label: 'Santé', pct: 15 }],
      notifs: [{ type: 'info', icon: 'ti-bell', title: 'Bienvenue sur la plateforme', time: 'Il y a 1 mois' }]
    }
  ];

  let selectedClient = clients[1];

  function renderClientList(filter = '') {
    const list = document.getElementById('client-list');
    list.innerHTML = '';
    clients.filter(c => c.name.includes(filter)).forEach(c => {
      const div = document.createElement('div');
      div.className = 'client-card' + (c.id === selectedClient.id ? ' active' : '');
      div.innerHTML = `
        <div class="client-card-top">
          <div class="avatar">${c.initials}</div>
          <span class="client-name">${c.name}</span>
        </div>
        <div class="client-card-bottom">
          <span class="${c.status === 'actif' ? 'badge-actif' : 'badge-suspendu'}">${c.status === 'actif' ? 'Actif' : 'Suspendu'}</span>
          <span class="client-amount">$${c.amount}</span>
        </div>`;
      div.addEventListener('click', () => {
        selectedClient = c;
        renderClientList(filter);
        renderDetail();
      });
      list.appendChild(div);
    });
  }

  function renderDetail() {
    const c = selectedClient;
    document.getElementById('detail-avatar').textContent = c.initials;
    document.getElementById('detail-name').textContent = c.name;
    document.getElementById('detail-balance').textContent = c.balance;
    const btn = document.getElementById('btn-suspend');
    const suspended = c.status === 'suspendu';
    btn.textContent = suspended ? 'Réactiver' : 'Suspendre';
    btn.className = 'btn-suspend' + (suspended ? ' suspended' : '');
    renderActivities();
    renderTransfers();
    renderBudgets();
    renderNotifs();
  }

  function renderActivities() {
    const tb = document.getElementById('activities-body');
    const acts = selectedClient.activities;
    if (!acts.length) {
      tb.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:32px;color:#6b7280">Aucune activité</td></tr>`;
      return;
    }
    tb.innerHTML = acts.map(a => `<tr>
      <td>${a.sinistre}</td>
      <td>${a.statut}</td>
      <td>${a.categorie}</td>
      <td>${a.score}</td>
      <td class="${a.neg ? 'amount-neg' : 'amount-pos'}">${a.somme}</td>
      <td>${a.date}<br><span style="font-size:11px;color:#9ca3af">${a.heure}</span></td>
    </tr>`).join('');
  }

  function renderTransfers() {
    const el = document.getElementById('transfers-list');
    const ts = selectedClient.transfers;
    if (!ts.length) { el.innerHTML = `<div class="empty-state"><i class="ti ti-transfer"></i>Aucun transfert</div>`; return; }
    el.innerHTML = ts.map(t => `
      <div class="transfer-item">
        <div>
          <div style="font-weight:500;font-size:13px">${t.label}</div>
          <div class="transfer-meta">${t.date}</div>
        </div>
        <div class="${t.dir === 'out' ? 'amount-neg' : 'amount-pos'}" style="font-weight:500">
          ${t.dir === 'out' ? '-' : '+'} ${t.montant}
        </div>
      </div>`).join('');
  }

  function renderBudgets() {
    const el = document.getElementById('budgets-content');
    const bs = selectedClient.budgets;
    if (!bs.length) { el.innerHTML = `<div class="empty-state"><i class="ti ti-chart-pie"></i>Aucun budget</div>`; return; }
    el.innerHTML = bs.map(b => `
      <div class="budget-bar-wrap">
        <div class="budget-label-row">
          <span>${b.label}</span>
          <span style="color:#6b7280">${b.pct}%</span>
        </div>
        <div class="budget-track">
          <div class="budget-fill" style="width:${b.pct}%;background:${b.pct > 80 ? '#EF4444' : b.pct > 60 ? '#F59E0B' : '#3B82F6'}"></div>
        </div>
      </div>`).join('');
  }

  function renderNotifs() {
    const el = document.getElementById('notifs-list');
    const ns = selectedClient.notifs;
    if (!ns.length) { el.innerHTML = `<div class="empty-state"><i class="ti ti-bell-off"></i>Aucune notification</div>`; return; }
    el.innerHTML = ns.map(n => `
      <div class="notif-item">
        <div class="notif-icon ${n.type}"><i class="ti ${n.icon}" style="font-size:18px"></i></div>
        <div>
          <div class="notif-title">${n.title}</div>
          <div class="notif-time">${n.time}</div>
        </div>
      </div>`).join('');
  }

  // Recherche
  document.getElementById('search-input').addEventListener('input', e => renderClientList(e.target.value));

  // Bouton suspendre / réactiver
  document.getElementById('btn-suspend').addEventListener('click', () => {
    selectedClient.status = selectedClient.status === 'suspendu' ? 'actif' : 'suspendu';
    renderDetail();
    renderClientList(document.getElementById('search-input').value);
  });

  // Onglets utilisateur
  document.querySelectorAll('.user-tab').forEach(t => {
    t.addEventListener('click', () => {
      document.querySelectorAll('.user-tab').forEach(x => x.classList.remove('active'));
      document.querySelectorAll('.view-section').forEach(x => x.classList.remove('active'));
      t.classList.add('active');
      document.getElementById('view-' + t.dataset.usertab).classList.add('active');
    });
  });

  // Onglets topbar
  const pageMap = { cotisations: 'page-cotisations', utilisateurs: 'page-utilisateurs', echange: 'page-echange', statistiques: 'page-statistiques' };
  document.querySelectorAll('.tab').forEach(t => {
    t.addEventListener('click', () => {
      document.querySelectorAll('.tab').forEach(x => x.classList.remove('active'));
      t.classList.add('active');
      Object.values(pageMap).forEach(p => {
        const el = document.getElementById(p);
        if (el) { el.style.display = 'none'; el.classList.remove('active'); }
      });
      const pg = document.getElementById(pageMap[t.dataset.tab]);
      if (pg) {
        pg.style.display = t.dataset.tab === 'utilisateurs' ? 'flex' : 'block';
        pg.classList.add('active');
      }
    });
  });

  // Nav latérale
  const navPageMap = { home: 'page-home', users: 'page-utilisateurs', folders: 'page-folders', apps: 'page-apps' };
  document.querySelectorAll('.nav-btn').forEach(b => {
    b.addEventListener('click', () => {
      document.querySelectorAll('.nav-btn').forEach(x => x.classList.remove('active'));
      b.classList.add('active');
      [...Object.values(navPageMap), ...Object.values(pageMap)].forEach(p => {
        const el = document.getElementById(p);
        if (el) { el.style.display = 'none'; el.classList.remove('active'); }
      });
      const pg = document.getElementById(navPageMap[b.dataset.nav]);
      if (pg) {
        pg.style.display = b.dataset.nav === 'users' ? 'flex' : 'block';
        pg.classList.add('active');
        if (b.dataset.nav === 'users') {
          document.querySelectorAll('.tab').forEach(x => x.classList.remove('active'));
          document.querySelector('.tab[data-tab="utilisateurs"]').classList.add('active');
        }
      }
    });
  });

  // Init
  renderClientList();
  renderDetail();
