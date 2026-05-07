<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>

  <section class="py-32">
    <div class="container">
      <div class="flex flex-col gap-4">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm mx-auto w-full max-w-md">
          <div class="flex flex-col space-y-1.5 p-6 items-center">

            <h3 class="font-semibold tracking-tight text-xl">S'inscrire</h3>
            <p class="text-sm text-zinc-600">Entrez vos informations pour créer un compte</p>
          </div>
          <div class="p-6 pt-0">
            <div class="grid gap-4">
              <button
                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-white hover:bg-zinc-100 hover:text-zinc-800 h-10 px-4 py-2 w-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-globe mr-2 size-4">
                  <circle cx="12" cy="12" r="10"></circle>
                  <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path>
                  <path d="M2 12h20"></path>
                </svg>S'inscrire avec Google
              </button>
              <div class="flex items-center gap-4">
                <span class="h-px w-full bg-gray-100"></span><span class="text-xs text-zinc-600">OU</span><span
                  class="h-px w-full bg-gray-100"></span>
              </div>

              <div class="grid gap-2">
                <label
                  class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  for="nom">Nom</label><input type="text"
                  class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-zinc-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  id="nom" placeholder="Entrez votre nom" required="" name="nom" />
              </div>

              <div class="grid gap-2">
                <label
                  class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  for="prenom">Prénom</label><input type="text"
                  class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-zinc-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  id="prenom" placeholder="Entrez votre prénom" required="" name="prenom" />
              </div>

              <div class="grid gap-2">
                <label
                  class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  for="email">Email</label><input type="email"
                  class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-zinc-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  id="email" placeholder="Entrez votre email" required="" name="email" />
              </div>


              <div class="grid gap-2">
                <label
                  class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  for="mdp">Mot de Passe</label><input type="password"
                  class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-zinc-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  id="mdp" placeholder="Entrez votre mot de passe" required="" name="mdp" />
              </div>

              <div class="grid gap-2">
                <label
                  class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                  for="confirmation">Confirmation</label><input type="password"
                  class="flex h-10 w-full rounded-md border border-input bg-white px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-zinc-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                  id="confirmation" placeholder="Confirmez votre mot de passe" required="" name="confirmation" />
              </div>


              <button
                class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-zinc-900 text-white hover:bg-zinc-900/90 h-10 px-4 py-2 w-full"
                type="submit">
                creer un compte
              </button>
            </div>

          </div>
        </div>
        <div class="mx-auto flex gap-1 text-sm">
          <p>Déjà un compte?</p>
          <a href="connexion.php" class="underline">Se connecter</a>
        </div>
      </div>
    </div>
  </section>

</body>

</html>

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