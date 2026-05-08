<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: connexion.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $pdo->prepare("SELECT nom, prenom, statut, score_global FROM utilisateurs WHERE id = :id");
$stmt->execute(["id" => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: connexion.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT COUNT(*) as total,
    SUM(CASE WHEN date_sinistre >= DATE_SUB(NOW(), INTERVAL 12 MONTH) THEN 1 ELSE 0 END) as derniers12mois
    FROM sinistres WHERE utilisateur_id = :id
");
$stmt->execute(["id" => $user_id]);
$sinistres = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT MAX(date_declaration) as last_update FROM sinistres WHERE utilisateur_id = :id");
$stmt->execute(["id" => $user_id]);
$last_update = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT type_sinistre, score_sinistre, statut 
    FROM sinistres WHERE utilisateur_id = :id 
    ORDER BY date_declaration DESC LIMIT 5
");
$stmt->execute(["id" => $user_id]);
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/dashbord.css">
    <script src="../assets/js/dashbord.js" defer></script>
</head>
<body>

<div class="app">
  <header class="topbar">
    <div class="brand">
      <div class="logo">
        <span class="logo-dot">
            <img src="../assets/images/user-sign-icon-front-side-removebg-preview.png" alt="">
        </span>
        <span class="logo-word"><?= htmlspecialchars($user["nom"]) ?> <?= htmlspecialchars($user["prenom"]) ?></span>
      </div>
      <div class="subtitle">Compte Securisée</div>
    </div>

    <div class="controls">
      <div class="control">
        <label>status du compte</label>
        <p class="status"><?= htmlspecialchars($user["statut"]) ?></p>
      </div>
      <div class="control">
        <label>historique des sinistres</label>
        <p class="population"><?= $sinistres["derniers12mois"] ?> sinistres sur les 12 derniers mois</p>
      </div>
      <a href="historiques.php"><button id="exportBtn" class="btn">voir sinistres</button></a>
    </div>
  </header>

  <main class="content">
    <section class="hero">
      <div class="hero-left">
        <h1>Vue d'ensemble</h1>
        <p class="hero-note">
          declares des sinistres en toute securité <strong>application de sevère en cas se fraude</strong>, la <strong>dynamique d'habitudes</strong> et
          l'<strong>impact perçu</strong> — <strong>prises en charges</strong>, cotisation a jour.
        </p>
      </div>
      <div class="hero-right">
        <div class="pill">
          <span class="pill-dot"></span>
          Adresse • Abidjan • Rivera
        </div>
        <div class="meta">
          <div><span class="muted">Numéro d'assurée</span> <strong id="orgName">N°<?= $user_id ?></strong></div>
          <div><span class="muted">Dernière mise à jour :</span> <strong id="lastUpdate"><?= $last_update["last_update"] ?? "aucune déclaration" ?></strong></div>
        </div>
      </div>
    </section>

    <section class="grid" id="kpiGrid"></section>

    <section class="details">
      <div class="panel">
        <div class="panel-head">
          <h2>Score de risque :</h2>
          <div class="muted"><?= $user["score_global"] ?> pts</div>
        </div>
        <ul class="insights" id="insights">
          <?php foreach ($details as $d): ?>
            <li><?= htmlspecialchars($d["type_sinistre"]) ?> — Score <?= $d["score_sinistre"] ?> (<?= $d["statut"] ?>)</li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="panel">
        <div class="panel-head">
          <h2>Statut du Compte</h2>
          <div class="muted"><?= htmlspecialchars($user["statut"]) ?></div>
        </div>
        <div class="defs" id="defs"></div>
      </div>
    </section>

    <footer class="footer">
      <div class="muted">
        <a href="declarer.php"><button class="btn" style="font-size: 1rem;">Déclarer un sinistre +</button></a>
      </div>
    </footer>
  </main>
</div>
</body>
</html>