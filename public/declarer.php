<?php
session_start();
require_once "../config/database.php";
require_once "../scoring/scoring.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: connexion.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$erreur  = '';
$succes  = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $type_sinistre = trim($_POST["type_sinistre"]);
    $date_sinistre = trim($_POST["date_sinistre"]);
    $montant       = floatval($_POST["montant"]);
    $description   = trim($_POST["description"]);

    if (empty($type_sinistre) || empty($date_sinistre) || empty($montant) || empty($description)) {
        $erreur = "Veuillez remplir tous les champs.";
    } else {

        $photo_path = null;

        if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === 0) {
            $ext_autorisees = ['jpg', 'jpeg', 'png'];
            $ext            = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));

            if (!in_array($ext, $ext_autorisees)) {
                $erreur = "Format non autorisé. (jpg, jpeg, png)";
            } elseif ($_FILES["photo"]["size"] > 5 * 1024 * 1024) {
                $erreur = "Photo trop lourde (max 5 Mo).";
            } else {
                $nom_fichier = uniqid("photo_") . "." . $ext;
                $destination = "../assets/uploads/" . $nom_fichier;
                move_uploaded_file($_FILES["photo"]["tmp_name"], $destination);
                $photo_path = $destination;
            }
        }

        if (empty($erreur)) {

            $ip_actuelle = $_SERVER["REMOTE_ADDR"] ?? '0.0.0.0';

            $stmtIp = $pdo->prepare("
                SELECT ip_declaration FROM sinistres
                WHERE utilisateur_id = :id
                ORDER BY date_sinistre DESC LIMIT 1
            ");
            $stmtIp->execute(["id" => $user_id]);
            $derniere_ip = $stmtIp->fetchColumn() ?? '';

            $stmtHisto = $pdo->prepare("
                SELECT COUNT(*) FROM sinistres
                WHERE utilisateur_id = :id
                AND date_sinistre >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            ");
            $stmtHisto->execute(["id" => $user_id]);
            $nb_sinistres_6mois = (int) $stmtHisto->fetchColumn();

            $data_scoring = [
                "montant"            => $montant,
                "photo_path"         => $photo_path,
                "date_sinistre"      => $date_sinistre,
                "description"        => $description,
                "nb_sinistres_6mois" => $nb_sinistres_6mois,
                "ip"                 => $ip_actuelle,
                "derniere_ip"        => $derniere_ip
            ];

            $resultat = calculerScore($data_scoring);

            $stmt = $pdo->prepare("
                INSERT INTO sinistres 
                    (utilisateur_id, type_sinistre, date_sinistre, montant,
                     description, photo_path, score_sinistre, niveau_fraude,
                     raisons, ip_declaration, statut)
                VALUES 
                    (:uid, :type, :date, :montant,
                     :description, :photo, :score, :niveau,
                     :raisons, :ip, 'en_attente')
            ");
            $stmt->execute([
                "uid"         => $user_id,
                "type"        => $type_sinistre,
                "date"        => $date_sinistre,
                "montant"     => $montant,
                "description" => $description,
                "photo"       => $photo_path,
                "score"       => $resultat["score"],
                "niveau"      => $resultat["niveau"],
                "raisons"     => json_encode($resultat["raisons"], JSON_UNESCAPED_UNICODE),
                "ip"          => $ip_actuelle
            ]);

            $sinistre_id = $pdo->lastInsertId();

            if ($resultat["score"] >= 60) {
                $alert = $pdo->prepare("
                    INSERT INTO alertes 
                        (sinistre_id, utilisateur_id, type_alerte, score_declencheur, message)
                    VALUES (:sid, :uid, 'Suspicion fraude', :score, 'Score sinistre élevé')
                ");
                $alert->execute([
                    "sid"   => $sinistre_id,
                    "uid"   => $user_id,
                    "score" => $resultat["score"]
                ]);
            }

            @file_get_contents("http://localhost/Lasespadas/scoring/scoring.php?utilisateur_id=" . $user_id);

            $succes = "Sinistre déclaré. Score : {$resultat['score']}/100 — {$resultat['niveau']}";
        }
    }
}
?>