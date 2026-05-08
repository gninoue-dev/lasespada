<?php
// ══════════════════════════════════════════════════════
//  scoring/scoring.php — Calcul du score global de fraude
//  Appelé après chaque nouveau sinistre enregistré
//  Usage : scoring.php?utilisateur_id=5
// ══════════════════════════════════════════════════════

session_start();
require_once "../config/database.php"; // Connexion PDO

// ── VÉRIFICATION DU PARAMÈTRE ─────────────────────────
// On s'assure qu'un utilisateur_id est bien passé en GET
if (isset($_GET["utilisateur_id"])) {

    // intval() protège contre les injections non numériques
    $utilisateur_id = intval($_GET["utilisateur_id"]);

    // ── RÉCUPÉRATION DES SINISTRES ────────────────────
    // On prend tous les scores individuels de l'utilisateur
    $stmt = $pdo->prepare("
        SELECT score_sinistre 
        FROM sinistres 
        WHERE utilisateur_id = :id
    ");
    $stmt->execute(["id" => $utilisateur_id]);
    $sinistres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($sinistres) {

        // ── CALCUL DU SCORE GLOBAL ────────────────────
        // On additionne tous les scores puis on fait la moyenne
        $score_total = 0;
        foreach ($sinistres as $s) {
            $score_total += intval($s["score_sinistre"]);
        }

        // Arrondi à l'entier le plus proche
        $score_global = round($score_total / count($sinistres));

        // ── DÉTERMINATION DU STATUT ───────────────────
        // 3 niveaux selon la moyenne des scores
        if ($score_global < 40) {
            $statut = "normal";       // Pas de risque détecté
        } elseif ($score_global < 70) {
            $statut = "douteux";      // À surveiller
        } else {
            $statut = "frauduleux";   // Alerte déclenchée
        }

        // ── MISE À JOUR DE L'UTILISATEUR EN BDD ──────
        // On stocke le score global et le statut calculé
        $update = $pdo->prepare("
            UPDATE utilisateurs 
            SET score_global = :score, 
                statut = :statut 
            WHERE id = :id
        ");
        $update->execute([
            "score"  => $score_global,
            "statut" => $statut,
            "id"     => $utilisateur_id
        ]);

        // ── ALERTE AUTOMATIQUE SI FRAUDULEUX ─────────
        // On insère une alerte en BDD uniquement si le statut est frauduleux
        // sinistre_id = NULL car c'est une alerte globale, pas liée à 1 sinistre
        if ($statut === "frauduleux") {
            $alert = $pdo->prepare("
                INSERT INTO alertes 
                    (sinistre_id, utilisateur_id, type_alerte, score_declencheur, message)
                VALUES 
                    (NULL, :uid, 'Suspicion fraude', :score, 'Score global trop élevé')
            ");
            $alert->execute([
                "uid"   => $utilisateur_id,
                "score" => $score_global
            ]);
        }

        // ── RÉPONSE ───────────────────────────────────
        echo "Score global calculé : $score_global | Statut : $statut";

    } else {
        echo "Aucun sinistre trouvé pour cet utilisateur.";
    }

} else {
    echo "Utilisateur non spécifié.";
}
?>