<?php
session_start();
require_once "../config/database.php"; // connexion PDO

// Activer l'affichage des erreurs pour debug
error_reporting(E_ALL);
ini_set("display_errors", 1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = trim($_POST["email"]);
    $mdp = trim($_POST["mdp"]);
    $confirmation = trim($_POST["confirmation"]);

    if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($mdp) && !empty($confirmation)) {
        if ($mdp !== $confirmation) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {
            try {
                // Vérifier si l'email existe déjà
                $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
                $stmt->execute(["email" => $email]);

                if ($stmt->fetch()) {
                    $error = "Cet email est déjà utilisé.";
                } else {
                    // Hash du mot de passe
                    $hash = password_hash($mdp, PASSWORD_BCRYPT);

                    // Insertion en BDD
                    $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) 
                                           VALUES (:nom, :prenom, :email, :mdp)");
                    $stmt->execute([
                        "nom" => $nom,
                        "prenom" => $prenom,
                        "email" => $email,
                        "mdp" => $hash
                    ]);

                    // Redirection vers la connexion
                    header("Location: connexion.php");
                    exit;
                }
            } catch (PDOException $e) {
                $error = "Erreur SQL : " . $e->getMessage();
            }
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription</title>
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
            <?php if (!empty($error)) echo "<p class='text-red-500'>$error</p>"; ?>
          </div>
          <div class="p-6 pt-0">
            <!-- FORMULAIRE -->
            <form action="inscription.php" method="POST" class="grid gap-4">
              <div class="grid gap-2">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required placeholder="Entrez votre nom"
                  class="flex h-10 w-full rounded-md border px-3 py-2 text-sm" />
              </div>
              <div class="grid gap-2">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" required placeholder="Entrez votre prénom"
                  class="flex h-10 w-full rounded-md border px-3 py-2 text-sm" />
              </div>
              <div class="grid gap-2">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Entrez votre email"
                  class="flex h-10 w-full rounded-md border px-3 py-2 text-sm" />
              </div>
              <div class="grid gap-2">
                <label for="mdp">Mot de Passe</label>
                <input type="password" id="mdp" name="mdp" required placeholder="Entrez votre mot de passe"
                  class="flex h-10 w-full rounded-md border px-3 py-2 text-sm" />
              </div>
              <div class="grid gap-2">
                <label for="confirmation">Confirmation</label>
                <input type="password" id="confirmation" name="confirmation" required placeholder="Confirmez votre mot de passe"
                  class="flex h-10 w-full rounded-md border px-3 py-2 text-sm" />
              </div>
              <button type="submit"
                class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-zinc-900 text-white hover:bg-zinc-900/90 h-10 px-4 py-2 w-full">
                Créer un compte
              </button>
            </form>
            <!-- FIN FORMULAIRE -->
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
