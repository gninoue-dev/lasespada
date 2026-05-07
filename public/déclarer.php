<?php
// public/declarer.php
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_once '../config/database.php';
require_once '../scoring/scoring.php';

requireConnexion();

$erreurs = [];
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifierCSRF($_POST['csrf_token'] ?? '')) {
        $erreurs[] = "Token invalide. Rechargez la page.";
    } else {
        // --- Récupération des champs ---
        $type_sinistre = clean($_POST['type_sinistre'] ?? '');
        $date_sinistre = clean($_POST['date_sinistre'] ?? '');
        $montant = floatval($_POST['montant_estime'] ?? 0);
        $localisation = clean($_POST['localisation'] ?? '');
        $description = clean($_POST['description'] ?? '');
        $utilisateur_id = $_SESSION['utilisateur_id'];

        // --- Validations basiques ---
        if (empty($type_sinistre))
            $erreurs[] = "Type de sinistre requis.";
        if (empty($date_sinistre))
            $erreurs[] = "Date du sinistre requise.";
        if ($montant <= 0)
            $erreurs[] = "Montant estimé invalide.";
        if (empty($description))
            $erreurs[] = "Description requise.";
        if (strtotime($date_sinistre) > time())
            $erreurs[] = "La date ne peut pas être dans le futur.";

        // --- Validation photo ---
        $photo_data = null;
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $erreurs[] = "Photo justificative requise.";
        } else {
            $file = $_FILES['photo'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $mime = mime_content_type($file['tmp_name']);
            $allowed_mime = ['image/jpeg', 'image/png', 'image/webp'];
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($mime, $allowed_mime) || !in_array($ext, $allowed_ext)) {
                $erreurs[] = "Format de photo invalide. (JPG, PNG, WEBP uniquement)";
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $erreurs[] = "Photo trop lourde. (5 Mo max)";
            } else {
                $photo_data = $file;
            }
        }

        if (empty($erreurs)) {
            $pdo = getDB();

            // --- Insertion du sinistre ---
            $stmt = $pdo->prepare("
                INSERT INTO sinistres
                    (utilisateur_id, type_sinistre, date_sinistre, description, montant_estime, localisation)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$utilisateur_id, $type_sinistre, $date_sinistre, $description, $montant, $localisation]);
            $sinistre_id = $pdo->lastInsertId();

            // --- Upload photo ---
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir))
                mkdir($upload_dir, 0755, true);

            $nom_fichier = uniqid('sinistre_', true) . '.' . $ext;
            $chemin = $upload_dir . $nom_fichier;
            move_uploaded_file($photo_data['tmp_name'], $chemin);

            // --- Analyse EXIF ---
            $date_exif = null;
            $gps_exif = null;
            $score_meta = 0;

            if (in_array($ext, ['jpg', 'jpeg'])) {
                $exif = @exif_read_data($chemin);
                if ($exif) {
                    // Date EXIF
                    if (!empty($exif['DateTimeOriginal'])) {
                        $date_exif = $exif['DateTimeOriginal'];
                        $ts_exif = strtotime(str_replace(':', '-', substr($date_exif, 0, 10)) . substr($date_exif, 10));
                        $ts_sinistre = strtotime($date_sinistre);
                        // Photo prise APRÈS la date du sinistre = suspect
                        if ($ts_exif > $ts_sinistre + 86400)
                            $score_meta += 25;
                    }
                    // GPS EXIF
                    if (!empty($exif['GPSLatitude'])) {
                        $gps_exif = "GPS détecté";
                        // Ici on pourrait comparer avec localisation déclarée
                    }
                } else {
                    // Pas de données EXIF = photo possiblement retouchée
                    $score_meta += 10;
                }
            }

            // --- Insertion preuve ---
            $stmt2 = $pdo->prepare("
                INSERT INTO preuves
                    (sinistre_id, nom_fichier, chemin_fichier, type_fichier, date_exif, gps_exif, score_metadata)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt2->execute([
                $sinistre_id,
                $nom_fichier,
                $chemin,
                $ext,
                $date_exif,
                $gps_exif,
                $score_meta
            ]);

            // --- Calcul du score de fraude ---
            $score = calculerScore($pdo, $sinistre_id, $utilisateur_id, [
                'montant' => $montant,
                'date_sinistre' => $date_sinistre,
                'description' => $description,
                'score_meta' => $score_meta,
            ]);

            $statut = statutParScore($score);

            // --- Mise à jour du sinistre avec score et statut ---
            $pdo->prepare("UPDATE sinistres SET score_sinistre = ?, statut = ? WHERE id = ?")
                ->execute([$score, $statut, $sinistre_id]);

            // --- Mise à jour score global utilisateur ---
            $pdo->prepare("UPDATE utilisateurs SET score_global = score_global + ?, statut = ? WHERE id = ?")
                ->execute([$score, $statut, $utilisateur_id]);

            // --- Génération alerte si score >= 40 ---
            if ($score >= 40) {
                $type_alerte = $score >= 80 ? 'frauduleux_detecte' : 'score_eleve';
                $message = "Score de fraude : $score pts — Statut : $statut";
                $pdo->prepare("
                    INSERT INTO alertes (sinistre_id, utilisateur_id, type_alerte, score_declencheur, message)
                    VALUES (?, ?, ?, ?, ?)
                ")->execute([$sinistre_id, $utilisateur_id, $type_alerte, $score, $message]);
            }

            $succes = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Déclarer un sinistre — FraudShield</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .declarer-wrap {
            max-width: 720px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 32px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
        }

        .card h2 {
            margin-bottom: 24px;
            font-size: 1.3rem;
        }

        .upload-zone {
            border: 2px dashed #d1d5db;
            border-radius: 10px;
            padding: 28px;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s;
        }

        .upload-zone:hover {
            border-color: var(--primary);
        }

        .upload-zone p {
            color: var(--muted);
            font-size: .9rem;
            margin-top: 8px;
        }

        #preview {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 12px;
            display: none;
        }

        .nav-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1e2a3a;
            padding: 14px 28px;
            color: #fff;
        }

        .nav-bar a {
            color: #9ca3af;
            text-decoration: none;
            font-size: .9rem;
        }

        .nav-bar a:hover {
            color: #fff;
        }
    </style>
</head>

<body>

    <div class="nav-bar">
        <span>🛡️ FraudShield</span>
        <div style="display:flex;gap:20px;">
            <a href="historique.php">📋 Mes sinistres</a>
            <a href="logout.php">🚪 Déconnexion</a>
        </div>
    </div>

    <div class="declarer-wrap">
        <div class="card">
            <h2>📝 Déclarer un sinistre</h2>

            <?php if ($succes): ?>
                <div class="alert success">
                    ✅ Votre déclaration a été enregistrée et analysée.
                    <a href="historique.php">Voir mon historique</a>
                </div>
            <?php endif; ?>

            <?php foreach ($erreurs as $e): ?>
                <div class="alert error">
                    <?= $e ?>
                </div>
            <?php endforeach; ?>

            <?php if (!$succes): ?>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= genererCSRF() ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Type de sinistre</label>
                            <select name="type_sinistre" required>
                                <option value="">-- Choisir --</option>
                                <option value="Accident automobile">Accident automobile</option>
                                <option value="Vol de véhicule">Vol de véhicule</option>
                                <option value="Incendie">Incendie</option>
                                <option value="Vol de moto">Vol de moto</option>
                                <option value="Dégâts matériels">Dégâts matériels</option>
                                <option value="Blessures corporelles">Blessures corporelles</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date du sinistre</label>
                            <input type="date" name="date_sinistre" max="<?= date('Y-m-d') ?>" required
                                value="<?= clean($_POST['date_sinistre'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Montant estimé (FCFA)</label>
                            <input type="number" name="montant_estime" min="1" step="1000"
                                value="<?= clean($_POST['montant_estime'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Localisation</label>
                            <input type="text" name="localisation" placeholder="Ex: Abidjan, Plateau"
                                value="<?= clean($_POST['localisation'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description de l'incident</label>
                        <textarea name="description" placeholder="Décrivez précisément ce qui s'est passé..."
                            required><?= clean($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Photo justificative</label>
                        <div class="upload-zone" onclick="document.getElementById('photo').click()">
                            <span style="font-size:2rem">📷</span>
                            <p>Cliquez pour choisir une photo (JPG, PNG — max 5 Mo)</p>
                            <img id="preview" alt="Aperçu photo">
                        </div>
                        <input type="file" name="photo" id="photo" accept="image/*" style="display:none"
                            onchange="previewPhoto(this)">
                    </div>

                    <button type="submit" class="btn-primary">📤 Soumettre la déclaration</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function previewPhoto(input) {
            const preview = document.getElementById('preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>