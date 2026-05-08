<?php
// ══════════════════════════════════════════════════════
//  public/historiques.php — Historique des sinistres
//  Accessible uniquement aux utilisateurs connectés
// ══════════════════════════════════════════════════════

session_start();
require_once "../config/database.php";

// ── PROTECTION PAGE ───────────────────────────────────
// Si l'utilisateur n'est pas connecté → retour à la connexion
if (!isset($_SESSION["user_id"])) {
    header("Location: connexion.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// ── RÉCUPÉRATION DES SINISTRES ────────────────────────
// On récupère uniquement les sinistres de l'utilisateur connecté
// Triés du plus récent au plus ancien
$stmt = $pdo->prepare("
    SELECT date_sinistre, type_sinistre, score_sinistre, statut
    FROM sinistres
    WHERE utilisateur_id = :id
    ORDER BY date_sinistre DESC
");
$stmt->execute(["id" => $user_id]);
$sinistres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des sinistres</title>
    <link rel="stylesheet" href="../assets/css/historique.css">
    <script src="../assets/js/historique.js"></script>
</head>
<body class="large-screen">

<div class="wrap">
    <div class="table-wrapper">
        <table class="table-responsive card-list-table">
            <thead>
                <tr>
                    <th>DATES</th>
                    <th>TYPES</th>
                    <th>SCORES</th>
                    <th>NIVEAU DE RISQUE</th>
                    <th>STATUT</th>
                </tr>
            </thead>
            <tbody>

                <?php if (empty($sinistres)): ?>
                <!-- Aucun sinistre trouvé pour cet utilisateur -->
                <tr>
                    <td colspan="5" style="text-align:center">Aucun sinistre enregistré.</td>
                </tr>

                <?php else: ?>
                <?php foreach ($sinistres as $s): ?>
                <tr>
                    <!-- Date du sinistre -->
                    <td data-title="DATES"><?= htmlspecialchars($s["date_sinistre"]) ?></td>

                    <!-- Type de sinistre (vol, accident, incendie...) -->
                    <td data-title="TYPES"><?= htmlspecialchars($s["type_sinistre"]) ?></td>

                    <!-- Score individuel du sinistre -->
                    <td data-title="SCORES"><?= $s["score_sinistre"] ?></td>

                    <!-- Niveau de risque calculé depuis le score -->
                    <!-- >= 80 : Élevé | >= 40 : Moyen | < 40 : Faible -->
                    <td data-title="NIVEAU DE RISQUE">
                        <?php
                        if ($s["score_sinistre"] >= 80)      echo "Élevé";
                        elseif ($s["score_sinistre"] >= 40)  echo "Moyen";
                        else                                  echo "Faible";
                        ?>
                    </td>

                    <!-- Statut du dossier (en_attente, validé, rejeté) -->
                    <td data-title="STATUT"><?= htmlspecialchars($s["statut"]) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

</body>
</html>