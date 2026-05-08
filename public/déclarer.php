<<<<<<< HEAD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/declarer.css">
</head>
<body>

<form id="rendered-form">
  <div class="rendered-form">
    <div class="formbuilder-number form-group field-txt_age">
      <label for="txt_age" class="formbuilder-number-label">Que vous est-il arrivé ? <span class="formbuilder-required">*</span></label><br>
      <input type="text" placeholder="un accident de voiture" class="form-control" name="txt_age" min="0" max="130" step="1" id="txt_age" required="required" aria-required="true">
    </div>
    <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label"> veuillez indiquez l'endroit de l'accident ! <span class="formbuilder-required">*</span></label><br>
      <input type="text" placeholder="abidjan,abobo,sanmanké" class="form-control" name="txt_postalcode" id="txt_postalcode" required="required" aria-required="true">
    </div>

      <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label"> veuillez indiquez la date de l'accident ! <span class="formbuilder-required">*</span></label><br>
      <input type="date" placeholder="abidjan,abobo,sanmanké" class="form-control" name="txt_postalcode" id="txt_postalcode" required="required" aria-required="true">
    </div>

      <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label"> Veuillez indiquer la date de déclaration ! <span class="formbuilder-required">*</span></label><br>
      <input type="date" placeholder="abidjan,abobo,sanmanké" class="form-control" name="txt_postalcode" id="txt_postalcode" required="required" aria-required="true">
    </div>


          <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label">Combien de temps s'est écoulé depuis l'accident ? <span class="formbuilder-required">*</span></label><br>
      <input type="time" placeholder="abidjan,abobo,sanmanké" class="form-control" name="txt_postalcode" id="txt_postalcode" required="required" aria-required="true">
    </div>


    <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label"> De combien avez vous besoin ? <span class="formbuilder-required">*</span></label><br>
      <input type="number" placeholder="500000" class="form-control" name="txt_postalcode" id="txt_postalcode" >
    </div>

    <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label">Expliquez les faits !<span class="formbuilder-required">*</span></label><br>
      <textarea type="number" placeholder="Explique vous ici....." class="form-control" name="txt_postalcode" id="txt_postalcode" style="padding-bottom: 90px;"></textarea>
    </div>

        <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label">Envoyez des preuves<span class="formbuilder-required">*</span></label><br>
      <input type="file" placeholder="Explique vous ici....." class="form-control" name="txt_postalcode" id="txt_postalcode" required="required" aria-required="true">
    </div>


        <div class="formbuilder-number form-group field-txt_postalcode">
      <label for="txt_postalcode" class="formbuilder-number-label">Soumettez le Rapport de police si possible ??<span class="formbuilder-required">*</span></label><br>
      <input type="file" class="form-control" name="txt_postalcode" id="txt_postalcode" >
    </div>


    <div class="formbuilder-checkbox-group form-group field-chk_legalage">
      <label for="chk_legalage" class="formbuilder-checkbox-group-label"> y'avais t'il des Témoins ?</label><br>
      <div class="checkbox-group">
        <div class="formbuilder-checkbox">
          <input name="chk_legalage[]" id="chk_legalage-0" class="form-checkbox" aria-required="true" type="checkbox" required="required" placeholder="">
          Oui
          <input name="chk_legalage[]" id="chk_legalage-0" class="form-checkbox" aria-required="true" type="checkbox" required="required" placeholder="">
          NOn
        </div>
      </div>
    </div>
    <div class="formbuilder-button form-group field-btn_submit">
      <button type="submit" class="btn-default btn btn_submit" name="btn_submit" value="true" style="default" id="btn_submit">Declarer</button>
    </div>
  </div>
</form>
    
</body>
</html>
=======
<?php

//  public/declarer.php — Déclaration d'un sinistre
//  Accessible uniquement aux utilisateurs connectés

session_start();
require_once "../config/database.php";
require_once "../scoring/scoring.php";

// ── PROTECTION PAGE ───────────────────────────────────
if (!isset($_SESSION["user_id"])) {
    header("Location: connexion.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$erreur  = '';
$succes  = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ── RÉCUPÉRATION DES CHAMPS ───────────────────────
    $type_sinistre  = trim($_POST["type_sinistre"]);
    $date_sinistre  = trim($_POST["date_sinistre"]);
    $montant        = floatval($_POST["montant"]);
    $description    = trim($_POST["description"]);

    // ── VALIDATION ────────────────────────────────────
    if (empty($type_sinistre) || empty($date_sinistre) || empty($montant) || empty($description)) {
        $erreur = "Veuillez remplir tous les champs.";

    } else {

        // ── UPLOAD PHOTO ──────────────────────────────
        $photo_path = null;

        if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] === 0) {

            $ext_autorisees = ['jpg', 'jpeg', 'png'];
            $ext            = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));

            if (!in_array($ext, $ext_autorisees)) {
                $erreur = "Format photo non autorisé. (jpg, jpeg, png uniquement)";
            } elseif ($_FILES["photo"]["size"] > 5 * 1024 * 1024) {
                // Limite 5 Mo
                $erreur = "Photo trop lourde (max 5 Mo).";
            } else {
                // Nom unique pour éviter les collisions de fichiers
                $nom_fichier = uniqid("photo_") . "." . $ext;
                $destination = "../assets/uploads/" . $nom_fichier;
                move_uploaded_file($_FILES["photo"]["tmp_name"], $destination);
                $photo_path = $destination;
            }
        }

        if (empty($erreur)) {

            // ── RÉCUPÉRATION IP + HISTORIQUE ──────────
            // Nécessaire pour le calcul du score
            $ip_actuelle = $_SERVER["REMOTE_ADDR"] ?? '0.0.0.0';

            // Dernière IP utilisée par cet utilisateur
            $stmtIp = $pdo->prepare("
                SELECT ip_declaration FROM sinistres
                WHERE utilisateur_id = :id
                ORDER BY date_sinistre DESC
                LIMIT 1
            ");
            $stmtIp->execute(["id" => $user_id]);
            $derniere_ip = $stmtIp->fetchColumn() ?? '';

            // Nombre de sinistres des 6 derniers mois
            $stmtHisto = $pdo->prepare("
                SELECT COUNT(*) FROM sinistres
                WHERE utilisateur_id = :id
                AND date_sinistre >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            ");
            $stmtHisto->execute(["id" => $user_id]);
            $nb_sinistres_6mois = (int) $stmtHisto->fetchColumn();

            // ── CALCUL DU SCORE ───────────────────────
            // On prépare les données pour scoring.php
            $data_scoring = [
                "montant"           => $montant,
                "photo_path"        => $photo_path,
                "date_sinistre"     => $date_sinistre,
                "description"       => $description,
                "nb_sinistres_6mois"=> $nb_sinistres_6mois,
                "ip"                => $ip_actuelle,
                "derniere_ip"       => $derniere_ip
            ];

            $resultat = calculerScore($data_scoring);

            // ── INSERTION DU SINISTRE EN BDD ──────────
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

            // ── MISE À JOUR SCORE GLOBAL UTILISATEUR ──
            // On appelle le calcul de score global après chaque nouveau sinistre
            $scoring_url = "http://localhost/scoring/scoring.php?utilisateur_id=" . $user_id;
            file_get_contents($scoring_url);

            // ── ALERTE SI FRAUDE PROBABLE ─────────────
            if ($resultat["niveau"] === "frauduleux" || $resultat["niveau"] === "fraude probable") {
                $alert = $pdo->prepare("
                    INSERT INTO alertes 
                        (sinistre_id, utilisateur_id, type_alerte, score_declencheur, message)
                    VALUES 
                        (:sid, :uid, 'Suspicion fraude', :score, 'Score sinistre élevé')
                ");
                $alert->execute([
                    "sid"   => $sinistre_id,
                    "uid"   => $user_id,
                    "score" => $resultat["score"]
                ]);
            }

            $succes = "Sinistre déclaré avec succès. Score : {$resultat['score']}/100 — {$resultat['niveau']}";
        }
    }
}
>>>>>>> new_m1
