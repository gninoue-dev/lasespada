<?php
require_once "../includes/session.php";
require_once "../includes/functions.php";
require_once "../config/database.php";
requireAdmin();

$id = intval($_GET["id"] ?? 0);

if (!$id) {
    header("Location: liste_sinistres.php");
    exit;
}

// Action admin : valider / rejeter / mettre en attente
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["statut"])) {
    $statutsOk = ['validé', 'rejeté', 'en_attente'];
    if (in_array($_POST["statut"], $statutsOk)) {
        $stmt = $pdo->prepare("UPDATE sinistres SET statut = ? WHERE id = ?");
        $stmt->execute([$_POST["statut"], $id]);
    }
    header("Location: detail_sinistre.php?id=$id");
    exit;
}

// Récupération sinistre + utilisateur
$stmt = $pdo->prepare("
    SELECT s.*, u.nom, u.prenom, u.email, u.score_global, u.statut as statut_compte
    FROM sinistres s
    JOIN utilisateurs u ON s.utilisateur_id = u.id
    WHERE s.id = :id
");
$stmt->execute(["id" => $id]);
$s = $stmt->fetch();

if (!$s) {
    die("Sinistre introuvable.");
}

$raisons = json_decode($s["raisons"] ?? '[]', true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail sinistre #<?= $id ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="../assets/css/detail_sinistres.css">
</head>
<body>
<div class="container">
  <div class="header">
    <div style="display:flex;gap:10px;align-items:center">
      <button class="back" onclick="history.back()">← Retour</button>
      <h2 style="margin:0">Détail sinistre #<?= $id ?></h2>
    </div>
  </div>

  <!-- INFOS SINISTRE -->
  <div class="card">
    <h3>Informations du sinistre</h3>
    <div class="grid">
      <div><div class="label">Client</div><div class="value"><?= clean($s["nom"]) ?> <?= clean($s["prenom"]) ?></div></div>
      <div><div class="label">Email</div><div class="value"><?= clean($s["email"]) ?></div></div>
      <div><div class="label">Type</div><div class="value"><?= clean($s["type_sinistre"]) ?></div></div>
      <div><div class="label">Montant demandé</div><div class="value"><?= number_format($s["montant"], 0, ',', ' ') ?> FCFA</div></div>
      <div><div class="label">Date sinistre</div><div class="value"><?= clean($s["date_sinistre"]) ?></div></div>
      <div><div class="label">Statut</div><div class="value"><?= badgeStatut($s["statut"]) ?></div></div>
      <div><div class="label">Score fraude</div><div class="value"><strong><?= $s["score_sinistre"] ?>/100</strong></div></div>
      <div><div class="label">Niveau</div><div class="value"><?= badgeNiveau($s["niveau_fraude"] ?? 'normal') ?></div></div>
      <div><div class="label">Score global client</div><div class="value"><?= $s["score_global"] ?></div></div>
    </div>
  </div>

  <!-- DESCRIPTION -->
  <div class="card">
    <h3>Description</h3>
    <p style="color:#4b5563"><?= clean($s["description"]) ?></p>
  </div>

  <!-- RAISONS DU SCORE -->
  <?php if (!empty($raisons)): ?>
  <div class="card">
    <h3>Raisons du score de fraude</h3>
    <ul style="margin-top:10px;color:#dc2626">
      <?php foreach ($raisons as $r): ?>
        <li style="margin-bottom:6px">⚠ <?= clean($r) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <!-- PHOTO -->
  <?php if (!empty($s["photo_path"])): ?>
  <div class="card">
    <h3>Photo uploadée</h3>
    <img src="<?= clean($s["photo_path"]) ?>" style="max-width:400px;border-radius:8px;margin-top:10px" alt="Photo sinistre">
  </div>
  <?php endif; ?>

  <!-- ACTIONS ADMIN -->
  <div class="card">
    <h3>Actions administrateur</h3>
    <div style="display:flex;gap:10px;margin-top:15px">
      <form method="POST">
        <input type="hidden" name="statut" value="validé">
        <button type="submit" class="btn btn-validate">✔ Valider</button>
      </form>
      <form method="POST">
        <input type="hidden" name="statut" value="rejeté">
        <button type="submit" class="btn btn-refuse">✖ Refuser</button>
      </form>
      <form method="POST">
        <input type="hidden" name="statut" value="en_attente">
        <button type="submit" class="btn btn-warn">⚠ Mettre en attente</button>
      </form>
    </div>
  </div>

</div>
</body>
</html>