<?php
session_start();
require_once "../config/database.php";

// ══════════════════════════════════════════════════════
//  FONCTION 1 — calculerScore()
//  Appelée par declarer.php pour scorer un sinistre
// ══════════════════════════════════════════════════════
function calculerScore(array $data): array {

    $score   = 0;
    $raisons = [];

    // Montant élevé
    if ($data["montant"] > 5000) {
        $score += 20;
        $raisons[] = "Montant élevé ({$data['montant']} FCFA)";
    }

    // Analyse EXIF photo
    $exif = @exif_read_data($data["photo_path"]);
    if (!$exif) {
        $score += 20;
        $raisons[] = "Photo sans métadonnées EXIF";
    } elseif (isset($exif["DateTimeOriginal"])) {
        $datePhoto    = strtotime($exif["DateTimeOriginal"]);
        $dateSinistre = strtotime($data["date_sinistre"]);
        if (abs($datePhoto - $dateSinistre) > 86400) {
            $score += 15;
            $raisons[] = "Date photo EXIF différente de la date sinistre";
        }
    }

    // Mots-clés suspects
    $motsCles = ['urgent', 'vite', 'immédiat', 'tout perdu', 'rapidement'];
    foreach ($motsCles as $mot) {
        if (stripos($data["description"], $mot) !== false) {
            $score += 15;
            $raisons[] = "Mot-clé suspect : \"$mot\"";
            break;
        }
    }

    // Heure suspecte
    $heure = (int) date("H", strtotime($data["date_sinistre"]));
    if ($heure >= 22 || $heure <= 6) {
        $score += 10;
        $raisons[] = "Sinistre déclaré la nuit ({$heure}h)";
    }

    // Sinistres répétés
    if ($data["nb_sinistres_6mois"] >= 2) {
        $score += 10;
        $raisons[] = "Sinistres répétés ({$data['nb_sinistres_6mois']} en 6 mois)";
    }

    // IP identique
    if (!empty($data["derniere_ip"]) && $data["ip"] === $data["derniere_ip"]) {
        $score += 10;
        $raisons[] = "IP identique au sinistre précédent";
    }

    // Niveau
    if ($score <= 30) {
        $niveau = "normal";
    } elseif ($score <= 60) {
        $niveau = "douteux";
    } else {
        $niveau = "frauduleux";
    }

    return [
        "score"   => $score,
        "niveau"  => $niveau,
        "raisons" => $raisons
    ];
}

// ══════════════════════════════════════════════════════
//  FONCTION 2 — Score global utilisateur
//  Appelée via URL : scoring.php?utilisateur_id=X
// ══════════════════════════════════════════════════════
if (isset($_GET["utilisateur_id"])) {

    $utilisateur_id = intval($_GET["utilisateur_id"]);

    $stmt = $pdo->prepare("SELECT score_sinistre FROM sinistres WHERE utilisateur_id = :id");
    $stmt->execute(["id" => $utilisateur_id]);
    $sinistres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($sinistres) {

        $score_total = 0;
        foreach ($sinistres as $s) {
            $score_total += intval($s["score_sinistre"]);
        }

        $score_global = round($score_total / count($sinistres));

        if ($score_global < 40) {
            $statut = "normal";
        } elseif ($score_global < 70) {
            $statut = "douteux";
        } else {
            $statut = "frauduleux";
        }

        $update = $pdo->prepare("UPDATE utilisateurs SET score_global = :score, statut = :statut WHERE id = :id");
        $update->execute([
            "score"  => $score_global,
            "statut" => $statut,
            "id"     => $utilisateur_id
        ]);

        if ($statut === "frauduleux") {
            $alert = $pdo->prepare("
                INSERT INTO alertes (sinistre_id, utilisateur_id, type_alerte, score_declencheur, message)
                VALUES (NULL, :uid, 'Suspicion fraude', :score, 'Score global trop élevé')
            ");
            $alert->execute([
                "uid"   => $utilisateur_id,
                "score" => $score_global
            ]);
        }

        echo "Score global : $score_global | Statut : $statut";

    } else {
        echo "Aucun sinistre trouvé.";
    }
}