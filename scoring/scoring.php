<?php
session_start();
require_once "../config/database.php"; // connexion PDO

if (isset($_GET["utilisateur_id"])) {
    $utilisateur_id = intval($_GET["utilisateur_id"]);

    // Récupérer tous les sinistres de l'utilisateur
    $stmt = $pdo->prepare("SELECT score_sinistre FROM sinistres WHERE utilisateur_id = :id");
    $stmt->execute(["id" => $utilisateur_id]);
    $sinistres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($sinistres) {
        $score_total = 0;
        foreach ($sinistres as $s) {
            $score_total += intval($s["score_sinistre"]);
        }

        // Calcul du score global (moyenne des sinistres)
        $score_global = round($score_total / count($sinistres));

        // Déterminer le statut
        if ($score_global < 40) {
            $statut = "normal";
        } elseif ($score_global < 70) {
            $statut = "douteux";
        } else {
            $statut = "frauduleux";
        }

        // Mettre à jour la table utilisateurs
        $update = $pdo->prepare("UPDATE utilisateurs SET score_global = :score, statut = :statut WHERE id = :id");
        $update->execute([
            "score" => $score_global,
            "statut" => $statut,
            "id" => $utilisateur_id
        ]);

        // Enregistrer une alerte si frauduleux
        if ($statut === "frauduleux") {
            $alert = $pdo->prepare("INSERT INTO alertes (sinistre_id, utilisateur_id, type_alerte, score_declencheur, message) 
                                    VALUES (NULL, :uid, 'Suspicion fraude', :score, 'Score global trop élevé')");
            $alert->execute([
                "uid" => $utilisateur_id,
                "score" => $score_global
            ]);
        }

        echo "Score global calculé : $score_global | Statut : $statut";
    } else {
        echo "Aucun sinistre trouvé pour cet utilisateur.";
    }
} else {
    echo "Utilisateur non spécifié.";
}
?>
