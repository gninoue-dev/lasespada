<?php
require_once "../includes/session.php";
require_once "../includes/functions.php";
require_once "../config/database.php";
requireAdmin();

// Stats globales
$totalUsers     = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$totalSinistres = $pdo->query("SELECT COUNT(*) FROM sinistres")->fetchColumn();
$enAttente      = $pdo->query("SELECT COUNT(*) FROM sinistres WHERE statut = 'en_attente'")->fetchColumn();
$frauduleux     = $pdo->query("SELECT COUNT(*) FROM sinistres WHERE niveau_fraude IN ('frauduleux','fraude probable')")->fetchColumn();

// 5 derniers sinistres
$derniers = $pdo->query("
    SELECT s.*, u.nom, u.prenom
    FROM sinistres s
    JOIN utilisateurs u ON s.utilisateur_id = u.id
    ORDER BY s.id DESC LIMIT 5
")->fetchAll();

// Alertes non traitées
$alertes = $pdo->query("SELECT COUNT(*) FROM alertes")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Assurance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css"/>
    <link rel="stylesheet" href="../assets/css/dashbord2.css"/>
    <script src="../assets/js/dashbord2.js" defer></script>
</head>
<body>
<div class="shell">
  <div class="sidebar-nav">
    <div class="nav-logo"></div>
    <div class="nav-items">
      <button class="nav-btn active" data-nav="home" title="Accueil"><i class="ti ti-home"></i></button>
      <button class="nav-btn" data-nav="users" title="Utilisateurs"><i class="ti ti-users"></i></button>
      <button class="nav-btn" data-nav="folders" title="Sinistres"><i class="ti ti-folder-plus"></i></button>
    </div>
  </div>

  <div class="main">
    <div class="topbar">
      <button class="tab active">DASHBOARD</button>
      <div class="topbar-right">
        <span style="color:#6b7280;font-size:14px">
            Bonjour, <?= clean($_SESSION["user_nom"] ?? "Admin") ?>
        </span>
        <a href="../public/logout.php" class="btn-suspend">Déconnexion</a>
      </div>
    </div>

    <div class="content-area">

      <!-- STATS -->
      <div class="stat-grid" style="padding:20px">
        <div class="stat-card">
          <div class="stat-label">Utilisateurs</div>
          <div class="stat-value"><?= $totalUsers ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total sinistres</div>
          <div class="stat-value"><?= $totalSinistres ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">En attente</div>
          <div class="stat-value" style="color:#d97706"><?= $enAttente ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Fraudes détectées</div>
          <div class="stat-value" style="color:#dc2626"><?= $frauduleux ?></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Alertes</div>
          <div class="stat-value" style="color:#f59e0b"><?= $alertes ?></div>
        </div>
      </div>

      <!-- LIENS RAPIDES -->
      <div style="padding:0 20px;display:flex;gap:10px;margin-bottom:20px">
        <a href="liste_sinistres.php" class="btn-suspend" style="background:#2563eb;color:#fff;padding:8px 16px;border-radius:6px;text-decoration:none">
            Voir sinistres
        </a>
        <a href="gestion_alertes.php" class="btn-suspend" style="background:#dc2626;color:#fff;padding:8px 16px;border-radius:6px;text-decoration:none">
            Voir alertes
        </a>
      </div>

      <!-- DERNIERS SINISTRES -->
      <div style="padding:0 20px">
        <h2 style="font-weight:600;margin-bottom:10px">5 derniers sinistres</h2>
        <table style="width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden">
          <thead style="background:#f3f4f6">
            <tr>
              <th style="padding:10px;text-align:left">Client</th>
              <th style="padding:10px;text-align:left">Type</th>
              <th style="padding:10px;text-align:left">Montant</th>
              <th style="padding:10px;text-align:left">Score</th>
              <th style="padding:10px;text-align:left">Niveau</th>
              <th style="padding:10px;text-align:left">Statut</th>
              <th style="padding:10px;text-align:left">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($derniers as $s): ?>
            <tr style="border-top:1px solid #e5e7eb">
              <td style="padding:10px"><?= clean($s["nom"]) ?> <?= clean($s["prenom"]) ?></td>
              <td style="padding:10px"><?= clean($s["type_sinistre"]) ?></td>
              <td style="padding:10px"><?= number_format($s["montant"], 0, ',', ' ') ?> FCFA</td>
              <td style="padding:10px"><?= $s["score_sinistre"] ?>/100</td>
              <td style="padding:10px"><?= badgeNiveau($s["niveau_fraude"] ?? '') ?></td>
              <td style="padding:10px"><?= badgeStatut($s["statut"]) ?></td>
              <td style="padding:10px">
                <a href="detail_sinistre.php?id=<?= $s["id"] ?>" style="color:#2563eb;text-decoration:underline">Voir</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>
</body>
</html>