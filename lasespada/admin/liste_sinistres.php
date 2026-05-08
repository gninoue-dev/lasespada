<?php
require_once "../includes/session.php";
require_once "../includes/functions.php";
require_once "../config/database.php";
requireAdmin();

// Filtre statut
$filtre = $_GET["filtre"] ?? "all";

$filtresSQL = [
    "pending"  => "WHERE s.statut = 'en_attente'",
    "valid"    => "WHERE s.statut = 'validé'",
    "refused"  => "WHERE s.statut = 'rejeté'",
    "fraud"    => "WHERE s.niveau_fraude IN ('frauduleux','fraude probable')",
];

$where = $filtresSQL[$filtre] ?? "";

$sinistres = $pdo->query("
    SELECT s.*, u.nom, u.prenom
    FROM sinistres s
    JOIN utilisateurs u ON s.utilisateur_id = u.id
    $where
    ORDER BY s.id DESC
")->fetchAll();

// Stats
$total     = $pdo->query("SELECT COUNT(*) FROM sinistres")->fetchColumn();
$attente   = $pdo->query("SELECT COUNT(*) FROM sinistres WHERE statut = 'en_attente'")->fetchColumn();
$valides   = $pdo->query("SELECT COUNT(*) FROM sinistres WHERE statut = 'validé'")->fetchColumn();
$refuses   = $pdo->query("SELECT COUNT(*) FROM sinistres WHERE statut = 'rejeté'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des sinistres</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="../assets/css/liste_sinistres.css">
</head>
<body>
<div class="container">
  <div class="header">
    <h1>Liste des sinistres</h1>
    <a href="dashbord.php" style="color:#2563eb;text-decoration:underline">← Dashboard</a>
  </div>

  <!-- STATS -->
  <div class="stats">
    <div class="card"><div>Total</div><div class="value"><?= $total ?></div></div>
    <div class="card"><div>En attente</div><div class="value" style="color:#d97706"><?= $attente ?></div></div>
    <div class="card"><div>Validés</div><div class="value" style="color:#16a34a"><?= $valides ?></div></div>
    <div class="card"><div>Refusés</div><div class="value" style="color:#dc2626"><?= $refuses ?></div></div>
  </div>

  <!-- FILTRES -->
  <div class="filters">
    <a href="?filtre=all"     class="filter <?= $filtre === 'all'     ? 'active' : '' ?>">Tous</a>
    <a href="?filtre=pending" class="filter <?= $filtre === 'pending' ? 'active' : '' ?>">En attente</a>
    <a href="?filtre=valid"   class="filter <?= $filtre === 'valid'   ? 'active' : '' ?>">Validés</a>
    <a href="?filtre=refused" class="filter <?= $filtre === 'refused' ? 'active' : '' ?>">Refusés</a>
    <a href="?filtre=fraud"   class="filter <?= $filtre === 'fraud'   ? 'active' : '' ?>" style="color:#dc2626">Fraudes</a>
  </div>

  <!-- TABLE -->
  <div class="table-box">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Client</th>
          <th>Type</th>
          <th>Montant</th>
          <th>Score</th>
          <th>Niveau</th>
          <th>Statut</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($sinistres as $s): ?>
        <tr>
          <td>#<?= $s["id"] ?></td>
          <td><?= clean($s["nom"]) ?> <?= clean($s["prenom"]) ?></td>
          <td><?= clean($s["type_sinistre"]) ?></td>
          <td><?= number_format($s["montant"], 0, ',', ' ') ?> FCFA</td>
          <td><?= $s["score_sinistre"] ?>/100</td>
          <td><?= badgeNiveau($s["niveau_fraude"] ?? 'normal') ?></td>
          <td><?= badgeStatut($s["statut"]) ?></td>
          <td><?= clean($s["date_sinistre"]) ?></td>
          <td>
            <a href="detail_sinistre.php?id=<?= $s["id"] ?>" class="view-btn">Voir</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($sinistres)): ?>
        <tr><td colspan="9" style="text-align:center;padding:20px;color:#9ca3af">Aucun sinistre trouvé.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>