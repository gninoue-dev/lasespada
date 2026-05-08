<?php
// ══════════════════════════════════════════════════════
//  public/historiques.php — Historique des sinistres
//  Accessible uniquement aux utilisateurs connectés
// ══════════════════════════════════════════════════════

session_start();
require_once "../config/database.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    header("Location: connexion.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// 🔹 Récupérer les sinistres de l'utilisateur
$stmt = $pdo->prepare("SELECT date_sinistre, type_sinistre, score_sinistre, statut 
                       FROM sinistres 
                       WHERE utilisateur_id = :id 
                       ORDER BY date_sinistre DESC");
$stmt->execute(["id" => $user_id]);
$sinistres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/historique.css">
    <script src="../assets/js/historique.js" defer></script>
</head>
<body>
    

<body class="large-screen">
  <div class="wrap">
    <div class="btn-toolbar buttons">
      <div class="btn-group">
    
      </div>
      <div class="btn-group">
    
      </div>
    </div>
    <div class="table-wrapper">
      <table class="table-responsive card-list-table">
        <thead>
          <tr>
            <th>DATES</th>
            <th>TYPES</th>
            <th>SCORES</th>
            <th>niveaux de risques</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sinistres as $s): ?>
            <tr>
              <td data-title="DATES"><?= htmlspecialchars($s["date_sinistre"]) ?></td>
              <td data-title="TYPES"><?= htmlspecialchars($s["type_sinistre"]) ?></td>
              <td data-title="SCORES"><?= $s["score_sinistre"] ?></td>
              <td data-title="niveaux de risques">
                <?php
                  if ($s["score_sinistre"] >= 80) echo "Élevé";
                  elseif ($s["score_sinistre"] >= 40) echo "Moyen";
                  else echo "Faible";
                ?>
              </td>
              <td data-title="Status"><?= htmlspecialchars($s["statut"]) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</body>
</html>