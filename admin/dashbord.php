<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Assurance</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" />
  <link rel="stylesheet" href="../assets/css/dashbord2.css"/>
  <script src="../assets/js/dashbord2.js" defer></script>

</head>
<body>

<div class="shell">
  <!-- Sidebar -->
  <div class="sidebar-nav">
    <div class="nav-logo">
    
    </div>
    <div class="nav-items">
      <button class="nav-btn" data-nav="home" title="Accueil"><i class="ti ti-home"></i></button>
      <button class="nav-btn active" data-nav="users" title="Utilisateurs"><i class="ti ti-users"></i></button>
      <button class="nav-btn" data-nav="folders" title="Dossiers"><i class="ti ti-folder-plus"></i></button>
      <button class="nav-btn" data-nav="apps" title="Applications"><i class="ti ti-layout-grid"></i></button>
    </div>
  </div>

  <!-- Main -->
  <div class="main">
    <!-- Topbar -->
    <div class="topbar">
      <button class="tab active" data-tab="cotisations">COTISATIONS</button>
      <button class="tab" data-tab="utilisateurs">UTILISATEURS</button>
      <button class="tab" data-tab="echange">ÉCHANGE</button>
      <button class="tab" data-tab="statistiques">STATISTIQUES</button>
      <div class="topbar-right">
        <button class="btn-suspend" id="btn-suspend">Suspendre</button>
        <button class="admin-btn">
          <span style="position:relative">
            <span class="status-dot" style="position:absolute;right:-1px;bottom:-1px"></span>
            <i class="ti ti-user-circle" style="font-size:22px"></i>
          </span>
          <span>Nom admin</span>
          <i class="ti ti-chevron-down" style="font-size:14px"></i>
        </button>
      </div>
    </div>

    <!-- Content area -->
    <div class="content-area" id="main-content">

      <!-- PAGE : UTILISATEURS -->
      <div id="page-utilisateurs" class="page-section active" style="padding:0;display:flex;flex:1;overflow:hidden">
        <div class="left-panel">
          <div class="left-panel-header">
            <div class="left-panel-label">UTILISATEURS</div>
            <div class="search-wrap">
              <i class="ti ti-search"></i>
              <input type="text" placeholder="Rechercher..." id="search-input" />
            </div>
          </div>
          <div class="client-list" id="client-list"></div>
        </div>

        <div class="main-view">
          <div class="user-header">
            <div class="user-header-top">
              <div class="user-title">
                <div class="avatar-lg" id="detail-avatar">C2</div>
                <span id="detail-name">client2</span>
              </div>
              <div class="balance-section">
                <div class="balance-label">Balance</div>
                <div class="balance-amount" id="detail-balance">7 235 674 F</div>
              </div>
            </div>
            <div class="user-tabs">
              <button class="user-tab active" data-usertab="activities">Activités</button>
              <button class="user-tab" data-usertab="transfers">Sommes transférées</button>
              <button class="user-tab" data-usertab="budgets">Budgets restants</button>
              <button class="user-tab" data-usertab="notifications">Notifications</button>
            </div>
          </div>

          <div class="view-content">
            <div id="view-activities" class="view-section active">
              <div class="filter-row">
                <button class="filter-btn">
                  <i class="ti ti-calendar"></i> 30 derniers jours
                  <i class="ti ti-chevron-down"></i>
                </button>
              </div>
              <table>
                <thead>
                  <tr>
                    <th>Sinistres</th>
                    <th>Statut</th>
                    <th>Catégories</th>
                    <th>Score</th>
                    <th>Somme</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody id="activities-body"></tbody>
              </table>
            </div>

            <div id="view-transfers" class="view-section">
              <div id="transfers-list"></div>
            </div>

            <div id="view-budgets" class="view-section">
              <div id="budgets-content"></div>
            </div>

            <div id="view-notifications" class="view-section">
              <div id="notifs-list"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- PAGE : COTISATIONS -->
      <div id="page-cotisations" class="page-section" style="display:none">
        <div class="section-title">Cotisations</div>
        <div class="stat-grid">
          <div class="stat-card"><div class="stat-label">Total collecté</div><div class="stat-value">42 800 000 F</div></div>
          <div class="stat-card"><div class="stat-label">Cotisations actives</div><div class="stat-value">128</div></div>
          <div class="stat-card"><div class="stat-label">En retard</div><div class="stat-value" style="color:#EF4444">12</div></div>
        </div>
        <table>
          <thead><tr><th>Client</th><th>Montant</th><th>Fréquence</th><th>Prochaine échéance</th><th>Statut</th></tr></thead>
          <tbody>
            <tr><td>client2</td><td>45 000 F</td><td>Mensuelle</td><td>01.06.2026</td><td><span class="badge-actif">À jour</span></td></tr>
            <tr><td>client5</td><td>30 000 F</td><td>Trimestrielle</td><td>15.07.2026</td><td><span class="badge-actif">À jour</span></td></tr>
            <tr><td>client1</td><td>60 000 F</td><td>Mensuelle</td><td>01.05.2026</td><td><span class="badge-suspendu">En retard</span></td></tr>
          </tbody>
        </table>
      </div>

      <!-- PAGE : ÉCHANGE -->
      <div id="page-echange" class="page-section" style="display:none">
        <div class="section-title">Échanges</div>
        <div class="stat-grid">
          <div class="stat-card"><div class="stat-label">Volume échangé</div><div class="stat-value">8 200 000 F</div></div>
          <div class="stat-card"><div class="stat-label">Transactions</div><div class="stat-value">34</div></div>
          <div class="stat-card"><div class="stat-label">Ce mois</div><div class="stat-value amount-pos">+12%</div></div>
        </div>
        <table>
          <thead><tr><th>De</th><th>Vers</th><th>Montant</th><th>Date</th></tr></thead>
          <tbody>
            <tr><td>client2</td><td>client3</td><td>150 000 F</td><td>05.05.2026</td></tr>
            <tr><td>client4</td><td>client2</td><td>80 000 F</td><td>02.05.2026</td></tr>
            <tr><td>client1</td><td>client5</td><td>200 000 F</td><td>28.04.2026</td></tr>
          </tbody>
        </table>
      </div>

      <!-- PAGE : STATISTIQUES -->
      <div id="page-statistiques" class="page-section" style="display:none">
        <div class="section-title">Statistiques</div>
        <div class="stat-grid">
          <div class="stat-card"><div class="stat-label">Utilisateurs actifs</div><div class="stat-value">5</div></div>
          <div class="stat-card"><div class="stat-label">Sinistres ce mois</div><div class="stat-value">8</div></div>
          <div class="stat-card"><div class="stat-label">Montant sinistres</div><div class="stat-value amount-neg">1 850 000 F</div></div>
        </div>
        <div style="background:#f9fafb;border-radius:8px;padding:20px;text-align:center;color:#6b7280">
          <i class="ti ti-chart-bar" style="font-size:48px;margin-bottom:12px;display:block"></i>
          Graphiques disponibles dans la version complète
        </div>
      </div>

      <!-- PAGE : ACCUEIL -->
      <div id="page-home" class="page-section" style="display:none">
        <div class="section-title">Tableau de bord</div>
        <div class="stat-grid">
          <div class="stat-card"><div class="stat-label">Clients total</div><div class="stat-value">5</div></div>
          <div class="stat-card"><div class="stat-label">Actifs</div><div class="stat-value">4</div></div>
          <div class="stat-card"><div class="stat-label">Suspendus</div><div class="stat-value" style="color:#EF4444">1</div></div>
        </div>
      </div>

      <!-- PAGE : DOSSIERS -->
      <div id="page-folders" class="page-section" style="display:none">
        <div class="section-title">Dossiers</div>
        <div class="empty-state"><i class="ti ti-folder"></i>Aucun dossier créé</div>
      </div>

      <!-- PAGE : APPLICATIONS -->
      <div id="page-apps" class="page-section" style="display:none">
        <div class="section-title">Applications</div>
        <div class="empty-state"><i class="ti ti-layout-grid"></i>Module en développement</div>
      </div>

    </div>
  </div>
</div>
</body>
</html>
