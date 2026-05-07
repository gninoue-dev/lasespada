?php
// public/inscription.php
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_once '../config/database.php';

$erreurs = [];
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// Vérif CSRF
if (!verifierCSRF($_POST['csrf_token'] ?? '')) {
$erreurs[] = "Token invalide. Rechargez la page.";
} else {
$nom = clean($_POST['nom'] ?? '');
$prenom = clean($_POST['prenom'] ?? '');
$email = clean($_POST['email'] ?? '');
$tel = clean($_POST['telephone'] ?? '');
$mdp = $_POST['mot_de_passe'] ?? '';
$mdp2 = $_POST['mot_de_passe_confirm'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'];

// Validations
if (empty($nom)) $erreurs[] = "Nom requis.";
if (empty($prenom)) $erreurs[] = "Prénom requis.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = "Email invalide.";
if (strlen($mdp) < 6) $erreurs[]="Mot de passe trop court (6 caractères min)." ; if ($mdp !==$mdp2)
    $erreurs[]="Les mots de passe ne correspondent pas." ; if (empty($erreurs)) { $pdo=getDB(); // Email déjà utilisé ?
    $check=$pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
    $erreurs[] = "Cet email est déjà enregistré.";
    } else {
    $hash = password_hash($mdp, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("
    INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, adresse_ip)
    VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$nom, $prenom, $email, $hash, $tel, $ip]);
    $succes = true;
    }
    }
    }
    }
    ?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <title>Inscription — FraudShield</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>

    <body>
        <div class="auth-container">
            <div class="auth-card">
                <h1>🛡️ FraudShield</h1>
                <h2>Créer un compte</h2>

                <?php if ($succes): ?>
                    <div class="alert success">Compte créé ! <a href="connexion.php">Se connecter</a></div>
                <?php endif; ?>

                <?php foreach ($erreurs as $e): ?>
                    <div class="alert error">
                        <?= $e ?>
                    </div>
                <?php endforeach; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= genererCSRF() ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom</label>
                            <input type="text" name="nom" value="<?= clean($_POST['nom'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Prénom</label>
                            <input type="text" name="prenom" value="<?= clean($_POST['prenom'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= clean($_POST['email'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" name="telephone" value="<?= clean($_POST['telephone'] ?? '') ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Mot de passe</label>
                            <input type="password" name="mot_de_passe" required>
                        </div>
                        <div class="form-group">
                            <label>Confirmer</label>
                            <input type="password" name="mot_de_passe_confirm" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">S'inscrire</button>
                </form>

                <p class="auth-link">Déjà inscrit ? <a href="connexion.php">Se connecter</a></p>
            </div>
        </div>
    </body>

    </html