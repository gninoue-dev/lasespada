<?php
require_once "../includes/session.php";
require_once "../includes/functions.php";
require_once "../config/database.php";
requireAdmin();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["alerte_id"])) {
  $pdo->prepare("DELETE FROM alertes WHERE id = ?")->execute([(int) $_POST["alerte_id"]]);
  header("Location: gestion_alertes.php");
  exit;
}

$alertes = $pdo->query("
    SELECT a.*, u.nom, u.prenom, s.montant_estime, s.type_sinistre
    FROM alertes a
    JOIN utilisateurs u ON a.utilisateur_id = u.id
    LEFT JOIN sinistres s ON a.sinistre_id = s.id
    ORDER BY a.id DESC
")->fetchAll();

$total = count($alertes);
$critiques = count(array_filter($alertes, fn($a) => $a["score_declencheur"] >= 70));
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Gestion des alertes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../assets/css/gestion_alertes.css">
</head>

<body>
  <div class="container">
    <div class="header">
      <div class="title">Gestion des alertes</div>
      <a href="dashbord.php" style="color:#2563eb;text-decoration:underline">← Dashboard</a>
    </div>

    <div class="stats">
      <div class="card">
        <div>Total alertes</div>
        <div class="value">
          <?= $total ?>
        </div>
      </div>
      <div class="card">
        <div>Critiques (score ≥ 70)</div>
        <div class="value" style="color:#dc2626">
          <?= $critiques ?>
        </div>
      </div>
      <div class="card">
        <div>Non traitées</div>
        <div class="value" style="color:#f59e0b">
          <?= $total ?>
        </div>
      </div>
    </div>

    <div class="alert-list">
      <?php foreach ($alertes as $a): ?>
        <?php $classe = $a["score_declencheur"] >= 70 ? "critical" : "warning"; ?>
        <div class="alert <?= $classe ?>">
          <div class="alert-text">
            <?= $classe === 'critical' ? '🚨' : '⚠️' ?>
            <?= clean($a["type_alerte"]) ?> —
            <?= clean($a["nom"]) ?>
            <?= clean($a["prenom"]) ?>
            <?php if (!empty($a["montant_estime"])): ?>
              —
              <?= number_format($a["montant_estime"], 0, ',', ' ') ?> FCFA
            <?php endif; ?>
            <small style="color:#6b7280;margin-left:8px">Score :
              <?= $a["score_declencheur"] ?>
            </small>
          </div>
          <div style="display:flex;gap:8px">
            <?php if ($a["sinistre_id"]): ?>
              <a href="detail_sinistre.php?id=<?= $a["sinistre_id"] ?>" class="btn view">Voir</a>
            <?php endif; ?>
            <form method="POST" style="display:inline">
              <input type="hidden" name="alerte_id" value="<?= $a["id"] ?>">
              <button type="submit" class="btn done">Traiter</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($alertes)): ?>
        <div style="text-align:center;padding:40px;color:#9ca3af">Aucune alerte.</div>
      <?php endif; ?>
    </div>
  </div>
</body>

</html>