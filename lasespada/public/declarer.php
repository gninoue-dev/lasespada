<?php
// ══════════════════════════════════════════════════════
//  public/declarer.php — Déclaration de sinistre
// ══════════════════════════════════════════════════════

require_once "../includes/session.php";
require_once "../config/database.php";
require_once "../includes/functions.php";

// Redirige si non connecté
requireLogin();

// Récupère l'utilisateur connecté
$utilisateur_id = $_SESSION["user_id"];

$erreurs = [];
$message = "";
$type_message = "";

// ══════════════════════════════════════════════════════
//  TRAITEMENT DU FORMULAIRE
// ══════════════════════════════════════════════════════
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  // --- 1. Récupération & nettoyage ---
  $type_sinistre = trim($_POST["type_sinistre"] ?? "");
  $localisation = trim($_POST["localisation"] ?? "");
  $date_sinistre = trim($_POST["date_sinistre"] ?? "");
  $date_declaration = trim($_POST["date_declaration"] ?? "");
  $duree_ecoulee = trim($_POST["duree_ecoulee"] ?? "");
  $montant_estime = trim($_POST["montant_estime"] ?? "");
  $description = trim($_POST["description"] ?? "");
  $temoins = trim($_POST["temoins"] ?? "");

  // --- 2. Validation ---
  if (empty($type_sinistre))
    $erreurs[] = "Veuillez décrire ce qui vous est arrivé.";
  if (empty($localisation))
    $erreurs[] = "Le lieu de l'accident est obligatoire.";
  if (empty($date_sinistre))
    $erreurs[] = "La date de l'accident est obligatoire.";
  if (empty($date_declaration))
    $erreurs[] = "La date de déclaration est obligatoire.";
  if (empty($duree_ecoulee))
    $erreurs[] = "Veuillez indiquer le temps écoulé.";
  if (empty($description))
    $erreurs[] = "La description des faits est obligatoire.";

  // Vérif : date sinistre pas dans le futur
  if (!empty($date_sinistre) && strtotime($date_sinistre) > time()) {
    $erreurs[] = "La date de l'accident ne peut pas être dans le futur.";
  }

  // Vérif : date déclaration >= date sinistre
  if (!empty($date_sinistre) && !empty($date_declaration)) {
    if (strtotime($date_declaration) < strtotime($date_sinistre)) {
      $erreurs[] = "La date de déclaration ne peut pas être avant la date de l'accident.";
    }
  }

  // Montant (optionnel mais doit être positif si renseigné)
  if (!empty($montant_estime) && (!is_numeric($montant_estime) || $montant_estime < 0)) {
    $erreurs[] = "Le montant doit être un nombre positif.";
  }

  // --- 3. Upload fichiers ---
  $dossier_upload = "../uploads/sinistres/";
  $extensions_autorisees = ["jpg", "jpeg", "png", "pdf", "mp4", "mov", "doc", "docx"];

  if (!is_dir($dossier_upload)) {
    mkdir($dossier_upload, 0755, true);
  }

  // Fonction upload
  function uploadFichier(array $fichier, string $dossier, array $ext_ok): array
  {
    if ($fichier["error"] !== UPLOAD_ERR_OK || empty($fichier["name"])) {
      return ["succes" => false, "chemin" => "", "nom" => "", "type" => ""];
    }
    $ext = strtolower(pathinfo($fichier["name"], PATHINFO_EXTENSION));
    if (!in_array($ext, $ext_ok)) {
      return [
        "succes" => false,
        "chemin" => "",
        "nom" => "",
        "type" => "",
        "erreur" => "Extension non autorisée : .$ext"
      ];
    }
    if ($fichier["size"] > 10 * 1024 * 1024) {
      return [
        "succes" => false,
        "chemin" => "",
        "nom" => "",
        "type" => "",
        "erreur" => "Fichier trop volumineux (max 10 Mo)."
      ];
    }
    $nom_unique = uniqid("sinistre_", true) . "." . $ext;
    $chemin = $dossier . $nom_unique;
    if (!move_uploaded_file($fichier["tmp_name"], $chemin)) {
      return [
        "succes" => false,
        "chemin" => "",
        "nom" => "",
        "type" => "",
        "erreur" => "Échec du déplacement du fichier."
      ];
    }
    return ["succes" => true, "chemin" => $chemin, "nom" => $nom_unique, "type" => $ext];
  }

  // Upload preuve (obligatoire)
  $preuve = ["succes" => false, "chemin" => "", "nom" => "", "type" => ""];
  $rapport = ["succes" => false, "chemin" => "", "nom" => "", "type" => ""];

  if (!empty($_FILES["preuves"]["name"])) {
    $preuve = uploadFichier($_FILES["preuves"], $dossier_upload, $extensions_autorisees);
    if (!$preuve["succes"] && isset($preuve["erreur"])) {
      $erreurs[] = "Preuves : " . $preuve["erreur"];
    }
  } else {
    $erreurs[] = "Veuillez envoyer une preuve (photo, vidéo ou document).";
  }

  // Upload rapport de police (optionnel)
  if (!empty($_FILES["rapport_police"]["name"])) {
    $rapport = uploadFichier($_FILES["rapport_police"], $dossier_upload, $extensions_autorisees);
    // Non bloquant — champ optionnel
  }

  // ══════════════════════════════════════════════════════
  //  CALCUL DU SCORE DE FRAUDE
  // ══════════════════════════════════════════════════════
  if (empty($erreurs)) {

    $score = 0;

    // Montant élevé
    if (!empty($montant_estime)) {
      if ($montant_estime > 5000000)
        $score += 30;
      elseif ($montant_estime > 2000000)
        $score += 15;
    }

    // Déclaration le même jour que l'accident → suspect
    if (!empty($date_sinistre) && !empty($date_declaration)) {
      $diff_jours = (strtotime($date_declaration) - strtotime($date_sinistre)) / 86400;
      if ($diff_jours < 1)
        $score += 20;
    }

    // Pas de rapport de police
    if (!$rapport["succes"])
      $score += 10;

    // Pas de témoins
    if ($temoins === "Non" || empty($temoins))
      $score += 10;

    // Même IP qu'un autre utilisateur
    $ip_actuelle = getIP();
    $stmt_ip = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE adresse_ip = ? AND id != ?");
    $stmt_ip->execute([$ip_actuelle, $utilisateur_id]);
    if ($stmt_ip->fetchColumn() > 0)
      $score += 25;

    // Sinistres multiples du même utilisateur
    $stmt_nb = $pdo->prepare("SELECT COUNT(*) FROM sinistres WHERE utilisateur_id = ?");
    $stmt_nb->execute([$utilisateur_id]);
    $nb_sinistres = (int) $stmt_nb->fetchColumn();
    if ($nb_sinistres >= 3)
      $score += 30;
    elseif ($nb_sinistres >= 1)
      $score += 15;

    // Statut final
    if ($score >= 70)
      $statut = "frauduleux";
    elseif ($score >= 40)
      $statut = "douteux";
    else
      $statut = "normal";

    // ══════════════════════════════════════════════════════
    //  INSERTION EN BASE
    // ══════════════════════════════════════════════════════
    try {
      $pdo->beginTransaction();

      // 1. Sinistre
      $stmt = $pdo->prepare("
                INSERT INTO sinistres
                    (utilisateur_id, type_sinistre, date_sinistre, date_declaration,
                     description, montant_estime, localisation, score_sinistre, statut)
                VALUES
                    (:utilisateur_id, :type_sinistre, :date_sinistre, NOW(),
                     :description, :montant_estime, :localisation, :score_sinistre, :statut)
            ");
      $stmt->execute([
        ":utilisateur_id" => $utilisateur_id,
        ":type_sinistre" => $type_sinistre,
        ":date_sinistre" => $date_sinistre,
        ":description" => $description,
        ":montant_estime" => $montant_estime !== "" ? $montant_estime : null,
        ":localisation" => $localisation,
        ":score_sinistre" => $score,
        ":statut" => $statut,
      ]);
      $sinistre_id = $pdo->lastInsertId();

      // 2. Preuves
      $sql_preuve = "INSERT INTO preuves (sinistre_id, nom_fichier, chemin_fichier, type_fichier)
                           VALUES (:sinistre_id, :nom, :chemin, :type)";

      if ($preuve["succes"]) {
        $pdo->prepare($sql_preuve)->execute([
          ":sinistre_id" => $sinistre_id,
          ":nom" => $preuve["nom"],
          ":chemin" => $preuve["chemin"],
          ":type" => $preuve["type"],
        ]);
      }
      if ($rapport["succes"]) {
        $pdo->prepare($sql_preuve)->execute([
          ":sinistre_id" => $sinistre_id,
          ":nom" => $rapport["nom"],
          ":chemin" => $rapport["chemin"],
          ":type" => $rapport["type"],
        ]);
      }

      // 3. Mise à jour score & IP utilisateur
      $pdo->prepare("UPDATE utilisateurs SET score_global = score_global + ?, adresse_ip = ? WHERE id = ?")
        ->execute([$score, $ip_actuelle, $utilisateur_id]);

      // 4. Mise à jour statut utilisateur
      if ($score >= 70) {
        $pdo->prepare("UPDATE utilisateurs SET statut = 'frauduleux' WHERE id = ?")
          ->execute([$utilisateur_id]);
      } elseif ($score >= 40) {
        $pdo->prepare("UPDATE utilisateurs SET statut = 'douteux' WHERE id = ? AND statut = 'normal'")
          ->execute([$utilisateur_id]);
      }

      // 5. Alerte si score >= 40
      if ($score >= 40) {
        $type_alerte = $score >= 70 ? "fraude_probable" : "score_eleve";
        $msg_alerte = "Sinistre #$sinistre_id — score de fraude : $score. Statut : $statut.";
        $pdo->prepare("INSERT INTO alertes (sinistre_id, utilisateur_id, type_alerte, score_declencheur, message)
                               VALUES (?, ?, ?, ?, ?)")
          ->execute([$sinistre_id, $utilisateur_id, $type_alerte, $score, $msg_alerte]);
      }

      $pdo->commit();

      $message = "Votre déclaration a été soumise avec succès ! Numéro de dossier : <strong>#$sinistre_id</strong>";
      $type_message = "succes";

    } catch (PDOException $e) {
      $pdo->rollBack();
      $message = "Erreur lors de l'enregistrement : " . $e->getMessage();
      $type_message = "erreur";
    }

  } else {
    $message = implode("<br>• ", $erreurs);
    $type_message = "erreur";
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Déclarer un sinistre</title>
  <link rel="stylesheet" href="../assets/css/declarer.css">
  <style>
    .alerte {
      padding: 14px 18px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 15px;
      line-height: 1.7;
    }

    .alerte.succes {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .alerte.erreur {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>

<body>

  <?php if (!empty($message)): ?>
    <div class="alerte <?= htmlspecialchars($type_message) ?>">
      <?= ($type_message === "succes" ? "✅ " : "❌ • ") . $message ?>
    </div>
  <?php endif; ?>

  <form id="rendered-form" method="POST" action="" enctype="multipart/form-data">
    <div class="rendered-form">

      <!-- Que s'est-il passé -->
      <div class="formbuilder-number form-group field-txt_age">
        <label for="type_sinistre" class="formbuilder-number-label">
          Que vous est-il arrivé ? <span class="formbuilder-required">*</span>
        </label><br>
        <input type="text" placeholder="un accident de voiture" class="form-control" name="type_sinistre"
          id="type_sinistre" value="<?= htmlspecialchars($_POST['type_sinistre'] ?? '') ?>" required>
      </div>

      <!-- Lieu -->
      <div class="formbuilder-number form-group field-txt_postalcode">
        <label for="localisation" class="formbuilder-number-label">
          Veuillez indiquer l'endroit de l'accident ! <span class="formbuilder-required">*</span>
        </label><br>
        <input type="text" placeholder="Abidjan, Abobo, Sanmanké" class="form-control" name="localisation"
          id="localisation" value="<?= htmlspecialchars($_POST['localisation'] ?? '') ?>" required>
      </div>

      <!-- Date accident -->
      <div class="formbuilder-number form-group field-txt_postalcode">
        <label for="date_sinistre" class="formbuilder-number-label">
          Veuillez indiquer la date de l'accident ! <span class="formbuilder-required">*</span>
        </label><br>
        <input type="date" class="form-control" name="date_sinistre" id="date_sinistre"
          value="<?= htmlspecialchars($_POST['date_sinistre'] ?? '') ?>" required>
      </div>

      <!-- Date déclaration -->
      <div class="formbuilder-number form-group field-txt_postalcode">
        <label for="date_declaration" class="formbuilder-number-label">
          Veuillez indiquer la date de déclaration ! <span class="formbuilder-required">*</span>
        </label><br>
        <input type="date" class="form-control" name="date_declaration" id="date_declaration"
          value="<?= htmlspecialchars($_POST['date_declaration'] ?? '') ?>" required>
      </div>

      <!-- Durée écoulée -->
      <div class="formbuilder-number form-group field-txt_postalcode">
        <label for="duree_ecoulee" class="formbuilder-number-label">
          Combien de temps s'est écoulé depuis l'accident ? <span class="formbuilder-required">*</span>
        </label><br>
        <input type="time" class="form-control" name="duree_ecoulee" id="duree_ecoulee"
          value="<?= htmlspecialchars($_POST['duree_ecoulee'] ?? '') ?>" required>
      </div>

      <!-- Montant -->
      <div class="formbuilder-number form-group field-txt_postalcode">
        <label for="montant_estime" class="formbuilder-number-label">
          De combien avez-vous besoin ?
        </label><br>
        <input type="number" placeholder="500000" class="form-control" name="montant_estime" id="montant_estime" min="0"
          value="<?= htmlspecialchars($_POST['montant_estime'] ?? '') ?>">
      </div>

      <!-- Description -->
      <div class="formbuilder-number form-group field-txt_postalcode">
        <label for="description" class="formbuilder-number-label">
          Expliquez les faits ! <span class="formbuilder-required">*</span>
        </label><br>
        <textarea class="form-control" name="description" id="description" placeholder="Expliquez-vous ici....."
          style="padding-bottom: 90px;" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
      </div>

      <!-- Preuves -->
      <div class="formbuilder-number form-group field-txt_postalcode">
        <label for="preuves" class="formbuilder-number-label">
          Envoyez des preuves <span class="formbuilder-required">*</span>
        </label><br>
        <input type="file" class="form-control" name="preuves" id="preuves"
          accept=".jpg,.jpeg,.png,.pdf,.mp4,.mov,.doc,.docx" required>
      </div>

      <!-- Rapport de police -->
      <div class="formbuilder-number form-group field-txt_postalcode">
        <label for="rapport_police" class="formbuilder-number-label">
          Soumettez le Rapport de police si possible
        </label><br>
        <input type="file" class="form-control" name="rapport_police" id="rapport_police"
          accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
      </div>

      <!-- Témoins -->
      <div class="formbuilder-checkbox-group form-group field-chk_legalage">
        <label class="formbuilder-checkbox-group-label">Y avait-il des témoins ?</label><br>
        <div class="checkbox-group">
          <div class="formbuilder-checkbox">
            <input name="temoins" id="temoins_oui" class="form-checkbox" type="radio" value="Oui" <?= (($_POST['temoins'] ?? '') === 'Oui') ? 'checked' : '' ?>>
            <label for="temoins_oui">Oui</label>
            &nbsp;&nbsp;
            <input name="temoins" id="temoins_non" class="form-checkbox" type="radio" value="Non" <?= (($_POST['temoins'] ?? '') === 'Non') ? 'checked' : '' ?>>
            <label for="temoins_non">Non</label>
          </div>
        </div>
      </div>

      <!-- Bouton -->
      <div class="formbuilder-button form-group field-btn_submit">
        <button type="submit" class="btn-default btn btn_submit" name="btn_submit" id="btn_submit">
          Déclarer
        </button>
      </div>

    </div>
  </form>

</body>

</html>